<?php
/**
 * WooCommerce
 *
 * @package Jobify
 * @category Integration
 * @since 3.0.0
 */
class Jobify_WooCommerce extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-woocommerce-registration.php',
			'class-woocommerce-template.php',
			'class-woocommerce-layout.php',
		);

		// add customizer support
		$this->has_customizer = true;

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		$this->template = new Jobify_WooCommerce_Template();
		$this->layout = new Jobify_WooCommerce_Layout();
		$this->registration = new Jobify_WooCommerce_Registration();
	}

	public function setup_actions() {
		add_filter( 'submit_job_form_login_url', array( $this, 'login_url' ), 10 );
		add_filter( 'submit_resume_form_login_url', array( $this, 'login_url' ), 10 );
		add_filter( 'wpjmf_form_login_url', array( $this, 'login_url' ), 10 );

		add_action( 'pre_get_posts', array( $this, 'hide_packages_from_shop' ) );
	}

	public function login_url( $url ) {
		// not sure why this is -1 sometimes
		if ( -1 == wc_get_page_id( 'myaccount' ) ) {
			return $url;
		}

		$parts = parse_url( $url );
		parse_str( $parts['query'], $query );

		$url = get_permalink( wc_get_page_id( 'myaccount' ) );

		if ( isset( $query['redirect_to'] ) ) {
			$url = add_query_arg( 'redirect_to', $query['redirect_to'], $url );
		}

		return esc_url( $url );
	}

	/**
	 * Automatically hide job and resume packages from the shop archives.
	 *
	 * @since 3.2.0
	 *
	 * @param object $query WP_Query
	 * @return void
	 */
	public function hide_packages_from_shop( $query ) {
		if ( ! $query->is_main_query() || ! $query->is_post_type_archive() ) {
			return;
		}

		if ( is_admin() ) {
			return;
		}

		if ( is_shop() || is_search() ) {
			$tax_query = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'job_package', 'job_package_subscription', 'resume_package', 'resume_package_subscription' ),
				'operator' => 'NOT IN',
			);

			$query->tax_query->queries[] = $tax_query;
			$query->query_vars['tax_query'] = $query->tax_query->queries;
		}
	}

}
