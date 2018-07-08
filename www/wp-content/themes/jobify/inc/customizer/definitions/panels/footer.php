<?php
/**
 * Footer
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_panel( 'footer', array(
	'title' => _x( 'Footer', 'customizer panel title', 'jobify' ),
	'priority' => 30,
) );
