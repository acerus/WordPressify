<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 30/03/16
 * Time: 12:24
 */

namespace WPCCrawler;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use WPCCrawler\objects\Settings;

/**
 * TODO: Move this to AbstractBot.
 *
 * @package WPCCrawler
 */
class Bot {

    /**
     * @var Client
     */
    private $client;

    /** @var array Settings for the site to be crawled */
    private $settings;

    private $errors = [];

    /**
     * Bot constructor.
     * @param array $settings Settings for the site to be crawled
     */
    public function __construct($settings) {
        $this->settings = $settings;
        $this->createClient();
    }

    /**
     * Creates a client to be used to perform browser actions
     */
    private function createClient() {
        // Get the default settings
        $defaultGeneralSettings = Factory::generalSettingsController()->getDefaultGeneralSettings();

        // If this bot is for a site which has its own custom settings, then use them.
        if(Utils::getPostMetaValue($this->settings, '_do_not_use_general_settings')) {
            $allowCookies = isset($this->settings['_wpcc_http_allow_cookies']) && $this->settings['_wpcc_http_allow_cookies'];

            // Set ACCEPT and USER_AGENT. If these settings do not exist, use default values.
            $httpAccept = isset($this->settings["_wpcc_http_accept"]) && $this->settings["_wpcc_http_accept"] ?
                $this->settings["_wpcc_http_accept"] :
                $defaultGeneralSettings["_wpcc_http_accept"];

            $httpUserAgent = isset($this->settings["_wpcc_http_user_agent"]) && $this->settings["_wpcc_http_user_agent"] ?
                $this->settings["_wpcc_http_user_agent"] :
                $defaultGeneralSettings["_wpcc_http_user_agent"];

//            var_dump($allowCookies);
//            var_dump($httpAccept);
//            var_dump($httpUserAgent);

        // Otherwise use general settings.
        } else {
            // Get settings
            $allGeneralSettings = Settings::getAllGeneralSettings();

            // Set client settings by using user's preferences.
            $allowCookies = isset($allGeneralSettings["_wpcc_http_allow_cookies"]) && $allGeneralSettings["_wpcc_http_allow_cookies"];

            // Set ACCEPT and USER_AGENT. If these settings do not exist, use default values.
            $httpAccept = isset($allGeneralSettings["_wpcc_http_accept"]) && $allGeneralSettings["_wpcc_http_accept"] ?
                $allGeneralSettings["_wpcc_http_accept"] :
                $defaultGeneralSettings["_wpcc_http_accept"];

            $httpUserAgent = isset($allGeneralSettings["_wpcc_http_user_agent"]) && $allGeneralSettings["_wpcc_http_user_agent"] ?
                $allGeneralSettings["_wpcc_http_user_agent"] :
                $defaultGeneralSettings["_wpcc_http_user_agent"];
        }

        $this->client = new Client();
        $this->client->setClient(new \GuzzleHttp\Client([
            'cookies'   =>  $allowCookies,
        ]));

        $this->client->setServerParameter("HTTP_ACCEPT", $httpAccept);
        $this->client->setServerParameter('HTTP_USER_AGENT', $httpUserAgent);
    }

    /**
     * Creates a new Client and prepares it by adding Accept and User-Agent headers and enabling cookies.
     * Some other routines can also be done here.
     *
     * @return Client
     */
    public function getClient() {
        return $this->client;
    }
    
    public function getSiteUrl() {
        return Utils::getPostMetaValue($this->settings, '_main_page_url');
    }

    /**
     * Collects URLs for a site from the given URL
     * TODO: Move this to UrlBot
     *
     * @param string $url A full URL to be used to get post URLs
     * @return array An array with keys "post_urls" and "next_page_url"
     */
    public function collectUrls($url) {
        $this->errors = [];
        $data = [];

        $findAndReplacesForFirstLoad            = Utils::getPostMetaValue($this->settings, '_category_find_replace_first_load');
        $categoryUnnecessaryElementSelectors    = Utils::getPostMetaValue($this->settings, '_category_unnecessary_element_selectors');
        $categoryLinkSelectors                  = Utils::getPostMetaValue($this->settings, '_category_post_link_selectors');
        $categorySaveThumbnails                 = Utils::getPostMetaValue($this->settings, '_category_post_save_thumbnails');
        $categoryNextPageSelectors              = Utils::getPostMetaValue($this->settings, '_category_next_page_selectors');

        try {
            /** @var Crawler $crawler */
            $crawler = $this->client->request("GET", $url);
        } catch(\GuzzleHttp\Exception\ConnectException $e) {
            // If the URL cannot be fetched, then just return null.
            return null;

        } catch(\Exception $e) {
            error_log("Post Tools - Collect URLs (Exception for {$url}): " . $e->getMessage());
            return null;
        }

        // Replace relative URLs with direct URLs
        $crawler = $this->makeInitialReplacements($crawler, $findAndReplacesForFirstLoad);

        // Clear the crawler from unnecessary category elements
        $this->removeElementsFromCrawler($crawler, $categoryUnnecessaryElementSelectors);

        // Get post urls
        if($postUrlData = $this->extractData($crawler, $categoryLinkSelectors, "href", "url", false, true)) {
            // Make relative URLs direct
            foreach($postUrlData as &$mPostUrl) {
                $mPostUrl["data"] = Utils::prepareUrl($this->getSiteUrl(), $mPostUrl["data"]);
            }
            $data['post_urls'] = $postUrlData;
        }

        // Get thumbnail URLs
        if($categorySaveThumbnails) {
            $categoryPostThumbnailSelectors = Utils::getPostMetaValue($this->settings, '_category_post_thumbnail_selectors');
            $findAndReplacesForThumbnailUrl = Utils::getPostMetaValue($this->settings, '_category_find_replace_thumbnail_url');

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
                        $nThumbnailData["data"] = Utils::prepareUrl($this->getSiteUrl(), $nThumbnailData["data"]);
                    }

                    $data["thumbnails"] = $thumbnailData;
                    break;
                }
            }

            // Match thumbnails with post URLs
            if($thumbnailData && !empty($thumbnailData)) {
                // Combine URL and thumbnail data and sort the combined array ascending by start position
                $postDataCombined = array_merge($thumbnailData, $postUrlData);

                // Sort the combined data and reset the array keys
                $postDataCombined = array_values(Utils::array_msort($postDataCombined, ["start" => SORT_ASC]));
//                var_dump($postDataCombined);

                $isLinkBeforeThumb = Utils::getPostMetaValue($this->settings, '_category_post_is_link_before_thumbnail');

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
                                if($mUrlData["data"] == $postDataCombined[$i]["data"]) {
                                    $mUrlData["thumbnail"] = $thumbnailHolder;
                                    break;
                                }
                            }
                        }
                    }
                }

                $data["post_urls"] = $postUrlData;
                unset($postDataCombined);
                unset($thumbnailData);
            }
        }

//        var_dump($postUrlData);

        // Get the next page url
        foreach($categoryNextPageSelectors as $nextPageSelector) {
            $attr = isset($nextPageSelector["attr"]) && $nextPageSelector["attr"] ? $nextPageSelector["attr"] : "href";
            if ($nextPageUrl = $this->extractData($crawler, $nextPageSelector["selector"], $attr, false, true, true)) {
                $data['next_page_url'] = Utils::prepareUrl($this->getSiteUrl(), $nextPageUrl);
                break;
            }
        }

        $data["errors"] = $this->errors;

        return $data;
    }

    /**
     * Crawls a post and prepares the data as array, does not save the post to the db.
     * TODO: Move this to PostBot
     *
     * @param string $postUrl A full URL
     * @return array An array with keys: TODO: Add explanation for keys
     */
    public function crawlPost($postUrl) {
        $this->errors = [];
        $data = [];

        $findAndReplacesForFirstLoad        = Utils::getPostMetaValue($this->settings, '_post_find_replace_first_load');
        $postTitleSelectors                 = Utils::getPostMetaValue($this->settings, '_post_title_selectors');
        $postExcerptSelectors               = Utils::getPostMetaValue($this->settings, '_post_excerpt_selectors');
        $postContentSelectors               = Utils::getPostMetaValue($this->settings, '_post_content_selectors');
        $postCustomShortCodeSelectors       = Utils::getPostMetaValue($this->settings, '_post_custom_content_shortcode_selectors');
        $postTagSelectors                   = Utils::getPostMetaValue($this->settings, '_post_tag_selectors');
        $postIsPaginate                     = Utils::getPostMetaValue($this->settings, '_post_paginate');
        $postNextPageUrlSelectors           = Utils::getPostMetaValue($this->settings, '_post_next_page_url_selectors');
        $postAllPageUrlsSelectors           = Utils::getPostMetaValue($this->settings, '_post_next_page_all_pages_url_selectors');
        $postIsListType                     = Utils::getPostMetaValue($this->settings, '_post_is_list_type');
        $postListItemsStartAfterSelectors   = Utils::getPostMetaValue($this->settings, '_post_list_item_starts_after_selectors');
        $postListNumberSelectors            = Utils::getPostMetaValue($this->settings, '_post_list_item_number_selectors');
        $postListTitleSelectors             = Utils::getPostMetaValue($this->settings, '_post_list_title_selectors');
        $postListContentSelectors           = Utils::getPostMetaValue($this->settings, '_post_list_content_selectors');
        $postListNumberAutoInsert           = Utils::getPostMetaValue($this->settings, '_post_list_item_auto_number');
        $postListInsertReversed             = Utils::getPostMetaValue($this->settings, '_post_list_insert_reversed');
        $postSaveMetaKeywords               = Utils::getPostMetaValue($this->settings, '_post_meta_keywords');
        $postMetaKeywordsAsTags             = Utils::getPostMetaValue($this->settings, '_post_meta_keywords_as_tags');
        $postSaveMetaDescription            = Utils::getPostMetaValue($this->settings, '_post_meta_description');
        $postUnnecessaryElementSelectors    = Utils::getPostMetaValue($this->settings, '_post_unnecessary_element_selectors');
        $postSaveImagesAsMedia              = Utils::getPostMetaValue($this->settings, '_post_save_images_as_media');
        $postSaveImagesAsGallery            = Utils::getPostMetaValue($this->settings, '_post_save_images_as_gallery');
        $postGalleryImageSelectors          = Utils::getPostMetaValue($this->settings, '_post_gallery_image_selectors');
        $postImageSelectors                 = Utils::getPostMetaValue($this->settings, '_post_image_selectors');
        $findAndReplacesForImageUrls        = Utils::getPostMetaValue($this->settings, '_post_find_replace_image_urls');
        $postSaveThumbnailIfNotExist        = Utils::getPostMetaValue($this->settings, '_post_save_thumbnails_if_not_exist');
        $findAndReplacesForThumbnailUrl     = Utils::getPostMetaValue($this->settings, '_post_find_replace_thumbnail_url');
        $postThumbnailSelectors             = Utils::getPostMetaValue($this->settings, '_post_thumbnail_selectors');
        $postCustomPostMetaSelectors        = Utils::getPostMetaValue($this->settings, '_post_custom_meta_selectors');

        $templateMain                       = Utils::getPostMetaValue($this->settings, '_post_template_main');
        $templateListItem                   = Utils::getPostMetaValue($this->settings, '_post_template_list_item');
        $templateGalleryItem                = Utils::getPostMetaValue($this->settings, '_post_template_gallery_item');

        $templateUnnecessaryElementSelectors= Utils::getPostMetaValue($this->settings, '_template_unnecessary_element_selectors');

        $findAndReplacesForTemplate         = Utils::getPostMetaValue($this->settings, '_post_find_replace_template');
        $findAndReplacesForTitle            = Utils::getPostMetaValue($this->settings, '_post_find_replace_title');
        $findAndReplacesForExcerpt          = Utils::getPostMetaValue($this->settings, '_post_find_replace_excerpt');
        $findAndReplacesForTags             = Utils::getPostMetaValue($this->settings, '_post_find_replace_tags');
        $findAndReplacesForMetaKeywords     = Utils::getPostMetaValue($this->settings, '_post_find_replace_meta_keywords');
        $findAndReplacesForMetaDescription  = Utils::getPostMetaValue($this->settings, '_post_find_replace_meta_description');

        try {
            /** @var Crawler $crawler Main crawler */
            $crawler = $this->client->request("GET", $postUrl);

            $crawler->html();

        } catch(\GuzzleHttp\Exception\ConnectException $e) {
            // If the URL cannot be fetched, then just return null.
            return null;

        } catch(\GuzzleHttp\Exception\RequestException $e) {
            // If the URL cannot be fetched, then just return null.
            return null;

        } catch(\InvalidArgumentException $e) {
            // If the HTML could not be retrieved, then just return null.
            return null;

        } catch(\Exception $e) {
            error_log("Post Tools - Crawl Post (Exception for {$postUrl}): " . $e->getMessage());
            return null;
        }

        // Replace relative URLs with direct URLs.
        $crawler = $this->makeInitialReplacements($crawler, $findAndReplacesForFirstLoad, true);

        // Add whether or not to paginate the post when saving to the db
        $data["paginate"] = $postIsPaginate ? true : false;

        // Before clearing the content, check if the post should be paginated and take related actions.
        // Do this before clearing the content, because pagination might be inside the content and the user might mark
        // it as unnecessary element.
        if($postIsPaginate) {
            // Get next page URL of the post
            foreach($postNextPageUrlSelectors as $nextPageSelector) {
                $attr = isset($nextPageSelector["attr"]) && $nextPageSelector["attr"] ? $nextPageSelector["attr"] : "href";
                if ($nextPageUrl = $this->extractData($crawler, $nextPageSelector["selector"], $attr, false, true, true)) {
                    $data['next_page_url'] = Utils::prepareUrl($this->getSiteUrl(), $nextPageUrl);
                    break;
                }
            }

            // Get all page URLs of the post
            foreach($postAllPageUrlsSelectors as $selector) {
                $attr = isset($selector["attr"]) && $selector["attr"] ? $selector["attr"] : "href";
                if ($allPageUrls = $this->extractData($crawler, $selector, $attr, "part_url", false, true)) {
                    $allPageUrls = Utils::array_msort($allPageUrls, ["start" => SORT_ASC]);
                    $data["all_page_urls"] = $allPageUrls;
                    break;
                }
            }
        }

        // Clear the crawler from unnecessary post elements
        $this->removeElementsFromCrawler($crawler, $postUnnecessaryElementSelectors);

        // Get title
        if($title = $this->extractData($crawler, $postTitleSelectors, "text", false, true, true)) {
            $title = $this->findAndReplace($findAndReplacesForTitle, $title);
            $data["title"] = $title;
        }

        // Get excerpt
        if($excerpt = $this->extractData($crawler, $postExcerptSelectors, "html", "excerpt", true, true)) {
            $excerpt["data"] = $this->findAndReplace($findAndReplacesForExcerpt, $excerpt["data"]);
            $data["excerpt"] = $excerpt;
        }

        // Get contents
        if($contents = $this->extractData($crawler, $postContentSelectors, "html", "content", false, true)) {
            $contents = Utils::array_msort($contents, ['start' => SORT_ASC]);
            $data["contents"] = $contents;
        }

        // Get custom shortcode contents
        $shortCodeContent = [];
        if($postCustomShortCodeSelectors && !empty($postCustomShortCodeSelectors)) {
            foreach($postCustomShortCodeSelectors as $selectorData) {
                if(
                    !isset($selectorData["selector"]) || empty($selectorData["selector"]) ||
                    !isset($selectorData["short_code"]) || empty($selectorData["short_code"])
                )
                    continue;

                $attr = !isset($selectorData["attr"]) || empty($selectorData["attr"]) ? 'html' : $selectorData["attr"];
                $isSingle = isset($selectorData["single"]);

                if($results = $this->extractData($crawler, $selectorData["selector"], $attr, false, $isSingle, true)) {
                    $result = '';

                    // If the results is an array, combine all the data into a single string.
                    if(is_array($results)) {
                        foreach($results as $key => $r) $result .= $r;
                    } else {
                        $result = $results;
                    }

                    $shortCodeContent[] = [
                        "data"          =>  $result,
                        "short_code"    =>  $selectorData["short_code"]
                    ];
                }
            }

            if(!empty($shortCodeContent)) $data["short_code_data"] = $shortCodeContent;
        }

        // Get tags
        $allTags = [];
        if($postTagSelectors && !empty($postTagSelectors)) {
            foreach ($postTagSelectors as $tagSelector) {
                $attr = isset($tagSelector["attr"]) && $tagSelector["attr"] ? $tagSelector["attr"] : "text";
                if ($tags = $this->extractData($crawler, $tagSelector["selector"], $attr, false, false, true)) {
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

            if(!empty($allTags)) $data["tags"] = array_unique($allTags);
        }

        // Get list items
        $listNumbers = $listTitles = $listContents = [];
        $combinedListData = [];
        if($postIsListType) {
            $listStartPos = 0;
            // Get the position after which the list items start
            if(!empty($postListItemsStartAfterSelectors)) {
                foreach($postListItemsStartAfterSelectors as $selector) {
                    /** @var Crawler $node */
                    $node = $crawler->filter($selector)->first();

                    try {
                        $pos = strpos($crawler->html(), $node->html());
                        if ($pos > $listStartPos) $listStartPos = $pos;
                    } catch(\InvalidArgumentException $e) {}
                }

                $data["list_start_pos"] = $listStartPos;
            }

            // Get item numbers
            foreach($postListNumberSelectors as $selector) {
                if ($listNumbers = $this->extractData($crawler, $selector, "text", "list_number", false, true)) {
                    if ($listStartPos) $this->removeItemsBeforePos($listNumbers, $listStartPos);
                    $data["list_numbers"] = $listNumbers;
                    break;
                }
            }

            // Get titles
            foreach($postListTitleSelectors as $selector) {
                if ($listTitles = $this->extractData($crawler, $selector, "text", "list_title", false, true)) {
                    if ($listStartPos) $this->removeItemsBeforePos($listTitles, $listStartPos);
                    $data["list_titles"] = $listTitles;
                    break;
                }
            }

            // Get contents
            if($listContents = $this->extractData($crawler, $postListContentSelectors, "html", "list_content", false, true)) {
                if($listStartPos) $this->removeItemsBeforePos($listContents, $listStartPos);
                $data["list_contents"] = $listContents;
            }

            // Remove the list content from main content
            if($listStartPos > 0) {
                if($listStartPos > 0 && !empty($contents)) {
                    // Find start and end pos of the list
                    $combinedListData = Utils::combineArrays($combinedListData, $listNumbers, $listTitles, $listContents);

                    $startPos = $endPos = 0;
                    foreach($combinedListData as $listData) {
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

                    $data["contents"] = $contents;
                }
            }
        }

        // Meta keywords
        if($postSaveMetaKeywords) {
            if($metaKeywords = $this->extractData($crawler, "meta[name=keywords]", "content", false, true, true)) {
                $metaKeywords = trim($this->findAndReplace($findAndReplacesForMetaKeywords, $metaKeywords), ",");
                $data["meta_keywords"] = $metaKeywords;
                if($postMetaKeywordsAsTags) {
                    $metaKeywordsAsTags = array_unique(explode(',', $metaKeywords));

                    // Add these tags to allTags as well
                    $allTags = array_merge($allTags, $metaKeywordsAsTags);

                    $data["meta_keywords_as_tags"] = $metaKeywordsAsTags;
                }
            }
        }

        // Prepare the tags by applying find-and-replaces
        $tagsPrepared = [];
        if(!empty($allTags)) {
            foreach ($allTags as $mTag) {
                if ($mTag = $this->findAndReplace($findAndReplacesForTags, $mTag)) {
                    $tagsPrepared[] = $mTag;
                }
            }

            // Add all tags to the main data
            if(!empty($tagsPrepared)) {
                $data["prepared_tags"] = array_unique($tagsPrepared);
            }
        }

        // Meta description
        if($postSaveMetaDescription) {
            if($metaDescription = $this->extractData($crawler, "meta[name=description]", "content", false, true, true))
                $metaDescription = $this->findAndReplace($findAndReplacesForMetaDescription, $metaDescription);
                $data["meta_description"] = $metaDescription;
        }

        // Get source URLs of to-be-saved files and thumbnail image URL
        $sourceData = null;
        $findAndReplacesForMedia = [];
        if($postSaveImagesAsMedia || $postSaveThumbnailIfNotExist) {

            // Find and save the source URLs
            $sourceData = [];

            /* THUMBNAIL URL */
            // Save the thumbnail URL first, because the thumbnail may be removed by gallery image selectors later.
            if($postSaveThumbnailIfNotExist && $thumbnailUrl = $this->extractData($crawler, $postThumbnailSelectors, "src", false, true, true)) {
                $thumbnailUrl = $this->findAndReplace($findAndReplacesForThumbnailUrl, $thumbnailUrl);
                $data["thumbnail_url"] = Utils::prepareUrl($this->getSiteUrl(), $thumbnailUrl);
            }

            /* IMAGE GALLERY */
            if($postSaveImagesAsGallery) {
                foreach($postGalleryImageSelectors as $selectorData) {
                    if(!isset($selectorData["selector"])) continue;

                    $attr = isset($selectorData["attr"]) && $selectorData["attr"] ? $selectorData["attr"] : "src";

                    if($imageData = $this->extractData($crawler, $selectorData["selector"], $attr, false, false, true)) {
                        // Remove these elements from the source code of the page
                        $this->removeElementsFromCrawler($crawler, $selectorData["selector"]);

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
            if(!empty($contents))       $allContent = array_merge($allContent, $contents);
            if(!empty($listContents))   $allContent = array_merge($allContent, $listContents);

            if(!empty($allContent)) {
                $combinedContent= "";
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
//                    var_dump($sourceData);
                $data["source_urls"] = $sourceData;

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

                    $file = static::saveMedia(Utils::prepareUrl($this->getSiteUrl(), $src));
                    if($file) {

                        // Only add a find-replace for the source URL when required
                        if(!isset($source["do_not_replace"]) || !$source["do_not_replace"]) {
                            $findAndReplacesForMedia[] = [
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
                    // Each
                    $data["attachment_data"] = $fileData;
                }
            }


        }

        // Custom post meta
        if(!empty($postCustomPostMetaSelectors)) {
            $customMeta = [];
            foreach ($postCustomPostMetaSelectors as $selectorData) {
                if (
                    !isset($selectorData["meta_key"]) || empty($selectorData["meta_key"])
                )
                    continue;

                $attr = !isset($selectorData["attr"]) || empty($selectorData["attr"]) ? 'text' : $selectorData["attr"];
                $isMultiple = isset($selectorData["multiple"]) /*&& $selectorData["multiple"] This is not necessary and it may cause misbehaviour*/;

                if ($results = $this->extractData($crawler, $selectorData["selector"], $attr, false, !$isMultiple, true)) {
                    $customMeta[] = [
                        "data"      =>  $results,
                        "meta_key"  =>  $selectorData["meta_key"],
                        "multiple"  =>  $isMultiple ? 1 : 0,
                    ];
                }
            }

            if(!empty($customMeta)) $data["custom_meta"] = $customMeta;
        }

        /*
         * TEMPLATING
         */

        $listItems = [];
        if($postIsListType) {
            // Combine each element and sort them according to their position in DOM ascending
            if(empty($combinedListData))
                $combinedListData = Utils::combineArrays($combinedListData, $listNumbers, $listTitles, $listContents);

            if(!empty($combinedListData)) {
                // Sort the list data according to the elements' start position
                $combinedListData = Utils::array_msort($combinedListData, ['start' => SORT_ASC]);

                // Now, match.
                $listItems[] = []; // Add an empty array to initialize
                foreach($combinedListData as $listData) {
                    $dataType = $listData["type"];
                    $val = $listData["data"];

                    //  If the last item of listItems has "list_content", and this data is also a "list_content", then
                    // append it to the last item's list_content.
                    //  If the last item of listItems has "list_number", then add a new array to the listItems with the
                    // value of "list_number". If the last item does not have "list_number", then add a "list_number" to
                    // the last item of listItems. Do this for each key other than "list_content".
                    //  By this way, we are able to combine relevant data for each list item into one array.

                    if(isset($listItems[sizeof($listItems) - 1][$dataType])) {
                        if($dataType != "list_content") {
                            $listItems[] = [
                                $dataType => $val
                            ];
                        } else {
                            $listItems[sizeof($listItems) - 1][$dataType] .= $val;
                        }

                    } else {
                        $listItems[sizeof($listItems) - 1][$dataType] = $val;
                    }
                }

                // Insert list items into template
                if($templateListItem) {
                    $template = null;
                    foreach ($listItems as $key => &$item) {
                        $template = $templateListItem;
                        $this->replaceShortCode($template, '[wcc-list-item-title]', isset($item['list_title']) ? $item['list_title'] : '');
                        $this->replaceShortCode($template, '[wcc-list-item-content]', isset($item['list_content']) ? $item['list_content'] : '');
                        $this->replaceShortCode($template, '[wcc-list-item-position]',
                            isset($item['list_number']) ? $item['list_number'] : ($postListNumberAutoInsert ? $key + 1 : '')
                        );
                        $item["template"] = $template;
                    }
                }

            }
        }

        // Insert main data into template
        if($templateMain) {
            // Combine list item templates
            $combinedListItemTemplate = '';
            if(!empty($listItems)) {
                // Reverse the array, if it is desired
                if($postListInsertReversed) $listItems = array_reverse($listItems);

                foreach($listItems as $key => $mItem) {
                    if(isset($mItem["template"])) $combinedListItemTemplate .= $mItem["template"];
                }
            }

            $template = $templateMain;

            $this->replaceShortCode($template, '[wcc-main-title]', $title ? $title : '');

            $excerpt["data"] = $this->findAndReplace($findAndReplacesForMedia, $excerpt["data"]);
            $this->replaceShortCode($template, '[wcc-main-excerpt]', $excerpt && isset($excerpt["data"]) ? $excerpt["data"] : '');

            // Combine the contents and replace its short code
            $combinedContent = '';
            if($contents) {
                foreach ($contents as $content) {
                    if (isset($content["data"])) $combinedContent .= "<p>" . $content["data"] . "</p>";
                }
            }
            $this->replaceShortCode($template, '[wcc-main-content]', $combinedContent ? $combinedContent : '');

            // Combine list contents and replace its short code
            $this->replaceShortCode($template, '[wcc-main-list]', $combinedListItemTemplate);

            // Source URL short code
            $this->replaceShortCode($template, '[wcc-source-url]', $postUrl);

            // Prepare the gallery and replace gallery short code
            $galleryTemplate = '';
            if(isset($data["attachment_data"]) && !empty($data["attachment_data"])) {
                // Prepare each item and append it to the main gallery template
                if($templateGalleryItem) {
                    foreach ($data["attachment_data"] as $ad) {
                        if (isset($ad["gallery_image"]) && $ad["gallery_image"] && isset($ad["url"]) && !empty($ad["url"])) {
                            $currentItemTemplate = $templateGalleryItem;
                            $this->replaceShortCode($currentItemTemplate, '[wcc-gallery-item-url]', $ad["url"]);
                            $galleryTemplate .= $currentItemTemplate;
                        }
                    }
                }

            }

            // Now replace the gallery short code in main post template
            $this->replaceShortCode($template, '[wcc-main-gallery]', $galleryTemplate);


            // Replace custom short codes
            if(!empty($shortCodeContent)) {
                foreach($shortCodeContent as $scData) {
                    $this->replaceShortCode($template, "[" . $scData["short_code"] . "]", $scData["data"]);
                }
            }

            // Replace short codes which have no content
            if($postCustomShortCodeSelectors && !empty($postCustomShortCodeSelectors)) {
                foreach ($postCustomShortCodeSelectors as $selectorData) {
                    if(!isset($selectorData["short_code"])) continue;
                    $this->replaceShortCode($template, "[" . $selectorData["short_code"] . "]", "");
                }
            }

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
            $template = $this->findAndReplace($findAndReplacesForMedia, $template);

            // Find and replace for template and add final value to the data
            $template = $this->findAndReplace($findAndReplacesForTemplate, $template);

            // Set the template
            $data["template"] = $template;
        } else {
            $data["template"] = '';
        }

        $data["errors"] = $this->errors;

        return $data;
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * First, makes the replacements provided, then replaces relative URLs in a crawler's HTML with direct URLs.
     *
     * @param Crawler $crawler Crawler for the page for which the replacements will be done
     * @param array $findAndReplaces An array of arrays. Inner array should have:
     *      "regex":    bool    If this key exists, then search will be performed as regular expression. If not, a normal search will be done.
     *      "find":     string  What to find
     *      "replace":  string  Replacement for what is found
     * @param bool $applyGeneralReplacements True if you want to apply the replacements inserted in general settings page
     * @return Crawler A new crawler with replacements done
     */
    private function makeInitialReplacements($crawler, $findAndReplaces = null, $applyGeneralReplacements = false) {
        $html = $crawler->html();

        // First, apply general replacements
        if($applyGeneralReplacements) {
            $findAndReplacesGeneral = Utils::getOptionUnescaped('_wpcc_find_replace');
            $html = $this->findAndReplace($findAndReplacesGeneral, $html);
        }

        // Find and replace what user wants.
        if($findAndReplaces) {
            $html = $this->findAndReplace($findAndReplaces, $html);
        }

        // Below fails
        // Replace relative URLs with direct URLs
//        $html = $this->findAndReplace([
//            [
//                "regex"     =>  true,
//                "find"      =>  '(href|src)=("|\')?(\/.*?)("|\'|>|\s)',
//                "replace"   =>  sprintf('$1=$2%s$3$4', rtrim($this->getSiteUrl(), '/'))
//            ]
//        ], $html);

        return new Crawler($html);
    }

    /**
     * Applies replacement to the given subject.
     * TODO: Move this to FindAndReplace trait
     *
     * @param array $findAndReplaces An array of arrays. Inner array should have:
     *      "regex":    bool    If this key exists, then search will be performed as regular expression. If not, a normal search will be done.
     *      "find":     string  What to find
     *      "replace":  string  Replacement for what is found
     * @param string $subject The subject to which finding and replacing will be applied
     * @return string The subject with all of the replacements are done
     */
    private function findAndReplace($findAndReplaces, $subject) {
        if($findAndReplaces && !empty($findAndReplaces)) {
            foreach ($findAndReplaces as $fr) {
                if(empty($fr["find"])) continue;
                if (isset($fr['regex'])) {
                    $r = preg_replace('/' . $fr['find'] . '/', $fr['replace'], $subject);

                    if($r !== null) {
                        $subject = $r;
                    } else {
                        $error = '';
                        switch(preg_last_error()) {
                            case PREG_INTERNAL_ERROR:
                                $error = "Internal error";
                                break;
                            case PREG_NO_ERROR:
                                $error = "No error";
                                break;
                            case PREG_BACKTRACK_LIMIT_ERROR:
                                $error = "Backtrack limit error";
                                break;
                            case PREG_RECURSION_LIMIT_ERROR:
                                $error = "Recursion limit error";
                                break;
                            case PREG_BAD_UTF8_OFFSET_ERROR:
                                $error = "Bad UTF8 offset error";
                                break;
                            case PREG_BAD_UTF8_ERROR:
                                $error = "Bad UTF8 error";
                                break;
                            case PREG_JIT_STACKLIMIT_ERROR:
                                $error = "JIT stacklimit error";
                                break;
                            default:
                                $error = "Unknown error";
                                break;
                        }

                        $this->errors[] = [
                            "error" =>  $error,
                            "data"  =>  _wpcc("Find") . ": " . $fr["find"] . " | " . _wpcc("Replace") . ": " . $fr["replace"]
                        ];
                    }
                } else {
                    $subject = str_replace($fr['find'], $fr['replace'], $subject);
                }
            }
        }

        return trim($subject);
    }

    /**
     * Replaces a short code in a template with a value
     *
     * @param string $template The template including the short code
     * @param string $shortCode The short code to be replaced
     * @param string $value The string to put in place of the short code
     */
    private function replaceShortCode(&$template, $shortCode, $value) {
        $template = str_replace($shortCode, $value, $template);
    }

    /**
     * Removes the items with a 'start' position less than the given pos value.
     *
     * @param array $itemsArray An array of items. Each item in the array should have 'start' key and its value.
     * @param int $pos The reference DOM position. The elements with a 'start' position less than this will be removed.
     */
    private function removeItemsBeforePos(&$itemsArray, $pos) {
        if(!$pos) return;

        foreach($itemsArray as $key => &$item) {
            if($item["start"] < $pos) {
                unset($itemsArray[$key]);
            }
        }
    }

    /**
     * @param Crawler $crawler The crawler from which the elements will be removed
     * @param array|string $selectors A selector or an array of selectors for the elements to be removed
     */
    private function removeElementsFromCrawler(&$crawler, $selectors = []) {
        if(empty($selectors)) return;

        if(!is_array($selectors)) $selectors = [$selectors];

        foreach ($selectors as $selector) {
            if (!$selector) continue;

            $crawler->filter($selector)->each(function ($node, $i) {
                foreach ($node as $child) {
                    $child->parentNode->removeChild($child);
                }
            });
        }
    }

    /**
     * Extracts specified data from the crawler
     *
     * @param Crawler $crawler
     * @param array|string $selectors A single selector as string or more than one selectors as array
     * @param string|array $dataType "text", "html", "href" or attribute of the element (e.g. "content")
     * @param string|null|false $contentType Type of found content. This will be included as "type" in resultant array.
     * @param bool $singleResult True if you want a single result, false if you want all matches. If true, the first
     *      match will be returned.
     * @param bool $trim True if you want each match trimmed, false otherwise.
     * @return array|null|string If found, the result. Otherwise, null. If there is a valid content type, then the
     *      result will include an array including the position of the found value in the crawler HTML. If the content
     *      type is null or false, then just the found value will be included. <p><p>
     *
     *      If there are more than one dataType:
     *          If more than one match is found, then the "data" value will be an array.
     *          If only one match is found, then the data will be a string.
     */
    private function extractData($crawler, $selectors, $dataType, $contentType, $singleResult, $trim) {
        // Check if the selectors are empty. If so, do not bother.
        if(empty($selectors)) return null;

        // If the selectors is not an array, make it one.
        if(!is_array($selectors)) $selectors = [$selectors];

        // If the data type is not an array, make it one.
        if(!is_array($dataType)) $dataType = [$dataType];

        $results = [];
        foreach($selectors as $selector) {
            if(!$selector) continue;
            if($singleResult && !empty($results)) break;

            $crawler->filter($selector)->each(function($node, $i) use ($crawler, $dataType,
                            $singleResult, $trim, $contentType, &$results) {
                /** @var Crawler $node */

                // If single result is needed and we have found one, then do not continue.
                if($singleResult && !empty($results)) return;

                $value = null;
                foreach ($dataType as $dt) {
                    try {
                        $val = null;
                        switch ($dt) {
                            case "text":
                                $val = $node->text();
                                break;
                            case "html":
                                $val = Utils::getNodeHTML($node);
                                break;
                            default:
                                $val = $node->attr($dt);
                                break;
                        }

                        if($val) {
                            if($trim) $val = trim($val);
                            if($val) {
                                if(!$value) $value = [];
                                $value[$dt] = $val;
                            }
                        }

                    } catch (\InvalidArgumentException $e) { }
                }

                try {
                    if($value && !empty($value)) {
                        if ($contentType) {
                            $html = Utils::getNodeHTML($node);
                            $start = strpos($crawler->html(), $html);
                            $results[] = [
                                "type"  =>  $contentType,
                                "data"  =>  sizeof($value) == 1 ? array_values($value)[0] : $value,
                                "start" =>  $start,
                                "end"   =>  $start + strlen($html)
                            ];
                        } else {
                            $results[] = sizeof($value) == 1 ? array_values($value)[0] : $value;
                        }
                    }

                } catch(\InvalidArgumentException $e) { }
            });
        }

        // Return the results
        if($singleResult && !empty($results)) {
            return $results[0];

        } else if(!empty($results)) {
            return $results;
        }

        return null;
    }

    /**
     * TODO: Move this to somewhere else, probably to Utils.
     *
     * @param string $postType
     * @return bool True if the post type is a product
     */
    public function isProduct($postType) {
        return $postType == "product";
    }
}