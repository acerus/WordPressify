<?php
/**
 * Site Title Color
 *
 * @uses $wp_customize
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$color = get_theme_mod( 'site-title-color', '#000fff' );

astoundify_themecustomizer_add_css( array(
	'selectors' => '.site-title',
	'declarations' => array(
		'color' => esc_attr( $color ),
	),
) );
