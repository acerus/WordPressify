<?php
namespace um_ext\um_friends\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Friends_Main_API {

	function __construct() {

		global $wpdb;
		$this->table_name = $wpdb->prefix . "um_friends";

	}

	/***
	***	@Checks if user enabled email notification
	***/
	function enabled_email( $user_id ) {
		$_enable_new_friend = true;
		if ( get_user_meta( $user_id, '_enable_new_friend', true ) == 'yes' ) {
			$_enable_new_friend = 1;
		} else if ( get_user_meta( $user_id, '_enable_new_friend', true ) == 'no' ) {
			$_enable_new_friend = 0;
		}
		return $_enable_new_friend;
	}

	/***
	***	@Show the friends list URL
	***/
	function friends_link( $user_id ) {
		$nav_link = um_user_profile_url();
		$nav_link = add_query_arg('profiletab', 'friends', $nav_link );
		return $nav_link;
	}
	
	function menu_ui( $position, $element, $trigger, $items ) {
		
		$output = '<div class="um-dropdown" data-element="' . $element . '" data-position="' . $position . '" data-trigger="' . $trigger . '">
			<div class="um-dropdown-b">
				<div class="um-dropdown-arr"><i class=""></i></div>
				<ul>';
				
				foreach( $items as $k => $v ) {
					
				$output .= '<li>' . $v . '</li>';
					
				}
		$output .= '</ul>
			</div>
		</div>';
		
		return $output;
	}

	/***
	***	@Show the friend button for two users
	***/
	function friend_button( $user_id1, $user_id2, $twobtn = false ) {
		$res = '';
		if ( ! is_user_logged_in() ) {
			$redirect = um_get_core_page( 'register' );
			$redirect = add_query_arg( 'redirect_to', UM()->permalinks()->get_current_url(), $redirect );
			$res = '<a href="' . $redirect . '" class="um-login-to-friend-btn um-button um-alt">'. __( 'Add Friend', 'um-friends' ). '</a>';
			return $res;
		}

		if ( $this->can_friend( $user_id1, $user_id2 ) ) {

		if ( ! $this->is_friend( $user_id1, $user_id2 ) ) {

			if ( $pending = $this->is_friend_pending( $user_id1, $user_id2 ) ) {

				if ( $pending == $user_id2 ) { // User should respond
					
					if ( $twobtn == false ) {
						
						$res = '<div class="um-friend-respond-zone">
							<a href="#" class="um-friend-respond-btn um-button um-alt" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Respond to Friend Request','um-friends'). '</a>';

						$items = array(
							'confirm' 	=> '<a href="#" class="um-friend-accept-btn" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Confirm','um-friends'). '</a>',
							'delete' 	=> '<a href="#" class="um-friend-reject-btn" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Delete Request','um-friends'). '</a>',
							'cancel' 	=> '<a href="#" class="um-dropdown-hide">'.__('Cancel','um-friends').'</a>',
						);

						$res .= $this->menu_ui( 'bc', '.um-friend-respond-zone', 'click', $items );
						$res .= '</div>';
					
					} else {
						$res = '<a href="#" class="um-friend-accept-btn um-button" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Confirm','um-friends'). '</a>';
						$res .= '&nbsp;&nbsp;<a href="#" class="um-friend-reject-btn um-button um-alt" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Delete Request','um-friends'). '</a>';
					}
					
				} else {
					$res = '<a href="#" class="um-friend-pending-btn um-button um-alt" data-cancel-friend-request="' . __('Cancel Friend Request','um-friends') . '" data-pending-friend-request="' . __('Friend Request Sent','um-friends') . '" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Friend Request Sent','um-friends'). '</a>';
				}
				
			} else {
				$res = '<a href="#" class="um-friend-btn um-button um-alt" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'">'. __('Add Friend','um-friends'). '</a>';
			}
		} else {
			
			$res = '<a href="#" class="um-unfriend-btn um-button um-alt" data-user_id1="'.$user_id1.'" data-user_id2="'.$user_id2.'" data-friends="'.__('Friends','um-friends').'"  data-unfriend="'.__('Unfriend','um-friends').'">'. __('Friends','um-friends'). '</a>';
		
		}

		}
		return $res;
	}

	/**
	 * If user can friend
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function can_friend( $user_id1, $user_id2 ) {
		if ( ! is_user_logged_in() )
			return true;

		$roles1 = UM()->roles()->get_all_user_roles( $user_id1 );

		$role2 = UM()->roles()->get_priority_user_role( $user_id2 );
		$role_data2 = UM()->roles()->role_data( $role2 );
		$role_data2 = apply_filters( 'um_user_permissions_filter', $role_data2, $user_id2 );

		if ( ! $role_data2['can_friend'] )
			return false;

		if ( ! empty( $role_data2['can_friend_roles'] ) &&
		     ( empty( $roles1 ) || count( array_intersect( $roles1, maybe_unserialize( $role_data2['can_friend_roles'] ) ) ) <= 0 ) )
			return false;

		if ( $user_id1 != $user_id2 && is_user_logged_in() )
			return true;

		return false;
	}


	/**
	 * Get the count of friends
	 *
	 * @param int $user_id
	 * @return null|string
	 */
	function count_friends_plain( $user_id = 0 ) {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) 
			FROM {$this->table_name} 
			WHERE status = 1 AND 
				  ( user_id1= %d OR user_id2 = %d )",
			$user_id,
			$user_id
		) );

		return $count;
	}


	/**
	 * Get the count of received requests
	 *
	 * @param int $user_id
	 * @return int
	 */
	function count_friend_requests_received( $user_id = 0 ) {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) 
			FROM {$this->table_name} 
			WHERE status = 0 AND 
				  user_id1 = %d",
			$user_id
		) );

		return absint( $count );
	}


	/**
	 * Get the count of sent requests
	 *
	 * @param int $user_id
	 * @return int
	 */
	function count_friend_requests_sent( $user_id = 0 ) {
		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) 
			FROM {$this->table_name} 
			WHERE status = 0 AND 
				  user_id2 = %d",
			$user_id
		) );

		return absint( $count );
	}

	/***
	***	@Get the count of friends in nice format
	***/
	function count_friends( $user_id = 0, $html = true ) {
		$count = $this->count_friends_plain( $user_id );
		if ( $html )
			return '<span class="um-ajax-count-friends">' . number_format( $count ) . '</span>';
		return number_format( $count );
	}

	/**
	 * Add a friend action
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function add( $user_id1, $user_id2 ) {
		global $wpdb;

		// if already friends do not add
		if ( $this->is_friend( $user_id1, $user_id2 ) )
			return false;

		$result = $wpdb->insert(
			$this->table_name,
			array(
				'time'      => current_time( 'mysql' ),
				'user_id1'  => $user_id1,
				'user_id2'  => $user_id2,
				'status'    => 0
			)
		);

		return $result;
	}


	/**
	 * Approve friend
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function approve( $user_id1, $user_id2 ) {
		global $wpdb;

		// if already friends do not add
		if ( $this->is_friend( $user_id1, $user_id2 ) )
			return false;

		$wpdb->update(
			$this->table_name,
			array(
				'status' => 1
			),
			array(
				'user_id1' => $user_id2,
				'user_id2' => $user_id1
			)
		);
	}

	/**
	 * Removes a friend connection
	 *
	 * @param $user_id1
	 * @param $user_id2
	 */
	function remove( $user_id1, $user_id2 ) {
		global $wpdb;

		$table_name = $this->table_name;

		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$table_name} 
			WHERE ( user_id1 = %d AND user_id2 = %d ) OR 
				  ( user_id1 = %d AND user_id2 = %d )",
			$user_id2,
			$user_id1,
			$user_id1,
			$user_id2
		) );
	}

	/**
	 * cancel a pending friend connection
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function cancel( $user_id1, $user_id2 ) {
		global $wpdb;

		// Not applicable to pending requests
		if ( $this->is_friend( $user_id1, $user_id2 ) )
			return false;

		$table_name = $this->table_name;
		
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$table_name} 
			WHERE status = 0 AND 
				  ( ( user_id1 = %d AND user_id2 = %d ) OR 
					( user_id1 = %d AND user_id2 = %d ) )",
			$user_id2,
			$user_id1,
			$user_id1,
			$user_id2
		) );

		return true;
	}


	/**
	 * Checks if user is friend of another user
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function is_friend( $user_id1, $user_id2 ) {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id1 
			FROM {$this->table_name} 
			WHERE status = 1 AND 
				  ( ( user_id1 = %d AND user_id2 = %d ) OR 
				    ( user_id1 = %d AND user_id2 = %d ) ) 
			LIMIT 1",
			$user_id2,
			$user_id1,
			$user_id1,
			$user_id2
		) );

		if ( $results && isset( $results[0] ) )
			return true;

		return false;
	}


	/**
	 * Checks if user is pending friend of another user
	 *
	 * @param $user_id1
	 * @param $user_id2
	 * @return bool
	 */
	function is_friend_pending( $user_id1, $user_id2 ) {
		global $wpdb;
		
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id1 
			FROM {$this->table_name} 
			WHERE status = 0 AND 
				  ( ( user_id1 = %d AND user_id2 = %d ) OR 
				    ( user_id1 = %d AND user_id2 = %d ) ) 
			LIMIT 1",
			$user_id2,
			$user_id1,
			$user_id1,
			$user_id2
		) );

		if ( $results && isset( $results[0] ) )
			return $results[0]->user_id1;

		return false;
	}

	/**
	 * Get friends as array
	 *
	 * @param $user_id1
	 * @return array|bool|null|object
	 */
	function friends( $user_id1 ) {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id1, user_id2 
			FROM {$this->table_name} 
			WHERE status = 1 AND 
				  ( user_id1 = %d OR user_id2 = %d ) 
			ORDER BY time DESC",
			$user_id1,
			$user_id1
		), ARRAY_A );

		if ( $results )
			return $results;

		return false;
	}


	/**
	 * Get friend requests as array
	 *
	 * @param $user_id1
	 * @return array|bool|null|object
	 */
	function friend_reqs( $user_id1 ) {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id2 
			FROM {$this->table_name} 
			WHERE status = 0 AND 
				  user_id1 = %d 
			ORDER BY time DESC",
			$user_id1
		), ARRAY_A );

		if ( $results )
			return $results;

		return false;
	}


	/**
	 * Get friend requests as array
	 *
	 * @param $user_id1
	 * @return array|bool|null|object
	 */
	function friend_reqs_sent( $user_id1 ) {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT user_id1 
			FROM {$this->table_name} 
			WHERE status = 0 AND 
				  user_id2 = %d
			ORDER BY time DESC",
			$user_id1
		), ARRAY_A );

		if ( $results )
			return $results;

		return false;
	}


    /**
     * Ajax Approve friend request
     */
    function ajax_friends_approve() {
        extract( $_POST );
        $output = array();

        // Checks
        if ( ! is_user_logged_in() )
            die(0);

        if ( ! isset( $user_id1 ) || ! isset( $user_id2 ) )
            die(0);

        if ( ! is_numeric( $user_id1 ) || ! is_numeric( $user_id2 ) )
            die(0);

        if ( ! $this->can_friend( $user_id1, $user_id2 ) )
            die(0);

        if ( $this->is_friend( $user_id1, $user_id2 ) )
            die(0);

        $this->approve( $user_id1, $user_id2 );

        $output['btn'] = $this->friend_button( $user_id1, $user_id2 );

        do_action('um_friends_after_user_friend', $user_id1, $user_id2 );

        $output=json_encode($output);
        if(is_array($output)){print_r($output);}else{echo $output;}die;

    }


    /**
     * Ajax Add friend
     */
    function ajax_friends_add() {

	    /**
	     * @var $user_id1
	     * @var $user_id2
	     */
	    extract( $_POST );
        $output = array();

        // Checks
        if ( ! is_user_logged_in() ) {
	        wp_send_json_error();
        }
        if ( ! isset( $user_id1 ) || !isset( $user_id2 ) ) {
	        wp_send_json_error();
        }
        if ( ! is_numeric( $user_id1 ) || !is_numeric( $user_id2 ) ) {
	        wp_send_json_error();
        }
        if ( ! $this->can_friend( $user_id1, $user_id2 ) ) {
	        wp_send_json_error();
        }
        if ( $this->is_friend( $user_id1, $user_id2 ) ) {
	        wp_send_json_error();
        }

        $this->add( $user_id1, $user_id2 );

        $output['btn'] = $this->friend_button( $user_id1, $user_id2 ); // following user id , current user id

        do_action('um_friends_after_user_friend_request', $user_id1, $user_id2 );

	    wp_send_json_success( $output );
    }


    /**
     * Ajax UnFriend
     */
    function ajax_friends_unfriend() {
        extract($_POST);
        $output = array();

        // Checks
        if ( ! is_user_logged_in() ) die(0);
        if ( ! isset( $user_id1 ) || !isset( $user_id2 ) ) die(0);
        if ( ! is_numeric( $user_id1 ) || !is_numeric( $user_id2 ) ) die(0);
        if ( ! $this->can_friend( $user_id1, $user_id2 ) ) die(0);

        $this->remove( $user_id1, $user_id2 );

        $output['btn'] = $this->friend_button( $user_id1, $user_id2 );

        do_action('um_friends_after_user_unfriend', $user_id1, $user_id2 );

        $output=json_encode($output);
        if(is_array($output)){print_r($output);}else{echo $output;}die;

    }


    /**
     * Ajax cancel friend's request
     */
    function ajax_friends_cancel_request() {
        extract( $_POST );
        $output = array();

        // Checks
        if ( ! is_user_logged_in() )
            die(0);

        if ( ! isset( $user_id1 ) || ! isset( $user_id2 ) )
            die(0);

        if ( ! is_numeric( $user_id1 ) || ! is_numeric( $user_id2 ) )
            die(0);

        if ( ! $this->can_friend( $user_id1, $user_id2 ) )
            die(0);

        $this->cancel( $user_id1, $user_id2 );

        $output['btn'] = $this->friend_button( $user_id1, $user_id2 );

        do_action( 'um_friends_after_user_cancel_request', $user_id1, $user_id2 );

        $output = json_encode( $output );
        if ( is_array( $output ) ) {
            print_r( $output );
        } else {
            echo $output;
        }
        die;

    }

}
