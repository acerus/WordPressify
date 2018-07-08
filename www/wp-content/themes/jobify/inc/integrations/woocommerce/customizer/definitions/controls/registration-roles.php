<?php
/**
 * Registration Roles
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'registration-roles', array(
	'default' => 'employer',
) );

$wp_customize->add_control( new Astoundify_ThemeCustomizer_Control_Multiselect(
	$wp_customize,
	'registration-roles',
	array(
		'label'   => __( 'Available Registration Roles', 'jobify' ),
		'choices' => jobify()->get( 'woocommerce' )->registration->get_registration_roles(),
		'description' => __( 'If no roles are selected, the default in "Job Listings > Settings" will be used and no role field will be shown on the registration form.', 'jobify' ),
		'section' => 'accounts',
		'priority' => 10,
	)
) );
