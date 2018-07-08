<?php
/**
 * WP Job Manager - Products
 *
 * @package Jobify
 * @category Integration
 * @since 3.0.0
 */
class Jobify_WP_Job_Manager_Products extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'widgets/class-widget-products.php'
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 20 );
	}

	/**
	 * Registers widgets, and widget areas for WP Job Manager - Products
	 *
	 * @since Jobify 3.0.0
	 *
	 * @return void
	 */
	public function widgets_init() {
		require_once( $this->get_dir() . 'widgets/class-widget-products.php' );

		register_widget( 'Jobify_Widget_Products' );
	}

}
