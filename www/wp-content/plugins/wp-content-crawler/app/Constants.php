<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 15:32
 */

namespace WPCCrawler;


class Constants {

    /** @var string Domain which is used to define WCC-specific things in WordPress */
    public static $APP_DOMAIN = 'wp-content-crawler';

    /** @var string An abbreviation for the plugin */
    public static $APP_SHORT_NAME = 'wpcc';

    /** @var string Used to validate nonces */
    public static $NONCE_NAME = "wcc_nonce";

    /** @var string Name of custom post type which is used to store the sites to be crawled */
    public static $POST_TYPE = 'wcc_sites';

    /** @var string Name of the main plugin file */
    public static $PLUGIN_FILE_NAME = "wp-content-crawler";

    /** @var string App directory */
    private static $APP_DIR = null /*'/wp-content/plugins/wp-content-crawler/app'*/;

    /** @var null Admin directory name */
    private static $ADMIN_DIR_NAME = null;

    /** @var string ID of the meta box used to show settings in custom post (site) create/edit page */
    public static $SITE_SETTINGS_META_BOX_ID = 'wpc-crawler-settings';

    /** @var string ID of the meta box used to show notes in custom post (site) create/edit page */
    public static $SITE_SETTINGS_NOTES_META_BOX_ID = 'wpc-crawler-notes';

    public static $DATE_FORMAT = "H:i:s d-m-Y";

    public static $MYSQL_DATE_FORMAT = "Y-m-d H:i:s";

    /** @var string Directory name of app folder */
    public static $APP_DIR_NAME = 'app';

    /** @var string Storage directory relative to app dir */
    public static $RELATIVE_STORAGE_DIR = '/storage';

    /** @var string Cache directory relative to app dir */
    public static $RELATIVE_CACHE_DIR = '/storage/cache';

    /** @var string Views directory relative to app dir */
    public static $RELATIVE_VIEWS_DIR = '/views';

    /** @var string The capability that will be able to see the plugin's settings */
    public static $ALLOWED_USER_CAPABILITY = 'manage_options';

    /**
     * Get the app directory of the plugin relative to WordPress root
     * @return string The relative path without a trailing slash
     */
    public static function appDir() {
        if(!static::$APP_DIR) {
            static::$APP_DIR = DIRECTORY_SEPARATOR . str_replace(str_replace("/", DIRECTORY_SEPARATOR, trailingslashit(ABSPATH)), '', WP_CONTENT_CRAWLER_PATH) . static::$APP_DIR_NAME;
        }

        return static::$APP_DIR;
    }

    /**
     * Get admin directory name. Although it is hard-coded in a number of places in WP's core, this is the safest way
     * to get the admin directory name.
     *
     * @return string
     */
    public static function adminDirName() {
        if(!static::$ADMIN_DIR_NAME) {
            // Get admin directory name from blog url
            $adminDirName = str_replace(get_bloginfo('url'), '', admin_url());
            $adminDirName = str_replace(DIRECTORY_SEPARATOR, '', $adminDirName);
            static::$ADMIN_DIR_NAME = $adminDirName;
        }

        return static::$ADMIN_DIR_NAME;
    }
}