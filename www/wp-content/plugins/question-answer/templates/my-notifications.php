<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

?>

<div class="qa-notifications">

    <?php



    if( ! is_user_logged_in() ) return;

    $userid = get_current_user_id();

    global $wpdb;
    $PER_PAGE = 10;

   // $paged 		= isset( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : 1;
    if ( get_query_var('paged') ) {

	    $paged = get_query_var('paged');

    } elseif ( get_query_var('page') ) {

	    $paged = get_query_var('page');

    } else {

	    $paged = 1;

    }

    $total_entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE subscriber_id='$userid' ORDER BY id DESC" );


    $OFFSET 	= ($paged - 1) * $PER_PAGE ;
    $entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE subscriber_id='$userid' ORDER BY id DESC LIMIT $PER_PAGE  OFFSET $OFFSET" );
    //$wdm_downloads = $wpdb->get_results( $entries, OBJECT );
    $total_notification = count($total_entries);

    //var_dump($paged);


    echo '<div class="title">'.
         __(sprintf('Notifications <span class="count">(%s)</span>', $total_notification), 'question-answer').' 
			<span class="qa_breadcrumb_refresh">'.__('Refresh', 'question-answer').' <i class="fa fa-refresh"></i></span>
		</div>';



    ?>
    <div class="list-items">
    <?php

    if(!empty($entries)):
    foreach( $entries as $entry ){


        $id = $entry->id;
        $q_id = $entry->q_id;
        $a_id = $entry->a_id;
        $c_id = $entry->c_id;
        $user_id = $entry->user_id;
        $subscriber_id = $entry->subscriber_id;
        $action = $entry->action;
        $datetime = $entry->datetime;
        $status = $entry->status;

        $entry_date = new DateTime($datetime);
        $datetime = $entry_date->format('M d, Y h:i A');

        $user = get_user_by( 'ID', $user_id);

        if(!empty($user->display_name)){
            $user_display_name = $user->display_name;
        }
        else{
            $user_display_name = __('Anonymous', 'question-answer');
        }


        if($status=='unread'){
	        $notify_mark_html = '<span class="notify-mark" notify_id="'.$id.'" ><i class="fa fa-bell-o" aria-hidden="true"></i></span>';
        }
        else{
            $notify_mark_html = '<span class="notify-mark" notify_id="'.$id.'" ><i class="fa fa-bell-slash" aria-hidden="true"></i></span>';
        }


	    ?>

        <div class="item">
        <?php


	    echo '<img src="'.get_avatar_url($user_id,  array('size'=>40)).'" class="thumb">';

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
    else:

        ?>
            <div class="empty-notify"><i class="fa fa-bell-slash-o" aria-hidden="true"></i> <?php echo __('No notification right now.', 'question-answer'); ?></div>
        <?php
    endif;

    ?>
        </div>

	    <?php
	    $num_rows_query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}qa_notification WHERE subscriber_id='$userid' ORDER BY id DESC" );
	    //$num_rows_query = $wpdb->get_results("SELECT * FROM ".WDM_TABLE_NAME." ORDER BY id DESC");

	    //var_dump($num_rows_query);

	    $big = 999999999;
	    $paginate_links = paginate_links( array(
		    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		    'format' => '?paged=%#%',
		    'current' => max( 1, $paged ),
		    'prev_text'          => '«',
		    'next_text'          => '»',
		    'total' => (int)ceil($wpdb->num_rows / $PER_PAGE)
	    ) );


	    ?>
        <div class="paginate">
		    <?php
		    echo $paginate_links;
		    ?>

        </div>

</div>




