<?php
namespace um_ext\um_notifications\core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Notifications_Main_API {

	/***
	***	@Did user enable this web notification?
	***/
	function user_enabled( $key, $user_id ) {
		if ( ! UM()->options()->get('log_'.$key ) ) {
			return false;
		}
		$prefs = get_user_meta( $user_id, '_notifications_prefs', true );
		if ( $prefs && isset($prefs[$key]) && !$prefs[$key] ) {
			return false;
		}

		// if all checkboxes were not selected
		if( $prefs === array('') ) {
			return false;
		}

		return true;
	}

	/***
	***	@Register notification types
	***/
	function get_log_types() {

		$array['upgrade_role'] = array(
			'title' => __('Role upgrade','um-notifications'),
			'template' => __('Your membership level has been changed from <strong>{role_pre}</strong> to <strong>{role_post}</strong>','um-notifications'),
			'account_desc' => __('When my membership level is changed','um-notifications'),
		);

		$array['comment_reply'] = array(
			'title' => __('New comment reply','um-notifications'),
			'template' => __('<strong>{member}</strong> has replied to one of your comments.','um-notifications'),
			'account_desc' => __('When a member replies to one of my comments','um-notifications'),
		);

		$array['user_comment'] = array(
			'title' => __('New user comment','um-notifications'),
			'template' => __('<strong>{member}</strong> has commented on your <strong>post</strong>. <span class="b1">"{comment_excerpt}"</span>','um-notifications'),
			'account_desc' => __('When a member comments on my posts','um-notifications'),
		);

		$array['guest_comment'] = array(
			'title' => __('New guest comment','um-notifications'),
			'template' => __('A guest has commented on your <strong>post</strong>. <span class="b1">"{comment_excerpt}"</span>','um-notifications'),
			'account_desc' => __('When a guest comments on my posts','um-notifications'),
		);

		$array['profile_view'] = array(
			'title' => __('User view profile','um-notifications'),
			'template' => __('<strong>{member}</strong> has viewed your profile.','um-notifications'),
			'account_desc' => __('When a member views my profile','um-notifications'),
		);

		$array['profile_view_guest'] = array(
			'title' => __('Guest view profile','um-notifications'),
			'template' => __('A guest has viewed your profile.','um-notifications'),
			'account_desc' => __('When a guest views my profile','um-notifications'),
		);

		$array = apply_filters('um_notifications_core_log_types', $array );

		return $array;

	}

	/**
	 * Get unread count by user ID
	 *
	 * @param int $user_id
	 * @return int
	 */
	function unread_count( $user_id = 0 ) {
		global $wpdb;

		$user_id = ( $user_id > 0 ) ? $user_id : get_current_user_id();

		$table_name = $wpdb->prefix . "um_notifications";
		$results = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT id FROM {$table_name} WHERE user = %d AND status='unread'",
							$user_id
						)
					);

		if ( $wpdb->num_rows == 0 ) {
			return 0;
		} else {
			return $wpdb->num_rows;
		}
	}

	/***
	***	@Deletes a notification by its ID
	***/
	function delete_log( $notification_id ) {
		global $wpdb;
		if ( !is_user_logged_in() ) return;
		$user_id = get_current_user_id();
		$table_name = $wpdb->prefix . "um_notifications";
		$wpdb->delete( $table_name, array('id' => $notification_id) );
	}

	/***
	***	@Gets icon for notification
	***/
	function get_icon( $type ) {
		$output = null;
		switch( $type ) {

			default:
				$output = apply_filters('um_notifications_get_icon', $output, $type );
				break;

			case 'user_comment':
			case 'guest_comment':
				$output = '<i class="um-faicon-comment" style="color: #DB6CD2"></i>';
				break;

			case 'user_review':
				$output = '<i class="um-faicon-star" style="color: #FFD700"></i>';
				break;

			case 'profile_view':
			case 'profile_view_guest':
				$output = '<i class="um-faicon-eye" style="color: #6CB9DB"></i>';
				break;

			case 'bbpress_user_reply':
			case 'bbpress_guest_reply':
				$output = '<i class="um-faicon-comments" style="color: #67E264"></i>';
				break;

			case 'mycred_award':
			case 'mycred_custom_notification':
			case 'mycred_points_sent':
				$output = '<i class="um-faicon-plus-circle" style="color: #DFB250"></i>';
				break;

			case 'upgrade_role':
				$output = '<i class="um-faicon-exchange" style="color: #999"></i>';
				break;

		}

		return $output;
	}

	/***
	***	@Gets time in user-friendly way
	***/
	function nice_time( $time ) {
		
		$from_time_unix = strtotime( $time );
		$offset = get_option( 'gmt_offset' );
		$offset = apply_filters("um_notifications_time_offset", $offset );

		$from_time = $from_time_unix - $offset * HOUR_IN_SECONDS; 
		$from_time = apply_filters("um_notifications_time_from", $from_time, $time );

		$current_time = current_time('timestamp') - $offset * HOUR_IN_SECONDS;
		$current_time = apply_filters("um_notifications_current_time", $current_time );

		$nice_time = human_time_diff( $from_time, $current_time  );
		$nice_time = apply_filters("um_notifications_time_nice", $nice_time, $from_time, $current_time );

		$time = sprintf(__('%s ago','um-notifications'), $nice_time );

		return $time;
	}

	/**
	 * Gets notifications
	 *
	 * @param int $per_page
	 * @param bool $unread_only
	 * @param bool $count
	 * @return array|bool|int|null|object
	 */
	function get_notifications( $per_page = 10, $unread_only = false, $count = false ) {
		global $wpdb;
		$user_id = get_current_user_id();
		$table_name = $wpdb->prefix . "um_notifications";

		if ( $unread_only == 'unread' && $count == true ) {

			$results = $wpdb->get_results( $wpdb->prepare(
				"SELECT * 
				FROM {$table_name} 
				WHERE user = %d AND 
					  status = 'unread'",
				$user_id
			) );

			return $wpdb->num_rows;

		} else if ( $unread_only == 'unread' ) {

			$results = $wpdb->get_results( $wpdb->prepare(
				"SELECT * 
				FROM {$table_name} 
				WHERE user = %d AND 
					  status='unread' 
				ORDER BY time DESC 
				LIMIT %d",
				$user_id,
				$per_page
			) );

		} else {

			$results = $wpdb->get_results( $wpdb->prepare(
				"SELECT * 
				FROM {$table_name} 
				WHERE user = %d 
				ORDER BY time DESC 
				LIMIT %d",
				$user_id,
				$per_page
			) );

		}

		if ( $results )
			return $results;

		return false;
	}

	/**
	 * Saves a notification
	 *
	 * @param $user_id
	 * @param $type
	 * @param array $vars
	 */
	function store_notification( $user_id, $type, $vars = array() ) {

		global $wpdb;

		$url = '';

		// Check if user opted-in
		if ( !$this->user_enabled( $type, $user_id ) ) return;

		$content = $this->get_notify_content( $type, $vars );
		if ( $vars ) {
			foreach( $vars as $key => $var ) {
				$content = str_replace('{'.$key.'}', $var, $content);
			}
		}

		$content = implode(' ',array_unique(explode(' ', $content)));

		if ( $vars && isset($vars['photo']) ) {
			$photo = $vars['photo'];
		} else {
			$photo = um_get_default_avatar_uri();
		}

		if ( $vars && isset($vars['notification_uri']) ) {
			$url = $vars['notification_uri'];
		}

		$table_name = $wpdb->prefix . "um_notifications";

		// Try to update a similar log
		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT * 
			FROM {$table_name} 
			WHERE user = %d AND 
				  type = %s AND 
				  content = %s 
			ORDER BY time DESC 
			LIMIT 1",
			$user_id,
			$type,
			$content
		) );

		$exclude_type = array( 
			'comment_reply',
			'new_wall_post',
			'new_wall_comment',
			'bbpress_user_reply',
			'bbpress_guest_reply'
		);

		$exclude_type = apply_filters('um_notifications_exclude_types', $exclude_type );

		if ( $results && isset( $results[0] ) && ! in_array( $type , $exclude_type ) ) {
			$wpdb->update(
				$table_name,
				array(
					'status' 	=> 'unread',
					'time' 		=> current_time( 'mysql' ),
					'url'		=> $url
				),
				array(
					'user' 		=> $user_id,
					'type' 		=> $type,
					'content'	=> $content
				)
			);
			$do_not_insert = true;
		}

		if ( isset( $do_not_insert ) ) return;

		$wpdb->insert(
			$table_name,
			array(
				'time' => current_time( 'mysql' ),
				'user' => $user_id,
				'status' => 'unread',
				'photo' => $photo,
				'type' => $type,
				'url' => $url,
				'content' => $content
			)
		);

	}

	/***
	***	@Get notification content
	***/
	function get_notify_content( $type, $vars = array() ) {
		$content = null;
		$content = UM()->options()->get('log_' . $type . '_template');
		$content = apply_filters("um_notification_modify_entry_{$type}", $content, $vars);
		return $content;
	}

	/***
	***	@Mark as read
	***/
	function set_as_read( $notification_id ) {
		global $wpdb;
		$user_id = get_current_user_id();
		$table_name = $wpdb->prefix . "um_notifications";
		$wpdb->update(
			$table_name,
			array(
				'status' 	=> 'read',
			),
			array(
				'user' 		=> $user_id,
				'id' 		=> $notification_id
			)
		);
	}

	/***
	***	@Checks if notification is unread
	***/
	function is_unread( $notification_id ) {
		$user_id = get_current_user_id();
		$saved_id = get_post_meta( $notification_id, '_belongs_to', true );
		if ( $saved_id == $user_id ) {
			$is_unread = get_post_meta( $notification_id, 'status', true );
			if ( $is_unread == 'unread' ) {
				return true;
			}
		}
		return false;
	}


    function ajax_delete_log() {
        if ( !isset( $_POST['notification_id'] ) || !is_user_logged_in() ) die(0);
        $notification_id = $_POST['notification_id'];
        $this->delete_log( $notification_id );
        die(0);
    }

    /***
     ***	@mark a notification as read
     ***/
    function ajax_mark_as_read() {
        if ( !isset( $_POST['notification_id'] ) || !is_user_logged_in() ) die(0);
        $notification_id = $_POST['notification_id'];
        $this->set_as_read( $notification_id );
        die(0);
    }

    /***
     ***	@checks for update
     ***/
    function ajax_check_update() {
        extract($_POST);

        $unread = $this->get_notifications( 0, 'unread', true );

        $refresh_count = false;

        if ( $unread ) {

            $refresh_count = ( absint( $unread ) > 9 ) ? '+9' : $unread;

            $notifications = $this->get_notifications( 1, 'unread');

            if ( $notifications ) {
                foreach( $notifications as $notification ) {

                    $unread = '<div class="um-notification ' . $notification->status . '" data-notification_id="' . $notification->id . '" data-notification_uri="'. $notification->url . '">'. '<img src="'. $notification->photo .'" alt="" class="um-notification-photo" />' . $notification->content;

                    $unread .= '<span class="b2">' . $this->get_icon( $notification->type ) . $this->nice_time( $notification->time ) . '</span>';

                    $unread .= '<span class="um-notification-hide"><a href="#"><i class="um-icon-android-close"></i></a></span></div>';

                }
            }

        }

        $output = array(
            'refresh_count' => ! empty( $refresh_count ) ? $refresh_count : 0,
            'unread' => $unread
        );

        $output=json_encode($output);
        if(is_array($output)){print_r($output);}else{echo $output;}die;

    }

}
