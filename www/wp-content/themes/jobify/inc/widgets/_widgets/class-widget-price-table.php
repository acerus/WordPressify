<?php
/**
 * Price table populated by Price Options
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Price_Table extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_price_table';
		$this->widget_description = __( 'Output the price table (based on the "Price Table" widget area)', 'jobify' );
		$this->widget_id          = 'jobify_widget_price_table';
		$this->widget_name        = __( 'Jobify - Page: Price Table', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Plans and Pricing', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 4,
				'std'   => '',
				'label' => __( 'Description:', 'jobify' ),
			),
			'nothing' => array(
				'type' => 'description',
				'std'  => __( 'Drag "Price Option" widgets to the "Price Table" widget area to populate this widget.', 'jobify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$description = isset( $instance['description'] ) ? $instance['description'] : '';

		echo $before_widget;
		?>

		<div class="container">

			<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

			<?php if ( $description ) : ?>
				<p class="widget-description widget-description--home"><?php echo $description; ?></p>
			<?php endif; ?>

			<div class="price-table row" data-columns>
				<?php dynamic_sidebar( 'widget-area-price-options' ); ?>
			</div>

		</div>

		<?php
		echo $after_widget;
	}
}
