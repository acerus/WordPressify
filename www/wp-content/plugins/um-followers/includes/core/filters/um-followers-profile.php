<?php

	/***
	***	@More profile privacy options
	***/
	add_filter('um_profile_privacy_options', 'um_followers_profile_privacy_options', 100 );
	function um_followers_profile_privacy_options( $options ) {
		$options[] = __('Only people I follow can view my profile','um-followers');
		$options[] = __('Followers','um-followers');
		return $options;
	}


	add_filter('um_messaging_privacy_options', 'um_followers_messaging_privacy_options', 10, 1 );
	function um_followers_messaging_privacy_options( $options ) {
        $options['followed'] = __( 'Only people I follow', 'um-followers' );
		$options['follower'] = __( 'Followers', 'um-followers' );
		return $options;
	}

	/***
	***	@add a hidden tab
	***/
	add_filter('um_profile_tabs', 'um_followers_add_tabs', 2000 );
	function um_followers_add_tabs( $tabs ) {
		
		$tabs['followers'] = array(
			'hidden' => true,
			'_builtin' => true,
		);
		
		$tabs['following'] = array(
			'hidden' => true,
			'_builtin' => true,
		);
		
		return $tabs;
		
	}
	
	/***
	***	@Check if user can view user profile
	***/
	add_filter('um_profile_can_view_main', 'um_followers_can_view_main', 10, 2 );
	function um_followers_can_view_main( $can_view, $user_id ) {
		if ( ! is_user_logged_in() || get_current_user_id() != $user_id ) {
			$is_private_case = UM()->user()->is_private_case( $user_id, __('Followers','um-followers') );
			if ( $is_private_case ) {
				$can_view = __('You must follow this user to view profile','um-followers');
			}
			
			$is_private_case = UM()->user()->is_private_case( $user_id, __('Only people I follow can view my profile','um-followers') );
			if ( $is_private_case ) {
				$can_view = __('You cannot view this profile because the user has not followed you','um-followers');
			}
		}
		
		return $can_view;
	}
	
	/***
	***	@Test case to hide profile
	***/
	add_filter('um_is_private_filter_hook', 'um_followers_private_filter_hook', 100, 3 );
	function um_followers_private_filter_hook( $default, $option, $user_id ) {
		// user selected this option in privacy
		if ( $option == __('Followers','um-followers') ) {
			if ( ! UM()->Followers_API()->api()->followed( $user_id, get_current_user_id() ) ) {
				return true;
			}
		}
		
		if ( $option == __('Only people I follow can view my profile','um-followers') ) {
			if ( ! UM()->Followers_API()->api()->followed( get_current_user_id(), $user_id ) ) {
				return true;
			}
		}
		
		return $default;
	}


	add_filter( 'um_activity_mention_integration', 'um_followers_activity_mention_integration', 10, 4 );
	function um_followers_activity_mention_integration( $content, $user_id, $post_id, $status ) {
        $mentioned_in_post = get_post_meta( $post_id, '_mentioned', true );

        $following = UM()->Followers_API()->api()->following( $user_id );
        if ( $following ) {
            foreach( $following as $k => $arr ) {
                extract( $arr );
                um_fetch_user( $user_id1 );

                if ( !stristr( $content, um_user('display_name') ) ) continue;

                if ( $mentioned_in_post && in_array( $user_id1, $mentioned_in_post ) ) {
                    $user_mentioned_in_post = true;
                } else {
                    $user_mentioned_in_post = false;
                }

                $user_link = '<a href="' . um_user_profile_url() . '" class="um-link um-user-tag">' . um_user('display_name') . '</a>';
                $content = str_ireplace( '@' . um_user('display_name'), $user_link, $content );

                if ( $user_mentioned_in_post == false ) {
                    do_action( 'um_following_new_mention', $user_id, $user_id1, $post_id );

                    $mention[] = $user_id1;
                }
            }
        }

        $followers = UM()->Followers_API()->api()->followers( $user_id );
        if ( $followers ) {
            foreach ( $followers as $k => $arr ) {
                extract( $arr );
                um_fetch_user( $user_id2 );

                if ( !stristr( $content, um_user('display_name') ) ) continue;

                if ( $mentioned_in_post && in_array( $user_id2, $mentioned_in_post ) ) {
                    $user_mentioned_in_post = true;
                } else {
                    $user_mentioned_in_post = false;
                }

                $user_link = '<a href="' . um_user_profile_url() . '" class="um-link um-user-tag">' . um_user('display_name') . '</a>';
                $content = str_ireplace( '@' .um_user('display_name'), $user_link, $content );

                if ( $user_mentioned_in_post == false ) {
                    do_action( 'um_followers_new_mention', $user_id, $user_id2, $post_id );

                    $mention[] = $user_id2;
                }
            }
        }

        if ( isset( $mention ) ) {
            update_post_meta( $post_id, '_mentioned', $mention );
        }

        return $content;
	}


/**
 * Lock activity wall for followers only
 *
 * @param $can_view
 * @param $profile_id
 *
 * @return bool|string|void
 */
function um_wall_can_view_follower( $can_view, $profile_id ) {

	if ( get_current_user_id() == $profile_id )
		return $can_view;

	$privacy = get_user_meta( $profile_id, 'wall_privacy', true );

	if ( $privacy == 3 ) {
		if ( ! UM()->Followers_API()->api()->followed( $profile_id, get_current_user_id() ) ) {
			return __( 'You must follow this user to view their social activity', 'um-followers' );
		}
	}

	if ( $privacy == 4 ) {
		if ( ! UM()->Followers_API()->api()->followed( get_current_user_id(), $profile_id ) ) {
			return __( 'This user must follow you before you can view their wall', 'um-followers' );
		}
	}

	return $can_view;
}
add_filter( "um_wall_can_view", 'um_wall_can_view_follower', 11, 2 );


/**
 * @param string $classes
 * @return string
 */
function um_followers_profile_navbar_classes( $classes ) {
    $classes .= " um-followers-bar";
    return $classes;
}
add_filter( "um_profile_navbar_classes", 'um_followers_profile_navbar_classes', 10, 1 );


function um_followers_can_message_restrict( $restrict, $who_can_pm, $recipient ) {
    // only people I follow
    if ( $who_can_pm == 'followed' ) {
        if ( ! UM()->Followers_API()->api()->followed( get_current_user_id(), $recipient ) ) {
            return true;
        }
    }

    // followers can message
    if ( $who_can_pm == 'follower' ) {
        if ( ! UM()->Followers_API()->api()->followed( $recipient, get_current_user_id() ) ) {
            return true;
        }
    }

    return $restrict;
}
add_filter('um_messaging_can_message_restrict', 'um_followers_can_message_restrict', 10, 3 );