<?php
/**
 * Manage live reloading of the preview iframe.
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customize Preview
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Output_LivePreview extends Astoundify_ModuleLoader_Module {

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.1.0
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		add_action( 'customize_preview_init', array( $this, 'customizer_scripts_preview' ), 99 );

		add_action( 'wp_ajax_astoundify-themecustomizer-css', array( $this, 'rebuild_css' ) );
		add_action( 'wp_ajax_astoundify-themecustomizer-webfont', array( $this, 'rebuild_webfont' ) );

		add_action( 'astoundify_themecustomizer_output_customizer_preview_css', array( $this, 'preview_thememods' ) );

		$this->is_hooked = true;
	}

	/**
	 * Preview Scripts and styles
	 *
	 * @since 1.0.0
	 *
	 * @param object $wp_customize Customize API
	 */
	public function customizer_scripts_preview( $wp_customize ) {
		wp_enqueue_script( 'webfontloader', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js', array(), '1.6.16' );

		wp_enqueue_script( 'astoundify-themecustomizer-preview', astoundify_themecustomizer_get_option( 'install_url' ) . '/assets/js/customizer-preview.js', array( 'jquery', 'underscore', 'webfontloader' ), time(), true );

		wp_localize_script( 'astoundify-themecustomizer-preview', 'astoundifyThemeCustomizerPreview', array(
			'stylesheet' => astoundify_themecustomizer_get_option( 'stylesheet' ),
		) );
	}

	/**
	 * Rebuild and return generated CSS based on the current customizer settings.
	 *
	 * @see preview_thememods()
	 *
	 * @since 1.0.0
	 *
	 * @return string JSON response object.
	 */
	public function rebuild_css() {
		// let things know we are in a live preview
		do_action( 'astoundify_themecustomizer_output_customizer_preview_css' );

		// load output files
		do_action( 'astoundify_themecustomizer_load_output_css' );

		$css = astoundify_themecustomizer_get_css();

		if ( '' == $css ) {
			return wp_send_json_error();
		}

		return wp_send_json_success( $css );
	}

	/**
	 * Rebuild and return a JSON for Webfont Loader.
	 *
	 * This currently assumes you are using the `googlefonts` AssetSource.
	 *
	 * @see https://github.com/typekit/webfontloader
	 * @since 1.0.0
	 *
	 * @return string JSON response.
	 */
	public function rebuild_webfont() {
		// let things know we are in a live preview
		do_action( 'astoundify_themecustomizer_output_customizer_preview_css' );

		$controls = (array) $_POST['astoundify-themecustomizer-font-family-controls'];
		$controls = array_map( 'esc_attr', $controls );

		$json = array();
		$fonts = array_values( $controls );

		// gather google
		$source = astoundify_themecustomizer_get_assetsource( 'googlefonts' );
		$json = $source->generate_webfont_json();

		if ( empty( $json ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( $json );
	}

	/**
	 * Wrapper function for substituting preview values in theme mod settings.
	 *
	 * @since 1.0.0
	 */
	public function preview_thememods() {
		if ( ! isset( $_POST['astoundify-themecustomizer-style-controls'] ) ) {
			return;
		}

		$preview = (array) $_POST['astoundify-themecustomizer-style-controls'];

		foreach ( $preview as $setting_id => $value ) {
			add_filter( 'theme_mod_' . sanitize_key( $setting_id ), array( $this, 'preview_thememod_value' ) );
		}
	}

	/**
	 * Return a preview value for a particular theme mod setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value
	 * @return mixed $value
	 */
	public function preview_thememod_value( $value ) {
		if ( ! isset( $_POST['astoundify-themecustomizer-style-controls'] ) ) {
			return $value;
		}

		$preview = (array) $_POST['astoundify-themecustomizer-style-controls'];

		$setting_id = str_replace( 'theme_mod_', '', current_filter() );

		if ( isset( $preview[ $setting_id ] ) ) {
			return $preview[ $setting_id ];
		}

		return $value;
	}

}
