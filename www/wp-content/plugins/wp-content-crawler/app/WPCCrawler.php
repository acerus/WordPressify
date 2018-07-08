<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 28/03/16
 * Time: 21:30
 */

namespace WPCCrawler;

use WP_Post;

class WPCCrawler {

    /*
     *  INITIALIZE EVERYTHING
     */

    /**
     * @var WPCCrawler
     */
    private static $INSTANCE;

    public static function getInstance() {
        // Set the folder including translation files, and handle translations
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(Constants::$APP_DOMAIN, false, Constants::$PLUGIN_FILE_NAME . "/app/lang/");
        });

        // Check if PHP version is OK. If not, show a notice.
        if(version_compare(phpversion(), "5.6", "<")) {
            add_action("admin_notices", function() {
                ?>
                <div class="update-nag">
                    <p><?php echo _wpcc("WP Content Crawler requires at least PHP 5.6. Your current PHP version is " . phpversion()); ?></p>
                </div>
                <?php
            });
        }

        // Notify if mbstring is not enabled.
        if (!extension_loaded('mbstring')) {
            add_action("admin_notices", function() {
                ?>
                <div class="update-nag">
                    <p><?php echo _wpcc("WP Content Crawler requires mbstring extension enabled. Please enable mbstring extension. "); ?></p>
                </div>
                <?php
            });
        }

        // Initialize the factory.
        Factory::getInstance();

        if (!static::$INSTANCE) static::$INSTANCE = new WPCCrawler();
        return static::$INSTANCE;
    }

    public function __construct() {
        // Set chmod of storage dir when the plugin is activated
        register_activation_hook(Utils::getPluginFilePath(), function() {
            $storagePath    = WP_CONTENT_CRAWLER_PATH . Constants::$APP_DIR_NAME . Constants::$RELATIVE_STORAGE_DIR;
            $cachePath      = WP_CONTENT_CRAWLER_PATH . Constants::$APP_DIR_NAME . Constants::$RELATIVE_CACHE_DIR;

            chmod($storagePath, 0755);
            chmod($cachePath,   0755);
        });

        // Add plugin action links for easy navigation
        add_filter(sprintf('plugin_action_links_%s', plugin_basename(Utils::getPluginFilePath())), function($links) {
            $newLinks = [
                sprintf('<a href="%s">%s</a>', Factory::generalSettingsController()->getFullPageUrl(), _wpcc("General Settings")),
            ];
            return array_merge($links, $newLinks);
        });

        add_action('admin_init', function() {

            // Listen to post deletes
            add_action('delete_post', function ($postId) {
                // Set a post's URL deleted, if it is one of posts saved by the plugin.
                Factory::databaseService()->setUrlDeleted($postId);
            });

            // Listen to post updates
            add_action('post_updated', function ($postId, $postAfter, $postBefore) {
                /** @var WP_Post $postAfter */
                /** @var WP_Post $postBefore */

                // Update corresponding URL's "saved_at" when the post's "post_date" is changed
                if($postAfter && $postBefore && $postAfter->post_date != $postBefore->post_date) {
                    Factory::databaseService()->updateUrlPostSavedAtByPostId($postId, $postAfter->post_date);
                }

            }, 10, 3);

        });
    }

    /**
     * Set whether the script is being run for a test or not. You can get the test status from {@link WPCCrawler::isDoingTest}.
     */
    public static function setDoingTest($test) {
        if($test) {
            if(!defined('WPCC_DOING_TEST')) define('WPCC_DOING_TEST', true);
        } else {
            if(defined('WPCC_DOING_TEST')) define('WPCC_DOING_TEST', false);
        }
    }

    /**
     * @return bool True if the script is run to conduct a test. False otherwise.
     */
    public static function isDoingTest() {
        return defined('WPCC_DOING_TEST') && WPCC_DOING_TEST ? true : false;
    }

}