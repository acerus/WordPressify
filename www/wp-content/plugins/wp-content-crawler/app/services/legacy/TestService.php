<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 29/03/16
 * Time: 19:45
 */

namespace WPCCrawler\services;

use Illuminate\View\View;
use WPCCrawler\Constants;
use WPCCrawler\Factory;
use WPCCrawler\Utils;

class TestService {

    public function __construct() {
        $wptslm = Factory::wptslmClient();
        if($wptslm->isUserCool()) {
            // Create sub menu page
            add_action('admin_menu', function () {
                add_submenu_page(
                    'edit.php?post_type=' . Constants::$POST_TYPE,
                    _wpcc('Tester'),
                    _wpcc('Tester'),
                    Constants::$ALLOWED_USER_CAPABILITY,
                    Constants::$POST_TYPE . '_tester',
                    function () {
                        echo $this->getTestPage();
                    }
                );
            }, 1);

            // Add styles and scripts for test page
            add_action('admin_enqueue_scripts', function ($hook) {
                // Check if we are on the custom post page.
                $valid = $hook == sprintf('%1$s_page_%1$s_tester', Constants::$POST_TYPE);
                if (!$valid) return;

                Factory::assetManager()->addTooltip();
                Factory::assetManager()->addSiteTester();
            });

            // Listen AJAX requests and respond;
            add_action('wp_ajax_wcc_site_tester', function () {
                if (!check_ajax_referer('wcc-site-tester', Constants::$NONCE_NAME)) wp_die();
                if(!current_user_can(Constants::$ALLOWED_USER_CAPABILITY)) wp_die("You are not allowed for this.");

                if (!isset($_POST["data"])) wp_die(_wpcc("Data does not exist in your request. The request should include 'data'"));

                $data = $_POST["data"];

                // Show the test results
                echo Factory::test()->conductGeneralTest($data["site_id"], $data["test_type"], $data["test_url_part"]);
                wp_die();
            });
        }
    }

    public function getTestPage() {
        // Get available sites
        $sites = get_posts(['post_type' => Constants::$POST_TYPE, 'numberposts' => -1]);

        return Utils::view('site-tester/site-tester')->with([
            'sites' => $sites,
        ])->render();
    }

}