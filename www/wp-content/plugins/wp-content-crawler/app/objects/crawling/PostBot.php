<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 23:50
 */

namespace WPCCrawler\objects\crawling;


use DateTime;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\objects\crawling\data\PostData;
use WPCCrawler\objects\Settings;
use WPCCrawler\objects\translation\PostTranslator;
use WPCCrawler\Utils;
use WPCCrawler\WPCCrawler;

class PostBot extends AbstractBot {

    /** @var Crawler */
    private $crawler;
    
    /** @var PostData */
    private $postData;
    
    /*
     * 
     */

    /** @var array */
    private $findAndReplacesForMedia = [];

    /** @var array */
    private $combinedListData = [];

    /** @var array */
    private $listItems = [];

    /** @var string */
    private $postUrl = '';

    /** @var array */
    private $predefinedPreparedShortCodes = [];

    /*
     *
     */

    private $removeLinksFromShortCodes = false;
    private $removeLinksFindAndReplaceConfig = null;

    public static $REMOVE_LINKS_FIND = '/<a\b[^>]*>((?:\n|.)*?)<\/a>/';
    public static $REMOVE_LINKS_REPLACE = '$1';

    private $keyLastEmptySelectorEmailDate = '_last_post_empty_selector_email_sent';

    /**
     * Crawls a post and prepares the data as array, does not save the post to the db.
     *
     * @param string $postUrl A full URL
     * @return PostData|null
     */
    public function crawlPost($postUrl) {
        $this->resetFindAndReplaceErrors();
        $this->postUrl  = $postUrl;
        $this->postData = new PostData();

        $findAndReplacesForFirstLoad        = $this->getSetting('_post_find_replace_first_load');
        $postUnnecessaryElementSelectors    = $this->getSetting('_post_unnecessary_element_selectors');
        $this->removeLinksFromShortCodes    = $this->getSetting('_post_remove_links_from_short_codes');
        $notifyWhenEmptySelectors           = $this->getSetting('_post_notify_empty_value_selectors');

        /**
         * Fires just before the source code of a post page is retrieved from the target site.
         *
         * @param int siteId        ID of the site
         * @param string $postUrl   URL of the post
         * @param PostBot $this     The bot itself
         * @since 1.6.3
         */
        do_action('wpcc/post/source-code/before_retrieve', $this->getSiteId(), $postUrl, $this);

        $this->crawler = $this->request($postUrl, "GET");
        if(!$this->crawler) return null;

        /**
         * Fires just after the source code of a post page is retrieved from the target site.
         *
         * @param int siteId        ID of the site
         * @param string $postUrl   URL of the post
         * @param PostBot $this     The bot itself
         * @param Crawler $crawler  Crawler containing raw, unmanipulated source code of the target post
         * @since 1.6.3
         */
        do_action('wpcc/post/source-code/after_retrieve', $this->getSiteId(), $postUrl, $this, $this->crawler);

        /**
         * Modify the raw crawler that contains source code of the target post page
         *
         * @param Crawler $crawler  Crawler containing raw, unmanipulated source code of the target post
         * @param int siteId        ID of the site
         * @param string $postUrl   URL of the post
         * @param PostBot $this     The bot itself
         *
         * @return Crawler          Modified crawler
         * @since 1.6.3
         */
        $this->crawler = apply_filters('wpcc/post/crawler/raw', $this->crawler, $this->getSiteId(), $postUrl, $this);

        // Make initial replacements.
        $this->crawler = $this->makeInitialReplacements($this->crawler, $findAndReplacesForFirstLoad, true);

        // Apply HTML manipulations
        $this->applyFindAndReplaceInElementAttributes($this->crawler, '_post_find_replace_element_attributes');
        $this->applyExchangeElementAttributeValues($this->crawler, '_post_exchange_element_attributes');
        $this->applyRemoveElementAttributes($this->crawler, '_post_remove_element_attributes');
        $this->applyFindAndReplaceInElementHTML($this->crawler, '_post_find_replace_element_html');

        // Prepare pagination info
        $this->preparePaginationInfo();

        // Clear the crawler from unnecessary post elements
        $this->removeElementsFromCrawler($this->crawler, $postUnnecessaryElementSelectors);

        /**
         * Modify the prepared crawler that contains source code of the target post page. At this point, the crawler was
         * manipulated. Unnecessary elements were removed, find-and-replace options were applied, etc.
         *
         * @param crawler Crawler   Crawler containing manipulated source code of the target post
         * @param int siteId        ID of the site
         * @param string $postUrl   URL of the post
         * @param PostBot $this     The bot itself
         *
         * @return Crawler          Modified crawler
         * @since 1.6.3
         */
        $this->crawler = apply_filters('wpcc/post/crawler/prepared', $this->crawler, $this->getSiteId(), $postUrl, $this);

        // Get title
        $this->prepareTitle();

        // Get excerpt
        $this->prepareExcerpt();

        // Get contents
        $this->prepareContents();

        // Get the date
        $this->prepareDateCreated();

        // Get custom short code contents
        $this->prepareShortCodeInfo();

        // Get list items
        $this->prepareListInfo();

        // Get tags and meta info
        $this->prepareMetaAndTagInfo();

        // Get source URLs of to-be-saved files and thumbnail image URL
        $this->prepareAndSaveMedia();

        // Get custom post meta
        $this->prepareCustomPostMeta();

        /*
         * TEMPLATING
         */

        // Insert main data into template
        $this->prepareTemplates();

        // Get find-and-replace errors
        $this->postData->setErrors($this->getFindAndReplaceErrors());

        /*
         * TRANSLATE
         */

        // Translate if it is required
        $this->maybeTranslate();

        /*
         *
         */

        /**
         * Modify the prepared PostData object, which stores all the required data retrieved from the target site.
         *
         * @param PostData $postData Prepared PostData object
         * @param int      siteId    ID of the site
         * @param string   $postUrl  URL of the post
         * @param PostBot  $this     The bot itself
         * @param Crawler  $crawler  Crawler containing manipulated source code of the target post
         * @return PostData     Modified PostData
         * @since 1.6.3
         */
        $this->postData = apply_filters('wpcc/post/post-data', $this->postData, $this->getSiteId(), $postUrl, $this, $this->crawler);

        /*
         * NOTIFY
         */

        // Notify if this is not a test.
        if(!WPCCrawler::isDoingTest() && $notifyWhenEmptySelectors)
            $this->notifyUser($postUrl, $this->crawler, $notifyWhenEmptySelectors, $this->keyLastEmptySelectorEmailDate);

        /**
         * Fires just after the post data is prepared according to the settings. All of the necessary changes were made
         * to the post data, such as removal of unnecessary elements and replacements.
         *
         * @param int      siteId    ID of the site
         * @param string   $postUrl  URL of the post
         * @param PostBot  $this     The bot itself
         * @param PostData $postData The data retrieved from the target site by using the settings configured by the user.
         * @param Crawler  $crawler  Crawler containing the target post page's source code. The crawler was manipulated
         *                           according to the settings.
         * @since 1.6.3
         */
        do_action('wpcc/post/data/after_prepared', $this->getSiteId(), $postUrl, $this, $this->postData, $this->crawler);

        return $this->postData;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Prepares the title
     */
    private function prepareTitle() {
        $postTitleSelectors         = $this->getSetting('_post_title_selectors');
        $findAndReplacesForTitle    = $this->prepareFindAndReplaces($this->getSetting('_post_find_replace_title'));

        if($title = $this->extractData($this->crawler, $postTitleSelectors, "text", false, true, true)) {
            $title = $this->findAndReplace($findAndReplacesForTitle, $title);
            $this->postData->setTitle($title);
        }
    }

    /**
     * Prepares the excerpt
     */
    private function prepareExcerpt() {
        $postExcerptSelectors       = $this->getSetting('_post_excerpt_selectors');
        $findAndReplacesForExcerpt  = $this->prepareFindAndReplaces($this->getSetting('_post_find_replace_excerpt'));

        if($excerpt = $this->extractData($this->crawler, $postExcerptSelectors, "html", "excerpt", true, true)) {
            $excerpt["data"] = trim($this->findAndReplace($findAndReplacesForExcerpt, $excerpt["data"]));
            $this->postData->setExcerpt($excerpt);
        }
    }

    /**
     * Prepares contents using post content selectors
     */
    private function prepareContents() {
        $postContentSelectors = $this->getSetting('_post_content_selectors');

        if($contents = $this->extractData($this->crawler, $postContentSelectors, "html", "content", false, true)) {
            $contents = Utils::array_msort($contents, ['start' => SORT_ASC]);
            $this->postData->setContents($contents);
        }
    }

    /**
     * Prepares the date using date selectors
     */
    private function prepareDateCreated() {
        $dateSelectors          = $this->getSetting('_post_date_selectors');
        $findAndReplacesForDate = $this->getSetting('_post_find_replace_date');
        $minutesToAdd           = $this->getSetting('_post_date_add_minutes');

        $finalDate = current_time('mysql');

        if($dateSelectors) {
            foreach($dateSelectors as $dateSelector) {
                $attr = isset($dateSelector["attr"]) && $dateSelector["attr"] ? $dateSelector["attr"] : "content";
                if($date = $this->extractData($this->crawler, $dateSelector["selector"], $attr, false, true, true)) {
                    // Apply find-and-replaces
                    $date = $this->findAndReplace($findAndReplacesForDate, $date);

                    // Get the timestamp. If there is a valid timestamp, prepare the date and assign it
                    // to postData
                    if($timestamp = strtotime($date)) {
                        // Get the date in MySQL date format.
                        $finalDate = date(Constants::$MYSQL_DATE_FORMAT, $timestamp);

                        // No need to continue. One match is enough.
                        break;
                    }
                }
            }
        }

        // Create a DateTime object for the date so that we can manipulate it as we please.
        $dt = new DateTime($finalDate);

        // Now, manipulate the date if the user defined how many minutes should be added to the date.
        if($minutesToAdd) {
            // Minutes can be comma-separated. Get each minute by making sure they are integers.
            $minutes = array_map(function ($m) {
                return (int) trim($m);
            }, explode(",", $minutesToAdd));

            // If there are minutes, get a random one and add it to the date.
            if($minutes) {
                $dt->modify($minutes[array_rand($minutes)] . " minute");
            }
        }

        // Set the date in postData after formatting it by MySQL date format
        $this->postData->setDateCreated($dt->format(Constants::$MYSQL_DATE_FORMAT));
    }

    /**
     * Prepare pagination info by getting next page URL and/or all page URLs.
     */
    private function preparePaginationInfo() {
        $postIsPaginate           = $this->getSetting('_post_paginate');
        $postNextPageUrlSelectors = $this->getSetting('_post_next_page_url_selectors');
        $postAllPageUrlsSelectors = $this->getSetting('_post_next_page_all_pages_url_selectors');

        // Add whether or not to paginate the post when saving to the db
        $this->postData->setPaginate($postIsPaginate ? true : false);

        // Before clearing the content, check if the post should be paginated and take related actions.
        // Do this before clearing the content, because pagination might be inside the content and the user might mark
        // it as unnecessary element.
        if($postIsPaginate) {
            // Get next page URL of the post
            foreach($postNextPageUrlSelectors as $nextPageSelector) {
                $attr = isset($nextPageSelector["attr"]) && $nextPageSelector["attr"] ? $nextPageSelector["attr"] : "href";
                if ($nextPageUrl = $this->extractData($this->crawler, $nextPageSelector["selector"], $attr, false, true, true)) {

                    $this->postData->setNextPageUrl(Utils::prepareUrl($this->getSiteUrl(), $nextPageUrl, $this->postUrl));
                    break;
                }
            }

            // Get all page URLs of the post
            foreach($postAllPageUrlsSelectors as $selector) {
                $attr = isset($selector["attr"]) && $selector["attr"] ? $selector["attr"] : "href";
                if ($allPageUrls = $this->extractData($this->crawler, $selector, $attr, "part_url", false, true)) {
                    $allPageUrls = Utils::array_msort($allPageUrls, ["start" => SORT_ASC]);

                    $this->postData->setAllPageUrls($allPageUrls);
                    break;
                }
            }
        }
    }

    /**
     * Prepare custom short code info.
     */
    private function prepareShortCodeInfo() {
        $postCustomShortCodeSelectors       = $this->getSetting('_post_custom_content_shortcode_selectors');
        $shortCodeSpecificFindAndReplaces   = $this->getSetting('_post_find_replace_custom_short_code');
        $findAndReplacesForCustomShortCodes = $this->prepareFindAndReplaces($this->getSetting('_post_find_replace_custom_shortcodes'));

        if($postCustomShortCodeSelectors && !empty($postCustomShortCodeSelectors)) {
            foreach($postCustomShortCodeSelectors as $selectorData) {
                if(
                    !isset($selectorData["selector"]) || empty($selectorData["selector"]) ||
                    !isset($selectorData["short_code"]) || empty($selectorData["short_code"])
                )
                    continue;

                $attr = !isset($selectorData["attr"]) || empty($selectorData["attr"]) ? 'html' : $selectorData["attr"];
                $isSingle = isset($selectorData["single"]);

                if($results = $this->extractData($this->crawler, $selectorData["selector"], $attr, false, $isSingle, true)) {
                    $result = '';

                    // If the results is an array, combine all the data into a single string.
                    if(is_array($results)) {
                        foreach($results as $key => $r) $result .= $r;
                    } else {
                        $result = $results;
                    }

                    // Find and replace in custom short codes (regex, short code name, find, replace)
                    if($shortCodeSpecificFindAndReplaces) {
                        foreach($shortCodeSpecificFindAndReplaces as $item) {
                            if(Utils::array_get($item, "short_code") == $selectorData["short_code"]) {
                                $result = $this->findAndReplaceSingle(
                                    Utils::array_get($item, "find"),
                                    Utils::array_get($item, "replace"),
                                    $result,
                                    isset($item["regex"])
                                );
                            }
                        }
                    }

                    // Apply find-and-replaces
                    $result = $this->findAndReplace($findAndReplacesForCustomShortCodes, $result);

                    $shortCodeContent[] = [
                        "data"          =>  $result,
                        "short_code"    =>  $selectorData["short_code"]
                    ];
                }
            }

            if(!empty($shortCodeContent)) {
                $this->postData->setShortCodeData($shortCodeContent);
            }
        }
    }

    /**
     * Prepare meta keywords, meta description and tags.
     */
    private function prepareMetaAndTagInfo() {
        $postTagSelectors                   = $this->getSetting('_post_tag_selectors');

        $postSaveMetaKeywords               = $this->getSetting('_post_meta_keywords');
        $postMetaKeywordsAsTags             = $this->getSetting('_post_meta_keywords_as_tags');
        $postSaveMetaDescription            = $this->getSetting('_post_meta_description');

        $findAndReplacesForTags             = $this->prepareFindAndReplaces($this->getSetting('_post_find_replace_tags'));
        $findAndReplacesForMetaKeywords     = $this->prepareFindAndReplaces($this->getSetting('_post_find_replace_meta_keywords'));
        $findAndReplacesForMetaDescription  = $this->prepareFindAndReplaces($this->getSetting('_post_find_replace_meta_description'));

        // Get tags
        $allTags = [];
        if($postTagSelectors && !empty($postTagSelectors)) {
            foreach ($postTagSelectors as $tagSelector) {
                $attr = isset($tagSelector["attr"]) && $tagSelector["attr"] ? $tagSelector["attr"] : "text";
                if ($tags = $this->extractData($this->crawler, $tagSelector["selector"], $attr, false, false, true)) {
                    foreach($tags as $tagData) {
                        // Explode the tags from commas and trim each tag to remove spaces from start and end of each tag
                        $allTags = array_merge(
                            $allTags,
                            array_map(function($t) {
                                return trim($t);
                            }, explode(",", $tagData))
                        );
                    }
                }
            }

            if(!empty($allTags)) {
                $this->postData->setTags(array_unique($allTags));
            }
        }

        // Meta keywords
        if($postSaveMetaKeywords) {
            if($metaKeywords = $this->extractData($this->crawler, "meta[name=keywords]", "content", false, true, true)) {
                $metaKeywords = trim($this->findAndReplace($findAndReplacesForMetaKeywords, $metaKeywords), ",");

                $this->postData->setMetaKeywords($metaKeywords);

                if($postMetaKeywordsAsTags) {
                    $metaKeywordsAsTags = array_unique(explode(',', $metaKeywords));

                    // Add these tags to allTags as well
                    $allTags = array_merge($allTags, $metaKeywordsAsTags);

                    $this->postData->setMetaKeywordsAsTags($metaKeywordsAsTags);
                }
            }
        }

        // Prepare the tags by applying find-and-replaces
        if(!empty($allTags)) {
            foreach ($allTags as $mTag) {
                if ($mTag = $this->findAndReplace($findAndReplacesForTags, $mTag)) {
                    $tagsPrepared[] = $mTag;
                }
            }

            // Add all tags to the main data
            if(!empty($tagsPrepared)) {
                $this->postData->setPreparedTags(array_unique($tagsPrepared));
            }
        }

        // Meta description
        if($postSaveMetaDescription) {
            if($metaDescription = $this->extractData($this->crawler, "meta[name=description]", "content", false, true, true))
                $metaDescription = $this->findAndReplace($findAndReplacesForMetaDescription, $metaDescription);
            $this->postData->setMetaDescription($metaDescription);
        }
    }

    /**
     * Prepare custom post meta.
     */
    private function prepareCustomPostMeta() {
        $postCustomPostMetaSelectors        = $this->getSetting('_post_custom_meta_selectors');
        $customPostMetaData                 = $this->getSetting('_post_custom_meta');
        $postMetaSpecificFindAndReplaces    = $this->getSetting('_post_find_replace_custom_meta');
        $customMeta = [];

        // Get custom meta with selectors
        if(!empty($postCustomPostMetaSelectors)) {
            foreach ($postCustomPostMetaSelectors as $selectorData) {
                if (!isset($selectorData["meta_key"]) || empty($selectorData["meta_key"]))
                    continue;

                $attr       = !isset($selectorData["attr"]) || empty($selectorData["attr"]) ? 'text' : $selectorData["attr"];
                $isMultiple = isset($selectorData["multiple"]);

                if ($results = $this->extractData($this->crawler, $selectorData["selector"], $attr, false, !$isMultiple, true)) {
                    // If the results variable is not empty, add it among the custom meta.
                    if($results) {
                        $customMeta[] = [
                            "data"      =>  $results,
                            "meta_key"  =>  $selectorData["meta_key"],
                            "multiple"  =>  $isMultiple ? 1 : 0,
                        ];
                    }
                }
            }
        }

        // Get manually added custom post meta
        if(!empty($customPostMetaData)) {
            foreach($customPostMetaData as $metaData) {
                if(!isset($metaData["key"]) || !$metaData["key"] || !isset($metaData["value"])) continue;
                $isMultiple = isset($metaData["multiple"]);

                $customMeta[] = [
                    "data"      =>  $metaData["value"],
                    "meta_key"  =>  $metaData["key"],
                    "multiple"  =>  $isMultiple ? 1 : 0,
                ];
            }
        }

        // Find replace in specific custom meta
        if($customMeta && $postMetaSpecificFindAndReplaces) {

            // Loop over each custom meta created previously
            foreach($customMeta as $i => &$customMetaItem) {
                // Get current meta item's meta key and data
                $currentMetaKey = Utils::array_get($customMetaItem, "meta_key", null);
                $results        = Utils::array_get($customMetaItem, "data");

                // Continue with the next one if meta key or data does not exist in the current custom meta item.
                if(!$currentMetaKey || !$results) continue;

                // Create an array of results, even if the original results variable is not an array. This is
                // to make this less complex.
                $mResults = is_array($results) ? $results : [$results];

                // We'll store the new results in this array.
                $newResults = [];

                // Find "find and replace" options for this particular meta key and apply changes.
                foreach($postMetaSpecificFindAndReplaces as $item) {
                    if($item["meta_key"] == $currentMetaKey) {
                        // This is the settings for the target meta key.
                        $find       = Utils::array_get($item, "find");
                        $replace    = Utils::array_get($item, "replace");
                        $regex      = isset($item["regex"]);

                        // Apply replacements to each result and store the new results
                        foreach($mResults as $r) {
                            $newResults[] = $this->findAndReplaceSingle($find, $replace, $r, $regex);
                        }
                    }

                }

                // Now, reassign the results. If the results variable was an array, make it an array. Otherwise,
                // get the first item.
                $results = $newResults ? (is_array($results) ? $newResults : $newResults[0]) : null;

                // If there are results, reassign it to the current custom meta item.
                if($results) {
                    $customMetaItem["data"] = $results;

                // Otherwise, remove the current meta item.
                } else {
                    unset($customMetaItem[$i]);
                }
            }
        }

        if(!empty($customMeta)) {
            $this->postData->setCustomMeta($customMeta);
        }
    }

    /**
     * Prepare list items by getting list contents, titles and item numbers.
     */
    private function prepareListInfo() {
        $postIsListType                     = $this->getSetting('_post_is_list_type');
        $postListItemsStartAfterSelectors   = $this->getSetting('_post_list_item_starts_after_selectors');
        $postListNumberSelectors            = $this->getSetting('_post_list_item_number_selectors');
        $postListTitleSelectors             = $this->getSetting('_post_list_title_selectors');
        $postListContentSelectors           = $this->getSetting('_post_list_content_selectors');
        $findAndReplaces                    = $this->prepareFindAndReplaces([]);

        if($postIsListType) {
            $listNumbers = $listTitles = $listContents = [];
            $listStartPos = 0;
            // Get the position after which the list items start
            if(!empty($postListItemsStartAfterSelectors)) {
                foreach($postListItemsStartAfterSelectors as $selector) {
                    /** @var Crawler $node */
                    $node = $this->crawler->filter($selector)->first();

                    try {
                        $nodeHtml = Utils::getNodeHTML($node);
                        $pos = $nodeHtml ? mb_strpos($this->crawler->html(), $nodeHtml) : 0;
                        if ($pos > $listStartPos) $listStartPos = $pos;
                    } catch(\InvalidArgumentException $e) {}
                }

                $this->postData->setListStartPos($listStartPos);
            }

            // Get item numbers
            foreach($postListNumberSelectors as $selector) {
                if ($listNumbers = $this->extractData($this->crawler, $selector, "text", "list_number", false, true)) {
                    if ($listStartPos) $this->removeItemsBeforePos($listNumbers, $listStartPos);

                    $this->postData->setListNumbers($listNumbers);
                    break;
                }
            }

            // Get titles
            foreach($postListTitleSelectors as $selector) {
                if ($listTitles = $this->extractData($this->crawler, $selector, "text", "list_title", false, true)) {
                    if ($listStartPos) $this->removeItemsBeforePos($listTitles, $listStartPos);

                    $this->postData->setListTitles($listTitles);
                    break;
                }
            }

            // Get contents
            $allListContents = [];
            foreach($postListContentSelectors as $selector) {
                if ($listContents = $this->extractData($this->crawler, $selector, "html", "list_content", false, true)) {
                    if ($listStartPos) $this->removeItemsBeforePos($listContents, $listStartPos);

                    // Apply find-and-replaces
                    $listContents = $this->modifyArrayValue($listContents, 'data', function ($val) use (&$findAndReplaces) {
                        return $this->findAndReplace($findAndReplaces, $val);
                    });

                    $allListContents = array_merge($allListContents, $listContents);
                }
            }
            $listContents = $allListContents;
            $this->postData->setListContents($listContents);

            // Remove the list content from main content
            if($listStartPos > 0 && $contents = $this->postData->getContents()) {
                // Find start and end pos of the list
                $this->combinedListData = Utils::combineArrays($this->combinedListData, $listNumbers, $listTitles, $listContents);

                $startPos = $endPos = 0;
                foreach($this->combinedListData as $listData) {
                    if(!$startPos || (isset($listData["start"]) && $listData["start"] < $startPos)) {
                        $startPos = $listData["start"];
                    }

                    if(!$endPos || (isset($listData["end"]) && $listData["end"] > $endPos)) {
                        $endPos = $listData["end"];
                    }
                }

                // If start and end positions are valid, remove the content between these positions
                if($startPos && $endPos) {
                    foreach($contents as $key => $mContent) {
                        if(isset($mContent["start"]) && $mContent["start"] > $startPos &&
                            isset($mContent["end"]) && $mContent["end"] < $endPos) {
                            unset($contents[$key]);
                        }
                    }
                }

                $this->postData->setContents($contents);
            }
        }
    }

    /**
     * Prepare and save thumbnail and other media files
     */
    private function prepareAndSaveMedia() {
        $postSaveAllImagesInContent         = $this->getSetting('_post_save_all_images_in_content');
        $postSaveImagesAsMedia              = $this->getSetting('_post_save_images_as_media');
        $postSaveImagesAsGallery            = $this->getSetting('_post_save_images_as_gallery');
        $postGalleryImageSelectors          = $this->getSetting('_post_gallery_image_selectors');
        $postImageSelectors                 = $this->getSetting('_post_image_selectors');
        $findAndReplacesForImageUrls        = $this->getSetting('_post_find_replace_image_urls');
        $postSaveThumbnailIfNotExist        = $this->getSetting('_post_save_thumbnails_if_not_exist');
        $findAndReplacesForThumbnailUrl     = $this->getSetting('_post_find_replace_thumbnail_url');
        $postThumbnailSelectors             = $this->getSetting('_post_thumbnail_selectors');

        // If the user wants to save all images inside the post content, manually add "img" selector to the post image
        // selectors.
        if($postSaveAllImagesInContent) {
            $postSaveImagesAsMedia = true;
            if(!$postImageSelectors) $postImageSelectors = [];

            $postImageSelectors[] = "img";

            // Make sure there is only one "img" in the image selectors.
            $postImageSelectors = array_unique(array_map(function($s) {
                return trim($s);
            }, $postImageSelectors));
        }

        $this->findAndReplacesForMedia = [];
        if($postSaveImagesAsMedia || $postSaveThumbnailIfNotExist) {

            // Find and save the source URLs
            $sourceData = [];

            /* THUMBNAIL URL */
            // Save the thumbnail URL first, because the thumbnail may be removed by gallery image selectors later.
            if($postSaveThumbnailIfNotExist && $thumbnailUrl = $this->extractData($this->crawler, $postThumbnailSelectors, "src", false, true, true)) {
                $thumbnailUrl = $this->findAndReplace($findAndReplacesForThumbnailUrl, $thumbnailUrl);

                $this->postData->setThumbnailUrl(Utils::prepareUrl($this->getSiteUrl(), $thumbnailUrl, $this->postUrl));
            }

            /* IMAGE GALLERY */
            if($postSaveImagesAsGallery) {
                foreach($postGalleryImageSelectors as $selectorData) {
                    if(!isset($selectorData["selector"])) continue;

                    $attr = isset($selectorData["attr"]) && $selectorData["attr"] ? $selectorData["attr"] : "src";

                    if($imageData = $this->extractData($this->crawler, $selectorData["selector"], $attr, false, false, true)) {
                        // Remove these elements from the source code of the page
                        $this->removeElementsFromCrawler($this->crawler, $selectorData["selector"]);

                        // Make replacements
                        foreach($imageData as $key => &$nImageData) {
                            $src = $nImageData;
                            $original = $src;

                            // Make the replacements for the image URL
                            if($src) $src = $this->findAndReplace($findAndReplacesForImageUrls, $src);

                            $nImageData = [];
                            $nImageData["src"] = $src;
                            $nImageData["original"] = $original;
                            $nImageData["do_not_replace"] = true;
                            $nImageData["gallery_image"] = true;
                        }

                        $sourceData = array_merge($sourceData, $imageData);
                    }
                }
            }

            $allContent = [];

            // Get all of the contents
            if($this->postData->getContents())      $allContent = array_merge($allContent, $this->postData->getContents());
            if($this->postData->getListContents())  $allContent = array_merge($allContent, $this->postData->getListContents());
            if($this->postData->getShortCodeData()) $allContent = array_merge($allContent, $this->postData->getShortCodeData());

            if(!empty($allContent)) {
                $combinedContent = "";
                foreach($allContent as $content) {
                    $combinedContent .= $content["data"];
                }

                // Create a crawler for the combined content and search for URLs
                $sourceCrawler = new Crawler($combinedContent);

                /* SOURCE URLS */
                if($postSaveImagesAsMedia && $imageData = $this->extractData($sourceCrawler, $postImageSelectors, ["src", "alt", "title"], false, false, true)) {
                    // Make replacements
                    foreach($imageData as $key => &$mImageData) {
                        $src = is_array($mImageData) ? $mImageData["src"] : $mImageData;
                        $original = $src;
                        if($src) $src = $this->findAndReplace($findAndReplacesForImageUrls, $src);

                        $mImageData = [];
                        $mImageData["src"] = $src;
                        $mImageData["original"] = $original;
                    }

                    $sourceData = array_merge($sourceData, $imageData);
                }

            }

            if(!empty($sourceData)) {
                $this->postData->setSourceUrls($sourceData);

                // Save the files and replace their URLs with local URLs
                $regexFindBase      = 'src=("|\')?(%s)("|\'|>|\s)';    // Matches src attribute with given variable
                $regexReplaceBase   = 'src=$1%s$3';
                $fileData = [];
                $holder = [];
                foreach($sourceData as $key => $source) {
                    if(is_array($source) && !isset($source["src"])) continue;

                    $src = is_array($source) ? $source["src"] : $source;

                    // If this is a duplicate, continue.
                    if(in_array($src, $holder)) continue;
                    $holder[] = $src;

                    if(!is_array($source)) $source = ["src" => $src];
                    $originalSrc = is_array($source) && isset($source["original"]) ? $source["original"] : $src;

                    $preparedMediaUrl = Utils::prepareUrl($this->getSiteUrl(), $src, $this->postUrl);
                    $file = Utils::saveMedia($preparedMediaUrl);
                    if($file) {

                        /**
                         * Modify the saved media file. You can move the file, rename it, modify it by, say, adding a text
                         * on it, etc.
                         *
                         * @param array $file               An array containing data about the file, such as its path and URL.
                         * @param string $preparedMediaUrl  The URL from which the file was retrieved
                         * @param string $postUrl           URL of the post page that stores the target media file
                         * @param int $siteId               Site ID
                         * @param PostBot $postBot          PostBot itself
                         *
                         * @since 1.6.3
                         * @return array
                         */
                        $file = apply_filters('wpcc/post/media/file', $file, $preparedMediaUrl, $this->postUrl, $this->getSiteId(), $this);

                        // Only add a find-replace for the source URL when required
                        if(!isset($source["do_not_replace"]) || !$source["do_not_replace"]) {
                            $this->findAndReplacesForMedia[] = [
                                'regex'     => true,
                                'find'      => sprintf($regexFindBase, preg_quote($originalSrc, '/')),
                                'replace'   => sprintf($regexReplaceBase, $file['url']),
                            ];
                        }

                        $source["path"] = $file['file'];
                        $source["url"] = $file["url"];
                        $fileData[] = $source;
                    }
                }

                if(!empty($fileData)) {
                    $this->postData->setAttachmentData($fileData);
                }
            }

        }
    }

    /*
     * TEMPLATES
     */

    /**
     * Prepare the title template
     */
    private function prepareTitleTemplate() {
        $templatePostTitle = $this->getSetting('_post_template_title');

        // If there is no template, stop.
        if(!$templatePostTitle) return;

        $this->replaceShortCode($templatePostTitle, "wcc-main-title", $this->postData->getTitle() ? $this->postData->getTitle() : '');
        $this->replaceCustomShortCodes($templatePostTitle);

        // Clear remaining predefined short codes
        $this->clearRemainingPredefinedShortCodes($templatePostTitle);

        $this->postData->setTitle($templatePostTitle);
    }

    /**
     * Prepare the excerpt template
     */
    private function prepareExcerptTemplate() {
        $templatePostExcerpt = $this->getSetting('_post_template_excerpt');

        // If there is no template, stop.
        if(!$templatePostExcerpt) return;

        $excerpt = $this->postData->getExcerpt();
        if($excerpt && isset($excerpt["data"]) && $data = $excerpt["data"]) {
            $this->replaceShortCode($templatePostExcerpt, "wcc-main-excerpt", $data);
        }

        $this->replaceShortCode($templatePostExcerpt, "wcc-main-title", $this->postData->getTitle() ? $this->postData->getTitle() : '');
        $this->replaceCustomShortCodes($templatePostExcerpt);

        // Clear remaining predefined short codes
        $this->clearRemainingPredefinedShortCodes($templatePostExcerpt);

        $excerpt["data"] = $templatePostExcerpt;
        $this->postData->setExcerpt($excerpt);

    }

    /**
     * Prepare list item templates. This method creates final list item HTMLs by replacing short codes in the list item
     * template. At the end, each item in {@link PostBot::$listItems} will have a 'template' key storing HTML code for
     * that item.
     */
    private function prepareListItemTemplates() {
        $postIsListType             = $this->getSetting('_post_is_list_type');
        $templateListItem           = $this->getSetting('_post_template_list_item');
        $postListNumberAutoInsert   = $this->getSetting('_post_list_item_auto_number');

        $this->listItems = [];
        if($postIsListType) {
            // Combine each element and sort them according to their position in DOM ascending
            if(empty($this->combinedListData))
                $this->combinedListData = Utils::combineArrays(
                    $this->combinedListData,
                    $this->postData->getListNumbers(),
                    $this->postData->getListTitles(),
                    $this->postData->getListContents()
                );

            if(!empty($this->combinedListData)) {
                // Sort the list data according to the elements' start position
                $this->combinedListData = Utils::array_msort($this->combinedListData, ['start' => SORT_ASC]);

                // Now, match.
                $this->listItems[] = []; // Add an empty array to initialize
                foreach($this->combinedListData as $listData) {
                    $dataType = $listData["type"];
                    $val = $listData["data"];

                    //  If the last item of listItems has "list_content", and this data is also a "list_content", then
                    // append it to the last item's list_content.
                    //  If the last item of listItems has "list_number", then add a new array to the listItems with the
                    // value of "list_number". If the last item does not have "list_number", then add a "list_number" to
                    // the last item of listItems. Do this for each key other than "list_content".
                    //  By this way, we are able to combine relevant data for each list item into one array.

                    if(isset($this->listItems[sizeof($this->listItems) - 1][$dataType])) {
                        if($dataType != "list_content") {
                            $this->listItems[] = [
                                $dataType => $val
                            ];
                        } else {
                            $this->listItems[sizeof($this->listItems) - 1][$dataType] .= $val;
                        }

                    } else {
                        $this->listItems[sizeof($this->listItems) - 1][$dataType] = $val;
                    }
                }

                // Insert list items into template
                if($templateListItem) {
                    $template = null;
                    foreach ($this->listItems as $key => &$item) {
                        $template = $templateListItem;
                        $this->replaceShortCode($template, 'wcc-list-item-title', isset($item['list_title']) ? $item['list_title'] : '');
                        $this->replaceShortCode($template, 'wcc-list-item-content', isset($item['list_content']) ? $item['list_content'] : '');
                        $this->replaceShortCode($template, 'wcc-list-item-position',
                            isset($item['list_number']) ? $item['list_number'] : ($postListNumberAutoInsert ? $key + 1 : '')
                        );
                        $item["template"] = $template;
                    }
                }

            }
        }
    }

    /*
     * Prepares final post content template by rendering short codes and doing other necessary changes, such as applying
     * find and replace options and replacing short codes.
     */
    private function prepareTemplates() {
        $this->prepareTitleTemplate();
        $this->prepareExcerptTemplate();
        $this->prepareListItemTemplates();

        $templateMain                           = $this->getSetting('_post_template_main', '[wcc-main-content]');
        $postListInsertReversed                 = $this->getSetting('_post_list_insert_reversed');
        $templateGalleryItem                    = $this->getSetting('_post_template_gallery_item');
        $findAndReplacesForTemplate             = $this->getSetting('_post_find_replace_template');
        $templateUnnecessaryElementSelectors    = $this->getSetting('_template_unnecessary_element_selectors');
        $findAndReplacesForCombinedContent      = $this->prepareFindAndReplaces([]);

        if($templateMain) {
            // Combine list item templates
            $combinedListItemTemplate = '';
            if(!empty($this->listItems)) {
                // Reverse the array, if it is desired
                if($postListInsertReversed) $this->listItems = array_reverse($this->listItems);

                foreach($this->listItems as $key => $mItem) {
                    if(isset($mItem["template"])) $combinedListItemTemplate .= $mItem["template"];
                }
            }

            $template = $templateMain;

            $this->replaceShortCode($template, 'wcc-main-title', $this->postData->getTitle() ? $this->postData->getTitle() : '');

            $excerpt = $this->postData->getExcerpt();
            $excerpt["data"] = $this->findAndReplace($this->findAndReplacesForMedia, $excerpt["data"]);
            $this->postData->setExcerpt($excerpt);

            $this->replaceShortCode($template, 'wcc-main-excerpt', $excerpt && isset($excerpt["data"]) ? $excerpt["data"] : '');

            // Combine the contents and replace its short code
            $combinedContent = '';
            if($this->postData->getContents()) {
                foreach ($this->postData->getContents() as $content) {
                    if (isset($content["data"])) $combinedContent .= "<p>" . $content["data"] . "</p>";
                }

                $combinedContent = $this->findAndReplace($findAndReplacesForCombinedContent, $combinedContent);
            }
            $this->replaceShortCode($template, 'wcc-main-content', $combinedContent ? $combinedContent : '');

            // Combine list contents and replace its short code
            $this->replaceShortCode($template, 'wcc-main-list', $combinedListItemTemplate);

            // Source URL short code
            $this->replaceShortCode($template, 'wcc-source-url', $this->postUrl);

            // Prepare the gallery and replace gallery short code
            $galleryTemplate = '';
            if($this->postData->getAttachmentData()) {
                // Prepare each item and append it to the main gallery template
                if($templateGalleryItem) {
                    foreach ($this->postData->getAttachmentData() as $ad) {
                        if (isset($ad["gallery_image"]) && $ad["gallery_image"] && isset($ad["url"]) && !empty($ad["url"])) {
                            $currentItemTemplate = $templateGalleryItem;
                            $this->replaceShortCode($currentItemTemplate, 'wcc-gallery-item-url', $ad["url"]);
                            $galleryTemplate .= $currentItemTemplate;
                        }
                    }
                }

            }

            // Now replace the gallery short code in main post template
            $this->replaceShortCode($template, 'wcc-main-gallery', $galleryTemplate);

            // Replace custom short codes
            $this->replaceCustomShortCodes($template);

            // Clear the post content from unnecessary elements
            if(!empty($templateUnnecessaryElementSelectors)) {
                // Create a crawler using the HTML of the template
                $templateCrawler = new Crawler(
                    '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $template . '</body></html>'
                );

                $this->removeElementsFromCrawler($templateCrawler, $templateUnnecessaryElementSelectors);

                // Get the HTML of body tag for the template.
                $template = $templateCrawler->filter("body")->first()->html();
            }

            // Find and replace for media
            $template = $this->findAndReplace($this->findAndReplacesForMedia, $template);

            // Find and replace for template and add final value to the data
            $template = $this->findAndReplace($findAndReplacesForTemplate, $template);

            // Clear remaining predefined short codes
            $this->clearRemainingPredefinedShortCodes($template);

            // Set the template
            $this->postData->setTemplate($template);

        } else {
            $this->postData->setTemplate('');
        }
    }

    /**
     * Translates {@link postData} if post translation is active.
     */
    private function maybeTranslate() {
        // No need to translate if the translation is not activated in general settings page or site settings page.
        if(!Settings::isTranslationActive() || !$this->getSettingForCheckbox('_active_translation')) return;

        /**
         * Fires just before the post data is translated according to the settings.
         *
         * @param int      siteId    ID of the site
         * @param string   $postUrl  URL of the post
         * @param PostBot  $this     The bot itself
         * @param PostData $postData The data retrieved from the target site by using the settings configured by the user.
         * @param Crawler  $crawler  Crawler containing the target post page's source code. The crawler was manipulated
         *                           according to the settings.
         * @since 1.6.3
         */
        do_action('wpcc/post/data/before_translate', $this->getSiteId(), $this->postUrl, $this, $this->postData, $this->crawler);

        $postTranslator = new PostTranslator($this->getSettings(), $this->postData);
        $this->postData = $postTranslator->translate();

        /**
         * Fires just after the post data is translated according to the settings.
         *
         * @param int      siteId    ID of the site
         * @param string   $postUrl  URL of the post
         * @param PostBot  $this     The bot itself
         * @param PostData $postData Translated post data.
         * @param Crawler  $crawler  Crawler containing the target post page's source code. The crawler was manipulated
         *                           according to the settings.
         * @since 1.6.3
         */
        do_action('wpcc/post/data/after_translate', $this->getSiteId(), $this->postUrl, $this, $this->postData, $this->crawler);
    }

    /*
     * OTHER PRIVATE HELPERS
     */

    /**
     * Replace custom short codes inside a template
     *
     * @param string $template The template that contains custom short codes
     */
    private function replaceCustomShortCodes(&$template) {
        $postCustomShortCodeSelectors = $this->getSetting('_post_custom_content_shortcode_selectors');
        if(!$postCustomShortCodeSelectors) return;

        // Replace custom short codes with their values
        if($this->postData->getShortCodeData()) {
            foreach($this->postData->getShortCodeData() as $scData) {
                $this->replaceShortCode($template, $scData["short_code"], $scData["data"]);
            }
        }

        // Replace short codes which have no content
        if($postCustomShortCodeSelectors && !empty($postCustomShortCodeSelectors)) {
            foreach ($postCustomShortCodeSelectors as $selectorData) {
                if(!isset($selectorData["short_code"])) continue;
                $this->replaceShortCode($template, $selectorData["short_code"], "");
            }
        }
    }

    /**
     * Clear a template from remaining short codes. For instance, if the template has predefined short codes in it,
     * which is not replaced, this method will remove those short codes. For predefined short codes, see
     * {@link PostService::getPredefinedShortCodes}.
     *
     * @param string $template The template which should be cleared from remaining short codes
     */
    private function clearRemainingPredefinedShortCodes(&$template) {
        if(!$this->predefinedPreparedShortCodes) {
            // Prepare the short codes. replaceShortCode method requires short code names not to have square brackets.
            // So, remove square brackets.
            $this->predefinedPreparedShortCodes = array_map(function($sc) {
                return str_replace(["[", "]"], "", $sc);
            }, Factory::postService()->getPredefinedShortCodes());
        }

        // Remove each short code from the template.
        foreach($this->predefinedPreparedShortCodes as $shortCode) {
            $this->replaceShortCode($template, $shortCode, "");
        }

    }

    /**
     * Prepare find-and-replaces by adding link removal config the supplied find-and-replace array.
     *
     * @param array $findAndReplaces An array of find and replace options. See
     *                               {@link FindAndReplaceTrait::findAndReplace} to learn more about this array.
     * @return array
     */
    private function prepareFindAndReplaces($findAndReplaces) {
        if($this->removeLinksFromShortCodes && is_array($findAndReplaces)) {
            if(!$this->removeLinksFindAndReplaceConfig) {
                $this->removeLinksFindAndReplaceConfig = $this->createFindReplaceConfig(
                    trim(static::$REMOVE_LINKS_FIND, '/'),
                    static::$REMOVE_LINKS_REPLACE,
                    true
                );
            }

            $findAndReplaces[] = $this->removeLinksFindAndReplaceConfig;
        }

        return $findAndReplaces;
    }

    /**
     * Modify a value in each inner array of an array.
     *
     * @param array    $array    An array of arrays. E.g. <i>[ ['data' => 'a'], ['data' => 'b'] ]</i>
     * @param string   $key      Inner array key whose value should be modified. E.g. <i>'data'</i>
     * @param callable $callback Called for each inner array. func($val) {return $modifiedVal; }. $val is, e.g., the
     *                           value of 'data'. This should return the modified value.
     * @return array             Modified array
     */
    private function modifyArrayValue($array, $key, $callback) {
        if(!is_callable($callback)) return $array;

        $preparedArray = [];
        foreach($array as $data) {
            if(isset($data[$key])) {
                $data[$key] = $callback($data[$key]);
            }

            $preparedArray[] = $data;
        }

        return $preparedArray;
    }

    /*
     * PUBLIC GETTERS
     */

    /**
     * @return Crawler
     */
    public function getCrawler() {
        return $this->crawler;
    }
}