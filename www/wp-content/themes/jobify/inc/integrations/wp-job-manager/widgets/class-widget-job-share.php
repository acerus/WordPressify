<?php
/**
 * Job/Resume: Share
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Share extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_share';
		$this->widget_description = __( 'Display job/resume sharing options', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_share';
		$this->widget_name        = __( 'Jobify - Job/Resume: Share', 'jobify' );
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

		ob_start();
		do_action( 'jobify_share_object' );
		$content = ob_get_clean();

		if ( '' == $content ) {
			return;
		}

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
