<?php
/**
 * Frontend output/handling of theme mods. This usually means creating
 * CSS based on the set values, but can extend beyond that.
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
 * Bootstrap the frontend.
 *
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Output_Manager extends Astoundify_ModuleLoader_Module {

	/**
	 * @since 1.1.0
	 * @var array $modules
	 * @access protected
	 */
	protected $modules = array(
		'cssgenerator' => 'Astoundify_ThemeCustomizer_Output_CSSGenerator',
		'livepreview' => 'Astoundify_ThemeCustomizer_Output_LivePreview',
	);

	/**
	 * Hook in to WordPress
	 *
	 * @since 1.1.0
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		// early so all styles are added before most enqueing should happen
		add_action( 'wp_enqueue_scripts', array( $this, 'load_output' ), 1 );

		// allow output to be called elsewhere (such as live preview)
		add_action( 'astoundify_themecustomizer_load_output_css', array( $this, 'load_output' ) );

		// attach the generated styles to the provided style dependency
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 20 );

		$this->is_hooked = true;
	}

	/**
	 * Load output definitions.
	 *
	 * @since 1.0.0
	 */
	public function load_output() {
		foreach ( glob( astoundify_themecustomizer_get_option( 'definitions_dir' ) . '/output-styles/*.php' ) as $file ) {
			include_once( $file );
		}
	}

	/**
	 * Attach generated scripts to the set style depenency
	 *
	 * @since 1.0.0
	 */
	public function wp_enqueue_scripts() {
		wp_add_inline_style( astoundify_themecustomizer_get_option( 'stylesheet' ), astoundify_themecustomizer_get_css() );
	}

}
