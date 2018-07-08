<?php
/**
 * Template Name: Page: Home
 *
 * @package Jobify
 * @since Jobify 1.0
 */

get_header(); ?>

	<div id="primary" role="main">

		<?php if ( jobify()->get( 'woocommerce' ) ) : ?>
			<?php wc_print_notices(); ?>
		<?php endif; ?>

		<?php
		if ( ! dynamic_sidebar( 'widget-area-front-page' ) ) :
			the_widget(
				'Jobify_Widget_Map',
				array(
					'filters' => 1,
				),
				array(
				'before_widget' => '<section class="widget widget--home jobify_widget_map">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title widget-title--hom">',
				'after_title'   => '</h3>',
				)
			);

			the_widget(
				'Jobify_Widget_Jobs',
				array(
					'title' => 'Recent Jobs',
					'filters' => 0,
					'number' => 5,
					'spotlight' => 1,
					'spotlight-title' => 'Job Spotlight',
				),
				array(
				'before_widget' => '<section class="widget widget--home jobify_widget_jobs">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title widget-title--home">',
				'after_title'   => '</h3>',
				)
			);
			endif;
		?>

	</div><!-- #primary -->

<?php get_footer(); ?>
