<?php
/**
 * Global Typopgraphy
 *
 * @uses $wp_customize
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$elements = jobify_themecustomizer_get_typography_elements();

foreach ( $elements as $element => $label ) {

	$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_Typography( $wp_customize, array(
		'selector' => $element,
		'source' => 'googlefonts',
		'controls' => jobify_themecustomizer_get_default_typography_controls(),
		'section' => 'typography-' . $element,
	) ) );

}
