<?php

class Jobify_WP_Job_Manager_Application_Deadline extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'widgets/class-widget-job-deadline.php'
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	public function widgets_init() {
		register_widget( 'Jobify_Widget_Job_Deadline' );
	}

}
