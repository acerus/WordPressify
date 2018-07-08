<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * More profile privacy options
 *
 * @param array $options
 *
 * @return array
 */
function um_friends_profile_privacy_options( $options ) {
	$options[] = __( 'Friends only', 'um-friends' );
	return $options;
}
add_filter( 'um_profile_privacy_options', 'um_friends_profile_privacy_options', 100 );


/**
 * Make private messaging privacy
 *
 * @param array $options
 *
 * @return array
 */
function um_friends_messaging_privacy_options( $options ) {
	$options['friends'] = __( 'Friends', 'um-friends' );
	return $options;
}
add_filter( 'um_messaging_privacy_options', 'um_friends_messaging_privacy_options', 10, 1 );


/**
 * Extend profile tabs
 *
 * @param array $tabs
 *
 * @return array
 */
function um_friends_add_tabs( $tabs ) {
	$user_id = um_user( 'ID' );
	if ( ! $user_id )
		return $tabs;

	$tabs['friends'] = array(
		//'hidden' => true,
		'_builtin' => true,
		'name' => __( 'Friends', 'um-friends' ),
		'icon' => 'um-faicon-users',
	);

	return $tabs;
}
add_filter( 'um_profile_tabs', 'um_friends_add_tabs', 2000 );


/**
 * Add tabs based on user
 *
 * @param array $tabs
 *
 * @return array
 */
function um_friends_user_add_tab( $tabs ) {
	$user_id = um_user( 'ID' );
	if ( ! $user_id )
		return $tabs;

	if ( ! UM()->profile()->can_view_tab( 'friends' ) ) {
		return $tabs;
	}

	$username = um_user( 'display_name' );

	$myfriends = ( um_is_myprofile() ) ? __( 'My Friends', 'um-friends' ) : sprintf( __( '%s\'s friends', 'um-friends' ), $username );
	$myfriends .= '<span>' . UM()->Friends_API()->api()->count_friends( $user_id, false ) . '</span>';

	$new_reqs = UM()->Friends_API()->api()->count_friend_requests_received( $user_id );

	if ( $new_reqs > 0 ) {
		$class = 'um-friends-notf';
	} else {
		$class = '';
	}


	$tabs['friends']['subnav_default'] = 'myfriends';
	$tabs['friends']['subnav'] = array(
		'myfriends'     => $myfriends,
	);

	if ( um_is_myprofile() ) {
		$tabs['friends']['subnav']['friendreqs'] = __( 'Friend Requests','um-friends') . '<span class="'. $class . '">' . $new_reqs . '</span>';
		$tabs['friends']['subnav']['sentreqs'] = __( 'Friend Requests Sent','um-friends') . '<span>' . UM()->Friends_API()->api()->count_friend_requests_sent( $user_id ) . '</span>';
	}

	return $tabs;
}
add_filter( 'um_user_profile_tabs', 'um_friends_user_add_tab', 1000 );


/**
 * Check if user can view user profile
 *
 * @param $can_view
 * @param int $user_id
 *
 * @return string|void
 */
function um_friends_can_view_main( $can_view, $user_id ) {
	if ( ! is_user_logged_in() || get_current_user_id() != $user_id ) {
		$is_private_case = UM()->user()->is_private_case( $user_id, __( 'Friends only', 'um-friends' ) );
		if ( $is_private_case && ! current_user_can( 'manage_options' ) ) { //Enable admin to be able to view
			$can_view = __( 'You must be a friend of this user to view their profile', 'um-friends' );
		}
	}
		
	return $can_view;
}
add_filter( 'um_profile_can_view_main', 'um_friends_can_view_main', 10, 2 );


/**
 * Test case to hide profile
 *
 * @param $default
 * @param $option
 * @param $user_id
 *
 * @return bool
 */
function um_friends_private_filter_hook( $default, $option, $user_id ) {
	// user selected this option in privacy
	if ( $option == __( 'Friends only', 'um-friends' ) ) {
		if ( ! UM()->Friends_API()->api()->is_friend( $user_id, get_current_user_id() ) ) {
			return true;
		}
	}
		
	return $default;
}
add_filter( 'um_is_private_filter_hook', 'um_friends_private_filter_hook', 100, 3 );


/**
 * Case if user can message only with friends
 *
 * @param $restrict
 * @param $who_can_pm
 * @param $recipient
 *
 * @return bool
 */
function um_friends_can_message_restrict( $restrict, $who_can_pm, $recipient ) {
	// user selected this option in privacy
	if ( $who_can_pm == 'friends' ) {
		if ( ! UM()->Friends_API()->api()->is_friend( get_current_user_id(), $recipient ) ) {
			return true;
		}
	}

	return $restrict;
}
add_filter( 'um_messaging_can_message_restrict', 'um_friends_can_message_restrict', 10, 3 );