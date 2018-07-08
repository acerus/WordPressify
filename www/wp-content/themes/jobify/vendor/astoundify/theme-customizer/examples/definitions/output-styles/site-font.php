<?php
/**
 * Site Font
 *
 * @uses $wp_customize
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This is a static source but could be dependent on another control...
 *
 * However the set theme mod's value should be relevant to the set source
 * or a fallback stack will be used.
 */
$source = 'googlefonts';
$body = astoundify_themecustomizer_get_typography_mod( 'site-font-family' );

astoundify_themecustomizer_add_css( array(
	'selectors' => 'body',
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $body, $source ),
	),
) );
