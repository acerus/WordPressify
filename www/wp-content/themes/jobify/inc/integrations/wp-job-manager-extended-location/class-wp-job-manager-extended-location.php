<?php

class Jobify_WP_Job_Manager_Extended_Location {

	public function __construct() {
		add_action( 'job_manager_settings', array( $this, 'settings' ), 11 );
	}

	public function settings( $settings ) {
		unset( $settings['wpjmel_settings'][1][0] );

		return $settings;
	}

}

$_GLOBALS['jobify_job_manager_extended_location'] = new Jobify_WP_Job_Manager_Extended_Location();
