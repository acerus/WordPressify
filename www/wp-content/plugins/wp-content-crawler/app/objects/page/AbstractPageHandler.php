<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 07/06/16
 * Time: 15:13
 */

namespace WPCCrawler\objects\page;
use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\objects\Settings;
use WPCCrawler\objects\Test;
use WPCCrawler\Utils;

/**
 * An abstract class for handling page creation routines, such as handling POST and AJAX requests.
 * @package WPTSPostTools\objects
 */
abstract class AbstractPageHandler {

    public function __construct() {
        // Create pageActionKey JS variable, which can be used when making AJAX requests as action variable
        add_action('admin_print_scripts', function() {
            // Print the script only if we are on this page.
            if(isset($_GET["page"]) && $_GET["page"] == $this->getFullPageSlug()) {
                echo "
                    <script type='text/javascript'>
                        var pageActionKey = '" . $this->getPageActionKey() . "';
                    </script>
                ";
            }
        });

        // Listen to POST requests
        add_action('admin_post_' . $this->getPageActionKey(), function() {
            // Verify nonce
            $nonce = Utils::getValueFromArray($_POST, Constants::$NONCE_NAME, false);
            if(!$nonce || !wp_verify_nonce($nonce, $this->getPageActionKey())) {
                dd("Nonce is invalid.");
            }

            $this->handlePOST();
        });

        // Listen to AJAX requests
        add_action('wp_ajax_' . $this->getPageActionKey(), function() {
            if(!check_admin_referer($this->getPageActionKey(), Constants::$NONCE_NAME)) wp_die();

            $this->handleAJAX();

            wp_die();
        });
    }

    /**
     * Get the page action name. This will be used for catching AJAX and POST requests that are made for this page
     * @return string Page action name
     */
    public function getPageActionKey() {
        return Constants::$APP_SHORT_NAME . "_" . $this->getPageSlug();
    }

    /**
     * Get page slug with the app domain.
     * @return string
     */
    public function getFullPageSlug() {
        return Constants::$APP_DOMAIN . "-" . $this->getPageSlug();
    }

    /**
     * Get full page URL for this page. You can also set additional URL parameters.
     *
     * @param array $args URL parameters as key,value pairs
     * @return string Prepared full URL
     */
    public function getFullPageUrl($args = []) {
        $args = array_merge([
            'page'  =>  $this->getFullPageSlug(),
        ], $args);
        return untrailingslashit(get_site_url()) . $this->getBaseUrl() . "&" . http_build_query($args);
    }

    /**
     * @return string Slug for the page
     */
    public abstract function getPageSlug();

    /**
     * Handle POST requests
     * @return mixed
     */
    public function handlePOST() {

    }

    /**
     * Handle AJAX requests. <b>Required data should be sent via data key. This returns $_POST["data"].</b>
     *
     * @return array The data in the request
     */
    public function handleAJAX() {
        if(!isset($_POST["data"])) wp_die(_wpcc("Data does not exist in your request. The request should include 'data'"));

        // We'll return JSON response.
        header('Content-Type: application/json');

        return $_POST["data"];
    }

    /**
     * Respond to AJAX requests. This method handles common AJAX requests that can be made via settings pages.
     *
     * @param array $data The data sent via AJAX request
     * @return bool True if the request is processed, false otherwise.
     */
    protected function respondToAJAX($data) {
        // Handle if this is a testing request
        $result = Test::respondToTestRequest($data);
        if($result !== null) {
            echo $result;
            return true;
        }

        // Handle if this is a request sent from a settings page (i.e. general settings and site settings)
        $result = Settings::respondToAjaxRequest($data);
        if($result !== null) {
            echo $result;
            return true;
        }

        return false;
    }

    /*
     *
     */

    /**
     * Get base URL for the menu page item. This can be used to add a sub menu item under the parent menu item.
     *
     * @return string Parent page URL relative to the WordPress index page
     */
    public function getBaseUrl() {
        return '/wp-admin/edit.php?post_type=' . Constants::$POST_TYPE;
    }

    /**
     * @param bool $success         Whether the operation is succeeded or not
     * @param string $message       The message to be displayed to the user
     * @param array $queryParams    Additional query parameters that are appended to the redirect URL
     */
    public function redirectBack($success = true, $message = '', $queryParams = []) {
        $params = [];
        $params['success'] = $success ? 'true' : 'false';

        if($message) $params['message'] = urlencode($message);

//        $redirectParams = 'success=' . ($success ? 'true' : 'false') . ($message ? '&message=' . urlencode($message) : '');
//        wp_redirect(admin_url(sprintf('edit.php?post_type=%1$s&page=%1$s_general_settings&' . $redirectParams, Constants::$POST_TYPE)));

        wp_redirect($this->getFullPageUrl(array_unique(array_merge($params, $queryParams))));
        exit;
    }
}