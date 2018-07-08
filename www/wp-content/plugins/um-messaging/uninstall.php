<?php
/**
 * Uninstall UM Messaging
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_messaging_path' ) )
    define( 'um_messaging_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_messaging_url' ) )
    define( 'um_messaging_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_messaging_plugin' ) )
    define( 'um_messaging_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_messaging\core\Messaging_Setup' ) )
        require_once um_messaging_path . 'includes/core/class-messaging-setup.php';

    $messaging_setup = new um_ext\um_messaging\core\Messaging_Setup();

    //remove settings
    foreach ( $messaging_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_messaging_license_key'] );

    update_option( 'um_options', $options );
}