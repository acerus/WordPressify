<?php
/**
 * Job Listing Map
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>

<div class="<?php echo $type ?>-map-wrapper">
	<?php do_action( 'jobify_map_before' ); ?>

	<div class="<?php echo $type ?>-map">
		<div id="<?php echo $type ?>-map-canvas"></div>
	</div>

	<?php do_action( 'jobify_map_after' ); ?>
</div>
