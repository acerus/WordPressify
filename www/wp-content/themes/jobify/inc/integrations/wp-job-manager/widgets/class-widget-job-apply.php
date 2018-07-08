<?php
/**
 * Job/Resume: Actions
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Apply extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_apply';
		$this->widget_description = __( 'Display the job/resume action buttons', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_apply';
		$this->widget_name        = __( 'Jobify - Job/Resume: Actions', 'jobify' );
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
		if ( 'preview' == get_post()->post_status ) {
			return;
		}

		$output = $content = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		// job listing
		if ( 'job_listing' == get_post_type() ) {
			if ( candidates_can_apply() && get_post()->post_status == 'publish' ) {
				ob_start();
				get_job_manager_template( 'job-application.php' );
				$content .= ob_get_clean();
			}

			if ( '' != jobify_get_the_company_video() ) {
				$content .= '<a href="#company-video" class="button view-video popup-trigger">' . __( 'Watch Video', 'jobify' ) . '</a>';

				// so embeds run
				ob_start();
				jobify_the_company_video();
				$video = ob_get_clean();

				$content .= '<div id="company-video" class="modal">' . $video . '</div>';
			}
		} else {
			ob_start();
			get_job_manager_template(
				'contact-details.php',
				array(
					'post' => get_post(),
				),
				'resume_manager',
				RESUME_MANAGER_PLUGIN_DIR . '/templates/'
			);
			$content .= ob_get_clean();

			if ( '' != get_the_candidate_video() ) {
				$content .= '<a href="#candidate-video" class="button view-video popup-trigger">' . __( 'Video Resume', 'jobify' ) . '</a>';

				// so embeds run
				ob_start();
				the_candidate_video();
				$video = ob_get_clean();

				$content .= '<div id="candidate-video" class="modal">' . $video . '</div>';
			}
		}// End if().

		// legacy
		ob_start();
		do_action( 'jobify_widget_job_apply_after' );
		$after = ob_get_clean();

		$output .= apply_filters( $this->widget_id . '_content', $content . $after, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
