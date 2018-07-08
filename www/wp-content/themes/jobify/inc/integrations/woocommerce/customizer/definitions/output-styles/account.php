<?php
/**
 * WooCommerce account.
 *
 * @since 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$page = wc_get_page_id( 'myaccount' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		".logged-in .modal .post-{$page} .entry-content"
	),
	'declarations' => array(
		'padding' => '30px',
	),
) );
