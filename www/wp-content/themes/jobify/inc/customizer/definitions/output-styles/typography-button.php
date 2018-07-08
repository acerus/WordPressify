<?php
/**
 * Output button typography.
 *
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-button-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-button-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-button-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-button-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.button',
		'input[type=button]',
		'button',
		'#submitcomment',
		'#commentform input[type=submit]',
		'.widget--footer input[type=submit]',

		// Applications
		'input[name=wp_job_manager_send_application]',
		'input[name=wp_job_manager_edit_application]',

		// Bookmarks
		'input[name=submit_bookmark]',

		// RCP
		'#rcp_submit',

		// Resumes
		'input[name=wp_job_manager_resumes_apply_with_resume]',
		'input[name=wp_job_manager_resumes_apply_with_resume_create]',

		// Contact Form 7
		'.wpcf7-submit',

		// Ninja
		'input[type=submit].ninja-forms-field',

		// Alerts
		'input[name=submit-job-alert]',

		// Hero Search
		'.hero-search .search_jobs>div input[type=submit]',
		'.hero-search .search_resumes>div input[type=submit]',
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
		'font-size' => $size . 'px',
	),
) );
