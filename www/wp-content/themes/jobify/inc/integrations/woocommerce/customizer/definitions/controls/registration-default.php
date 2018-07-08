<?php
/**
 * Registration Default
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'registration-default', array(
	'default' => 'employer',
) );

$wp_customize->add_control( 'registration-default', array(
	'label'   => __( 'Default Role Selection', 'jobify' ),
	'type'    => 'select',
	'choices' => jobify()->get( 'woocommerce' )->registration->get_registration_roles(),
	'description' => __( 'The role which is selected in the dropdown by default.', 'jobify' ),
	'section' => 'accounts',
	'priority' => 20,
) );
