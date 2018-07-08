<?php
/**
 * WP Job Manager - Listing Tags
 *
 * @since 3.6.0
 */
class Jobify_WP_Job_Manager_Listing_Tags extends Jobify_Integration {

	/**
	 * @since 3.6.0
	 */
	public function __construct() {
		$this->includes = array(
			'../wp-job-manager-tags/widgets/class-widget-job-tags.php',
			'widgets/class-widget-job-listing-tags.php',
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	/**
	 * Hook in to WordPress
	 *
	 * @since 3.6.0
	 */
	public function setup_actions() {
		$listing_tags = WPJMLT_Front_Setup::get_instance();
		remove_filter( 'the_job_description', array( $listing_tags, 'display_tags' ) );

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	/**
	 * Alert that plugin is active. Required for backwards compat with fork.
	 *
	 * @since 3.6.0
	 */
	public function body_class( $classes ) {
		$classes[] = 'wp-job-manager-tags';

		return $classes;
	}

	/**
	 * Register widgets.
	 *
	 * @since 3.6.0
	 */
	public function widgets_init() {
		register_widget( 'Jobify_Widget_Listing_Tags' );
	}

}
