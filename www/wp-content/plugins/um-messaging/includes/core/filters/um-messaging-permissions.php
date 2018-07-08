<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@Filter user permissions in PM
	***/
	add_filter('um_user_permissions_filter', 'um_messaging_user_permissions_filter', 10, 4);
	function um_messaging_user_permissions_filter( $meta, $user_id ){
		
		if ( !isset( $meta['enable_messaging'] ) )
			$meta['enable_messaging'] = 1;
		
		if ( !isset( $meta['can_start_pm'] ) )
			$meta['can_start_pm'] = 1;

		if ( !isset( $meta['can_read_pm'] ) )
			$meta['can_read_pm'] = 1;
		
		if ( !isset( $meta['can_reply_pm'] ) )
			$meta['can_reply_pm'] = 1;
		
		if ( !isset( $meta['pm_max_messages'] ) )
			$meta['pm_max_messages'] = '';
		
		if ( !isset( $meta['pm_max_messages_tf'] ) )
			$meta['pm_max_messages_tf'] = '';
		
		return $meta;
	}