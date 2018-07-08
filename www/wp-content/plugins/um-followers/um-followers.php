<?php
/*
Plugin Name: Ultimate Member - Followers
Plugin URI: http://ultimatemember.com/
Description: Add a follow feature for your community users easily.
Version: 2.0.3
Author: Ultimate Member
Author URI: http://ultimatemember.com/
*/

require_once(ABSPATH.'wp-admin/includes/plugin.php');

$plugin_data = get_plugin_data( __FILE__ );

define( 'um_followers_url', plugin_dir_url( __FILE__ ) );
define( 'um_followers_path', plugin_dir_path( __FILE__ ) );
define( 'um_followers_plugin', plugin_basename( __FILE__ ) );
define( 'um_followers_extension', $plugin_data['Name'] );
define( 'um_followers_version', $plugin_data['Version'] );
define( 'um_followers_textdomain', 'um-followers' );

define( 'um_followers_requires', '2.0.1' );

function um_followers_plugins_loaded() {
	$locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
    load_textdomain( um_followers_textdomain, WP_LANG_DIR . '/plugins/' . um_followers_textdomain . '-' . $locale . '.mo');
    load_plugin_textdomain( um_followers_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'um_followers_plugins_loaded', 0 );

add_action( 'plugins_loaded', 'um_followers_check_dependencies', -20 );

if ( ! function_exists( 'um_followers_check_dependencies' ) ) {
    function um_followers_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_followers_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-followers' ), um_followers_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_followers_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = um\is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not installed
                function um_followers_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-followers' ), um_followers_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_followers_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_followers_requires, um_followers_version, 'followers', um_followers_extension ) ) {

                //UM old version is active
                function um_followers_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_followers_requires, um_followers_version, 'followers', um_followers_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_followers_dependencies' );

            } else {
                require_once um_followers_path . 'includes/core/um-followers-init.php';
            }
        }
    }
}


register_activation_hook( um_followers_plugin, 'um_followers_activation_hook' );
function um_followers_activation_hook() {
    //first install
    $version = get_option( 'um_followers_version' );
    if ( ! $version )
        update_option( 'um_followers_last_version_upgrade', um_followers_version );

    if ( $version != um_followers_version )
        update_option( 'um_followers_version', um_followers_version );


    //run setup
    if ( ! class_exists( 'um_ext\um_followers\core\Followers_Setup' ) )
        require_once um_followers_path . 'includes/core/class-followers-setup.php';

    $followers_setup = new um_ext\um_followers\core\Followers_Setup();
    $followers_setup->run_setup();
}