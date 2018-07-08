<?php
/**
 * Background Image
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

if ( $wp_customize->get_section( 'background_image' ) ) {
	$wp_customize->get_section( 'background_image' )->panel = 'general';
	$wp_customize->get_section( 'background_image' )->priority = 30;
}
