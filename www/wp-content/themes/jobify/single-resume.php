<?php
/**
 * Single Resume
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */

global $post;

get_header();
?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_job_manager_template_part( 'content-single', 'resume' ); ?>

	<?php endwhile; ?>

<?php get_footer();
