<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 24/08/16
 * Time: 16:24
 */

namespace WPCCrawler\controllers;


use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\objects\enums\ErrorType;
use WPCCrawler\objects\page\AbstractMenuPage;
use WPCCrawler\objects\savers\PostSaver;
use WPCCrawler\objects\traits\ErrorTrait;
use WPCCrawler\Utils;

class ToolsController extends AbstractMenuPage {

    use ErrorTrait;

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle() {
        return _wpcc('Tools');
    }

    /**
     * @return string Page title
     */
    public function getPageTitle() {
        return _wpcc('Tools');
    }

    /**
     * @return string Slug for the page
     */
    public function getPageSlug() {
        return "tools";
    }

    /**
     * Get view for the page.
     *
     * @return mixed Not-rendered blade view for the page
     */
    public function getView() {
        // Add assets
        Factory::assetManager()->addPostSettings();
        Factory::assetManager()->addTools();

        // Get available sites
        $availableSites = get_posts(['post_type' => Constants::$POST_TYPE, 'numberposts' => -1]);

        $sites = [];
        foreach($availableSites as $site) {
            $sites[$site->ID] = $site->post_title;
        }

        return Utils::view('tools/main')->with([
            'settings'      =>  [], // To prevent errors, since all form items work with $settings variable
            'sites'         =>  $sites,
            'categories'    =>  Utils::getCategories(),
            'urlTypes'      =>  $this->getUrlTypes()
        ]);
    }

    public function handleAJAX() {
        $data = parent::handleAJAX();

        $result = null;
        switch ($data["tool_type"]) {
            // Save a post
            case 'save_post':
                if (isset($data["_wpcc_tools_post_url"])) {
                    $postId = $this->savePostManually(
                        $data["_wpcc_tools_site_id"],
                        $data["_wpcc_tools_post_url"],
                        $data["_wpcc_tools_category_id"],
                        $data["_wpcc_tools_featured_image_url"]
                    );

                    if ($postId) {
                        $postUrl = get_permalink($postId);
                        $result = sprintf(_wpcc('The post is saved. You can check it here') . ': <a href="%1$s" target="_blank">%1$s</a>', $postUrl);

                    } else {
                        $result = _wpcc('The post could not be saved.');
                        if($errors = $this->getErrorDescriptions()) $result .= "<br>" . implode("<br>", $errors);

                        // If there is a duplicate post, show its link to the user.
                        if($duplicatePostId = $this->getErrorValue(ErrorType::DUPLICATE_POST)) {
                            $duplicatePost = get_post($duplicatePostId);

                            if($duplicatePost) {
                                $duplicatePostTitle = $duplicatePost->post_title ? $duplicatePost->post_title : _wpcc("No Title");
                                $duplicatePostUrl = get_permalink($duplicatePostId);
                                $result .= "<br>" . _wpcc("You should delete the duplicate post first") . ": " .
                                    sprintf('<a href="%1$s" target="_blank">%2$s</a> ID: %3$s', $duplicatePostUrl, $duplicatePostTitle, $duplicatePostId);
                            }
                        }
                    }
                }
                break;

            // Recrawl a post
            case 'recrawl_post':
                if (isset($data["_wpcc_tools_recrawl_post_id"])) {
                    $postId = $this->recrawlPostManually($data["_wpcc_tools_recrawl_post_id"]);

                    if ($postId) {
                        $postUrl = get_permalink($postId);
                        $result = sprintf(_wpcc('The post is recrawled. You can check it here') . ': <a href="%1$s" target="_blank">%1$s</a>', $postUrl);

                    } else {
                        $result = _wpcc('The post could not be found or it might not have been saved by WP Content Crawler.');
                        if($errors = $this->getErrorDescriptions()) $result .= "<br>" . implode("<br>", $errors);
                    }
                }
                break;

            // Delete URLs
            case "delete_urls":
                if (isset($data["_wpcc_tools_safety_check"]) && $data["_wpcc_tools_safety_check"]
                    && isset($data["_wpcc_tools_site_id"]) && isset($data["_wpcc_tools_url_type"])) {

                    $siteId = $data["_wpcc_tools_site_id"];
                    $resetLastCrawled = false;
                    $resetLastRecrawled = false;

                    switch ($data["_wpcc_tools_url_type"]) {
                        case "url_type_queue":
                            $result = Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($siteId, false);
                            $resetLastCrawled = true;
                            break;

                        case "url_type_saved":
                            $result = Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($siteId, true);
                            $resetLastRecrawled = true;
                            break;

                        case "url_type_all":
                            $result = Factory::databaseService()->deleteUrlsBySiteId($siteId);
                            $resetLastCrawled = true;
                            $resetLastRecrawled = true;
                            break;
                    }

                    if($resetLastCrawled) {
                        Factory::postSaver()->setIsRecrawl(false);
                        Factory::postSaver()->resetLastCrawled($siteId);
                    }

                    if($resetLastRecrawled) {
                        Factory::postSaver()->setIsRecrawl(true);
                        Factory::postSaver()->resetLastCrawled($siteId);
                    }

                    if ($result !== false) {
                        $result = _wpcc("Deleted successfully.");
                    }

                } else if(!isset($data["_wpcc_tools_safety_check"])) {
                    $result = _wpcc('You did not check the safety checkbox.');
                }

                break;

            // Unlock URLs
            case "unlock_all_urls":

                $res = Factory::databaseService()->unlockAllUrls();
                $result = $res ?
                    ($res > 1 ? sprintf(_wpcc("%s URLs are unlocked."), $res) : _wpcc("1 URL is unlocked.") ) :
                    _wpcc("There are no locked URLs currently.");

                break;
        }

        echo json_encode(['view' => $result]);
    }

    /*
     * HELPERS
     */

    /**
     * Saves a post by post URL, site ID and category ID
     *
     * @param int $siteId ID of a site (custom post type) to which the URL belongs
     * @param string $postUrl The URL for the post-to-be-saved
     * @param int $categoryId ID of a category in which the saved post is saved
     * @param null|string $thumbnailUrl Thumbnail (featured image) URL for the post
     * @return int|null Inserted post's ID
     */
    public function savePostManually($siteId, $postUrl, $categoryId, $thumbnailUrl = null) {
        $settings = get_post_meta($siteId);

        $postSaver = new PostSaver();
        $postSaver->setSettings($settings, Factory::postService()->getSingleMetaKeys());
        $postSaver->setIsRecrawl(false);

        // First check if it exists
        $urlId = null;
        $urlTuple = Factory::databaseService()->getUrlBySiteIdAndUrl($siteId, $postUrl);

        // Check for duplicate
        $postData = null;
        if($urlTuple && $urlTuple->saved_post_id) {
            // Get the post
            $postData = get_post($urlTuple->saved_post_id, ARRAY_A);

            // If the post exists, this is a duplicate. Checking this is vital. If we skip this and check it via
            // isDuplicate method, it will exclude the saved_post_id when checking. And then, it won't be able to
            // catch this duplicate post. Either check the existence of postData here, or set urlTuple's saved_post_id
            // to null before passing it to isDuplicate method.
            if($postData) {
                $this->addError(ErrorType::DUPLICATE_POST, $urlTuple->saved_post_id);
                return null;
            }

            // Otherwise, check for another duplicate post. This is a very unlikely case.
            if($postSaver->isDuplicate($postUrl, $postData, true, true)) {
                // Get the errors from the post saver so that we can use them later.
                $this->setErrors($postSaver->getErrors());

                return null;
            }
        }

        // If saved, delete.
        if($urlTuple) Factory::databaseService()->deleteUrl($urlTuple->id);

        // Now, save the URL
        $urlId = Factory::databaseService()->addUrl($siteId, $postUrl, $thumbnailUrl, $categoryId);

        // Define the required variables. These variables will be changed by savePost function.
        $nextPageUrl    = null;
        $nextPageUrls   = null;
        $draftPostId    = null;

        $postId = null;
        $finished = false;
        while(!$finished) {
            $postId = $postSaver->savePost($siteId, $settings, $urlId, false, $nextPageUrl, $nextPageUrls, $postId);

            if(!$nextPageUrl || !$postId) $finished = true;
        }
//        var_dump("Saving the post is finished. Post ID is ");
//        var_dump($postId);

        // Get the errors from the post saver so that we can use them later.
        $this->setErrors($postSaver->getErrors());

        return $postId;

    }

    /**
     * Recrawl a post manually by its post ID
     *
     * @param int $postId ID of the post to be recrawled
     * @return null|int ID of the post or null if there was something wrong
     */
    public function recrawlPostManually($postId) {
        if(!$postId || $postId < 1) return null;
        $urlTuple = Factory::databaseService()->getUrlByPostId($postId);

        if(!$urlTuple) return null;

        // Define the required variables. These variables will be changed by savePost function.
        $siteId         = $urlTuple->post_id;
        $nextPageUrl    = null;
        $nextPageUrls   = null;
        $draftPostId    = null;

        $settings = get_post_meta($siteId);

        $postSaver = new PostSaver();
        $postSaver->setIsRecrawl(true);

        $finished = false;
        while(!$finished) {
            $postId = $postSaver->savePost($siteId, $settings, $urlTuple->id, false,
                $nextPageUrl, $nextPageUrls, $postId);

            if(!$nextPageUrl || !$postId) $finished = true;
        }
//        var_dump("Recrawling the post is finished. Post ID is ");
//        var_dump($postId);

        // Get the errors from the post saver so that we can use them later.
        $this->setErrors($postSaver->getErrors());

        return $postId;
    }

    /**
     * Get URL types to be shown as options in a select element.
     * @return array URL types as key,value pairs
     */
    private function getUrlTypes() {
        return [
            "url_type_queue"    =>  _wpcc("In Queue"),
            "url_type_saved"    =>  _wpcc("Already Saved"),
            "url_type_all"      =>  _wpcc("All")
        ];
    }
}