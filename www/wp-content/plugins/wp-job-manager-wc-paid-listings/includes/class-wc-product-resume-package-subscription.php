<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resume Package Product Type
 */
class WC_Product_Resume_Package_Subscription extends WP_Job_Manager_WCPL_Subscription_Product {

	/**
	 * Constructor
	 *
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
		$this->product_type = 'resume_package_subscription';
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'resume_package_subscription';
	}

	/**
	 * Checks the product type.
	 *
	 * Backwards compat with downloadable/virtual.
	 *
	 * @access public
	 * @param mixed $type Array or string of types
	 * @return bool
	 */
	public function is_type( $type ) {
		return ( 'resume_package_subscription' == $type || ( is_array( $type ) && in_array( 'resume_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
	}

	/**
	 * We want to sell jobs one at a time
	 *
	 * @return boolean
	 */
	public function is_sold_individually() {
		return true;
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
		$resume_duration = $this->get_resume_duration();
		if ( 'listing' === $this->get_package_subscription_type() ) {
			return false;
		} elseif ( $resume_duration ) {
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

	/**
	 * Get package subscription type
	 *
	 * @return string
	 */
	public function get_package_subscription_type() {
		return $this->get_product_meta( 'package_subscription_type' );
	}
}
