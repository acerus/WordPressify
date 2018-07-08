<?php
/**
 * Source Loader factory.
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
 * Determine how to load a soure.
 *
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_AssetLoader_LoaderFactory {

	/**
	 * Try to load the origin file for the source.
	 *
	 * @since 1.1.0
	 *
	 * @param string $origin
	 * @return mixed
	 */
	public static function get_loader( $origin ) {
		try {
			$classname = self::get_loader_class( $origin );

			if ( ! $classname ) {
				throw new Exception( 'Missing loader class', 422 );
			}

			if ( ! class_exists( $classname ) ) {
				$classname = 'Astoundify_ThemeCustomizer_Source_Loader_Local';
			}

			return new $classname( $origin );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Get the class to parse the origin.
	 *
	 * @since 1.1.0
	 *
	 * @param string $origin
	 * @return mixed String of class or false
	 */
	public static function get_loader_class( $origin ) {
		if ( ! $origin || '' == $origin ) {
			return false;
		}

		if ( is_file( $origin ) ) {
			$classname = 'Local';
		} elseif ( false !== filter_var( $origin, FILTER_VALIDATE_URL ) ) {
			$classname = 'Remote';
		}

		$classname = apply_filters( 'astoundify_themecustomizer_asset_loader_class', $classname, $origin );

		$base = 'Astoundify_ThemeCustomizer_AssetLoader_';

		return $base . $classname;
	}

}
