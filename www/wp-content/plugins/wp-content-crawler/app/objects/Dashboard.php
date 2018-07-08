<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/02/17
 * Time: 15:21
 */

namespace WPCCrawler\objects;


use WP_Post;
use WP_Query;
use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\Utils;

class Dashboard {

    /** @var int Number of last posts that will be shown at max */
    private $lastPostsLimit = 10;

    /** @var string Used for formatting dates in MySQL date format. */
    private $dateFormat = "Y-m-d H:i:s";

    /*
     *
     */

    /**
     * @var array Stores CRON event keys as array's keys. The values are the descriptions of the events recurrence
     *      frequency. Such as ["wpcc_event_collect_urls" => "Every 10 minutes", ...]
     */
    private $cronIntervalDescriptions = [];

    /*
     *
     */

    /** @var WP_Post[] Stores last crawled posts */
    private $lastCrawledPosts = [];

    /** @var WP_Post[] Stores last recrawled posts */
    private $lastRecrawledPosts = [];

    /** @var object[] An array of URL tuple objects, which were added to the queue last */
    private $lastUrlsInQueue = [];

    /** @var object[] An array of URL tuple objects, which were set as deleted last */
    private $lastUrlsMarkedAsDeleted = [];

    /*
     *
     */

    /** @var int Number of URLs waiting to be saved */
    private $totalUrlsInQueue = 0;

    /** @var int Number of URLs in the queue which are added today */
    private $totalUrlsInQueueAddedToday = 0;

    /** @var int Total number of posts saved */
    private $totalSavedPosts = 0;

    /** @var int Total number of posts saved today */
    private $totalSavedPostsToday = 0;

    /** @var int Total number of posts recrawled */
    private $totalRecrawledPosts = 0;

    /** @var int Total number of posts recrawled today */
    private $totalRecrawledPostsToday = 0;

    /** @var int Total number of recrawls */
    private $totalRecrawlCount = 0;

    /** @var int Total number of deleted posts */
    private $totalDeletedPosts = 0;

    /** @var int Total number of posts deleted today */
    private $totalDeletedPostsToday = 0;

    /*
     *
     */

    /** @var null|string Date at which the last URL collection was performed */
    private $lastUrlCollectionDate = null;

    /** @var null|string Saving date of the last crawled post */
    private $lastPostCrawlDate = null;

    /** @var null|string Recrawling date of the last recrawled post */
    private $lastPostRecrawlDate = null;

    /** @var null|string The last time at which the post-deleting event run */
    private $lastPostDeleteDate = null;

    /*
     *
     */

    /** @var null|string */
    private $nextURLCollectionDate = null;

    /** @var null|string */
    private $nextPostCrawlDate = null;

    /** @var null|string */
    private $nextPostRecrawlDate = null;

    /** @var null|string */
    private $nextPostDeleteDate = null;

    /*
     *
     */

    /** @var null|WP_Post */
    private $nextUrlCollectionSite = null;

    /** @var null|WP_Post */
    private $nextPostCrawlSite = null;

    /** @var null|WP_Post */
    private $nextPostRecrawlSite = null;

    /** @var null|WP_Post */
    private $nextPostDeleteSite = null;

    /*
     *
     */

    /**
     * @var object[] An array of URL tuple objects. Stores the URLs currently being crawled. Each object has a 'site'
     *      key which stores the site data as {@link WP_Post}. */
    private $urlsCurrentlyBeingCrawled = [];

    /**
     * @var WP_Post[] An array of WP_Post objects with each having 'wpcc' key storing the corresponding URL for that
     *      post. Stores the posts currently being recrawled.
     */
    private $postsCurrentlyBeingSaved = [];

    /*
     *
     */

    /**
     * @var WP_Post[] Stores sites active for either scheduling or recrawling with a few other data such as last URL
     *      collection ,last post crawl, last post recrawl, number of posts crawled and number of posts in the queue.
     *      Keys for these are <b>countSaved, countQueue, countRecrawled, lastCheckedAt, lastCrawledAt, lastRecrawledAt,
     *      activeScheduling, activeRecrawling, countQueueToday, countSavedToday, countRecrawledToday</b>
     */
    private $activeSites = [];

    public function __construct() {
        $this->init();
    }

    /**
     * Initializes the class by preparing local variables.
     */
    private function init() {
        $this->initCronIntervalDescriptions();
        $this->initLastSaves();
        $this->initTotals();
        $this->initLastCronRunInfo();
        $this->initNextCronRunInfo();
        $this->initCurrentlyBeingDone();
        $this->initActiveSites();
    }

    /*
     * INITIALIZER METHODS
     */

    private function initCronIntervalDescriptions() {
        $cronArr  = _get_cron_array();

        /**
         * Stores the target events. Each key in this array will be tried to be populated with its description.
         */
        $targetEvents = [
            Factory::schedulingService()->eventCollectUrls => null,
            Factory::schedulingService()->eventCrawlPost   => null,
            Factory::schedulingService()->eventRecrawlPost => null,
            Factory::schedulingService()->eventDeletePosts => null,
        ];

        $intervals = Factory::schedulingService()->getIntervals();
        $remaining = sizeof($targetEvents);
        $targetEventKeys = array_keys($targetEvents);

        // CRON array is a little bit deep. So let's get started.
        // Each key of CRON array is an epoch value. The values for these keys are arrays.
        foreach($cronArr as $epoch => $valArr) {
            if($remaining <= 0) break;

            // Each array stores event keys as keys, and an array as value.
            foreach($valArr as $eventKey => $eventData) {
                if($remaining <= 0) break 2;

                // Now, we can check if this event is one of the events we are looking for. If so and its description
                // was not retrieved before, let's get its description.
                if(in_array($eventKey, $targetEventKeys) && $targetEvents[$eventKey] === null) {

                    // Each data array's keys are hashes. Its values are again an array.
                    foreach($eventData as $hash => $currentHashData) {
                        if($remaining <= 0) break 3;

                        // The hash data stores the interval key under 'schedule' key. Let's get it.
                        $intervalKey = $currentHashData['schedule'];

                        // Now, we can get the description from the intervals defined by the plugin.
                        if(isset($intervals[$intervalKey])) {
                            $targetEvents[$eventKey] = $intervals[$intervalKey][0];

                            // Decrease the remaining count. We keep this so that we do not keep trying after all of
                            // the descriptions are retrieved.
                            $remaining--;

                            // No need to check another item of the current event data.
                            break;
                        }
                    }

                }
            }
        }

        $this->cronIntervalDescriptions = $targetEvents;
    }

    /**
     * Initialize last crawled posts, last recrawled posts and last URLs in the queue.
     */
    private function initLastSaves() {
        // Prepare last crawled and last recrawled posts
        $this->lastCrawledPosts     = $this->getLastPosts('saved_at', get_option('_wpcc_dashboard_count_last_crawled_posts', 10));
        $this->lastRecrawledPosts   = $this->getLastPosts('recrawled_at', get_option('_wpcc_dashboard_count_last_recrawled_posts', 10));

        // Prepare last added URLs
        $this->lastUrlsInQueue = $this->getLastAddedUrls(get_option('_wpcc_dashboard_count_last_urls', 10));

        // Prepare last deleted URLs
        $this->lastUrlsMarkedAsDeleted = $this->getLastDeletedUrls(get_option('_wpcc_dashboard_count_last_deleted_urls', 10));
    }

    /**
     * Initialize total URLs in queue, total saved posts, total recrawled posts and total recrawl count variables.
     */
    private function initTotals() {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        $today = $this->getToday();

        // Total URLs in the queue
        $queryTotalUrlsInQueue = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NULL
            AND recrawled_at IS NULL
            AND is_locked = FALSE
            AND is_saved = FALSE";
        $this->totalUrlsInQueue = $wpdb->get_var($queryTotalUrlsInQueue);
        $this->totalUrlsInQueueAddedToday = $wpdb->get_var($queryTotalUrlsInQueue . " AND created_at >= '{$today}'");

        // Total saved posts
        $queryTotalSavedPosts = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND is_locked = FALSE
            AND is_saved = TRUE";
        $this->totalSavedPosts = $wpdb->get_var($queryTotalSavedPosts);
        $this->totalSavedPostsToday = $wpdb->get_var($queryTotalSavedPosts . " AND saved_at >= '{$today}'");

        // Total recrawled posts
        $queryTotalRecrawledPosts = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND recrawled_at IS NOT NULL
            AND update_count > 0
            AND is_locked = FALSE
            AND is_saved = TRUE";
        $this->totalRecrawledPosts = $wpdb->get_var($queryTotalRecrawledPosts);
        $this->totalRecrawledPostsToday = $wpdb->get_var($queryTotalRecrawledPosts . " AND recrawled_at >= '{$today}'");

        // Total recrawl count
        $queryTotalRecrawlCount = "SELECT SUM(update_count) FROM {$tableUrls}
          WHERE update_count > 0";
        $this->totalRecrawlCount = $wpdb->get_var($queryTotalRecrawlCount);

        // Total deleted posts
        $queryTotalDeletedPosts = "SELECT COUNT(*) FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NOT NULL
            AND is_locked = FALSE
            AND is_saved = TRUE
            AND deleted_at IS NOT NULL";
        $this->totalDeletedPosts = $wpdb->get_var($queryTotalDeletedPosts);
        $this->totalDeletedPostsToday = $wpdb->get_var($queryTotalDeletedPosts . " AND deleted_at >= '{$today}'");
    }

    /**
     * Initialize last URL collection, last post crawl and last post recrawl variables.
     */
    private function initLastCronRunInfo() {
        global $wpdb;

        $query = "SELECT last_url_collection, last_crawled, last_recrawled, last_deleted FROM
            (SELECT MAX(meta_value) as last_url_collection  FROM {$wpdb->postmeta} WHERE meta_key = '_cron_last_checked_at') t_url_collection,
            (SELECT MAX(meta_value) as last_crawled         FROM {$wpdb->postmeta} WHERE meta_key = '_cron_last_crawled_at') t_crawl,
            (SELECT MAX(meta_value) as last_recrawled       FROM {$wpdb->postmeta} WHERE meta_key = '_cron_recrawl_last_crawled_at') t_recrawl,
            (SELECT MAX(meta_value) as last_deleted         FROM {$wpdb->postmeta} WHERE meta_key = '_cron_last_deleted_at') t_deleted";

        $res = $wpdb->get_results($query, ARRAY_A);

        if($res && isset($res[0])) {
            $values = $res[0];

            $this->lastUrlCollectionDate    = Utils::array_get($values, 'last_url_collection');
            $this->lastPostCrawlDate        = Utils::array_get($values, 'last_crawled');
            $this->lastPostRecrawlDate      = Utils::array_get($values, 'last_recrawled');
            $this->lastPostDeleteDate       = Utils::array_get($values, 'last_deleted');
        }
    }

    /**
     * Initialize variables storing next CRON event dates.
     */
    private function initNextCronRunInfo() {
        $offsetInSeconds = $this->getGMTOffset();

        // Next CRON event dates
        $nextCollectUrls    = wp_next_scheduled(Factory::schedulingService()->eventCollectUrls);
        $nextCrawlPost      = wp_next_scheduled(Factory::schedulingService()->eventCrawlPost);
        $nextRecrawlPost    = wp_next_scheduled(Factory::schedulingService()->eventRecrawlPost);
        $nextDeletePosts    = wp_next_scheduled(Factory::schedulingService()->eventDeletePosts);

        if($nextCollectUrls)    $this->nextURLCollectionDate    = date($this->dateFormat, $nextCollectUrls + $offsetInSeconds);
        if($nextCrawlPost)      $this->nextPostCrawlDate        = date($this->dateFormat, $nextCrawlPost + $offsetInSeconds);
        if($nextRecrawlPost)    $this->nextPostRecrawlDate      = date($this->dateFormat, $nextRecrawlPost + $offsetInSeconds);
        if($nextDeletePosts)    $this->nextPostDeleteDate       = date($this->dateFormat, $nextDeletePosts + $offsetInSeconds);

        /*
         * Next sites
         */

        // Get last site IDs
        $keySiteIdLastUrlCollection = Factory::urlSaver()->optionLastCheckedSiteId;
        $keySiteIdLastPostCrawl     = Factory::postSaver()->optionLastCrawledSiteId;
        $keySiteIdLastPostRecrawl   = Factory::postSaver()->optionLastRecrawledSiteId;
        $keySiteIdLastPostDelete    = Factory::schedulingService()->optionKeyLastPostDeletedSiteId;

        // Get next site IDs using the last site IDs
        $nextSiteIdUrlCollection    = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastUrlCollection);
        $nextSiteIdPostCrawl        = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastPostCrawl);
        $nextSiteIdPostRecrawl      = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastPostRecrawl);
        $nextSiteIdPostDelete       = Factory::schedulingService()->getSiteIdForEvent($keySiteIdLastPostDelete);

        // Get the sites as WP_Post
        $sites = $this->getPosts([$nextSiteIdUrlCollection, $nextSiteIdPostCrawl, $nextSiteIdPostRecrawl, $nextSiteIdPostDelete], Constants::$POST_TYPE);

        // Assign the related class variables
        foreach($sites as $site) {
            if($site->ID == $nextSiteIdUrlCollection)   $this->nextUrlCollectionSite    = $site;
            if($site->ID == $nextSiteIdPostCrawl)       $this->nextPostCrawlSite        = $site;
            if($site->ID == $nextSiteIdPostRecrawl)     $this->nextPostRecrawlSite      = $site;
            if($site->ID == $nextSiteIdPostDelete)      $this->nextPostDeleteSite       = $site;
        }
    }

    /**
     * Initialize variables that store things currently being done.
     */
    private function initCurrentlyBeingDone() {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        // URLs currently being crawled
        $resultsBeingCrawled = $wpdb->get_results("SELECT * FROM {$tableUrls}
          WHERE is_locked = TRUE
            AND saved_at IS NULL
            AND saved_post_id IS NULL
            AND recrawled_at IS NULL
          ORDER BY created_at ASC
        ");

        $this->urlsCurrentlyBeingCrawled = $this->addSiteDataToUrlTuples($resultsBeingCrawled);

        // Posts currently being recrawled
        $resultsBeingRecrawled = $wpdb->get_results("SELECT * FROM {$tableUrls}
          WHERE is_locked = TRUE
            AND saved_at IS NOT NULL
            AND saved_post_id IS NOT NULL
          ORDER BY updated_at ASC
        ");

        $this->postsCurrentlyBeingSaved = $this->getPostsFromUrlTuples($resultsBeingRecrawled);
    }

    /**
     * Initialize {@link activeSites} variable
     */
    private function initActiveSites() {
        $query = new WP_Query([
            'post_type'     =>  Constants::$POST_TYPE,
            'meta_query'    => [
                'relation' => 'OR',
                [
                    'key'       => '_active',
                    'value'     => ['on', 1],
                    'compare'   => 'in',
                ],
                [
                    'key'       => '_active_recrawling',
                    'value'     => ['on', 1],
                    'compare'   => 'in',
                ],
                [
                    'key'       => '_active_post_deleting',
                    'value'     => ['on', 1],
                    'compare'   => 'in',
                ]
            ],
            'post_status'   =>  'publish',
            'nopaging'      =>  true,
        ]);

        // Get currently active sites
        $activeSites = $query->get_posts();

        // If there is no active site, do not proceed.
        if(empty($activeSites)) return;

        $activeSiteIds = [];
        foreach($activeSites as $activeSite) $activeSiteIds[] = $activeSite->ID;
        $activeSiteIdsStr = implode(", ", $activeSiteIds);

        // Get today's counts
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();

        $today = $this->getToday();

        $queryTotalUrlsInQueueToday = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NULL
            AND recrawled_at IS NULL
            AND is_locked = FALSE
            AND is_saved = FALSE
            AND created_at >= '{$today}'
            AND post_id IN ({$activeSiteIdsStr})
        GROUP BY post_id";

        $queryTotalCrawledPostsToday = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND is_locked = FALSE
            AND is_saved = TRUE
            AND saved_at >= '{$today}'
            AND post_id IN ({$activeSiteIdsStr})
          GROUP BY post_id";

        $queryTotalRecrawledPosts = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND saved_at IS NOT NULL
            AND recrawled_at IS NOT NULL
            AND update_count > 0
            AND is_locked = FALSE
            AND is_saved = TRUE
            AND post_id IN ({$activeSiteIdsStr})
            %s
          GROUP BY post_id";

        $queryTotalDeletedPostsToday = "SELECT post_id, COUNT(*) as count FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND is_locked = FALSE
            AND deleted_at >= '{$today}'
            AND post_id IN ({$activeSiteIdsStr})
          GROUP BY post_id";

        $resultsTotalUrlsInQueueToday       = $this->preparePostIdCountDbResults($wpdb->get_results($queryTotalUrlsInQueueToday));
        $resultsTotalCrawledPostsToday      = $this->preparePostIdCountDbResults($wpdb->get_results($queryTotalCrawledPostsToday));
        $resultsTotalRecrawledPosts         = $this->preparePostIdCountDbResults($wpdb->get_results(sprintf($queryTotalRecrawledPosts, "")));
        $resultsTotalRecrawledPostsToday    = $this->preparePostIdCountDbResults($wpdb->get_results(sprintf($queryTotalRecrawledPosts, "AND recrawled_at >= '{$today}'")));
        $resultsTotalDeletedPostsToday      = $this->preparePostIdCountDbResults($wpdb->get_results($queryTotalDeletedPostsToday));

        // Add a few meta values and counts to each active site
        foreach($activeSites as $activeSite) {
            $postId = $activeSite->ID;

            $counts = Factory::postService()->getUrlTableCounts();
            $activeSite->countSaved     = isset($counts[$postId]) && isset($counts[$postId]["count_saved"]) ? $counts[$postId]["count_saved"] : 0;
            $activeSite->countQueue     = isset($counts[$postId]) && isset($counts[$postId]["count_queue"]) ? $counts[$postId]["count_queue"] : 0;
            $activeSite->countDeleted   = isset($counts[$postId]) && isset($counts[$postId]["count_deleted"]) ? $counts[$postId]["count_deleted"] : 0;
            $activeSite->countRecrawled = isset($resultsTotalRecrawledPosts[$postId]) && $resultsTotalRecrawledPosts[$postId] ? $resultsTotalRecrawledPosts[$postId] : 0;

            $activeSite->lastCheckedAt      = get_post_meta($postId, '_cron_last_checked_at', true);
            $activeSite->lastCrawledAt      = get_post_meta($postId, '_cron_last_crawled_at', true);
            $activeSite->lastRecrawledAt    = get_post_meta($postId, '_cron_recrawl_last_crawled_at', true);
            $activeSite->lastDeletedAt      = get_post_meta($postId, Factory::schedulingService()->metaKeyCronLastDeleted, true);

            $activeSite->activeScheduling   = get_post_meta($postId, '_active', true);
            $activeSite->activeRecrawling   = get_post_meta($postId, '_active_recrawling', true);
            $activeSite->activeDeleting     = get_post_meta($postId, '_active_post_deleting', true);

            $activeSite->countQueueToday        = isset($resultsTotalUrlsInQueueToday[$postId])     && $resultsTotalUrlsInQueueToday[$postId]       ? $resultsTotalUrlsInQueueToday[$postId]    : 0;
            $activeSite->countSavedToday        = isset($resultsTotalCrawledPostsToday[$postId])    && $resultsTotalCrawledPostsToday[$postId]      ? $resultsTotalCrawledPostsToday[$postId]   : 0;
            $activeSite->countRecrawledToday    = isset($resultsTotalRecrawledPostsToday[$postId])  && $resultsTotalRecrawledPostsToday[$postId]    ? $resultsTotalRecrawledPostsToday[$postId] : 0;
            $activeSite->countDeletedToday      = isset($resultsTotalDeletedPostsToday[$postId])    && $resultsTotalDeletedPostsToday[$postId]      ? $resultsTotalDeletedPostsToday[$postId]   : 0;
        }

        $this->activeSites = $activeSites;
    }

    /*
     * GETTERS
     */

    /**
     * @return array See {@link lastCrawledPosts}
     */
    public function getLastCrawledPosts() {
        return $this->lastCrawledPosts;
    }

    /**
     * @return array See {@link lastRecrawledPosts}
     */
    public function getLastRecrawledPosts() {
        return $this->lastRecrawledPosts;
    }

    /**
     * @return \stdClass[] See {@link lastUrlsInQueue}
     */
    public function getLastUrlsInQueue() {
        return $this->lastUrlsInQueue;
    }

    /**
     * @return \object[] See {@link lastUrlsMarkedAsDeleted}
     */
    public function getLastUrlsMarkedAsDeleted() {
        return $this->lastUrlsMarkedAsDeleted;
    }

    /**
     * @return int See {@link totalUrlsInQueue}
     */
    public function getTotalUrlsInQueue() {
        return $this->totalUrlsInQueue;
    }

    /**
     * @return int See {@link totalUrlsInQueueAddedToday}
     */
    public function getTotalUrlsInQueueAddedToday() {
        return $this->totalUrlsInQueueAddedToday;
    }

    /**
     * @return int See {@link totalSavedPosts}
     */
    public function getTotalSavedPosts() {
        return $this->totalSavedPosts;
    }

    /**
     * @return int See {@link totalSavedPostsToday}
     */
    public function getTotalSavedPostsToday() {
        return $this->totalSavedPostsToday;
    }

    /**
     * @return int See {@link totalRecrawledPosts}
     */
    public function getTotalRecrawledPosts() {
        return $this->totalRecrawledPosts;
    }

    /**
     * @return int See {@link totalRecrawledPostsToday}
     */
    public function getTotalRecrawledPostsToday() {
        return $this->totalRecrawledPostsToday;
    }

    /**
     * @return int See {@link totalRecrawlCount}
     */
    public function getTotalRecrawlCount() {
        return $this->totalRecrawlCount;
    }

    /**
     * @return int See {@link totalDeletedPosts}
     */
    public function getTotalDeletedPosts() {
        return $this->totalDeletedPosts;
    }

    /**
     * @return int See {@link totalDeletedPostsToday}
     */
    public function getTotalDeletedPostsToday() {
        return $this->totalDeletedPostsToday;
    }

    /**
     * @return null|string See {@link lastUrlCollectionDate}
     */
    public function getLastUrlCollectionDate() {
        return $this->lastUrlCollectionDate;
    }

    /**
     * @return null|string See {@link lastPostCrawlDate}
     */
    public function getLastPostCrawlDate() {
        return $this->lastPostCrawlDate;
    }

    /**
     * @return null|string See {@link lastPostRecrawlDate}
     */
    public function getLastPostRecrawlDate() {
        return $this->lastPostRecrawlDate;
    }

    /**
     * @return null|string See {@link lastPostDeleteDate}
     */
    public function getLastPostDeleteDate() {
        return $this->lastPostDeleteDate;
    }

    /**
     * @return null|string See {@link nextURLCollectionDate}
     */
    public function getNextURLCollectionDate() {
        return $this->nextURLCollectionDate;
    }

    /**
     * @return null|string See {@link nextPostCrawlDate}
     */
    public function getNextPostCrawlDate() {
        return $this->nextPostCrawlDate;
    }

    /**
     * @return null|string See {@link nextPostRecrawlDate}
     */
    public function getNextPostRecrawlDate() {
        return $this->nextPostRecrawlDate;
    }

    /**
     * @return null|string See {@link nextPostDeleteDate}
     */
    public function getNextPostDeleteDate() {
        return $this->nextPostDeleteDate;
    }

    /**
     * @return null|WP_Post See {@link nextSiteUrlCollection}
     */
    public function getNextUrlCollectionSite() {
        return $this->nextUrlCollectionSite;
    }

    /**
     * @return null|WP_Post See {@link nextSitePostCrawl}
     */
    public function getNextPostCrawlSite() {
        return $this->nextPostCrawlSite;
    }

    /**
     * @return null|WP_Post See {@link nextSitePostRecrawl}
     */
    public function getNextPostRecrawlSite() {
        return $this->nextPostRecrawlSite;
    }

    /**
     * @return null|WP_Post See {@link nextSitePostDelete}
     */
    public function getNextPostDeleteSite() {
        return $this->nextPostDeleteSite;
    }

    /**
     * @return \object[] See {@link urlsCurrentlyBeingCrawled}
     */
    public function getUrlsCurrentlyBeingCrawled() {
        return $this->urlsCurrentlyBeingCrawled;
    }

    /**
     * @return \WP_Post[] See {@link postsCurrentlyBeingRecrawled}
     */
    public function getPostsCurrentlyBeingSaved() {
        return $this->postsCurrentlyBeingSaved;
    }

    /**
     * @return \WP_Post[] See {@link activeSites}
     */
    public function getActiveSites() {
        return $this->activeSites;
    }

    /**
     * Get interval description of a scheduled CRON event.
     *
     * @param string $eventKey Key of a CRON event defined by the plugin. E.g. "wpcc_event_collect_urls"
     * @return string E.g. "Every 10 minutes"
     */
    public function getCronEventIntervalDescription($eventKey) {
        $description = Utils::array_get($this->cronIntervalDescriptions, $eventKey);
        return $description ? $description : "-";
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Get last posts from URLs table.
     *
     * @param string   $orderBy A column name, e.g. 'saved_at' or 'recrawled_at'
     * @param null|int $limit   Max number of items that should be retrieved. If this is null, {@link lastPostsLimit}
     *                          will be used.
     * @return \WP_Post[] An array of {@link WP_Post} instances with each having a 'wpcc'
     *                          key which stores the tuple instances with each having a 'wpcc' key which stores the
     *                          tuple from URLs table for that post as an object.
     */
    private function getLastPosts($orderBy, $limit = null) {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();
        $query = "SELECT * FROM {$tableUrls}
          WHERE saved_post_id IS NOT NULL
            AND {$orderBy} IS NOT NULL
            AND is_saved = TRUE
            AND is_locked = FALSE
          ORDER BY {$orderBy} DESC
          LIMIT %d";

        // Get the results
        $results = $wpdb->get_results($wpdb->prepare($query, $limit != null ? (int) $limit : $this->lastPostsLimit));

        // Get the posts
        $posts = $this->getPostsFromUrlTuples($results);

        return $posts;
    }

    /**
     * Adds site data to each URL tuple, under 'wpcc' key.
     *
     * @param array $urlTuples An array of URL tuple objects.
     * @return \WP_Post[] Posts for URL tuples, with each post having 'wpcc' key that stores the URL tuple.
     */
    private function getPostsFromUrlTuples($urlTuples = []) {
        if(!$urlTuples || empty($urlTuples)) return [];

        /**
         * @var array $preparedResults Stores "saved_post_id"s as key and tuple object as value.
         */
        $preparedResults = [];

        foreach($urlTuples as $key => $value) {
            if(!isset($value->saved_post_id)) continue;

            $preparedResults[$value->saved_post_id] = $value;
        }

        // Get the posts with the "saved_post_id"s
        $posts = $this->getPosts(array_keys($preparedResults));

        // Add each post the corresponding tuple from URLs table.
        foreach($posts as $post) {
            /** @var WP_Post $post */
            if(!isset($preparedResults[$post->ID])) continue;

            // Add 'wpcc' to the post with 'site' data.
            $post->wpcc = $this->addSiteDataToUrlTuples($preparedResults[$post->ID])[0];
        }

        return $posts;
    }

    /**
     * Get the latest URLs added to the queue.
     *
     * @param null|int $limit   Max number of items that should be retrieved. If this is null, {@link lastPostsLimit}
     *                          will be used.
     * @return \object[] An array of rows from URLs table, with each having a 'site' key storing the site as
     *                          {@link WP_Post}.
     */
    private function getLastAddedUrls($limit = null) {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();
        $query = "SELECT * FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND saved_at IS NULL
            AND is_locked = FALSE
            AND is_saved = FALSE
          ORDER BY created_at DESC LIMIT %d;";

        // Get the URLs
        $results = $wpdb->get_results($wpdb->prepare($query, $limit != null ? $limit : $this->lastPostsLimit));
        if(!$results) return [];

        // Now, get the site for each URL tuple so that we can show site info with the URL.
        $results = $this->addSiteDataToUrlTuples($results);

        return $results;
    }

    /**
     * Get the latest URLs deleted from the database.
     *
     * @param null|int $limit   Max number of items that should be retrieved. If this is null, {@link lastPostsLimit}
     *                          will be used.
     * @return \object[] An array of rows from URLs table, with each having a 'site' key storing the site as
     *                          {@link WP_Post}.
     */
    private function getLastDeletedUrls($limit = null) {
        global $wpdb;
        $tableUrls = Factory::databaseService()->getDbTableUrlsName();
        $query = "SELECT * FROM {$tableUrls}
          WHERE saved_post_id IS NULL
            AND is_locked = FALSE
            AND deleted_at IS NOT NULL
          ORDER BY deleted_at DESC LIMIT %d;";

        // Get the URLs
        $results = $wpdb->get_results($wpdb->prepare($query, $limit != null ? $limit : $this->lastPostsLimit));
        if(!$results) return [];

        // Now, get the site for each URL tuple so that we can show site info with the URL.
        $results = $this->addSiteDataToUrlTuples($results);

        return $results;
    }

    /**
     * Adds 'site' data to each URL tuple.
     *
     * @param object|object[] $urlTuples A single URL tuple object or an array of URL tuple objects.
     * @return object[] An array of URL tuple objects with each having a 'site' key, which stores a site as
     *                  {@link WP_Post}.
     */
    private function addSiteDataToUrlTuples($urlTuples) {
        if(!$urlTuples) return $urlTuples;

        if(!is_array($urlTuples) && is_object($urlTuples)) $urlTuples = [$urlTuples];

        // First, get the site IDs.
        $siteIds = [];
        foreach($urlTuples as $urlTuple) $siteIds[] = $urlTuple->post_id;

        // Get the sites.
        $sites = $this->getPosts($siteIds, Constants::$POST_TYPE);

        // Add each URL its site data.
        $urlTuples = array_map(function($result) use (&$sites) {
            foreach($sites as $site) {
                if($site->ID == $result->post_id) {
                    $result->site = $site;
                    break;
                }
            }

            return $result;

        }, $urlTuples);

        return $urlTuples;
    }

    /**
     * @param array  $ids IDs of the posts
     * @param string $postType Post type
     * @return \WP_Post[]
     */
    private function getPosts($ids = [], $postType = 'post') {
        if(!$ids) return [];

        return get_posts([
            'numberposts' => -1,
            'orderby'     => 'post__in',
            'post_type'   => $postType,
            'include'     => $ids,
            'post_status' => 'any',
        ]);
    }

    /**
     * Get today's date at midnight.
     *
     * @return string Date formatted as <b>Y-m-d H:i:s</b> (MySQL)
     */
    private function getToday() {
        $now = time() + $this->getGMTOffset();
        return date("Y-m-d", $now) . " 00:00:00";
    }

    /**
     * Get GMT offset in seconds
     *
     * @return int GMT offset in seconds.
     */
    private function getGMTOffset() {
        $utcOffset = get_option('gmt_offset', null);
        return $utcOffset != null ? ($utcOffset * 60 * 60) : 0;
    }

    /**
     * Prepares the results structured as [post_id => '', 'count' => '' ] as [post_id => count]
     *
     * @param array $results An array of objects with each object having 'post_id' and 'count' keys.
     * @return array Prepared results
     */
    private function preparePostIdCountDbResults($results) {
        $prepared = [];
        foreach($results as $result) {
            $prepared[$result->post_id] = $result->count;
        }

        return $prepared;
    }
}