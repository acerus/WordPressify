<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

if ( post_password_required() || ! ( comments_open() || get_comments_number() ) ) {
	return;
}
?>

<div class="row">
	<div id="comments" class="comments-area col-md-9 col-md-offset-3">

		<?php if ( have_comments() ) : ?>
			<h2 class="comments-title">
				<?php
					printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'jobify' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
				?>
			</h2>

			<ol class="commentlist">
				<?php
					wp_list_comments( array(
						'callback'    => array( jobify()->template->comments, 'comment' ),
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 74,
					) );
				?>
			</ol><!-- .comment-list -->

			<?php
				// Are there comments to navigate through?
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			?>
			<nav class="navigation comment-navigation" role="navigation">
			<h3 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'jobify' ); ?></h3>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'jobify' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'jobify' ) ); ?></div>
			</nav><!-- .comment-navigation -->
			<?php endif; // Check for comment navigation ?>

			<?php if ( ! comments_open() && get_comments_number() ) : ?>
			<p class="no-comments"><?php _e( 'Comments are closed.' , 'jobify' ); ?></p>
			<?php endif; ?>

		<?php endif; // have_comments() ?>

		<?php comment_form( array(
			'comment_notes_before' => null,
			'comment_notes_after'  => null,
			'id_submit'            => 'submitcomment',
		) ); ?>

	</div><!-- #comments -->
</div>
