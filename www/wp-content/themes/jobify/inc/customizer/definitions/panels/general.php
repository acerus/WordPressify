<?php
/**
 * General
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_panel( 'general', array(
	'title' => _x( 'General', 'customizer panel title', 'jobify' ),
	'priority' => 1,
) );
