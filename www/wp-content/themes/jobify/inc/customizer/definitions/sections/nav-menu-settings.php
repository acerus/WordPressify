<?php
/**
 * Navigation Settings
 *
 * @uses $wp_customize
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'nav-menu-settings', array(
	'title' => _x( 'Settings', 'customizer section title', 'jobify' ),
	'panel' => 'nav_menus',
	'priority' => 1,
) );
