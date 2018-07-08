<?php
/**
 * Address Format
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'resume-display-address-format', array(
	'default' => '{city}, {state}',
) );

$wp_customize->add_control( 'resume-display-address-format', array(
	'label'   => __( 'Address Format', 'jobify' ),
	'description' => __( 'Choose between {address_1}, {address_2}, {postcode}, {city}, {state}, {state_code}, {country}. Leave empty to use location as entered.', 'jobify' ),
	'section' => 'resumes',
	'priority' => 30,
) );
