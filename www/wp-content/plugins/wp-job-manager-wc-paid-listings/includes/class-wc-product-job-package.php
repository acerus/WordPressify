<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Job Package Product Type
 */
class WC_Product_Job_Package extends WP_Job_Manager_WCPL_Package_Product {

	/**
	 * Constructor
	 *
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		$this->product_type = 'job_package';
		parent::__construct( $product );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'job_package';
	}

	/**
	 * We want to sell jobs one at a time
	 *
	 * @return boolean
	 */
	public function is_sold_individually() {
		return apply_filters( 'wcpl_' . $this->get_type() . '_is_sold_individually', true );
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_url() {
		$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() );

		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text
	 *
	 * @access public
	 * @return string
	 */
	public function add_to_cart_text() {
		$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'wp-job-manager-wc-paid-listings' ) : __( 'Read More', 'wp-job-manager-wc-paid-listings' );

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
	}

	/**
	 * Job Packages can always be purchased regardless of price.
	 *
	 * @return boolean
	 */
	public function is_purchasable() {
		return true;
	}

	/**
	 * Jobs are always virtual
	 *
	 * @return boolean
	 */
	public function is_virtual() {
		return true;
	}

	/**
	 * Return job listing duration granted
	 *
	 * @return int
	 */
	public function get_duration() {
		$job_listing_duration = $this->get_job_listing_duration();
		if ( $job_listing_duration ) {
			return $job_listing_duration;
		} else {
			return get_option( 'job_manager_submission_duration' );
		}
	}

	/**
	 * Return job listing limit
	 *
	 * @return int 0 if unlimited
	 */
	public function get_limit() {
		$job_listing_limit = $this->get_job_listing_limit();
		if ( $job_listing_limit ) {
			return $job_listing_limit;
		} else {
			return 0;
		}
	}

	/**
	 * Return if featured
	 *
	 * @return bool true if featured
	 */
	public function is_job_listing_featured() {
		return 'yes' === $this->get_job_listing_featured();
	}

	/**
	 * Get job listing featured flag
	 *
	 * @return string
	 */
	public function get_job_listing_featured() {
		return $this->get_product_meta( 'job_listing_featured' );
	}

	/**
	 * Get job listing limit
	 *
	 * @return int
	 */
	public function get_job_listing_limit() {
		return $this->get_product_meta( 'job_listing_limit' );
	}

	/**
	 * Get job listing duration
	 *
	 * @return int
	 */
	public function get_job_listing_duration() {
		return $this->get_product_meta( 'job_listing_duration' );
	}
}
