<?php
if ( ! defined( 'ABSPATH' ) ) exit;


	/***
	***	@Add "follows you" if the user is following current user
	***/
	add_action('um_after_profile_name_inline', 'um_followers_add_state', 200 );
	function um_followers_add_state( $args ) {
		if ( !is_user_logged_in() || !um_profile_id() )
			return;

		if ( get_current_user_id() == um_profile_id() )
			return;

		if ( UM()->Followers_API()->api()->followed( get_current_user_id(), um_profile_id() ) ) {
			echo '<span class="um-follows-you">'. __('follows you','um-followers') . '</span>';
		}

	}

	/***
	***	@Followers List
	***/
	add_action('um_profile_content_followers_default', 'um_profile_content_followers_default');
	function um_profile_content_followers_default( $args ) {
		echo do_shortcode('[ultimatemember_followers user_id='.um_profile_id().']');
	}

	/***
	***	@Following List
	***/
	add_action('um_profile_content_following_default', 'um_profile_content_following_default');
	function um_profile_content_following_default( $args ) {
		echo do_shortcode('[ultimatemember_following user_id='.um_profile_id().']');
	}

	/***
	***	@customize the nav bar
	***/
	add_action('um_profile_navbar', 'um_followers_add_profile_bar', 4 );
	function um_followers_add_profile_bar( $args ) {
		echo do_shortcode('[ultimatemember_followers_bar user_id='.um_profile_id().']');
	}


	/***
	***	@customize the nav bar
	***/
	add_action('um_activity_ajax_get_user_suggestions', 'um_followers_ajax_get_user_suggestions' );
	function um_followers_ajax_get_user_suggestions() {
        $term = $_GET['term'];
        $term = str_replace( '@', '', $term );

        if ( empty( $term ) )
            return;

        $user_id = get_current_user_id();

        $following = UM()->Followers_API()->api()->following( $user_id );
        if ( $following ) {
            foreach ( $following as $k => $arr) {
                extract( $arr );
                um_fetch_user( $user_id1 );
                if ( ! stristr( um_user( 'display_name' ), $term ) ) continue;
                $data[$user_id1]['user_id'] = $user_id1;
                $data[$user_id1]['photo'] = get_avatar( $user_id1, 80 );
                $data[$user_id1]['name'] = str_replace( $term, '<strong>' . $term . '</strong>', um_user( 'display_name' ) );
                $data[$user_id1]['username'] = um_user( 'user_login' );
            }
        }

        $followers = UM()->Followers_API()->api()->followers( $user_id );
        if ( $followers ) {
            foreach ( $followers as $k => $arr ) {
                extract( $arr );
                um_fetch_user( $user_id2 );
                if ( ! stristr( um_user( 'display_name' ), $term ) ) continue;
                $data[$user_id2]['user_id'] = $user_id2;
                $data[$user_id2]['photo'] = get_avatar( $user_id2, 80 );
                $data[$user_id2]['name'] = str_replace( $term, '<strong>' . $term . '</strong>', um_user( 'display_name' ) );
                $data[$user_id2]['username'] = um_user( 'user_login' );
            }
        }

        if ( isset( $data ) )
            wp_send_json( $data );
	}