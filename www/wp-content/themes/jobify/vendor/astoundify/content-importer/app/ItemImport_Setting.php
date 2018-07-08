<?php
/**
 * Import a setting
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.0.0
 */
class Astoundify_ItemImport_Setting extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Get the setting key
	 *
	 * @since 1.0.0
	 * @return false|string The key string if it exists. False if it does not.
	 */
	private function get_key() {
		return $this->get_id();
	}

	/**
	 * Get the theme mod value
	 *
	 * @since 1.0.0
	 * @return false|string The value string if it exists. False if it does not.
	 */
	private function get_value() {
		if ( ! isset( $this->item['data'] ) ) {
			return false;
		}

		$value = $this->item['data'];

		if ( is_array( $value ) ) {
			$value = wp_parse_args( $value, get_option( $this->get_key(), array() ) );
		}

		return $value;
	}

	/**
	 * Import a single item
	 *
	 * @since 1.0.0
	 * @return bool True on success
	 */
	public function import() {
		$key = $this->get_key();
		$value = $this->get_value();

		if ( ! $key || ! $value ) {
			return $this->get_default_error();
		}

		$result = update_option( $key, $value );

		if ( ! $result ) {
			return $this->get_default_error();
		}

		return $result;
	}

	/**
	 * Reset a single item
	 *
	 * @since 1.0.0
	 * @return bool True on success
	 */
	public function reset() {
		$option = $this->get_previous_import();

		$key = $this->get_key();
		$value = $this->item['data'];

		if ( ! $key || ! $value ) {
			return $this->get_default_error();
		}

		if ( is_array( $value ) ) {
			$option = get_option( $this->get_key() );

			foreach ( $this->item['data'] as $key => $v ) {
				unset( $option[ $key ] );
			}

			$result = update_option( $this->get_key(), $option, $value );
		} else {
			$result = delete_option( $key );
		}

		if ( ! $result ) {
			return $this->get_default_error();
		}

		return $result;
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.0.0
	 * @uses $wpdb
	 * @return Theme mod if true, or false
	 */
	public function get_previous_import() {
		return get_option( $this->get_key() );
	}

}
