<?php
/*
Plugin Name: Ultimate Member - Verified Users
Plugin URI: https://ultimatemember.com/
Description: Allow users to get verified and have a verification badge next to their name
Version: 2.0.2
Author: Ultimate Member
Author URI: https://ultimatemember.com/
*/

require_once(ABSPATH.'wp-admin/includes/plugin.php');

$plugin_data = get_plugin_data( __FILE__ );

define('um_verified_users_url',plugin_dir_url(__FILE__ ));
define('um_verified_users_path',plugin_dir_path(__FILE__ ));
define('um_verified_users_plugin', plugin_basename( __FILE__ ) );
define('um_verified_users_extension', $plugin_data['Name'] );
define('um_verified_users_version', $plugin_data['Version'] );
define('um_verified_users_textdomain', 'um-verified' );

define('um_verified_users_requires', '2.0.3');

function um_verified_users_plugins_loaded() {
	$locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
	load_textdomain( um_verified_users_textdomain, WP_LANG_DIR . '/plugins/um-verified-users-' . $locale . '.mo' );
    load_plugin_textdomain( um_verified_users_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'um_verified_users_plugins_loaded', 0 );

add_action( 'plugins_loaded', 'um_verified_users_check_dependencies', -20 );

if ( ! function_exists( 'um_verified_users_check_dependencies' ) ) {
    function um_verified_users_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_verified_users_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-verified' ), um_verified_users_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_verified_users_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = um\is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not active
                function um_verified_users_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-verified' ), um_verified_users_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_verified_users_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_verified_users_requires, um_verified_users_version, 'verified-users', um_verified_users_extension ) ) {
                //UM old version is active
                function um_verified_users_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_verified_users_requires, um_verified_users_version, 'verified-users', um_verified_users_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_verified_users_dependencies' );

            } else {
                require_once um_verified_users_path . 'includes/core/um-verified-users-init.php';
            }
        }
    }
}


register_activation_hook( um_verified_users_plugin, 'um_verified_users_activation_hook' );
function um_verified_users_activation_hook() {
    //first install
    $version = get_option( 'um_verified_users_version' );
    if ( ! $version )
        update_option( 'um_verified_users_last_version_upgrade', um_verified_users_version );

    if ( $version != um_verified_users_version )
        update_option( 'um_verified_users_version', um_verified_users_version );

    //run setup
    if ( ! class_exists( 'um_ext\um_verified_users\core\Verified_Users_Setup' ) )
        require_once um_verified_users_path . 'includes/core/class-verified-users-setup.php';

    $verified_users_setup = new um_ext\um_verified_users\core\Verified_Users_Setup();
    $verified_users_setup->run_setup();
}