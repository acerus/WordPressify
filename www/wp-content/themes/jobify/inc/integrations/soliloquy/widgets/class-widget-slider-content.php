<?php
/**
 * Solioquy Content Slider
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Slider extends Jobify_Widget {

	var $image;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_slider';
		$this->widget_description = __( 'Display a Soliloquy slider with captions.', 'jobify' );
		$this->widget_id          = 'jobify_widget_slider';
		$this->widget_name        = __( 'Jobify - Page: Solioquy Content Slider', 'jobify' );
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
			'background' => array(
				'type'    => 'text',
				'label'   => __( 'Background Image URL:', 'jobify' ),
				'std'     => '',
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
		$background = esc_url( $instance['background'] );

		echo $before_widget;

		if ( function_exists( 'soliloquy_slider' ) ) {
			add_filter( 'soliloquy_output_before_image', array( $this, 'soliloquy_output_before_image' ), 10, 5 );
			add_filter( 'soliloquy_output_after_image', array( $this, 'soliloquy_output_after_image' ), 10, 5 );
			add_filter( 'soliloquy_output_before_caption', array( $this, 'soliloquy_output_before_caption' ), 10, 5 );
			add_filter( 'soliloquy_output_after_caption', array( $this, 'soliloquy_output_after_image' ), 10, 5 );
			add_filter( 'soliloquy_output_caption', array( $this, 'soliloquy_output_caption' ), 10, 5 );

			soliloquy( $slider );

			remove_filter( 'soliloquy_output_before_image', array( $this, 'soliloquy_output_before_image' ), 10, 5 );
			remove_filter( 'soliloquy_output_after_image', array( $this, 'soliloquy_output_after_image' ), 10, 5 );
			remove_filter( 'soliloquy_output_before_caption', array( $this, 'soliloquy_output_before_caption' ), 10, 5 );
			remove_filter( 'soliloquy_output_after_caption', array( $this, 'soliloquy_output_after_image' ), 10, 5 );
			remove_filter( 'soliloquy_output_caption', array( $this, 'soliloquy_output_caption' ), 10, 5 );
		}

		?>
			<style>
			#<?php echo $this->id; ?> { background-image: url(<?php echo $background; ?>); background-size: cover; }
			</style>
		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_slider', ob_get_clean(), $instance, $args );

		echo $content;
	}

	function soliloquy_output_before_caption( $output, $id, $item, $data, $i ) {
		$output .= '<div class="soliloquy-caption-wrap">';

		return $output;
	}

	function soliloquy_output_before_image( $output, $id, $item, $data, $i ) {
		$output .= sprintf( '<div class="soliloquy-image-wrap"><a href="%s">', $item['link'] );

		return $output;
	}

	function soliloquy_output_after_image( $output, $id, $item, $data, $i ) {
		$output .= '</a></div>';

		return $output;
	}

	function soliloquy_output_caption( $output, $id, $item, $data, $i ) {
		$output = '<h2 class="soliloquy-caption-title">' . $item['title'] . '</h2>' . wpautop( $output );

		return $output;
	}
}

function jobify_slider_options() {
	$sliders  = new WP_Query( array(
		'post_type'              => array( 'soliloquy' ),
		'no_found_rows'          => true,
		'nopaging'               => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );

	if ( ! $sliders->have_posts() ) {
		return array();
	}

	$_sliders = array_combine(
		wp_list_pluck( $sliders->posts, 'ID' ),
		wp_list_pluck( $sliders->posts, 'post_title' )
	);

	return $_sliders;
}
