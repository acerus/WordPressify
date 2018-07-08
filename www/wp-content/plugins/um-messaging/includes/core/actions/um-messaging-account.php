<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@hook in account update
	***/
	add_action('um_post_account_update', 'um_messaging_account_update');
	function um_messaging_account_update() {
		$user_id = um_user('ID');
		
		if ( isset( $_POST['_enable_new_pm'] ) ) {
			update_user_meta( $user_id, '_enable_new_pm', 'yes' );
		} else {
			update_user_meta( $user_id, '_enable_new_pm', 'no' );
		}
	}