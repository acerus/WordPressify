<?php
/**
 * Resume: File
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_File extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_file';
		$this->widget_description = __( 'Display the resume\'s file', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_file';
		$this->widget_name        = __( 'Jobify - Resume: File', 'jobify' );
		$this->settings           = array(
			'resume' => array(
				'std' => __( 'Resume', 'jobify' ),
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
		$resume_file = get_post_meta( get_the_ID(), '_resume_file', true );

		if ( ! $resume_file ) {
			return;
		}

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$content = sprintf(
			'<a rel="nofollow" href="%s" class="resume-file resume-file--%s">%s</a>',
			esc_url( get_resume_file_download_url( get_the_ID() ) ),
			substr( strrchr( $resume_file, '.' ), 1 ),
			sprintf( __( 'Resume.%s', 'jobify' ), substr( strrchr( $resume_file, '.' ), 1 ) )
		);

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
