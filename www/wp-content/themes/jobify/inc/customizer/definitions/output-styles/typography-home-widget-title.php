<?php
/**
 * Output homepage widget title typography.
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-title-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-title-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-title-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-title-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.widget-title.widget-title--home',
		'.callout-feature-title',
		'.hero-search__title',
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => '.widget-title.widget-title--home',
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
