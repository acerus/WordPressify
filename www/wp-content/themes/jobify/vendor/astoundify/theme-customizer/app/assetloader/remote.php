<?php
/**
 * Load a remote source.
 *
 * Expects a 200 response and a JSON file. Valid results are cached via WP_Object_Cache.
 *
 * @todo Save results in a transient. This loading should be used sparingly anyway.
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
 * Load a remote source.
 *
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_AssetLoader_Remote extends Astoundify_ThemeCustomizer_AsestLoader_Loader implements Astoundify_ThemeCustomizer_AssetLoader_LoaderInterface {

	/**
	 * Load raw data from a remote file.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed $raw_data Array if valid JSON or null.
	 */
	public function load_data() {
		$url = $this->get_raw_data_origin();

		if ( false === wp_cache_get( $url ) ) {
			$file = wp_safe_remote_get( $url );

			if ( is_wp_error( $file ) || 200 != wp_remote_retrieve_response_code( $file ) ) {
				return null;
			}

			$raw_data = wp_remote_retrieve_body( $file );
			$raw_data = json_decode( $raw_data, true );

			wp_cache_add( $url, $raw_data );
		}

		return $raw_data;
	}

}
