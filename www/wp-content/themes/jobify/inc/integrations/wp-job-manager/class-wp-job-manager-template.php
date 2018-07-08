<?php

class Jobify_WP_Job_Manager_Template {

	public function __construct() {
		// Global
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ), 15 );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_action( 'template_redirect', array( $this, 'job_archives' ) );

		// Single
		add_action( 'init', array( $this, 'single_job_listing_layout' ) );

		if ( ! get_option( 'job_application_form_for_url_method' ) ) {
			add_action( 'job_manager_application_details_url', array( $this, 'contact_wrapper_start' ), 0 );
			add_action( 'job_manager_application_details_url', array( $this, 'contact_wrapper_end' ), 10.00001 );
		}
	}

	public function contact_wrapper_start() {
		echo '<div class="job_manager_contact_details_inner">';
	}

	public function contact_wrapper_end() {
		echo '</div>';
	}

	public function body_class( $classes ) {
		$classes[] = 'single-listing-style-' . get_theme_mod( 'job-display-sidebar', 'top' );
		$classes[] = 'single-resume-style-' . get_theme_mod( 'job-display-sidebar', 'top' );

		$categories = true;
		$categories = $categories && get_option( 'job_manager_enable_categories' );
		$categories = $categories && ! is_tax( 'job_listing_category' );

		if ( $categories ) {
			$classes[] = 'wp-job-manager-categories-enabled';

			if ( get_option( 'job_manager_enable_default_category_multiselect' ) && ! is_page_template( 'page-templates/jobify.php' ) ) {
				$classes[] = 'wp-job-manager-categories-multi-enabled';
			}
		}

		if ( get_option( 'resume_manager_enable_categories' ) ) {
			$classes[] = 'wp-resume-manager-categories-enabled';

			if ( get_option( 'resume_manager_enable_default_category_multiselect' ) && ! is_page_template( 'page-templates/jobify.php' ) ) {
				$classes[] = 'wp-resume-manager-categories-multi-enabled';
			}
		}

		if ( get_post() ) {
			$apply = get_the_job_application_method();

			if ( $apply ) {
				$classes[] = 'wp-job-manager-apply-' . $apply->type;
			}
		}

		return $classes;
	}

	public function wp_enqueue_scripts() {
		wp_dequeue_style( 'wp-job-manager-frontend' );
		wp_dequeue_style( 'chosen' );
	}

	/**
	 * Registers widgets, and widget areas.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function widgets_init() {
		/** Widgets */
		$widgets = array(
			'class-widget-job-company-logo.php',
			'class-widget-job-type.php',
			'class-widget-job-location.php',
			'class-widget-job-apply.php',
			'class-widget-job-company-social.php',
			'class-widget-job-categories.php',
			'class-widget-job-more-jobs.php',
			'class-widget-job-share.php',

			'class-widget-jobs-recent.php',
			'class-widget-jobs-spotlight.php',
			'class-widget-jobs-search.php',
			'class-widget-jobs-map.php',

			'class-widget-search-hero.php',
		);

		foreach ( $widgets as $widget ) {
			require_once( get_template_directory() . '/inc/integrations/wp-job-manager/widgets/' . $widget );
		}

		unregister_widget( 'WP_Job_Manager_Widget_Recent_Jobs' );

		register_widget( 'Jobify_Widget_Job_Company_Logo' );
		register_widget( 'Jobify_Widget_Job_Type' );
		register_widget( 'Jobify_Widget_Job_Location' );
		register_widget( 'Jobify_Widget_Job_Apply' );
		register_widget( 'Jobify_Widget_Job_Company_Social' );
		register_widget( 'Jobify_Widget_Job_Categories' );
		register_widget( 'Jobify_Widget_Job_More_Jobs' );
		register_widget( 'Jobify_Widget_Job_Share' );

		register_widget( 'Jobify_Widget_Jobs' );
		register_widget( 'Jobify_Widget_Jobs_Spotlight' );
		register_widget( 'Jobify_Widget_Jobs_Search' );
		register_widget( 'Jobify_Widget_Stats' );
		register_widget( 'Jobify_Widget_Map' );
		register_widget( 'Jobify_Widget_Search_Hero' );

		if ( 'side' == get_theme_mod( 'job-display-sidebar', 'top' ) ) {
			register_sidebar( array(
				'name'          => __( 'Job Page Sidebar', 'jobify' ),
				'id'            => 'sidebar-single-job_listing',
				'description'   => __( 'Choose what should display on single job listings.', 'jobify' ),
				'before_widget' => '<aside id="%1$s" class="widget widget--job_listing %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title widget-title--job_listing">',
				'after_title'   => '</h3>',
			) );
		} else {
			$columns = get_theme_mod( 'job-display-sidebar-columns', 3 );

			for ( $i = 1; $i <= $columns + 1; $i++ ) {
				register_sidebar( array(
					'name'          => sprintf( __( 'Job Widget Column %s', 'jobify' ), $i ),
					'id'            => sprintf( 'single-job_listing-top-%s', $i ),
					'description'   => sprintf( __( 'Choose what should display on single job listings column #%s.', 'jobify' ), $i ),
					'before_widget' => '<aside id="%1$s" class="widget widget--job_listing widget--job_listing-top %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="widget-title widget-title--job_listing widget-title--job_listing-top">',
					'after_title'   => '</h3>',
				) );
			}
		}
	}

	/**
	 * When viewing a taxonomy archive, use the same template for all.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function job_archives() {
		global $wp_query;

		$taxonomies = array(
			'job_listing_category',
			'job_listing_region',
			'job_listing_type',
			'job_listing_tag',
		);

		if ( ! is_tax( $taxonomies ) ) {
			return;
		}

		locate_template( array( 'taxonomy-job_listing_category.php' ), true );

		exit();
	}

	public function single_job_listing_layout() {
		remove_action( 'single_job_listing_start', 'job_listing_company_display', 30 );
		add_action( 'single_job_listing_meta_end', array( $this, 'job_listing_company_name' ) );
	}

	public function job_listing_company_name() {
		if ( '' == jobify_get_the_company_name() ) {
			return;
		}
?>
<li class="job-company">
	<?php
	if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != jobify_get_the_company_name() ) :
		$companies   = Astoundify_Job_Manager_Companies::instance();
		$company_url = esc_url( $companies->company_url( jobify_get_the_company_name() ) );
	?>
		<a href="<?php echo $company_url; ?>" target="_blank"><?php jobify_the_company_name(); ?></a>
	<?php else : ?>
		<?php jobify_the_company_name(); ?>
	<?php endif; ?>
</li>
<?php
	}


}
