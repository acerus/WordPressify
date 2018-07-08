<?php
/**
 * Job: Type
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Type extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_type';
		$this->widget_description = __( 'Display the job type', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_type';
		$this->widget_name        = __( 'Jobify - Job: Type', 'jobify' );
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
		$output = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$types = jobify_get_the_job_types();
		$content = '';
		foreach( $types as $type ) {
			$content .= sprintf(
				'<div class="job-type %s term-%s">%s</div>',
				$type->slug,
				( $type ? sanitize_title( $type->term_id ) : '' ),
				$type->name
			);
		}
		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args, $this );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
