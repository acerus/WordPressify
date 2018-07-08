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
interface Astoundify_ThemeCustomizer_AssetLoader_LoaderInterface {
	public function load_data();
}
