<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 08/04/16
 * Time: 09:52
 */

namespace WPCCrawler\services;

use WP_Query;
use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\objects\savers\PostSaver;
use WPCCrawler\objects\savers\UrlSaver;
use WPCCrawler\objects\Settings;
use WPCCrawler\objects\traits\SettingsTrait;
use WPCCrawler\Utils;

class SchedulingService {

    use SettingsTrait;

    private static $DEBUG = false;

    private $intervals;

    /*
     * ATTENTION (if your newly-added CRON event does not work)
     *
     * So you added a new CRON event. You created its function, added its function as WordPress action, added its
     * settings, added "schedule" and "remove schedule" functions... Everything looks OK. But, you do not see your CRON
     * event among the scheduled events? How come? Right? Yeah. I wasted a lot of hours to fix that problem. That's
     * twice now. It's because of my interesting way of initializing the plugin.
     *
     * In short, go to wp-content-crawler.php and make sure SchedulingService is initialized when your new CRON event
     * is scheduled. You are gonna see a few "is active" checks before initializing SchedulingService. Just add your
     * new condition there.
     *
     * I forget that every time.
     */

    public $eventCollectUrls   = 'wpcc_event_collect_urls';
    public $eventCrawlPost     = 'wpcc_event_crawl_post';
    public $eventRecrawlPost   = 'wpcc_event_recrawl_post';
    public $eventDeletePosts   = 'wpcc_event_delete_posts';

    /** @var string The meta key to store the time at which the last post deleting event run */
    public $metaKeyCronLastDeleted = '_cron_last_deleted_at';

    /** @var string Option key storing the ID of the site whose posts are deleted via delete event the last time. */
    public $optionKeyLastPostDeletedSiteId = '_last_post_deleted_site_id';

    private $maxRunCount = 1000;

    /**
     * @var array Stores the result of the db query used to retrieve active sites. It's a key-value pair. Keys are the
     *            meta keys that stores whether a site is active for an event or not. E.g. '_active',
     *            '_active_recrawling', etc.
     */
    private $activeSiteIdsCache = [];

    public function __construct() {
        // Add custom time intervals
        $this->setCRONIntervals();

        // Set what function to call for CRON events
        add_action($this->eventCollectUrls, [$this, 'executeEventCollectUrls']);
        add_action($this->eventCrawlPost,   [$this, 'executeEventCrawlPost']);
        add_action($this->eventRecrawlPost, [$this, 'executeEventRecrawlPost']);
        add_action($this->eventDeletePosts, [$this, 'executeEventDeletePosts']);

        // Activate/deactive CRON jobs when the plugin is activated. No need to remove CRON jobs on deactivation/uninstall.
        // WordPress removes them if it cannot find the action for the events.
        register_activation_hook(Utils::getPluginFilePath(), function () {
            Factory::schedulingService()->handleCronEvents();
        });
    }

    /*
     * COLLECT URLS
     */

    /**
     * Execute the event to collect URLs
     * @param bool $bypassInactiveScheduling true if you want to run this even if the scheduling is inactive
     */
    public function executeEventCollectUrls($bypassInactiveScheduling = false) {
        if (!Factory::wptslmClient()->isUserCool()) return;

        // If the scheduling is not active, do not continue.
        if (!$bypassInactiveScheduling && !Settings::isSchedulingActive()) {
            // Sometimes, when general settings are saved, the events cannot be unscheduled for some reason :) Here,
            // we're handling that case. If for some reason this method is called when the scheduling is inactive,
            // that means the CRON event could not be unscheduled. Let's just try again :)
            $this->removeURLCollectionAndCrawlingEvents();
            return;
        }

        $urlSaver = new UrlSaver();

        $this->execute($urlSaver->optionLastCheckedSiteId,
            // No site ID callback
            function() {
                Factory::urlSaver()->executeUrlSave(null);
            },

            // Get required run count callback
            function() {
                return $this->getSetting('_wpcc_run_count_url_collection', 1);
            },

            // Execute event callback
            function($siteIdToCheck) use (&$urlSaver) {
                $urlSaver->executeUrlSave($siteIdToCheck);
            },

            // "Has event run" callback
            function() use (&$urlSaver) {
                return !$urlSaver->isRequestMade();
            }
        );
    }

    /*
     * CRAWL POST
     */

    /**
     * Execute the event to crawl a post.
     * @param bool $bypassInactiveScheduling true if you want to run this even if the scheduling is inactive
     */
    public function executeEventCrawlPost($bypassInactiveScheduling = false) {
        if (!Factory::wptslmClient()->isUserCool()) return;

        // If the scheduling is not active, do not continue.
        if (!$bypassInactiveScheduling && !Settings::isSchedulingActive()) {
            $this->removeURLCollectionAndCrawlingEvents();
            return;
        }

        $postSaver = new PostSaver();

        $this->execute($postSaver->optionLastCrawledSiteId, null,
            // Get required run count callback
            function() {
                return $this->getSetting('_wpcc_run_count_post_crawl', 1);
            },

            // Execute event callback
            function($siteIdToCheck) use (&$postSaver) {
                $postSaver->executePostSave($siteIdToCheck);
            },

            // "Has event run" callback
            function() use (&$postSaver) {
                return !$postSaver->isRequestMade();
            }
        );

    }

    /*
     * RECRAWL POST
     */

    /**
     * Execute the event to crawl a post.
     * @param bool $bypassInactiveScheduling true if you want to run this even if the scheduling is inactive
     */
    public function executeEventRecrawlPost($bypassInactiveScheduling = false) {
        if (!Factory::wptslmClient()->isUserCool()) return;

        // If the scheduling is not active, do not continue.
        if (!$bypassInactiveScheduling && !Settings::isRecrawlingActive()) {
            $this->removeRecrawlingEvent();
            return;
        }

        $postSaver = new PostSaver();
        $results = null;

        $this->execute($postSaver->optionLastRecrawledSiteId, null,
            // Get required run count callback
            function() {
                return $this->getSetting('_wpcc_run_count_post_recrawl', 1);
            },

            // Execute event callback
            function($siteIdToCheck) use (&$postSaver, &$results) {
                global $wpdb;

                // Prepare "max recrawl count" part of the query
                $maxRecrawlCount = (int) $this->getSetting('_wpcc_max_recrawl_count', 0);
                $updateCountPart = $maxRecrawlCount > 0 ? " AND update_count < " . $maxRecrawlCount . " " : "";

                // Prepare "time between two recrawls" and "posts newer than" part
                $now = current_time('mysql');
                $timeBetweenRecrawlsInMin = (int) $this->getSetting('_wpcc_min_time_between_two_recrawls_in_min');
                $recrawledAtPart = $timeBetweenRecrawlsInMin ? " AND ((recrawled_at IS NULL AND saved_at < DATE_SUB('{$now}', INTERVAL {$timeBetweenRecrawlsInMin} MINUTE)) OR recrawled_at < DATE_SUB('{$now}', INTERVAL {$timeBetweenRecrawlsInMin} MINUTE)) " : "";

                $newerThanInMin = (int) $this->getSetting('_wpcc_recrawl_posts_newer_than_in_min');
                $newerThanPart = $newerThanInMin ? " AND saved_at > DATE_SUB('{$now}', INTERVAL {$newerThanInMin} MINUTE) " : "";

                // Get URL tuple to be recrawled
                // Make sure no post with 'trash' status can be selected. We're allowing 'draft' as well, in this case.
                $query = "SELECT t1.* FROM " . Factory::databaseService()->getDbTableUrlsName() . " t1
                    INNER JOIN {$wpdb->posts} t2 ON t1.saved_post_id = t2.ID
                    WHERE post_id = %d {$updateCountPart} {$recrawledAtPart} {$newerThanPart}
                        AND saved_post_id IS NOT NULL
                        AND is_locked = false
                        AND is_saved = true
                        AND t2.post_status <> 'trash'
                    ORDER BY recrawled_at ASC
                    LIMIT 1;";
                $results = $wpdb->get_results($wpdb->prepare($query, $siteIdToCheck));

                // If there is a URL tuple found, recrawl it.
                if(!empty($results)) {
                    $postSaver->executePostRecrawl($results[0]);

                } else {

                    // If there was a post waiting for recrawling to be finished, go on with it.
                    $prefix = $postSaver->cronRecrawlPostMetaPrefix;
                    $lastCrawledUrlId   = $this->getSetting($prefix . '_last_crawled_url_id');
                    $draftPostId        = $this->getSetting($prefix . '_post_draft_id');

                    // If the draft post does not exist, nullify the draft post meta for this site.
                    if($draftPostId && !get_post($draftPostId)) $draftPostId = null;

                    // If the draft post and its URL tuple exist, execute post recrawl on that URL tuple.
                    if($draftPostId && $urlTuple = Factory::databaseService()->getUrlById($lastCrawledUrlId)) {
                        $postSaver->executePostRecrawl($urlTuple);

                    // Otherwise, mark this site as last recrawled and nullify draft post meta for this site.
                    } else {
                        $postSaver->setIsRecrawl(true);
                        $postSaver->resetLastCrawled($siteIdToCheck);
                    }
                }
            },

            // "Has event run" callback
            function() use (&$postSaver, &$results) {
                return empty($results);
            }
        );
    }

    /*
     * DELETE POSTS
     */

    /**
     * Execute event to delete posts.
     *
     * @param bool $bypassInactiveScheduling
     */
    public function executeEventDeletePosts($bypassInactiveScheduling = false) {
        if (!Factory::wptslmClient()->isUserCool()) return;

        // If the scheduling is not active, do not continue.
        if (!$bypassInactiveScheduling && !Settings::isDeletingActive()) {
            $this->removeDeletingEvent();
            return;
        }

        $optionKeyMaxPostsToDelete = '_wpcc_max_post_count_per_post_delete_event';
        $maxPostsToDelete = (int) get_option($optionKeyMaxPostsToDelete);

        // If max posts value is not valid, use the default.
        if(!$maxPostsToDelete || $maxPostsToDelete < 1) {
            $maxPostsToDelete = Factory::generalSettingsController()->getDefaultGeneralSettings()[$optionKeyMaxPostsToDelete];
        }

        $remaining = $maxPostsToDelete;

        $this->execute($this->optionKeyLastPostDeletedSiteId, null,
            // "Get required run count" callback
            function() {
                return 1;
            },

            // "Execute event" callback
            function($siteIdToCheck) use (&$totalDeleteCount, &$remaining) {
                global $wpdb;

                if($remaining < 1) return;

                // Get the time offset from the settings
                $optionKeyOlderThan = '_wpcc_delete_posts_older_than_in_min';

                $olderThanInMin = (int) $this->getSetting($optionKeyOlderThan);

                // If minute value is not valid, use the default.
                if(!$olderThanInMin || $olderThanInMin < 1) {
                    $olderThanInMin = Factory::generalSettingsController()->getDefaultGeneralSettings()[$optionKeyOlderThan];
                }

                $isDeleteAttachments = $this->getSetting('_wpcc_is_delete_post_attachments');

                /*
                 *
                 */

                $postIds = Factory::databaseService()->getOldPostIdsForSite($siteIdToCheck, $olderThanInMin, $remaining);

                // TODO: Bulk delete posts or delete them WordPress way. Ask the user how the posts should be deleted.
                // Deleting the posts via WordPress way is inefficient.

                foreach($postIds as $id) {
                    if($isDeleteAttachments) {
                        // Delete the attachments
                        foreach(get_attached_media('image', $id) as $mediaPost) wp_delete_post($mediaPost->ID);
                    }

                    // First, set the post's URL deleted. This should be called before deleting the post. Because,
                    // when the post is deleted, its URL tuple's saved_post_id becomes null. If this happens, we cannot
                    // find the URL tuple belonging to this post. So, we need to delete its URL before it is deleted.
                    Factory::databaseService()->setUrlDeleted($id);

                    // Finally, delete the post
                    wp_delete_post($id, true);
                }

                // Set new remaining
                $remaining -= sizeof($postIds);

                // Update the last deleted time for the site
                Utils::savePostMeta($siteIdToCheck, $this->metaKeyCronLastDeleted, current_time('mysql'), true);

                // Update the site ID option storing the last site ID whose posts are deleted
                update_option($this->optionKeyLastPostDeletedSiteId, $siteIdToCheck);
            },

            // "Has event run" callback
            function() use (&$remaining) {
                return $remaining > 0;
            }
        );

    }

    /**
     * Executes an event for a site as many times as it is required. If the event has not succeeded for a site, another
     * site will be tried for the event.
     *
     * @param string        $lastCheckedSiteIdOptionName  Option name that stores last checked site ID for this event.
     *                                                    E.g. '_wpcc_last_checked_site_id'
     * @param callable|null $noSiteIdCallback             A callback that will be called when there is no site ID to
     *                                                    interact with. No need to return anything.
     * @param callable      $getRequiredRunCountCallback  A callback that <b>must return an integer</b>. This integer
     *                                                    will be used to define how many times the event will run.
     * @param callable      $executeEventCallback         A callback that does the actual job this event should do.
     *                                                    This will be called multiple times depending on required run
     *                                                    count. <i>Takes params:</i> <b>(int) $siteIdToCheck</b>. No
     *                                                    need to return anything.
     * @param callable      $hasEventRunCallback          A callback that <b>must return a boolean</b>. If this returns
     *                                                    true for the first try, another site will be tried for this
     *                                                    event, if there are any more sites. If this returns true after
     *                                                    first try, later executions won't be run.
     */
    private function execute($lastCheckedSiteIdOptionName, $noSiteIdCallback, $getRequiredRunCountCallback, $executeEventCallback,
        $hasEventRunCallback) {
        $siteIdToCheck = null;
        $run = false;

        /** @var array $triedSiteIds Stores IDs of the sites that are already tried. */
        $triedSiteIds = [];

        do {
            // If there is no site ID to check, get the next site ID that needs to be checked.
            if(!$siteIdToCheck) {
                // Get site ID to check
                $siteIdToCheck = $this->getSiteIdForEvent($lastCheckedSiteIdOptionName);

                // If there is no valid site ID to check, call the event so that it can handle the case where there is no
                // valid site ID.
                if(!$siteIdToCheck) {
                    if($noSiteIdCallback && is_callable($noSiteIdCallback)) call_user_func($noSiteIdCallback);
                    break;
                }
            }

            // If this site has already been tried, break the loop.
            if(array_search($siteIdToCheck, $triedSiteIds) !== false) break;

            // Get settings for the site ID
            $settings = get_post_meta($siteIdToCheck);
            $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

            // Get how many times this should run.
            $requiredRunCount = (int) call_user_func($getRequiredRunCountCallback);
            if($requiredRunCount < 1) $requiredRunCount = 1;

            // Run the event as many times as the user wants.
            $count = 0;
            do {
                // If this is not the first run, get the settings from the database and assign them to this class again.
                // This is important, because the settings might have been changed in the first run.
                if($count > 0) {
                    $settings = get_post_meta($siteIdToCheck);
                    $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());
                }

                call_user_func($executeEventCallback, $siteIdToCheck);

                if($count === 0) {
                    // If this is the first run of this inner loop, add the site ID among tried site IDs.
                    $triedSiteIds[] = $siteIdToCheck;

                    // Try another site when required.
                    if(call_user_func($hasEventRunCallback)) {
//                        error_log("WPCC - Nothing to do. Try another site. Option: {$lastCheckedSiteIdOptionName}, Current Site ID: {$siteIdToCheck} ");
                        $run = true;
                        $siteIdToCheck = null;
                        break;

                    // Otherwise, make sure outer loop won't run again.
                    } else {
                        $run = false;
                    }

                // If the event was not executed after the first run, break the loops.
                } else if(call_user_func($hasEventRunCallback)) {
                    break 2;
                }

                $count++;

                if($count >= $this->maxRunCount) break 2;

            } while($count < $requiredRunCount);

        } while($run);
    }

    /*
     *
     */

    /**
     * Handles scheduling by setting the CRON jobs if scheduling is active, or deleting current jobs if scheduling is
     * disabled.
     */
    public function handleCronEvents() {
        // URL collection and post-crawling
        if(Settings::isSchedulingActive()) {
            $this->scheduleEvents();
        } else {
            $this->removeURLCollectionAndCrawlingEvents();
        }

        // Recrawling
        if(Settings::isRecrawlingActive()) {
            $this->scheduleRecrawlingEvent();
        } else {
            $this->removeRecrawlingEvent();
        }

        // Deleting
        if(Settings::isDeletingActive()) {
            $this->scheduleDeletingEvent();
        } else {
            $this->removeDeletingEvent();
        }
    }

    /**
     * Schedule events with time intervals specified by the user
     */
    public function scheduleEvents() {
        $intervalCollectUrls = get_option('_wpcc_interval_url_collection');
        $intervalCrawlPosts  = get_option('_wpcc_interval_post_crawl');

        $this->scheduleEvent($this->eventCollectUrls, $intervalCollectUrls);
        $this->scheduleEvent($this->eventCrawlPost, $intervalCrawlPosts);
    }

    /**
     * Start scheduling for recrawling event with the time interval specified by the user
     */
    public function scheduleRecrawlingEvent() {
        $interval = get_option('_wpcc_interval_post_recrawl');

        $this->scheduleEvent($this->eventRecrawlPost, $interval);
    }

    /**
     * Start scheduling for deleting event with the time interval specified by the user
     */
    public function scheduleDeletingEvent() {
        $interval = get_option('_wpcc_interval_post_delete');

        $this->scheduleEvent($this->eventDeletePosts, $interval);
    }

    /**
     * Schedules an event after removes the old event, if it exists.
     *
     * @param string $eventName Name of the event
     * @param string $interval One of the registered CRON interval keys
     */
    private function scheduleEvent($eventName, $interval) {
        // Try to remove the next schedule.
        $this->removeScheduledEvent($eventName);

        // Schedule the event
        if(!$timestamp = wp_get_schedule($eventName)) {
            wp_schedule_event(time() + 5, $interval, $eventName);
        }
    }

    /*
     *
     */

    /**
     * Removes scheduled events
     */
    public function removeURLCollectionAndCrawlingEvents() {
        $eventNames = [$this->eventCollectUrls, $this->eventCrawlPost];
        foreach($eventNames as $eventName) {
            $this->removeScheduledEvent($eventName);
        }
    }

    /**
     * Removes (disables) recrawling event.
     */
    public function removeRecrawlingEvent() {
        $this->removeScheduledEvent($this->eventRecrawlPost);
    }

    /**
     * Removes (disables) deleting event.
     */
    public function removeDeletingEvent() {
        $this->removeScheduledEvent($this->eventDeletePosts);
    }

    /**
     * Remove a scheduled event. i.e. disable the schedule for an event
     *
     * @param string $eventName Name of the event
     */
    private function removeScheduledEvent($eventName) {
        if($timestamp = wp_next_scheduled($eventName)) {
            wp_unschedule_event($timestamp, $eventName);
        }
    }

    /**
     * @return array Structured as
     * <b>[ interval_key => [interval_description, interval_in_seconds], interval_key_2 => [ ... ], ... ]</b>
     */
    public function getIntervals() {
        $this->intervals = [
            // Interval Name               Description      Interval in Seconds
            '_wpcc_1_minute'    =>  [_wpcc('Every minute'),        60],
            '_wpcc_2_minutes'   =>  [_wpcc('Every 2 minutes'),     2 * 60],
            '_wpcc_3_minutes'   =>  [_wpcc('Every 3 minutes'),     3 * 60],
            '_wpcc_5_minutes'   =>  [_wpcc('Every 5 minutes'),     5 * 60],
            '_wpcc_10_minutes'  =>  [_wpcc('Every 10 minutes'),    10 * 60],
            '_wpcc_15_minutes'  =>  [_wpcc('Every 15 minutes'),    15 * 60],
            '_wpcc_20_minutes'  =>  [_wpcc('Every 20 minutes'),    20 * 60],
            '_wpcc_30_minutes'  =>  [_wpcc('Every 30 minutes'),    30 * 60],
            '_wpcc_45_minutes'  =>  [_wpcc('Every 45 minutes'),    45 * 60],
            '_wpcc_1_hour'      =>  [_wpcc('Every hour'),          60 * 60],
            '_wpcc_2_hours'     =>  [_wpcc('Every 2 hours'),       2 * 60 * 60],
            '_wpcc_3_hours'     =>  [_wpcc('Every 3 hours'),       3 * 60 * 60],
            '_wpcc_4_hours'     =>  [_wpcc('Every 4 hours'),       4 * 60 * 60],
            '_wpcc_6_hours'     =>  [_wpcc('Every 6 hours'),       6 * 60 * 60],
            '_wpcc_12_hours'    =>  [_wpcc('Twice a day'),         12 * 60 * 60],
            '_wpcc_1_day'       =>  [_wpcc('Once a day'),          24 * 60 * 60],
            '_wpcc_2_days'      =>  [_wpcc('Every 2 days'),        2 * 24 * 60 * 60],
            '_wpcc_1_week'      =>  [_wpcc('Once a week'),         7 * 24 * 60 * 60],
            '_wpcc_2_weeks'     =>  [_wpcc('Every 2 weeks'),       2 * 7 * 24 * 60 * 60],
            '_wpcc_1_month'     =>  [_wpcc('Once a month'),        4 * 7 * 24 * 60 * 60],
        ];

        return $this->intervals;
    }

    /**
     * Adds custom time intervals for CRON scheduling.
     */
    private function setCRONIntervals() {
        $intervals = $this->getIntervals();
        add_filter('cron_schedules', function($schedules) use ($intervals) {
            foreach($intervals as $name => $interval) {
                $schedules[$name] = [
                    'interval'  =>  $interval[1],
                    'display'   =>  $interval[0]
                ];
            }

            return $schedules;
        });
    }

    /**
     * Get active and published sites' IDs
     *
     * @param string $activeKey The meta key that stores a checkbox value indicating the site is active or not.
     * @return array            An array of active and published site IDs. If there is no active and published site ID,
     *                          then empty array will be returned.
     */
    private function getActiveSiteIds($activeKey = '_active') {
        if(!$activeKey) return [];

        // If the result was cached before, return the cached result.
        if(isset($this->activeSiteIdsCache[$activeKey])) {
            return $this->activeSiteIdsCache[$activeKey];
        }

        $query = new WP_Query([
            'post_type'     =>  Constants::$POST_TYPE,
            'meta_query'    => [
                [
                    'key'       => $activeKey,
                    'value'     => ['on', 1],
                    'compare'   => 'in',
                ]
            ],
            'fields'        =>  'ids',
            'post_status'   =>  'publish',
            'nopaging'      =>  true,
        ]);

        // Get currently active sites
        $posts = $query->get_posts();

        // Cache the result
        $this->activeSiteIdsCache[$activeKey] = $posts;

        return $posts;
    }

    /**
     * Get next site ID for a CRON event
     *
     * @param string $optionName Option key used to store last checked ID for the event
     * @return int|null The site ID or null if no site ID is found
     */
    public function getSiteIdForEvent($optionName) {
        // Get the active key for this $optionName
        $activeKey = '_active';
        switch($optionName) {
            case Factory::postSaver()->optionLastRecrawledSiteId:
                $activeKey = '_active_recrawling';
                break;

            case $this->optionKeyLastPostDeletedSiteId:
                $activeKey = '_active_post_deleting';
                break;

            default:
                $activeKey = '_active';
        }

        // Get active site IDs
        $activeSites = $this->getActiveSiteIds($activeKey);

        // If there is no active site, then do not continue.
        if(empty($activeSites)) return null;

        // Get last checked site ID
        $lastCheckedSiteId = get_option($optionName);

        $siteIdToCheck = null;

        // If there is no last-checked site ID, then take the first active site in the active sites array
        if(!$lastCheckedSiteId) {
            if(static::$DEBUG) var_dump("Last checked site ID not found. Get the first active site's ID");
            $siteIdToCheck = $activeSites[0];

        // Otherwise, find the active site that comes after the last-checked site
        } else {
            // Find the active site that comes after the last-checked site. If the last checked site ID is the last
            // element in the array, then get the first element of it as site-id-to-check.

            $lastCheckedSiteIdPos = array_search($lastCheckedSiteId, $activeSites);
            if($lastCheckedSiteIdPos != sizeof($activeSites) - 1) {
                $siteIdToCheck = $activeSites[$lastCheckedSiteIdPos + 1];
            } else {
                $siteIdToCheck = $activeSites[0];
            }
        }

        return $siteIdToCheck;
    }

}