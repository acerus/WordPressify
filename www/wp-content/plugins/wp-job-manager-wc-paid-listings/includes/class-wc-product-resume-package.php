<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resume Package Product Type
 */
class WC_Product_Resume_Package extends WP_Job_Manager_WCPL_Package_Product {

	/**
	 * Constructor
	 *
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		$this->product_type = 'resume_package';
		parent::__construct( $product );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'resume_package';
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
		return apply_filters( 'woocommerce_product_add_to_cart_url', $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() ), $this );
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
	 * Return listing duration granted
	 *
	 * @return int
	 */
	public function get_duration() {
		$resume_duration = $this->get_resume_duration();
		if ( $resume_duration ) {
			return $resume_duration;
		} else {
			return get_option( 'resume_manager_submission_duration' );
		}
	}

	/**
	 * Return resume limit
	 *
	 * @return int 0 if unlimited
	 */
	public function get_limit() {
		$resume_limit = $this->get_resume_limit();
		if ( $resume_limit ) {
			return $resume_limit;
		} else {
			return 0;
		}
	}

	/**
	 * Return if featured
	 *
	 * @return bool true if featured
	 */
	public function is_resume_featured() {
		return 'yes' === $this->get_resume_featured();
	}

	/**
	 * Get resume featured flag
	 *
	 * @return string
	 */
	public function get_resume_featured() {
		return $this->get_product_meta( 'resume_featured' );
	}

	/**
	 * Get resume limit
	 *
	 * @return int
	 */
	public function get_resume_limit() {
		return $this->get_product_meta( 'resume_limit' );
	}

	/**
	 * Get resume duration
	 *
	 * @return int
	 */
	public function get_resume_duration() {
		return $this->get_product_meta( 'resume_duration' );
	}
}
