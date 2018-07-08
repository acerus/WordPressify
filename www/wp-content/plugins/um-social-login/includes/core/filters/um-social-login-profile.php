<?php
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter('um_user_photo_menu_edit','um_social_login_user_photo_menu_edit', 10, 1 );
add_filter('um_user_photo_menu_view','um_social_login_user_photo_menu_edit', 10, 1 );
function um_social_login_user_photo_menu_edit( $items ){

		$networks = UM()->Social_Login_API()->networks;
		$user_id = get_current_user_id();

		foreach( $networks as $provider => $arr ) {

			if ( UM()->Social_Login_API()->is_connected( $user_id, $provider ) ) {

				if ( UM()->Social_Login_API()->get_user_photo( $user_id, $provider ) ) {
					$image_option ='<a href="#" class="um-social-login-avatar-change"  data-provider="'.$provider .'" data-parent=".um-profile-photo" data-child=".um-btn-auto-width"><img src="'.UM()->Social_Login_API()->get_user_photo( $user_id, $provider ).'" class="um-provider-photo small" /><span class="um-social-login-avatar-change">'.__( 'Use this avatar','um-social-login').'</span></a><div class="um-clear"></div>';
					array_unshift( $items, $image_option );
				} else if ( UM()->Social_Login_API()->get_dynamic_user_photo( $user_id, $provider ) ) {
					$image_option ='<a href="#" class="um-social-login-avatar-change" data-provider="'.$provider .'"  data-parent=".um-profile-photo" data-child=".um-btn-auto-width"><img src="'.UM()->Social_Login_API()->get_dynamic_user_photo( $user_id, $provider ) .'" class="um-provider-photo small" /><span class="um-social-login-avatar-change">'.__( 'Use this avatar','um-social-login').'</span></a><div class="um-clear"></div>';
					array_unshift( $items, $image_option );
				}
			}

		}

		$profile_photo = get_user_meta( $user_id, 'profile_photo', true );
		if( $profile_photo ){
			$image_option ='<a href="#" class="um-social-login-avatar-change" data-provider="core"  data-parent=".um-profile-photo" data-child=".um-btn-auto-width"><img src="'.um_user_uploads_uri().$profile_photo.'" class="um-provider-photo small" /><span class="um-social-login-avatar-change">'.__( 'Use this avatar','um-social-login').'</span></a><div class="um-clear"></div>';
					array_unshift( $items, $image_option );
		}
	
	

	return $items;
}


add_filter( 'mycred_setup_hooks', 'um_social_login_mycred_hooks', 9, 2 );
function um_social_login_mycred_hooks( $installed, $point_type ) {
    // Connect
    $installed['um-mycred-social-login-connect'] = array(
        'title'        => 'UM - Connect Social Account',
        'description'  => 'Award points for users connecting Social Network.',
        'callback'     => array( 'UM_myCRED_Social_Login_Connect' )
    );

    // Disconnect
    $installed['um-mycred-social-login-disconnect'] = array(
        'title'        => 'UM - Disconnect Social Account',
        'description'  => 'Deduct points for users disconnecting Social Network.',
        'callback'     => array( 'UM_myCRED_Social_Login_Disconnect' )
    );

    return $installed;
}


add_filter( 'um_profile_completeness_skip_field', 'um_profile_completeness_skip_field', 10, 3 );
function um_profile_completeness_skip_field( $skip, $key, $result ) {
    if ( $key == 'profile_photo' && um_user( 'synced_profile_photo' ) ) {
        return true;
    }

    return $skip;
}