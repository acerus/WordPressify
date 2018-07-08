<?php
/**
 * Search Form Header
 *
 * @package Jobify
 * @since 3.0.0
 * @version 3.8.0
 */
?>
<form role="search" method="get" id="searchform" class="searchform searchform--header" action="<?php echo home_url( '/' ); ?>">
	<label class="screen-reader-text" for="s"><?php _e( 'Search for', 'jobify' ); ?>:</label>
	<input type="text" value="" name="s" id="s" class="searchform__input searchform--header__input" placeholder="<?php _e( 'Keywords...', 'jobify' ); ?>" />
	<button type="submit" id="searchsubmit" class="searchform__submit searchform--header__submit"><span class="screen-reader-text"><?php _e( 'Search', 'jobify' ); ?></button>
</form>
