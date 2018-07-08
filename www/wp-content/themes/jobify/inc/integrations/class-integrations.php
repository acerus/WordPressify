<?php

class Jobify_Integrations {

	private $supported_integrations;
	public $integrations;

	public function __construct() {
		$this->supported_integrations = array(
			'wp-job-manager' => array(
				defined( 'JOB_MANAGER_VERSION' ),
				'Jobify_WP_Job_Manager',
			),
			'wp-job-manager-resumes' => array(
				defined( 'RESUME_MANAGER_VERSION' ),
				'Jobify_WP_Resume_Manager',
			),
			'wp-job-manager-wc-paid-listings' => array(
				defined( 'JOB_MANAGER_WCPL_VERSION' ),
				'Jobify_WP_Job_Manager_WCPL',
			),
			'wp-job-manager-wc-advanced-paid-listings' => array(
				defined( 'JWAPL_VERSION' ),
				'Jobify_WP_Job_Manager_WCAPL',
			),
			'wp-job-manager-bookmarks' => array(
				defined( 'JOB_MANAGER_BOOKMARKS_VERSION' ),
				'Jobify_WP_Job_Manager_Bookmarks',
			),
			'wp-job-manager-tags' => array(
				defined( 'JOB_MANAGER_TAGS_PLUGIN_URL' ),
				'JobifY_WP_Job_Manager_Tags',
			),
			'wp-job-manager-listing-tags' => array(
				defined( 'WPJMLT_VERSION' ),
				'JobifY_WP_Job_Manager_Listing_Tags',
			),
			'wp-job-manager-application-deadline' => array(
				defined( 'JOB_MANAGER_APPLICATION_DEADLINE_PLUGIN_URL' ),
				'Jobify_WP_Job_Manager_Application_Deadline',
			),
			'wp-job-manager-applications' => array(
				defined( 'JOB_MANAGER_APPLICATIONS_VERSION' ),
				'Jobify_WP_Job_Manager_Applications',
			),
			'wp-job-manager-apply-linkedin' => array(
				defined( 'JOB_MANAGER_APPLY_WITH_LINKEDIN_VERSION' ),
				'Jobify_WP_Job_Manager_Apply_LinkedIn',
			),
			'wp-job-manager-apply-with-xing' => array(
				defined( 'JOB_MANAGER_APPLY_WITH_XING_VERSION' ),
				'Jobify_WP_Job_Manager_XING',
			),
			'wp-job-manager-contact-listing' => array(
				class_exists( 'Astoundify_Job_Manager_Contact_Listing' ),
				'Jobify_WP_Job_Manager_Contact_Listing',
			),
			'wp-job-manager-products' => array(
				class_exists( 'WP_Job_Manager_Products' ),
				'Jobify_WP_Job_Manager_Products',
			),
			'wp-job-manager-favorites' => array(
				defined( 'WPJMF_VERSION' ),
				'Jobify_WP_Job_Manager_Favorites',
			),
			'restrict-content-pro' => array(
				defined( 'RCP_PLUGIN_DIR' ),
				'Jobify_Restrict_Content_Pro',
			),
			'woocommerce' => array(
				class_exists( 'woocommerce' ),
				'Jobify_WooCommerce',
			),
			'testimonials' => array(
				class_exists( 'woothemes_testimonials' ),
				'Jobify_Testimonials',
			),
			'soliloquy' => array(
				function_exists( 'soliloquy' ),
				'Jobify_Soliloquy',
			),
			'gravityforms' => array(
				function_exists( 'gravity_form' ) && class_exists( 'astoundify_job_manager_contact_listing' ),
				'Jobify_GravityForms',
			),
			'ninja-forms' => array(
				class_exists( 'Ninja_Forms' ) && class_exists( 'astoundify_job_manager_contact_listing' ),
				'Jobify_Ninja_Forms',
			),
			'jetpack' => array(
				class_exists( 'jetpack' ) || defined( 'JETPACK__VERSION' ),
				'Jobify_Jetpack',
			),
			'sidekick' => array(
				defined( 'SK_API' ),
				'Jobify_Sidekick',
			),
			'geo-my-wp' => array(
				defined( 'GJM_TITLE' ),
				'Jobify_Geo_My_WP',
			),
			'no-captcha-recaptcha-for-woocommerce' => array(
				class_exists( 'woocommerce' ) && class_exists( 'WC_Ncr_No_Captcha_Recaptcha' ),
				'Jobify_No_Captcha_Recaptcha_For_Woocommerce',
			),
			'polylang' => array(
				defined( 'POLYLANG_VERSION' ) && POLYLANG_VERSION,
				'Jobify_Polylang',
			),
		);

		$this->load_integrations();
	}

	public function has( $key ) {
		return isset( $this->integrations[ $key ] );
	}

	public function get( $key ) {
		if ( ! $this->has( $key ) ) {
			return false;
		}

		return $this->integrations[ $key ];
	}

	public function add( $key, $class ) {
		$this->integrations[ $key ] = $class;
	}

	private function load_integrations() {
		foreach ( $this->supported_integrations as $key => $integration ) {
			if ( $integration[0] ) {
				require_once( trailingslashit( dirname( __FILE__ ) ) . trailingslashit( $key ) . 'class-' . $key . '.php' );

				$class = new $integration[1];

				$this->add( $key, $class );
			}
		}
	}

}
