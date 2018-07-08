<?php

class Jobify_WP_Job_Manager_Contact_Listing extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_filter( 'job_manager_contact_listing_gravityforms_apply_form_args', array( $this, 'gravityforms_args' ) );
		add_filter( 'job_manager_contact_listing_cf7_apply_form_args', array( $this, 'cf7_args' ) );
	}

	public function body_class( $classes ) {
		$plugin = Astoundify_Job_Manager_Contact_Listing::$active_plugin;

		if ( '' != get_option( 'resume_manager_form_contact' ) ) {
			$classes[] = $plugin . '-contact-resume-form';
			$classes[] = 'wp-job-manager-contact-listing';
		}

		if ( '' != get_option( 'job_manager_form_contact' ) ) {
			$classes[] = $plugin . '-contact-job-form';
			$classes[] = 'wp-job-manager-contact-listing';
		}

		return $classes;
	}

	public function gravityforms_args( $args ) {
		$args = str_replace( 'title="false"', 'title="true"', $args );

		return $args;
	}

	public function cf7_args( $args ) {
		global $post;

		if ( 'job_listing' == $post->post_type ) {
			$title = __( 'Apply for Job', 'jobify' );
		} else {
			$title = __( 'Contact Candidate', 'jobify' );
		}

		$args = $args . sprintf( ' title="%s"', $title );

		return $args;
	}

}
