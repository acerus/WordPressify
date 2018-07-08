<?php
/**
 * Output input typography.
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-input-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-input-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-input-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-input-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.entry-content .rcp_form input[type=email]',
		'.entry-content .rcp_form input[type=password]',
		'.entry-content .rcp_form input[type=text]',
		'.jobify-input',
		'input[type=email]',
		'input[type=number]',
		'input[type=password]',
		'input[type=search]',
		'input[type=tel]',
		'input[type=text]',
		'select',
		'textarea',
		'body .chosen-container-single .chosen-single span',
		'body .chosen-container .chosen-results li.active-result',
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
		'font-size' => $size . 'px',
	),
) );
