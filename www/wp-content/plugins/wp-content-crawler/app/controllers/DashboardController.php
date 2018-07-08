<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 05/02/17
 * Time: 14:33
 */

namespace WPCCrawler\controllers;


use WPCCrawler\Factory;
use WPCCrawler\objects\Dashboard;
use WPCCrawler\objects\page\AbstractMenuPage;
use WPCCrawler\Utils;

class DashboardController extends AbstractMenuPage {

    /**
     * @var array Structured as ['option name' => 'default value']
     */
    public $settings = [
        '_wpcc_dashboard_count_last_recrawled_posts'    => 10,       // int  Number of items that should be shown in last recrawled posts table
        '_wpcc_dashboard_count_last_crawled_posts'      => 10,       // int  Number of items that should be shown in last crawled posts table
        '_wpcc_dashboard_count_last_urls'               => 10,       // int  Number of items that should be shown in last added URLs table
        '_wpcc_dashboard_count_last_deleted_urls'       => 10,       // int  Number of items that should be shown in last deleted URLs table
    ];

    /**
     * @return string Menu title for the page
     */
    public function getMenuTitle() {
        return _wpcc("Dashboard");
    }

    /**
     * @return string Page title
     */
    public function getPageTitle() {
        return _wpcc("Dashboard");
    }

    /**
     * Get view for the page.
     *
     * @return mixed Not-rendered blade view for the page
     */
    public function getView() {
        // Add assets
        Factory::assetManager()->addPostSettings();
        Factory::assetManager()->addBootstrapGrid();
        Factory::assetManager()->addDashboard();

        // Create a new Dashboard so that we can get the statistics from the view.
        $dashboard = new Dashboard();

        // Prepare the settings
        $settings = [];
        foreach($this->settings as $key => $default) $settings[$key] = get_option($key, $default);

        // This is important to appropriately assign the values of the already-saved options to the form elements.
        $isOption = true;

        // Attach page action key to the view, as well. This is important because this method is utilized to send
        // AJAX responses. Normally, all menu pages include this variable by default. However, they should be loaded
        // completely. When this is utilized for AJAX responses, $pageActionKey is not included automatically. So,
        // we need to do it here manually.
        $pageActionKey = $this->getPageActionKey();

        return Utils::view('dashboard/main')->with(compact('dashboard', 'settings', 'isOption', 'pageActionKey'));
    }

    public function handlePOST() {
        parent::handlePOST();

        $data = $_POST;
        $keys = array_keys($this->settings);

        // Save options
        foreach($data as $key => $value) {
            if(in_array($key, $keys)) {
                update_option($key, $value, false);

                // Remove the key, since it is saved.
                unset($keys[array_search($key, $keys)]);
            }
        }

        // Redirect back
        $this->redirectBack(true);
    }

    public function handleAJAX() {
        $data = parent::handleAJAX();

        $cmd = Utils::array_get($data, "cmd");
        if(!$cmd) return;

        switch($cmd) {
            case "refresh_dashboard":
                echo json_encode([
                    'view' => $this->getView()->render()
                ]);

                break;

            case "refresh_section":
                $value = (int) Utils::array_get($data, "value");
                $optionKey = Utils::array_get($data, "optionKey");

                if($optionKey && array_key_exists($optionKey, $this->settings) && $value > 0) {
                    update_option($optionKey, $value, false);
                }

                echo json_encode([
                    'view' => $this->getView()->render()
                ]);

                break;
        }
    }


    /**
     * @return string Slug for the page
     */
    public function getPageSlug() {
        return 'dashboard';
    }
}