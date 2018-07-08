<?php
/**
 * WP Job Manager
 */

class Jobify_WP_Job_Manager extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-wp-job-manager-listing.php',
			'class-wp-job-manager-map.php',
			'class-wp-job-manager-template.php',
			'class-wp-job-manager-spotlight.php',
			'class-wp-job-manager-submission.php',
		);

		// add customizer support
		$this->has_customizer = true;

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		$this->map = new Jobify_WP_Job_Manager_Map();
		$this->template = new Jobify_WP_Job_Manager_Template();
		$this->spotlight = new JobifY_WP_Job_Manager_Spotlight();
		$this->submission = new Jobify_WP_Job_Manager_Submission();
	}

	public function setup_actions() {
		add_filter( 'job_manager_output_jobs_defaults', array( $this, 'job_manager_output_jobs_defaults' ) );
		add_filter( 'pre_get_posts', array( $this, 'archives_query' ) );

		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );

		add_filter( 'register_post_type_job_listing', array( $this, 'post_type_job_listing' ) );

		add_filter( 'submit_job_form_fields', array( $this, 'submit_job_form_fields' ) );
		add_action( 'job_manager_update_job_data', array( $this, 'update_job_data' ), 10, 2 );
		add_filter( 'job_manager_job_listing_data_fields', array( $this, 'job_listing_data_fields' ) );
		add_action( 'job_manager_save_job_listing', array( $this, 'save_job_listing' ), 10, 2 );

		add_action( 'jobify_output_job_results', array( $this, 'output_results' ) );

		add_action( 'job_manager_job_filters_search_jobs_end', array( $this, 'job_filters_after' ), 9 );
		add_action( 'resume_manager_resume_filters_search_resumes_end', array( $this, 'job_filters_after' ), 9 );

		// Job listing JSON-LD data.
		add_action( 'wpjm_get_job_listing_structured_data', array( $this, 'job_listing_structured_data' ), 10, 2 );
	}

	/**
	 * Sets up theme support.
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	public function add_theme_support() {
		add_theme_support( 'job-manager-templates' );
	}

	/**
	 * Job Listing post type arguments.
	 *
	 * @since Jobify 1.0.0
	 *
	 * @param array $args
	 * @return array $args
	 */
	function post_type_job_listing( $args ) {
		$args['supports'][] = 'thumbnail';

		return $args;
	}

	/**
	 * When viewing a taxonomy archive, make sure the job manager settings are respected.
	 *
	 * @since Jobify 1.0
	 *
	 * @param $query
	 * @return $query
	 */
	function archives_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$taxonomies = array(
			'job_listing_category',
			'job_listing_region',
			'job_listing_type',
			'job_listing_tag',
		);

		if ( is_tax( $taxonomies ) ) {
			$query->set( 'posts_per_page', get_option( 'job_manager_per_page' ) );
			$query->set( 'post_type', array( 'job_listing' ) );
			$query->set( 'post_status', array( 'publish' ) );
			$query->set( 'orderby', 'meta_key' );
			$query->set( 'meta_key', '_featured' );

			add_filter( 'posts_clauses', 'order_featured_job_listing' );

			if ( get_option( 'job_manager_hide_filled_positions' ) == 1 ) {
				$query->set( 'meta_query', array(
					array(
						'key'     => '_filled',
						'value'   => '1',
						'compare' => '!=',
					),
				) );
			}
		}

		return $query;
	}

	public function job_manager_output_jobs_defaults( $default ) {
		$type = get_queried_object();

		if ( is_tax( 'job_listing_type' ) ) {
			$default['job_types'] = $type->slug;
			$default['selected_job_types'] = $type->slug;
			$default['show_categories'] = true;
		} elseif ( is_tax( 'job_listing_category' ) ) {
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
			$categories = array_filter( array_map( 'esc_attr', $_GET['search_categories'] ), 'jobify_array_filter_deep' );

			if ( ! empty( $categories ) ) {
				$default['selected_category'] = $categories[0];
			}

			$default['show_categories'] = true;
			$default['categories'] = false;
		}

		return $default;
	}

	/**
	 * Add extra fields to the submission form.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function submit_job_form_fields( $fields ) {
		$fields['company']['company_website']['priority'] = 4.2;

		$fields['company']['company_description'] = array(
		  'label'       => _x( 'Description', 'company description on submission form', 'jobify' ),
		  'type'        => 'wp-editor',
		  'required'    => false,
		  'placeholder' => '',
		  'priority'    => 3.5,
		);

		$fields['company']['company_facebook'] = array(
		  'label'       => __( 'Facebook username', 'jobify' ),
		  'type'        => 'text',
		  'required'    => false,
		  'placeholder' => __( 'yourcompany', 'jobify' ),
		  'priority'    => 4.5,
		);

		$fields['company']['company_google'] = array(
		  'label'       => __( 'Google+ username', 'jobify' ),
		  'type'        => 'text',
		  'required'    => false,
		  'placeholder' => __( 'yourcompany', 'jobify' ),
		  'priority'    => 4.5,
		);

		$fields['company']['company_linkedin'] = array(
		  'label'       => __( 'LinkedIn username', 'jobify' ),
		  'type'        => 'text',
		  'required'    => false,
		  'placeholder' => __( 'yourcompany', 'jobify' ),
		  'priority'    => 4.6,
		);

		return $fields;
	}


	/**
	 * Save the extra frontend fields
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function update_job_data( $job_id, $values ) {
		update_post_meta( $job_id, '_company_description', $values['company']['company_description'] );
		update_post_meta( $job_id, '_company_facebook', $values['company']['company_facebook'] );
		update_post_meta( $job_id, '_company_google', $values['company']['company_google'] );
		update_post_meta( $job_id, '_company_linkedin', $values['company']['company_linkedin'] );

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_company_description', $values['company']['company_description'] );
			update_user_meta( get_current_user_id(), '_company_facebook', $values['company']['company_facebook'] );
			update_user_meta( get_current_user_id(), '_company_google', $values['company']['company_google'] );
			update_user_meta( get_current_user_id(), '_company_linkedin', $values['company']['company_linkedin'] );
		}
	}


	/**
	 * Add extra fields to the WordPress admin.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	function job_listing_data_fields( $fields ) {
		$fields['_company_description'] = array(
			'label' => __( 'Company Description', 'jobify' ),
			'placeholder' => '',
			'type'        => 'textarea',
		);

		$fields['_company_facebook'] = array(
			'label' => __( 'Company Facebook', 'jobify' ),
			'placeholder' => '',
		);

		$fields['_company_google'] = array(
			'label' => __( 'Company Google+', 'jobify' ),
			'placeholder' => '',
		);

		$fields['_company_linkedin'] = array(
			'label' => __( 'Company LinkedIn', 'jobify' ),
			'placeholder' => '',
		);

		return $fields;
	}


	/**
	 * Save the extra admin fields.
	 *
	 * WP Job Manager strips our tags out. Resave it after with the tags.
	 *
	 * @since Jobify 1.4.4
	 *
	 * @return void
	 */
	function save_job_listing( $job_id, $post ) {
		update_post_meta( $job_id, '_company_description', wp_kses_post( $_POST['_company_description'] ) );
	}


	public function output_results() {
		echo do_shortcode( apply_filters( 'jobify_job_archive_shortcode', '[jobs]' ) );
	}

	/**
	 * Add a submit button the filtering options.
	 *
	 * @since Jobify 1.0
	 *
	 * @return void
	 */
	public function job_filters_after() {
	?>
		<div class="search_submit">
			<input type="submit" name="submit" value="<?php echo esc_attr_e( 'Search', 'jobify' ); ?>" />
		</div>
	<?php
	}

	/**
	 * Add data to WPJM JSON-LD structured data.
	 *
	 * @since 3.8.0
	 *
	 * @param array   $data Structured Data.
	 * @param WP_Post $post WP Post object.
	 */
	public function job_listing_structured_data( $data, $post ) {
		$image = get_the_post_thumbnail_url( $post, 'full' );
		if ( $image ) {
			$data['image'] = array(
				'@type' => 'URL',
				'@id'   => esc_url( $image ),
			);
		}
		$listing = jobify_get_listing( $post );
		$cats = $listing->get_the_job_category();
		if ( $cats ) {
			$data['industry'] = $listing->get_the_job_category_names();
		}
		return $data;
	}

}
