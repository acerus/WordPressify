<?php
/**
 * Created by PhpStorm.
 * User: turgutsaricam
 * Date: 18/04/16
 * Time: 08:33
 */

namespace WPCCrawler;

use DateTime;

if(!class_exists('WPTSLM')) {

class WPTSLMClient {

    private $productName;
    private $productId;
    private $type;
    private $apiUrl;
    private $pluginFilePath;
    private $textDomain;

    /**
     * WPTSLMClient constructor.
     * @param string $productName
     * @param string $productId
     * @param string $type
     * @param string $apiUrl
     * @param string|null $pluginFilePath Required only if this is a plugin. Full path for the plugin file.
     * @param string $textDomain Text domain of the plugin/theme.
     */
    public function __construct($productName, $productId, $type = 'plugin', $apiUrl, $pluginFilePath, $textDomain) {
        $this->productName      = $productName;
        $this->productId        = $productId;
        $this->type             = $type;
        $this->apiUrl           = $apiUrl;
        $this->pluginFilePath   = $pluginFilePath;
        $this->textDomain       = $textDomain;

        $this->init();
    }

    public function init() {
        $this->setIntervals();
        if(!wp_next_scheduled($this->getEventName())) $this->scheduleEvents();

        add_action($this->getEventName(), [$this, 'validate']);
        $this->maybeRun();

        register_activation_hook($this->pluginFilePath, [$this, 'validate']);
        register_deactivation_hook($this->pluginFilePath, [$this, 'deactivate']);

        $valid = $this->getValid();
        if($valid != 0 && $valid != 1 && $valid !== null) {
            $dt = new DateTime($valid);
            $now = new DateTime(current_time("mysql"));
            if($dt <= $now) $this->setValid(0);
        }

        // Add license menu
        add_action('admin_menu', function() {
            // Create sub menu page
            add_options_page(
                __('License Settings', $this->textDomain),
                sprintf(__('%s License Settings', $this->textDomain), $this->getProductName()),
                'manage_options',
                $this->getPageSlug(),
                function() { $this->renderLicenseSettingsPage(); }
            );
        }, 3);

        // Add a notice for the user to remind that license settings should be updated
        add_action('admin_notices', [$this, 'showAdminNotice']);

        // Listen post requests
        add_action(sprintf('admin_post_%s', $this->getPageSlug()), function() {
            $this->postLicenseSettingsPage();
        });

        // Handle updates
        if($this->isPlugin()) {
            // Check for updates for plugins
            add_filter('pre_set_site_transient_update_plugins', [$this, 'checkForUpdate']);

            // Show plugin information when the user wants to see it
            add_filter('plugins_api', [$this, 'handlePluginsApi'], 10, 3);
        } else {
            // Check for updates for themes
            add_filter('pre_set_site_transient_update_themes', [$this, 'checkForUpdate']);
        }

        // Add a link among plugin action links
        add_filter(sprintf('plugin_action_links_%s', plugin_basename($this->pluginFilePath)), function($links) {
            $newLinks = [
                sprintf('<a href="%s">%s</a>', $this->getLicenseSettingsPageUrl(), __("License Settings", $this->textDomain)),
            ];
            return array_merge($links, $newLinks);
        });

    }

    public function getLicenseSettingsPageUrl() {
        return admin_url("options-general.php?page=" . $this->getPageSlug());
    }

    /**
     * A function for the WordPress "plugins_api" filter. Checks if the user is requesting information about the
     * current plugin and returns its details if needed.
     *
     * This function is called before the Plugins API checks for plugin information on WordPress.org.
     *
     * @param $res      bool|object The result object, or false (= default value).
     * @param $action   string      The Plugins API action. We're interested in 'plugin_information'.
     * @param $args     array       The Plugins API parameters.
     *
     * @return object   The API response.
     */
    public function handlePluginsApi($res, $action, $args) {
        if($action == 'plugin_information') {
            // If the request is for this plugin, respond to it
            if (isset($args->slug) && $args->slug == plugin_basename($this->pluginFilePath)) {
                $info = $this->getProductInfo();

                $res = (object) [
                    'name'              => isset($info->title)              ? $info->title              : '',
                    'version'           => $info->version_pretty,
                    'homepage'          => isset($info->homepage)           ? $info->homepage           : null,
                    'author'            => isset($info->author)             ? $info->author             : null,
                    'slug'              => $args->slug,
                    'download_link'     => $info->package_url,

                    'tested'            => isset($info->tested)             ? $info->tested             : '',
                    'requires'          => isset($info->requires)           ? $info->requires           : '',
                    'last_updated'      => isset($info->last_updated)       ? $info->last_updated       : '',

                    'sections'  => [
                        'description'   => $info->description,
                    ],

                    'banners'   => [
                        'low'       => isset($info->banner_low) ? $info->banner_low : '',
                        'high'      => isset($info->banner_high) ? $info->banner_high : ''
                    ],

                    'external' => true
                ];

                // Add a few tabs
                if (isset($info->installation)) $res->sections['installation'] = $info->installation;
                if (isset($info->screenshots)) $res->sections['screenshots'] = $info->screenshots;
                if (isset($info->changelog)) $res->sections['changelog'] = $info->changelog;
                if (isset($info->faq)) $res->sections['faq'] = $info->faq;

                return $res;
            }
        }

        // Not our request, let WordPress handle this.
        return $res;
    }

    public function checkForUpdate($transient) {
        if(empty($transient->checked)) return $transient;

        $info = $this->isUpdateAvailable();
        if($info !== false) {
            if($this->isPlugin()) {
                // Plugin
                $pluginSlug = plugin_basename($this->pluginFilePath);

                $transient->response[$pluginSlug] = (object) [
                    'new_version'   => $info->version_pretty,
                    'package'       => $info->package_url,
                    'slug'          => $pluginSlug
                ];

            } else {
                // Theme
                $themeData = wp_get_theme();
                $themeSlug = $themeData->get_template();

                $transient->response[$themeSlug] = [
                    'new_version'   => $info->version_pretty,
                    'package'       => $info->package_url,
//                    'url'           => $info->description_url
                ];
            }
        }

        return $transient;
    }

    /**
     * Show a notice to remind the user that he/she should fill license credentials.
     */
    public function showAdminNotice() {
        if(!$this->getLicenseKey() || /*!$this->getLicenseEmail() ||*/ $this->getValid() != 1) {
            if($this->getValid() != 1 && $this->getValid() !== null) {
                if($this->getValid() != 0) {
                    $dt = new DateTime($this->getValid());
                    $msg = __('Your %1$s license has expired. Please get a new license until %2$s to continue using %3$s.', $this->textDomain);
                    $msg = sprintf($msg, $this->getProductName(), $dt->format('d/m/Y H:i'), $this->getProductName());
                } else {
                    $msg = __('Your %1$s license has expired and you did not provide a new license. The features are disabled.
                        Please get a new license to continue using %2$s.', $this->textDomain);
                    $msg = sprintf($msg, $this->getProductName(), $this->getProductName());
                }
            } else {
                $msg = __('Please enter your license key for %s to use its features and get updates.', $this->textDomain);
                $msg = sprintf($msg, $this->getProductName());
            }
            $errorMessage = $this->getErrorMessage();
            ?>
                <div class="update-nag">
                    <p><?php echo $msg; ?></p>
                    <?php if($errorMessage) {
                        echo "<p>" . __("Message", $this->textDomain) . ': ' . __($errorMessage, $this->textDomain) . "</p>";
                    } ?>
                    <p>
                        <a href="<?php echo admin_url('options-general.php?page=' . $this->getPageSlug()); ?>">
                            <?php _e('Complete the setup now.', $this->textDomain); ?>
                        </a>
                    </p>
                </div>
            <?php
        }
    }

    public function isUserCool() {
        $valid = $this->getValid();

        if($valid == 1) return true;
        if($valid == 0) return false;
        if($valid !== null) {
            $dt = new DateTime($valid);
            $now = new DateTime(current_time("mysql"));
            if($dt <= $now) {
                return false;
            }
        }

        return true;
    }

    /**
     * Renders license settings page.
     */
    public function renderLicenseSettingsPage() {
        $showAlert = isset($_GET["success"]) && $_GET["success"] == 'true';
        ?>
            <div class="wrap">
                <h1><?php echo sprintf(__('License Settings for %s', $this->textDomain), $this->getProductName()) ?></h1>

                <div class="notice notice-success <?php if(!$showAlert) echo 'hidden'; ?>">
                    <p><?php _e('License settings updated.', $this->textDomain) ?></p>
                </div>

                <form action="admin-post.php" method="post" novalidate="novalidate">
                    <input type="hidden" name="action" value="<?php echo $this->getPageSlug(); ?>" id="hiddenaction">
                    <?php wp_nonce_field() ?>

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="<?php echo $this->getLicenseKeyOptionName(); ?>">
                                        <?php _e('License Key', $this->textDomain) ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="password" class="regular-text"
                                           name="<?php echo $this->getLicenseKeyOptionName(); ?>"
                                           id="<?php echo $this->getLicenseKeyOptionName(); ?>"
                                           value="<?php echo $this->getLicenseKey(); ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <?php submit_button(); ?>
                </form>

                <?php if($this->getLicenseKey()) { ?>
                    <form action="admin-post.php" method="post" novalidate="novalidate">
                        <input type="hidden" name="action" value="<?php echo $this->getPageSlug(); ?>" id="hiddenaction2">
                        <input type="hidden" name="deactivate" value="1">
                        <?php wp_nonce_field() ?>
                        <?php submit_button(__('Deactivate on this domain', $this->textDomain), 'secondary right'); ?>
                    </form>
                <?php } ?>
            </div>

        <?php
    }

    /**
     * Handles post request made from license settings page's form
     */
    public function postLicenseSettingsPage() {
        $data = $_POST;
        $success = true;

        // If the user wants to deactivate the plugin on current domain
        if(isset($data["deactivate"]) && $data["deactivate"]) {
            $success = $this->deactivate(true);
            if($success) {
                deactivate_plugins(plugin_basename($this->pluginFilePath), true);
                wp_redirect(admin_url("plugins.php"));
                return;
            }
        } else {

            // Save settings
            if (isset($data[$this->getLicenseKeyOptionName()])) {
                update_option($this->getLicenseKeyOptionName(), $data[$this->getLicenseKeyOptionName()], true);
            }

//        if(isset($data[$this->getLicenseEmailOptionName()])) {
//            update_option($this->getLicenseEmailOptionName(), $data[$this->getLicenseEmailOptionName()], true);
//        }

            $this->validate();
            $this->scheduleEvents();
        }

        // Redirect back
        $url = admin_url(sprintf('options-general.php?page=%s&success=%s', $this->getPageSlug(), $success ? 'true' : 'false'));
        wp_redirect($url);
    }

    public function validate() {
        $this->updateLastRun();
        $info = $this->getProductInfo();

        $valid = $this->getValid();

        if(!is_object($info) || !$info) {
            $dt = new DateTime(current_time('mysql'));
            $dt->modify('+3 days');
            $this->setValid($dt->format('Y-m-d H:i:s'));
            $this->setErrorMessage(__("The license could not be checked with the server. Please try saving your license
                settings again in a few minutes. If the error persists, please contact the developer.", $this->textDomain));

        } else if((isset($info->valid) && $info->valid) || !isset($info->error)) {
            $this->setValid(1);
            $this->setErrorMessage(null);

        } else if($valid == 1 || $valid === null || $valid == 0) {
            if(isset($info->expiration)) {
                $dt = new DateTime($info->expiration);
                $this->modifyDateToLocalTime($dt);
                $dt->modify('+3 days');

                $now = new DateTime(current_time('mysql'));

//                var_dump("Expiration: " . $dt->format("Y-m-d H:i:s"));
//                var_dump("Now: " . $now->format("Y-m-d H:i:s"));

                if($dt <= $now) {
                    $this->setValid(0);
                } else {
                    $this->setValid($dt->format('Y-m-d H:i:s'));
                }
            } else {
                $dt = new DateTime(current_time('mysql'));
                $dt->modify('+3 days');

//                var_dump("Expiration +3 days: " . $dt->format("Y-m-d H:i:s"));

                $this->setValid($dt->format('Y-m-d H:i:s'));
            }

            $this->setErrorMessage($info->error);

        } else if($valid != 0 && $valid != 1) {
            $dt = new DateTime($valid);
            $now = new DateTime(current_time('mysql'));

//            var_dump("Expiration +3 days: " . $dt->format("Y-m-d H:i:s"));
//            var_dump("Now: " . $now->format("Y-m-d H:i:s"));

            if($dt <= $now) {
                $this->setValid(0);
            }
            $this->setErrorMessage($info->error);

        } else {
            $this->setValid(0);
            $this->setErrorMessage($info->error);
        }

    }

    public function deactivate($network_wide) {
        $result = [];
        if(is_multisite() && $network_wide) {
            global $wpdb;

            // store the current blog id
            $currentBlog = $wpdb->blogid;

            // Get all blogs in the network and activate plugin on each one
            $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

            $serverNames = [];
            foreach ($blogIds as $blogId) {
                switch_to_blog($blogId);

                if (!$this->getLicenseKey()) continue;

                // Do not call the API if it is already called for this server name.
                if(in_array($this->getServerName(), $serverNames)) continue;

                $result = (array) $this->callApi('uninstall', [
                    'p' => $this->productId,
                    'l' => $this->getLicenseKey(),
                    'd' => $this->getServerName()
                ]);
                $serverNames[] = $this->getServerName();

                restore_current_blog();
            }

        } else {
            if (!$this->getLicenseKey()) return false;

            $result = (array) $this->callApi('uninstall', [
                'p' => $this->productId,
                'l' => $this->getLicenseKey(),
                'd' => $this->getServerName()
            ]);
        }

        return isset($result["success"]) && $result["success"];
    }

    /**
     * Checks the license manager to see if there is an update available for this theme.
     *
     * @return object|bool  If there is an update, returns the license information.
     *                      Otherwise returns false.
     */
    public function isUpdateAvailable() {
        $licenseInfo = $this->getProductInfo();
        if ($this->isApiError($licenseInfo)) return false;

        if (version_compare($licenseInfo->version_pretty, $this->getLocalVersion(), '>')) {
            return $licenseInfo;
        }

        return false;
    }

    /**
     * Get information about the licensed product.
     * @return bool|object
     */
    public function getProductInfo() {
        if(!$this->getLicenseKey() /*|| !$this->getLicenseEmail()*/) return false;

        return $this->callApi('info', [
            'p' =>  $this->productId,
            'l' =>  $this->getLicenseKey(),
//            'e' =>  $this->getLicenseEmail(),
            'd' =>  $this->getServerName()
        ]);
    }

    /*
     * PRIVATE HELPERS
     */

    private function getProductName() {
        return __($this->productName, $this->textDomain);
    }

    private function scheduleEvents() {
        $this->removeScheduledEvents();
        if(!wp_get_schedule($this->getEventName())) {
            wp_schedule_event(time(), '_wptslm_1_day', $this->getEventName());
        }
    }

    private function removeScheduledEvents() {
        $eventNames = [$this->getEventName()];
        foreach($eventNames as $eventName) {
            if($timestamp = wp_next_scheduled($eventName)) {
                wp_unschedule_event($timestamp, $eventName);
            }
        }
    }

    private function setIntervals() {
        $intervals = [
            '_wptslm_1_minute'  =>  ['Every minute',    60],
            '_wptslm_1_day'     =>  ['Every day',       24 * 60 * 60],
            '_wptslm_2_days'    =>  ['Every 2 days',    2 * 24 * 60 * 60],
        ];
        add_filter('cron_schedules', function($schedules) use ($intervals) {
            foreach($intervals as $name => $interval) {
                $schedules[$name] = [
                    'interval'  =>  $interval[1],
                    'display'   =>  $interval[0]
                ];
            }

            return $schedules;
        });
    }

    /**
     * Checks the API response to see if there was an error.
     *
     * @param $response mixed|object The API response to verify
     * @return bool True if there was an error. Otherwise false.
     */
    private function isApiError($response) {
        if ($response === false) return true;
        if (!is_object($response)) return true;
        if (isset($response->error)) return true;

        return false;
    }

    /**
     * Make an API call
     *
     * @param string $action
     * @param array $params
     * @return bool|object False if fails, the results as object if succeeds.
     */
    private function callApi($action, $params) {
        $url = $this->apiUrl . '/' . $action;

        // Append parameters for GET request
        $url .= '?' . http_build_query($params);

        // Send the request
        $response = wp_remote_get($url);
        if (is_wp_error($response)) return false;

        $responseBody = wp_remote_retrieve_body($response);
        return json_decode($responseBody);
    }

    private function getServerName() {
        return $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : '';
    }

    /*
     *
     */

//    private function getLicenseEmailOptionName() {
//        return $this->getPrefix() . '_license_email';
//    }
//
//    private function getLicenseEmail() {
//        return get_option($this->getLicenseEmailOptionName(), null);
//    }

    private function getLicenseKeyOptionName() {
        return $this->getPrefix() . '_license_key';
    }

    private function getLicenseKey() {
        return get_option($this->getLicenseKeyOptionName(), null);
    }

    private function getValidOptionName() {
        return md5($this->getPrefix() . '_toolm');
    }

    private function setValid($value) {
        update_option($this->getValidOptionName(), $value, true);
    }

    private function getValid() {
        $valid = get_option($this->getValidOptionName(), null);
        if($valid === null) {
            $dt = new DateTime(current_time('mysql'));
            $dt->modify('+3 days');
            $valid = $dt->format('Y-m-d H:i:s');
            $this->setValid($valid);
        }

        return $valid;
    }

    private function getErrorMessageOptionName() {
        return $this->getPrefix() . '_license_message';
    }

    private function getErrorMessage() {
        return get_option($this->getErrorMessageOptionName(), null);
    }

    private function setErrorMessage($message) {
        update_option($this->getErrorMessageOptionName(), $message);
    }

    private function getEventName() {
        return 'wptslm_' . md5($this->textDomain);
    }

    private function getPageSlug() {
        return $this->textDomain . '_license_settings';
    }

    /**
     * Get a string to be used as prefix for option names.
     * @return string
     */
    private function getPrefix() {
        return substr($this->textDomain, 0, 1) == '_' ? $this->textDomain : '_' . $this->textDomain;
    }

    /**
     * Get version of the plugin/theme.
     * @return mixed
     */
    private function getLocalVersion() {
        if($this->isPlugin()) {
            $pluginData = get_plugin_data($this->pluginFilePath, false);
            return $pluginData["Version"];
        } else {
            // This is a theme
            $themeData = wp_get_theme();
            return $themeData->Version;
        }
    }

    /**
     * @return bool True if this is a plugin, false otherwise.
     */
    private function isPlugin() {
        return $this->type == 'plugin';
    }

    private function maybeRun() {
        $lastRun = get_option($this->getEventName() . '_run');
        if(!$lastRun || $lastRun < time() - 2.5 * 24 * 60 * 60) {
            $this->validate();
        }
    }

    private function updateLastRun() {
        update_option($this->getEventName() . '_run', time(), true);
    }

    /**
     * Modify a universal time to convert it to local time
     * @param DateTime  $dt
     * @param bool|int  $gmtOffset GMT offset of target local time. If false, WordPress settings will be used to get
     *                  GMT offset.
     */
    private function modifyDateToLocalTime(&$dt, $gmtOffset = false) {
        if(!$gmtOffset) $gmtOffset = get_option('gmt_offset');
        $dt->modify(($gmtOffset >= 0 ? "+" : "-") . $gmtOffset . " hour" . ($gmtOffset > 1 || $gmtOffset < -1 ? "s" : ""));
    }

}

}