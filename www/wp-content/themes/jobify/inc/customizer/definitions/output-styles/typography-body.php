<?php
/**
 * Output body/global typography.
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-body-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-body-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-body-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-body-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => 'body',
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => 'body',
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
