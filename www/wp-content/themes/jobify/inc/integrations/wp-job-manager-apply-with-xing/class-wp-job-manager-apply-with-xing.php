<?php

class Jobify_WP_Job_Manager_XING extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_styles' ) );
	}

	public function dequeue_styles() {
		wp_dequeue_style( 'wp-job-manager-apply-with-xing-styles' );
	}

}
