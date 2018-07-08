<?php
/**
 * Output homepage widget description typography.
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-description-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-description-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-description-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-home-widget-description-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.widget-description.widget-description--home',
		'.callout-feature-content p',
		'.hero-search__content p',
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => '.widget-description.widget-description--home',
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
