<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>" rel="bookmark" class="entry-header__featured-image">
				<span class="overlay"></span>
				<?php the_post_thumbnail( 'content-grid' ); ?>
			</a>
		<?php endif; ?>

		<h3 class="entry-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h3>

		<div class="entry-meta">
			<?php echo get_the_date(); ?>
			<?php if ( comments_open() ) : ?>
				<span class="comments-link">
					 |
					<?php comments_popup_link( __( '0 Comments', 'jobify' ), __( '1 Comment', 'jobify' ), __( '% Comments', 'jobify' ) ); ?>
				</span><!-- .comments-link -->
			<?php endif; ?>
		</div>
	</header><!-- .entry-header -->

	<div class="entry">
		<div class="entry-summary">
			<?php the_excerpt(); ?>

			<p><a href="<?php the_permalink(); ?>" rel="bookmark" class="button button--size-medium"><?php _e( 'Continue Reading', 'jobify' ); ?></a></p>
		</div>
	</div>
</article><!-- #post -->
