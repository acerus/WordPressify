<?php
/**
 * Output page header typography.
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-page-header-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-page-header-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-page-header-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-page-header-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => '.page-header',
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => '.page-header',
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
