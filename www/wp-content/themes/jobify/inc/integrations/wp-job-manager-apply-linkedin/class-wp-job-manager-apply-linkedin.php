<?php

class Jobify_WP_Job_Manager_Apply_LinkedIn extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_style' ) );
	}

	public function dequeue_style() {
		wp_dequeue_style( 'wp-job-manager-apply-with-linkedin-styles' );
	}

}
