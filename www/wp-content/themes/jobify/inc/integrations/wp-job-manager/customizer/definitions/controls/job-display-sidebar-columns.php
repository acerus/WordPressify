<?php
/**
 * Job Columns
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'job-display-sidebar-columns', array(
	'default' => 3,
) );

$wp_customize->add_control( 'job-display-sidebar-columns', array(
	'label'   => __( 'Widget Columns', 'jobify' ),
	'type' => 'select',
	'choices' => array(
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
	),
	'section' => 'jobs',
	'priority' => 20,
) );
