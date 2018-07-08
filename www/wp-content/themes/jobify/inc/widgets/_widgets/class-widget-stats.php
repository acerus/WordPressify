<?php
/**
 * Site Stats
 *
 * @since Jobify 1.0
 */
class Jobify_Widget_Stats extends Jobify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'jobify_widget_stats widget--home-stats';
		$this->widget_description = __( 'Display useful statistics about your website.', 'jobify' );
		$this->widget_id          = 'jobify_widget_stats';
		$this->widget_name        = __( 'Jobify - Page: Stats', 'jobify' );
		$this->settings           = array(
			'home widgetized' => array(
				'std' => __( 'Homepage/Widgetized', 'jobify' ),
				'type' => 'widget-area',
			),
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Site Stats', 'jobify' ),
				'label' => __( 'Title:', 'jobify' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 4,
				'std'   => 'Here we list our site stats and how many people we&#39;ve helped find a job and companies have
found recruits',
				'label' => __( 'Description:', 'jobify' ),
			),
			'show' => array(
				'type'    => 'multicheck',
				'label'   => __( 'Stats to Show:', 'jobify' ),
				'std'     => array( 'jobs', 'filled', 'companies', 'users' ),
				'options' => array(
					'jobs'      => __( 'Jobs', 'jobify' ),
					'filled'    => __( 'Positions Filled', 'jobify' ),
					'companies' => __( 'Companies', 'jobify' ),
					'users'     => __( 'Users', 'jobify' ),
					'resumes'   => __( 'Resumes', 'jobify' ),
				),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		global $wpdb;

		ob_start();

		extract( $args );

		$title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		$show        = isset( $instance['show'] ) ? maybe_unserialize( $instance['show'] ) : array( 'jobs', 'filled', 'companies', 'users' );

		if ( in_array( 'jobs', $show ) ) {
			$count = wp_count_posts( 'job_listing' );

			if ( ! isset( $count->publish ) ) {
				unset( $show[ array_search( 'jobs', $show ) ] );
			} else {
				$jobs_posted = apply_filters( 'jobify_stats_jobs_posted', $count->publish );
			}
		}

		if ( in_array( 'resumes', $show ) ) {
			$count = wp_count_posts( 'resume' );

			if ( ! isset( $count->publish ) ) {
				unset( $show[ array_search( 'resumes', $show ) ] );
			} else {
				$resumes_posted = apply_filters( 'jobify_stats_resumes_posted', $count->publish );
			}
		}

		if ( in_array( 'filled', $show ) ) {
			$jobs_filled = $wpdb->get_var(
				"SELECT COUNT(*)
				 FROM $wpdb->postmeta
				 WHERE meta_key = '_filled'
				 AND meta_value = '1'"
			);

			$jobs_filled = apply_filters( 'jobify_stats_jobs_filled', $jobs_filled );
		}

		if ( in_array( 'companies', $show ) ) {
			$companies   = $wpdb->get_col(
				"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
				 LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = '_company_name'
				 AND p.post_status = 'publish'
				 AND p.post_type = 'job_listing'"
			);
			$companies   = apply_filters( 'jobify_stats_companies', count( array_unique( $companies ) ) );
		}

		if ( in_array( 'users', $show ) ) {
			$users       = count_users();
			$registered  = apply_filters( 'jobify_stats_users', $users['total_users'] );
		}

		$columns     = ceil( 12 / count( $show ) );

		if ( 5 == count( $show ) ) {
			$columns = 2;
		}

		echo $before_widget;
		?>

		<div class="container">

			<?php if ( $title ) { echo $before_title . $title . $after_title;} ?>

			<?php if ( $description ) : ?>
				<p class="widget-description widget-description--home"><?php echo $description; ?></p>
			<?php endif; ?>

			<ul class="job-stats row showing-<?php echo count( $show ); ?>">

				<?php if ( in_array( 'jobs', $show ) ) : ?>
				<li class="job-stat col-md-<?php echo $columns; ?> col-sm-6 col-xs-12">
					<strong><?php echo number_format_i18n( $jobs_posted ); ?></strong>
					<?php echo _n( 'Job Posted', 'Jobs Posted', $jobs_posted, 'jobify' ); ?>
				</li><?php endif; ?><?php if ( in_array( 'resumes', $show ) ) : ?><li class="job-stat col-md-<?php echo $columns; ?> col-sm-6 col-xs-12">
					<strong><?php echo number_format_i18n( $resumes_posted ); ?></strong>
					<?php echo _n( 'Resume Posted', 'Resumes Posted', $resumes_posted, 'jobify' ); ?>
				</li><?php endif; ?><?php if ( in_array( 'filled', $show ) ) : ?><li class="job-stat col-md-<?php echo $columns; ?> col-sm-6 col-xs-12">
					<strong><?php echo number_format_i18n( $jobs_filled ); ?></strong>
					<?php echo _n( 'Job Filled', 'Jobs Filled', $jobs_filled, 'jobify' ); ?>
				</li><?php endif; ?><?php if ( in_array( 'companies', $show ) ) : ?><li class="job-stat col-md-<?php echo $columns; ?> col-sm-6 col-xs-12">
					<strong><?php echo number_format_i18n( $companies ); ?></strong>
					<?php echo _n( 'Company', 'Companies', $companies, 'jobify' ); ?>
				</li><?php endif; ?><?php if ( in_array( 'users', $show ) ) : ?><li class="job-stat col-md-<?php echo $columns; ?> col-sm-6 col-xs-12">
					<strong><?php echo number_format_i18n( $registered ); ?></strong>
					<?php echo _n( 'Member', 'Members', $registered, 'jobify' ); ?>
				</li>
				<?php endif; ?>

			</ul>

		</div>

		<?php
		echo $after_widget;

		$content = apply_filters( 'jobify_widget_stats', ob_get_clean(), $instance, $args );

		echo $content;
	}
}
