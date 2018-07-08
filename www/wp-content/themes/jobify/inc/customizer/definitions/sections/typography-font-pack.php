<?php
/**
 * Font Pack
 *
 * @uses $wp_customize
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'typography-font-pack', array(
	'title' => _x( 'Font Pack', 'customizer section title', 'jobify' ),
	'panel' => 'typography',
	'priority' => 10,
) );
