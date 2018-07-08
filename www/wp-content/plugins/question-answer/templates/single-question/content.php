<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


$question_id = get_the_ID();


?>

<?php do_action('qa_action_single_question_content_before');

$author_id 	= get_post_field( 'post_author', get_the_ID() );
$author 	= get_userdata($author_id);

$polls = get_post_meta(get_the_ID(), 'polls', true);

$qa_flag 	= get_post_meta( get_the_ID(), 'qa_flag', true );

if(empty($qa_flag)) $qa_flag = array();
$flag_count 		= sizeof($qa_flag);
$user_ID		= get_current_user_id();


if(!empty($polls) && is_serialized($polls) ){
    $polls = unserialize($polls);
    }

		//echo '<pre>'.var_export($polls, true).'</pre>';

	
$author_name = !empty( $author->display_name ) ? $author->display_name : $author->user_login;
$author_role = !empty( $author->roles ) ? $author->roles[0] : __('Anonymous', 'question-answer');
$author_date = !empty( $author->user_registered ) ? $author->user_registered : 'N/A';
	
	
$comments = get_comments( array(
    'post_id' 	=> get_the_ID(),
    'order' 	=> 'ASC',
    'status'	=> 'approve',
    'number' => 3,
) );


$count_comments = wp_count_comments(get_the_ID());
$total_comments = $count_comments->approved;
$comment_remain_count = $total_comments - 3;


$qa_allow_question_comment = get_option( 'qa_allow_question_comment', 'yes' );
if( $qa_allow_question_comment == 'no' ) $comments = array();


global $qa_css;

$qa_color_single_user_role = get_option( 'qa_color_single_user_role' );
if( empty( $qa_color_single_user_role ) ) $qa_color_single_user_role = '';

$qa_color_single_user_role_background = get_option( 'qa_color_single_user_role_background' );
if( empty( $qa_color_single_user_role_background ) ) $qa_color_single_user_role_background = '';

$qa_color_add_comment_background = get_option( 'qa_color_add_comment_background' );
if( empty( $qa_color_add_comment_background ) ) $qa_color_add_comment_background = '';

$qa_flag_button_bg_color = get_option( 'qa_flag_button_bg_color' );


$qa_vote_button_bg_color = get_option( 'qa_vote_button_bg_color' );
if( empty( $qa_vote_button_bg_color ) ) $qa_vote_button_bg_color = '';

$qa_css .= ".single-question .qa-user-role{ color: $qa_color_single_user_role; background-color: $qa_color_single_user_role_background; } 
	.single-question .qa-add-comment, .single-question .qa-cancel-comment, .qa-answer-reply { background: $qa_color_add_comment_background; }";

if( !empty( $qa_flag_button_bg_color ) ) {

    $qa_css .=  '.single-question .qa-flag,.single-question .qa-comment-flag{background: $qa_flag_button_bg_color none repeat scroll 0 0;}';
};


$qa_css .= ".qa-single-vote .qa-thumb-up, .qa-single-vote .qa-thumb-reply, .qa-single-vote .qa-thumb-down{ background: $qa_vote_button_bg_color;border:1px solid ".$qa_vote_button_bg_color." } .votted{background: rgba(0, 0, 0, 0) linear-gradient(to bottom, ".$qa_vote_button_bg_color." 5%, #fff 60%) repeat scroll 0 0 !important; color:".$qa_vote_button_bg_color." !important; border:1px solid ".$qa_vote_button_bg_color." !important;}";

	
?>

<div itemprop="description" class="question-content">
	<div class="content-header">

        <div class="question-author-avatar meta">
			<?php echo get_avatar( $author->user_email, "45" ); ?>
        </div>
        <div class="qa-user qa-user-card-loader" author_id="<?php echo $author_id; ?>" has_loaded="no">
			<?php echo apply_filters('qa_question_author_name', $author_name); ?>
            <div class="qa-user-card">
                <div class="card-loading">
                    <i class="fa fa-cog fa-spin"></i>
                </div>
                <div class="card-data"></div>
            </div>
        </div>


		<?php
		echo apply_filters( 'qa_filter_single_question_meta_post_date', sprintf( '<span class="qa-meta-item">%s %s</span>', '<i class="fa fa-clock-o"></i>', get_the_date('M d, Y h:i A') ) );


		$wp_query_answer = new WP_Query(
			array (
				'post_type' 	=> 'answer',
				'post_status' 	=> 'publish',
				'meta_query' => array(
					array(
						'key'     => 'qa_answer_question_id',
						'value'   => get_the_ID(),
						'compare' => '=',
					),
				),
			) );

		echo apply_filters( 'qa_filter_single_question_meta_answer_count', sprintf('<span class="qa-meta-item">%s %s '.__('Answers', 'question-answer').'</span>', '<i class="fa fa-comments"></i>', number_format_i18n($wp_query_answer->found_posts)) );

		wp_reset_query();


		$category = get_the_terms(get_the_ID(), 'question_cat');
		if(!empty($category[0])){
			echo apply_filters( 'qa_filter_single_question_meta_category', sprintf( '<span class="qa-meta-item">%s %s</span>', '<i class="fa fa-folder-open"></i>', $category[0]->name ) );
		}

		?>



		<div class="qa-users-meta meta">

            <?php
            do_action('qa_question_user_meta', $question_id);
            ?>



			<span class="qa-user-badge"><?php echo apply_filters('qa_filter_single_question_badge','',$author->ID, 2); ?></span>            
			<span class="qa-member-since"><?php echo sprintf( __('Member Since %s', 'question-answer'), date( "M Y", strtotime( $author_date ) )); ?><?php //echo date( "M Y", strtotime( $author_date ) ); ?></span>
		</div>


        <?php



        do_action('qa_action_single_question_meta'); ?>

	</div> <!-- .content-header -->
	
	<div class="content-body">
        <div class="question-content">
	        <?php echo get_the_content(); ?>
        </div>

        <ul class="qa-polls">
        <?php
        if(!empty($polls) && is_array($polls)){
			
			foreach($polls as $id=>$poll){
				
				if(!empty($poll))
				echo '<li q_id="'.get_the_ID().'" data-id="'.$id.'"><i class="fa fa-circle-o" aria-hidden="true"></i><i class="fa fa-dot-circle-o" aria-hidden="true"></i> '.$poll.'</li>';
				
				}
			}
        ?>
        </ul>

        <div class="poll-result">
            <i class="loading fa fa-spinner fa-spin" aria-hidden="true"></i>
            <div class="results">
            <?php

            $poll_result = get_post_meta(get_the_ID(), 'poll_result', true);
            if(!empty($poll_result) && is_array($poll_result)){

                $total = count($poll_result);
                $count_values = array_count_values($poll_result);
                //var_dump($count_values);
                echo '<div class="">'.__('Total:', 'question-answer').' '.$total.'</div>';

                foreach($count_values as $id=>$value){

                    echo '<div class="poll-line"><div style="width:'.(($value/$total)*100).'%" class="fill">&nbsp;'.$polls[$id].' - ('.$value.')'.' </div></div>';

                    }
                }
            ?>
            </div>

        </div>

        <div class="qa-content-tags">

            <?php
            $tag_list = wp_get_post_terms(get_the_ID(), 'question_tags', array("fields" => "all"));
            $total_tag = count($tag_list);

            if(!empty($tag_list)){

                $tag_html = '';
                $i=1;
                foreach($tag_list as $tag){

                    $tag_html.= '<a class="tag" href="#">'.$tag->name.'</a>';
                    if($total_tag!=$i){
                        $tag_html.= ' ';
                        }
                    $i++;
                    }
                }
            else{
                $tag_html = __('N/A', 'question-answer');
                }

            if($total_tag>0)
            echo apply_filters( 'qa_filter_single_question_tags', __('Tags: ', 'question-answer' ).$tag_html );

             ?>
         </div> <!-- End of Tags -->

        <div class="qa-question-comment-reply qa-question-comment-reply-<?php echo get_the_ID(); ?> clearfix ">

		<?php 
		$status = 0;
		$tt_text = '<i class="fa fa-lock"></i> '.__('Login First', 'question-answer');
		
	
		$current_user 	= wp_get_current_user();
		$user_ID		= $current_user->ID;
		
		
		if( !empty($user_ID) ) {
			$status = 1;
			$tt_text = '<i class="fa fa-thumbs-down"></i> '.__('Report this', 'question-answer');
		}
		
		foreach( $comments as $comment ) {

            $comment_date 	= new DateTime($comment->comment_date);
            $comment_date 	= $comment_date->format('M d, Y h:i A');
            $comment_author	= get_comment_author( $comment->comment_ID );



            if(!empty($comment->comment_author)){

            //$comment_author = $comment->comment_author;
		    $comment_author_user_data = get_user_by('email', $comment->comment_author_email);
		    $comment_author = $comment_author_user_data->display_name;

            }

            else{
            $comment_author =  __('Anonymous', 'question-answer');
            }




            $qa_flag_comment 	= get_comment_meta( $comment->comment_ID, 'qa_flag_comment', true );

            if(!is_array($qa_flag_comment)){
            $qa_flag_comment = array();
            }


            $flag_comment_count 		= sizeof($qa_flag_comment);

            if( array_key_exists($user_ID, $qa_flag_comment) && $qa_flag_comment[$user_ID]['type']=='flag'  ) {

            $flag_text = __('Unflag', 'question-answer');

            } else {

            $flag_text = __('Flag', 'question-answer');
            }

            $flag_html = '<div class="qa-comment-flag qa-comment-flag-action float_right" comment_id="'.$comment->comment_ID.'"><i class="fa fa-flag flag-icon"></i> <span class="flag-text">'.$flag_text.'</span><span class="flag-count">('.$flag_comment_count.')</span> <span class="waiting"><i class="fa fa-cog fa-spin"></i></span> </div>';


                ?>
                <div id="comment-<?php echo $comment->comment_ID; ?>" class="qa-single-comment single-reply">

                    <div class="qa-avatar float_left"><?php echo get_avatar( $comment->comment_author_email, "30" ); ?></div>
                    <div class="qa-comment-content">
                        <div class="ap-comment-header">
                            <div class="ap-comment-author qa-user-card-loader" author_id="<?php echo $author_id; ?>" has_loaded="no">
                                <?php echo $comment_author; ?>

                                <div class="qa-user-card">
                                    <div class="card-loading">
                                        <i class="fa fa-cog fa-spin"></i>
                                    </div>
                                    <div class="card-data"></div>
                                </div>
                            </div> - <a class="comment-link" href="#comment-<?php echo $comment->comment_ID; ?>"> <?php echo $comment_date; ?></a>

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

		?>
        </div>

		<?php
		if($comment_remain_count>0):
            ?>
            <a total_comments="<?php echo $total_comments; ?>"  per_page="3" paged="1" class="qa-load-comments" post_id="<?php echo get_the_ID(); ?>" href="#"><?php echo sprintf(__('<span class="count">%s</span>+ more comments.'), $comment_remain_count); ?> <span class="icon-loading"><i class='fa fa-cog fa-spin'></i></span></a>
            <?php
		endif;
		?>


            <?php


		if( $qa_allow_question_comment == 'yes' ) {

            $current_user 	= wp_get_current_user();
            ?>

            <div class="qa-answer-reply" post_id="<?php echo get_the_ID(); ?>">
                <i class="fa fa-reply"></i>
                <span><?php echo __('Reply on This', 'question-answer'); ?></span>
            </div>
            <div class="qa-reply-popup qa-reply-popup-<?php echo get_the_ID(); ?>">
                <div class="qa-reply-form">
                    <span class="close"><i class="fa fa-times"></i></span>
                    <span class="qa-reply-header"><?php echo __('Replying as', 'question-answer'); ?> <?php echo $current_user->display_name; ?></span>
                    <textarea rows="4" cols="40" id="qa-answer-reply-<?php echo get_the_ID(); ?>"></textarea>
                    <span class="qa-reply-form-submit" id="<?php echo get_the_ID(); ?>"><?php echo __('Submit', 'question-answer'); ?></span>
                </div>
            </div>

		<?php
		}
		?>
		

		
	
	</div> <!-- .content-body -->
</div><!-- .question-content -->

<?php do_action('qa_action_single_question_content_after'); ?>


