<?php

class Jobify_WP_Job_Manager_Submission {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_filter( 'submit_job_form_fields', array( $this, 'contact' ) );
		add_filter( 'submit_job_form_fields', array( $this, 'featured_image' ) );

		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'featured_image_admin' ) );
	}

	public function contact( $fields ) {
		$fields['job']['application']['priority'] = 2;

		return $fields;
	}

	public function featured_image( $fields ) {
		$fields['job']['featured_image'] = array(
			'label'       => __( 'Featured Image', 'jobify' ),
			'description' => __( 'Used for the Job Spotlight display', 'jobify' ),
			'type'        => 'file',
			'required'    => false,
			'placeholder' => '',
			'priority'    => 4.99,
			'ajax'        => true,
			'allowed_mime_types' => array(
				'jpg'  => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'gif'  => 'image/gif',
				'png'  => 'image/png',
			),
		);

		return $fields;
	}

	public function featured_image_admin( $fields ) {
		$fields['_featured_image'] = array(
			'label'       => __( 'Featured Image', 'jobify' ),
			'description' => __( 'Used for the Job Spotlight display', 'jobify' ),
			'type'        => 'file',
			'priority'    => 7,
		);

		return $fields;
	}

}
