<?php
/**
 * Company Testimonials
 *
 * Use category "company" with Testimonials by WooThemes
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Companies extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_companies widget--home-companies';
		$this->widget_description = __( 'Display a slider of company logos you have helped find jobs.', 'jobify' );
		$this->widget_id          = 'jobify_widget_companies';
		$this->widget_name        = __( 'Jobify - Page: Companies Helped', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Companies We&#39;ve Helped', 'jobify' ),
				'label' => __( 'Title', 'jobify' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 4,
				'std'   => 'Some of the companies we&#39;ve helped recruit excellent applicants over the years.',
				'label' => __( 'Description', 'jobify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 12,
				'label' => __( 'Number of companies to show.', 'jobify' ),
			),
			'autoPlay' => array(
				'type' => 'checkbox',
				'label' => __( 'Automatically rotate company logos.', 'jobify' ),
				'std' => 0,
			),
			'category' => array(
				'type' => 'description',
				'std' => sprintf( __( 'Assign <a href="%1$s">Testimonials</a> to the <a href="%2$s">Company</a> category.', 'jobify' ), esc_url( admin_url( 'post-new.php?post_type=testimonial' ) ), esc_url( admin_url( 'edit-tags.php?taxonomy=testimonial-category&post_type=testimonial' ) ) ),
			),
		);

		add_filter( 'jobify_js_settings', array( $this, 'jobify_js_settings' ) );

		parent::__construct();
	}

	/**
	 * Update the global javascript settings based on the widget settings.
	 *
	 * @since 3.7.0
	 *
	 * @param array $args
	 * @return array $args
	 */
	public function jobify_js_settings( $args ) {
		$settings = $this->get_settings();

		if ( ! isset( $settings[ $this->number ] ) ) {
			return $args;
		}

		$settings = $settings[ $this->number ];

		$args['widgets'][ $this->id_base ] = array(
			'autoPlay' => isset( $settings['autoPlay'] ) && '1' === esc_attr( $settings['autoPlay'] ) ? true : false,
		);

		return $args;
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

		$title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : false, $instance, $this->id_base );
		$number      = isset( $instance['number'] ) ? absint( $instance['number'] ) : 1;
		$description = isset( $instance['description'] ) ? esc_attr( $instance['description'] ) : false;

		echo $before_widget;
		?>

		<div class="container">

			<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

			<?php if ( $description ) : ?>
				<p class="widget-description widget-description--home"><?php echo $description; ?></p>
			<?php endif; ?>

			<div class="company-slider">
				<?php
					do_action( 'woothemes_testimonials', array(
						'category' => 'company',
						'limit'    => $number,
						'size'     => array( 99999, 9999 ),
						'before'   => '',
						'after'    => '',
					) );
				?>
			</div>

		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo $content;
	}
}
