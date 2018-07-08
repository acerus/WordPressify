<?php
/**
 * Single Company
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

get_header(); ?>

	<?php the_post(); ?>
	<header class="page-header">
		<h1 class="page-title"><?php printf( __( 'Jobs at %s', 'jobify' ), esc_attr( urldecode( get_query_var( apply_filters( 'wp_job_manager_companies_company_slug', 'company' ) ) ) ) ); ?></h1>

		<h2 class="page-subtitle"><strong><?php printf( _n( '%d Job Available', '%d Jobs Available', $wp_query->found_posts, 'jobify' ), $wp_query->found_posts ); ?></strong> <?php if ( jobify_get_the_company_tagline( get_the_ID() ) ) : ?>&bull; <?php jobify_the_company_tagline( '', '', true, get_the_ID() ); ?><?php endif; ?></h2>
	</header>
	<?php rewind_posts(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="container" role="main">
				
			<div class="company-profile row">
				<?php if ( jobify_get_the_company_description() ) : ?>
				<div class="col-xs-12 job-company-about">
					<h2 class="widget-title widget-title--job_listing-top"><?php printf( __( 'About %s', 'jobify' ), jobify_get_the_company_name() ); ?></h2>

					<?php jobify_the_company_description(); ?>
				</div>
				<?php endif; ?>

				<div class="company-profile-jobs col-md-10 col-sm-8 col-xs-12">
					<?php if ( have_posts() ) : ?>
					<div class="job_listings">
						<ul class="job_listings">
							<?php while ( have_posts() ) : the_post(); ?>
								<?php get_job_manager_template_part( 'content', 'job_listing' ); ?>
							<?php endwhile; ?>
						</ul>
					</div>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
				</div>

				<div class="company-profile-info job-meta col-md-2 col-sm-4 col-xs-4">

					<?php
						$args = array(
							'before_widget' => '<aside class="widget widget--job_listing">',
							'after_widget'  => '</aside>',
							'before_title'  => '<h3 class="widget-title widget-title--job_listing">',
							'after_title'   => '</h3>',
						);

						the_widget( 'Jobify_Widget_Job_Company_Logo', array(), $args );

						the_widget(
							'Jobify_Widget_Job_Company_Social',
							array(
								'title' => __( 'Company Social', 'jobify' ),
							),
							$args
						);
					?>

				</div>

			</div>
		</div><!-- #content -->

		<?php do_action( 'jobify_loop_after' ); ?>
	</div><!-- #primary -->

<?php get_footer(); ?>
