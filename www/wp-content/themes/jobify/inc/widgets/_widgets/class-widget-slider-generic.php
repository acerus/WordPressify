<?php
/**
 * Slider widget that will allow a slider shortcode to be full-width.
 *
 * @since Jobify 1.6
 */
class Jobify_Widget_Slider_Generic extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_slider_generic';
		$this->widget_description = __( 'Display any slider that supports shortcodes.', 'jobify' );
		$this->widget_id          = 'jobify_widget_slider_generic';
		$this->widget_name        = __( 'Jobify - Page: Slider', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'shortcode' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Slider Shortcode', 'jobify' ),
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

		if ( ! isset( $instance['shortcode'] ) ) {
			return;
		}

		echo $before_widget;
		echo do_shortcode( $instance['shortcode'] );
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_slider_generic', ob_get_clean(), $instance, $args );

		echo $content;
	}
}
