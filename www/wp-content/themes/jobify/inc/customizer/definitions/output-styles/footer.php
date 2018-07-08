<?php
/**
 * Output footer.
 *
 * @todo split this up.
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Call to Action
 */
$text = jobify_theme_color( 'color-cta-text', '#ffffff' );
$background = jobify_theme_color( 'color-cta-background', '#7dc246' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.footer-cta',
		'.footer-cta a',
		'.footer-cta tel',
	),
	'declarations' => array(
		'color' => esc_attr( $text ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.footer-cta a.button:hover'
	),
	'declarations' => array(
		'color' => esc_attr( $background ) . ' !important',// ew
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.footer-cta',
	),
	'declarations' => array(
		'background-color' => esc_attr( $background ),
	),
) );

/**
 * Footer Widgets
 */
$title = jobify_theme_color( 'color-footer-widgets-title', '#d1d1d1' );
$link = jobify_theme_color( 'color-footer-widgets-link', '#d1d1d1' );
$text = jobify_theme_color( 'color-footer-widgets-text', '#d1d1d1' );
$background = jobify_theme_color( 'color-footer-widgets-background', '#666666' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.widget-title--footer'
	),
	'declarations' => array(
		'color' => esc_attr( $title ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.widget--footer a'
	),
	'declarations' => array(
		'color' => esc_attr( $link ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.widget--footer'
	),
	'declarations' => array(
		'color' => esc_attr( $text ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.footer-widgets'
	),
	'declarations' => array(
		'background-color' => esc_attr( $background ),
	),
) );

/**
 * Copyright
 */
$link = jobify_theme_color( 'color-copyright-link', '#b2b2b2' );
$text = jobify_theme_color( 'color-copyright-text', '#b2b2b2' );
$background = jobify_theme_color( 'color-copyright-background', '#666666' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.copyright a'
	),
	'declarations' => array(
		'color' => esc_attr( $link ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.copyright'
	),
	'declarations' => array(
		'color' => esc_attr( $text ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.site-footer'
	),
	'declarations' => array(
		'background-color' => esc_attr( $background ),
	),
) );
