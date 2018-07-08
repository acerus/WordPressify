<?php
/**
 * Site Tags
 *
 * @uses $wp_customize
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'site-tags', array(
	'default' => array( 'cool', 'awesome' ),
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_Multiselect(
	$wp_customize,
	'site-tags',
	array(
		'label' => 'Site Tags',
		'placeholder' => 'Choose a tag...',
		'choices' => array(
			'cool' => 'Cool',
			'really-cool' => 'Really Cool',
			'awesome' => 'Awesome',
		),
		'priority' => 30,
		'section' => 'title_tagline',
	)
) );
