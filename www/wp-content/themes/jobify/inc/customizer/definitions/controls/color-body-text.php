<?php
/**
 * Color: Body Text
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'color-body-text', array(
	'default' => astoundify_themecustomizer_get_colorscheme_mod_default( 'color-body-text' ),
	'transport' => 'postMessage',
) );

$wp_customize->add_control( new WP_Customize_Color_Control(
	$wp_customize,
	'color-body-text',
	array(
		'label'   => __( 'Body Text', 'jobify' ),
		'section' => 'colors-global',
		'priority' => 40,
	)
) );
