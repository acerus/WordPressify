<?php
if ( ! function_exists( 'astoundify_moduleloader_autoload_internal' ) ) :
	/**
	 * Autoloader for built in modules
	 *
	 * Based on an example implementation of PSR-4, but compatible with PHP 5.2.
	 *
	 * @since 1.0.0
	 * @link http://www.php-fig.org/psr/psr-4/examples/
	 *
	 * @param string $class The name of the class to be autoloaded
	 */
	function astoundify_moduleloader_autoload_internal( $class ) {
		// Prefix for all classes that are loaded
		$prefix = 'Astoundify_ModuleLoader_';
		$length = strlen( $prefix );

		// Does the current class have the set prefix?
		if ( 0 !== strncmp( $prefix, $class, $length ) ) {
			return;
		}

		$base_dir = dirname( __FILE__ );
		$relative_class = strtolower( substr( $class, $length ) );
		$file = trailingslashit( $base_dir ) . str_replace( '_', '/', $relative_class ) . '.php';

		// Load the file if it exists and is readable
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
endif;
spl_autoload_register( 'astoundify_moduleloader_autoload_internal' );

if ( ! function_exists( 'astoundify_moduleloader_autoload' ) ) :
	/**
	 * Internals for a secondary autoloader for external modules.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class The name of the class to be autoloaded
	 * @param string $prefix The prefix or namespace of all classes
	 * @param string $base_dir The starting point to look for files
	 */
	function astoundify_moduleloader_autoload( $class, $prefix, $base_dir ) {
		// Prefix for all classes that are loaded
		$prefix = $prefix;
		$length = strlen( $prefix );

		// Does the current class have the set prefix?
		if ( 0 !== strncasecmp( $prefix, $class, $length ) ) {
			return;
		}

		$base_dir = $base_dir;
		$relative_class = strtolower( substr( $class, $length ) );

		// convert PHP real or fake namespaced classes
		//
		// My_Class -> my/class.php
		// My/Class -> my/class.php
		$relative_file = str_replace( array( '\\', '_' ), '/', $relative_class ) . '.php';

		$file = trailingslashit( $base_dir ) . $relative_file;

		// Load the file if it exists and is readable
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
endif;
