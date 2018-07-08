<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 21:56
 */

namespace WPCCrawler\objects\savers;

use WPCCrawler\Factory;
use WPCCrawler\objects\crawling\CategoryBot;
use WPCCrawler\objects\traits\SettingsTrait;
use WPCCrawler\Utils;

class UrlSaver extends AbstractSaver {

    use SettingsTrait;

    private static $DEBUG = false;

    /** @var string Stores ID of the site which is checked for URL collection (category crawling) the last */
    public $optionLastCheckedSiteId    = '_wpcc_last_checked_site_id';

    /**
     * Collect URLs and store in the database. This method gets next category URL from category map of the site. Then,
     * collects URLs of a page in that category. The page of the category is tracked. So, if last crawled category page
     * number is 2, this method goes and collects the URLs on page 3, and so on.
     *
     * @param int  $siteIdToCheck Site ID for which the URLs will be collected
     */
    public function executeUrlSave($siteIdToCheck) {
        $this->setRequestMade(false);

        // Get site ID to check
        if(!$siteIdToCheck) {
            $this->resetLastChecked(null);
            return;
        }

        // Now, get the site's categories
        $settings = get_post_meta($siteIdToCheck);
        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $categories = $this->getSetting('_category_map');

        // If there is no category, then do not continue.
        if(empty($categories)) {
            $this->resetLastChecked($siteIdToCheck);
            error_log("WPCC - No category is found for site ID: " . $siteIdToCheck);
            return;
        }

        // Reset the array keys. The keys of the array may start from a number other than zero, or may not increase one by one.
        // So, make sure the array keys start from 0 and increase one by one.
        $categories = array_values($categories);

//        if(static::$DEBUG) var_dump("categories");
//        if(static::$DEBUG) var_dump($categories);

        /**
         * Holds the category URL to be checked. This URL is the same as the URL in category map that the user prepared.
         * This is used as an ID among all categories.
         * @var string $categoryUrlToCheck
         */
        $categoryUrlToCheck = null;

        /**
         * Holds ID of the category in which found URLs will be inserted, when they are crawled.
         * @var string $targetCategoryId
         */
        $targetCategoryId = null;

        /**
         * Holds the URL to be crawled.
         * @var string $categoryCurrentPageUrl
         */
        $categoryCurrentPageUrl = null;

        /**
         * Holds number of pages crawled for the current category
         * @var int $crawledPageCount
         */
        $crawledPageCount = 0;

        // Get last checked category URL
        $lastCheckedCategoryUrl = $this->getSetting('_cron_last_checked_category_url');

        // If there is no last checked category URL, then get the first category url
        if(!$lastCheckedCategoryUrl) {
            // If the first category does not have a URL or cat_id, then do not continue.
            if(!isset($categories[0]["url"]) || !$categories[0]["url"]) {
                $this->resetLastChecked($siteIdToCheck);
                error_log("WPCC - Category URL is not valid for site ID: " . $siteIdToCheck);
                return;
            }

            if(!isset($categories[0]["cat_id"]) || !$categories[0]["cat_id"]) {
                $this->resetLastChecked($siteIdToCheck);
                error_log("WPCC - Category ID is not valid for site ID: " . $siteIdToCheck);
                return;
            }

            $categoryUrlToCheck     = $categories[0]["url"];
            $categoryCurrentPageUrl = $categoryUrlToCheck;
            $targetCategoryId       = $categories[0]["cat_id"];

            // Otherwise, get the next page URL of the last-checked category
        } else {
//                if(static::$DEBUG) var_dump("there is a last-checked-category-url");
            // Get next page of the last-checked category. If there is none, set the next category as category-to-check.
            $categoryUrlToCheck     = $lastCheckedCategoryUrl;
            $categoryCurrentPageUrl = $this->getSetting('_cron_last_checked_category_next_page_url');
            $crawledPageCount       = $this->getSetting('_cron_crawled_page_count');
            if(!$crawledPageCount) $crawledPageCount = 0;

//                if(static::$DEBUG) var_dump($categoryUrlToCheck);
//                if(static::$DEBUG) var_dump($categoryCurrentPageUrl);

            // Get the position of the URL in category map and set target category ID
            $lastCheckedCategoryUrlPos = false;
            foreach($categories as $key => $category) {
                if(isset($category["url"]) && $category["url"] == $categoryUrlToCheck) {
                    $lastCheckedCategoryUrlPos = $key;
                    $targetCategoryId = $category["cat_id"];
//                        if(static::$DEBUG) var_dump("last checked cat id pos is found.");
//                        if(static::$DEBUG) var_dump($category["url"]);
//                        if(static::$DEBUG) var_dump($key);
                    break;
                }
            }

            if(static::$DEBUG) var_dump("Last checked category URL pos is " . $lastCheckedCategoryUrlPos);

            // If there is no next page of the previously crawled category, then move on to the next category
            if(!$categoryCurrentPageUrl) {
                // Get the next category url
                if($lastCheckedCategoryUrlPos === false) {
                    // If somehow the position is not found, then go with the first category.
                    $categoryUrlToCheck = $categories[0]["url"];
                    $targetCategoryId   = $categories[0]["cat_id"];

                } else {
                    // If last checked category url is the last item in the array, then go get the first item. Otherwise,
                    // get the next item.
                    $mCategory = $categories[
                    $lastCheckedCategoryUrlPos != sizeof($categories) - 1 ? $lastCheckedCategoryUrlPos + 1 : 0
                    ];
//                        if(static::$DEBUG) var_dump("mCategory");
//                        if(static::$DEBUG) var_dump($mCategory);
                    $categoryUrlToCheck = $mCategory["url"];
                    $targetCategoryId   = $mCategory["cat_id"];
                }

                $categoryCurrentPageUrl = $categoryUrlToCheck;
                $crawledPageCount       = 0;
            }
        }

        $mainSiteUrl = $this->getSetting('_main_page_url');

        // Make sure we have a valid URL
        $categoryCurrentPageUrl = Utils::prepareUrl($mainSiteUrl, $categoryCurrentPageUrl);
        if(!$categoryCurrentPageUrl) {
            $this->updateLastChecked($siteIdToCheck, $categoryUrlToCheck, false);
            return;
        };

        // Now, get post URLs.
        $bot = new CategoryBot($settings, $siteIdToCheck);
        $data = $bot->collectUrls($categoryCurrentPageUrl);
        $this->setRequestMade(true);
//        if(static::$DEBUG) var_dump($data);

        // If the data is null, update last checked and write an error message.
        if($data === null) {
            $this->updateLastChecked($siteIdToCheck, $categoryUrlToCheck, false);
            error_log("WPCC - Cannot get the data from URL: " . $categoryCurrentPageUrl . " of site: " . $siteIdToCheck);
            return;
        }

        if(!$data->getPostUrls()) {
            $this->updateLastChecked($siteIdToCheck, $categoryUrlToCheck, false);
            return;
        }

        // Insert the URLs into db.
        $insertCount = 0;
        foreach($data->getPostUrls() as $key => $item) {
//                if(static::$DEBUG) var_dump($item);
            if($item["data"]) {
                $thumbnailUrl = isset($item["thumbnail"]) ? $item["thumbnail"] : null;
                $postUrl = $item["data"];

                /**
                 * Fires just before a post URL is inserted to the database table storing the URLs.
                 *
                 * @param UrlSaver $this        The UrlSaver itself
                 * @param int $siteIdToCheck    ID of the site that this URL belongs to
                 * @param string $postUrl       Post URL for which a new entry in the db will be created
                 * @param array $item           An array containing the data about to-be-inserted URL
                 * @since 1.6.3
                 */
                do_action('wpcc/category/url/before_insert_url', $this, $siteIdToCheck, $postUrl, $item);

                if($urlId = Factory::databaseService()->addUrl($siteIdToCheck, $postUrl, $thumbnailUrl, $targetCategoryId)) {
                    $insertCount++;

                    /**
                     * Fires just after a post URL is inserted to the database table storing the URLs.
                     *
                     * @param UrlSaver $this        The UrlSaver itself
                     * @param int $siteIdToCheck    ID of the site that this URL belongs to
                     * @param string $postUrl       Post URL for which a new entry in the db was created
                     * @param array $item           An array containing the data about to-be-inserted URL
                     * @param int $urlId            ID of the inserted URL
                     * @since 1.6.3
                     */
                    do_action('wpcc/category/url/after_insert_url', $this, $siteIdToCheck, $postUrl, $item, $urlId);
                }
            }
        }

        // If the next page URL is the same as the current page, then set next page url to false. By this way, the next
        // request will be for another category.
        $nextPageUrl = $data->getNextPageUrl() ? $data->getNextPageUrl() : false;

        if($nextPageUrl && $nextPageUrl == $categoryCurrentPageUrl) $nextPageUrl = false;

        // If there is a next page and no new URLs inserted in this page, act how the user wants, stop or continue looking
        if(!$this->handleNoNewUrlInsertedCount($siteIdToCheck, $settings, $nextPageUrl, $insertCount)) {
            $nextPageUrl = false;
            if(static::$DEBUG) var_dump("Stop trying to find new post URLs in the next pages. Go back to the first page to find new URLs on the next crawl.");
        }

        // Set crawled page count
        if($nextPageUrl) {
            if($this->getSetting('_do_not_use_general_settings')) {
                $pageCrawlLimit = $this->getSetting('_wpcc_max_page_count_per_category');
                if(!$pageCrawlLimit) $pageCrawlLimit = 0;
            } else {
                $pageCrawlLimit = get_option('_wpcc_max_page_count_per_category', 0);
            }
            if(static::$DEBUG) var_dump("Page Crawl Limit:" . $pageCrawlLimit);

            // If the limit is reached, set next page URL false. By this way, the next crawl will be for first page of the category.
            if($pageCrawlLimit != 0 && $crawledPageCount >= $pageCrawlLimit) {
                // Here, no need to reset crawled page count. It will be reset automatically on the next run, when a new
                // category is crawled.
                $nextPageUrl = false;
                if(static::$DEBUG) var_dump("Max page count is reached. Stop crawling this category and go back to the first page next time.");

                // Otherwise, increase crawled page count.
            } else {
                Utils::savePostMeta($siteIdToCheck, '_cron_crawled_page_count', ++$crawledPageCount, true);
            }

        }

        $this->updateLastChecked($siteIdToCheck, $categoryUrlToCheck, $nextPageUrl);

        if(static::$DEBUG) var_dump("Current site ID was "         . $siteIdToCheck . ", and main URL was " . $mainSiteUrl);
        if(static::$DEBUG) var_dump("Target category ID was "      . $targetCategoryId);
        if(static::$DEBUG) var_dump("Insert count: "               . $insertCount);
        if(static::$DEBUG) var_dump("Current category URL was "    . $categoryUrlToCheck);
        if(static::$DEBUG) var_dump("Current page URL was "        . $categoryCurrentPageUrl);
        if(static::$DEBUG) var_dump("Next page URL is "            . ($nextPageUrl ? $nextPageUrl : 'false'));
        if(static::$DEBUG) var_dump("Crawled page count is "       . $crawledPageCount);

    }

    /**
     * Handle the number of pages crawled with no new URL insertion. Here is the scenario. By the time the
     * crawler goes to get new post URLs from the category, there may be more than one new page of posts added to the
     * target category. In this case, the crawler won't find any new URLs. Let's say there are 3 items per page. The
     * crawler goes and gets the first page (Items 1, 2, and 3). Next time, the crawler goes and gets the second page
     * (Items 4, 5 and 6). Before the crawler goes and gets the 3rd page, the target site adds 6 more items. In this case,
     * 3rd page will now have items 1, 2 and 3; and 4th page will have items 4, 5, and 6. So, when the crawler tries to
     * get 3rd page, it will not find any new URLs. It will be the same for the 4th page. However, there are more posts
     * after page 4. In this case, what are we gonna do? Do we give up or continue to look for all of the pages?
     *
     * So, this can be left to the user to decide. We keep track of how many pages we crawled with no new URLs. The user
     * will set a limit for this. For example, he/she may say, stop after 4 pages with no new URLs. Then we stop at 4th
     * page with no new URLs and start from the first page again.
     *
     * This function will handle the count of page-crawls with no new URL for the specified site.
     *
     * @param int $siteId The ID of the site
     * @param array $settings Settings for the site (post meta)
     * @param string|bool $nextPageUrl Next page URL for the category
     * @param int $insertedCount Number of new URLs inserted for current page
     * @return bool True if the next page should be crawled, false if the crawler should go and check the first page on next
     * crawl.
     */
    private function handleNoNewUrlInsertedCount($siteId, $settings, $nextPageUrl, $insertedCount) {
        if($insertedCount == 0 && $nextPageUrl) {
            if(!$this->getSettings()) $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

            // Get current count
            $currentTrialCount = $this->getSetting('_cron_no_new_url_inserted_count');
            if($this->getSetting('_do_not_use_general_settings')) {
                $trialLimit = $this->getSetting('_wpcc_no_new_url_page_trial_limit');
            } else {
                $trialLimit = get_option('_wpcc_no_new_url_page_trial_limit');
            }

            if (!$currentTrialCount) $currentTrialCount = 0;
            if (!$trialLimit) $trialLimit = 0;

            if(static::$DEBUG) var_dump("Current trial count: "    . $currentTrialCount);
            if(static::$DEBUG) var_dump("Trial limit: "            . $trialLimit);

            $shouldContinue = $trialLimit === 0 || $currentTrialCount < $trialLimit;

            // If we should stop, then reset the count. Otherwise, increase the count by one.
            Utils::savePostMeta($siteId, '_cron_no_new_url_inserted_count', !$shouldContinue ? 0 : $currentTrialCount + 1, true);
            return $shouldContinue;

        } else {
            // We have newly inserted values, reset the count.
            Utils::savePostMeta($siteId, '_cron_no_new_url_inserted_count', 0, true);
        }

        return true;
    }

    /**
     * Sets the values required to keep track of URL collection from categories
     *
     * @param int $siteId The ID of the site to be marked as last-checked
     * @param string $lastCheckedCategoryUrl One of the category URLs belonging the site.
     * @param string|bool $nextPageOfLastCheckedCategory Next page of the last checked category, or false if there is none.
     */
    private function updateLastChecked($siteId, $lastCheckedCategoryUrl, $nextPageOfLastCheckedCategory) {
        // Set last checked category ID and next page URL to be crawled for the category
        Utils::savePostMeta($siteId, '_cron_last_checked_category_url', $lastCheckedCategoryUrl, true);
        Utils::savePostMeta($siteId, '_cron_last_checked_category_next_page_url', $nextPageOfLastCheckedCategory, true);
        Utils::savePostMeta($siteId, '_cron_last_checked_at', current_time('mysql'), true);

        // Set last checked site id
        update_option($this->optionLastCheckedSiteId, $siteId, false);
    }

    /**
     * Resets CRON options and post metas for a site
     * @param int $siteId
     */
    public function resetLastChecked($siteId) {
        $this->updateLastChecked($siteId, null, null);
    }

}