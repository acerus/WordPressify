<?php

class Jobify_WP_Job_Manager_Bookmarks extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'init', array( $this, 'move_output' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'deregister_script' ), 20 );
	}

	function move_output() {
		global $job_manager_bookmarks;

		remove_action( 'single_job_listing_meta_after', array( $job_manager_bookmarks, 'bookmark_form' ) );
		remove_action( 'single_resume_start', array( $job_manager_bookmarks, 'bookmark_form' ) );

		add_action( 'jobify_widget_job_apply_after', array( $job_manager_bookmarks, 'bookmark_form' ) );
	}

	function deregister_script() {
		wp_deregister_script( 'wp-job-manager-bookmarks-bookmark-js' );
	}

}
