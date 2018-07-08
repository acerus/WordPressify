<?php

class Jobify_Template_Comments {

	public function __construct() {
		add_filter( 'comment_class', array( $this, 'comment_class' ) );
	}

	function comment_class( $classes ) {
		if ( ! get_option( 'show_avatars' ) ) {
			$classes[] = 'no-avatars';
		}

		return $classes;
	}

	public function comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
				// Display trackbacks differently than normal comments.
		?>
		<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<p><?php _e( 'Pingback:', 'jobify' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'jobify' ), '<span class="ping-meta"><span class="edit-link">', '</span></span>' ); ?></p>
		<?php
				break;
			default :
				// Proceed with normal comments.
		?>
		<li id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 75 ); ?>
				</div><!-- .comment-author -->

				<header class="comment-meta">
					<span class="comment-author vcard"><cite class="fn"><?php comment_author_link(); ?></cite></span>
					<?php echo _x( 'on', 'comment author "on" date', 'jobify' ); ?>
						<?php
						printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							sprintf( _x( '%1$s at %2$s', 'on 1: date, 2: time', 'jobify' ), get_comment_date(), get_comment_time() )
						);
						edit_comment_link( __( 'Edit', 'jobify' ), '<span class="edit-link"><i class="icon-pencil"></i> ', '<span>' );

						comment_reply_link( array_merge( $args, array(
							'reply_text' => __( 'Reply', 'jobify' ),
							'depth' => $depth,
							'max_depth' => $args['max_depth'],
						) ) );
					?>
				</header><!-- .comment-meta -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'jobify' ); ?></p>
				<?php endif; ?>

				<div class="comment-content">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->
			</article><!-- #comment-## -->
		<?php
			break;
		endswitch; // End comment_type check.
	}

}
