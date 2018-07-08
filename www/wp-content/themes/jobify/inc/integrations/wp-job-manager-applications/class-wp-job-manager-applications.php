<?php

class Jobify_WP_Job_Manager_Applications extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		global $job_manager_applications;

		$this->applications = $job_manager_applications;
	}

	public function setup_actions() {
		add_action( 'job_application_form_fields_start', array( $this, 'add_form_title' ) );
	}

	public function add_form_title() {
		echo '<h2 class="modal-title">' . __( 'Apply', 'jobify' ) . '</h2>';
	}

}
