<?php
/**
 * Manage multiple asset sources.
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
 * Manage multiple asset sources.
 *
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_AssetSources_Manager {

	/**
	 * Registered sources.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var string $sources
	 */
	public static $sources;

	/**
	 * Add a source.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key The unique key of the source.
	 * @param object $source Source instance.
	 * @return object Source instance.
	 */
	public static function add( $key, $source ) {
		if ( ! self::exists( $key ) ) {
			self::$sources[ $key ] = $source;
		}

		return self::get( $key );
	}

	/**
	 * Check if the source currently exists.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key The unique key of the source.
	 * @return bool
	 */
	public static function exists( $key ) {
		return isset( self::$sources[ $key ] );
	}

	/**
	 * Get a source.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key The unique key of the source.
	 * @return mixed Source instance or false if not registered
	 */
	public static function get( $key ) {
		// attempt to dynamically load the source if it does not exist
		if ( ! self::exists( $key ) ) {
			$source = Astoundify_ThemeCustomizer_AssetSource_SourceFactory::get_source( $key );

			if ( $source instanceof Astoundify_ThemeCustomizer_AssetSource_Source ) {
				self::add( $key, $source );
			}
		}

		// try again
		if ( ! self::exists( $key ) ) {
			return false;
		}

		return self::$sources[ $key ];
	}

}
