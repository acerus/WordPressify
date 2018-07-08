<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@hook in account update to subscribe/unsubscribe users
	***/
	add_action('um_post_account_update', 'um_friends_account_update');
	function um_friends_account_update() {
		$user_id = um_user('ID');
		
		if ( isset( $_POST['_enable_new_friend'] ) ) {
			update_user_meta( $user_id, '_enable_new_friend', 'yes' );
		} else {
			update_user_meta( $user_id, '_enable_new_friend', 'no' );
		}
	}