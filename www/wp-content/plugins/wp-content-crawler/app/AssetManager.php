<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 13/04/16
 * Time: 23:13
 */

namespace WPCCrawler;


class AssetManager {

    private $scriptUtils            = 'wcc_utils_js';

    private $stylePostSettings      = 'wcc_post_settings_css';
    private $scriptPostSettings     = 'wcc_post_settings_js';

    private $scriptTooltip          = 'tooltipjs';

    private $scriptClipboard        = 'clipboardjs';

    private $scriptPostList         = 'wcc_post_list_js';

    private $styleGeneralSettings   = 'wcc_general_settings_css';

    private $styleSiteTester        = 'wcc_site_tester_css';
    private $scriptSiteTester       = 'wcc_site_tester_js';

    private $styleTools             = 'wcc_tools_css';
    private $scriptTools            = 'wcc_tools_js';

    private $styleDashboard         = 'wcc_dashboard_css';
    private $scriptDashboard        = 'wcc_dashboard_js';

    private $styleDevTools          = 'wcc_dev_tools_js';
    private $scriptDevTools         = 'wcc_dev_tools_js';

    private $styleFeatherlight      = 'featherlight_css';
    private $scriptFeatherlight     = 'featherlight_js';
    private $scriptOptimalSelect    = 'optimal_select_js';
    private $scriptJSDetectElementResize = 'js_detect_element_size_js';

    private $scriptNotifyJs         = 'notifyjs_js';

    private $styleBootstrapGrid     = 'bootstrap_grid_css';

    /** @var bool True if script localization was done at least once. If not, false. */
    private $isLocalized = false;

    /**
     * Add post-settings.css, post-settings.js and utils.js
     */
    public function addPostSettings() {
        Factory::assetManager()->addSortable();

        $this->addStyle($this->stylePostSettings, Constants::appDir() . '/public/styles/post-settings.css', false);

        $this->addUtils();
        $this->addNotifyJs();

        // These are required for properly use animate feature of jQuery.
        $this->addScript('jquery-ui-core');
        $this->addScript('jquery-color');

        $this->addScript($this->scriptPostSettings, Constants::appDir() . '/public/scripts/post-settings.js', ['jquery', $this->scriptUtils]);
    }

    /**
     * Add tooltip.js
     */
    public function addTooltip() {
        $this->addScript($this->scriptTooltip, Constants::appDir() . '/public/scripts/tooltip.min.js', ['jquery'], '3.3.6');
    }

    /**
     * Add clipboard.js
     */
    public function addClipboard() {
        $this->addScript($this->scriptClipboard, Constants::appDir() . '/public/scripts/clipboard.min.js', false, '1.5.9');
    }

    /**
     * Add post-list.js and utils.js
     */
    public function addPostList() {
        $this->addUtils();
        $this->addScript($this->scriptPostList, Constants::appDir() . '/public/scripts/post-list.js',
            ['jquery', $this->scriptUtils], false);
    }

    /**
     * Add general-settings.css
     */
    public function addGeneralSettings() {
        $this->addStyle($this->styleGeneralSettings, Constants::appDir() . '/public/styles/general-settings.css', false);
    }

    /**
     * Add site-tester.css, site-tester.js and utils.js
     */
    public function addSiteTester() {
        $this->addStyle($this->styleSiteTester, Constants::appDir() . '/public/styles/site-tester.css', false);
        $this->addUtils();

        $this->addScript($this->scriptSiteTester, Constants::appDir() . '/public/scripts/site-tester.js', ['jquery', $this->scriptUtils]);
    }

    /**
     * Add tools.css, tools.js and utils.js
     */
    public function addTools() {
        $this->addStyle($this->styleTools, Constants::appDir() . '/public/styles/tools.css', false);
        $this->addUtils();

        $this->addScript($this->scriptTools, Constants::appDir() . '/public/scripts/tools.js', ['jquery', $this->scriptUtils]);
    }

    /**
     * Add dashboard.css
     */
    public function addDashboard() {
        $this->addStyle($this->styleDashboard, Constants::appDir() . '/public/styles/dashboard.css', false);

        // These are required for properly use animate feature of jQuery.
        $this->addScript('jquery-ui-core');
        $this->addScript('jquery-color');

        $this->addScript($this->scriptDashboard, Constants::appDir() . '/public/scripts/dashboard.js', 'jquery');
    }

    /**
     * Add dev-tools.js and dev-tools.css
     */
    public function addDevTools() {
        $this->addStyle($this->styleDevTools, Constants::appDir() . '/public/styles/dev-tools.css', false);

        // Add the lightbox library after the dev-tools style so that we can override the styles of the library.
        // Also, the lib should be added before the dev-tools script so that we can refer to the lib's script.
        $this->addFeatherlight();

        $this->addScript($this->scriptOptimalSelect, Constants::appDir() . '/public/node_modules/optimal-select/dist/optimal-select.js');
        $this->addScript($this->scriptJSDetectElementResize, Constants::appDir() . '/public/bower_components/javascript-detect-element-resize/jquery.resize.js', ['jquery']);

        $devToolsDir = Constants::appDir() . '/public/scripts/dev-tools';
        $objectFileNames = ["AddressBar", "CSSSelectorToolbar", "DEVTools", "IFrameHandler", "OptionsToolbar", "SidebarHandler"];

        foreach($objectFileNames as $fileName) {
            $this->addScript($this->scriptDevTools . "-" . $fileName, "{$devToolsDir}/objects/{$fileName}.js", ['jquery']);
        }

        $this->addScript($this->scriptDevTools . "-dev-tools", "{$devToolsDir}/dev-tools.js", ['jquery']);

    }

    /**
     * Add featherlight.css and featherlight.js
     */
    public function addFeatherlight() {
        $this->addStyle($this->styleFeatherlight, Constants::appDir() . '/public/bower_components/featherlight/src/featherlight.css', false);
        $this->addScript($this->scriptFeatherlight, Constants::appDir() . '/public/bower_components/featherlight/src/featherlight.js', ['jquery']);
    }

    /**
     * Add utils.js
     */
    public function addUtils() {
        $this->addScript($this->scriptUtils, Constants::appDir() . '/public/scripts/utils.js', ['jquery']);
    }

    /**
     * Adds bootstrap-grid.css
     */
    public function addBootstrapGrid() {
        $this->addStyle($this->styleBootstrapGrid, Constants::appDir() . '/public/styles/bootstrap-grid.css', false);
    }

    /**
     * Adds WordPress' default jquery UI sortable library
     */
    public function addSortable() {
        $this->addScript('jquery-ui-sortable');
    }

    /**
     * Adds notify.js
     */
    public function addNotifyJs() {
        $this->addScript($this->scriptNotifyJs, Constants::appDir() . '/public/bower_components/notifyjs/dist/notify.js');
    }

    /*
     * PRIVATE HELPERS
     */

    /**
     * Register and enqueue a style.
     *
     * @param $handle
     * @param $src
     * @param array $deps
     * @param bool $ver
     * @param string $media
     */
    private function addStyle($handle, $src = null, $deps = [], $ver = false, $media = 'all') {
        $src = str_replace(DIRECTORY_SEPARATOR, "/", $src);
        if(!wp_style_is($handle, 'registered')) {
            if(!$src) return;
            if(!$ver) $ver = $this->getLastModifiedTime($src);
            wp_register_style($handle, $src, $deps, $ver, $media);
        }

        if(!wp_style_is($handle, 'enqueued')) {
            wp_enqueue_style($handle);
        }
    }

    /**
     * Register, enqueue and/or localize a script.
     *
     * @param $handle
     * @param $src
     * @param array $deps
     * @param bool $ver
     * @param array $localizeValues Key-value pairs of localized texts
     * @param string $localizeName Variable name under which the localized values will be available
     * @param bool $in_footer
     */
    private function addScript($handle, $src = null, $deps = [], $ver = false, $localizeValues = [], $localizeName = 'wpcc', $in_footer = false) {
        $src = str_replace(DIRECTORY_SEPARATOR, "/", $src);
        if(!wp_script_is($handle, 'registered')) {
            if(!$src) return;
            if(!$ver) $ver = $this->getLastModifiedTime($src);
            wp_register_script($handle, $src, $deps, $ver, $in_footer);
        }

        if(!wp_script_is($handle, 'enqueued')) {
            wp_enqueue_script($handle);

            // Add script localization if it was not added before. It is enough to do this once. No need to print the
            // same values to the page's source code for each script. Once is enough.
            if(!$this->isLocalized) {
                wp_localize_script($handle, 'wpcc', $this->getLocalizationValues());
                $this->isLocalized = true;
            }
        }
    }

    /**
     * Get last modified time of an asset.
     *
     * @param string $relativePath Path relative to the WP installation directory
     * @return false|int False if the file is not found, last modified time otherwise.
     */
    private function getLastModifiedTime($relativePath) {
        $fullPath = str_replace("/", DIRECTORY_SEPARATOR, trailingslashit(ABSPATH) . ltrim($relativePath, DIRECTORY_SEPARATOR));
        if(file_exists($fullPath)) {
            return filemtime($fullPath);
        }

        return false;
    }

    /**
     * Get script localization values.
     *
     * @return array
     */
    private function getLocalizationValues() {
        return [
            'an_error_occurred'     =>  _wpcc("An error occurred."),
            'press_to_copy'         =>  _wpcc("Press {0} to copy"),
            'copied'                =>  _wpcc("Copied!"),
            'no_result'             =>  _wpcc("No result."),
            'found'                 =>  _wpcc("Found"),
            'required_for_test'     =>  _wpcc("This is required to perform the test."),
            'required'              =>  _wpcc("This is required."),
            'css_selector_found'    =>  _wpcc("CSS selector found"),
        ];
    }
}