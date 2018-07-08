<?php
/**
 * Callout
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Callout extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_callout widget--home-callout';
		$this->widget_description = __( 'Display call-out area with a bit of text and a button.', 'jobify' );
		$this->widget_id          = 'jobify_widget_callout';
		$this->widget_name        = __( 'Jobify - Page: Callout', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 4,
				'std'   => null,
				'label' => __( 'Description:', 'jobify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => 'Learn More',
				'label' => __( 'Button Label:', 'jobify' ),
			),
			'button-url' => array(
				'type'  => 'text',
				'std'   => null,
				'label' => __( 'Button URL:', 'jobify' ),
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

		$button_label = isset( $instance['title'] ) ? $instance['title'] : null;
		$button_url = isset( $instance['button-url'] ) ? esc_url( $instance['button-url'] ) : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';

		echo $before_widget;
		?>

<div class="container">
	<div class="callout-container">
		<div class="col-xs-12 col-md-8 callout-description">
			<?php echo wpautop( $description ); ?>
		</div><div class="col-xs-12 col-md-4 callout-action">
			<a href="<?php echo esc_url( $button_url ); ?>" class="button"><?php echo esc_attr( $button_label ); ?></a>
		</div>
	</div>
</div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_callout', ob_get_clean(), $instance, $args );

		echo $content;
	}
}
