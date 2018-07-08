<?php
/**
 * Customizer helper functions and template tags.
 *
 * @package Jobify
 * @category Customizer
 * @since 3.0.0
 */

/**
 * Return a single theme mod, or its default.
 *
 * @since 3.0.0
 *
 * @param string $key The mod key.
 * @return string $mod The mod.
 */
function jobify_theme_mod( $key, $default = null ) {
	return get_theme_mod( $key, $default );
}

/**
 * Stub until full color packs are implemented
 *
 * @since 3.5.0
 *
 * @param string     $key
 * @param $deprecated
 * @return string $mod
 */
function jobify_theme_color( $key, $deprecated ) {
	return astoundify_themecustomizer_get_colorscheme_mod( $key );
}

/**
 * Get default control settings for the Typography multi-control.
 *
 * @since 3.6.0
 *
 * @return array $controls
 */
function jobify_themecustomizer_get_default_typography_controls() {
	return array(
		'font-family' => array(
			'label' => __( 'Font Family', 'jobify' ),
			'placeholder' => __( 'Search for a font...', 'jobify' ),
		),
		'font-size' => array(
			'label' => __( 'Font Size', 'jobify' ),
		),
		'font-weight' => array(
			'label' => __( 'Font Weight', 'jobify' ),
			'choices' => array(
				'normal' => __( 'Normal', 'jobify' ),
				'bold' => __( 'Bold', 'jobify' ),
			),
		),
		'line-height' => array(
			'label' => __( 'Line Height', 'jobify' ),
		),
	);
}

/**
 * Get the customizable typography elements.
 *
 * @since 3.6.0
 *
 * @return array $elements
 */
function jobify_themecustomizer_get_typography_elements() {
	$elements = array(
		'body' => _x( 'Global', 'customizer section title', 'jobify' ),
		'page-header' => _x( 'Page Headers', 'customizer section title', 'jobify' ),
		'entry-title' => _x( 'Blog Post Titles', 'customizer section title', 'jobify' ),
		'widget-title' => _x( 'Widget Titles', 'customizer section title', 'jobify' ),
		'home-widget-title' => _x( 'Homepage Widget Titles', 'customizer section title', 'jobify' ),
		'home-widget-description' => _x( 'Homepage Widget Descriptions', 'customizer section title', 'jobify' ),
		'button' => _x( 'Buttons', 'customizer section title', 'jobify' ),
		'input' => _x( 'Inputs', 'customizer section title', 'jobify' ),
	);

	return $elements;
}
