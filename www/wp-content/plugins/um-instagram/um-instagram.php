<?php
/**
 * Plugin Name: 	  Ultimate Member - Instagram
 * Plugin URI:        https://ultimatemember.com/
 * Description:       An extension to allow a user to connect to their Instagram account which embeds their most recent instagram photos onto their Ultimate Member profile
 * Version:           2.0.1
 * Author:            Ultimate Member
 * Author URI:        https://ultimatemember.com/
 * Text Domain:       um-instagram
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( ABSPATH.'wp-admin/includes/plugin.php' );

$plugin_data = get_plugin_data( __FILE__ );

define( 'um_instagram_url', plugin_dir_url( __FILE__ ) );
define( 'um_instagram_path', plugin_dir_path( __FILE__ ) );
define( 'um_instagram_plugin', plugin_basename( __FILE__ ) );
define( 'um_instagram_extension', $plugin_data['Name'] );
define( 'um_instagram_version', $plugin_data['Version'] );
define( 'um_instagram_textdomain', 'um-instagram' );

define( 'um_instagram_requires', '2.0' );

add_action( 'plugins_loaded', 'um_instagram_check_dependencies', -20 );

if ( ! function_exists( 'um_instagram_check_dependencies' ) ) {
    function um_instagram_check_dependencies() {
        if ( ! defined( 'um_path' ) || ! file_exists( um_path  . 'includes/class-dependencies.php' ) ) {
            //UM is not installed
            function um_instagram_dependencies() {
                echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-instagram' ), um_instagram_extension ) . '</p></div>';
            }

            add_action( 'admin_notices', 'um_instagram_dependencies' );
        } else {

            if ( ! function_exists( 'UM' ) ) {
                require_once um_path . 'includes/class-dependencies.php';
                $is_um_active = um\is_um_active();
            } else {
                $is_um_active = UM()->dependencies()->ultimatemember_active_check();
            }

            if ( ! $is_um_active ) {
                //UM is not active
                function um_instagram_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'um-instagram' ), um_instagram_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_instagram_dependencies' );

            } elseif ( true !== UM()->dependencies()->compare_versions( um_instagram_requires, um_instagram_version, 'instagram', um_instagram_extension ) ) {
                //UM old version is active
                function um_instagram_dependencies() {
                    echo '<div class="error"><p>' . UM()->dependencies()->compare_versions( um_instagram_requires, um_instagram_version, 'instagram', um_instagram_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_instagram_dependencies' );

            } elseif ( ! UM()->dependencies()->php_version_check( '5.4' ) ) {
                //UM old version is active
                function um_instagram_dependencies() {
                    echo '<div class="error"><p>' . sprintf( __( 'The <strong>%s</strong> extension requires <strong>PHP 5.4 or better</strong> installed on your server.', 'um-instagram' ), um_instagram_extension ) . '</p></div>';
                }

                add_action( 'admin_notices', 'um_instagram_dependencies' );

            } else {
                require_once um_instagram_path . 'includes/core/um-instagram-init.php';
            }
        }
    }
}

register_activation_hook( um_instagram_plugin, 'um_instagram_activation_hook' );
function um_instagram_activation_hook() {
    //first install
    $version = get_option( 'um_instagram_version' );
    if ( ! $version )
        update_option( 'um_instagram_last_version_upgrade', um_instagram_version );

    if ( $version != um_instagram_version )
        update_option( 'um_instagram_version', um_instagram_version );


    //run setup
    if ( ! class_exists( 'um_ext\um_instagram\core\Instagram_Setup' ) )
        require_once um_instagram_path . 'includes/core/class-instagram-setup.php';

    $instagram_setup = new um_ext\um_instagram\core\Instagram_Setup();
    $instagram_setup->run_setup();
}