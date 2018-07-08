<?php
/**
 * Manage scripts for interacting with the customizer and custom controls.
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
 * Scripts
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Customize_Scripts extends Astoundify_ModuleLoader_Module {

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.1.0
	 */
	public function hook() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_register_scripts' ), 5 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_enqueue_scripts' ), 20 );
	}

	/**
	 * Register control script dependencies early.
	 *
	 * @since 1.0.0
	 */
	public function customize_controls_register_scripts() {
		$install_url = trailingslashit( astoundify_themecustomizer_get_option( 'install_url' ) );

		wp_register_script( 'astoundify-themecustomizer-select2', $install_url . 'assets/js/vendor/select2/js/select2.min.js' );
		wp_register_style( 'astoundify-themecustomizer-select2-base', $install_url . 'assets/js/vendor/select2/css/select2.min.css' );
		wp_register_style( 'astoundify-themecustomizer-select2', $install_url . 'assets/css/select2.css', array( 'astoundify-themecustomizer-select2-base' ) );
	}

	/**
	 * Set a base for script localization and allow other controls to add their data
	 * to be accessed elsewhere.
	 *
	 * These are enqueued late to allow controls to add their own information.
	 *
	 * @since 1.0.0
	 */
	public function customize_enqueue_scripts() {
		$install_url = trailingslashit( astoundify_themecustomizer_get_option( 'install_url' ) );

		wp_enqueue_script( 'astoundify-themecustomizer', $install_url . '/assets/js/customizer-admin.js', array( 'jquery' ) );

		wp_localize_script( 'astoundify-themecustomizer', 'astoundifyThemeCustomizer', apply_filters( 'astoundify_themecustomizer_scripts', array(
			'BigChoices' => array(),
		) ) );
	}

}
