<?php
/**
 * Price Table for Restrict Content Pro
 *
 * Automatically populated with subscriptions.
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Price_Table_RCP extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_price_table_rcp';
		$this->widget_description = __( 'Outputs subscription options for Restrict Content Pro', 'jobify' );
		$this->widget_id          = 'jobify_widget_price_table_rcp';
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
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		global $rcp_options;

		extract( $args );

		$title        = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$description  = isset( $instance['description'] ) ? $instance['description'] : '';
		$levels       = rcp_get_subscription_levels( 'active' );
		$url          = esc_url( get_permalink( $rcp_options['registration_page'] ) );

		if ( ! $levels ) {
			return;
		}

		echo $before_widget;
		?>

		<div class="container">

			<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

			<?php if ( $description ) : ?>
				<p class="widget-description widget-description--home"><?php echo $description; ?></p>
			<?php endif; ?>

			<div class="price-table row" data-columns>

				<?php foreach ( $levels as $key => $level ) : ?>
					<?php if ( rcp_show_subscription_level( $level->id ) ) : ?>

					<div class="price-option">
						<div class="price-option__title">
							<?php echo stripslashes( $level->name ); ?>
						</div>

						<div class="price-option__description">
							<h2 class="price-option__price"><?php echo $level->price > 0 ? rcp_currency_filter( $level->price ) : __( 'free', 'jobify' ); ?></h2>

							<p class="price-option__duration"><?php echo $level->duration > 0 ? $level->duration . '&nbsp;' . rcp_filter_duration_unit( $level->duration_unit, $level->duration ) : __( 'unlimited', 'jobify' ); ?></p>

							<?php echo rcp_get_subscription_description( $level->id ); ?>

							<p><a href="<?php echo $url; ?>" class="button button--type-action button--style-inverted"><?php _e( 'Get Started', 'jobify' ); ?></a></p>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>

			</div>

		</div>

		<?php
		echo $after_widget;

		echo $content;
	}
}
