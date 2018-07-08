<?php
/**
 * Uninstall UM Notifications
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_notifications_path' ) )
    define( 'um_notifications_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_notifications_url' ) )
    define( 'um_notifications_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_notifications_plugin' ) )
    define( 'um_notifications_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_notifications\core\Notifications_Setup' ) )
        require_once um_notifications_path . 'includes/core/class-notifications-setup.php';

    $notifications_setup = new um_ext\um_notifications\core\Notifications_Setup();

    //remove settings
    foreach ( $notifications_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_notifications_license_key'] );

    update_option( 'um_options', $options );
}