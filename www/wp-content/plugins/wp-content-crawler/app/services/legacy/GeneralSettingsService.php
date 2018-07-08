<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 06/04/16
 * Time: 11:01
 */

namespace WPCCrawler\services;

use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\objects\Settings;
use WPCCrawler\Utils;

class GeneralSettingsService {

    /**
     * @var array Keys for general settings. These keys are used both as options (general settings) and
     *            as post meta (custom settings for site).
     */
    public $settings = [
        '_wpcc_http_user_agent',                        // string   The user agent for the crawler
        '_wpcc_http_accept',                            // string   The user agent for the crawler
        '_wpcc_http_allow_cookies',                     // bool     True if cookies are allowed, false otherwise

        '_wpcc_is_scheduling_active',                   // bool     If true, CRON scheduling is active
        '_wpcc_no_new_url_page_trial_limit',            // int      Stores the limit for how many pages should be crawled if there is no new URL. Read the doc of
                                                        //          SchedulingService#handleNoNewUrlInsertedCount method to understand why this is necessary.
        '_wpcc_max_page_count_per_category',            // int      Max number of pages to be checked for each category
        '_wpcc_interval_url_collection',                // string   Key of a WPCC CRON interval, indicating url collection interval
        '_wpcc_interval_post_crawl',                    // string   Key of a WPCC CRON interval, indicating post-crawling interval

        '_wpcc_allow_comments',                         // bool     True to allow comments, false otherwise
        '_wpcc_post_status',                            // string   One of the WordPress post statuses
        '_wpcc_post_type',                              // string   One of the WordPress post types
        '_wpcc_post_author',                            // int      ID of a user
        '_wpcc_post_tag_limit',                         // int      The number of tags that can be added to a post at max
        '_wpcc_post_password',                          // string   Password for the posts

        '_wpcc_meta_keywords_meta_key',                 // string   Post meta key to store meta keywords
        '_wpcc_meta_description_meta_key',              // string   Post meta key to store meta description
        '_wpcc_test_find_replace',                      // string   Test code for find-and-replaces
        '_wpcc_find_replace',                           // array    An array including what to find and with what to replace for page
    ];

    public function __construct() {
        $wptslm = Factory::wptslmClient();
        if($wptslm->isUserCool()) {
            add_action('admin_menu', function () {
                // Create sub menu page
                add_submenu_page(
                    'edit.php?post_type=' . Constants::$POST_TYPE,
                    _wpcc('General Settings'),
                    _wpcc('General Settings'),
                    Constants::$ALLOWED_USER_CAPABILITY,
                    Constants::$POST_TYPE . '_general_settings',
                    function () {
                        echo $this->getGeneralSettingsPage();
                    }
                );
            }, 3);

            // Add styles and scripts for general settings page
            add_action('admin_enqueue_scripts', function ($hook) {
                // Check if we are on the general settings page.
                $valid = $hook == sprintf('%1$s_page_%1$s_general_settings', Constants::$POST_TYPE);
                if (!$valid) return;

                Factory::assetManager()->addPostSettings();
                Factory::assetManager()->addGeneralSettings();
                Factory::assetManager()->addTooltip();
            });

            // Listen post requests
            add_action('admin_post_wcc_general_settings', function () {
                $this->postGeneralSettingsPage();
            });

            // Set default settings when the plugin is activated
            register_activation_hook(Utils::getPluginFilePath(), function () {
                Factory::generalSettingsController()->setDefaultGeneralSettings();
            });
        }
    }

    /**
     * Get the HTML for the general settings page
     * @return string View for the general settings page
     */
    public function getGeneralSettingsPage() {
        return Utils::view('general-settings/general-settings')->with(Settings::getSettingsPageVariables())->render();
    }

    /**
     * Handles post requests made from general settings page
     */
    public function postGeneralSettingsPage() {
        $data = $_POST;

        $keys = $this->settings;
        $message = '';
        $success = true;

        // Validate the password fields
        $validate = Utils::validatePasswordInput($data, $keys);
        if(!$validate["success"]) {
            $message = $validate["message"] . ' ' . _wpcc('Settings are updated, but password could not be changed.');
            $success = false;
        }

        // Save options
        foreach ($data as $key => $value) {
            if (in_array($key, $this->settings)) {
                update_option($key, $value, false);

                // Remove the key, since it is saved.
                unset($keys[array_search($key, $keys)]);
            }
        }

        // Delete options which are not set
        foreach($keys as $key) delete_option($key);

        // Set or remove CRON events
        Factory::schedulingService()->handleCronEvents();

        // Redirect back
        $redirectParams = 'success=' . ($success ? 'true' : 'false') . ($message ? '&message=' . urlencode($message) : '');
        wp_redirect(admin_url(sprintf('edit.php?post_type=%1$s&page=%1$s_general_settings&' . $redirectParams, Constants::$POST_TYPE)));
    }

}