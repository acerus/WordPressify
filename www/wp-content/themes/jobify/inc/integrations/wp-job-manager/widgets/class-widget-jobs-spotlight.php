<?php
/**
 * Home: Job Spotlight
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Jobs_Spotlight extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_jobs_spotlight widget--home-job-spotlights';
		$this->widget_description = __( 'Output a grid of spotlighted jobs.', 'jobify' );
		$this->widget_id          = 'jobify_widget_jobs_spotlight';
		$this->widget_name        = __( 'Jobify - Page: Jobs Spotlight', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Job Spotlight', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 3,
				'label' => __( 'Number of jobs to show:', 'jobify' ),
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
		) );

		if ( ! $spotlight->have_posts() ) {
			return;
		}

		echo $before_widget;
		?>

			<div class="container">

				<?php if ( $title ) { echo $before_title . $title . $after_title;}  ?>

				<div class="row job-spotlights" data-columns>

					<?php while ( $spotlight->have_posts() ) : $spotlight->the_post(); ?>

						<?php
							add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
							get_template_part( 'content', 'single-job-featured' );
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
