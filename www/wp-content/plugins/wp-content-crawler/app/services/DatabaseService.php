<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/04/16
 * Time: 22:19
 */

namespace WPCCrawler\services;

use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\Utils;

class DatabaseService {

    /** @var string Holds the version of the database */
    private $dbVersion = "3.0";

    /** @var string Holds the option name used to store db version */
    private $dbVersionOptionName = '_wpcc_db_version';

    /** @var string Name for the URLs table */
    private $dbTableUrls = 'wpcc_urls';

    public function __construct() {
        // Make sure the DB is up-to-date. This is important, because when the plugin is updated automatically from
        // the admin panel, activation hook is not called. So, we make sure that the DB is up-to-date everytime
        // this class is constructed.
        if(version_compare($this->getDbVersion(), $this->dbVersion, '<')) $this->createDbTables();

        // Create database tables when the plugin is activated
        register_activation_hook(Utils::getPluginFilePath(), function($network_wide) {
            // Check if this is a multi-site environment
            if (is_multisite() && $network_wide) {
                // Create database tables for each blog
                global $wpdb;

                // store the current blog id
                $currentBlog = $wpdb->blogid;

                // Get all blogs in the network and activate plugin on each one
                $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

                foreach ($blogIds as $blogId) {
                    switch_to_blog($blogId);

                    Factory::databaseService()->createDbTables();

                    restore_current_blog();
                }

            // If this is a single blog, just create the tables.
            } else {
                Factory::databaseService()->createDbTables();
            }

        });

        // Run the activation functions/methods for the plugin when a new blog is created on multi site environment
        add_action('wpmu_new_blog', function($blogId, $userId, $domain, $path, $siteId, $meta) {

            // Do this only if the plugin is already activated
            if(is_plugin_active_for_network(sprintf('%1$s' . DIRECTORY_SEPARATOR . '%1$s.php', Constants::$PLUGIN_FILE_NAME))) {
                switch_to_blog($blogId);

                Factory::databaseService()->createDbTables();

                restore_current_blog();
            }
        }, 10, 6);
    }

    /**
     * Get a URL from the database table, as object
     *
     * @param int $urlId
     * @return null|object
     */
    public function getUrlById($urlId) {
        if(!$urlId) return null;

        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $this->getDbTableUrlsName() . " WHERE id = %d", $urlId));

        if(!empty($results)) return $results[0];
        return null;
    }

    /**
     * Get a URL from the database table by saved post ID, as object
     *
     * @param int $savedPostId
     * @return null|object
     */
    public function getUrlByPostId($savedPostId) {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $this->getDbTableUrlsName() . " WHERE saved_post_id = %d", $savedPostId));

        if(!empty($results)) return $results[0];
        return null;
    }

    /**
     * Get a URL from the database table by site ID and URL
     *
     * @param int $siteId
     * @param string $url
     * @return null|object
     */
    public function getUrlBySiteIdAndUrl($siteId, $url) {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $this->getDbTableUrlsName() . " WHERE post_id = %d
            AND (url = %s OR url = %s) LIMIT 1", $siteId, trailingslashit($url), rtrim($url, "/")));

        if(!empty($results)) return $results[0];
        return null;
    }

    /**
     * Add a URL to the urls table
     *
     * @param int $postId ID of the custom post type (site)
     * @param string $url The URL
     * @param string $thumbnailUrl Thumbnail URL for the post
     * @param int $categoryId Category ID in which the URL will be added when crawled
     * @param bool $isSaved
     * @return int ID of the inserted row
     */
    public function addUrl($postId, $url, $thumbnailUrl, $categoryId, $isSaved = false) {
        global $wpdb;

        // Check if this URL is already added
        $findQuery = "SELECT id FROM " . $this->getDbTableUrlsName() . " WHERE post_id = %d AND (url = %s OR url = %s)";
        $count = $wpdb->query($wpdb->prepare($findQuery, [$postId, trailingslashit($url), rtrim($url, "/")]));

        // If the URL is added, do not insert it again.
        if($count && $count > 0) return false;

        $wpdb->insert(
            $this->getDbTableUrlsName(),
            [
                'post_id'       =>  $postId,
                'url'           =>  $url,
                'thumbnail_url' =>  $thumbnailUrl,
                'category_id'   =>  $categoryId,
                'is_saved'      =>  $isSaved,
                'created_at'    =>  current_time('mysql'),
                'updated_at'    =>  current_time('mysql'),
            ]
        );

        return $wpdb->insert_id;
    }

    /**
     * Delete a URL from the URLs table
     *
     * @param int $urlId ID of the URL
     * @return false|int False on fail, 1 on success
     */
    public function deleteUrl($urlId) {
        global $wpdb;

        return $wpdb->delete(
            $this->getDbTableUrlsName(),
            [
                'id'    =>  $urlId
            ]
        );
    }

    /**
     * Update a URL's saved status
     *
     * @param int      $urlId       ID of the URL
     * @param bool     $saved       Whether the URL is saved as a post or not.
     * @param int|null $savedPostId ID of the saved post
     * @param int      $updateCount Number of times the post of this URL is updated
     * @param bool     $isLocked    Whether this tuple is locked or not.
     * @return false|int
     */
    public function updateUrlSavedStatus($urlId, $saved, $savedPostId = null, $updateCount = 0, $isLocked = false) {
        global $wpdb;

        return $wpdb->update(
            $this->getDbTableUrlsName(),
            [
                'is_saved'      =>  $saved,
                'saved_post_id' =>  $savedPostId,
                'updated_at'    =>  current_time('mysql'),
                'update_count'  =>  $updateCount,
                'is_locked'     =>  $isLocked
            ],
            [
                'id'        =>  $urlId
            ]
        );
    }

    /**
     * Update a URL's saved post ID
     *
     * @param int      $urlId       ID of the URL
     * @param int|null $savedPostId ID of the saved post
     * @return false|int
     */
    public function updateUrlSavedPostId($urlId, $savedPostId) {
        global $wpdb;

        return $wpdb->update(
            $this->getDbTableUrlsName(),
            [
                'saved_post_id' =>  $savedPostId,
                'updated_at'    =>  current_time('mysql'),
            ],
            [
                'id'        =>  $urlId
            ]
        );
    }

    /**
     * Update a URL's post saved at (saved_at) value as current time or null if $savedPostId is not valid
     *
     * @param int         $urlId       ID of the URL
     * @param int|null    $savedPostId ID of the saved post
     * @param string|null $savedAt     A date string formatted in MySQL date format. If null or empty, current time
     *                                 will be used.
     * @return false|int
     */
    public function updateUrlPostSavedAt($urlId, $savedPostId, $savedAt = null) {
        global $wpdb;

        return $wpdb->update(
            $this->getDbTableUrlsName(),
            [
                'saved_post_id' =>  $savedPostId,
                'saved_at'      =>  $savedPostId ? ($savedAt ? $savedAt : current_time('mysql')) : null,
            ],
            [
                'id'        =>  $urlId
            ]
        );
    }

    /**
     * Update a URL's post saved at (saved_at) value
     *
     * @param int|null    $savedPostId ID of the saved post
     * @param string|null $savedAt     A date string formatted in MySQL date format. If null or empty, current time
     *                                 will be used.
     * @return false|int
     */
    public function updateUrlPostSavedAtByPostId($savedPostId, $savedAt = null) {
        global $wpdb;

        return $wpdb->update(
            $this->getDbTableUrlsName(),
            [
                'saved_at' => $savedPostId ? ($savedAt ? $savedAt : current_time('mysql')) : null,
            ],
            [
                'saved_post_id' => $savedPostId
            ]
        );
    }

    /**
     * Update a URL's recrawled status by making sure is_saved is true
     *
     * @param int  $urlId       ID of the URL
     * @param int  $updateCount How many times this URL's post was recrawled
     * @param bool $isLocked    Whether this tuple is locked or not.
     * @return false|int
     */
    public function updateUrlRecrawledStatus($urlId, $updateCount, $isLocked = false) {
        global $wpdb;

        return $wpdb->update(
            $this->getDbTableUrlsName(),
            [
                'update_count'  =>  $updateCount,
                'updated_at'    =>  current_time('mysql'),
                'is_locked'     =>  $isLocked,
                'recrawled_at'  =>  current_time('mysql'),
                'is_saved'      =>  true,
            ],
            [
                'id'        =>  $urlId
            ]
        );
    }

    /**
     * Delete all URLs which belong to a specific site
     *
     * @param int $siteId ID of the site
     * @return false|int
     */
    public function deleteUrlsBySiteId($siteId) {
        global $wpdb;
        return $wpdb->delete($this->getDbTableUrlsName(), ['post_id' => $siteId], ['%d']);
    }

    /**
     * Delete all URLs which belong to a specific site by saved status
     *
     * @param int $siteId ID of the site
     * @param bool $isSaved True if you want to delete saved URLs, false otherwise
     * @return false|int
     */
    public function deleteUrlsBySiteIdAndSavedStatus($siteId, $isSaved) {
        global $wpdb;
        return $wpdb->delete($this->getDbTableUrlsName(), ['post_id' => $siteId, 'is_saved' => $isSaved ? 1 : 0], ['%d', '%d']);
    }

    /**
     * Make is_locked columns false for all URLs in the database.
     *
     * @return false|int
     */
    public function unlockAllUrls() {
        global $wpdb;
        $query = "UPDATE {$this->getDbTableUrlsName()} SET is_locked = FALSE WHERE is_locked = TRUE";
        return $wpdb->query($query);
    }

    /**
     * Sets delete_at column's value to now and sets saved_post_id to null for the URL found by the supplied post ID.
     * Also removes the lock on the URL.
     *
     * @param int|int[] $postId             ID of the post with whose URL will be set deleted. You can also supply an
     *                                      array of post IDs.
     * @return false|int
     */
    public function setUrlDeleted($postId) {
        if(!$postId) return 0;

        // Prepare
        $preparedIdsStr = '';

        if(is_array($postId)) {
            $preparedIds = [];
            foreach($postId as $id) $preparedIds[] = esc_sql($id);

            // Remove empty values.
            $preparedIds = array_filter($preparedIds);
            if(!$preparedIds) return 0;

            // Get comma-separated string. We'll use this in query's IN clause.
            $preparedIdsStr = implode(", ", $preparedIds);

        } else {
            $preparedIdsStr = $postId;
        }

        // Make sure the string is not empty
        if(!$preparedIdsStr) return 0;

        global $wpdb;

        // Final query
        $query = "UPDATE {$this->getDbTableUrlsName()}
            SET saved_post_id = NULL, is_locked = FALSE, updated_at = %s, deleted_at = %s
            WHERE saved_post_id IN ({$preparedIdsStr})
        ";

        $now = current_time('mysql');
        return $wpdb->query($wpdb->prepare($query, $now, $now));
    }

    /**
     * Get old post IDs for a site.
     *
     * @param int $siteId             ID of the site whose posts should be found
     * @param int $olderThanInMinutes At least how many minutes old the post of a URL should be.
     *                                In other words, <b>now - olderThanInMin < postCreatedAt</b> will be searched for.
     * @param int $limit              How many posts at maximum can be returned
     * @return int[]
     */
    public function getOldPostIdsForSite($siteId, $olderThanInMinutes = 0, $limit = 0) {
        global $wpdb;

        // Prepare query parts
        $now = current_time('mysql');

        $olderThanInMinutes = (int) $olderThanInMinutes;
        $olderThanPart = $olderThanInMinutes && $olderThanInMinutes > 0 ?
            " AND saved_at < DATE_SUB('{$now}', INTERVAL {$olderThanInMinutes} MINUTE) " : "";

        $limit = (int) $limit;
        $limitPart = $limit && $limit > 0 ? " LIMIT {$limit} " : "";

        // Final query
        $query = "SELECT saved_post_id FROM {$this->getDbTableUrlsName()}
            WHERE saved_post_id IS NOT NULL
                AND post_id = %d
                AND is_locked = FALSE
                AND deleted_at IS NULL
            {$olderThanPart}
            ORDER BY saved_at ASC
            {$limitPart}
        ";

        $results = $wpdb->get_results($wpdb->prepare($query, $siteId));

        $ids = [];
        foreach($results as $res) $ids[] = $res->saved_post_id;

        return $ids;
    }

    /*
     *
     */

    public function getDbTableUrlsName() {
        global $wpdb;
        return $wpdb->prefix . $this->dbTableUrls;
    }

    public function getDbVersion() {
        return get_option($this->dbVersionOptionName, "0.0");
    }

    /**
     * Create the database tables required for the plugin
     */
    public function createDbTables() {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();

        $currentDbVersion = $this->getDbVersion();
        $tableUrls = $this->getDbTableUrlsName();

        /**
         * Query creating URLs table. This table will be used to store URLs to be crawled and already crawled. A way to
         * keep the track of the URLs. This way, we won't be adding a post URL more than once.
         *
         * IE11 allows max of 2083 chars for a URL. However, to be on the safe side, let's define URL field as 2560 chars.
         * @see http://stackoverflow.com/a/417184/2883487
         */
        $sql = "CREATE TABLE {$tableUrls} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            url varchar(2560) NOT NULL,
            thumbnail_url varchar(2560) DEFAULT NULL,
            category_id mediumint(9) NOT NULL,
            is_saved boolean NOT NULL DEFAULT FALSE,
            saved_post_id bigint(20) UNSIGNED,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charsetCollate;";

        require_once(str_replace("/", DIRECTORY_SEPARATOR, (trailingslashit(ABSPATH) . Constants::adminDirName() . '/includes/upgrade.php')));
        dbDelta($sql);

        /**
         * Updates for DB version 2.0
         */
        if(version_compare($currentDbVersion, '2.0', '<')) {
            // created_at and updated_at can now be null. This is important to successfully modify the columns of the URLs
            // table. Otherwise, MySQL will tell you that the values of created_at and updated_at are invalid, when you
            // try to alter the table.

            // Adds saved_at column to store the time the post is created
            // Adds recrawled_at column to store the last time the post is recrawled
            // Adds update_count column to store how many times a post is updated.
            // Adds is_locked column to store a status. E.g. the tuple will be locked when it is being crawled/recrawled etc.
            $wpdb->query("ALTER TABLE {$tableUrls}
                CHANGE created_at created_at DATETIME NULL DEFAULT NULL,
                CHANGE updated_at updated_at DATETIME NULL DEFAULT NULL,
                ADD COLUMN saved_at DATETIME NULL DEFAULT NULL AFTER updated_at,
                ADD COLUMN recrawled_at DATETIME NULL DEFAULT NULL AFTER saved_at,
                ADD COLUMN update_count INT UNSIGNED NOT NULL DEFAULT '0' AFTER saved_post_id,
                ADD COLUMN is_locked TINYINT NOT NULL DEFAULT '0' AFTER update_count;");

            // Initialize saved_at columns by getting post_date values from wp_posts table
            $wpdb->query("UPDATE {$tableUrls}
                INNER JOIN {$wpdb->posts} on {$tableUrls}.saved_post_id = {$wpdb->posts}.ID
                SET {$tableUrls}.saved_at = {$wpdb->posts}.post_date WHERE {$tableUrls}.saved_post_id IS NOT NULL;");
        }

        /**
         * Updates for DB version 3.0
         */
        if(version_compare($currentDbVersion, '3.0', '<')) {
            // Adds deleted_at column to store the time the post is deleted
            $wpdb->query("ALTER TABLE {$tableUrls}
                ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL AFTER recrawled_at;");
        }

        /**
         * Fires after all database tables are created and upgrades are made.
         *
         * @param string $currentDbVersion Current database version, such as '1.0', '2.4', etc. You can use
         *                                 version_compare function to compare the current version with another version,
         *                                 and decide if you need to make upgrades or create tables.
         * @since 1.6.3
         */
        do_action('wpcc/database/after_create', $currentDbVersion);

        // Store the version in the database to handle upgrades properly.
        update_option($this->dbVersionOptionName, $this->dbVersion);
    }
}