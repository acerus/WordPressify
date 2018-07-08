<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/


/*
 *  Action
 *
 *      new_question
 *      new_answer
 *      q_solved
 *      q_not_solved
 *      vote_up
 *      vote_down
 *      new_comment
 *      comment_flag
 *      comment_unflag
 *      comment_vote_down
 *      comment_vote_up
 *      best_answer
 *      best_answer_removed
 *      flag
 *      unflag
 *      user_follow
 *      user_unfollow
 *
 *
 * */



add_action('qa_action_notification_save','add_question_track_notification', 10, 1);


function add_question_track_notification($notification_data){


	$q_id = isset($notification_data['q_id']) ? $notification_data['q_id'] : '';
	$a_id = isset($notification_data['a_id']) ? $notification_data['a_id'] : '';
	$c_id = isset($notification_data['c_id']) ? $notification_data['c_id'] : '';
	$user_id = isset($notification_data['user_id']) ? $notification_data['user_id'] : '';
	$subscriber_id = isset($notification_data['subscriber_id']) ? $notification_data['subscriber_id'] : '';
	$action = isset($notification_data['action']) ? $notification_data['action'] : '';



	global $wpdb;
	$table = $wpdb->prefix . "qa_notification";

	$status = 'unread';

	$gmt_offset = get_option('gmt_offset');
	$datetime = date('Y-m-d h:i:s', strtotime('+'.$gmt_offset.' hour'));

	if($action=='new_question'){

		if( $subscriber_id == $user_id ) return;

		$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
			array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}

	elseif($action=='new_answer'){

		$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
			array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

	}



	elseif($action=='q_solved' || $action=='q_not_solved'){


		if(!empty($subscriber_id) && is_array($subscriber_id)){

			foreach ($subscriber_id as $id){
				if($id != $user_id){
					$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
						array	( '', $q_id, $a_id, $c_id, $user_id, $id, $action, $status, $datetime)));
				}


			}
		}

	}

	elseif($action=='vote_up' || $action=='vote_down'){

		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}




	}


	elseif($action=='new_comment'){

		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}

	}

	elseif($action=='comment_flag'){



		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}

	}


	elseif($action=='comment_unflag'){

		//$qa_answer_question_id = get_post_meta($a_id, 'qa_answer_question_id', true);
		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}


	}



	elseif($action=='comment_vote_down' || $action=='comment_vote_up'){


		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}




	}




	elseif($action=='best_answer'){



		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}





	}


	elseif($action=='best_answer_removed'){

		//$qa_answer_question_id = get_post_meta($a_id, 'qa_answer_question_id', true);

		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}

	}

	elseif($action=='flag' || $action=='unflag'){

		if($subscriber_id != $user_id){

			$wpdb->query( $wpdb->prepare("INSERT INTO $table 
			( id, q_id, a_id, c_id, user_id, subscriber_id,  action, status, datetime )
			VALUES	( %d, %d, %d, %d, %d, %d, %s, %s, %s )",
				array	( '', $q_id, $a_id, $c_id, $user_id, $subscriber_id, $action, $status, $datetime)));

		}




	}


}

