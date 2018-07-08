<?php
/**
 * The very basic loading of definitions to add items to the Customize API.
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
 * Hook in to the WordPress Customize API
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Customize_Register extends Astoundify_ModuleLoader_Module {

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.1.0
	 */
	public function hook() {
		add_action( 'customize_register', array( $this, 'customize_register_custom_controls' ), 15 );
		add_action( 'customize_register', array( $this, 'customize_register_items' ), 20 );
	}


	/**
	 * Load custom control classes.
	 *
	 * @since 1.0.0
	 * @param object $wp_customize Customize API
	 */
	public function customize_register_custom_controls( $wp_customize ) {
		foreach ( glob( trailingslashit( astoundify_themecustomizer_get_option( 'install_dir' ) ) . 'control/*.php' ) as $file ) {
			include_once( $file );
		}
	}

	/**
	 * Load customizer items.
	 *
	 * This includes panels, sections, and controls.
	 *
	 * @since 1.0l.0
	 * @param object $wp_customize Customize API
	 */
	public function customize_register_items( $wp_customize ) {
		foreach ( array( 'panels', 'sections', 'controls' ) as $item ) {
			foreach ( glob( trailingslashit( astoundify_themecustomizer_get_option( 'definitions_dir' ) ) . $item . '/*.php' ) as $file ) {
				include_once( $file );
			}
		}
	}

}
