<?php
/**
 * WP Job Manager - WC Paid Listings
 */
class Jobify_WP_Job_Manager_WCPL extends Jobify_Integration {

	public function __construct() {
		$this->includes = array();

		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 20 );
		add_action( 'wp_footer', array( $this, 'package_selection' ) );
	}

	/**
	 * Registers widgets, and widget areas for WooCommerce
	 *
	 * @since 1.7.0
	 * @return void
	 */
	public function widgets_init() {
		if ( ! jobify()->get( 'woocommerce' ) ) {
			return;
		}

		require_once( $this->get_dir() . 'widgets/class-widget-price-table.php' );

		register_widget( 'Jobify_Widget_Price_Table_WC' );
	}

	/**
	 * Add the selected package to the submission form so it is carried over when submitting
	 * a listing. JS picks up this value and checks the proper radio and submits the form.
	 *
	 * @since 3.2.0
	 * @return void;
	 */
	public function package_selection() {
		if ( ! isset( $_GET['selected_package'] ) ) {
			return;
		}

		$package = absint( $_GET['selected_package'] );

		echo '<input type="hidden" id="jobify_selected_package" value="' . $package . '" />';
	}

}
