<?php
/**
* Uninstall UM Followers
*
*/

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_followers_path' ) )
    define( 'um_followers_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_followers_url' ) )
    define( 'um_followers_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_followers_plugin' ) )
    define( 'um_followers_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_followers\core\Followers_Setup' ) )
        require_once um_followers_path . 'includes/core/class-followers-setup.php';

    $followers_setup = new um_ext\um_followers\core\Followers_Setup();

    //remove settings
    foreach ( $followers_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_followers_license_key'] );

    update_option( 'um_options', $options );
}