<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 22:09
 */

namespace WPCCrawler\objects\savers;


use WP_User_Query;
use WPCCrawler\Factory;
use WPCCrawler\objects\crawling\data\PostData;
use WPCCrawler\objects\crawling\PostBot;
use WPCCrawler\objects\enums\ErrorType;
use WPCCrawler\objects\traits\ErrorTrait;
use WPCCrawler\objects\traits\SettingsTrait;
use WPCCrawler\Utils;

class PostSaver extends AbstractSaver {
    
    use SettingsTrait;
    use ErrorTrait;

    private static $DEBUG = false;

    /** @var string Stores ID of the site for which the last post crawl was performed. */
    public $optionLastCrawledSiteId = '_wpcc_last_crawled_site_id';

    /** @var string Stores ID of the site for which the last post recrawl was performed */
    public $optionLastRecrawledSiteId = '_wpcc_last_recrawled_site_id';

    /** @var string Stores source URLs as an array. Each inserted post will have this meta. */
    private $postMetaSourceUrls = '_wpcc_source_urls';

    /** @var string Stores first page URL of the target post. Each inserted post will have this meta. */
    private $postMetaPostFirstPageUrl = '_wpcc_post_url';

    private $wooCommerceGalleryPostMetaKey = '_product_image_gallery';

    /*
     *
     */

    /** @var PostData */
    private $data;

    /** @var bool Stores whether the current task is a recrawl task or not. */
    private $isRecrawl = false;

    /** @var string Prefix that will be added to the meta keys used in regular crawling task */
    public $cronCrawlPostMetaPrefix = '_cron';

    /** @var string Prefix that will be added to the meta keys used in recrawl task */
    public $cronRecrawlPostMetaPrefix = '_cron_recrawl';

    /*
     * DUPLICATE CHECK TYPES
     */

    const DUPLICATE_CHECK_URL       = 'url';
    const DUPLICATE_CHECK_TITLE     = 'title';
    const DUPLICATE_CHECK_CONTENT   = 'content';

    /**
     * Update (recrawl) a post of a URL tuple.
     *
     * @param object $urlTuple A row in wpcc_urls table
     * @return null
     */
    public function executePostRecrawl($urlTuple) {
        $this->setRequestMade(false);
        $this->clearErrors();

        // Do not proceed if the URL tuple is not found or it does not have a saved post ID.
        if(!$urlTuple || !$urlTuple->saved_post_id) return null;

        $this->isRecrawl = true;

        $siteIdToCheck = $urlTuple->post_id;

        // Get settings for the site ID
        $settings = get_post_meta($siteIdToCheck);

        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $prefix = $this->getCronPostMetaPrefix();
        $lastCrawledUrlId   = $this->getSetting($prefix . '_last_crawled_url_id');
        $nextPageUrl        = $this->getSetting($prefix . '_post_next_page_url');
        $nextPageUrls       = $this->getSetting($prefix . '_post_next_page_urls');
        $draftPostId        = $this->getSetting($prefix . '_post_draft_id');

        // If the post with saved_post_id does not exist, make URL tuple's saved_post_id null, and stop.
        $post = get_post($lastCrawledUrlId && $draftPostId ? $draftPostId : $urlTuple->saved_post_id);
        if(!$post) {
            Factory::databaseService()->updateUrlSavedPostId($lastCrawledUrlId, null);

            // Otherwise, make variables null to continue with the URL tuple.
            $lastCrawledUrlId = null;
            $nextPageUrl = null;
            $nextPageUrls = null;
            $draftPostId = null;
        }

        $this->savePost(
            $siteIdToCheck,
            $settings,
            // If there is a draft post ID, it means that post is not finished to be saved. So, use URL ID of the draft
            // post instead of the ID of the current URL tuple.
            $lastCrawledUrlId && $draftPostId ? $lastCrawledUrlId : $urlTuple->id,
            true,
            $nextPageUrl,
            $nextPageUrls,
            $lastCrawledUrlId && $draftPostId ? $draftPostId : $urlTuple->saved_post_id
        );
    }

    /**
     * Save a post for a site. This method does two things:
     * <li>Save a post's next page if there is a post that has pages and has not yet saved completely.</li>
     * <li>Save an unsaved post.</li>
     *
     * @param int  $siteIdToCheck Site ID for which a post will be saved
     */
    public function executePostSave($siteIdToCheck) {
        $this->setRequestMade(false);
        $this->clearErrors();

        if(!$siteIdToCheck) return;

        $this->isRecrawl = false;

        // Get settings for the site ID
        $settings = get_post_meta($siteIdToCheck);

        $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $prefix             = $this->getCronPostMetaPrefix();
        $lastCrawledUrlId   = $this->getSetting($prefix . '_last_crawled_url_id');
        $nextPageUrl        = $this->getSetting($prefix . '_post_next_page_url');
        $nextPageUrls       = $this->getSetting($prefix . '_post_next_page_urls');
        $draftPostId        = $this->getSetting($prefix . '_post_draft_id');

        $this->savePost($siteIdToCheck, $settings, $lastCrawledUrlId, true, $nextPageUrl, $nextPageUrls, $draftPostId);
    }

    /*
     *
     */

    /**
     * Save a post to the database. This method does 3 things:
     * <ul>
     * <li> If a urlId is supplied, saves its post URL to the database. This is used to save a post manually. Just pick
     * an ID from the database.</li>
     * <li> If there are only siteIdToCheck and its settings, then a URL will be found by using CRON settings and saved
     * to the database.</li>
     * <li> If there are urlId, nextPageUrl(s) and draftPostId, then a next page will be saved for the specified urlId.</li>
     * </ul>
     *
     * @param int         $siteIdToCheck     Site ID which the post belongs to, to get the settings for crawling
     * @param array       $settings          Settings for siteIdToCheck
     * @param null|int    $urlId             ID of a URL tuple from wpcc_urls table
     * @param bool        $updateLastCrawled True if you want to update CRON options about last crawled site, false
     *                                       otherwise
     * @param null|string $nextPageUrl       Next page URL for the post, if exists
     * @param null|array  $nextPageUrls      All next page URLs for the post, if exists
     * @param null|int    $draftPostId       ID of a post which is used to save content for this post, for previous
     *                                       pages
     * @return int|null Post ID, or null if the post is not saved
     */
    public function savePost($siteIdToCheck, $settings, $urlId = null, $updateLastCrawled = false,
                             &$nextPageUrl = null, &$nextPageUrls = null, $draftPostId = null) {
        if(!$this->getSettings()) $this->setSettings($settings, Factory::postService()->getSingleMetaKeys());

        $urlToCrawl         = false;
        $isFirstPage        = true;

        $lastCrawledUrlId = $urlId;

        if(static::$DEBUG) var_dump('Last Crawled Url ID: ' . $lastCrawledUrlId);
        if(static::$DEBUG) var_dump('Next Page URL: ' . $nextPageUrl);
        if(static::$DEBUG) var_dump('Next Page URLs:');
        if(static::$DEBUG) var_dump($nextPageUrls);
        if(static::$DEBUG) var_dump('Draft Post ID: ' . $draftPostId);

        global $wpdb;

        // Decide what we're doing. Crawling a next page for the same post, or a new post?
        if($nextPageUrl && $lastCrawledUrlId) {
            // We're getting a next page for a post.
            $isFirstPage = false;

            $query = "SELECT * FROM " . Factory::databaseService()->getDbTableUrlsName() . " WHERE id = %d";
            $results = $wpdb->get_results($wpdb->prepare($query, $lastCrawledUrlId));

            // If the URL is not found, then reset the cron options for this site and return.
            if (empty($results)) {
                error_log(
                    "WPCC - There are a next page URL and a last crawled URL ID, but the URL does not exist in database."
                    . " URL ID: " . $lastCrawledUrlId
                    . ", Next Page URL: " . $nextPageUrl
                );

                if($updateLastCrawled) {
                    $this->resetLastCrawled($siteIdToCheck);

                } else {
                    error_log("WPCC - CRON settings for last-crawled are not reset. This may cause a loop where no post will be saved.");
                }

                $this->addError(ErrorType::URL_TUPLE_NOT_EXIST);

                return null;
            }

            // Get the URL tuple we will work on
            $urlTuple = $results[0];

            // Set the page url we should crawl
            $urlToCrawl = $nextPageUrl;

        } else {
            // We're getting a specified post or a random-ish one
            $urlTuple = $lastCrawledUrlId ? Factory::databaseService()->getUrlById($lastCrawledUrlId) : null;

            // Prevent below if statement from running when updating a post. Only run it when not updating.

            if(!$urlTuple || (!$this->isRecrawl && $urlTuple->is_saved)) {
                // We're getting a new post. Let's find a URL tuple to save.
                $urlTuple = $this->getUrlTupleToCrawl($siteIdToCheck, $lastCrawledUrlId);

                // If no URL is found, then reset the cron options for this site and return.
                if($urlTuple === null) {
                    error_log("WPCC - No URL is found in the database."
                        . " Site ID to check: " . ($siteIdToCheck ? $siteIdToCheck : 'does not exist')
                        . ", Last Crawled URL ID: " . ($lastCrawledUrlId ? $lastCrawledUrlId : 'does not exist')
                    );

                    if($updateLastCrawled) {
                        $this->resetLastCrawled($siteIdToCheck);

                    } else {
                        error_log("WPCC - CRON settings for last-crawled are not reset. This may cause a loop where no post will be saved.");
                    }

                    $this->addError(ErrorType::URL_TUPLE_NOT_EXIST);

                    return null;
                }
            }

            // Set the page url we should crawl
            $urlToCrawl = $urlTuple->url;

        }

        if(static::$DEBUG) var_dump($urlTuple);

        // Do not proceed if this URL tuple is locked.
        if($urlTuple->is_locked) {
            $this->addError(ErrorType::URL_LOCKED);
            return null;
        }

        // Lock the URL tuple so that it won't be selected as the URL to crawl again during saving process
        Factory::databaseService()->updateUrlSavedStatus($urlTuple->id, $urlTuple->is_saved, $urlTuple->saved_post_id, $urlTuple->update_count, true);

        // Assign last crawled URL ID as current tuple's ID.
        $lastCrawledUrlId = $urlTuple->id;

        $mainSiteUrl    = $this->getSetting('_main_page_url');
        $postUrl        = Utils::prepareUrl($mainSiteUrl, $urlToCrawl);

        // Get the data
        $bot = new PostBot($settings, $siteIdToCheck);
        $this->data = $bot->crawlPost($postUrl);
        $this->setRequestMade(true);

        // If there is an error with the connection, reset last crawled and set this URL as saved. By this way,
        // this URL won't be tried again in the future.
        if($this->data === null) {
            $this->resetLastCrawled($siteIdToCheck);

            $this->addError(ErrorType::URL_COULD_NOT_BE_FETCHED);

            // If the URL tuple does not have a post, delete it.
            if(!$urlTuple->saved_post_id) {
                Factory::databaseService()->deleteUrl($urlTuple->id);

                // Write an error
                error_log("WPCC - The URL cannot be fetched (" . $postUrl . "). There was a connection error. The URL is
                    deleted.");

                return null;
            }

            // Set this URL as saved
            Factory::databaseService()->updateUrlSavedStatus($urlTuple->id, true, $urlTuple->saved_post_id, $urlTuple->update_count, false);

            // If this is a recrawl, mark this URL as recrawled so that it won't be tried again and again.
            if($this->isRecrawl) {
                Factory::databaseService()->updateUrlRecrawledStatus($urlTuple->id, $urlTuple->update_count + 1, false);
            }

            // Write an error
            error_log("WPCC - The URL cannot be fetched (" . $postUrl . "). There was a connection error. The URL is
                marked as saved now. Last crawled settings are reset.");

            return null;
        }

        // Reset next page variables and assign them according to the data.
        $nextPageUrl = '';

        // If the post should be paginated, get the next page's URL (or URLs) and store it as option
        if($this->data->isPaginate()) {
            if($this->data->getNextPageUrl()) {
                // The post has a next page URL on each page.
                $nextPageUrl = $this->data->getNextPageUrl();

            } else if($this->data->getAllPageUrls()) {

                if(static::$DEBUG) var_dump("All page URLs are found.");

                // If there is no next page URLs, then this is the first time we crawl this post.
                // First, save all page URLs.
                if(!$nextPageUrls || empty($nextPageUrls)) {
                    if(static::$DEBUG) var_dump('Next Page URLs is false or empty. Get them from the data.');
                    // The post has all URLs for pages in a page.
                    $nextPageUrls = $this->data->getAllPageUrls();

                    // Check if the urls array contains the current page. If so, remove the current page.
                    foreach ($nextPageUrls as $key => &$mUrl) {
                        if ($mUrl["data"] == $postUrl) {
                            unset($nextPageUrls[$key]);
                            if(static::$DEBUG) var_dump("Unset " . $mUrl);
                        }
                    }

                    // Reset the keys of the array
                    $nextPageUrls = array_values(array_map(function($url) {
                        return $url["data"];
                    }, $nextPageUrls));
                }

                if(static::$DEBUG) var_dump("Next Page URLs: ");
                if(static::$DEBUG) var_dump($nextPageUrls);

                // Get the next page URL.
                if(!empty($nextPageUrls)) {
                    if(static::$DEBUG) var_dump("Next page URLs is not empty. Find next page URL.");
                    if(static::$DEBUG) var_dump("Current URL is: " . $urlToCrawl);

                    // We have next page URLs. Find the next page URL.
                    $currentUrlPos = false;
                    foreach ($nextPageUrls as $key => $url) {
                        if(static::$DEBUG) var_dump("Possible Current URL: " . $url);

                        if ($url == $urlToCrawl) {
                            $currentUrlPos = $key;

                            if(static::$DEBUG) var_dump("Current URL pos is found as " . $currentUrlPos . ", which is " . $url);

                            break;
                        }
                    }

                    // If current URL is found among next page URLs, and it is not the last URL, then we can get the next
                    // URL as next page URL.
                    if ($currentUrlPos !== false && $currentUrlPos < sizeof($nextPageUrls) - 1) {
                        if(static::$DEBUG) var_dump("Current URL position is valid: " . $currentUrlPos . ". Get the next item in the list.");
                        $nextPageUrl = $nextPageUrls[$currentUrlPos + 1];

                        // If current URL is not found among next page URLs, then get the first URL as next page URL.
                    } else if($currentUrlPos === false) {
                        if(static::$DEBUG) var_dump("Current URL Position is false. Get the first URL in the list.");
                        $nextPageUrl = $nextPageUrls[0];
                    }

                    // Otherwise, next page URL will be empty, since it is not assigned.

                    // Also, since there is no next page, reset all next pages.
                    if(!$nextPageUrl) $nextPageUrls = [];
                }

            }
        }

        /*
         * CHECK FOR EXISTENCE OF THE CONTENT
         */

        // Sometimes, next pages may be empty due to a malfunction of the site. Scenario is that the post does not have
        // content on the next page, but there is a link on the page indicating there is a next page. In this case,
        // the crawler cannot find any content in the next page. If this is the case, do not continue to next pages.
        $contentExists = true;

        // Get main post template
        $templateMain = $this->getSetting('_post_template_main');
        $clearedTemplateMain = $templateMain;

        // Remove short codes
        // First get predefined short codes
        $allShortCodes = Factory::postService()->getPredefinedShortCodes();

        // Now get user-defined short codes
        $shortCodeSelectors = $this->getSetting('_post_custom_content_shortcode_selectors');
        if($shortCodeSelectors) {
            foreach ($shortCodeSelectors as $selector) {
                if (isset($selector["short_code"])) {
                    $allShortCodes[] = "[" . $selector["short_code"] . "]";
                }
            }
        }

        // Now remove them from the original raw template
        foreach($allShortCodes as $shortCode) {
            $clearedTemplateMain = str_replace($shortCode, "", $clearedTemplateMain);
        }

        if(static::$DEBUG) var_dump("Cleared Template Main:" . $clearedTemplateMain);
        if(static::$DEBUG) var_dump("Original Template Main: " . $templateMain);
        if(static::$DEBUG) var_dump($allShortCodes);
        if(static::$DEBUG) var_dump(mb_strlen($this->data->getTemplate()) <= mb_strlen($clearedTemplateMain));

        // Now, check if the prepared template's length is greater than that of short-codes-removed template. So, if
        // the prepared template's length is less, it means the page is empty. Hence, we do not have any variables in
        // the page.
        if (!$this->data->getTemplate() || mb_strlen($this->data->getTemplate()) <= mb_strlen($clearedTemplateMain)) {
            $nextPageUrl = null;
            $nextPageUrls = null;
            $contentExists = false;
        }

        /*
         * SAVE THE POST
         */

        // Get general settings
        // If this site has different settings, then use them.
        if($this->getSetting('_do_not_use_general_settings')) {
            $allowComments  = $this->getSetting('_wpcc_allow_comments');
            $postStatus     = $this->getSetting('_wpcc_post_status');
            $postType       = $this->getSetting('_wpcc_post_type');
            $postAuthor     = $this->getSetting('_wpcc_post_author');
            $tagLimit       = $this->getSetting('_wpcc_post_tag_limit');
            $postPassword   = $this->getSetting('_wpcc_post_password');

        // Otherwise, go on with general settings.
        } else {
            $allowComments  = get_option('_wpcc_allow_comments');
            $postStatus     = get_option('_wpcc_post_status');
            $postType       = get_option('_wpcc_post_type');
            $postAuthor     = get_option('_wpcc_post_author');
            $tagLimit       = get_option('_wpcc_post_tag_limit', 0);
            $postPassword   = get_option('_wpcc_post_password');
        }

        // Prepare the data
        if($this->data->getPreparedTags() && $tagLimit && ((int) $tagLimit) > 0 && sizeof($this->data->getPreparedTags()) > $tagLimit) {
            $this->data->setPreparedTags(array_slice($this->data->getPreparedTags(), 0, $tagLimit));
        }

        // Check if we have a draft post ID to edit
        $content = '';
        $sourceUrls = [];
        $post = null;

        if($draftPostId && $post = get_post($draftPostId)) {

            if(!$isFirstPage) {

                $content = $post->post_content;
                if(!empty($content)) {
                    $content = $content . "<!--nextpage-->";
                }

                // Get source URLs
                $sourceUrls = get_post_meta($draftPostId, $this->postMetaSourceUrls, true);

                if(!$sourceUrls) $sourceUrls = [];
            }
        }

        // Append current source URL
        $sourceUrls[] = $postUrl;

        /*
         * PREPARE POST DATA
         */

        // If post author is not set, then set the first administrator as post author.
        if(!$postAuthor) {
            $userQuery = new WP_User_Query([
                'role'      => 'Administrator',
                'fields'    =>  'ID',
                'number'    =>  1
            ]);
            $postAuthor = $userQuery->get_results()[0];
        }

        $postData = [
            'ID' => $draftPostId ? $draftPostId : 0,
            // If there is a next page to append to this post, then make this post's status draft no matter what.
            // Otherwise, go on with the settings.
            'post_status'       => $nextPageUrl                 ? 'draft'       : ($postStatus ? $postStatus : 'draft'),
            'post_type'         => post_type_exists($postType)  ? $postType     : 'post',
            'post_password'     => $postPassword                ? $postPassword : '',
            'post_category'     => [$urlTuple->category_id],
            'meta_input'        => [
                // Store the source URLs just in case
                $this->postMetaSourceUrls       =>  $sourceUrls
            ],
        ];

        // Set the date if this is the first page of the newly created post.
        $postDate = $this->data->getDateCreated();
        if(!$this->isRecrawl && $isFirstPage) {
            $postData["post_date"] = $postDate;
        }

        // If content exists, append in to the content of the original post
        if($contentExists) {
            $postData['post_content'] = $content . $this->data->getTemplate();

        // Otherwise, do not change the content.
        } else if($post) {
            $postData['post_content'] = $post->post_content;
        }

        // If this is the first page, set other required data
        if($isFirstPage || !$post) {
            $postData = array_merge($postData, [
                'post_author'       => $postAuthor,
                'post_title'        => $this->data->getTitle()        ? $this->data->getTitle() : '',
                'post_excerpt'      => $this->data->getExcerpt()      ? $this->data->getExcerpt()["data"] : '',
                'comment_status'    => $allowComments                 ? 'open' : 'closed',
                'tags_input'        => $this->data->getPreparedTags() ? $this->data->getPreparedTags() : ''
            ]);

            if($post) {
                $postData = array_merge($postData, [
                    'post_date'         => $post->post_date,
                    'post_date_gmt'     => $post->post_date_gmt,
                    'post_name'         => $post->post_name,
                    'guid'              => $post->guid,
                ]);
            }

        // Set everything from the current found post. Even if this is an update, WP requires some variables again.
        } else if($post) {
            $postData = array_merge($postData, [
                'post_author'       => $post->post_author,
                'post_title'        => $post->post_title,
                'post_excerpt'      => $post->post_excerpt,
                'comment_status'    => $post->comment_status,
                'post_date'         => $post->post_date,
                'post_date_gmt'     => $post->post_date_gmt,
                'post_name'         => $post->post_name,
                'guid'              => $post->guid,
            ]);
        }

//        if(static::$DEBUG) var_dump('Post data:');
//        if(static::$DEBUG) var_dump($postData);

        /*
         * CHECK IF DUPLICATE
         */

        // No need to do this when recrawling.
        if(!$this->isRecrawl && $duplicatePostId = $this->isDuplicate($urlTuple->url, $postData, $isFirstPage, !$nextPageUrl)) {

            /**
             * Fires just after a post is decided to be duplicate. At this point, no new post is inserted to the database
             * and the saved files are not deleted yet.
             *
             * @param int $siteIdToCheck    ID of the site
             * @param int $duplicatePostId  Found duplicate post ID
             * @param PostData postData     Data retrieved from the target post URL
             * @param string $postUrl       URL of the post
             * @param PostSaver $this       PostSaver itself
             * @since 1.6.3
             */
            do_action('wpcc/post/after_decided_duplicate', $siteIdToCheck, $duplicatePostId, $this->data, $postUrl, $this);

            // Delete images that were saved from current page.
            $this->data->deleteAttachments();

            // If there is a draft post, delete its attachments. Then, delete the post.
            if($draftPostId) {
                // Delete the thumbnail
                Utils::deletePostThumbnail($draftPostId);

                // Delete the attachments
                foreach(get_attached_media('image', $draftPostId) as $mediaPost) wp_delete_post($mediaPost->ID);

                // Delete the post without sending it to trash.
                wp_delete_post($draftPostId, true);
            }

            $this->resetLastCrawled($siteIdToCheck);

            // Set this URL as saved so that this won't be tried to be saved again and unlock it.
            Factory::databaseService()->updateUrlSavedStatus($urlTuple->id, true, null, $urlTuple->update_count, false);

            // Write an error
            error_log("WPCC - Duplicate post ({$duplicatePostId}) found ({$postUrl}) for site {$siteIdToCheck}. The URL is not saved and it
                is marked as saved so that it will not be tried again.");

            return null;
        }

        /*
         *
         */

        /**
         * Modify post data before it is saved to the database.
         *
         * @param array $postData       The data that will be passed to wp_insert_post function.
         * @param PostData postData     Data retrieved from the target post page's source code
         * @param PostBot $bot          PostBot object used to retrieve the data from the target page
         * @param PostSaver $this       PostSaver itself
         * @param int $siteIdToCheck    ID of the site that stores the settings
         * @param string $postUrl       URL of the post
         * @param array $urlTuple       An array containing info about the URL. This array is retrieved from the URL table.
         *                              Hence, it has all the columns and their values in that table.
         * @param bool isRecrawl        True if this is fired for a recrawl.
         *
         * @return array|null $postData Modified post data. Return null if you do not want to save the post.
         * @since 1.6.3
         */
        $postData = apply_filters('wpcc/post/wp-post', $postData, $this->data, $bot, $this, $siteIdToCheck, $postUrl, $urlTuple, $this->isRecrawl);

        // If the post data is null, do not save the post.
        if($postData === null) return null;

        /**
         * Fires just before a post is inserted/updated.
         *
         * @param array $postData       Data that will be used to create/update a post in the database. If 'ID' key has
         *                              a valid integer value, this means this is fired for an update.
         * @param PostData data         Data retrieved from the target site according to the site settings
         * @param PostSaver $this       PostSaver itself
         * @param int $siteIdToCheck    ID of the site for which the post is retrieved
         * @param string $postUrl       URL of the post
         * @param array $urlTuple       An array containing the URL data. The keys are columns of the DB table storing the URLs.
         * @param bool $isRecrawl       True if this is a recrawl.
         * @param bool $isFirstPage     True if this is the first page of the post
         * @since 1.6.3
         */
        do_action('wpcc/post/before_save', $postData, $this->data, $this, $siteIdToCheck, $postUrl, $urlTuple, $this->isRecrawl, $isFirstPage);

        //
        $postId = wp_insert_post($postData);
        //

        /**
         * Fires just after a post is inserted/updated.
         *
         * @param array $postData       Data that was used to create/update a post in the database. If 'ID' key has
         *                              a valid integer value, this means this is fired for an update.
         * @param PostData data         Data retrieved from the target site according to the site settings
         * @param PostSaver $this       PostSaver itself
         * @param int $siteIdToCheck    ID of the site for which the post is retrieved
         * @param string $postUrl       URL of the post
         * @param array $urlTuple       An array containing the URL data. The keys are columns of the DB table storing the URLs.
         * @param bool $isRecrawl       True if this is a recrawl.
         * @param int $postId           ID of the saved post
         * @param bool $isFirstPage     True if this is the first page of the post
         * @since 1.6.3
         */
        do_action('wpcc/post/after_save', $postData, $this->data, $this, $siteIdToCheck, $postUrl, $urlTuple, $this->isRecrawl, $postId, $isFirstPage);

        if($draftPostId && $postId != $draftPostId) {
            error_log("Draft post ID ({$draftPostId}) and inserted post ID ({$postId}) are different.");
        }

        if(static::$DEBUG) var_dump("Inserted Post ID: " . $postId);

        // Save post thumbnail, meta keywords and description
        if($isFirstPage && $postId) {
            if(static::$DEBUG) var_dump('Save meta and thumbnail');

            $thumbnailUrl = null;

            if($urlTuple->thumbnail_url) {
                $thumbnailUrl = $urlTuple->thumbnail_url;

            } else if($this->data->getThumbnailUrl()) {
                $thumbnailUrl = $this->data->getThumbnailUrl();
            }

            if($thumbnailUrl) {
                // Save the thumbnail
                $file = Utils::saveMedia(Utils::prepareUrl($bot->getSiteUrl(), $thumbnailUrl));
                if($file) {
                    // Delete the already existing thumbnail of the post when updating. Do this only when the first page
                    // is being crawled.
                    if($this->isRecrawl) Utils::deletePostThumbnail($postId);

                    // Save as attachment and get the attachment id.
                    $thumbnailAttachmentId = Utils::insertMedia($postId, $file["file"]);

                    // Set this attachment as post thumbnail
                    set_post_thumbnail($postId, $thumbnailAttachmentId);
                }
            }

            if($this->data->getMetaKeywords()) {
                $key = get_option('_wpcc_meta_keywords_meta_key');
                if($key) Utils::savePostMeta($postId, $key, $this->data->getMetaKeywords(), true);
            }

            if($this->data->getMetaDescription()) {
                $key = get_option('_wpcc_meta_description_meta_key');
                if($key) Utils::savePostMeta($postId, $key, $this->data->getMetaDescription(), true);
            }

            // Set a few WooCommerce things
            if($bot->isProduct($postType)) {
                // Set tags
                if($this->data->getPreparedTags()) {
                    wp_set_object_terms($postId, $this->data->getPreparedTags(), 'product_tag');
                }
            }
        }

        // Save custom meta
        if($postId && $this->data->getCustomMeta()) {
            foreach($this->data->getCustomMeta() as $metaData) {
                $metaValue = $metaData["data"];

                // Delete old meta values first when updating. Do this only when the first page is being crawled.
                if($isFirstPage && $this->isRecrawl) {
                    delete_post_meta($postId, $metaData["meta_key"]);
                }

                if(isset($metaData["multiple"]) && $metaData["multiple"]) {

                    if(is_array($metaValue)) {
                        if(empty($metaValue)) continue;

                        foreach($metaValue as $value) {
                            add_post_meta($postId, $metaData["meta_key"], $value, false);
                        }

                    } else {
                        add_post_meta($postId, $metaData["meta_key"], $metaValue, false);
                    }

                } else {
                    update_post_meta($postId, $metaData["meta_key"], $metaValue);
                }
            }
        }

        // Delete already existing attachments first when updating. Do this only when the first page is being crawled.
        if($isFirstPage && $this->isRecrawl) {
            $alreadyAttachedMedia = get_attached_media('image', $postId);

            foreach($alreadyAttachedMedia as $mediaPost) {
                // If this is the just-saved thumbnail ID, do not delete it.
                if(isset($thumbnailAttachmentId) && $thumbnailAttachmentId == $mediaPost->ID) continue;
                wp_delete_post($mediaPost->ID);
            }

            // Remove WooCommerce gallery attachment IDs
            delete_post_meta($postId, $this->wooCommerceGalleryPostMetaKey);
        }

        // Save attachments
        if($postId && $this->data->getAttachmentData()) {
            $saveAsWooCommerceGallery = $this->getSetting('_post_save_images_as_woocommerce_gallery');
            $galleryAttachmentIds = [];
            foreach($this->data->getAttachmentData() as $attachmentData) {
                $thumbnailAttachmentId = Utils::insertMedia(
                    $postId,
                    $attachmentData["path"],
                    isset($attachmentData["title"])   ? $attachmentData["title"]    : null,
                    isset($attachmentData["alt"])     ? $attachmentData["alt"]      : null
                );

                if($saveAsWooCommerceGallery && isset($attachmentData["gallery_image"]) && $attachmentData["gallery_image"]) {
                    $galleryAttachmentIds[] = $thumbnailAttachmentId;
                }
            }

            // Save the gallery as WooCommerce product gallery
            if(!empty($galleryAttachmentIds)) {

                // First get the images that are already set as WC gallery images if exists
                $existingAttachmentIds = get_post_meta($postId, $this->wooCommerceGalleryPostMetaKey, null);

                if($existingAttachmentIds === null) {
                    $existingAttachmentIds = [];

                } else if(!is_array($existingAttachmentIds)) {
                    $existingAttachmentIds = explode(",", $existingAttachmentIds);
                }

                if(!is_array($existingAttachmentIds)) $existingAttachmentIds = [$existingAttachmentIds];

                foreach($galleryAttachmentIds as $aid) {
                    if(array_search($aid, $existingAttachmentIds) === false) {
                        $existingAttachmentIds[] = $aid;
                    }
                }

                // Update the gallery meta
                update_post_meta($postId, $this->wooCommerceGalleryPostMetaKey, implode(", ", $existingAttachmentIds));
            }
        }

        // Some WooCommerce-specific things
        if($bot->isProduct($postType) && $postId) {

            // Things that should be done only when the first page is being saved
            if($isFirstPage) {
                // Set the product category
                $term = get_term_by('id', $urlTuple->category_id, 'product_cat');

                if($term && isset($term->term_id)) {
                    wp_set_object_terms($postId, $term->term_id, 'product_cat');
                }
            }
        }

        // Free-up some memory
        unset($this->data);

        // Save related meta
        if($updateLastCrawled)
            $this->updateLastCrawled($siteIdToCheck, $nextPageUrl ? $lastCrawledUrlId : null, $nextPageUrl, $nextPageUrls, $nextPageUrl ? $postId : '');

        // Save post URL as post meta
        if($isFirstPage && $postId && isset($urlTuple->url))
            update_post_meta($postId, $this->postMetaPostFirstPageUrl, $urlTuple->url);

        // Update saved_at if this is the first page and the URL tuple does not have a saved_post_id
        if($isFirstPage && $postId && !$urlTuple->saved_post_id) {
            Factory::databaseService()->updateUrlPostSavedAt($urlTuple->id, $postId, $postDate);
        }

        // If this is the last page, tidy up things.
        if(!$nextPageUrl) {

            // Set this URL as saved
            if(!$this->isRecrawl) {
                Factory::databaseService()->updateUrlSavedStatus(
                    $urlTuple->id,
                    true,
                    $postId ? $postId : null,
                    $urlTuple->update_count,
                    false
                );

            // Otherwise, set this URL as recrawled
            } else {
                Factory::databaseService()->updateUrlRecrawledStatus($urlTuple->id, $urlTuple->update_count + 1, false);
            }

        // Otherwise, remove the lock so that the next page can be saved. Also, make this URL not saved so that it won't
        // be selected as a URL that needs to be crawled for post crawling event.
        } else {
            Factory::databaseService()->updateUrlSavedStatus($urlTuple->id, false, $postId ? $postId : null, $urlTuple->update_count, false);
        }

        if(static::$DEBUG) var_dump('Last Crawled Url ID: '    . $lastCrawledUrlId);
        if(static::$DEBUG) var_dump('Category ID: '            . $urlTuple->category_id);
        if(static::$DEBUG) var_dump('Next Page URL: '          . $nextPageUrl);
        if(static::$DEBUG) var_dump('Next Page URLs:');
        if(static::$DEBUG) var_dump($nextPageUrls);
        if(static::$DEBUG) var_dump('Draft Post ID: '          . ($nextPageUrl ? $postId : ''));

        return $postId;
    }

    /*
     *
     */

    /**
     * Does the updates for post-crawling event
     *
     * @param int         $siteId           Last updated site ID
     * @param int         $lastCrawledUrlId ID of the URL from the urls table which is crawled
     * @param string|null $nextPageUrl      Next page URL
     * @param array|null  $nextPageUrls     Next page URLs
     * @param int|null    $draftPostId      Draft post ID
     */
    private function updateLastCrawled($siteId, $lastCrawledUrlId, $nextPageUrl, $nextPageUrls, $draftPostId) {
        // Get the prefix for the CRON meta keys of the current task
        $prefix = $this->getCronPostMetaPrefix();

        Utils::savePostMeta($siteId, $prefix . '_last_crawled_url_id',    $lastCrawledUrlId,                     true);
        Utils::savePostMeta($siteId, $prefix . '_post_next_page_url',     $nextPageUrl,                          true);
        Utils::savePostMeta($siteId, $prefix . '_post_next_page_urls',    $nextPageUrls,                         true);
        Utils::savePostMeta($siteId, $prefix . '_post_draft_id',          $draftPostId ? $draftPostId : '',      true);
        Utils::savePostMeta($siteId, $prefix . '_last_crawled_at',        current_time('mysql'),                 true);

        // Set last crawled site id if there is no draft post ID. By this way, if there is a paged post crawling in progress,
        // before we get a post from another site, we finish crawling all pages of current post.
        if(!$draftPostId) update_option($this->isRecrawl ? $this->optionLastRecrawledSiteId : $this->optionLastCrawledSiteId, $siteId, false);
    }

    /**
     * Updates last recrawled site ID option
     *
     * @param int $siteId
     */
    public function updateLastRecrawledSiteId($siteId) {
        update_option($this->optionLastRecrawledSiteId, $siteId, false);
    }

    /**
     * Reset CRON metas about last-crawled URL
     *
     * @param int $siteId ID of the site
     */
    public function resetLastCrawled($siteId) {
        $this->updateLastCrawled($siteId, null, null, null, null);
    }

    /**
     * Get a URL tuple to crawl. This method is good for crawling URLs uniformly, by getting a URL from a different
     * category.
     *
     * @param int $siteId Site ID for which a URL tuple will be retrieved
     * @param int $lastCrawledUrlId Last crawled URL id from urls table
     * @return null|object Null or found URL tuple as object
     */
    public function getUrlTupleToCrawl($siteId, $lastCrawledUrlId) {
        global $wpdb;
        $tableName = Factory::databaseService()->getDbTableUrlsName();

        // If last crawled URL id is null, then get the first URL that needs to be saved.
        if($lastCrawledUrlId === null) {
            // Get the last crawled URL ID instead of getting the first found URL ID that needs saving.
            $query = "SELECT * FROM $tableName WHERE is_saved = TRUE AND is_locked = FALSE AND saved_post_id IS NOT NULL AND post_id = %d ORDER BY saved_at DESC LIMIT 1";
            $results = $wpdb->get_results($wpdb->prepare($query, $siteId));

            // Then, if a URL is found, call this method with that URL ID so that another URL ID from a different
            // category can be found.
            if(!empty($results)) return $this->getUrlTupleToCrawl($siteId, $results[0]->id);

            // Otherwise, if there is no last crawled URL, get the first URL that needs to be saved.
            $query = "SELECT * FROM $tableName WHERE is_saved = FALSE AND is_locked = FALSE AND saved_post_id IS NULL AND post_id = %d LIMIT 1";
            $results = $wpdb->get_results($wpdb->prepare($query, $siteId));

            return empty($results) ? null : $results[0];
        }

        // Get the last crawled URL as object from the table
        $query = "SELECT * FROM $tableName WHERE id = %d";
        $results = $wpdb->get_results($wpdb->prepare($query, $lastCrawledUrlId));

        // If the URL is not found in the table, then get the first URL that needs to be saved or return null.
        // Recalling this method with a null lastCrawledSiteId will do the job.
        if(empty($results)) {
            return $this->getUrlTupleToCrawl($siteId, null);
        }

        // Get the tuple as object
        $lastCrawledUrlTuple = $results[0];

        // Get reference category ID and try to get a URL for the next category.
        $referenceCategoryId = $lastCrawledUrlTuple->category_id;

        // Find all categories with an unsaved URL for the target site ID.
        $query = "SELECT DISTINCT category_id FROM $tableName  WHERE is_saved = FALSE AND is_locked = FALSE AND saved_post_id IS NULL AND post_id = %d";
        $categoryIds = $wpdb->get_results($wpdb->prepare($query, $siteId));

        // If there is no category, it means there is no URL to be saved. Return null.
        if(empty($categoryIds)) return null;

        // Try to find a URL with category different than the reference category. If there is no other category, then
        // find a URL with the reference category ID.
        $referenceCategoryPos = null;
        foreach($categoryIds as $key => $categoryIdObject) {
            if($categoryIdObject->category_id == $referenceCategoryId) {
                $referenceCategoryPos = $key;
                break;
            }
        }

        // If the reference category is not found, get the first category in the list.
        // If the reference category is the last item in the list, get the first category in the list.
        // Otherwise, get the category next to the reference category.
        $targetCategoryId = null;
        if($referenceCategoryPos === null || $referenceCategoryPos == sizeof($categoryIds) - 1) {
            $targetCategoryId = $categoryIds[0]->category_id;
        } else {
            $targetCategoryId = $categoryIds[$referenceCategoryPos + 1]->category_id;
        }

        // Now, get a URL that needs to be saved and belongs to the target site ID and target category ID.
        $query = "SELECT * FROM $tableName WHERE post_id = %d AND category_id = %d AND is_saved = FALSE AND is_locked = FALSE AND saved_post_id IS NULL LIMIT 1";
        $results = $wpdb->get_results($wpdb->prepare($query, [$siteId, $targetCategoryId]));

        // The results cannot be empty according to the logic. Return the first found URL tuple.
        return $results[0];
    }

    /**
     * Check if a post is duplicate considering the current settings set by {@link SettingsTrait::setSettings}.
     *
     * @param string     $url         URL of the post
     * @param array|null $postData    An array having keys named as columns in wp_posts table. And their values, of
     *                                course.
     * @param bool       $isFirstPage True if this check is done for the first page of the post.
     * @param bool       $isLastPage  True if this check is done for the last page of the post.
     * @return false|int Previously saved post ID if this is a duplicate. Otherwise, false.
     */
    public function isDuplicate($url, $postData, $isFirstPage, $isLastPage) {
        // If this is not the first and the last page, no need to check for duplicate.
        if(!$isFirstPage && !$isLastPage) return false;

        // Get the current post ID
        $currentPostId = Utils::array_get($postData, "ID");
        if(!$currentPostId) $currentPostId = 0;

        // Get the settings for duplicate checking
        $duplicateCheckSettingValues = $this->getSetting('_duplicate_check_types');

        // The values are stored under 0 key. So, make sure 0 key exists.
        if(!$duplicateCheckSettingValues || !isset($duplicateCheckSettingValues[0])) return false;

        $values = $duplicateCheckSettingValues[0];
        $checkUrl = isset($values[PostSaver::DUPLICATE_CHECK_URL]);
        $checkTitle = isset($values[PostSaver::DUPLICATE_CHECK_TITLE]);
        $checkContent = isset($values[PostSaver::DUPLICATE_CHECK_CONTENT]);

        global $wpdb;

        $id = null;

        // If this is the first page, check URL and title
        if($isFirstPage) {
            // Check the URL
            if($checkUrl && $url) {
                // Check the URL with and without a trailing slash
                $query = "SELECT post_id
                    FROM {$wpdb->postmeta}
                    WHERE meta_key = '{$this->postMetaPostFirstPageUrl}'
                      AND (meta_value = %s OR meta_value = %s)
                      AND post_id <> %d;
                ";
                $id = $wpdb->get_var($wpdb->prepare($query, trailingslashit($url), untrailingslashit($url), $currentPostId));
            }

            // Check the title
            if(!$id && $checkTitle && $postData) {
                $postTitle = Utils::array_get($postData, "post_title");
                $postType = Utils::array_get($postData, "post_type");

                $query = "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = %s AND ID <> %d";
                $id = $wpdb->get_var($wpdb->prepare($query, $postTitle, $postType, $currentPostId));
            }
        }

        // If this is the last page, check the content
        if(!$id && $isLastPage && $checkContent && $postData) {
            $postContent = Utils::array_get($postData, "post_content");
            $postType = Utils::array_get($postData, "post_type");

            $query = "SELECT ID FROM {$wpdb->posts} WHERE post_content = %s AND post_type = %s AND ID <> %d";
            $id = $wpdb->get_var($wpdb->prepare($query, $postContent, $postType, $currentPostId));
        }

        // If a duplicate post is found, add an error.
        if($id) $this->addError(ErrorType::DUPLICATE_POST, $id);

        return $id ? $id : false;
    }

    /**
     * Get post meta prefix for the meta keys that will be used to store data for current task.
     * @see $recrawlPostMetaPrefix
     * @see $crawlPostMetaPrefix
     * @return string
     */
    private function getCronPostMetaPrefix() {
        return $this->isRecrawl ? $this->cronRecrawlPostMetaPrefix : $this->cronCrawlPostMetaPrefix;
    }

    /**
     * @param bool $isRecrawl See {@link isRecrawl}
     */
    public function setIsRecrawl($isRecrawl) {
        $this->isRecrawl = $isRecrawl;
    }

    /*
     *
     */

    /**
     * Get duplicate check types prepared to be shown in a select element.
     *
     * @return array Returns an array with "values" and "defaults" keys, both of which has an array value. The
     *               key-description pairs are stored under "values" key. "defaults" stores key-defaultValue pairs.
     */
    public static function getDuplicateCheckOptionsForSelect() {
        return [
            "values" => [
                PostSaver::DUPLICATE_CHECK_URL     => _wpcc("URL"),
                PostSaver::DUPLICATE_CHECK_TITLE   => _wpcc("Title"),
                PostSaver::DUPLICATE_CHECK_CONTENT => _wpcc("Content"),
            ],
            "defaults" => [
                PostSaver::DUPLICATE_CHECK_URL     => 1,
                PostSaver::DUPLICATE_CHECK_TITLE   => 1,
                PostSaver::DUPLICATE_CHECK_CONTENT => 0,
            ]
        ];
    }

}