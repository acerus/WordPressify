<?php
/**
 * Job: Location
 *
 * @since Jobify 1.8.2
 */
class Jobify_Widget_Job_Location extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_location';
		$this->widget_description = __( 'Display the job type', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_location';
		$this->widget_name        = __( 'Jobify - Job: Location', 'jobify' );
		$this->settings           = array(
			'job_listing' => array(
				'std' => __( 'Job', 'jobify' ),
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

		$location = jobify_get_formatted_address();

		$content .= '<div class="job-location">' . $location . '</div>';

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
