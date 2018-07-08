<?php
/**
 * WP Job Manager - WC Advanced Paid Listings
 *
 * @since 3.6.0
 */
class Jobify_WP_Job_Manager_WCAPL extends Jobify_Integration {

	/**
	 * @since 3.6.0
	 */
	public function __construct() {
		$this->includes = array();

		parent::__construct( dirname( __FILE__ ) );
	}

	/**
	 * Hook in to WordPress
	 *
	 * @since 3.6.0
	 */
	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 20 );
	}

	/**
	 * Registers widgets, and widget areas.
	 *
	 * @since 3.6.0
	 */
	public function widgets_init() {
		if ( ! jobify()->get( 'woocommerce' ) ) {
			return;
		}

		require_once( get_template_directory() . '/inc/integrations/wp-job-manager-wc-paid-listings/widgets/class-widget-price-table.php' );

		register_widget( 'Jobify_Widget_Price_Table_WC' );
	}

}
