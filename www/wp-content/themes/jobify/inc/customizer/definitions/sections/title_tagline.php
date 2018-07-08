<?php
/**
 * Title & Tagline
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->get_section( 'title_tagline' )->panel = 'general';
$wp_customize->get_section( 'title_tagline' )->priority = 10;
$wp_customize->get_section( 'title_tagline' )->title = _x( 'Site Logo & Header', 'customizer panel title', 'jobify' );
