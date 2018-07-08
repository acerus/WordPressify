<?php
/**
 * Accounts
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'accounts', array(
	'title' => _x( 'Accounts', 'customizer section title', 'jobify' ),
	'panel' => 'general',
	'priority' => 40,
) );
