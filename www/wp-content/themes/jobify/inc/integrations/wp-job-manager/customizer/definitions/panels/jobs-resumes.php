<?php
/**
 * Jobs (and maybe Resumes)
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$title = _x( 'Jobs', 'customizer panel title', 'jobify' );

if ( jobify()->get( 'wp-job-manager-resumes' ) ) {
	$title = _x( 'Jobs/Resumes', 'customizer panel title', 'jobify' );
}

$wp_customize->add_panel( 'jobs-resumes', array(
	'title' => $title,
	'priority' => 25,
) );
