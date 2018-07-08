<?php
/**
 * Job/Resume: Logo
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Company_Logo extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_company_logo';
		$this->widget_description = __( 'Display the company logo or resume picture', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_company_logo';
		$this->widget_name        = __( 'Jobify - Job/Resume: Logo', 'jobify' );
		$this->settings           = array(
			'job_listing resume' => array(
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
		$output = $content = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		// job listing
		if ( 'job_listing' == get_post_type() ) {
			ob_start();
			jobify_the_company_logo( 'fullsize' );
			$logo = ob_get_clean();

			if ( class_exists( 'Astoundify_Job_Manager_Companies' ) && '' != jobify_get_the_company_name() ) {
				$companies   = Astoundify_Job_Manager_Companies::instance();
				$company_url = esc_url( $companies->company_url( jobify_get_the_company_name() ) );

				$content .= '<a href="' . esc_url( $company_url ) . '" target="_blank">' . $logo . '</a>';
			} else {
				$content .= $logo;
			}
		} else {
			ob_start();
			the_candidate_photo( 'fullsize' );
			$logo = ob_get_clean();

			$content .= $logo;
		}

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
