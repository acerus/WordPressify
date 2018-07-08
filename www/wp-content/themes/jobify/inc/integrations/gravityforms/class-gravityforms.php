<?php

class Jobify_GravityForms extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'jobify_single_job_listing_apply_button', '__return_false' );
	}

	/**
	 * Load the form scripts outside of the loop.
	 */
	function enqueue_scripts() {
		global $post;

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		if ( 'resume' == $post->post_type ) {
			$form = get_option( 'job_manager_resume_apply' );
		} else {
			$form = get_option( 'job_manager_job_apply' );
		}

		gravity_form_enqueue_scripts( $form, true );
	}

}
