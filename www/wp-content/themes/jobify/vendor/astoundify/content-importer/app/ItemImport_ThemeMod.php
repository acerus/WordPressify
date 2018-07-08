<?php
/**
 * Import a theme mod
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.2.0
 */
class Astoundify_ItemImport_ThemeMod extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Get the setting key
	 *
	 * @since 1.2.0
	 * @return false|string The key string if it exists. False if it does not.
	 */
	private function get_key() {
		return $this->get_id();
	}

	/**
	 * Get the theme mod value
	 *
	 * @since 1.2.0
	 * @return false|string The value string if it exists. False if it does not.
	 */
	private function get_value() {
		if ( ! isset( $this->item['data'] ) ) {
			return false;
		}

		$value = $this->item['data'];

		if ( is_array( $value ) ) {
			$value = wp_parse_args( $value, get_theme_mod( $this->get_key(), array() ) );
		}

		if ( is_numeric( $value ) ) {
			$value = intval( $value );
		}

		return $value;
	}

	/**
	 * Import a single item
	 *
	 * @since 1.2.0
	 * @return bool True on success
	 */
	public function import() {
		$key = $this->get_key();
		$value = $this->get_value();

		if ( ! $key ) {
			return $this->get_default_error();
		}

		set_theme_mod( $key, $value );

		// have to assume this worked since set_theme_mod returns nothing and
		// get_theme_mod can return anything
		return true;
	}

	/**
	 * Reset a single item
	 *
	 * @since 1.2.0
	 * @return bool True on success
	 */
	public function reset() {
		$option = $this->get_previous_import();

		$key = $this->get_key();
		$value = $this->item['data'];

		if ( ! $key ) {
			return $this->get_default_error();
		}

		if ( is_array( $value ) ) {
			$option = get_theme_mod( $this->get_key() );

			foreach ( $this->item['data'] as $key => $v ) {
				unset( $option[ $key ] );
			}

			set_theme_mod( $this->get_key(), $option, $value );
		} else {
			remove_theme_mod( $key );
		}

		// have to assume this worked since set_theme_mod returns nothing and
		// get_theme_mod can return anything
		return true;
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.2.0
	 * @uses $wpdb
	 * @return Theme mod if true, or false
	 */
	public function get_previous_import() {
		return get_theme_mod( $this->get_key() );
	}

}
