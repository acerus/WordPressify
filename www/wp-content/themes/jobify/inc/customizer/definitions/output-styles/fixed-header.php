<?php
/**
 * Fixed header.
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$fixed_header = get_theme_mod( 'fixed-header', true );

if ( ! $fixed_header ) {
	return;
}

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'body'
	),
	'declarations' => array(
		'padding-top' => '110px',
	),
) );
