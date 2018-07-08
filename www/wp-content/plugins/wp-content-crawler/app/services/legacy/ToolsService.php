<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 13/04/16
 * Time: 13:20
 */

namespace WPCCrawler\services;

use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\Utils;

class ToolsService {

    public function __construct() {
        $wptslm = Factory::wptslmClient();
        if($wptslm->isUserCool()) {
            // Create sub menu page
            add_action('admin_menu', function () {
                add_submenu_page(
                    'edit.php?post_type=' . Constants::$POST_TYPE,
                    _wpcc('Tools'),
                    _wpcc('Tools'),
                    Constants::$ALLOWED_USER_CAPABILITY,
                    Constants::$POST_TYPE . '_tools',
                    function () {
                        echo $this->getToolsPage();
                    }
                );
            }, 2);

            // Add styles and scripts for test page
            add_action('admin_enqueue_scripts', function ($hook) {
                // Check if we are on the custom post page.
                $valid = $hook == sprintf('%1$s_page_%1$s_tools', Constants::$POST_TYPE);
                if (!$valid) return;

                Factory::assetManager()->addPostSettings();
                Factory::assetManager()->addTools();
            });

            // Listen AJAX requests and respond;
            add_action('wp_ajax_wcc_tools', function () {
                if(!current_user_can(Constants::$ALLOWED_USER_CAPABILITY)) return;
                $this->ajaxToolsPage();
            });
        }
    }

    public function getToolsPage() {
        // Get available sites
        $availableSites = get_posts(['post_type' => Constants::$POST_TYPE, 'numberposts' => -1]);

        $sites = [];
        foreach($availableSites as $site) {
            $sites[$site->ID] = $site->post_title;
        }

        return Utils::view('tools/tools')->with([
            'settings'      =>  [], // To prevent errors, since all form items work with $settings variable
            'sites'         =>  $sites,
            'categories'    =>  Utils::getCategories(),
            'urlTypes'      =>  $this->getUrlTypes()
        ])->render();
    }

    /**
     * Handles AJAX requests made from tools page
     */
    public function ajaxToolsPage() {
        if (!check_admin_referer('wcc-tools', Constants::$NONCE_NAME))
            wp_die("Invalid nonce.");

        if (!isset($_POST["data"])) wp_die(_wpcc("Data does not exist in your request. The request should include 'data'"));

        $data = $_POST["data"];

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
                    }
                }
                break;

            // Delete URLs
            case "delete_urls":
                if (isset($data["_wpcc_tools_safety_check"]) && $data["_wpcc_tools_safety_check"]
                    && isset($data["_wpcc_tools_site_id"]) && isset($data["_wpcc_tools_url_type"])) {

                    $siteId = $data["_wpcc_tools_site_id"];
                    switch ($data["_wpcc_tools_url_type"]) {
                        case "url_type_queue":
                            $result = Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($siteId, false);

                            // Reset last-crawled CRON settings to avoid malfunctions
                            Factory::schedulingService()->resetLastCrawled($siteId);
                            break;
                        case "url_type_saved":
                            $result = Factory::databaseService()->deleteUrlsBySiteIdAndSavedStatus($siteId, true);
                            break;
                        case "url_type_all":
                            $result = Factory::databaseService()->deleteUrlsBySiteId($siteId);

                            // Reset last-crawled CRON settings to avoid malfunctions
                            Factory::schedulingService()->resetLastCrawled($siteId);
                            break;
                    }

                    if ($result !== false) {
                        $result = _wpcc("Deleted successfully.");
                    }

                } else if(!isset($data["_wpcc_tools_safety_check"])) {
                    $result = _wpcc('You did not check the safety checkbox.');
                }

                break;
        }

        echo $result;
        wp_die();
    }

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
        // First check if it exists
        $urlId = null;
        if($urlTuple = Factory::databaseService()->getUrlBySiteIdAndUrl($siteId, $postUrl)) {
            // If saved, delete.
            Factory::databaseService()->deleteUrl($urlTuple->id);
        }

        // Now, save the URL
        $urlId = Factory::databaseService()->addUrl($siteId, $postUrl, $thumbnailUrl, $categoryId);

        // Define the required variables. These variables will be changed by savePost function.
        $nextPageUrl    = null;
        $nextPageUrls   = null;
        $draftPostId    = null;

        $settings = get_post_meta($siteId);

        $postId = null;
        $finished = false;
        while(!$finished) {
            $postId = Factory::schedulingService()->savePost($siteId, $settings, $urlId, false,
                $nextPageUrl, $nextPageUrls, $postId);

            if(!$nextPageUrl || !$postId) $finished = true;
        }
//        var_dump("Saving the post is finished. Post ID is ");
//        var_dump($postId);

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