<?php
/**
 * Resumes
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'resumes', array(
	'title' => _x( 'Resumes', 'customizer section title (colors)', 'jobify' ),
	'panel' => 'jobs-resumes',
	'priority' => 20,
) );
