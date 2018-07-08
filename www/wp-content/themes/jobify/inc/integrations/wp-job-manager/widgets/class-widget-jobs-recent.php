<?php
/**
 * Home: Recent Jobs
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Jobs extends Jobify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_jobs';
		$this->widget_description = __( 'Output a list of recent jobs.', 'jobify' );
		$this->widget_id          = 'jobify_widget_jobs';
		$this->widget_name        = __( 'Jobify - Page: Recent Jobs', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Recent Jobs', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'filters' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Show Filters', 'jobify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of jobs to show:', 'jobify' ),
			),
			'spotlight' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display the "Job Spotlight" section', 'jobify' ),
			),
			'spotlight-title' => array(
				'type'  => 'text',
				'std'   => __( 'Job Spotlight', 'jobify' ),
				'label' => __( 'Spotlight Title:', 'jobify' ),
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

		$title   = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$number  = isset( $instance['number'] ) ? $instance['number'] : 5;
		$filters = ! isset( $instance['filters'] ) || '' != $instance['filters'] ? true : false;
		$has_spotlight   = ! isset( $instance['spotlight'] ) || '' != $instance['spotlight'] ? true : false;
		$spotlight_title = isset( $instance['spotlight-title'] ) ? esc_attr( $instance['spotlight-title'] ) : __( 'Job Spotlight', 'jobify' );

		if ( $has_spotlight ) {
			$spotlight = jobify()->get( 'wp-job-manager' )->spotlight->get();
			$has_spotlight = $spotlight->have_posts();
		}

		echo $before_widget;
		?>

			<div class="container">

				<div class="row">

					<div class="recent-jobs <?php if ( $filters ) : ?>filters<?php endif; ?> <?php if ( $has_spotlight ) : ?>has-spotlight col-lg-8 col-sm-12<?php else : ?>col-xs-12<?php endif; ?>">
						<?php
						if ( $title ) { echo $before_title . $title . $after_title;
						}
							echo do_shortcode( sprintf( '[jobs show_filters="%s" per_page="%d"]', $filters, $number ) );
						?>
					</div>

					<?php if ( $has_spotlight ) : ?>
					<div class="job-spotlight-wrapper col-lg-4 col-sm-12">
						<h3 class="widget-title widget-title--home"><?php echo esc_attr( $spotlight_title ); ?></h3>

						<?php
						while ( $spotlight->have_posts() ) : $spotlight->the_post();
							add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
						?>
						<?php get_template_part( 'content', 'single-job-featured' ); ?>
						<?php
						remove_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
							endwhile;
						?>
					</div>
					<?php endif; ?>

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
