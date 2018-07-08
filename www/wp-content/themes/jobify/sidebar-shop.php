<?php
/**
 * Shop Sidebar
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */

$args = array(
	'before_widget' => '<aside class="widget">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h3 class="widget-title">',
	'after_title'   => '</h3>',
);
?>

<div class="col-md-3 col-xs-12">
	<?php if ( ! dynamic_sidebar( 'widget-area-sidebar-shop' ) ) : ?>
		<?php the_widget( 'WC_Widget_Products', array(
			'title' => __( 'Recent Products', 'jobify' ),
		), $args ); ?>
	<?php endif; ?>
</div>
