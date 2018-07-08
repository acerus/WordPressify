<?php
/**
 * Site Title Color
 *
 * @uses $wp_customize
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'site-title-color', array(
	'default' => '#000fff',
	'transport' => 'postMessage',
) );

$wp_customize->add_control( new WP_Customize_Color_Control(
	$wp_customize,
	'site-title-color',
	array(
		'label' => 'Site Title Color',
		'priority' => 10,
		'section' => 'title_tagline',
	)
) );
