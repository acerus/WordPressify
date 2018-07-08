<?php
/**
 * Uninstall UM Social Login
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_social_login_path' ) )
    define( 'um_social_login_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_social_login_url' ) )
    define( 'um_social_login_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_social_login_plugin' ) )
    define( 'um_social_login_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_social_login\core\Social_Login_Setup' ) )
        require_once um_social_login_path . 'includes/core/class-social-login-setup.php';

    $social_login_setup = new um_ext\um_social_login\core\Social_Login_Setup();

    //remove settings
    foreach ( $social_login_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_social_login_license_key'] );

    update_option( 'um_options', $options );
}