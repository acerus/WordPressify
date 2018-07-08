<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@Filter user permissions in bbPress
	***/
	add_filter('um_user_permissions_filter', 'um_friends_user_permissions_filter', 10, 4);
	function um_friends_user_permissions_filter( $meta, $user_id ){
		
		if ( !isset( $meta['can_friend'] ) )
			$meta['can_friend'] = 1;
		
		return $meta;
	}