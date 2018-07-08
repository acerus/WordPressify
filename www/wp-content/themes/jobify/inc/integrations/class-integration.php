<?php
/**
 * Plugin integrations.
 */

abstract class Jobify_Integration {

	public $includes = array();

	public $directory;

	/**
	 * Add customizer support.
	 *
	 * @since 3.5.0
	 * @access public
	 * @var $has_customizer
	 */
	public $has_customizer;

	public function __construct( $directory ) {
		$this->directory = $directory;

		$this->includes();
		$this->init();
		$this->setup_actions();

		$this->internal_actions();

		// load customizer definitions if needed
		if ( $this->has_customizer ) {
			$this->customizer();
		}
	}

	private function includes() {
		if ( empty( $this->includes ) ) {
			return;
		}

		foreach ( $this->includes as $file ) {
			require_once( trailingslashit( $this->directory ) . $file );
		}
	}

	public function init() {}

	public function setup_actions() {}

	private function internal_actions() {
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function body_class( $classes ) {
		$classes[] = $this->get_slug();

		return $classes;
	}

	public function get_url() {
		return trailingslashit( get_template_directory_uri() . '/inc/integrations/' . $this->get_slug() );
	}

	public function get_dir() {
		return trailingslashit( $this->directory );
	}

	private function get_slug() {
		$slug = basename( $this->get_dir() );

		return $slug;
	}

	/**
	 * Add automatic customizer support for integrations.
	 *
	 * In the integration directory create the following structure:
	 *
	 *   class-integration.php
	 *   customizer/
	 *     defitions/
	 *       panels/*.php
	 *       controls/*.php
	 *       sections/*.php
	 *       output-styles/*.php
	 *
	 * @see https://github.com/Astoundify/theme-customizer/blob/master/theme-customizer/Output/Manager.php#L59
	 * @see https://github.com/Astoundify/theme-customizer/blob/master/theme-customizer/Customize/Register.php#L54
	 *
	 * @since 3.5.0
	 */
	public function customizer() {
		// frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'customizer_output_styles' ), 2 );

		// backend
		add_action( 'customize_register', array( $this, 'customize_register_items' ), 19 );
	}

	/**
	 * Load output definitions.
	 *
	 * @since 3.5.0
	 */
	public function customizer_output_styles() {
		$files = glob( $this->get_dir() . 'customizer/definitions/output-styles/*.php' );

		if ( empty( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			include_once( $file );
		}
	}

	/**
	 * Load customizer items.
	 *
	 * This includes panels, sections, and controls.
	 *
	 * @since 3.5.0
	 * @param object $wp_customize Customize API
	 */
	public function customize_register_items( $wp_customize ) {
		foreach ( array( 'panels', 'sections', 'controls' ) as $item ) {
			$files = glob( $this->get_dir() . 'customizer/definitions/' . $item . '/*.php' );

			if ( empty( $files ) ) {
				continue;
			}

			foreach ( $files as $file ) {
				include_once( $file );
			}
		}
	}
}
