<?php
/**
 * Single Job
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */

get_header();
?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php the_content(); ?>

	<?php endwhile; ?>

<?php get_footer();
