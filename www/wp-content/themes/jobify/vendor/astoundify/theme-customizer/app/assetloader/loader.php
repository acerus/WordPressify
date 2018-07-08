<?php
/**
 * Load a source.
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
 * Load a source.
 *
 * @since 1.1.0
 */
abstract class Astoundify_ThemeCustomizer_AssetLoader_Loader {

	/**
	 * Path/Url to the unparsed raw data.
	 *
	 * @since 1.1.0
	 * @access public
	 * @var string $raw_data_origin
	 */
	protected $raw_data_origin;

	/**
	 * Start things up.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $origin ) {
		$this->set_raw_data_origin( $origin );
	}

	/**
	 * Set the raw data origin. Usually a file or URL to be parsed.
	 *
	 * @since 1.1.0
	 *
	 * @param string $origin
	 */
	public function set_raw_data_origin( $origin ) {
		$this->raw_data_origin = $origin;
	}

	/**
	 * Get the raw data origin.
	 *
	 * @since 1.1.0
	 *
	 * @return string $raw_data_origin
	 */
	public function get_raw_data_origin() {
		return $this->raw_data_origin;
	}

}
