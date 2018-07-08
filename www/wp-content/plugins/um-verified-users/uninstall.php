<?php
/**
 * Uninstall UM Verified Users
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_verified_users_path' ) )
    define( 'um_verified_users_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_verified_users_url' ) )
    define( 'um_verified_users_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_verified_users_plugin' ) )
    define( 'um_verified_users_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_verified_users\core\Verified_Users_Setup' ) )
        require_once um_verified_users_path . 'includes/core/class-verified-users-setup.php';

    $verified_users_setup = new um_ext\um_verified_users\core\Verified_Users_Setup();

    //remove settings
    foreach ( $verified_users_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_verified_users_license_key'] );

    update_option( 'um_options', $options );
}