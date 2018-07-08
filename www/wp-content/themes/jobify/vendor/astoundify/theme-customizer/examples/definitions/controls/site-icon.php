<?php
/**
 * Site Icon
 *
 * Example of loading an asset source choices.
 *
 * @uses $wp_customize
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

/**
 * This is a static source but could be dependent on another control...
 */
$source = 'ionicons';

$wp_customize->add_setting( 'site-icon', array(
	'default' => 'flash',
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_BigChoices(
	$wp_customize,
	'site-icon',
	array(
		'label' => 'What icon best represents your website?',
		'placeholder' => 'Choose an icon...',
		'choices_id' => $source,
		'choices' => astoundify_themecustomizer_get_assetsource_choices( $source ),
		'priority' => 31,
		'section' => 'title_tagline',
	)
) );
