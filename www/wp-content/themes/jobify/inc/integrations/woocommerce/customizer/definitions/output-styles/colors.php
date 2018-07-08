<?php
/**
 * WooCommerce using colors.
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$primary = jobify_theme_color( 'color-primary', '#7dc246' );
$accent = jobify_theme_color( 'color-accent', '#7dc246' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.woocommerce ul.products li.product .onsale, .woocommerce-page ul.products li.product .onsale'
	),
	'declarations' => array(
		'background-color' => esc_attr( $accent ),
	),
) );


astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.woocommerce .price ins',
		'.woocommerce ul.product_list_widget ins',
	),
	'declarations' => array(
		'background-color' => esc_attr( $primary ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.single-product #content .woocommerce-tabs .tabs li.active a',
		'.woocommerce-MyAccount-navigation-link.is-active a',
	),
	'declarations' => array(
		'color' => $primary,
		'border-bottom' => '2px solid ' . esc_attr( $primary ),
	),
) );
