<?php
/**
 * Colors: Header/Navigation
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'colors-header', array(
	'title' => _x( 'Header/Navigation', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'colors',
	'priority' => 20,
) );
