<?php
/**
 * Job Package
 */
class WC_Paid_Listings_Package {
	/**
	 * @var stdClass
	 */
	private $package;

	/**
	 * @var WP_Post
	 */
	private $product;

	/**
	 * Constructor
	 */
	public function __construct( $package ) {
		$this->package = $package;
	}

	/**
	 * Checks if package is set.
	 *
	 * @return bool
	 */
	public function has_package() {
		return ! empty( $this->package );
	}

	/**
	 * Get package ID
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->package->id;
	}

	/**
	 * Get product post
	 *
	 * @return WP_Post
	 */
	public function get_product() {
		if ( empty( $this->product ) ) {
			$this->product = get_post( $this->get_product_id() );
		}
		return $this->product;
	}

	/**
	 * Get product id
	 *
	 * @return int
	 */
	public function get_product_id() {
		return $this->package->product_id;
	}

	/**
	 * Get title for package
	 *
	 * @return string
	 */
	public function get_title() {
		$product = $this->get_product();
		return $product ? $product->post_title : '-';
	}

	/**
	 * Is this package for features jobs/resumes?
	 *
	 * @return boolean
	 */
	public function is_featured() {
		return $this->package->package_featured == 1;
	}

	/**
	 * Get limit
	 *
	 * @return int
	 */
	public function get_limit() {
		return $this->package->package_limit;
	}

	/**
	 * Get count
	 *
	 * @return int
	 */
	public function get_count() {
		return $this->package->package_count;
	}

	/**
	 * Get duration
	 *
	 * @return int|bool
	 */
	public function get_duration() {
		return $this->package->package_duration ? $this->package->package_duration : false;
	}

	/**
	 * Get order id
	 *
	 * @return int
	 */
	public function get_order_id() {
		return $this->package->order_id;
	}
}
