<?php
/**
 * Colors: Footer Widgets
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'colors-footer-widgets', array(
	'title' => _x( 'Footer Widgets', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'colors',
	'priority' => 40,
) );
