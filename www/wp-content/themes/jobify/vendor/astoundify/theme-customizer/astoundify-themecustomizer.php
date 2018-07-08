<?php
/*
 * Plugin Name: Astoundify Theme Customizer
 * Plugin URI: https://astoundify.com
 * Description: Lightly wrap and heavily extend the WordPress Customize API for dealing with larger sets of data.
 * Version: 1.2.0
 * Author: Astoundify
 * Author URI: http://astoundify.com
 */

/**
 * Include Astoundify ModuleLoader
 *
 * @since 1.1.0
 * @see https://github.com/Astoundify/module-loader
 */
require_once( dirname( __FILE__ ) . '/vendor/astoundify/module-loader/astoundify-moduleloader.php' );

/**
 * Autoloader for Modules
 *
 * @since 1.1.0
 *
 * @param string $class
 */
function astoundify_themecustomizer_moduleloader( $class ) {
	// Prefix for all classes that are loaded
	$prefix = 'Astoundify_ThemeCustomizer_';
	$base_dir = dirname( __FILE__ ) . '/app/';

	astoundify_moduleloader_autoload( $class, $prefix, $base_dir );
}
spl_autoload_register( 'astoundify_themecustomizer_moduleloader' );

/**
 * Start up the customizer. Can pass initial configuration options.
 *
 * Included early so it can be referenced immediately when this file is included.
 *
 * @since 1.0.0
 *
 * @param array $options
 */
function astoundify_themecustomizer( $options = array() ) {
	new Astoundify_ThemeCustomizer_Manager();
	Astoundify_ThemeCustomizer_Manager::set_options( $options );
}
