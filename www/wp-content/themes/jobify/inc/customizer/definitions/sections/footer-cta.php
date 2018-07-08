<?php
/**
 * Footer: Call to Action
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'footer-cta', array(
	'title' => _x( 'Call to Action', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'footer',
	'priority' => 10,
) );
