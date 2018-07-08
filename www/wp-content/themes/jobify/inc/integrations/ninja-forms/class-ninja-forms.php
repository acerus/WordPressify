<?php
/**
 * Ninja forms integration.
 *
 * @since 3.9.0
 *
 * @package Jobify
 * @category Integration
 * @author Astoundify
 */

/**
 * Ninja Forms integration.
 *
 * @since 3.9.0
 */
class Jobify_Ninja_Forms extends Jobify_Integration {

	/**
	 * Setup integration.
	 *
	 * @since 3.9.0
	 */
	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	/**
	 * Initialize.
	 *
	 * @since 3.9.0
	 */
	public function init() {}

	/**
	 * Hook in to WordPress.
	 *
	 * @since 3.9.0
	 */
	public function setup_actions() {
		add_filter( 'jobify_single_job_listing_apply_button', '__return_false' );
	}

}