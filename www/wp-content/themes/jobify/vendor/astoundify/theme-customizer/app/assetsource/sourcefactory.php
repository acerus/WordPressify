<?php
/**
 * Source factory.
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine if a source can be loaded.
 *
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_AssetSource_SourceFactory {

	/**
	 * Try to create a new instance of the source.
	 *
	 * @since 1.1.0
	 *
	 * @param string $source_key
	 * @return mixed
	 */
	public static function get_source( $source_key ) {
		try {
			$classname = self::get_source_class( $source_key );

			if ( ! $classname ) {
				throw new Exception( 'Missing source class', 422 );
			}

			if ( ! class_exists( $classname ) ) {
				throw new Exception( 'Invalid source class', 422 );
			}

			return new $classname();
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Attempt to get the source class.
	 *
	 * @since 1.1.0
	 *
	 * @param string $source_key
	 * @return mixed String of class or false
	 */
	public static function get_source_class( $source_key ) {
		$class = false;

		if ( ! $source_key || '' == $source_key ) {
			return $class;
		}

		// dont modify custom source keys, use as class.
		$prefix = 'Astoundify_ThemeCustomizer_';
		$length = strlen( $prefix );

		$source_key = str_replace( '-', '_', $source_key );

		if ( 0 === strncasecmp( $prefix, $class, $length ) ) {
			$class = $source_key;
		} else {
			$class = 'Astoundify_ThemeCustomizer_AssetSources_' . $source_key . '_Manager';
		}

		return $class;
	}

}
