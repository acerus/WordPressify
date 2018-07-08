<?php
/**
 * Resume templating
 *
 * @package Jobify
 * @category Integration
 * @since 3.0.0
 */
class Jobify_Job_Manager_Resumes_Template {

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 15 );

		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_styles' ) );
		add_action( 'single_resume_end', array( $this, 'dequeue_scripts' ), 999 );

		add_action( 'jobify_output_resume_results', array( $this, 'output_results' ) );

		add_action( 'resume_manager_contact_details', array( $this, 'contact_wrapper_start' ), 0 );
		add_action( 'resume_manager_contact_details', array( $this, 'contact_wrapper_end' ), 999 );

		add_filter( 'the_candidate_location', array( $this, 'the_candidate_location' ), 9, 2 );
	}

	/**
	 * Output resume shortcode.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function output_results() {
		echo do_shortcode( apply_filters( 'jobify_resume_archive_shortcode', '[resumes]' ) );
	}

	/**
	 * Add template support for Resume Manager.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function add_theme_support() {
		add_theme_support( 'resume-manager-templates' );
	}

	/**
	 * Remove Resume Manager fronten styles.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function dequeue_styles() {
		wp_dequeue_style( 'wp-job-manager-resume-frontend' );
	}

	/**
	 * Remove Resume Manager frontend scripts.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function dequeue_scripts() {
		wp_dequeue_script( 'wp-resume-manager-resume-contact-details' );
	}

	/**
	 * Wrap the inside of the contact modal for better styling.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function contact_wrapper_start() {
		echo '<div class="resume_contact_details_inner">';
	}

	public function contact_wrapper_end() {
		echo '</div>';
	}

	/**
	 * Format the Candidate Location depending on the set address format.
	 *
	 * @since 3.0.0
	 *
	 * @param string $location
	 * @param object $post
	 * @return string $location
	 */
	public function the_candidate_location( $location, $post ) {
		if ( '' == get_theme_mod( 'resume-display-address-format', '{city}, {state}' ) ||
			true == apply_filters( 'jobify_force_skip_formatted_address', false ) ||
			! jobify()->get( 'woocommerce' )
		) {
			return $location;
		}

		$address = apply_filters( 'jobify_formatted_address', array(
			'first_name'    => '',
			'last_name'     => '',
			'company'       => '',
			'address_1'     => $post->geolocation_street,
			'address_2'     => '',
			'street_number' => $post->geolocation_street_number,
			'city'          => $post->geolocation_city,
			'state'         => $post->geolocation_state_short,
			'full_state'    => $post->geolocation_state_long,
			'postcode'      => $post->geolocation_postcode,
			'country'       => $post->geolocation_country_short,
			'full_country'  => $post->geolocation_country_long,
		), $location, $post );

		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'address_formats' ) );

		$location = WC()->countries->get_formatted_address( $address );

		remove_filter( 'woocommerce_localisation_address_formats', array( $this, 'address_formats' ) );

		return $location;
	}

	/**
	 * Temporarily filter out all other address formats except the one set in the customizer.
	 *
	 * @since 3.0.0
	 *
	 * @param array $formats
	 * @return array
	 */
	public function address_formats( $formats ) {
		return array(
			'default' => get_theme_mod( 'resume-display-address-format', '{city}, {state}' ),
		);
	}

	/**
	 * Register Resume-specific widgets.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function widgets_init() {
		$widgets = array(
			'class-widget-resumes-map.php',
			'class-widget-resumes-recent.php',
			'class-widget-resumes-spotlight.php',
			'class-widget-resume-links.php',
			'class-widget-resume-categories.php',
			'class-widget-resume-skills.php',
			'class-widget-resume-file.php',
		);

		foreach ( $widgets as $widget ) {
			require_once( get_template_directory() . '/inc/integrations/wp-job-manager-resumes/widgets/' . $widget );
		}

		register_widget( 'Jobify_Widget_Resumes' );
		register_widget( 'Jobify_Widget_Resumes_Map' );
		register_widget( 'Jobify_Widget_Resumes_Spotlight' );

		register_widget( 'Jobify_Widget_Resume_Links' );
		register_widget( 'Jobify_Widget_Resume_Categories' );

		if ( get_option( 'resume_manager_enable_skills' ) ) {
			register_widget( 'Jobify_Widget_Resume_Skills' );
		}

		if ( get_option( 'resume_manager_enable_resume_upload' ) ) {
			register_widget( 'Jobify_Widget_Resume_File' );
		}

		if ( 'side' == get_theme_mod( 'resume-display-sidebar', 'top' ) ) {
			register_sidebar( array(
				'name'          => __( 'Resume Page Sidebar', 'jobify' ),
				'id'            => 'sidebar-single-resume',
				'description'   => __( 'Choose what should display on single resume listings.', 'jobify' ),
				'before_widget' => '<aside id="%1$s" class="widget widget--resume %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title widget-title--resume">',
				'after_title'   => '</h3>',
			) );
		} else {
			$columns = get_theme_mod( 'resume-display-sidebar-columns', 3 );

			for ( $i = 1; $i <= $columns; $i++ ) {
				register_sidebar( array(
					'name'          => sprintf( __( 'Resume Widget Column %s', 'jobify' ), $i ),
					'id'            => sprintf( 'single-resume-top-%s', $i ),
					'description'   => sprintf( __( 'Choose what should display on resume listings column #%s.', 'jobify' ), $i ),
					'before_widget' => '<aside id="%1$s" class="widget widget--resume widget--resume-top %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="widget-title widget-title--resume widget-title--resume-top">',
					'after_title'   => '</h3>',
				) );
			}
		}
	}
}
