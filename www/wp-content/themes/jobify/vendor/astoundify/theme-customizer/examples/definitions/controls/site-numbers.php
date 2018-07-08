<?php
/**
 * Site Numbers
 *
 * @uses $wp_customize
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'site-number', array(
	'default' => 20,
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_BigChoices(
	$wp_customize,
	'site-number',
	array(
		'label' => 'How would you rate your site?',
		'placeholder' => 'Choose a number..',
		'choices_id' => 'numbers',
		'choices' => range( 0, 1000 ),
		'priority' => 32,
		'section' => 'title_tagline',
	)
) );
