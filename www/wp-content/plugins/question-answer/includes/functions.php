<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 








function qa_ajax_admin_actions_submit(){
	
	foreach( $_POST['form_data'] as $d ) ${$d['name']} = isset( $d['value'] ) ? sanitize_text_field( $d['value'] ) : '';

	if( ! wp_verify_nonce( $_wpnonce, 'nonce_qa_update_post_status' ) ) die();

	wp_update_post( array( 'ID' => $post_id, 'post_status' => $post_status ) );			
	
	echo get_permalink( $post_id );
	die();
}
add_action('wp_ajax_qa_ajax_admin_actions_submit', 'qa_ajax_admin_actions_submit');
add_action('wp_ajax_nopriv_qa_ajax_admin_actions_submit', 'qa_ajax_admin_actions_submit');	




add_action('wp_head','qa_single_question_view_count');

function qa_single_question_view_count(){

    //global $post;

    if(is_singular('question')){

	    $cookie_name = 'qa_view';
	    $q_id = get_the_ID();
	    $qa_view_count = get_post_meta(get_the_ID(),'qa_view_count', true);


	    if(!isset($_COOKIE[$cookie_name.'_'.$q_id])){
		    setcookie( $cookie_name.'_'.$q_id, $q_id, time() + (86400 * 30)); // 86400 = 1 day

		    update_post_meta(get_the_ID(), 'qa_view_count', ($qa_view_count+1));
	    }
    }



}







function qa_ajax_answer_posting(){
	
	
	global $current_user;
	$userid = $current_user->ID;

	$response 			= array();
	$response['html']  	= "";
	$answer_status 		= get_option( 'qa_submitted_answer_status', 'pending' );
	$form_data_arr 		= isset( $_POST['form_data'] ) ? $_POST['form_data'] : array();

	//var_dump($form_data_arr);
	
	foreach( $form_data_arr as $data ) {
		
		if( $data['name'] == 'qa_answer_editor' ) :
			
			${$data['name']} = wp_kses( $data['value'], array(
				'a'             => array(
					'href'  => array(),
					'title' => array()
				),
				'br'            => array(),
				'em'            => array(),
				'strong'        => array(),
				'code'          => array(
					'class' => array()
				),
				'pre'          => array(
					'class' => array()
				),

				'blockquote'    => array(),
				'quote'         => array(),
				'span'          => array(
					'style' 	=> array()
				),
				'img'           => array(
					'src'    	=> array(),
					'alt'    	=> array(),
					'width'  	=> array(),
					'height' 	=> array(),
					'style'  	=> array()
				),
				'ul'            => array(),
				'li'            => array(),
				'ol'            => array(),
			) );
			
		else : 
			${$data['name']} = isset( $data['value'] ) ? sanitize_text_field( $data['value'] ) : '';
		endif;
	}
	
	if( ! is_user_logged_in() || empty( $question_id ) || ! wp_verify_nonce( $_wpnonce, 'nonce_answer_post' ) ) {
		
		$response['html'] .= sprintf( "<p class='qa_notice qa_notice_error'>%s</p>", __( 'Something went wrong!', 'question-answer' ) );
		echo json_encode( $response );
		die();
	}
	
	if( empty( $qa_answer_editor ) ) {
		
		$response['html'] .= sprintf( "<p class='qa_notice qa_notice_error'>%s</p>", __('Empty content can"t be a valid answer!', 'question-answer') );
		echo json_encode( $response );
		die();
	}
	
	$new_answer_post = array(
		'post_type'		=> 'answer',
		'post_title'    => __('#Replay', 'question-answer').' - '.qa_shorten_string($qa_answer_editor) .' by '. $current_user->user_login ,
		'post_status'   => $answer_status,
		'post_content'  => $qa_answer_editor,
	);
	$new_answer_post_ID = wp_insert_post( $new_answer_post, true );

	update_post_meta( $new_answer_post_ID, 'qa_answer_question_id', $question_id );
	update_post_meta( $new_answer_post_ID, 'qa_answer_is_private', $is_private );


	if( ! $new_answer_post_ID ) {
		
		$response['html'] .= sprintf( "<p class='qa_notice qa_notice_error'>%s</p>", __( 'Something went wrong!', 'question-answer' ) );
		echo json_encode( $response );
		die();
	}
	

	
	$q_subscriber 	= get_post_meta( $question_id, 'q_subscriber', true );



	if(empty($q_subscriber)){
		update_post_meta($question_id, 'q_subscriber',array($userid) );

	}
	else{

		if(!in_array($userid, $q_subscriber)){

			$q_subscriber = array_merge($q_subscriber, array($userid));
			update_post_meta(get_the_ID(),'q_subscriber',$q_subscriber );

		}


	}


	update_post_meta( $question_id, 'q_subscriber', $q_subscriber );


	$answer_submit_success_message = apply_filters( "qa_filter_answer_submit_success_message", "<i class='fa fa-check'></i>".__("Answer submission successful", 'question-answer'), $new_answer_post_ID );
	
	$response['html'] .= sprintf( "<p class='qa_notice qa_notice_success'>%s</p>", $answer_submit_success_message );
	$response['html'] .= sprintf( "<p class='qa_notice qa_notice_success'>%s : %s</p>", __( 'Status', 'question-answer' ), $answer_status );
	
	$response['answer_id'] = $new_answer_post_ID;

	//do_action( 'qa_action_notification_save', $question_id, $new_answer_post_ID, '', $userid, 'new_answer' );



	$question_post = get_post($question_id);
	$question_author = $question_post->post_author;

	$notification_data = array();

	$notification_data['user_id'] = get_current_user_id();
	$notification_data['q_id'] = $question_id;
	$notification_data['a_id'] = $new_answer_post_ID;
	$notification_data['c_id'] = '';
	$notification_data['subscriber_id'] = $question_author;
	$notification_data['action'] = 'new_answer';

	do_action('qa_action_notification_save', $notification_data);

	do_action( 'qa_email_action_question_submit', $question_id );

	echo json_encode( $response );
	die();
}
add_action('wp_ajax_qa_ajax_answer_posting', 'qa_ajax_answer_posting');
add_action('wp_ajax_nopriv_qa_ajax_answer_posting', 'qa_ajax_answer_posting');









function qa_ajax_answer_update(){


	global $current_user;
	$userid = $current_user->ID;

	$response 			= array();
	$response['html']  	= "";
	$can_edit_answer 		= get_option( 'qa_can_edit_answer', 'no' );
	$answer_status 		= get_option( 'qa_submitted_answer_status', 'pending' );
	$form_data_arr 		= isset( $_POST['form_data'] ) ? $_POST['form_data'] : array();



	foreach( $form_data_arr as $data ){

		if( $data['name'] == 'qa_answer_editor' ) :

			${$data['name']} = wp_kses( $data['value'], array(
				'a'             => array(
					'href'  => array(),
					'title' => array()
				),
				'br'            => array(),
				'em'            => array(),
				'strong'        => array(),
				'code'          => array(
					'class' => array()
				),
				'pre'          => array(
					'class' => array()
				),

				'blockquote'    => array(),
				'quote'         => array(),
				'span'          => array(
					'style' 	=> array()
				),
				'img'           => array(
					'src'    	=> array(),
					'alt'    	=> array(),
					'width'  	=> array(),
					'height' 	=> array(),
					'style'  	=> array()
				),
				'ul'            => array(),
				'li'            => array(),
				'ol'            => array(),
			) );

		else :
			${$data['name']} = isset( $data['value'] ) ? sanitize_text_field( $data['value'] ) : '';
		endif;
	}



	if( empty( $qa_answer_editor ) ) {

		$response['html'] .= sprintf( "<p class='qa_notice qa_notice_error'>%s</p>", __('Empty content can"t be a valid answer!', 'question-answer') );
		echo json_encode( $response );
		die();
	}

	$new_answer_post = array(
		'ID'           => $answer_id,
		//'post_status'   => $answer_status,
		'post_content'  => $qa_answer_editor,
	);


	$new_answer_post_ID = wp_update_post( $new_answer_post );

	$edit_reason_old = get_post_meta($answer_id, 'edit_reason', true);
	if(empty($edit_reason_old)) $edit_reason_old = array();

	$time = time();
	$edit_reason = array_merge($edit_reason_old, array($time=>$edit_reason));


	update_post_meta( $new_answer_post_ID, 'edit_reason', $edit_reason );
	update_post_meta( $new_answer_post_ID, 'qa_answer_is_private', $is_private );


	if( ! $new_answer_post_ID ) {

		$response['html'] .= sprintf( "<p class='qa_notice qa_notice_error'>%s</p>", __( 'Something went wrong! 2', 'question-answer' ) );
		echo json_encode( $response );
		die();
	}


	$answer_submit_success_message = apply_filters( "qa_filter_answer_submit_success_message", "<i class='fa fa-check'></i> ".__("Answer update successful", 'question-answer'), $new_answer_post_ID );

	$response['html'] .= sprintf( "<p class='qa_notice qa_notice_success'>%s</p>", $answer_submit_success_message );
	$response['html'] .= sprintf( "<p class='qa_notice qa_notice_success'>%s : %s</p>", __( 'Status', 'question-answer' ), $answer_status );

	$response['answer_id'] = $answer_id;
	$response['url'] = get_permalink($question_id);


	//do_action( 'qa_action_notification_save', $answer_id, $new_answer_post_ID, '', $userid, 'update_answer' );


	$notification_data['user_id'] = get_current_user_id();
	$notification_data['q_id'] = $answer_id;
	$notification_data['a_id'] = $new_answer_post_ID;
	$notification_data['c_id'] = '';
	$notification_data['action'] = 'update_answer';

	do_action('qa_action_notification_save', $notification_data);









	//do_action( 'qa_email_action_question_submit', $answer_id );

	echo json_encode( $response );
	die();
}

add_action('wp_ajax_qa_ajax_answer_update', 'qa_ajax_answer_update');
//add_action('wp_ajax_nopriv_qa_ajax_answer_update', 'qa_ajax_answer_update');




function qa_ajax_load_more_comments(){

	$response = array();

    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $paged = isset($_POST['paged']) ? $_POST['paged'] : '';
    $per_page = isset($_POST['paged']) ? $_POST['per_page'] : '';
    $total_comments = isset($_POST['total_comments']) ? $_POST['total_comments'] : '';
	$user_ID = get_current_user_id();

	$count_comments = wp_count_comments($post_id);
	$total_comments = $count_comments->approved;

	$comment_remain_count = $total_comments - (($paged+1)*3);

	if($comment_remain_count<0){
		$comment_remain_count = 0;
    }


	$comments = get_comments( array(
		'post_id' 	=> $post_id,
		'order' 	=> 'ASC',
		'status'	=> 'approve',
		'number' => 3,
		'offset' => ($paged*3),
	) );


	ob_start();

	if(!empty($comments)):

		foreach( $comments as $comment ) {

			$comment_date 	= new DateTime($comment->comment_date);
			$comment_date 	= $comment_date->format('M d, Y h:i A');
			$comment_author	= get_comment_author( $comment->comment_ID );

			//var_dump($comment->user_id );

			$comment_author_user_data = get_user_by('email', $comment->comment_author_email);
			//var_dump($comment_author_user_data);

			if(!empty($comment->comment_author)){

				$comment_author = $comment->comment_author;
			}

			else{
				$comment_author =  __('Anonymous', 'question-answer');
			}


			$qa_flag_comment 	= get_comment_meta( $comment->comment_ID, 'qa_flag_comment', true );

			if(!is_array($qa_flag_comment)){
				$qa_flag_comment = array();
			}


			$flag_comment_count 		= sizeof($qa_flag_comment);

			//$flag_comment_count 		= count(explode(',', $qa_flag_comment ) ) - 1;
			//var_export($qa_flag_comment);

			?>
            <div id="comment-<?php echo $comment->comment_ID; ?>" class="qa-single-comment single-reply loading">
                <div class="qa-avatar float_left"><?php echo get_avatar( $comment->comment_author_email, "30" ); ?></div>
                <div class="qa-comment-content">
                    <div class="ap-comment-header">
						<?php

						if(!empty($comment_author_user_data->display_name)):
							?>
                            <div href="#" class="ap-comment-author qa-user-card-loader" author_id="<?php echo $comment_author_user_data->ID; ?>" has_loaded="no">
                                <?php echo $comment_author_user_data->display_name; ?>
                                <div class="qa-user-card">
                                    <div class="card-loading">
                                        <i class="fa fa-cog fa-spin"></i>
                                    </div>
                                    <div class="card-data"></div>
                                </div>
                            </div> - <a class="comment-link" href="#comment-<?php echo $comment->comment_ID; ?>"> <?php echo $comment_date; ?></a>
							<?php

						endif;


						if( array_key_exists($user_ID, $qa_flag_comment) && $qa_flag_comment[$user_ID]['type']=='flag'  ) {

							$flag_text = __('Unflag', 'question-answer');

						} else {

							$flag_text = __('Flag', 'question-answer');
						}

						?>
                        <div class="qa-comment-flag qa-comment-flag-action float_right" comment_id="<?php echo $comment->comment_ID; ?>">
                            <i class="fa fa-flag flag-icon"></i>
                            <span class="flag-text"><?php echo $flag_text; ?></span>
                            <span class="flag-count">(<?php echo $flag_comment_count; ?>)</span>
                            <span class="waiting"><i class="fa fa-cog fa-spin"></i></span>

                        </div>

						<?php

						$qa_vote_comment 	= get_comment_meta( $comment->comment_ID, 'qa_vote_comment', true );
						if(!is_array($qa_vote_comment)){
							$qa_vote_comment = array();
						}

						$down_vote_count = 0;
						$up_vote_count = 0;

						if(!empty($qa_vote_comment)){

							foreach ($qa_vote_comment as $comment_vote){

								$type = $comment_vote['type'];

								if($type=='down'){
									$down_vote_count += 1;
								}
								else{
									$up_vote_count += 1;
								}

							}



							$vote_count 		= $up_vote_count-$down_vote_count;
						}
						else{
							$vote_count 		= 0;

						}




						//$vote_count 		= sizeof($qa_vote_comment);
						$comment_votted_up_class = '';
						$comment_votted_down_class = '';

						if( array_key_exists($user_ID, $qa_vote_comment) && $qa_vote_comment[$user_ID]['type']=='up'  ) {

							$comment_votted_up_class = 'comment-votted';

						}

						if( array_key_exists($user_ID, $qa_vote_comment) && $qa_vote_comment[$user_ID]['type']=='down'  ) {

							$comment_votted_down_class = 'comment-votted';

						}


						?>
                        <div class="comment-vote float_right">
                            <span vote_type="up" class="comment-thumb-up comment-vote-action <?php echo  $comment_votted_up_class; ?>" comment_id="<?php echo $comment->comment_ID; ?>">
                                <?php echo apply_filters('qa_filter_comment_vote_up_html','<i class="fa s_22 fa-thumbs-up"></i>'); ?>
                            </span>
                            <span class="comment-vote-count comment-vote-count-<?php echo $comment->comment_ID; ?>">
                                <?php echo apply_filters('qa_filter_comment_vote_count_html', $vote_count); ?>
                                <?php //echo $review_count; ?>
                            </span>
                            <span vote_type="down" class="comment-thumb-down comment-vote-action <?php echo $comment_votted_down_class; ?>" comment_id="<?php echo $comment->comment_ID; ?>">
                                <?php echo apply_filters('qa_filter_comment_vote_down_html','<i class="fa s_22 fa-thumbs-down"></i>'); ?>
                            </span>
                        </div>







                    </div>
                    <div class="ap-comment-texts">
						<?php

						ob_start();
						qa_filter_badwords( comment_text( $comment->comment_ID ) );
						echo ob_get_clean();


						?>
                    </div>
                </div>

            </div>
			<?php

		}

		$response['html_output'] = ob_get_clean();
		$response['has_comment'] = 'yes';
		$response['comment_remain_count'] = $comment_remain_count;

    else:
	    $response['html_output'] = '';
	    $response['has_comment'] = 'no';

    endif;




	echo json_encode( $response );
	die();
}

add_action('wp_ajax_qa_ajax_load_more_comments', 'qa_ajax_load_more_comments');
add_action('wp_ajax_nopriv_qa_ajax_load_more_comments', 'qa_ajax_load_more_comments');



function qa_ajax_user_card(){

	$response = array();
	$author_id = isset($_POST['author_id']) ? sanitize_text_field($_POST['author_id']): '';

	if(!empty($author_id)):


		//$response['html'] = $author_id;

		ob_start();
		do_action('qa_question_user_card', $author_id);
		$response['html'] = ob_get_clean();


    endif;




    echo json_encode( $response );
    die();
}

add_action('wp_ajax_qa_ajax_user_card', 'qa_ajax_user_card');
add_action('wp_ajax_nopriv_qa_ajax_user_card', 'qa_ajax_user_card');





function qa_ajax_user_follow(){

	$author_id = sanitize_text_field($_POST['author_id']);
	$response 	= array();
	$user_info = get_userdata( $author_id );

	//do_action('qa_user_follow', $author_id);


	$gmt_offset = get_option('gmt_offset');
	$datetime = date('Y-m-d H:i:s', strtotime('+'.$gmt_offset.' hour'));


	if(is_user_logged_in()):

		$logged_user_id = get_current_user_id();

		$total_follower = (int)get_the_author_meta( 'total_follower', $author_id );
		$total_following = (int)get_the_author_meta( 'total_following', $logged_user_id );


		if($logged_user_id==$author_id):
			$response['toast_html'] = __("You can not follow yourself.", 'user-profile');

		else:

			global $wpdb;
			$table = $wpdb->prefix . "qa_follow";

			$follow_result = $wpdb->get_results("SELECT * FROM $table WHERE author_id = '$author_id' AND follower_id = '$logged_user_id'", ARRAY_A);

			$already_insert = $wpdb->num_rows;
			if($already_insert > 0 ):

				$wpdb->delete( $table, array( 'author_id' => $author_id, 'follower_id' => $logged_user_id), array( '%d','%d' ) );

				$response['toast_html'] = 'You are not following <strong>'. $user_info->display_name.'</strong>';
				$response['action'] = 'unfollow';
				$response['follower_id'] = $logged_user_id;

				$total_follower -=1;

				if($total_follower<0){$total_follower = 0; }
				update_user_meta( $author_id, 'total_follower', $total_follower );

				$total_following -=1;

				if($total_following<0){$total_following = 0; }
				update_user_meta( $logged_user_id, 'total_following', $total_following );


				//do_action('qa_action_notification_save', $q_id, $a_id, $c_id, $user_id, $action);

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = '';
				$notification_data['a_id'] = '';
				$notification_data['c_id'] = '';
				$notification_data['subscriber_id'] = $author_id;
				$notification_data['action'] = 'follow_user';


				do_action('qa_action_notification_save', $notification_data);



			else:

				$wpdb->query( $wpdb->prepare("INSERT INTO $table 
												( id, author_id, follower_id, datetime)
										VALUES	( %d, %d, %d, %s)",
					array	( '', $author_id, $logged_user_id,  $datetime )
				));

				$response['toast_html'] = 'Thanks for following <strong>'.$user_info->display_name.'</strong>';
				$response['action'] = 'following';
				$response['follower_id'] = $logged_user_id;

				$total_follower +=1;
				update_user_meta( $author_id, 'total_follower', $total_follower );

				$total_following +=1;
				update_user_meta( $logged_user_id, 'total_following', $total_following );


			endif;

		endif;

	else:
		$response['toast_html'] = __('Please login first.', 'user-profile');
	endif;

	echo json_encode($response);
	die();
}











add_action('wp_ajax_qa_ajax_user_follow', 'qa_ajax_user_follow');
add_action('wp_ajax_nopriv_qa_ajax_user_follow', 'qa_ajax_user_follow');
























function qa_ajax_getnotifications(){
	
	$response = array();
	$response['count'] = qa_breadcrumb_total_count(); 
	
	ob_start();

	$userid = get_current_user_id();
	global $wpdb;
	$limit = 10;

	$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE status='unread' AND subscriber_id='$userid' ORDER BY id DESC LIMIT $limit" );

	foreach( $entries as $entry ){


		$id = $entry->id;
		$q_id = $entry->q_id;
		$a_id = $entry->a_id;
		$c_id = $entry->c_id;
		$user_id = $entry->user_id;
		$subscriber_id = $entry->subscriber_id;
		$action = $entry->action;
		$datetime = $entry->datetime;

		$entry_date = new DateTime($datetime);
		$datetime = $entry_date->format('M d, Y h:i A');

		$user = get_user_by( 'ID', $user_id);

		if(!empty($user->display_name)){
			$user_display_name = $user->display_name;
		}
		else{
			$user_display_name = __('Anonymous', 'question-answer');
		}

		?>
		<div class="item">
		<?php

		echo '<img src="'.get_avatar_url($user_id,  array('size'=>40)).'" class="thumb">';

		$notify_mark_html = '<span class="notify-mark" notify_id="'.$id.'" ><i class="fa fa-bell-o" aria-hidden="true"></i></span>';


		if( $action == 'new_question' ) {

			echo '<span class="name">'.$user_display_name.'</span> '.__('posted', 'question-answer').' <span class="action">'.__('New Question',  'question-answer').'</span> <a href="'.get_permalink($q_id).'" class="link">'.get_the_title($q_id).'</a> ';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php

		}

		elseif( $action == 'new_answer' ) {


			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Answered', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($q_id).'</a> ';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php

		}


		elseif( $action == 'best_answer' ) {

			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Choosed best answer', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php


		}

		elseif( $action == 'best_answer_removed' ) {

			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Removed best answer', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php


		}

		elseif($action=='new_comment'){

			$comment_post_data = get_comment( $c_id );

			if(!empty($comment_post_data->comment_post_ID)):

				$comment_post_id = $comment_post_data->comment_post_ID;

				$comment_post_type = get_post_type($comment_post_id);

				if($comment_post_type=='answer'){

					$flag_post_type = 'answer';

					$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );


				}
				else{
					$flag_post_type = 'question';


				}

				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Commented', 'question-answer').'</span> on '.$flag_post_type.' <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($a_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			endif;


		}



		elseif($action=='comment_flag'){

			$comment_post_data = get_comment( $c_id );
			$comment_post_id = $comment_post_data->comment_post_ID ;

			$comment_post_type = get_post_type($comment_post_id);

			if($comment_post_type=='answer'){

				$flag_post_type = 'answer';
				$link_extra = '#comment-'.$c_id;
				$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
			}
			else{
				$flag_post_type = 'question';
				$link_extra = '#comment-'.$c_id;
				$q_id = $comment_post_id;
			}



			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Flagged comment', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php



		}


        elseif($action=='comment_unflag'){

			$comment_post_data = get_comment( $c_id );
			$comment_post_id = $comment_post_data->comment_post_ID ;

			$comment_post_type = get_post_type($comment_post_id);

			if($comment_post_type=='answer'){

				$flag_post_type = 'answer';
				$link_extra = '#comment-'.$c_id;
				$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
			}
			else{
				$flag_post_type = 'question';
				$link_extra = '#comment-'.$c_id;
				$q_id = $comment_post_id;
			}



			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Un-flagged comment', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

			?>
            <div class="meta">

                <span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
            </div>
			<?php



		}









		elseif($action=='comment_vote_up'){

			$comment_post_data = get_comment( $c_id );
			$comment_post_id = $comment_post_data->comment_post_ID ;

			$comment_post_type = get_post_type($comment_post_id);

			if($comment_post_type=='answer'){

				$flag_post_type = 'answer';
				$link_extra = '#comment-'.$c_id;
				$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
			}
			else{
				$flag_post_type = 'question';
				$link_extra = '#comment-'.$c_id;
				$q_id = $comment_post_id;
			}



			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('comment vote up', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php



		}

		elseif($action=='comment_vote_down'){

			$comment_post_data = get_comment( $c_id );
			$comment_post_id = $comment_post_data->comment_post_ID ;

			$comment_post_type = get_post_type($comment_post_id);

			if($comment_post_type=='answer'){

				$flag_post_type = 'answer';
				$link_extra = '#comment-'.$c_id;
				$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
			}
			else{
				$flag_post_type = 'question';
				$link_extra = '#comment-'.$c_id;
				$q_id = $comment_post_id;
			}



			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('comment vote down', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php


		}








		elseif($action=='vote_up'){

			$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Vote Up', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php



		}
		elseif($action=='vote_down'){

			$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
			echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Vote Down', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php



		}


		elseif($action=='q_solved'){

			echo ' <span class="name">'.$user_display_name.'</span> '.__('marked', 'question-answer').' <span class="action">'.__('Solved', 'question-answer').'</span> <a href="'.get_permalink($q_id).'" class="link">'.get_the_title($q_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php


		}

		elseif($action=='q_not_solved'){

			echo ' <span class="name">'.$user_display_name.'</span> '.__('marked', 'question-answer').' <span class="action">'.__('Not Solved','question-answer').'</span> <a href="'.get_permalink($q_id).'" class="link">'.get_the_title($q_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php


		}

		elseif($action=='flag'){

			if(!empty($a_id)){

				$flag_post_type = 'answer';
				$link_extra = '#single-answer-'.$a_id;
				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				$post_id = $a_id;
			}
			if(!empty($q_id)){

				$flag_post_type = 'question';
				$link_extra = '';
				$post_id = $q_id;
			}


			$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
			echo ' <span class="name">'.$user_display_name.'</span> '.sprintf(__('flagged your %s', 'question-answer'), $flag_post_type).' <span class="name"></span> <a href="'.get_permalink($q_id).$link_extra.'" class="link">'.get_the_title($post_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php


		}

		elseif($action=='unflag'){

			if(!empty($a_id)){

				$flag_post_type = 'answer';
				$link_extra = '#single-answer-'.$a_id;
				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				$post_id = $a_id;
			}
			if(!empty($q_id)){

				$flag_post_type = 'question';
				$link_extra = '';
				$post_id = $q_id;
			}



			echo ' <span class="name">'.$user_display_name.'</span> '.$flag_post_type.' <span class="action">'.__('unflagged ', 'question-answer').'</span> <a href="'.get_permalink($q_id).$link_extra.'" class="link">'.get_the_title($post_id).'</a>';

			?>
			<div class="meta">

				<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
				<?php echo $notify_mark_html; ?>
			</div>
			<?php



		}

		?>
		</div>
		<?php

	}


	$response['html'] = ob_get_contents();
	ob_end_clean();

	echo json_encode($response);
	die();
}
add_action('wp_ajax_qa_ajax_getnotifications', 'qa_ajax_getnotifications');
add_action('wp_ajax_nopriv_qa_ajax_getnotifications', 'qa_ajax_getnotifications');	






function qa_keyword_add_custom_column( $columns ) {
    return array_merge( $columns,
        array( 'search_count' => __( 'Search Count' ) ) );
}
add_filter( 'manage_qa_keyword_posts_columns' , 'qa_keyword_add_custom_column' );


function qa_keyword_posts_custom_column_display( $column, $post_id ) {
    if ($column == 'search_count'){

        $search_count = (int) get_post_meta(get_the_id($post_id), 'search_count', true);

        echo $search_count;

    }
}

add_action( 'manage_qa_keyword_posts_custom_column' , 'qa_keyword_posts_custom_column_display', 10, 2 );














	function qa_action_breadcrumb_add_question_button(){

            $qa_ask_button_bg_color = get_option('qa_ask_button_bg_color');
            $qa_ask_button_text_color = get_option('qa_ask_button_text_color');

			$qa_page_question_post = get_option('qa_page_question_post');
			$qa_page_question_post_url = get_permalink($qa_page_question_post);
			
			if(!empty($qa_page_question_post)){

				echo '<a class="add-question" href="'.$qa_page_question_post_url.'">'.__('Ask question', 'question-answer').'</a>';

                global $qa_css;

                $qa_ask_button_bg_color = get_option( 'qa_ask_button_bg_color' );
                if( empty( $qa_ask_button_bg_color ) ) $qa_ask_button_bg_color = '';

                $qa_ask_button_text_color = get_option( 'qa_ask_button_text_color' );
                if( empty( $qa_ask_button_text_color ) ) $qa_ask_button_text_color = '';

                $qa_css .= ".qa-breadcrumb .add-question{ color: $qa_ask_button_text_color !important; background: $qa_ask_button_bg_color; }";


				}

		}
		
		
	//add_action('qa_action_breadcrumb_before', 'qa_action_breadcrumb_add_question_button', 0);





	function qa_filter_poll_input_fields($input_fields){
		
		$qa_enable_poll = get_option('qa_enable_poll');
		
		if($qa_enable_poll == 'yes'){
			
			$meta_fields = array(
		
								'polls'=>array(
									'meta_key'=>'polls',
									'css_class'=>'polls',
									'placeholder'=>'',
									'required'=>'no',														
									'title'=>__('Polls', 'question-answer'),
									'option_details'=>__('Add your polls', 'question-answer'),					
									'input_type'=>'text_multi', // text, radio, checkbox, select,
									'input_values'=>array(time()=>'',), // could be array
									'field_args'=> array('dummy'=>'Dummy',),
									),
															
								);
			
			
			$input_fields['meta_fields'] = $meta_fields;
			
			
			}
		

		return $input_fields;
		
		
		
		}
		
	add_filter('qa_filter_question_input_fields', 'qa_filter_poll_input_fields');

















	function qa_all_roles() {
		
		 global $wp_roles;
		 $roles = $wp_roles->get_names();
		
		return  $roles;
		 // Below code will print the all list of roles.
		 //echo '<pre>'.var_export($wp_roles, true).'</pre>';  
		
		}

	add_shortcode('qa_all_roles','qa_all_roles');





	function qa_time_ago($post_time) {

		$gmt_offset = get_option('gmt_offset');
		$today = date('Y-m-d h:i:s', strtotime('+'.$gmt_offset.' hour'));
		$today = strtotime($today);
		//var_dump(strtotime($today));

		$post_time = get_post_time();
		//$today = time();		
		
		//var_dump($today);
		
		
		$diff = $today - $post_time;
		
		$minute = floor(($diff % 3600)/60);
		$hour = floor(($diff % 86400)/3600);
		$day = floor(($diff % 2592000)/86400);
		$month = floor($diff/2592000);
		$year = floor($diff/(86400*365));		
		
		if($year>0){
			return number_format_i18n($year) .' '.__('year ago', 'question-answer');
			}
				
		elseif($month > 0 && $day<=12 ){
			return number_format_i18n($month) .' '.__('month ago', 'question-answer');
			}
			
		elseif($day > 0 && $day<=30){
			return number_format_i18n($day).' '.__('day ago', 'question-answer');
			}
			
		elseif($hour > 0 && $hour<=24){
			return number_format_i18n($hour).' '.__('hour ago', 'question-answer');
			}		
			
		elseif($minute > 0 && $minute<60){
			return number_format_i18n($minute).' '.__('minute ago', 'question-answer');
			}	
				
		else{
			return $diff.' '.__('second ago', 'question-answer');;
			}
		
	}	
	
	
	
	function qa_post_duration($post_id) {


		$gmt_offset = get_option('gmt_offset');
		$today = date('Y-m-d h:i:s', strtotime('+'.$gmt_offset.' hour'));
		$today = strtotime($today);
		//var_dump(strtotime($today));

		$post_time = get_post_time();
		//$today = time();		
		
		//var_dump($today);
		
		
		$diff = $today - $post_time;
		
		$minute = floor(($diff % 3600)/60);
		$hour = floor(($diff % 86400)/3600);
		$day = floor(($diff % 2592000)/86400);
		$month = floor($diff/2592000);
		$year = floor($diff/(86400*365));		
		
		if($year>0){
			return number_format_i18n($year) .' '.__('year ago', 'question-answer');
			}
				
		elseif($month > 0 && $day<=12 ){
			return number_format_i18n($month) .' '.__('month ago', 'question-answer');
			}
			
		elseif($day > 0 && $day<=30){
			return number_format_i18n($day).' '.__('day ago', 'question-answer');
			}
			
		elseif($hour > 0 && $hour<=24){
			return number_format_i18n($hour).' '.__('hour ago', 'question-answer');
			}		
			
		elseif($minute > 0 && $minute<60){
			return number_format_i18n($minute).' '.__('minute ago', 'question-answer');
			}	
				
		else{
			return $diff.' '.__('second ago', 'question-answer');
			}
		
	}	
	
	
	
	
	
	
	

function qa__get_terms($taxonomy){

		
		//$cat_id = (int)$_POST['cat_id'];
		if(!isset($taxonomy)){
			$taxonomy = 'ads_cat';
			}

		$args=array(
		  'orderby' => 'name',
		  'order' => 'ASC',
		  'taxonomy' => $taxonomy,
		  'hide_empty' => false,
		  );
		
		$categories = get_categories($args);

			
		$html = '';

		foreach($categories as $category){
			
				$name = $category->name;
				$cat_ID = $category->cat_ID;
				$terms[$cat_ID] = $name;
				//$html.= '<li cat-id="'.$cat_ID.'"><i class="fa fa-check"></i> '.$name;
				//$html.= '</li>';
			}

		return $terms;
	}






	function qa_shorten_string($string, $wordcount = 3, $RemoveHtml = true, $Extension = ' ...' ) {
		
		if( $RemoveHtml ) $string = strip_tags($string); 
		$array = explode( " ", $string );
		if ( count($array) > $wordcount ) {
			array_splice($array, $wordcount);
			return implode(" ", $array) . $Extension;
		}
		else return $string;
    }

	function qa_single_question_template($single_template) {
		
		 global $post;
	
		 if ($post->post_type == 'question') {
			  $single_template = QA_PLUGIN_DIR . 'templates/single-question/single-question.php';
		 }
		 
		 return $single_template;
	}

	//add_filter( 'single_template', 'qa_single_question_template' );




function qa_single_question_content($content) {

	global $post;

	if ($post->post_type == 'question') {
		
		ob_start();
		include( QA_PLUGIN_DIR . 'templates/single-question/single-question.php' );
		return ob_get_clean();
	}
	else{
		return $content;
	}

}
add_filter( 'the_content', 'qa_single_question_content' );



add_filter('comments_template','qa_single_comments_template');

function qa_single_comments_template($templt){
	global $post;


	//var_dump($post);

	if($post->post_type == 'question'){ // assuming there is a post type called business
		//return dirname(__FILE__) . '/reviews.php';
		return  QA_PLUGIN_DIR. 'templates/single-question/comment.php';
	}

}







/*

	function qa_single_answer_template($single_template) {
		 global $post;
	
		 if ($post->post_type == 'answer') {
			  $single_template = QA_PLUGIN_DIR . 'templates/single-answer.php';
		 }
		 return $single_template;
	}
	add_filter( 'single_template', 'qa_single_answer_template' );	
	
*/


function qa_ajax_get_keyword_suggestion() {

    $response = array();
    //$keyword	= $_POST['keyword'];

    if(!empty($_POST['keyword']['term'])){
        $keyword = sanitize_text_field($_POST['keyword']['term']);
    }
    else{
        $keyword = '';
    }


    $wp_query = new WP_Query( array (
        'post_type' => 'qa_keyword',
        'post_status' => 'publish',
        's' => $keyword,

        'posts_per_page' => 10,

    ) );

    if ( $wp_query->have_posts() ) :
        while ( $wp_query->have_posts() ) : $wp_query->the_post();
            $response[get_the_id()] = get_the_title();
        endwhile;
    endif;

    //$response['status'] =  'read';

    //echo 'gggggggggggggg';

    echo json_encode($response);
    die();
}


add_action('wp_ajax_qa_ajax_get_keyword_suggestion', 'qa_ajax_get_keyword_suggestion');
add_action('wp_ajax_nopriv_qa_ajax_get_keyword_suggestion', 'qa_ajax_get_keyword_suggestion');













function qa_ajax_best_answer() {
		
		$answer_id 	= (int)sanitize_text_field($_POST['answer_id']);
		$question_id 	= get_post_meta( $answer_id, 'qa_answer_question_id', true );
		$best_answer_id	= get_post_meta( $question_id, 'qa_meta_best_answer', true );

		$question_data = get_post($question_id); 
		$q_author = $question_data->post_author;
		
		$response 	= array();
		
		
		if(!is_user_logged_in()){
			
			$response['toast'] .= '<i class="fa fa-check"></i> ' . __('Please login first.', 'question-answer');
			echo json_encode($response);
			die(); 
			}		
		
		if($q_author != get_current_user_id()){
			
			$response['toast'] .= '<i class="fa fa-check"></i> ' . __('Sorry you can\'t choose best answer.', 'question-answer');
			echo json_encode($response);
			die(); 
			}
		
		
		if( $best_answer_id == $answer_id ) {
			
			update_post_meta( $question_id, 'qa_meta_best_answer', '' );
			$response['status'] = 'removed';
			$response['toast'] .= '<i class="fa fa-times"></i> ' .__('Removed Best answer', 'question-answer');


			$question_post = get_post($answer_id);
			$question_author = $question_post->post_author;

			$notification_data = array();

			$notification_data['user_id'] = get_current_user_id();
			$notification_data['q_id'] = $question_id;
			$notification_data['a_id'] = $answer_id;
			$notification_data['c_id'] = '';
			$notification_data['subscriber_id'] = $question_author;
			$notification_data['action'] = 'best_answer_removed';

			do_action('qa_action_notification_save', $notification_data);

			} 
		
		else{
			
			update_post_meta( $question_id, 'qa_meta_best_answer', $answer_id );
			$response['status'] = 'updated';
			$response['toast'] .= '<i class="fa fa-check"></i> ' . __('Marked as Best answer', 'question-answer');

			$question_post = get_post($answer_id);
			$question_author = $question_post->post_author;

			$notification_data = array();

			$notification_data['user_id'] = get_current_user_id();
			$notification_data['q_id'] = $question_id;
			$notification_data['a_id'] = $answer_id;
			$notification_data['c_id'] = '';
			$notification_data['subscriber_id'] = $question_author;
			$notification_data['action'] = 'best_answer';

			do_action('qa_action_notification_save', $notification_data);
	
		}

		echo json_encode($response);
		die();
	}
	add_action('wp_ajax_qa_ajax_best_answer', 'qa_ajax_best_answer');
	add_action('wp_ajax_nopriv_qa_ajax_best_answer', 'qa_ajax_best_answer');	



	function qa_ajax_notify_mark() { 
		
		$notify_id 	= (int)sanitize_text_field($_POST['notify_id']);		
		global $wpdb;
		$table = $wpdb->prefix . "qa_notification";	
		
		$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE  id='$notify_id'" );

		$status = $entries[0]->status;
		
		if($status == 'unread'){
			
				$wpdb->update( 
					$table, 
					array( 
						'status' => 'read',	// string
					), 
					array( 'id' => $notify_id ), 
					array( 
						'%s',	// value1
					), 
					array( '%d' ) 
				);
				
				$response['status'] =  'read';
				
				$response['icon'] =  '<i class="fa fa-bell-slash"></i>';				
			}
		elseif($status == 'read'){
				$wpdb->update( 
					$table, 
					array( 
						'status' => 'unread',	// string
					), 
					array( 'id' => $notify_id ), 
					array( 
						'%s',	// value1
					), 
					array( '%d' ) 
				);
			
				$response['status'] =  'unread';
				$response['icon'] =  '<i class="fa fa-bell-o"></i>';
				
		}

		echo json_encode($response);
		die();
	}


	add_action('wp_ajax_qa_ajax_notify_mark', 'qa_ajax_notify_mark');
	add_action('wp_ajax_nopriv_qa_ajax_notify_mark', 'qa_ajax_notify_mark');
	

	
	function qa_ajax_featured_switch() {

		$post_id = (int)sanitize_text_field($_POST['post_id']);
		
		$response = array();
		
		if( !empty( $post_id ) ) {
		
			$qa_featured_questions = get_option( 'qa_featured_questions', array() );
		
			if ( ($key = array_search( $post_id , $qa_featured_questions)) !== false ) {
				
				unset($qa_featured_questions[$key]);
				$response['featured_class'] = 'qa-featured-no';
				$response['toast'] = '<i class="fa fa-times"></i> '.__( 'Removed from featured', 'question-answer' );

			} else {
				
				array_push( $qa_featured_questions, $post_id );
				$response['featured_class'] = 'qa-featured-yes';
				$response['toast'] = '<i class="fa fa-check"></i> '.__( 'Successfully featured', 'question-answer' );

			}

			update_option( 'qa_featured_questions', $qa_featured_questions );
		}	
		
		echo json_encode($response);
		die();
	}

	add_action('wp_ajax_qa_ajax_featured_switch', 'qa_ajax_featured_switch');
	add_action('wp_ajax_nopriv_qa_ajax_featured_switch', 'qa_ajax_featured_switch');
	
	function qa_do_comment_flag_action() {
		
		$qa_flag_comment = '';
		$action 	= sanitize_text_field($_POST['act']);
		$comment_id = (int)sanitize_text_field($_POST['comment_id']);
		$user_id 	= sanitize_text_field($_POST['user_id']);
		
		$qa_flag_comment = get_comment_meta( $comment_id, 'qa_flag_comment', true );
	
		if( empty($qa_flag_comment) ) 
			add_comment_meta( $comment_id, 'qa_flag_comment', $qa_flag_comment ); 
		
		if ( $action == 'flag' ) {
		
			if( !qa_search_user($user_id,$qa_flag_comment) ) {
				$qa_flag_comment .= $user_id . ',';
				update_comment_meta( $comment_id, 'qa_flag_comment', $qa_flag_comment );

				$comment_data = get_comment( $comment_id );
				$comment_author_id = $comment_data->user_id  ;



				$notification_data = array();

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = '';
				$notification_data['a_id'] = '';
				$notification_data['c_id'] = $comment_id;
				$notification_data['subscriber_id'] = $comment_author_id;
				$notification_data['action'] = 'comment_flag';

				//do_action('qa_action_notification_save', $notification_data);

				//do_action('qa_action_comment_flag', $comment_id);
				
				
			} 
		}
		
		if ( $action == 'unflag' ) {
			
			
			$qa_flag_comment = str_replace( $user_id.',', ' ', $qa_flag_comment);
			update_comment_meta( $comment_id, 'qa_flag_comment', $qa_flag_comment );


			$comment_data = get_comment( $comment_id );
			$comment_author_id = $comment_data->user_id  ;



			$notification_data = array();

			$notification_data['user_id'] = get_current_user_id();
			$notification_data['q_id'] = '';
			$notification_data['a_id'] = '';
			$notification_data['c_id'] = $comment_id;
			$notification_data['subscriber_id'] = $comment_author_id;
			$notification_data['action'] = 'comment_unflag';

			//do_action('qa_action_notification_save', $notification_data);


			//do_action('qa_action_comment_unflag', $comment_id);
		} 
		
		
		$count_flag = count(explode(',', $qa_flag_comment ) ) - 1;
		if( !empty($user_id) && qa_search_user($user_id, $qa_flag_comment) ) {
			$flag_html = __('Unflag','question-answer').' ('.$count_flag.')<span class="qa_ttt qa_w_160"><i class="fa fa-undo"></i> '.__('Undo Report', 'question-answer').'</span>';
		} else {
			$flag_html =  __('Flag','question-answer').' ('.$count_flag.')<span class="qa_ttt qa_w_160"><i class="fa fa-thumbs-down"></i> '.__('Report this', 'question-answer').'</span>';
		}

		echo $flag_html;
		die();
	}

	add_action('wp_ajax_qa_do_comment_flag_action', 'qa_do_comment_flag_action');
	add_action('wp_ajax_nopriv_qa_do_comment_flag_action', 'qa_do_comment_flag_action');





function qa_ajax_comment_flag(){

    $response = array();

    $comment_id = (int)sanitize_text_field($_POST['comment_id']);

    $user_ID		= get_current_user_id();
    $qa_flag_comment 	= get_comment_meta( $comment_id, 'qa_flag_comment', true );
    //$qa_flag 	= get_post_meta( $post_id, 'qa_flag', true );
    $flag_count 		= sizeof($qa_flag_comment);


    if(!is_user_logged_in()):

        $response['is_error'] = 'yes';
        $response['message'] = __('Please login', 'question-answer');
        $response['flag_count'] = $flag_count;

    else:


        if(is_array($qa_flag_comment)){

            if(array_key_exists($user_ID, $qa_flag_comment)){

                $flag_type = $qa_flag_comment[$user_ID]['type'];

                unset($flag[$user_ID]);

                if($flag_type=='flag'){

                    $response['flag_text'] = 'Flag';
                    $flag[$user_ID] = array('type'=>'unflag');
                    $response['message'] = __('Report removed.', 'question-answer');
                    $response['flag_count'] = $flag_count-1;
                    $action_type = 'comment_unflag';
                }
                elseif($flag_type=='unflag'){

                    $response['flag_text'] = 'Unflag';
                    $flag[$user_ID] = array('type'=>'flag');
                    $response['message'] = __('Thanks for report.', 'question-answer');
                    $response['flag_count'] = $flag_count;
                    $action_type = 'comment_flag';

                }
            }
            else{
                $response['flag_text'] = 'Unflag';

                $flag[$user_ID] = array('type'=>'flag');
                $response['message'] = __('Thanks for report.', 'question-answer');
                $response['flag_count'] = $flag_count+1;

                $action_type = 'comment_flag';

            }

            $flag = array_replace($qa_flag_comment, $flag);
            //update_post_meta($post_id, 'qa_flag', $flag);
            update_comment_meta( $comment_id, 'qa_flag_comment', $flag );
        }
        else{

            $flag[$user_ID] = array('type'=>'flag');
            //update_post_meta($post_id, 'qa_flag', $flag);
            update_comment_meta( $comment_id, 'qa_flag_comment', $flag );

            $response['flag_text'] = 'Unflag';
            $response['message'] = __('Thanks for report.', 'question-answer');
            $response['flag_count'] = $flag_count;
            $action_type = 'comment_flag';


        }

	    $comment_data = get_comment( $comment_id );
	    $comment_author_id = $comment_data->user_id;


	    $notification_data = array();

	    $notification_data['user_id'] = get_current_user_id();
	    $notification_data['q_id'] = '';
	    $notification_data['a_id'] = '';
	    $notification_data['c_id'] = $comment_id;
	    $notification_data['subscriber_id'] = $comment_author_id;
	    $notification_data['action'] = $action_type;

	    do_action('qa_action_notification_save', $notification_data);


        $response['is_error'] = 'no';



    endif;

    echo json_encode($response);
    //echo $qa_flag_value;
    die();
}

add_action('wp_ajax_qa_ajax_comment_flag', 'qa_ajax_comment_flag');
add_action('wp_ajax_nopriv_qa_ajax_comment_flag', 'qa_ajax_comment_flag');






function qa_ajax_comment_vote(){

    $response = array();

    $comment_id = (int)sanitize_text_field($_POST['comment_id']);
    //$comment_id_author = get_comment_author( $comment_ID );
    $vote_type = sanitize_text_field($_POST['vote_type']);


    $user_ID		= get_current_user_id();
    $qa_vote_comment 	= get_comment_meta( $comment_id, 'qa_vote_comment', true );






    if(!is_user_logged_in()):


        $down_vote_count = 0;
        $up_vote_count = 0;

        if(!empty($qa_vote_comment)){

            foreach ($qa_vote_comment as $comment){

                $type = $comment['type'];

                if($type=='down'){
                    $down_vote_count += 1;
                }
                else{
                    $up_vote_count += 1;
                }
            }

            $vote_count 		= $up_vote_count-$down_vote_count;
        }
        else{
            $vote_count 		= 0;

        }



        $response['is_error'] = 'yes';
        $response['message'] = __('Please login', 'question-answer');
        $response['vote_count'] = $vote_count;

    else:

        if(is_array($qa_vote_comment)){

            if(array_key_exists($user_ID, $qa_vote_comment)){

                $response['is_error'] = 'no';
                //$vote_type = $qa_vote_comment[$user_ID]['type'];

                unset($qa_vote_comment[$user_ID]);

                if($vote_type=='down'){

                    $vote[$user_ID] = array('type'=>'down');
                    $response['message'] = __('Vote Down.', 'question-answer');
                    //$response['vote_count'] = $vote_count-1;
                    $action_type = 'comment_vote_down';

                }
                elseif($vote_type=='up'){

                    $vote[$user_ID] = array('type'=>'up');
                    $response['message'] = __('Vote Up.', 'question-answer');
                    //$response['vote_count'] = $vote_count;
                    $action_type = 'comment_vote_up';

                }

            }
            else{
                $response['is_error'] = 'no';

                if($vote_type=='down'){

                    $vote[$user_ID] = array('type'=>'down');
                    $response['message'] = __('Vote Down.', 'question-answer');
                    //$response['vote_count'] = $vote_count-1;
                    $action_type = 'comment_vote_down';

                }
                elseif($vote_type=='up'){

                    $vote[$user_ID] = array('type'=>'up');
                    $response['message'] = __('Vote Up.', 'question-answer');
                    $response['vote_count'] = $vote_count;
                    $action_type = 'comment_vote_up';
                    //$response['vote_count'] = $vote_count+1;

                }



            }

            $vote = array_replace($qa_vote_comment, $vote);
            //update_post_meta($post_id, 'qa_flag', $flag);
            update_comment_meta( $comment_id, 'qa_vote_comment', $vote );
        }
        else{

            if($vote_type=='down'){

                $vote[$user_ID] = array('type'=>'down');
                $response['message'] = __('Vote Down.', 'question-answer');
                $action_type = 'comment_vote_down';
                //$vote_count = $vote_count-1;


            }
            else{

                $vote[$user_ID] = array('type'=>'up');
                $response['message'] = __('Vote Up.', 'question-answer');
                $action_type = 'comment_vote_up';
                //$vote_count = $vote_count+1;;
            }

            update_comment_meta( $comment_id, 'qa_vote_comment', $vote );

        }

	    $comment_data = get_comment( $comment_id );
	    $comment_author_id = $comment_data->user_id  ;


	    $notification_data = array();

	    $notification_data['user_id'] = get_current_user_id();
	    $notification_data['q_id'] = '';
	    $notification_data['a_id'] = '';
	    $notification_data['c_id'] = $comment_id;
	    $notification_data['subscriber_id'] = $comment_author_id;
	    $notification_data['action'] = $action_type;

	    do_action('qa_action_notification_save', $notification_data);



        $response['is_error'] = 'no';



    endif;

    $qa_vote_comment 	= get_comment_meta( $comment_id, 'qa_vote_comment', true );
    $down_vote_count = 0;
    $up_vote_count = 0;

    if(!empty($qa_vote_comment)){

        foreach ($qa_vote_comment as $comment){

            $type = $comment['type'];

            if($type=='down'){
                $down_vote_count += 1;
            }
            else{
                $up_vote_count += 1;
            }
        }

        $vote_count 		= $up_vote_count-$down_vote_count;
    }
    else{
        $vote_count 		= 0;

    }


    $response['vote_count'] = $vote_count;






    echo json_encode($response);
    //echo $qa_flag_value;
    die();
}

add_action('wp_ajax_qa_ajax_comment_vote', 'qa_ajax_comment_vote');
add_action('wp_ajax_nopriv_qa_ajax_comment_vote', 'qa_ajax_comment_vote');









function qa_ajax_post_flag(){

    $response = array();

    $post_id = (int)sanitize_text_field($_POST['post_id']);
    $user_ID		= get_current_user_id();
    $qa_flag 	= get_post_meta( $post_id, 'qa_flag', true );
    $flag_count 		= sizeof($qa_flag);



    if(!is_user_logged_in()):
        $response['is_error'] = 'yes';
        $response['message'] = __('Please login', 'question-answer');
        $response['flag_count'] = $flag_count;
    else:

        if(!empty($qa_flag)){

            if(array_key_exists($user_ID, $qa_flag)){

                $flag_type = $qa_flag[$user_ID]['type'];

                unset($qa_flag[$user_ID]);

                if($flag_type=='flag'){

                    $response['flag_text'] = 'Flag';
                    $flag[$user_ID] = array('type'=>'unflag');
                    $response['message'] = __('Report removed.', 'question-answer');
                    $response['flag_count'] = $flag_count-1;

                    $flag_action_type = 'unflag';

                }
                elseif($flag_type=='unflag'){

                    $response['flag_text'] = 'Unflag';
                    $flag[$user_ID] = array('type'=>'flag');
                    $response['message'] = __('Thanks for report.', 'question-answer');
                    $response['flag_count'] = $flag_count;
                    $flag_action_type = 'flag';
                }


            }
            else{
                $response['flag_text'] = 'Unflag';

                $flag[$user_ID] = array('type'=>'flag');
                $response['message'] = __('Thanks for report.', 'question-answer');
                $response['flag_count'] = $flag_count+1;

                $flag_action_type = 'flag';


            }

            $flag = array_replace($qa_flag, $flag);
            update_post_meta($post_id, 'qa_flag', $flag);
        }
        else{

            $flag[$user_ID] = array('type'=>'flag');
            update_post_meta($post_id, 'qa_flag', $flag);

            $response['flag_text'] = 'Unflag';
            $response['message'] = __('Thanks for report.', 'question-answer');
            $response['flag_count'] = $flag_count;
            $flag_action_type = 'flag';



        }

        $response['is_error'] = 'no';



        $post_type = get_post_type($post_id);
        $a_id = '';
        $q_id = '';

        if($post_type=='answer'){

            $a_id = $post_id;


        }
        elseif($post_type=='question'){

            $q_id = $post_id;
        }

	    $question_post = get_post($post_id);
	    $subscriber_id = $question_post->post_author;

	    $notification_data = array();

	    $notification_data['user_id'] = get_current_user_id();
	    $notification_data['q_id'] = $q_id;
	    $notification_data['a_id'] = $a_id;
	    $notification_data['c_id'] = '';
	    $notification_data['subscriber_id'] = $subscriber_id;
	    $notification_data['action'] = $flag_action_type;

	    do_action('qa_action_notification_save', $notification_data);


    endif;

    echo json_encode($response);
    //echo $qa_flag_value;
    die();
}

add_action('wp_ajax_qa_ajax_post_flag', 'qa_ajax_post_flag');
add_action('wp_ajax_nopriv_qa_ajax_post_flag', 'qa_ajax_post_flag');














	
	function qa_answer_reply_action() {

		if(is_user_logged_in()):
		
		
			$post_id 	= (int)sanitize_text_field($_POST['post_id']);
			$reply_msg 	= esc_textarea($_POST['reply_msg']);
			
			$current_user = wp_get_current_user();
			$userid = get_current_user_id();
			
			$data = array(
				'comment_post_ID' 		=> $post_id,
				'comment_author' 		=> $current_user->user_login,
				'comment_author_email' 	=> $current_user->user_email,
				'comment_content' 		=> $reply_msg,
				'comment_date' 			=> current_time('mysql'),
				'comment_approved' 		=> 1,
                'user_id' 		        => $userid,
			);
	
			if( !empty( $reply_msg ) ) {
				$new_comment_ID = wp_insert_comment($data);
				
				
				
				$a_subscriber = get_post_meta($post_id, 'a_subscriber', true);
				
				if(empty($a_subscriber)){
					update_post_meta($post_id,'a_subscriber',array($userid) );
					
					}
				else{
					
					if(!in_array($userid,$a_subscriber)){
						
						$a_subscriber = array_merge($a_subscriber, array($userid));
						update_post_meta($post_id,'a_subscriber',$a_subscriber );
						
						}
					}

				$question_post = get_post($post_id);
				$question_author = $question_post->post_author;

				$notification_data = array();

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = '';
				$notification_data['a_id'] = $post_id;
				$notification_data['c_id'] = $new_comment_ID;
				$notification_data['subscriber_id'] = $question_author;
				$notification_data['action'] = 'new_comment';

				do_action('qa_action_notification_save', $notification_data);


				do_action('qa_action_answer_comment', $new_comment_ID);
				
				
				
			}
			
			if ( !empty($new_comment_ID) ) {
				
				$comment_data = get_comment($new_comment_ID);
				
				echo '
				<div class="qa-single-comment single-reply">
					
					<div class="qa-avatar float_left">'.get_avatar( $current_user->user_email, "30" ).'</div>
					<div class="qa-comment-content">
						<div class="ap-comment-header">
							<a href="#" class="ap-comment-author">'.$current_user->display_name.'</a>'; 
							
							$comment_date = new DateTime();
							$comment_date = $comment_date->format('M d, Y h:i A');
							
							echo '<a class="comment-link" href="#comment-'.$new_comment_ID.'"> - '.$comment_date.' </a>
						</div>
						<div class="ap-comment-texts">'.(qa_filter_badwords(wpautop($comment_data->comment_content))).'</div>
					</div>
				
				</div>';
			} else {
				echo __('Something went wrong !', 'question-answer');
			}
			
			
		else:
			echo __('Please login to post comments', 'question-answer');
		
		endif;


		die();
	}

	add_action('wp_ajax_qa_answer_reply_action', 'qa_answer_reply_action');
	add_action('wp_ajax_nopriv_qa_answer_reply_action', 'qa_answer_reply_action');
	
	
	
	
	
	
	function qa_ajax_poll(){	
	
		$q_id = sanitize_text_field($_POST['q_id']);
		$data_id = sanitize_text_field($_POST['data_id']);		
		
		if(is_user_logged_in()){
			
			$user_id = get_current_user_id();
			$polls = get_post_meta($q_id, 'polls', true);	
			$polls = unserialize($polls);
				
			$poll_result = get_post_meta($q_id, 'poll_result', true);
			
			$poll_result[$user_id] =  $data_id;
	
			update_post_meta( $q_id, 'poll_result', $poll_result );
	
			
			//echo $data_id;
		
			$total = count($poll_result);
			$count_values = array_count_values($poll_result);		
			//var_dump($count_values);
			$response['html'] = '<div class="">'.__('Total:', 'question-answer').' '.$total.'</div>';
			//var_dump($count_values);
			foreach($count_values as $id=>$value){
				
				$response['html'].= '<div class="poll-line"><div style="width:'.(($value/$total)*100).'%" class="fill">&nbsp;'.$polls[$id].' - ('.$value.')'.' </div></div>';
				
				}
			
			}
		else{
			$response['error'] = __('Please login.', 'question-answer');
			}
		

		
		
		echo json_encode($response);
		die();
	
	}
	add_action('wp_ajax_qa_ajax_poll', 'qa_ajax_poll');
	add_action('wp_ajax_nopriv_qa_ajax_poll', 'qa_ajax_poll');
	
	
	
	
	
	
	
	
	
	
	function qa_answer_thumbsup_action(){
		$response = array();
		
		$post_id 		= (int)sanitize_text_field($_POST['post_id']);
		$current_user 	= wp_get_current_user();
		
		$qa_answer_review 	= get_post_meta( $post_id, 'qa_answer_review', true );
		
		if( empty( $current_user->ID ) ) {
			$response['error'] = __('Login first to Review !', 'question-answer');
		} else {

			$review_count = empty( $qa_answer_review['reviews'] ) ? 0 : (int)$qa_answer_review['reviews'];
			
			$status = isset( $qa_answer_review['users'][$current_user->ID]['type'] ) ? $qa_answer_review['users'][$current_user->ID]['type'] : '';
			if( $status != 'up' ) {
			
				$qa_answer_review['reviews'] = ++$review_count; // Plus 1
				$qa_answer_review['users'][$current_user->ID]['type'] =  'up';
			
				update_post_meta( $post_id, 'qa_answer_review', $qa_answer_review );
				$response['review_value'] = $review_count;


				$question_post = get_post($post_id);
				$question_author = $question_post->post_author;


				$notification_data = array();

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = '';
				$notification_data['a_id'] = $post_id;
				$notification_data['c_id'] = '';
				$notification_data['subscriber_id'] = $question_author;
				$notification_data['action'] = 'vote_up';

				do_action('qa_action_notification_save', $notification_data);


				do_action('qa_action_answer_vote_up', $post_id);
				
			
			} else $response['error'] = __( 'Already Reviewed !', 'question-answer' );
		}
		
		$response['status'] = $status;
		
		echo json_encode($response);
		die();
	}

	add_action('wp_ajax_qa_answer_thumbsup_action', 'qa_answer_thumbsup_action');
	add_action('wp_ajax_nopriv_qa_answer_thumbsup_action', 'qa_answer_thumbsup_action');
	
	function qa_answer_thumbsdown_action(){
		$response = array();
		
		$post_id 		= (int)sanitize_text_field($_POST['post_id']);
		$current_user 	= wp_get_current_user();
		
		$qa_answer_review 	= get_post_meta( $post_id, 'qa_answer_review', true );
		
		if( empty( $current_user->ID ) ) {
			$response['error'] = __('Login first to Review !', 'question-answer');
		} else {

			$review_count = empty( $qa_answer_review['reviews'] ) ? 0 : (int)$qa_answer_review['reviews'];
			
			$status = isset( $qa_answer_review['users'][$current_user->ID]['type'] ) ? $qa_answer_review['users'][$current_user->ID]['type'] : '';
			if( $status != 'down' ) {
			
				$qa_answer_review['reviews'] = --$review_count; // deduct 1
				$qa_answer_review['users'][$current_user->ID]['type'] =  'down';
			
				update_post_meta( $post_id, 'qa_answer_review', $qa_answer_review );
				$response['review_value'] = $review_count;

				$question_post = get_post($post_id);
				$question_author = $question_post->post_author;


				$notification_data = array();

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = '';
				$notification_data['a_id'] = $post_id;
				$notification_data['c_id'] = '';
				$notification_data['subscriber_id'] = $question_author;
				$notification_data['action'] = 'vote_down';

				do_action('qa_action_notification_save', $notification_data);



				do_action('qa_action_answer_vote_down', $post_id);
			
			
			} else $response['error'] = __( 'Already Reviewed!', 'question-answer' );
		}
		
		$response['status'] = $status;
		
		echo json_encode($response);
		die();
	}

	add_action('wp_ajax_qa_answer_thumbsdown_action', 'qa_answer_thumbsdown_action');
	add_action('wp_ajax_nopriv_qa_answer_thumbsdown_action', 'qa_answer_thumbsdown_action');
	
	
	// is solved
	function qa_subscribe_action() {
		$html = array();
		
		$post_id 		= (int)sanitize_text_field($_POST['post_id']);
		$current_user 	= wp_get_current_user();
		//$author_id 		= get_post_field( 'post_author', $post_id );
		
		if( is_user_logged_in() ) {
			
			$q_subscriber = get_post_meta( $post_id, 'q_subscriber', true );
			
			if(!is_array($q_subscriber)){
				$q_subscriber = array();
				}
			
			
			if(in_array($current_user->ID, $q_subscriber)){
				
				$html['toast'] = __('Unsubscribe from this question', 'question-answer');
				$html['html'] = '<i class="fa fa-bell-slash"></i>';
				$html['subscribe_class'] = 'not-subscribed';	

				//unset($q_subscriber);

				$q_subscriber = array_diff($q_subscriber, array($current_user->ID));
				update_post_meta($post_id,'q_subscriber', $q_subscriber );

				do_action('qa_action_question_subscribe', $post_id);

				}
			else{
				
				$html['toast'] = __('Subscribed to this question', 'question-answer');
				$html['html'] = '<i class="fa fa-bell"></i>';
				$html['subscribe_class'] = 'subscribed';
				
				$q_subscriber = array_merge($q_subscriber, array($current_user->ID));
				update_post_meta($post_id,'q_subscriber', $q_subscriber );
				
				do_action('qa_action_question_unsubscribe', $post_id);
				}

			} 
		else{
			
			$html['toast'] = __('Please login first!', 'question-answer');
		}
		
		echo json_encode($html);
		die();
	}

	add_action('wp_ajax_qa_subscribe_action', 'qa_subscribe_action');
	add_action('wp_ajax_nopriv_qa_subscribe_action', 'qa_subscribe_action');
	
	
	
	// is solved
	function qa_is_solved_action() {
		$html = array();
		
		$post_id 		= (int)sanitize_text_field($_POST['post_id']);
		$current_user 	= wp_get_current_user();
		$author_id 		= get_post_field( 'post_author', $post_id );
		$q_subscriber = get_post_meta( $post_id, 'q_subscriber', true );


		if( $current_user->ID == $author_id || is_admin() ) {
			$is_solved = get_post_meta( $post_id, 'qa_question_status', true );
			
			if ( $is_solved == 'solved' ) {
				
				update_post_meta( $post_id, 'qa_question_status', 'processing' );
				
				$html['toast'] 		= __('Successfully marked as unsolved', 'question-answer');
				$html['html'] 		= '<i class="fa fa-times"></i> '.__('Mark as Solved', 'question-answer');
				$html['is_solved'] 	= 'unsolved';
				$html['qa_ttt']		= __( 'Mark as Solved', 'question-answer' );


				$question_post = get_post($post_id);
				$question_author = $question_post->post_author;



				$notification_data = array();

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = $post_id;
				$notification_data['a_id'] = '';
				$notification_data['c_id'] = '';
				$notification_data['subscriber_id'] = $q_subscriber;
				$notification_data['action'] = 'q_not_solved';

				do_action('qa_action_notification_save', $notification_data);


				do_action('qa_action_question_not_solved', $post_id);
							
			} else {
				
				update_post_meta( $post_id, 'qa_question_status', 'solved' );
				
				$html['toast'] 		= __('Successfully marked as Solved', 'question-answer');
				$html['html'] 		= '<i class="fa fa-check"></i> '.__('Solved', 'question-answer');
				$html['is_solved'] 	= 'solved';
				$html['qa_ttt']		= __( 'Mark as Unsolved', 'question-answer' );

				$notification_data = array();

				$notification_data['user_id'] = get_current_user_id();
				$notification_data['q_id'] = $post_id;
				$notification_data['a_id'] = '';
				$notification_data['c_id'] = '';
				$notification_data['subscriber_id'] = $q_subscriber;
				$notification_data['action'] = 'q_solved';

				do_action('qa_action_notification_save', $notification_data);

				do_action('qa_action_question_solved', $post_id);

				
			}
		} else {
			$html['toast'] = __('Access Denied!', 'question-answer');
			
		}
		
		echo json_encode($html);
		die();
	}

	add_action('wp_ajax_qa_is_solved_action', 'qa_is_solved_action');
	add_action('wp_ajax_nopriv_qa_is_solved_action', 'qa_is_solved_action');
	
	
	
	
	function qa_ajax_question_suggestion() {
		
		$title = sanitize_text_field($_POST['title']);
		
		$args = array('post_type'=>'question', 'posts_per_page'=>5, 's'=>$title,);
		
		$wp_query = new WP_Query($args);
		
		
		if ( $wp_query->have_posts() ) : 
		
		
		

		while ( $wp_query->have_posts() ) : $wp_query->the_post();
		
		echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
		
		
		endwhile;
		wp_reset_query();

		endif;
		
		die();
		}
	add_action('wp_ajax_qa_ajax_question_suggestion', 'qa_ajax_question_suggestion');
	add_action('wp_ajax_nopriv_qa_ajax_question_suggestion', 'qa_ajax_question_suggestion');
	
	
	
	
	
	
	
	
	
	

	function qa_search_user( $search_item, $string, $seperator = ',' ) {
		if( empty($string) ) return false;
		$str_arr = explode( $seperator, $string );
		foreach( $str_arr as $single_item ) {
			if( $search_item == $single_item ) return true;
		}
		return false;
	}
	
	// function qa_set_pages( $content = NULL ) {
		
		// $qa_page_question_post		=  get_option( 'qa_page_question_post' );
		// $qa_page_question_archive	=  get_option( 'qa_page_question_archive' );
		
		// if ( get_the_ID() == $qa_page_question_post ) $content .= '[qa_add_question]';
		// if ( get_the_ID() == $qa_page_question_archive ) $content .= '[question_archive]';
		
		
		// return $content;
	// }
	// add_filter( 'the_content', 'qa_set_pages' );
	
	
	function qa_toast_message() {
		echo "<div class='toast qa-shake' style='display:none'></div>";
	}
	add_action( 'wp_footer', 'qa_toast_message' );
	

	function qa_get_terms($taxonomy){

		if(!isset($taxonomy)){
			$taxonomy = 'question_category';
			}
		
		
		$args=array(
			
		  'orderby' => 'id',
		  'taxonomy' => $taxonomy,
		  'hide_empty' => false,
		  'parent'  => 0,
		  );
		
		$categories = get_categories($args);

			
		$html = '';
		
		if(!empty($categories)){
			
			foreach($categories as $category){
				
					$name = $category->name;
					$cat_ID = $category->cat_ID;	
				
					$terms[$cat_ID] = 	$name;	
					
					$args_child=array(
						
					  'orderby' => 'id',
					  'taxonomy' => $taxonomy,
					  'hide_empty' => false,
					  'parent'  => $cat_ID,
					  );
					
					$categories_child = get_categories($args_child);
					
					if(!empty($categories_child))
					foreach($categories_child as $category_child){
						
						$name_child = $category_child->name;
						$cat_ID_child = $category_child->cat_ID;	
						
						$terms[$cat_ID_child] = $name_child;
						
						}
	
				}
			
			
			}
		else{
			$terms = array();
			}
		
		
		return $terms;

	}

	function qa_get_categories() {
		$args = array(
			'show_option_none' => __( 'Select category', 'question-answer'),
			'hide_empty'          => 0,
			'hierarchical'        => true,
			'order'               => 'ASC',
			'orderby'             => 'name',
			'taxonomy'            => 'question_cat',
			
		);
		$cat = get_terms( $args );
		$cat_arr = array();
		foreach( $cat as $cat_details ) {
			if ( $cat_details->parent == 0 ) $cat_arr[$cat_details->term_id]['0'] = $cat_details->name;
			else $cat_arr[$cat_details->parent][$cat_details->term_id] = $cat_details->name;
		}
		
		return $cat_arr;
	}
	
	
	add_action('admin_menu', 'qa_pending_question_count');

	function qa_pending_question_count() {
		
		global $menu;
		$count = 0;
		if ( current_user_can( 'administrator' ) )
			$count = (int)wp_count_posts( 'question' )->pending;
		
		if( $count > 0 ) {
			
			foreach ( $menu as $key => $value ) {
				if ( $menu[ $key ][2] == 'edit.php?post_type=question' ) {
					$menu[ $key ][0] .= ' <span class="awaiting-mod qa-pending-question-count count-' . $count . '"><span class="pending-count">' . $count . '</span></span>';
				}
			}
			
		}
		return true;
	}
	
	function qa_do_url( $url, $action, $args = array() ) {

		$args['wpas-do']       = $action;
		$args['wpas-do-nonce'] = wp_create_nonce( 'trigger_custom_action' );
		$url                   = esc_url( add_query_arg( $args, $url ) );

		return $url;

	}
	
	
	add_action('publish_post', 'update_question_status_meta');
	function update_question_status_meta($post_ID) {
		global $wpdb;
		if( !wp_is_post_revision($post_ID) && get_post_type($post_ID) == 'question' ) {
			update_post_meta( $post_ID, 'qa_question_status', 'processing' );
		}
	}
	
	
	//add_filter( 'pickform_filter_input_field_html', 'pickform_filter_input_field_select_hierarchy', 10, 2);
	
	
	function pickform_filter_input_field_select_hierarchy($field_html, $field_data) {
  
		$field_type = $field_data['input_type'];
  
		if($field_type=='select_hierarchy')  
		$field_html['select_hierarchy'] = array(
            'html'=>select_hierarchy($field_data),                            
        );                                    

		return $field_html;
	}
	
	function select_hierarchy($field_data){
	
		$html = '';
		$html.= '<div class="title">'.$field_data['title'].'</div>';    
		$html.= '<div class="details">'.$field_data['option_details'].'</div>';
		
		$html .= '<select id="question_cat" name="question_cat">';
		$html .= '<option value="">'.__('Select a Category', 'question-answer').'</option>';
		$qa_categories = qa_get_categories();
		foreach( $qa_categories as $cat_id => $cat_info ) { ksort($cat_info);
			foreach( $cat_info as $key => $value ) {
				if( $key == 0 )  $html .= '<option value="'.$cat_id.'"><b>'.$value.'</b></option>';
				else $html .= '<option value="'.$key.'">  - '.$value.'</option>';
			}
		}
		$html .= '</select>';
        
		return $html;
	}
	
	
	add_filter( 'qa_filters_question_list_sections',  'qa_filters_function_question_list_sections' );
	function qa_filters_function_question_list_sections() {
		
		$sections = array(
			'question_category' => array (
				'css_class'	=> 'question_answer',
				'title'		=> __('Category', 'question-answer'),
			),
		);
		

		return $sections;
	}
	
	function question_filter_function_question_category() {
		global $post;
		
		$category = get_the_terms( $post->ID, 'question_cat');
		return !empty( $category[0]->name ) ? $category[0]->name : '-';
	}

	function qa_filter_badwords( $content ){
		
		global $post;
		$arr_badwords 		= array();
		$filter_badwords 	= get_option( 'qa_options_filter_badwords', 'yes' );
		$badwords 			= get_option( 'qa_options_badwords', array() );
		$badwords_replacer	= get_option( 'qa_options_badwords_replacer', '---' );
		
		if( $filter_badwords != 'yes' || empty( $badwords ) ) return $content;
		
		if( !empty($post->post_type) && $post->post_type == 'question' || $post->post_type == 'answer' ) {
			
			foreach( explode( ',', $badwords ) as $word ) {
				$arr_badwords[] = $word;
				$arr_badwords[] = ucfirst($word);
				$arr_badwords[] = ucwords($word);
				$arr_badwords[] = strtoupper($word);
			}
			return str_replace( $arr_badwords , '<span title="'.__('Word moderate.', 'question-answer').'" class="bad-word">'.$badwords_replacer.'</span>', $content );
		}
		else return $content;
	}
	//add_filter( 'the_content', 'qa_filter_badwords', 20 );
	add_filter( 'wp_filter_comment', 'qa_filter_badwords', 20 );

	
	function callback($buffer){
		return $buffer;
	}

	function qa_add_ob_start(){
		ob_start("callback");
	}

	function qa_flush_ob_end(){
		ob_end_flush();
	}

	add_action('init', 'qa_add_ob_start');
	add_action('wp_footer', 'qa_flush_ob_end');
	
	function qa_featured_authors( $args = array() ) {
		global 	$wpdb;
		
		$authors	= array();
		$args 		= wp_parse_args( $args );
		
		$POST_TYPE 	= isset( $args['post_type'] ) ? $args['post_type'] : 'post';
		$LIMIT 		= isset( $args['limit'] ) ? 'LIMIT '. $args['limit'] : '';
		
		foreach ( $wpdb->get_results("SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = '".$POST_TYPE."' AND " . get_private_posts_cap_sql( $POST_TYPE ) . " GROUP BY post_author ORDER BY count DESC $LIMIT") as $row ) :
			$author = get_userdata( $row->post_author );
			$authors[$row->post_author]['name'] = $author->display_name;
			$authors[$row->post_author]['post_count'] = $row->count;
		endforeach;

		return $authors;
	}
	

	
	
	function qa_breadcrumb_total_count(){
		
		$userid = get_current_user_id();
		global $wpdb;
		$limit = 100;
		
		$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE status='unread' AND subscriber_id='$userid' ORDER BY id DESC LIMIT $limit" );

		return count($entries);
	}

    function qa_breadcrumb_nav_menu(){

        $class_qa_functions = new class_qa_functions();
        $menu_items = $class_qa_functions->qa_breadcrumb_menu_items_function();


        foreach( $menu_items as $item_key => $item_details ) {

            $link 	= isset( $item_details['link'] ) ? $item_details['link'] : '';
            $title 	= isset( $item_details['title'] ) ? $item_details['title'] : '';

            echo  '<div class="item '.$item_key.'"><a href="'.$link.'">'.$title.'</a></div>';

        }

    }


    add_action('qa_breadcrumb_menu','qa_breadcrumb_nav_menu');


	function qa_breadcrumb_menu_notifications(){
		
		if( ! is_user_logged_in() ) return;
		
		echo '<div class="notifications">';
		echo '<div class="title">'.
			__('Notifications', 'question-answer').' 
			<span class="qa_breadcrumb_refresh">'.__('Refresh', 'question-answer').' <i class="fa fa-refresh"></i></span>
		</div>';
		
		$userid = get_current_user_id();
		global $wpdb;
		$limit = 10;
	
		$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE status='unread' AND subscriber_id='$userid' ORDER BY id DESC LIMIT $limit" );

		?>
		<div class="list-items">
		<?php

		foreach( $entries as $entry ){
				
				
			$id = $entry->id;			
			$q_id = $entry->q_id;
			$a_id = $entry->a_id;	
			$c_id = $entry->c_id;				
			$user_id = $entry->user_id;			
			$subscriber_id = $entry->subscriber_id;			
			$action = $entry->action;
			$datetime = $entry->datetime;					
				
			$entry_date = new DateTime($datetime);
			$datetime = $entry_date->format('M d, Y h:i A');	
				
			$user = get_user_by( 'ID', $user_id);
		
			if(!empty($user->display_name)){
				$user_display_name = $user->display_name;
				}
			else{
				$user_display_name = __('Anonymous', 'question-answer');
				}
		
		    ?>

	        <div class="item">
	        <?php


		    echo '<img src="'.get_avatar_url($user_id,  array('size'=>40)).'" class="thumb">';
		
			$notify_mark_html = '<span class="notify-mark" notify_id="'.$id.'" ><i class="fa fa-bell-o" aria-hidden="true"></i></span>';


			if( $action == 'new_question' ) {

				echo '<span class="name">'.$user_display_name.'</span> '.__('posted', 'question-answer').' <span class="action">'.__('New Question',  'question-answer').'</span> <a href="'.get_permalink($q_id).'" class="link">'.get_the_title($q_id).'</a> ';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php

			}

			elseif( $action == 'new_answer' ) {


				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Answered', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($q_id).'</a> ';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php

			}


			elseif( $action == 'best_answer' ) {

				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Choosed best answer', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			}

			elseif( $action == 'best_answer_removed' ) {

				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Removed best answer', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			}

			elseif($action=='new_comment'){

				$comment_post_data = get_comment( $c_id );

				if(!empty($comment_post_data->comment_post_ID)):

					$comment_post_id = $comment_post_data->comment_post_ID;

					$comment_post_type = get_post_type($comment_post_id);

					if($comment_post_type=='answer'){

						$flag_post_type = 'answer';

						$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );


					}
					else{
						$flag_post_type = 'question';


					}

					$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
					echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Commented', 'question-answer').'</span> on '.$flag_post_type.' <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($a_id).'</a>';

					?>
					<div class="meta">

						<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
						<?php echo $notify_mark_html; ?>
					</div>
					<?php


				endif;


			}



			elseif($action=='comment_flag'){

				$comment_post_data = get_comment( $c_id );
				$comment_post_id = $comment_post_data->comment_post_ID ;

				$comment_post_type = get_post_type($comment_post_id);

				if($comment_post_type=='answer'){

					$flag_post_type = 'answer';
					$link_extra = '#comment-'.$c_id;
					$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
				}
				else{
					$flag_post_type = 'question';
					$link_extra = '#comment-'.$c_id;
					$q_id = $comment_post_id;
				}



				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Flagged comment', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php

			}

			elseif($action=='comment_unflag'){

				$comment_post_data = get_comment( $c_id );
				$comment_post_id = $comment_post_data->comment_post_ID ;

				$comment_post_type = get_post_type($comment_post_id);

				if($comment_post_type=='answer'){

					$flag_post_type = 'answer';
					$link_extra = '#comment-'.$c_id;
					$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
				}
				else{
					$flag_post_type = 'question';
					$link_extra = '#comment-'.$c_id;
					$q_id = $comment_post_id;
				}



				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Un-flagged comment', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php

			}



			elseif($action=='comment_vote_up'){

				$comment_post_data = get_comment( $c_id );
				$comment_post_id = $comment_post_data->comment_post_ID ;

				$comment_post_type = get_post_type($comment_post_id);

				if($comment_post_type=='answer'){

					$flag_post_type = 'answer';
					$link_extra = '#comment-'.$c_id;
					$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
				}
				else{
					$flag_post_type = 'question';
					$link_extra = '#comment-'.$c_id;
					$q_id = $comment_post_id;
				}



				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('comment vote up', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php



			}

			elseif($action=='comment_vote_down'){

				$comment_post_data = get_comment( $c_id );
				$comment_post_id = $comment_post_data->comment_post_ID ;

				$comment_post_type = get_post_type($comment_post_id);

				if($comment_post_type=='answer'){

					$flag_post_type = 'answer';
					$link_extra = '#comment-'.$c_id;
					$q_id = get_post_meta( $comment_post_id, 'qa_answer_question_id', true );
				}
				else{
					$flag_post_type = 'question';
					$link_extra = '#comment-'.$c_id;
					$q_id = $comment_post_id;
				}



				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('comment vote down', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#comment-'.$c_id.'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			}








			elseif($action=='vote_up'){

				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Vote Up', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php



			}
			elseif($action=='vote_down'){

				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				echo ' <span class="name">'.$user_display_name.'</span> <span class="action">'.__('Vote Down', 'question-answer').'</span> <a href="'.get_permalink($q_id).'#single-answer-'.$a_id.'" class="link">'.get_the_title($a_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php



			}


			elseif($action=='q_solved'){

				echo ' <span class="name">'.$user_display_name.'</span> '.__('marked', 'question-answer').' <span class="action">'.__('Solved', 'question-answer').'</span> <a href="'.get_permalink($q_id).'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			}

			elseif($action=='q_not_solved'){

				echo ' <span class="name">'.$user_display_name.'</span> '.__('marked', 'question-answer').' <span class="action">'.__('Not Solved','question-answer').'</span> <a href="'.get_permalink($q_id).'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			}

			elseif($action=='flag'){

				if(!empty($a_id)){

					$flag_post_type = 'answer';
					$link_extra = '#single-answer-'.$a_id;
				}
				else{

					$flag_post_type = 'question';
					$link_extra = '';
				}


				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				echo ' <span class="name">'.$user_display_name.'</span> '.sprintf(__('flagged your %s', 'question-answer'), $flag_post_type).' <span class="name"></span> <a href="'.get_permalink($q_id).$link_extra.'" class="link">'.get_the_title($a_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php


			}

			elseif($action=='unflag'){

				if(!empty($a_id)){

					$flag_post_type = 'answer';
					$link_extra = '#single-answer-'.$a_id;
				}
				else{

					$flag_post_type = 'question';
					$link_extra = '';
				}


				$q_id = get_post_meta( $a_id, 'qa_answer_question_id', true );
				echo ' <span class="name">'.$user_display_name.'</span> '.$flag_post_type.' <span class="action">'.__('unflagged ', 'question-answer').'</span> <a href="'.get_permalink($q_id).$link_extra.'" class="link">'.get_the_title($q_id).'</a>';

				?>
				<div class="meta">

					<span class="notify-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $datetime; ?></span>
					<?php echo $notify_mark_html; ?>
				</div>
				<?php



			}
			echo '</div>';

		}
		echo '</div>';
		echo '</div>';
	
	}

    add_action('qa_breadcrumb_menu','qa_breadcrumb_menu_notifications');


    function qa_breadcrumb_links($action){

        $archive_page_id = get_option( 'qa_page_question_archive' );
        $archive_page_title = empty( $archive_page_id ) ? __('Question Archive', 'question-answer') : get_the_title( $archive_page_id );
        $archive_page_href = empty( $archive_page_id ) ? '#' : get_the_permalink( $archive_page_id );

        echo apply_filters( 'qa_filter_breadcrumb_question_archive_link_html', sprintf( '<i class="fa fa-angle-double-right separator" aria-hidden="true"></i> <a class="link" href="%s">%s</a>', $archive_page_href, $archive_page_title ) );

        if( is_single() )
            echo apply_filters( 'qa_filter_breadcrumb_single_question_title_html', sprintf( ' <i class="fa fa-angle-double-right separator" aria-hidden="true"></i> <a class="link" href="#" >%s</a>', get_the_title() ) );

    }


    add_action('qa_breadcrumb_links','qa_breadcrumb_links');