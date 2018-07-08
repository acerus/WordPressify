<?php
/**
 * Job: Categories
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Job_Categories extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_job_categories';
		$this->widget_description = __( 'Display the job\'s categories', 'jobify' );
		$this->widget_id          = 'jobify_widget_job_categories';
		$this->widget_name        = __( 'Jobify - Job: Categories', 'jobify' );
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
		if ( ! get_option( 'job_manager_enable_categories' ) ) {
			return;
		}

		$post = get_post();

		$output = $content = '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$categories = get_the_terms( $post->ID, 'job_listing_category' );

		if ( ! $categories ) {
			return;
		}

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$content .= '<div class="job_listing-categories">';

		foreach ( $categories as $category ) {
			$content .= sprintf(
				'<a href="%s" class="job-category">%s</a>',
				esc_url( get_term_link( $category, 'job_listing_category' ) ),
				esc_attr( $category->name )
			);
		}

		$content .= '</div>';

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
