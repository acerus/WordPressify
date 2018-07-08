<?php
/**
 * Site
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'site', array(
	'default' => 'default',
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_ControlGroup(
	$wp_customize,
	'site',
	array(
		'label' => _x( 'Site', 'customizer control title', 'jobify' ),
		'section' => 'title_tagline',
		'priority' => 1,
	)
) );
