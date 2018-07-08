<?php
/**
 * Job: Company Listings
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_More_Jobs extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_more_jobs';
		$this->widget_description = __( 'Display a link to more jobs from the company', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_more_jobs';
		$this->widget_name        = __( 'Jobify - Job: Company Listings', 'jobify' );
		$this->settings           = array(
			'job_listing' => array(
				'std' => __( 'Job or Resume', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'jobify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		if ( ! class_exists( 'Astoundify_Job_Manager_Companies' ) ) {
			return;
		}

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$companies   = Astoundify_Job_Manager_Companies::instance();
		$company_url = esc_url( $companies->company_url( jobify_get_the_company_name() ) );

		$output = $content = '';

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$content .= sprintf(
			'<a href="%s" title="%s">%s</a>',
			esc_url( $company_url ),
			sprintf( __( 'More jobs by %s', 'jobify' ), jobify_get_the_company_name() ),
			__( 'More Jobs', 'jobify' )
		);

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
