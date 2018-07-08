<?php
/**
 * Restrict Content Pro
 */

class Jobify_Restrict_Content_Pro extends Jobify_Integration {

	public function __construct() {
		$this->includes = array(
			'widgets/class-widget-price-table-rcp.php'
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		$widgets = array(
			'jobify_widget_job_type',
			'jobify_widget_job_apply',
			'jobify_widget_job_company_logo',
			'jobify_widget_job_share',
			'jobify_widget_job_application_deadline',
			'jobify_widget_job_categories',
			'jobify_widget_job_company_social',
			'jobify_widget_job_more_jobs',
			'jobify_widget_job_location',
			'jobify_widget_job_tags',
			'jobify_widget_products',
			'jobify_widget_resume_categories',
			'jobify_widget_resume_file',
			'jobify_widget_resume_links',
			'jobify_widget_resume_skills',
		);

		foreach ( $widgets as $widget ) {
			add_filter( $widget . '_content', array( $this, 'widget_visibility' ), 10, 3 );
			add_filter( 'jobify_widget_settings_' . $widget, array( $this, 'content_restriction' ) );
		}

		add_filter( 'rcp_restricted_message', array( $this, 'rcp_restricted_message' ) );
	}

	public function content_restriction( $settings ) {
		$levels  = rcp_get_subscription_levels( 'all' );

		if ( empty( $levels ) ) {
			return $settings;
		}

		$keys    = wp_list_pluck( $levels, 'id' );
		$names   = wp_list_pluck( $levels, 'name' );

		if ( ! ( is_array( $keys ) && is_array( $names ) ) ) {
			return $settings;
		}

		$options = array_combine( $keys, $names );

		$settings['subscription'] = array(
			'label'   => __( 'Subscription Level Visibility:', 'jobify' ),
			'std'     => 0,
			'type'    => 'multicheck',
			'options' => $options,
		);

		return $settings;
	}

	/**
	 * Registers widgets, and widget areas for RCP
	 *
	 * @since Jobify 1.7.0
	 *
	 * @return void
	 */
	function widgets_init() {
		register_widget( 'Jobify_Widget_Price_Table_RCP' );
	}

	/**
	 * Filter Jobify widget output depending on RCP subscription level.
	 *
	 * @since Jobify 1.6.0
	 *
	 * @return $widget
	 */
	function widget_visibility( $content, $instance, $args ) {
		if ( ! isset( $instance['subscription'] ) ) {
			return $content;
		}

		$sub_level = maybe_unserialize( $instance['subscription'] );

		if ( ! is_array( $sub_level ) ) {
			$sub_level = array();
		}

		if ( ! in_array( rcp_get_subscription_id( get_current_user_id() ), $sub_level ) && ! empty( $sub_level ) ) {
			$content = $this->subscription_teaser();
		}

		return $content;
	}

	/**
	 * @unknown
	 *
	 * @since unknown
	 *
	 * @return string
	 */
	function subscription_teaser() {
		global $rcp_options;

		$message = isset( $rcp_options['paid_message'] ) ? $rcp_options['paid_message'] : __( 'Please upgrade your subscription level to view this content', 'jobify' );

		return rcp_format_teaser( $message );
	}

	/**
	 * Wrap the RCP teaser in a class so it can be targetted and styled.
	 *
	 * @since 3.2.0
	 * @param string $message
	 * @return string $message
	 */
	public function rcp_restricted_message( $message ) {
		if ( ! is_singular( 'job_listing' ) ) {
			return $message;
		}

		return '<div class="rcp-restrict-message">' . $message . '</div>';
	}

}
