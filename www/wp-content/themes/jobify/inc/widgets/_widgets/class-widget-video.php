<?php
/**
 * Video Widget
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Video extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_video widget--home-video';
		$this->widget_description = __( 'Display a video via oEmbed with a title and description.', 'jobify' );
		$this->widget_id          = 'jobify_widget_video';
		$this->widget_name        = __( 'Jobify - Page: Video', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'How it Works', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 8,
				'std'   => '',
				'label' => __( 'Description:', 'jobify' ),
			),
			'video' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Video URL:', 'jobify' ),
			),
		);
		$this->control_ops = array(
			'width'  => 400,
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
		global $wp_embed;

		ob_start();

		extract( $args );

		$title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$video       = isset( $instance['video'] ) ? esc_url( $instance['video'] ) : '';

		echo $before_widget;
		?>

		<div class="container">
			<div class="row">
				<div class="video-description col-xs-12 col-md-6 col-lg-7">
					<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

					<?php if ( $description ) : ?>
						<p class="widget-description widget-description--home"><?php echo wpautop( $description ); ?></p>
					<?php endif; ?>
				</div>

				<div class="video-preview col-xs-12 col-md-6 col-lg-5">
					<?php echo $wp_embed->run_shortcode( '[embed]' . $video . '[/embed]' ); ?>
				</div>
			</div>
		</div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_video', ob_get_clean(), $instance, $args );

		echo $content;
	}
}
