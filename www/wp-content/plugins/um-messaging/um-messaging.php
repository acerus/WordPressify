<?php
/*
Plugin Name: Ultimate Member - Private Messages
Plugin URI: http://ultimatemember.com/
Description: Allow users to send private messages to each other on your site.
Version: 2.0.4
Author: Ultimate Member
Author URI: http://ultimatemember.com/
*/

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );

define( 'um_messaging_url', plugin_dir_url( __FILE__ ) );
define( 'um_messaging_path', plugin_dir_path( __FILE__ ) );
define( 'um_messaging_plugin', plugin_basename( __FILE__ ) );
define( 'um_messaging_extension', $plugin_data['Name'] );
define( 'um_messaging_version', $plugin_data['Version'] );
define( 'um_messaging_textdomain', 'um-messaging' );

define( 'um_messaging_requires', '2.0.1' );

function um_messaging_plugins_loaded() {
	$locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
	load_textdomain( um_messaging_textdomain, WP_LANG_DIR . '/plugins/' . um_messaging_textdomain . '-' . $locale . '.mo' );
    load_plugin_textdomain( um_messaging_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'um_messaging_plugins_loaded', 0 );

add_action( 'plugins_loaded', 'um_messaging_check_dependencies', -20 );

if ( ! function_exists( 'um_messaging_check_dependencies' ) ) {
    function um_messaging_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_messaging_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-messaging' ), um_messaging_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_messaging_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = um\is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not active
                function um_messaging_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-messaging' ), um_messaging_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_messaging_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_messaging_requires, um_messaging_version, 'messaging', um_messaging_extension ) ) {
                //UM old version is active
                function um_messaging_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_messaging_requires, um_messaging_version, 'messaging', um_messaging_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_messaging_dependencies' );

            } else {
                require_once um_messaging_path . 'includes/core/um-messaging-init.php';
            }
        }
    }
}


register_activation_hook( um_messaging_plugin, 'um_messaging_activation_hook' );
function um_messaging_activation_hook() {
    //first install
    $version = get_option( 'um_messaging_version' );
    if ( ! $version )
        update_option( 'um_messaging_last_version_upgrade', um_messaging_version );

    if ( $version != um_messaging_version )
        update_option( 'um_messaging_version', um_messaging_version );


    //run setup
    if ( ! class_exists( 'um_ext\um_messaging\core\Messaging_Setup' ) )
        require_once um_messaging_path . 'includes/core/class-messaging-setup.php';

    $messaging_setup = new um_ext\um_messaging\core\Messaging_Setup();
    $messaging_setup->run_setup();
}