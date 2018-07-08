<?php
/**
 * Background Color
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';

$wp_customize->get_control( 'background_color' )->section = 'colors-global';
$wp_customize->get_control( 'background_color' )->label = __( 'Page Background', 'jobify' );
