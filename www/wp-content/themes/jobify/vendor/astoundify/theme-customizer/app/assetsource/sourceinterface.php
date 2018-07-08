<?php
/**
 * Manage a source of assets.
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
 * Manage a source of assets.
 *
 * @since 1.1.0
 */
interface Astoundify_ThemeCustomizer_AssetSource_SourceInterface {

	/**
	 * Parse results of the raw data find.
	 *
	 * Normalize the raw data array to something that looks like:
	 *
	 * ```
	 * array(
	 *   'item-1' => array(
	 *     'label' => 'Item 1',
	 *     'foo' => 'bar'
	 *    ),
	 *   'item-2' => array(
	 *     'label' => 'Item 2',
	 *     'foo' => 'bar'
	 *    )
	 * );
	 * ```
	 *
	 * @since 1.1.0
	 *
	 * @return array $data
	 */
	public function parse();

}
