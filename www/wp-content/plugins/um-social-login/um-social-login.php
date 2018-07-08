<?php
/*
Plugin Name: Ultimate Member - Social Login
Plugin URI: http://ultimatemember.com/
Description: Social registration and login for Ultimate Member plugin.
Version: 2.0.2
Author: Ultimate Member
Author URI: http://ultimatemember.com/
*/

require_once(ABSPATH.'wp-admin/includes/plugin.php');

$plugin_data = get_plugin_data( __FILE__ );

define('um_social_login_url',plugin_dir_url(__FILE__ ));
define('um_social_login_path',plugin_dir_path(__FILE__ ));
define('um_social_login_plugin', plugin_basename( __FILE__ ) );
define('um_social_login_extension', $plugin_data['Name'] );
define('um_social_login_version', $plugin_data['Version'] );

define('um_social_login_requires', '2.0.1');

function um_social_login_plugins_loaded() {
    load_plugin_textdomain( 'um-social-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'um_social_login_plugins_loaded', 0 );

add_action( 'plugins_loaded', 'um_social_login_check_dependencies', -20 );

if ( ! function_exists( 'um_social_login_check_dependencies' ) ) {
    function um_social_login_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_social_login_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-social-login' ), um_social_login_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_social_login_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = um\is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not active
                function um_social_login_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-social-login' ), um_social_login_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_social_login_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_social_login_requires, um_social_login_version, 'social-login', um_social_login_extension ) ) {
                //UM old version is active
                function um_social_login_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_social_login_requires, um_social_login_version, 'social-login', um_social_login_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_social_login_dependencies' );

            } elseif ( ! UM()->dependencies()->php_version_check( '5.4' ) ) {
                //UM old version is active
                function um_social_login_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires <strong>PHP 5.4 or better</strong> installed on your server.', 'um-instagram' ), um_instagram_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_social_login_dependencies' );

            } else {
                require_once um_social_login_path . 'includes/core/um-social-login-init.php';
            }
        }
    }
}


register_activation_hook( um_social_login_plugin, 'um_social_login_activation_hook' );
function um_social_login_activation_hook() {
    //first install
    $version = get_option( 'um_social_login_version' );
    if ( ! $version )
        update_option( 'um_social_login_last_version_upgrade', um_social_login_version );

    if ( $version != um_social_login_version )
        update_option( 'um_social_login_version', um_social_login_version );


    //run setup
    if ( ! class_exists( 'um_ext\um_social_login\core\Social_Login_Setup' ) )
        require_once um_social_login_path . 'includes/core/class-social-login-setup.php';

    $social_login_setup = new um_ext\um_social_login\core\Social_Login_Setup();
    $social_login_setup->run_setup();
}