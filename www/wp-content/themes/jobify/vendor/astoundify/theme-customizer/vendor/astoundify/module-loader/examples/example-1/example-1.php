<?php
/**
 * Basic example. PHP 5.2 compatible.
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
function astoundify_moduleloader_example_1( $class ) {
	// The PHP 5.2 "namespace" all classes will include.
	$prefix = 'Your_Plugin_';

	// Where to start searching for files.
	$base_dir = dirname( __FILE__ );

	// Include the autoloader
	astoundify_moduleloader_autoload( $class, $prefix, $base_dir );
}
spl_autoload_register( 'astoundify_moduleloader_example_1' );

/**
 * Use Classes
 */

// autoloads file test.php
$test = new Your_Plugin_Test();

// acesss the `foo` submodule of `Your_Plugin_Test`
$foo = $test->foo();

// call public method of `foo`
$foo->hello();
