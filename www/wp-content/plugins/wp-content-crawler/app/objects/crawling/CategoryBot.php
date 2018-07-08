<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 23:49
 */

namespace WPCCrawler\objects\crawling;


use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\objects\crawling\data\CategoryData;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

class CategoryBot extends AbstractBot {

    private $keyLastEmptySelectorEmailDate = '_last_category_empty_selector_email_sent';

    /**
     * Collects URLs for a site from the given URL
     *
     * @param string                $url    A full URL to be used to get post URLs
     * @return CategoryData|null
     *
     * @return array An array with keys "post_urls" and "next_page_url"
     */
    public function collectUrls($url) {
        $this->resetFindAndReplaceErrors();
        $categoryData = new CategoryData();

        $findAndReplacesForFirstLoad            = $this->getSetting('_category_find_replace_first_load');
        $categoryUnnecessaryElementSelectors    = $this->getSetting('_category_unnecessary_element_selectors');
        $categoryLinkSelectors                  = $this->getSetting('_category_post_link_selectors');
        $categoryUrlsInReverse                  = $this->getSetting('_category_collect_in_reverse_order');
        $categorySaveThumbnails                 = $this->getSetting('_category_post_save_thumbnails');
        $categoryNextPageSelectors              = $this->getSetting('_category_next_page_selectors');
        $notifyWhenEmptySelectors               = $this->getSetting('_category_notify_empty_value_selectors');

        /**
         * Fires just before the source code of a category page is retrieved from the target site.
         *
         * @param int siteId        ID of the site
         * @param string $url       URL of the target category page
         * @param $this CategoryBot The bot itself
         * @since 1.6.3
         */
        do_action('wpcc/category/source-code/before_retrieve', $this->getSiteId(), $url, $this);

        $crawler = $this->request($url, "GET");
        if(!$crawler) return null;

        /**
         * Fires just after the source code of a post page is retrieved from the target site.
         *
         * @param int siteId        ID of the site
         * @param string $url       URL of the post
         * @param $this CategoryBot The bot itself
         * @param crawler Crawler   Crawler containing raw, unmanipulated source code of the target category
         * @since 1.6.3
         */
        do_action('wpcc/post/source-code/after_retrieve', $this->getSiteId(), $url, $this, $crawler);

        /**
         * Modify the raw crawler that contains source code of the target category page
         *
         * @param crawler Crawler   Crawler containing raw, unmanipulated source code of the target category page
         * @param int siteId        ID of the site
         * @param string $postUrl   URL of the category page
         * @param $this PostBot     The bot itself
         *
         * @return Crawler          Modified crawler
         * @since 1.6.3
         */
        $crawler = apply_filters('wpcc/category/crawler/raw', $crawler, $this->getSiteId(), $url, $this);

        // Replace relative URLs with direct URLs
        $crawler = $this->makeInitialReplacements($crawler, $findAndReplacesForFirstLoad);

        // Apply HTML manipulations
        $this->applyFindAndReplaceInElementAttributes($crawler, '_category_find_replace_element_attributes');
        $this->applyExchangeElementAttributeValues($crawler, '_category_exchange_element_attributes');
        $this->applyRemoveElementAttributes($crawler, '_category_remove_element_attributes');
        $this->applyFindAndReplaceInElementHTML($crawler, '_category_find_replace_element_html');

        // Clear the crawler from unnecessary category elements
        $this->removeElementsFromCrawler($crawler, $categoryUnnecessaryElementSelectors);

        /**
         * Modify the prepared crawler that contains source code of the target category page. At this point, the crawler
         * was manipulated. Unnecessary elements were removed, find-and-replace options were applied, etc.
         *
         * @param crawler Crawler   Crawler containing manipulated source code of the target category page
         * @param int siteId        ID of the site
         * @param string $postUrl   URL of the category page
         * @param $this PostBot     The bot itself
         *
         * @return Crawler          Modified crawler
         * @since 1.6.3
         */
        $crawler = apply_filters('wpcc/category/crawler/prepared', $crawler, $this->getSiteId(), $url, $this);

        // Get post urls
        if($postUrlData = $this->extractData($crawler, $categoryLinkSelectors, "href", "url", false, true)) {
            // Make relative URLs direct
            foreach($postUrlData as &$mPostUrl) {
                $mPostUrl["data"] = Utils::prepareUrl($this->getSiteUrl(), $mPostUrl["data"], $url);
            }

            $categoryData->setPostUrls($postUrlData);
        }

        // Get thumbnail URLs
        if($categorySaveThumbnails) {
            $categoryPostThumbnailSelectors = $this->getSetting('_category_post_thumbnail_selectors');
            $findAndReplacesForThumbnailUrl = $this->getSetting('_category_find_replace_thumbnail_url');

            $thumbnailData = null;
            foreach($categoryPostThumbnailSelectors as $selector) {
                if ($thumbnailData = $this->extractData($crawler, $selector, "src", "thumbnail", false, true)) {
                    // Make replacements
                    if(!empty($thumbnailData) && !empty($findAndReplacesForThumbnailUrl)) {
                        foreach($thumbnailData as &$mThumbnailData) {
                            $mThumbnailData["data"] = $this->findAndReplace($findAndReplacesForThumbnailUrl, $mThumbnailData["data"]);
                        }
                    }

                    // Make relative URLs direct
                    foreach($thumbnailData as &$nThumbnailData) {
                        $nThumbnailData["data"] = Utils::prepareUrl($this->getSiteUrl(), $nThumbnailData["data"], $url);
                    }

                    $categoryData->setThumbnails($thumbnailData);
                    break;
                }
            }

            // Match thumbnails with post URLs
            if($thumbnailData && !empty($thumbnailData)) {
                // Combine URL and thumbnail data and sort the combined array ascending by start position
                $postDataCombined = array_merge($thumbnailData, $postUrlData);

                // Sort the combined data and reset the array keys
                $postDataCombined = array_values(Utils::array_msort($postDataCombined, ["start" => SORT_ASC]));

                $isLinkBeforeThumb = $this->getSetting('_category_post_is_link_before_thumbnail');

                $thumbnailHolder = null;
                for($i = 0; $i < sizeof($postDataCombined); $i++) {
                    $thumbnailHolder = null;
                    if($postDataCombined[$i]["type"] == "url") {
                        // Check if the url has a thumbnail
                        // If the link comes BEFORE the thumb
                        if($isLinkBeforeThumb && isset($postDataCombined[$i + 1])) {
                            if($postDataCombined[$i + 1]["type"] == "thumbnail") {
                                $thumbnailHolder = $postDataCombined[$i + 1]["data"];
                            }

                        // If the link comes AFTER the thumb
                        } else {
                            if($i !== 0 && $postDataCombined[$i - 1]["type"] == "thumbnail") {
                                $thumbnailHolder = $postDataCombined[$i - 1]["data"];
                            }
                        }

                        // If the thumbnail is found, add it to the postUrlData
                        if($thumbnailHolder) {
                            foreach($postUrlData as &$mUrlData) {
                                if(
                                    $mUrlData["data"] == $postDataCombined[$i]["data"] &&
                                    $mUrlData["start"] == $postDataCombined[$i]["start"] &&
                                    $mUrlData["end"] == $postDataCombined[$i]["end"]
                                ) {
                                    $mUrlData["thumbnail"] = $thumbnailHolder;
                                    break;
                                }
                            }
                        }
                    }
                }

                $categoryData->setPostUrls($postUrlData);

                unset($postDataCombined);
                unset($thumbnailData);
            }
        }

        // If the order of the URLs should be reversed, do so.
        if($categoryUrlsInReverse) {
            $categoryData->setPostUrls(array_reverse($categoryData->getPostUrls()));
        }

        // Get the next page url
        foreach($categoryNextPageSelectors as $nextPageSelector) {
            $attr = isset($nextPageSelector["attr"]) && $nextPageSelector["attr"] ? $nextPageSelector["attr"] : "href";

            if ($nextPageUrl = $this->extractData($crawler, $nextPageSelector["selector"], $attr, false, true, true)) {
                $categoryData->setNextPageUrl(Utils::prepareUrl($this->getSiteUrl(), $nextPageUrl, $url));
                break;
            }
        }

        $categoryData->setErrors($this->getFindAndReplaceErrors());

        /**
         * Modify the prepared CategoryData object, which stores all the required data retrieved from the target site.
         *
         * @param CategoryData $categoryData    Prepared CategoryData object
         * @param int siteId                    ID of the site
         * @param string $postUrl               URL of the category page
         * @param $this CategoryBot             The bot itself
         * @param crawler Crawler               Crawler containing manipulated source code of the target category page
         *
         * @return CategoryData     Modified CategoryData
         * @since 1.6.3
         */
        $categoryData = apply_filters('wpcc/category/category-data', $categoryData, $this->getSiteId(), $url, $this, $crawler);

        /*
         * NOTIFY
         */

        // Notify if this is not a test.
        if(!WPCCrawler::isDoingTest() && $notifyWhenEmptySelectors)
            $this->notifyUser($url, $crawler, $notifyWhenEmptySelectors, $this->keyLastEmptySelectorEmailDate);

        /**
         * Fires just after the category data is prepared according to the settings. All of the necessary changes were made
         * to the category data, such as removal of unnecessary elements and replacements.
         *
         * @param int siteId            ID of the site
         * @param string $url           URL of the target category page
         * @param $this CategoryBot     The bot itself
         * @param postData CategoryData The data retrieved from the target site by using the settings configured by the
         *                              user.
         * @param crawler  Crawler      Crawler containing the target category's source code. The crawler was manipulated
         *                              according to the settings.
         * @since 1.6.3
         */
        do_action('wpcc/category/data/after_prepared', $this->getSiteId(), $url, $this, $categoryData, $crawler);

        return $categoryData;
    }
}