<?php
/**
 * Resume: Skills
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_Skills extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_skills';
		$this->widget_description = __( 'Display the resume\'s skills', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_skills';
		$this->widget_name        = __( 'Jobify - Resume: Skills', 'jobify' );
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
		$tags  = get_the_terms( get_the_ID(), 'resume_skill' );

		if ( empty( $tags ) ) {
			return;
		}

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		foreach ( $tags as $tag ) {
			$content .= sprintf(
				'<a href="%s" class="job-tag">%s</a>',
				esc_url( get_term_link( $tag, 'resume_skill' ) ),
				esc_attr( $tag->name )
			);
		}

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
