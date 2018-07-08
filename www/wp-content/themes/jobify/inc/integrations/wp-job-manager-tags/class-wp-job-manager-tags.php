<?php

class Jobify_WP_Job_Manager_Tags extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'widgets/class-widget-job-tags.php'
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function body_class( $classes ) {
		$classes[] = 'wp-job-manager-tags';

		return $classes;
	}

	public function widgets_init() {
		register_widget( 'Jobify_Widget_Job_Tags' );
	}

}
