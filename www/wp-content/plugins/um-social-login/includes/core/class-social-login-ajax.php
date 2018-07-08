<?php
namespace um_ext\um_social_login\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Social_Login_Ajax {

	
	function ajax_change_photo(){
		
		if ( !isset( $_POST['user_id'] ) || !is_numeric( $_POST['user_id'] ) || !is_user_logged_in() ) return wp_send_json( array('error' => 'user_id' ) );
		if ( !isset( $_POST['provider'] ) )  return wp_send_json( array('error' => 'provider' ) );

		$user_id 	= $_POST['user_id'];
		$provider 	= $_POST['provider'];
		$avatar = '';
		if ( UM()->Social_Login_API()->get_user_photo( $user_id, $provider ) ) {
			$avatar = UM()->Social_Login_API()->get_user_photo( $user_id, $provider );
		} else if ( UM()->Social_Login_API()->get_dynamic_user_photo( $user_id, $provider ) ) {
			$avatar = UM()->Social_Login_API()->get_dynamic_user_photo( $user_id, $provider );
		} else if( $provider == "core" ){
			$profile_photo = get_user_meta( $user_id, 'profile_photo', true );
			$avatar = um_user_uploads_uri().$profile_photo;
		}

		if( $provider == 'twitter' ){
			$avatar = str_replace( '_normal' , '', $avatar );
		}elseif( $provider == 'facebook' ){
			$avatar = add_query_arg( array(
				'width' => 400,
				'height' => 400
			), $avatar );
		}

		update_user_meta( $user_id, 'synced_profile_photo', $avatar );
		update_user_meta( $user_id, '_um_social_login_avatar_provider', $provider );

		$output['success'] = 1;
		$output['source'] = $avatar;

		return wp_send_json( $output );
		
	}


}