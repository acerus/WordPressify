<?php
/**
 * Static Front Page
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

if ( $wp_customize->get_section( 'static_front_page' ) ) {
	$wp_customize->get_section( 'static_front_page' )->panel = 'general';
	$wp_customize->get_section( 'static_front_page' )->priority = 20;
	$wp_customize->get_section( 'static_front_page' )->title = _x( 'Homepage Display', 'customizer panel title', 'jobify' );
}
