<?php
/**
 * Uninstall UM User Tags
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


if ( ! defined( 'um_user_tags_path' ) )
    define( 'um_user_tags_path', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'um_user_tags_url' ) )
    define( 'um_user_tags_url', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'um_user_tags_plugin' ) )
    define( 'um_user_tags_plugin', plugin_basename( __FILE__ ) );

$options = get_option( 'um_options' );
$options = empty( $options ) ? array() : $options;

if ( ! empty( $options['uninstall_on_delete'] ) ) {
    if ( ! class_exists( 'um_ext\um_user_tags\core\User_Tags_Setup' ) )
        require_once um_user_tags_path . 'includes/core/class-user-tags-setup.php';

    $user_tags_setup = new um_ext\um_user_tags\core\User_Tags_Setup();

    //remove settings
    foreach ( $user_tags_setup->settings_defaults as $k => $v ) {
        unset( $options[$k] );
    }

    unset( $options['um_user_tags_license_key'] );

    update_option( 'um_options', $options );
}