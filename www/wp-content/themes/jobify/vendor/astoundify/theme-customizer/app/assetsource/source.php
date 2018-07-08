<?php
/**
 * Manage a source.
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
 * A source.
 *
 * @since 1.1.0
 */
abstract class Astoundify_ThemeCustomizer_AssetSource_Source {

	/**
	 * The source data.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var string $raw_data
	 */
	public $raw_data;

	/**
	 * The normalized source data.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var array $data
	 */
	public $data = array();

	/**
	 * Set the raw data.
	 *
	 * @since 1.1.0
	 *
	 * @param arary $raw_data
	 */
	public function set_raw_data( $raw_data ) {
		$this->raw_data = $raw_data;
	}

	/**
	 * Get the raw data.
	 *
	 * @since 1.1.0
	 *
	 * @return array $raw_data
	 */
	public function get_raw_data() {
		return $this->raw_data;
	}

	/**
	 * Set the parsed data.
	 *
	 * @since 1.1.0
	 *
	 * @param arary $data
	 */
	public function set_data( $data ) {
		$this->data = $data;
	}

	/**
	 * Get the parsed data.
	 *
	 * @since 1.1.0
	 *
	 * @return array $data
	 */
	public function get_data() {
		if ( empty( $this->data ) ) {
			$this->parse();
		}

		return $this->data;
	}

	/**
	 * Load the raw data.
	 *
	 * If the origin is a path load the file directly, otherwise request the remote URL.
	 *
	 * @since 1.1.0
	 *
	 * @param $origin Source origin
	 * @return mixed $raw_data
	 */
	public function load_raw_data( $origin ) {
		$loader = astoundify_themecustomizer_get_assetloader( $origin );

		if ( $loader ) {
			$this->set_raw_data( $loader->load_data() );
		}

		return $this->get_raw_data();
	}

	/**
	 * Return a super clean (even cleaner!) array of data that can be used
	 * in a WP Customize control.
	 *
	 * @since 1.1.0
	 *
	 * @return array $choices
	 */
	public function get_customize_control_choices() {
		$choices = array();

		foreach ( $this->get_data() as $item_key => $item ) {
			$choices[ $item_key ] = $item['label'];
		}

		return $choices;
	}

	/**
	 * Get a single item from the main source list.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key
	 * @return array Singular item. Empty if doesnt exist
	 */
	public function get_item( $key ) {
		$data = $this->get_data();

		if ( ! isset( $data[ $key ] ) ) {
			return array();
		}

		return $data[ $key ];
	}

}
