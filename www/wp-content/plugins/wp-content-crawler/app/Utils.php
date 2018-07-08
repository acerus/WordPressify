<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 15:45
 */

namespace WPCCrawler;


use Illuminate\Support\Arr;
use Philo\Blade\Blade;
use Symfony\Component\DomCrawler\Crawler;

class Utils {

    /**
     * @var \Philo\Blade\Blade
     */
    private static $BLADE;

    /**
     * Saves or updates a post meta for a post. <b>Note that</b> the meta key will be prefixed with an underscore if
     * it does not start with it. The meta keys starting with an underscore will be hidden on post edit/create page.
     * Hence, the meta keys can be shown with custom meta boxes.
     *
     * @param int $postId
     * @param mixed $metaKey
     * @param mixed $metaValue
     * @param bool $unique
     * @return bool|false|int
     */
    public static function savePostMeta($postId, $metaKey, $metaValue, $unique = true) {
        if(!starts_with($metaKey, '_')) $metaKey = '_' . $metaKey;

        if($unique) {
            return update_post_meta($postId, $metaKey, $metaValue);
        } else {
            return add_post_meta($postId, $metaKey, $metaValue, false);
        }
    }

    /**
     * Extracts the value of a meta key from meta array. If the value for the specified key is serialized, it will be
     * unserialized.
     *
     * @param array $postMeta An array of post meta acquired by get_post_meta() function.
     * @param string $key The key of the meta whose value is wanted
     * @return null|string|array
     */
    public static function getPostMetaValue($postMeta, $key) {
        if(isset($postMeta[$key])) {
            $val = $postMeta[$key][0];
            if(is_serialized($val)) {
                return unserialize($val);
            }

            return $val;
        }

        return null;
    }

    /**
     * Checks a parameter if it should be unserialized, and if so, does so. If the parameter has serialized values inside,
     * those will be unserialized as well. Hence, at the end, there will be no serialized strings inside the value.
     *
     * @param mixed $metaValue  The value to be unserialized
     * @return mixed            Unserialized value
     */
    public static function getUnserialized($metaValue) {
        $val = (!empty($metaValue) && isset($metaValue[0])) ? $metaValue[0] : $metaValue;
        return is_serialized($val) ? static::getUnserialized(unserialize($val)) : $metaValue;
    }

    /**
     * Prepares a valid URL from given parameters.
     *
     * @param string      $baseUrl
     * @param string      $urlPartToAppend
     * @param null|string $currentUrl Current page's URL. If this is null, $baseUrl will be used instead.
     * @return null|string A valid URL created from the givens
     */
    public static function prepareUrl($baseUrl, $urlPartToAppend, $currentUrl = null) {
        // If the URL starts with double slashes ("//"), prepend "http:" and return.
        if(substr($urlPartToAppend, 0, 2) == '//') {
            return "http:" . $urlPartToAppend;
        }

        // Remove the trailing slash from the base url
        $baseUrl = rtrim($baseUrl, "/");

        // If the url does not start with http, add main site url in front of it
        if(!starts_with($urlPartToAppend, "http")) {
            // If URL part starts with "www", just add "http://" in front of it and return.
            if(starts_with($urlPartToAppend, "www")) return "http://" . $urlPartToAppend;

            // Remove the first leading slash from the url, if exists.
            if(starts_with($urlPartToAppend, "/")) {
                $urlPartToAppend = substr($urlPartToAppend, 1);

            // If not, prepend current URL.
            } else {
                // The URL part is like "other/page.html". Let's say the current URL is "http://site.com/my/page". In
                // this case, browsers consider "other/page.html" link as "http://site.com/my/other/page.html". Here,
                // we are handling this situation.

                $currentUrl = $currentUrl ? $currentUrl : $baseUrl;

                // If the current URL does not end with a forward slash, we need to get the base resource URL.
                if(!ends_with($currentUrl, "/")) {
                    // Remove the last part from the URL when the URL has more than one resource.
                    // First, remove the part until ://. Then, explode it from forward slashes.
                    $parts = explode("/", preg_replace("%^[^:]+://%", "", $currentUrl));
                    if (sizeof($parts) > 1) {
                        $currentUrl = pathinfo($currentUrl, PATHINFO_DIRNAME);
                    }

                } else {
                    // When the URL ends with a forward slash, it means the URL currently points to the URL relative to
                    // this url part. E.g. When the URL is "http://abc.com/test/page/", and url part to append is
                    // "page.html", it means the intended URL is "http://abc.com/test/page/page.html". So, nothing to
                    // do here.
                }

                return rtrim($currentUrl, "/") . "/" . $urlPartToAppend;
            }

            // Prepare the full url and return it.
            return $baseUrl . "/" . $urlPartToAppend;
        }

        return $urlPartToAppend;
    }

    /**
     * Create a blade a view that can be rendered.
     * @param string $viewName
     * @return \Illuminate\Contracts\View\View
     */
    public static function view($viewName) {
        if (!static::$BLADE) {
            $views = __DIR__ . Constants::$RELATIVE_VIEWS_DIR;
            $cache = __DIR__ . Constants::$RELATIVE_CACHE_DIR;

            static::$BLADE = new Blade($views, $cache);
        }

        return static::$BLADE->view()->make($viewName);
    }

    /**
     * Sorts a multidimensional array according to the specified keys. Example usage:
     * <p><p>
     * $dataArray = [ ["start" => 2, "end" => 18], ["start" => 3, "end" => 5], ["start" => 19, "end" => 2] ]
     * <p>
     * array_msort($dataArray, ['start' => SORT_ASC])
     * <p><p> Above example will sort $dataArray ascending by 'start'
     *
     * @param array $array
     * @param array $cols
     * @return array
     */
    public static function array_msort($array, $cols) {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }

    /**
     * Get value from an array
     *
     * @param array      $array   The array
     * @param string     $key     Target key
     * @param null|mixed $default Default value
     * @return mixed
     */
    public static function array_get($array, $key, $default = null) {
        return Arr::get($array, $key, $default);
    }

    /**
     * Gets the HTML of the specified element with its own tag
     * @param Crawler $node
     * @return string HTML of the element
     */
    public static function getNodeHTML($node) {
        if(!$node || !$node->getNode(0)) return '';
        return $node->getNode(0)->ownerDocument->saveHTML($node->getNode(0));
    }

    /**
     * Combines 2 or more arrays into one.
     *
     * @param array $mainArray
     * @param null|array $array1
     * @param null|array $array2
     * @param null|array $array3
     * @return array
     */
    public static function combineArrays($mainArray, $array1 = null, $array2 = null, $array3 = null) {
        if($array1 && !empty($array1)) $mainArray = array_merge($mainArray, $array1);
        if($array2 && !empty($array2)) $mainArray = array_merge($mainArray, $array2);
        if($array3 && !empty($array3)) $mainArray = array_merge($mainArray, $array3);
        return $mainArray;
    }

    /**
     * @param string $date A date string
     * @return string Date string formatted according to WordPress settings
     */
    public static function getDateFormatted($date) {
//        return $date ? date_format(date_create($date), get_option('time_format') . " " . get_option('date_format')) : '-';

        if(!is_numeric($date)) $date = strtotime($date);
        return $date ? date_i18n(get_option('time_format'), $date) . " " . date_i18n(get_option('date_format'), $date) : '-';
    }

    /**
     * Get difference for humans between two timestamps.
     *
     * @param string      $from Timestamp
     * @param string|null $to Timestamp. If null, current time will be used.
     * @return string Difference for humans
     */
    public static function getDiffForHumans($from, $to = null) {
        if(!$from) return '-';

        if(!$to) $to = current_time('timestamp');

        return human_time_diff($from, $to);
    }

    /**
     * Get plugin file path. The path can be safely used for registration of activation/deactivation hooks.
     *
     * @return string
     */
    public static function getPluginFilePath() {
        return WP_CONTENT_CRAWLER_PATH . Constants::$PLUGIN_FILE_NAME . '.php';
//        return sprintf(ABSPATH . 'wp-content/plugins/%1$s/%1$s.php', Constants::$PLUGIN_FILE_NAME);
    }

    /**
     * Strips slashes of non-array values of the array.
     *
     * @param array $array The array whose string values' slashes will be stripped
     * @return array The array with slashes of its string values are stripped
     */
    public static function arrayStripSlashes($array) {
        $mArray = [];
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $mArray[$key] = static::arrayStripSlashes($value);
            } else {
                $mArray[$key] = stripslashes($value);
            }
        }

        return $mArray;
    }

    /**
     * Get value of an option unescaped. Value of the option is escaped before it is saved to the database. Hence,
     * you need to get the value unescaped. This function unescapes the escaped characters.
     *
     * @param string $key Option key
     * @return array|string Unescaped value
     */
    public static function getOptionUnescaped($key) {
        $value = get_option($key);
        return is_array($value) ? static::arrayStripSlashes($value) : stripslashes($value);
    }

    /**
     * Removes one backslash from repeating backslashes in a string. E.g. "\\\\ \\\ \\ \" will be "\\\ \\ \ "
     *
     * @param string $original
     * @return mixed
     */
    public static function removeOneBackslashFromRepeatingBackslashes($original) {
        preg_match_all("/\\\{1,}/", $original, $matches, PREG_OFFSET_CAPTURE);

        $decreaseOffset = 0;
        foreach($matches[0] as $m) {
            /** @var $m array 0 => string, 1 => offset */
            $mOffset = (int) $m[1] - $decreaseOffset;

            $original = substr_replace($original, substr($m[0], 0, strlen($m[0]) - 1), $mOffset, strlen($m[0]));
            $decreaseOffset += 1;
        }

        return $original;
    }

    /**
     * Slashes the values of an array using wp_slash function.
     *
     * @param $array array The array whose values will be slashed using wp_slash function
     * @return array
     */
    public static function arrayDeepSlash($array) {
        $result = [];
        foreach($array as $k => $v) {
            if(is_array($v)) {
                $result[$k] = static::arrayDeepSlash($v);
            } else {
                $result[$k] = wp_slash($v);
            }
        }

        return $result;
    }

    /**
     * Get categories as an array.
     * @return array Structure: [categoryId => categoryName]
     */
    public static function getCategories() {
        $wpCategories = get_categories(['hide_empty' => false]);

        // Get WooCommerce categories
        $wooCommerceCategories = get_categories([
            'taxonomy'      => 'product_cat',
            'orderby'       => 'name',
            'hierarchical'  => 0,
            'hide_empty'    =>  false,
        ]);

        $categories = [];
        foreach($wpCategories as $category) {
            $categories[$category->cat_ID] = $category->name . " (" . $category->cat_ID . ")";
        }

        if(!isset($wooCommerceCategories["errors"])) {
            foreach ($wooCommerceCategories as $category) {
                $categories[$category->cat_ID] = $category->name . " (" . $category->cat_ID . ") (WooCommerce)";
            }
        }

        /**
         * Modify the categories. The categories are shown, e.g., in Category Map setting.
         *
         * @param array $categories Categories. Structured as [category_id => "Category Name", category_id2 => "Category Name 2", ...]
         *
         * @return array Modified categories.
         * @since 1.6.3
         */
        $categories = apply_filters('wpcc/categories', $categories);

        return $categories;
    }

    /**
     * Get a value from an array
     *
     * @param array $array      The array
     * @param string $key       The key whose value is wanted
     * @param mixed $default    Default value if the value of the key is not valid
     * @return mixed            Value of the key or the default value
     */
    public static function getValueFromArray($array, $key, $default = false) {
        return isset($array[$key]) && $array[$key] ? $array[$key] : $default;
    }

    /**
     * Check if the user wants to change the password for the posts. If not, remove the password field.
     *
     * @param array       $data        Input data from user, such as $_POST
     * @param array       $keys        Available setting (post meta) keys
     * @param null|string $oldPassword Old password to check against. If null, general option will be used to get an
     *                                 old password.
     * @return bool True if valid, false otherwise
     */
    public static function validatePasswordInput(&$data, &$keys, $oldPassword = null) {
        $success = true;
        $message = '';
        if(!isset($data['_wpcc_change_password'])) {

            unset($data['_wpcc_post_password']);
            unset($keys[array_search('_wpcc_post_password', $keys)]);

        } else {
            // Check if the old pw is correct
            $oldPassword = $oldPassword === null ? get_option('_wpcc_post_password') : $oldPassword;
            if($oldPassword !== $data["_wpcc_post_password_old"]) {
                // Old password is not correct. Remove the password from data and keys, and set success as false.
                unset($data['_wpcc_post_password']);
                unset($keys[array_search('_wpcc_post_password', $keys)]);

                $success = false;
                $message = 'Old password is not correct.';
            } else {
                // Check if passwords match
                if($data["_wpcc_post_password"] !== $data["_wpcc_post_password_validation"]) {
                    $success = false;
                    $message = _wpcc('Passwords do not match.');
                }
            }

        }

        return [
            'success'   =>  $success,
            'message'   =>  $message
        ];
    }

    /**
     * Inserts the file as media for a given post
     *
     * @param int $postId The post ID to which the media will be attached
     * @param string $filePath Should be the path to a file in the upload directory.
     * @param null|string $title Title for the file. If empty, the title assigned automatically from path.
     * @param null|string $altText Alternative text for the file.
     * @return int ID of the inserted media (attachment)
     */
    public static function insertMedia($postId, $filePath, $title = null, $altText = null) {
        // Built on the example at: https://codex.wordpress.org/Function_Reference/wp_insert_attachment

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $fileType = wp_check_filetype(basename($filePath), null);

        // Get the path to the upload directory.
        $wpUploadDir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'              => $wpUploadDir['url'] . '/' . basename($filePath),
            'post_mime_type'    => $fileType['type'],
            'post_title'        => $title ? $title : preg_replace('/\.[^.]+$/', '', basename($filePath)),
            'post_content'      => '',
            'post_status'       => 'inherit'
        );

        // Insert the attachment.
        $attachmentId = wp_insert_attachment($attachment, $filePath, $postId);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(trailingslashit(ABSPATH) . Constants::adminDirName() . '/includes/image.php');

        // Generate the metadata for the attachment, and update the database record.
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $filePath);

        if($altText) $attachmentData['_wp_attachment_image_alt'] = $altText;

        wp_update_attachment_metadata($attachmentId, $attachmentData);
        return $attachmentId;
    }

    /**
     * Saves the given URL in uploads folder and returns full URL of the uploaded file.
     *
     * @param string $fileUrl Full URL of the file to be downloaded
     * @param int $timeoutSeconds
     * @return array|null An array with keys <b>'url'</b> (full URL for the file), <b>'file'</b> (absolute path of the
     *          file) and <b>'type'</b> (type of the file), or null
     */
    public static function saveMedia($fileUrl, $timeoutSeconds = 10) {
        // Built on the example at: https://codex.wordpress.org/Function_Reference/wp_handle_sideload
        // Gives us access to the download_url() and wp_handle_sideload() functions
        require_once(trailingslashit(ABSPATH) . Constants::adminDirName() . '/includes/file.php');

        // Download file to temp dir
        $tempFile = download_url($fileUrl, $timeoutSeconds);

        if (!is_wp_error($tempFile)) {
            $ext = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));

            // Strip parameters and get the basename
            $fileName = basename(preg_replace('/\?.*/', '', $fileUrl));

            // Remove the extension
            $fileName = explode(".", $fileName)[0];

            // If there is no extension, get the mime-type and extract the extension from it.
            if(!$ext || mb_strpos($ext, "?") !== false) {
                $ch = curl_init($fileUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);

                $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

                if(mb_strpos($type, "/") !== false) {
                    $ext = explode('/', $type)[1];

                    // Match the extension until ; or \s.
                    preg_match('/([^;\s]+)/', $ext, $matches);

                    // Set the new extension
                    if($matches && isset($matches[1])) {
                        $ext = $matches[1];
                    }
                }
            }

            // Array based on $_FILE as seen in PHP file uploads
            $file = [
                'name'      => "{$fileName}.{$ext}", // ex: wp-header-logo.png
                'ext'       => $ext,
                'tmp_name'  => $tempFile,
                'error'     => 0,
                'size'      => @filesize($tempFile),
            ];

            $overrides = [
                // Tells WordPress to not look for the POST form fields that would normally be present, default is true,
                // we downloaded the file from a remote server, so there will be no form fields
                'test_form'     => false,

                // Setting this to false lets WordPress allow empty files, not recommended
                'test_size'     => true,

                // A properly uploaded file will pass this test. There should be no reason to override this one.
                'test_upload'   => true,

                'test_type'     => false,
            ];

            // Move the temporary file into the uploads directory
            $results = wp_handle_sideload($file, $overrides);

            if (empty($results['error'])) {
//                $localUrl   = $results['url'];  // URL to the file in the uploads dir
//                $filePath   = $results['file']; // Full path to the file
//                $type       = $results['type']; // MIME type of the file

                return $results;
            } else {
//                var_dump("Error: wp_handle_sideload");
            }
        } else {
//            var_dump("Error: download_url");
        }

        return null;
    }

    /**
     * Delete a file
     *
     * @param string $filePath
     */
    public static function deleteFile($filePath) {
        if(!$filePath) return;

        wp_delete_file($filePath);
    }

    /**
     * Delete a post's thumbnail and the attachment.
     *
     * @param int $postId ID of the post whose thumbnail should be deleted
     */
    public static function deletePostThumbnail($postId) {
        // Get the ID of the thumbnail attachment
        $alreadyExistingThumbId = get_post_thumbnail_id($postId);

        // Delete the thumbnail from the post
        delete_post_thumbnail($postId);

        // Delete the attachment
        if($alreadyExistingThumbId) wp_delete_attachment($alreadyExistingThumbId);
    }

    /**
     * Convert encoding of a string
     *
     * @param string $string            The string whose encoding will be converted
     * @param string $targetEncoding    Target encoding
     * @return mixed|string             Resultant string
     */
    public static function convertEncoding($string, $targetEncoding = 'UTF-8') {
        return mb_convert_encoding($string, $targetEncoding, mb_detect_encoding($string, 'UTF-8, ISO-8859-1', true));
    }
}