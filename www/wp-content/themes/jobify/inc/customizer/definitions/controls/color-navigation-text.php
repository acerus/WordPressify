<?php
/**
 * Color: Navigation Text Color
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'color-navigation-text', array(
	'default' => astoundify_themecustomizer_get_colorscheme_mod_default( 'color-navigation-text' ),
	'transport' => 'postMessage',
) );

$wp_customize->add_control( new WP_Customize_Color_Control(
	$wp_customize,
	'color-navigation-text',
	array(
		'label'   => __( 'Navigation Text', 'jobify' ),
		'section' => 'colors-header',
		'priority' => 15,
	)
) );
