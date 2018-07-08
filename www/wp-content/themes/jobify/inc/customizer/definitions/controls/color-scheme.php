<?php
/**
 * Color Scheme
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'color-scheme', array(
	'default' => 'default',
	'transport' => 'postMessage',
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_ColorScheme(
	$wp_customize,
	'color-scheme',
	array(
		'label' => _x( 'Color Scheme', 'customizer control title', 'jobify' ),
		'section' => 'colors-scheme',
		'priority' => 0,
	)
) );
