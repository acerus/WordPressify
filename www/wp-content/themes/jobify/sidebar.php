<?php
/**
 * Sidebar Template
 *
 * @package Jobify
 * @since 1.0.0
 * @version 3.8.0
 */

if ( ! is_active_sidebar( 'sidebar-blog' ) ) {
	return;
}
?>

<div class="widget-area--sidebar col-md-3 col-xs-12">
	<?php dynamic_sidebar( 'sidebar-blog' ); ?>
</div>
