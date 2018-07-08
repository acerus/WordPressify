<?php
/**
 * Resume: Categories
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_Categories extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_categories';
		$this->widget_description = __( 'Display the resume\'s categories', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_categories';
		$this->widget_name        = __( 'Jobify - Resume: Categories', 'jobify' );
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

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		$output = $content = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$categories = get_the_terms( get_the_ID(), 'resume_category' );

		// Only if category available.
		if ( $categories ) {
			$output .= $args['before_widget'];

			if ( $title ) {
				$output .= $args['before_title'] . $title . $args['after_title'];
			}

			$content .= '<div class="resume-categories">';

			foreach ( $categories as $category ) {
				$content .= sprintf(
					'<a href="%s" class="resume-category">%s</a>',
					esc_url( get_term_link( $category, 'resume_category' ) ),
					esc_attr( $category->name )
				);
			}

			$content .= '</div>';

			$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

			$output .= $args['after_widget'];
		}

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
