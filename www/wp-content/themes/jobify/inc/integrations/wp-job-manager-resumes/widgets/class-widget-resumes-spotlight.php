<?php
/**
 * Page: Resume Spotlight
 *
 * @since Jobify 3.2.0
 */
class Jobify_Widget_Resumes_Spotlight extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--page-resumes-spotlights';
		$this->widget_description = __( 'Output a grid of spotlighted resumes.', 'jobify' );
		$this->widget_id          = 'jobify_resumes_spotlight';
		$this->widget_name        = __( 'Jobify - Page: Resumes Spotlight', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Resume Spotlight', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 3,
				'label' => __( 'Number of resumes to show:', 'jobify' ),
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

		$title   = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number  = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;

		$spotlight = jobify()->get( 'wp-job-manager' )->spotlight->get( array(
			'number' => $number,
			'post_type' => 'resume',
		) );

		if ( ! $spotlight->have_posts() ) {
			return;
		}

		echo $before_widget;
		?>

			<div class="container">

				<?php if ( $title ) { echo $before_title . $title . $after_title;}  ?>

				<div class="row resume-spotlights" data-columns>

					<?php while ( $spotlight->have_posts() ) : $spotlight->the_post(); ?>

						<?php
							add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
							get_template_part( 'content', 'single-resume-featured' );
							remove_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
						?>

					<?php endwhile; ?>

				</div>

			</div>

		<?php
		echo $after_widget;

		$content = apply_filters( $this->widget_id, ob_get_clean(), $instance, $args );

		echo $content;
	}

	public function excerpt_length() {
		return 20;
	}
}
