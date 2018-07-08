<?php
/**
 * Job Display Sidebar
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'job-display-sidebar', array(
	'default' => 'top',
) );

$wp_customize->add_control( 'job-display-sidebar', array(
	'label'   => __( 'Widget Area Location', 'jobify' ),
	'type' => 'select',
	'choices' => array(
		'top' => __( 'Top', 'jobify' ),
		'side' => __( 'Sidebar', 'jobify' ),
	),
	'section' => 'jobs',
	'priority' => 10,
) );
