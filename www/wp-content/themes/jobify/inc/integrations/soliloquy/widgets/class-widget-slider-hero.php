<?php
/**
 * Solioquy Hero Slider
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Slider_Hero extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_slider_hero';
		$this->widget_description = __( 'Display a "Hero" Soliloquy slider.', 'jobify' );
		$this->widget_id          = 'jobify_widget_slider_hero';
		$this->widget_name        = __( 'Jobify - Page: Solioquy Hero Slider', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'slider' => array(
				'type'    => 'select',
				'label'   => __( 'Slider:', 'jobify' ),
				'options' => jobify_slider_options(),
				'std'     => 0,
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

		$slider     = absint( $instance['slider'] );

		echo $before_widget;

		if ( function_exists( 'soliloquy_slider' ) ) {
			add_filter( 'soliloquy_output_caption', array( $this, 'soliloquy_output_caption' ), 10, 5 );

			soliloquy( $slider );

			remove_filter( 'soliloquy_output_caption', array( $this, 'soliloquy_output_caption' ), 10, 5 );
		}

		echo $after_widget;

		$content = apply_filters( 'jobify_widget_slider_hero', ob_get_clean(), $instance, $args );

		echo $content;
	}

	function soliloquy_output_caption( $output, $id, $item, $data, $i ) {
		$output = sprintf( '<a href="%s"><h2 class="soliloquy-caption-title">%s</h2>%s</a>', $item['link'], $item['title'], wpautop( $output ) );

		return $output;
	}
}
