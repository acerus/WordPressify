<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */
?>

		</div><!-- #main -->

		<?php if ( get_theme_mod( 'cta-display', true ) ) : ?>
		<div class="footer-cta">
			<div class="container">
				<?php echo wpautop( get_theme_mod( 'cta-text', '<h2>Got a question?</h2>We&#39;re here to help. Check out our FAQs, send us an email or call us at 1 800 555 5555' ) ); ?>
			</div>
		</div>
		<?php endif; ?>

		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php if ( is_active_sidebar( 'widget-area-footer' ) ) : ?>
			<div class="footer-widgets">
				<div class="container">
					<div class="row">
						<?php for ( $i = 1; $i <= 4; $i++ ) : ?> 
						<div class="col-xs-12 col-md-6 col-lg-3">
							<?php dynamic_sidebar( 'widget-area-footer' . ( $i > 1 ? ( '-' . absint( $i ) ) : '' ) ); ?>
						</div>
						<?php endfor; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div class="copyright">
				<div class="container">
					<div class="site-info">
						<?php echo apply_filters( 'jobify_footer_copyright', get_theme_mod( 'copyright', sprintf( '&copy; %1$s %2$s &mdash; All Rights Reserved', date( 'Y' ), get_bloginfo( 'name' ) ) ) ); ?>
					</div><!-- .site-info -->

					<?php
					if ( has_nav_menu( 'footer-social' ) ) :
						$social = wp_nav_menu( array(
							'theme_location'  => 'footer-social',
							'container_class' => 'footer-social',
							'items_wrap'      => '%3$s',
							'depth'           => 1,
							'echo'            => false,
							'link_before'     => '<span class="screen-reader-text">',
							'link_after'      => '</span>',
						) );

						echo strip_tags( $social, '<a><div><span>' );
						endif;
					?>

					<a href="#page" class="btt <?php if ( ! has_nav_menu( 'footer-social' ) ) : ?>btt--no-social<?php endif; ?>"><span class="screen-reader-text"><?php _e( 'Back to Top', 'jobify' ); ?></span></a>
				</div>
			</div>
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<div id="ajax-response"></div>

	<?php wp_footer(); ?>
</body>
</html>
