<?php
/**
 * Resume: Links
 *
 * @since Jobify 1.6.0
 */
class Jobify_Widget_Resume_Links extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_resume_links';
		$this->widget_description = __( 'Display the resume\'s links', 'jobify' );
		$this->widget_id          = 'jobify_widget_resume_links';
		$this->widget_name        = __( 'Jobify - Resume: Links', 'jobify' );
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
		$items = get_post_meta( get_the_ID(), '_links', true );

		if ( ! $items ) {
			return;
		}

		$output .= $args['before_widget'];

		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$content .= '<ul class="resume-links">';

		foreach ( $items as $item ) {
			$parsed_url = parse_url( $item['url'] );
			$host = '';

			if ( isset( $parsed_url['host'] ) ) {
				$host = current( explode( '.', $parsed_url['host'] ) );
			}

			$content .= sprintf(
				'<li class="resume-link resume-link-%s"><a rel="nofollow" href="%s">%s</a></li>',
				esc_attr( sanitize_title( $host ) ),
				esc_url( $item['url'] ),
				esc_html( $item['name'] )
			);
		}

		echo '</ul>';

		$output .= apply_filters( $this->widget_id . '_content', $content, $instance, $args );

		$output .= $args['after_widget'];

		$output = apply_filters( $this->widget_id, $output, $instance, $args );

		echo $output;
	}
}
