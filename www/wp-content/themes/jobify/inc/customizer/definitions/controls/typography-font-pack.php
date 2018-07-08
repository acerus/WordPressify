<?php
/**
 * Font Pack
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'typography-font-pack', array(
	'default' => 'default',
	'transport' => 'postMessage',
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_ControlGroup(
	$wp_customize,
	'typography-font-pack',
	array(
		'label' => _x( 'Font Pack', 'customizer control title', 'jobify' ),
		'section' => 'typography-font-pack',
		'priority' => 10,
	)
) );
