<?php
/**
 * Bootstrap the customizer library.
 *
 * @see https://github.com/Astoundify/theme-customizer
 *
 * @package Jobify
 * @subpackage Customize
 * @since Jobify 3.5.0
 */
class Jobify_Customize {

	/**
	 * Start things up.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		$this->includes();

		astoundify_themecustomizer( array(
			'stylesheet' => 'jobify-parent',
			'install_url' => get_template_directory_uri() . '/vendor/astoundify/theme-customizer/app',
			'definitions_dir' => get_template_directory() . '/inc/customizer/definitions',
		) );
	}

	/**
	 * Include libs and files.
	 *
	 * @since 3.5.0
	 */
	public function includes() {
		require_once( get_template_directory() . '/vendor/astoundify/theme-customizer/astoundify-themecustomizer.php' );
		require_once( trailingslashit( dirname( __FILE__ ) ) . 'helper-functions.php' );
	}

}

new Jobify_Customize();
