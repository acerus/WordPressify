<?php
/**
 * Header Text Color
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

$wp_customize->get_control( 'header_textcolor' )->label = __( 'Header Text', 'jobify' );
$wp_customize->get_control( 'header_textcolor' )->section = 'colors-header';
