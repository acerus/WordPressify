<?php
/**
 * Home: Jobs Search
 *
 * @since Jobify 1.7.0
 */
class Jobify_Widget_Jobs_Search extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_jobs_search';
		$this->widget_description = __( 'Output search options to search jobs.', 'jobify' );
		$this->widget_id          = 'jobify_widget_jobs_search';
		$this->widget_name        = __( 'Jobify - Page: Jobs Search', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Search Jobs', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		ob_start();

		extract( $args );

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $before_widget;
		?>

			<div class="container">

				<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

				<div class="row">
					<?php locate_template( array( 'job-filters-flat.php', 'job-filters.php' ), true, false ); ?>
				</div>

			</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo apply_filters( 'jobify_widget_jobs_search', $content );
	}
}
