<?php
/**
 * Home: Jobs Map
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Map extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_map';
		$this->widget_description = __( 'Display a map with pins indicating areas with active job listings.', 'jobify' );
		$this->widget_id          = 'jobify_widget_map';
		$this->widget_name        = __( 'Jobify - Page: Jobs Map', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'filters' => array(
				'type'  => 'checkbox',
				'label' => __( 'Show search filters', 'jobify' ),
				'std'   => 1,
			),
			'margin' => array(
				'type'  => 'checkbox',
				'label' => __( 'Enable standard widget spacing', 'jobify' ),
				'std'   => 1,
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
		ob_start();

		extract( $args );

		$filters = true;

		if ( isset( $instance['filters'] ) && '' == $instance['filters'] ) {
			$filters = false;
		}

		if ( isset( $instance['margin'] ) && '' == $instance['margin'] ) {
			$before_widget = str_replace( 'widget--home ', 'widget--home widget--home--no-margin ', $before_widget );
		}

		$before_widget = str_replace( 'jobify_widget_map', ( $filters ? 'filters' : 'no-filters' ) . ' jobify_widget_map', $before_widget );

		echo $before_widget;

		do_action( 'jobify_output_map' );
		do_action( 'jobify_output_job_results' );

		echo $after_widget;

		$content = apply_filters( 'jobify_widget_map', ob_get_clean(), $instance, $args );

		echo $content;
	}
}
