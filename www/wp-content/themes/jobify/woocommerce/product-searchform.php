<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form role="search" method="get" id="searchform" class="searchform" action="<?php echo home_url( '/' ); ?>">
	<label class="screen-reader-text" for="s"><?php _e( 'Search for', 'jobify' ); ?>:</label>
	<input type="text" value="" name="s" id="s" class="searchform__input" placeholder="<?php _e( 'Keywords...', 'jobify' ); ?>" />
	<button type="submit" id="searchsubmit" class="searchform__submit"><span class="screen-reader-text"><?php _e( 'Search', 'jobify' ); ?></button>
	<input type="hidden" name="post_type" value="product" />
</form>
