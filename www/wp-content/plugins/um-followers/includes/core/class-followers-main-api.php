<?php
namespace um_ext\um_followers\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Followers_Main_API {

	function __construct() {

		global $wpdb;
		$this->table_name = $wpdb->prefix . "um_followers";

	}

    /**
     * @var $id
     */
	function rest_get_following( $args ) {
        extract( $args );

        $response = array();
        $error = array();

        if ( ! $id ) {
            $error['error'] = __( 'You must provide a user ID', 'um-followers' );
            return $error;
        }

        $results = UM()->Followers_API()->api()->following( $id );
        if ( !$results ) {
            $error['error'] = __( 'No users were found', 'um-followers' );
            return $error;
        }
        $response['following']['count'] = $this->count_following_plain( $id );
        foreach( $results as $k => $v ) {
            $user = get_userdata( $v['user_id1'] );
            $response['following']['users'][$k]['ID'] = $v['user_id1'];
            $response['following']['users'][$k]['username'] = $user->user_login;
            $response['following']['users'][$k]['display_name'] = $user->display_name;
        }


        return $response;
    }

    /**
     * @var $id
     */
    function rest_get_followers( $args ) {
        extract( $args );

        $response = array();
        $error = array();

        if ( ! $id ) {
            $error['error'] = __( 'You must provide a user ID', 'um-followers' );
            return $error;
        }

        $results = $this->followers( $id );
        if ( !$results ) {
            $error['error'] = __( 'No users were found', 'um-followers' );
            return $error;
        }
        $response['followers']['count'] = $this->count_followers_plain( $id );
        foreach ( $results as $k => $v ) {
            $user = get_userdata( $v['user_id2'] );
            $response['followers']['users'][$k]['ID'] = $v['user_id2'];
            $response['followers']['users'][$k]['username'] = $user->user_login;
            $response['followers']['users'][$k]['display_name'] = $user->display_name;
        }

        return $response;
    }





	/***
	***	@Checks if user enabled email notification
	***/
	function enabled_email( $user_id ) {
		$_enable_new_follow = true;
		if ( get_user_meta( $user_id, '_enable_new_follow', true ) == 'yes' ) {
			$_enable_new_follow = 1;
		} else if ( get_user_meta( $user_id, '_enable_new_follow', true ) == 'no' ) {
			$_enable_new_follow = 0;
		}
		return $_enable_new_follow;
	}

	/***
	***	@Show the followers list URL
	***/
	function followers_link( $user_id ) {
		$nav_link = um_user_profile_url();
		$nav_link = add_query_arg('profiletab', 'followers', $nav_link );
		return $nav_link;
	}

	/***
	***	@Show the following list URL
	***/
	function following_link( $user_id ) {
		$nav_link = um_user_profile_url();
		$nav_link = add_query_arg('profiletab', 'following', $nav_link );
		return $nav_link;
	}

	/***
	***	@Show the follow button for two users
	***/
	function follow_button( $user_id1, $user_id2 ) {
		$res = '';

		$hide_follow_button = apply_filters( 'um_followers_hide_button', false );
		if ( $hide_follow_button || ( current_user_can( "manage_options" ) && UM()->options()->get( "followers_allow_admin_to_follow" ) == 0 ) ) {
		   return $res;
		}

		if ( ! is_user_logged_in() ) {
			$redirect = um_get_core_page( 'register' );
			$redirect = add_query_arg( 'redirect_to', UM()->permalinks()->get_current_url(), $redirect );
			$redirect = apply_filters( 'um_followers_button_redirect_url', $redirect );
			$res = '<a href="' . $redirect . '" class="um-login-to-follow-btn um-button um-alt">'. __('Follow','um-followers'). '</a>';
			return $res;
		}

		if ( $this->can_follow( $user_id1, $user_id2 ) ) {

            if ( ! $this->followed( $user_id1, $user_id2 ) ) {
                $res = '<a href="#" class="um-follow-btn um-button um-alt" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Follow','um-followers'). '</a>';
            } else {
                $res = '<a href="#" class="um-unfollow-btn um-button" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'" data-following="'.__('Following','um-followers').'"  data-unfollow="'.__('Unfollow','um-followers').'">'. __('Following','um-followers'). '</a>';
            }

		}
		return $res;
	}

	/**
	 * If user can follow
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function can_follow( $user_id1, $user_id2 ) {
		if ( ! is_user_logged_in() )
			return true;

		$roles1 = UM()->roles()->get_all_user_roles( $user_id1 );

		$role2 = UM()->roles()->get_priority_user_role( $user_id2 );
		$role_data2 = UM()->roles()->role_data( $role2 );
		$role_data2 = apply_filters( 'um_user_permissions_filter', $role_data2, $user_id2 );

		if ( ! $role_data2['can_follow'] )
			return false;

		if ( ! empty( $role_data2['can_follow_roles'] ) &&
		     ( empty( $roles1 ) || count( array_intersect( $roles1, maybe_unserialize( $role_data2['can_follow_roles'] ) ) ) <= 0 ) )
			return false;

		if ( $user_id1 != $user_id2 && is_user_logged_in() )
			return true;

		return false;
	}


	/**
	 * Get the count of followers
	 *
	 * @param int $user_id
	 * @return null|string
	 */
	function count_followers_plain( $user_id = 0 ) {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) 
			FROM {$this->table_name} 
			WHERE user_id1 = %d AND 
				  user_id2 IN ( SELECT ID FROM {$wpdb->users} )",
			$user_id
		) );

		return $count;
	}


	/**
	 * Get the count of followers in nice format
	 *
	 * @param int $user_id
	 * @return string
	 */
	function count_followers( $user_id = 0 ) {
		$count = $this->count_followers_plain ( $user_id );
		return '<span class="um-ajax-count-followers">' . number_format( $count ) . '</span>';
	}


	/**
	 * Get the count of following
	 *
	 * @param int $user_id
	 * @return null|string
	 */
	function count_following_plain( $user_id = 0 ) {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) 
			FROM {$this->table_name} 
			WHERE user_id2 = %d AND 
				  user_id1 IN ( SELECT ID FROM {$wpdb->users} )",
			$user_id
		) );

		return $count;
	}


	/**
	 * Get the count of following in nice format
	 *
	 * @param int $user_id
	 * @return string
	 */
	function count_following( $user_id = 0 ) {
		$count = $this->count_following_plain ( $user_id );
		return '<span class="um-ajax-count-following">' . number_format( $count ) . '</span>';
	}


	/**
	 * Add a follow action
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool|false|int
	 */
	function add( $user_id1, $user_id2 ) {
		global $wpdb;

		// if already followed do not add
		if ( $this->followed( $user_id1, $user_id2 ) )
			return false;

		$result = $wpdb->insert(
			$this->table_name,
			array(
				'time' => current_time( 'mysql' ),
				'user_id1' => $user_id1,
				'user_id2' => $user_id2
			)
		);

		return $result;
	}

	/**
	 * Removes a follow connection
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function remove( $user_id1, $user_id2 ) {
		global $wpdb;

		// If user is not followed do not do anything
		if ( ! $this->followed( $user_id1, $user_id2 ) )
			return false;

		$wpdb->delete(
			$this->table_name,
			array(
				'user_id1' => $user_id1,
				'user_id2' => $user_id2
			)
		);

		return true;
	}


	/**
	 * Checks if user is follower of another user
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function followed( $user_id1, $user_id2 ) {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id1 
			FROM {$this->table_name} 
			WHERE user_id1 = %d AND 
				  user_id2 = %d AND 
				  user_id1 IN ( SELECT ID FROM {$wpdb->users} ) AND 
				  user_id2 IN ( SELECT ID FROM {$wpdb->users} ) 
			LIMIT 1",
			$user_id1,
			$user_id2
		) );

		if ( $results && isset( $results[0] ) )
			return true;

		return false;
	}


	/**
	 * Get followers as array
	 *
	 * @param int $user_id1
	 * @param string|int $limit
	 * @return array|bool|null|object
	 */
	function followers( $user_id1, $limit = '' ) {
		global $wpdb;

		if ( ! empty( $limit ) ) {

			$prepared_query = $wpdb->prepare(
				"SELECT user_id2 
				FROM {$this->table_name} 
				WHERE user_id1 = %d AND 
					  user_id2 IN ( SELECT ID FROM {$wpdb->users} ) 
				ORDER BY time DESC 
				LIMIT %d",
				$user_id1,
				$limit
			);

		} else {

			$prepared_query = $wpdb->prepare(
				"SELECT user_id2 
				FROM {$this->table_name} 
				WHERE user_id1 = %d AND 
					  user_id2 IN ( SELECT ID FROM {$wpdb->users} ) 
				ORDER BY time DESC",
				$user_id1
			);

		}

		$results = $wpdb->get_results( $prepared_query, ARRAY_A );

		return ! empty( $results ) ? $results : false;
	}


	/**
	 * Get following as array
	 *
	 * @param int $user_id2
	 * @param string|int $limit
	 * @return array|bool|null|object
	 */
	function following( $user_id2, $limit = '' ) {
		global $wpdb;

		if ( ! empty( $limit ) ) {

			$prepared_query = $wpdb->prepare(
				"SELECT user_id1 
				FROM {$this->table_name} 
				WHERE user_id2 = %d AND 
					  user_id1 IN ( SELECT ID FROM {$wpdb->users} ) 
				ORDER BY time DESC 
				LIMIT %d",
				$user_id2,
				$limit
			);

		} else {

			$prepared_query = $wpdb->prepare(
				"SELECT user_id1 
				FROM {$this->table_name} 
				WHERE user_id2 = %d AND 
					  user_id1 IN ( SELECT ID FROM {$wpdb->users} ) 
				ORDER BY time DESC",
				$user_id2
			);

		}

		$results = $wpdb->get_results( $prepared_query, ARRAY_A );

		return ! empty( $results ) ? $results : false;
	}


    /**
     * Ajax handler on click Follow button
     * @var  $user_id1
     * @var  $user_id2
     */
    function ajax_followers_follow() {
        extract( $_POST );
        $output = array();

        // Checks
        if ( ! is_user_logged_in() )
            die(0);

        if ( ! isset( $user_id1 ) || ! isset( $user_id2 ) )
            die(0);

        if ( ! is_numeric( $user_id1 ) || ! is_numeric( $user_id2 ) )
            die(0);

        if ( ! $this->can_follow( $user_id1, $user_id2 ) )
            die(0);

        if ( $this->followed( $user_id1, $user_id2 ) )
            die(0);

        $this->add( $user_id1, $user_id2 );

        $output['btn'] = $this->follow_button( $user_id1, $user_id2 ); // following user id , current user id

        do_action( 'um_followers_after_user_follow', $user_id1, $user_id2 );

        $output = json_encode( $output );
        if ( is_array( $output ) ) {
            print_r( $output );
        } else {
            echo $output;
        }
        die;

    }

    /**
     * Ajax handler on click UnFollow button
     * @var  $user_id1
     * @var  $user_id2
     */
    function ajax_followers_unfollow() {
        extract( $_POST );
        $output = array();

        // Checks
        if ( ! is_user_logged_in() )
            die(0);

        if ( ! isset( $user_id1 ) || ! isset( $user_id2 ) )
            die(0);

        if ( ! is_numeric( $user_id1 ) || ! is_numeric( $user_id2 ) )
            die(0);

        if ( ! $this->can_follow( $user_id1, $user_id2 ) )
            die(0);

        if ( ! $this->followed( $user_id1, $user_id2 ) )
            die(0);

        $this->remove( $user_id1, $user_id2 );

        $output['btn'] = $this->follow_button( $user_id1, $user_id2 );

        do_action( 'um_followers_after_user_unfollow', $user_id1, $user_id2 );

        $output = json_encode( $output );
        if ( is_array( $output ) ) {
            print_r( $output );
        } else {
            echo $output;
        }
        die;

    }

}