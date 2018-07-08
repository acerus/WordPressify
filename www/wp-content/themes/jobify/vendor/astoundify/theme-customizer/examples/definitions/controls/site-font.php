<?php
/**
 * Site Font
 *
 * Use a grouped Typography control that creates multiple.
 *
 * @see https://github.com/Astoundify/themecustomizer/blob/master/astoundify-themecustomizer/Control/Typography.php
 * @see https://github.com/Astoundify/themecustomizer/blob/master/examples/definitions/control-groups/font-pack.php
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
$source = 'googlefonts';

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_Typography( $wp_customize, array(
	'selector' => 'site',
	'source' => $source,
	'controls' => astoundify_themecustomizer_get_default_typography_controls(),
	'priority' => 100,
	'section' => 'title_tagline',
) ) );
