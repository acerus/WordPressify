<?php
/*
Plugin Name: Ultimate Member - User Tags
Plugin URI: https://ultimatemember.com/
Description: Allow users to add tags to their profiles easily
Version: 2.0.2
Author: Ultimate Member
Author URI: https://ultimatemember.com/
*/

require_once(ABSPATH.'wp-admin/includes/plugin.php');

$plugin_data = get_plugin_data( __FILE__ );

define('um_user_tags_url',plugin_dir_url(__FILE__ ));
define('um_user_tags_path',plugin_dir_path(__FILE__ ));
define('um_user_tags_plugin', plugin_basename( __FILE__ ) );
define('um_user_tags_extension', $plugin_data['Name'] );
define('um_user_tags_version', $plugin_data['Version'] );
define('um_user_tags_textdomain', 'um-user-tags' );

define('um_user_tags_requires', '2.0');

function um_user_tags_plugins_loaded() {
	$locale = ( get_locale() != '' ) ? get_locale() : 'en_US';
	load_textdomain( um_user_tags_textdomain, WP_LANG_DIR . '/plugins/' . um_user_tags_textdomain . '-' . $locale . '.mo' );
    load_plugin_textdomain( um_user_tags_textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'um_user_tags_plugins_loaded', 0 );

add_action( 'plugins_loaded', 'um_user_tags_check_dependencies', -20 );

if ( ! function_exists( 'um_user_tags_check_dependencies' ) ) {
    function um_user_tags_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_user_tags_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-user-tags' ), um_user_tags_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_user_tags_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not active
                function um_user_tags_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-user-tags' ), um_user_tags_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_user_tags_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_user_tags_requires, um_user_tags_version, 'user-tags', um_user_tags_extension ) ) {
                //UM old version is active
                function um_user_tags_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_user_tags_requires, um_user_tags_version, 'user-tags', um_user_tags_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_user_tags_dependencies' );

            } else {
                require_once um_user_tags_path . 'includes/core/um-user-tags-init.php';
            }
        }
    }
}


register_activation_hook( um_user_tags_plugin, 'um_user_tags_activation_hook' );
function um_user_tags_activation_hook() {
    //first install

    //old version fix for old user_tags installs
    $version_old = get_option( 'um_user_tags_latest_version' );
    $version = get_option( 'um_user_tags_version' );
    if ( ! $version && ! $version_old )
        update_option( 'um_user_tags_last_version_upgrade', um_user_tags_version );

    if ( $version != um_user_tags_version )
        update_option( 'um_user_tags_version', um_user_tags_version );


    //run setup
    if ( ! class_exists( 'um_ext\um_user_tags\core\User_Tags_Setup' ) )
        require_once um_user_tags_path . 'includes/core/class-user-tags-setup.php';

    $user_tags_setup = new um_ext\um_user_tags\core\User_Tags_Setup();
    $user_tags_setup->run_setup();
}