<?php
/**
 * WP Job Manager - Favorites
 *
 * @since 3.6.0
 * @category Integration
 */
class Jobify_WP_Job_Manager_Favorites extends Jobify_Integration {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		// Load scripts.
		add_action( 'job_manager_job_filters_after', array( $this, 'enqueue_scripts' ) );
		add_action( 'wpjmf_favorite_form_before', array( $this, 'enqueue_scripts' ) );
		add_action( 'wpjmf_favorite_form_logged_out_before', array( $this, 'enqueue_scripts' ) );

		// Form position
		$favorites = WPJMF_Form::get_instance();

		remove_action( 'single_job_listing_meta_after', array( $favorites, 'form' ) );
		remove_action( 'single_resume_start', array( $favorites, 'form' ) );

		add_action( 'jobify_widget_job_apply_after', array( $favorites, 'form' ) );
	}

	/**
	 * Register scripts.
	 *
	 * @since 3.6.0
	 */
	public function register_scripts() {
		wp_dequeue_style( 'wpjmf-form' );
		wp_register_script( 'jobify-wp-job-manager-favorites', $this->get_url() . 'js/wp-job-manager-favorites.min.js', array( 'jquery', 'jobify', 'wp-job-manager-ajax-filters' ) );
	}

	/**
	 * Enqueue script on favorite form.
	 *
	 * @since 3.6.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jobify-wp-job-manager-favorites' );
		wp_enqueue_script( 'wpjmf-form' );
	}
}
