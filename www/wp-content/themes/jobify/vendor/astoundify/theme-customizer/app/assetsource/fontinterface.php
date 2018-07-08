<?php
/**
 * Manage font source.
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
 * Manage an font source.
 *
 * @since 1.1.0
 */
interface Astoundify_ThemeCustomizer_AssetSource_FontInterface {

	/*
	 * Generate data that can be sent to WebFont
	 *
	 * @since 1.1.0
	 * @see https://github.com/typekit/webfontloader
	 */
	public function generate_webfont_json();

}
