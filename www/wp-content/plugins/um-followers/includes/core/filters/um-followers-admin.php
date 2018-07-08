<?php
	
	/***
	***	@Filter user permissions in bbPress
	***/
	add_filter('um_user_permissions_filter', 'um_followers_user_permissions_filter', 10, 4);
	function um_followers_user_permissions_filter( $meta, $user_id ){
		
		if ( !isset( $meta['can_follow'] ) )
			$meta['can_follow'] = 1;
		
		return $meta;
	}