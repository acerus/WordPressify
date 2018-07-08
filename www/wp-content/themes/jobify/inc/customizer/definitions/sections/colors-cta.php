<?php
/**
 * Colors: Call to Action
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'colors-cta', array(
	'title' => _x( 'Call to Action', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'colors',
	'priority' => 30,
) );
