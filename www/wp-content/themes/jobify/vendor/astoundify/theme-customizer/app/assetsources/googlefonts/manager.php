<?php
/**
 * Astoundify_ThemeCustomizer_Source_GoogleFonts class
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
 * Manage Google Web Fonts.
 *
 * @see https://developers.google.com/fonts/
 * @since 1.1.0
 */
class Astoundify_ThemeCustomizer_AssetSources_GoogleFonts_Manager extends Astoundify_ThemeCustomizer_AssetSource_Font implements Astoundify_ThemeCustomizer_AssetSource_SourceInterface, Astoundify_ThemeCustomizer_AssetSource_FontInterface {

	/**
	 * Parse results of the raw data find.
	 *
	 * @since 1.1.0
	 * @see Astoundify_ThemeCustomizer_SourceInterface::parse()
	 *
	 * @return array $data
	 */
	public function parse() {
		$data = $this->load_raw_data( dirname( __FILE__ ) . '/fonts.json' );

		if ( empty( $data ) ) {
			return;
		}

		$this->set_data( $data );

		return $this->get_data();
	}

	/*
	 * Generate a URL to load any necessary Google Fonts.
	 *
	 * @since 1.1.0
	 * @see https://developers.google.com/fonts/docs/getting_started#a_quick_example
	 */
	public function generate_url() {
		$families = $this->get_families();
		$subsets = $this->get_subsets();

		$url = '';

		if ( ! empty( $families ) ) {
			// Start building the URL.
			$base_url = '//fonts.googleapis.com/css';

			// Add families
			$url = add_query_arg( 'family', implode( '|', $families ), $base_url );

			// Add subsets, if specified.
			if ( ! empty( $subsets ) ) {
				$subsets = array_map( 'sanitize_key', $subsets );
				$url = add_query_arg( 'subset', join( ',', $subsets ), $url );
			}
		}

		return $url;
	}

	/*
	 * Generate data that can be sent to WebFont
	 *
	 * @since 1.1.0
	 * @see https://github.com/typekit/webfontloader
	 */
	public function generate_webfont_json() {
		$data = array();
		$families = $this->get_families();
		$subsets = $this->get_subsets();

		// add the subset to the string ("Open+Sans:regular:latin")
		foreach ( $families as $key => $family ) {
			$families[ $key ] = $family . ':' . join( ',', $subets );
		}

		if ( ! empty( $families ) ) {
			$data['google'] = array(
				'families' => $families,
			);
		}

		return $data;
	}

	/*
	 * Get font families used.
	 *
	 * Create a list of family strings and variations. eg ("Open+Sans:regular")
	 *
	 * @since 1.1.0
	 *
	 * @return array $families
	 */
	public function get_families() {
		$fonts = $this->get_fonts_used();
		$families = array();

		foreach ( $fonts as $font ) {
			$item = $this->get_item( $font );

			if ( empty( $item ) ) {
				continue;
			}

			$variants = isset( $item['variants'] ) ? $item['variants'] : array();
			$families[] = urlencode( $font . ':' . join( ',', $this->choose_variants( $variants ) ) );
		}

		return $families;
	}

	/**
	 * Get the defined font subset.
	 *
	 * @since 1.1.0
	 *
	 * @return string $subset
	 */
	public function get_subsets() {
		$subsets = array();
		$fonts = $this->get_fonts_used();

		foreach ( $fonts as $font ) {
			$item = $this->get_item( $font );

			if ( isset( $item['subsets'] ) ) {
				$subsets = array_merge( $subsets, $item['subsets'] );
			}
		}

		return array_filter( array_unique( array_map( 'sanitize_key', $subsets ) ) );
	}

	/**
	 * Choose font variants to load for a given font, based on what's available.
	 *
	 * @since 1.1.0
	 *
	 * @param array $variants
	 * @return array
	 */
	public function choose_variants( $variants ) {
		$chosen_variants = array();

		// If a "regular" variant is not found, get the first variant.
		if ( ! in_array( 'regular', $variants ) && count( $variants ) >= 1 ) {
			$chosen_variants[] = $variants[0];
		} else {
			$chosen_variants[] = 'regular';
		}

		// Only add "italic" if it exists.
		if ( in_array( 'italic', $variants ) ) {
			$chosen_variants[] = 'italic';
		}

		// Only add "700" if it exists.
		if ( in_array( '700', $variants ) ) {
			$chosen_variants[] = '700';
		}

		// De-dupe.
		$chosen_variants = array_unique( $chosen_variants );

		return $chosen_variants;
	}

}
