<?php
/**
 * Jobs
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'jobs', array(
	'title' => _x( 'Jobs', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'jobs-resumes',
	'priority' => 10,
) );
