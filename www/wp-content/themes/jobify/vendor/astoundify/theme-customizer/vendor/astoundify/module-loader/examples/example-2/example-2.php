<?php
/**
 * Basic example. PHP 5.3+ compatible.
 *
 * Utilizes available functional dependency loading and hooking.
 *
 * @since 1.0.0
 */

/**
 * Load the library
 */
require_once( dirname( __FILE__ ) . '/../../astoundify-moduleloader/astoundify-moduleloader.php' );

/**
 * Autoloader for Modules
 *
 * Autload all classes starting with `Your_Plugin_` and start
 * looking from the current directory down.
 *
 * @since 1.0.0
 *
 * @param string $class
 */
function astoundify_moduleloader_example_2( $class ) {
	// The namespace for your project
	$prefix = 'Your\Plugin\\';

	// Where to start searching for files.
	$base_dir = dirname( __FILE__ );

	// Include the autoloader
	astoundify_moduleloader_autoload( $class, $prefix, $base_dir );
}
spl_autoload_register( 'astoundify_moduleloader_example_2' );

/**
 * Load namespaced app
 */
require_once( dirname( __FILE__ ) . '/app.php' );
