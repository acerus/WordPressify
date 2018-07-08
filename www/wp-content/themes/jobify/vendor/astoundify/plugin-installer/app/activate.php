<?php
/**
 * Handle plugin activation.
 *
 * @package Astoundify
 * @subpackage PluginInstaller
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extend the existing WP_Plugin_Install_List_Table to override item preperation
 * and only include the items defined by the library.
 *
 * @since 1.0.0
 */
class Astoundify_PluginInstaller_Activate extends Astoundify_ModuleLoader_Module {

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		add_action( 'wp_ajax_astoundify_plugininstaller_activate_plugin', array( $this, 'activate_plugin' ) );
	}

	/**
	 * AJAX callback for activating a plugin.
	 *
	 * @todo check for nonce, caps, etc
	 *
	 * @since 1.0.0
	 */
	public function activate_plugin() {
		$l10n = astoundify_plugininstaller_get_l10n();

		if ( empty( $_POST['plugin'] ) ) {
			wp_send_json_error( array(
				'slug' => '',
				'errorCode' => 'no_plugin_specified',
				'errorMessage' => esc_attr( $l10n['invalidPlugin'] ),
			) );
		}

		$plugin = $_POST['plugin'];

		$status = array(
			'slug' => sanitize_key( wp_unslash( $_POST['slug'] ) ),
		);

		// custom nonce so our response is valid and errors are appended to the card
		// check_ajax_referrer just returns a -1
		if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'updates' ) ) {
			$status['errorMessage'] = esc_attr( $l10n['invalidNonce'] );
			wp_send_json_error( $status );
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			$status['errorMessage'] = esc_attr( $l10n['invalidCap'] );
			wp_send_json_error( $status );
		}

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$result = activate_plugin( $plugin );

		if ( is_wp_error( $result ) ) {
			$status['errorMessage'] = $result->get_error_message();
			wp_send_json_error( $status );
		}

		$status['plugin'] = $plugin;

		wp_send_json_success( $status );
	}

}
