<?php
/**
 * Typography
 *
 * @uses $wp_customize
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_panel( 'typography', array(
	'title' => _x( 'Typography', 'customizer panel title', 'jobify' ),
	'priority' => 22,
) );
