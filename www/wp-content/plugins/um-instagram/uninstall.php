<?php
/**
 * Uninstall UM Instagram
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_instagram_path' ) )
    define( 'um_instagram_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_instagram_url' ) )
    define( 'um_instagram_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_instagram_plugin' ) )
    define( 'um_instagram_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_instagram\core\Instagram_Setup' ) )
        require_once um_instagram_path . 'includes/core/class-instagram-setup.php';

    $instagram_setup = new um_ext\um_instagram\core\Instagram_Setup();

    //remove settings
    foreach ( $instagram_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_instagram_license_key'] );

    update_option( 'um_options', $options );
}