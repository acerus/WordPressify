<?php
/**
 * Astoundify_ThemeCustomizer_Source_Ionicons class
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage Ionicons
 *
 * @since 1.0.0
 * @see https://github.com/driftyco/ionicons
 */
class Astoundify_ThemeCustomizer_AssetSources_Ionicons_Manager extends Astoundify_ThemeCustomizer_AssetSource_Icon implements Astoundify_ThemeCustomizer_AssetSource_SourceInterface {

	/**
	 * Parse results of the raw data find.
	 *
	 * @since 1.1.0
	 * @see Astoundify_ThemeCustomizer_SourceInterface::parse()
	 *
	 * @return array $data
	 */
	public function parse() {
		$data = $this->load_raw_data( dirname( __FILE__ ) . '/icons.json' );

		if ( empty( $data ) ) {
			return;
		}

		$this->set_data( $data );
	}

}
