<?php
/*
Plugin Name: Ultimate Member - Friends
Plugin URI: http://ultimatemember.com/
Description: Add a friendship system for your community users easily.
Version: 2.0.2
Author: Ultimate Member
Author URI: http://ultimatemember.com/
*/

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );

define( 'um_friends_url', plugin_dir_url( __FILE__ ) );
define( 'um_friends_path', plugin_dir_path( __FILE__ ) );
define( 'um_friends_plugin', plugin_basename( __FILE__ ) );
define( 'um_friends_extension', $plugin_data['Name'] );
define( 'um_friends_version', $plugin_data['Version'] );
define( 'um_friends_textdomain', 'um-friends' );

define( 'um_friends_requires', '2.0.1' );

function um_friends_plugins_loaded() {
    $locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
    load_textdomain( um_friends_textdomain, WP_LANG_DIR . '/plugins/' . um_friends_textdomain . '-' . $locale . '.mo' );
    load_plugin_textdomain( um_friends_textdomain, false, dirname( plugin_basename(  __FILE__ ) ) . '/languages/' );

}
add_action( 'init', 'um_friends_plugins_loaded', 0 );

add_action( 'plugins_loaded', 'um_friends_check_dependencies', -20 );

if ( ! function_exists( 'um_friends_check_dependencies' ) ) {
    function um_friends_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_friends_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-friends' ), um_friends_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_friends_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = um\is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not active
                function um_friends_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-friends' ), um_friends_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_friends_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_friends_requires, um_friends_version, 'friends', um_friends_extension ) ) {
                //UM old version is active
                function um_friends_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_friends_requires, um_friends_version, 'friends', um_friends_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_friends_dependencies' );

            } else {
                require_once um_friends_path . 'includes/core/um-friends-init.php';
            }
        }
    }
}


register_activation_hook( um_friends_plugin, 'um_friends_activation_hook' );
function um_friends_activation_hook() {
    //first install
    $version = get_option( 'um_friends_version' );
    if ( ! $version )
        update_option( 'um_friends_last_version_upgrade', um_friends_version );

    if ( $version != um_friends_version )
        update_option( 'um_friends_version', um_friends_version );


    //run setup
    if ( ! class_exists( 'um_ext\um_friends\core\Friends_Setup' ) )
        require_once um_friends_path . 'includes/core/class-friends-setup.php';

    $friends_setup = new um_ext\um_friends\core\Friends_Setup();
    $friends_setup->run_setup();
}