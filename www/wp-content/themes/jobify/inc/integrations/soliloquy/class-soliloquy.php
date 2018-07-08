<?php

class Jobify_Soliloquy extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	public function widgets_init() {
		require_once( $this->get_dir() . '/widgets/class-widget-slider-content.php' );
		require_once( $this->get_dir() . '/widgets/class-widget-slider-hero.php' );

		register_widget( 'Jobify_Widget_Slider' );
		register_widget( 'Jobify_Widget_Slider_Hero' );
	}

}
