<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@Show user online status beside name
	***/
	add_action( 'um_after_profile_name_inline', 'um_online_show_user_status' );
	function um_online_show_user_status( $args ) {
		$_hide_online_status = get_user_meta( get_current_user_id(), '_hide_online_status', true );
		if ( $_hide_online_status == 1 ) {
			return;
		}

		if ( UM()->Online_API()->is_online( um_profile_id() ) ) {
			echo '<span class="um-online-status online um-tip-n" title="' . __( 'online', 'um-online' ) . '"><i class="um-faicon-circle"></i></span>';
		}
	
	}