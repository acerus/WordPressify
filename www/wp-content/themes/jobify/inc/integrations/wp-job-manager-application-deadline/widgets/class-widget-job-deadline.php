<?php
/**
 * Job: Type
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Deadline extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_application_deadline';
		$this->widget_description = __( 'Display the job application deadline', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_application_deadline';
		$this->widget_name        = __( 'Jobify - Job: Application Deadline', 'jobify' );
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

		$deadline = get_post_meta( get_the_ID(), '_application_deadline', true );

		$expiring = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= -2 );
		$expired  = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= 0 );

		if ( ! $deadline ) {
			return;
		}

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$content .= sprintf(
			'<div class="application-deadline %s %s">%s</div>',
			( $expiring ? 'expiring' : '' ),
			( $expired ? 'expired' : '' ),
			date_i18n( __( 'M j, Y', 'jobify' ), strtotime( $deadline ) )
		);

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
