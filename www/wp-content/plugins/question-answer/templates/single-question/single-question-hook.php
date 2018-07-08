<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


add_action('qa_question_user_card','qa_question_user_card_cover', 10, 1);
add_action('qa_question_user_card','qa_question_user_card_avatar', 10, 1);
add_action('qa_question_user_card','qa_question_user_card_author_name', 10, 1);
add_action('qa_question_user_card','qa_question_user_card_author_follow', 10, 1);
add_action('qa_question_user_card','qa_card_author_total_question', 10, 1);
add_action('qa_question_user_card','qa_card_author_total_answer', 10, 1);
add_action('qa_question_user_card','qa_card_author_total_comment', 10, 1);
add_action('qa_question_user_card','qa_card_author_total_follower', 10, 1);




function qa_question_user_card_cover($author_id){

	$author 	= get_userdata($author_id);
    $cover_photo = get_user_meta($author_id, 'cover_photo', true);


    if(empty($cover_photo)){
	    $cover_photo = QA_PLUGIN_URL."assets/front/images/card-cover.jpg";
    }

    $qa_page_user_profile = get_option('qa_page_user_profile');

	$qa_page_user_profile_url = get_permalink($qa_page_user_profile);

    ?>
    <div class="card-cover">
        <a href="<?php echo $qa_page_user_profile_url; ?>?id=<?php echo $author_id?>">
            <img src="<?php echo $cover_photo; ?>" />
        </a>
    </div>
    <?php

}

function qa_question_user_card_avatar( $author_id){

	$author 	= get_userdata($author_id);

	$profile_photo = get_user_meta($author_id, 'profile_photo', true);

	if(empty($profile_photo)){
		$profile_photo = get_avatar_url( $author_id, array('size'=>'75') );
	}


	?>
    <div class="card-avatar">
		<img src="<?php echo $profile_photo; ?>" />
    </div>
	<?php

}



function qa_question_user_card_author_name( $author_id){

	$author 	= get_userdata($author_id);
	?>
    <div class="card-author-name">
		<?php echo $author->display_name; ?>
    </div>
	<?php

}

function qa_question_user_card_author_follow( $author_id){

	global $wpdb;
	$table = $wpdb->prefix . "qa_follow";
	$logged_user_id = get_current_user_id();

	$follow_result = $wpdb->get_results("SELECT * FROM $table WHERE author_id = '$author_id' AND follower_id = '$logged_user_id'", ARRAY_A);

	$already_insert = $wpdb->num_rows;
	if($already_insert > 0 ){
		$follow_text = __('Following', 'question-answer');
		$follow_class = 'following';
	}
	else{
		$follow_text = __('Follow', 'question-answer');
		$follow_class = '';
	}


	?>
    <div class="card-author-follow qa-follow <?php echo $follow_class; ?>" author_id="<?php echo $author_id; ?>"><?php echo $follow_text;  ?></div>
	<?php

}


function qa_card_author_total_question( $author_id){

    $author_total_question = qa_author_total_question($author_id);
	?>
    <div class="card-question-count card-meta"><?php echo sprintf(__("Total question: %s", 'question-answer'), $author_total_question); ?></div>
	<?php

}

function qa_card_author_total_answer( $author_id){

	$author_total_answer = qa_author_total_answer($author_id);
	?>
    <div class="card-answer-count card-meta"><?php echo sprintf(__("Total answer: %s", 'question-answer'), $author_total_answer); ?></div>
	<?php

}


function qa_card_author_total_comment( $author_id){

	$author_total_comment = qa_author_total_comment($author_id);
	?>
    <div class="card-answer-count card-meta"><?php echo sprintf(__("Total comment: %s", 'question-answer'), $author_total_comment); ?></div>
	<?php

}


function qa_card_author_total_follower( $author_id){

	$author_total_follower = qa_author_total_follower($author_id);
	?>
    <div class="card-follower-count card-meta"><?php echo sprintf(__("Total follower: %s", 'question-answer'), $author_total_follower); ?></div>
	<?php

}

























