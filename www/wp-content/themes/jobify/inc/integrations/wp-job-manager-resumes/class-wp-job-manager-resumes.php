<?php
/**
 * Resume Manager
 */

class Jobify_WP_Resume_Manager extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-wp-job-manager-resumes-template.php'
		);

		// add customizer support
		$this->has_customizer = true;

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		$this->template = new Jobify_Job_Manager_Resumes_Template();
	}

	public function setup_actions() {
		add_filter( 'resume_manager_output_resumes_defaults', array( $this, 'resume_manager_output_resumes_defaults' ) );

		add_action( 'template_redirect', array( $this, 'resume_archives' ) );
		add_filter( 'pre_get_posts', array( $this, 'resume_archives_query' ) );

		add_filter( 'register_post_type_resume', array( $this, 'post_type_resume' ) );
	}

	public function resume_manager_output_resumes_defaults( $default ) {
		$type = get_queried_object();

		if ( is_tax( 'resume_category' ) ) {
			$default['show_categories'] = true;
			$default['categories'] = $type->slug;
			$default['selected_category'] = $type->slug;
		} elseif ( is_search() ) {
			$default['keywords'] = get_search_query();
			$default['show_filters'] = false;
		}

		if ( is_home() || jobify()->widgets->widgetized_pages->is() ) {
			$default['show_category_multiselect'] = false;
		}

		if ( isset( $_GET['search_categories'] ) ) {
			$category = esc_attr( $_GET['search_categories'] );

			$default['selected_category'] = $category;
			$default['show_categories'] = true;
		}

		return $default;
	}

	/**
	 * Resume post type arguments.
	 *
	 * @since Jobify 1.5.0
	 *
	 * @param array $args
	 * @return array $args
	 */
	function post_type_resume( $args ) {
		$args['exclude_from_search'] = false;

		return $args;
	}

	/**
	 * When viewing a taxonomy archive, use the same template for all.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function resume_archives() {
		global $wp_query;

		$taxonomies = array(
			'resume_skill'
		);

		if ( ! is_tax( $taxonomies ) ) {
			return;
		}

		locate_template( array( 'taxonomy-resume_category.php' ), true );

		exit();
	}


	/**
	 * When viewing a taxonomy archive, make sure the job manager settings are respected.
	 *
	 * @since Jobify 1.0
	 *
	 * @param $query
	 * @return $query
	 */
	function resume_archives_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$taxonomies = array(
			'resume_category'
		);

		if ( is_tax( $taxonomies ) ) {
			$query->set( 'posts_per_page', get_option( 'job_manager_per_page' ) );
			$query->set( 'post_type', array( 'resume' ) );
			$query->set( 'post_status', array( 'publish' ) );
		}

		return $query;
	}

}
