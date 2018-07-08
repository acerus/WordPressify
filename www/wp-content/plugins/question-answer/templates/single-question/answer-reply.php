<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	$current_user	= wp_get_current_user();


	$qa_who_can_comment_answer = get_option( 'qa_who_can_comment_answer' );	
	$author_id 	= get_post_field( 'post_author', get_the_ID() );
	$author 	= get_userdata($author_id);
	
	$qa_answer_is_private 	= get_post_meta( get_the_ID(), 'qa_answer_is_private', true );
	
	$question_id = get_post_meta( get_the_ID(), 'qa_answer_question_id', true );
	$question_author_id = get_post_field( 'post_author', $question_id );
	
	

	
	//if ( $qa_answer_is_private == '1' && !in_array('administrator',  wp_get_current_user()->roles)  ) return;
	
	//echo '<pre>';print_r($current_user);echo '</pre>';


    $current_user = wp_get_current_user();
    $roles = $current_user->roles;
    $current_user_role = array_shift( $roles );
	
	//var_dump($current_user);
	//var_dump($qa_who_can_answer);	
	




	if ( $qa_answer_is_private == '1' ) { 
		

		
		
		if( $question_author_id == $current_user->ID || in_array( $current_user_role, $qa_who_can_comment_answer) || $author_id == $current_user->ID ){
			

			
			$private_answer_access = 'yes';
			
						
			?>
					<div class="qa-answer-comment-reply qa-answer-comment-reply-<?php echo get_the_ID(); ?> clearfix ">
						
						
						
						
					<?php 
//						$comments = get_comments( array(
//
//								'post_id' 	=> get_the_ID(),
//								'order' 	=> 'ASC',
//								'status'	=> 'approve',
//
//							) );

                        $args = array(
	                        'post_id' 	=> get_the_ID(),
	                        'order' 	=> 'ASC',
	                        'status'	=> 'approve',
	                        'number' => 2,
                        );

                        $comments_query = new WP_Comment_Query;
                        $comments = $comments_query->query( $args );






						
						foreach( $comments as $comment ) {

								$comment_date 	= new DateTime($comment->comment_date);
								$comment_date 	= $comment_date->format('M d, Y h:i A');
								$comment_author	= get_comment_author( $comment->comment_ID );

					            $comment_author_user_data = get_user_by('email', $comment->comment_author_email);


								if(!empty($comment->comment_author)){

									$comment_author = $comment->comment_author;
									}

								else{
									$comment_author =  __('Anonymous', 'question-answer');
									}


                                    ?>

                                    <div id="comment-<?php echo $comment->comment_ID; ?>" class="qa-single-comment single-reply">
                                        <div class="qa-avatar float_left " >
                                            <?php echo get_avatar( $comment->comment_author_email, "30" ); ?>
                                        </div>
                                    <?php


                                    ?>
                                        <div class="qa-comment-content">
                                            <div class="ap-comment-header">
                                                <div href="#" class="ap-comment-author qa-user-card-loader" author_id="<?php echo $comment_author_user_data->ID; ?>" has_loaded="no">
                                                    <?php echo $comment_author; ?>
                                                    <div class="qa-user-card">
                                                        <div class="card-loading">
                                                            <i class="fa fa-cog fa-spin"></i>
                                                        </div>
                                                        <div class="card-data"></div>
                                                    </div>
                                                </div>
                                        <?php

								echo ' - <a class="comment-link" href="#comment-'.$comment->comment_ID.'"> '.$comment_date.'</a>
											
										</div>
										<div class="ap-comment-texts">';

										ob_start();
										qa_filter_badwords( comment_text( $comment->comment_ID ) );
										echo ob_get_clean();


										echo '</div>
									</div>
								
								</div>
								';

							}
					?>
				</div>
                
                
				<?php
                    $current_user_ID = get_current_user_id();
                    
                    if ( $current_user_ID == 0 ) {
                ?>
                    <a class="qa-answer-reply" href="<?php echo wp_login_url( $_SERVER['REQUEST_URI'] ); ?> ">
                        <i class="fa fa-sign-in"></i>
                            <span><?php echo __('Sign in to Reply', 'question-answer'); ?></span>
                    </a>		
                <?php
                    } else {
                    
                        
                ?>
                    <div class="qa-answer-reply" post_id="<?php echo get_the_ID(); ?>">
                        <i class="fa fa-reply"></i>
                            <span><?php echo __('Reply on This', 'question-answer'); ?></span>
                    </div>
                <?php
                    }
                ?> 
                
                
                
                
                
			<?php
						
			
			
			
			
			
			
			}
		else{
			$private_answer_access = 'no';
			}
			
			
			
			
			
			
			
			
		}
	else{
				
		?>
		<div class="qa-answer-comment-reply qa-answer-comment-reply-<?php echo get_the_ID(); ?> clearfix ">
			
			
			
			
		<?php 
			$comments = get_comments( array(

					'post_id' 	=> get_the_ID(),
					'order' 	=> 'ASC',
					'status'	=> 'approve',
		            'number' => 3,

				) );


		$count_comments = wp_count_comments(get_the_ID());

		$total_comments = $count_comments->approved;

		$comment_remain_count = $total_comments - 3;



		//var_dump($total_comments);

        $current_user 	= wp_get_current_user();
        $user_ID		= $current_user->ID;
        $status = 1;
        $tt_text = '<i class="fa fa-thumbs-down"></i> '.__('Report this', 'question-answer');
        if( !empty($user_ID) ) {


        }


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
                <div id="comment-<?php echo $comment->comment_ID; ?>" class="qa-single-comment single-reply">
                    <div class="qa-avatar float_left " >
                        <?php echo get_avatar( $comment->comment_author_email, "30" ); ?>

                    </div>
                    <div class="qa-comment-content">
                        <div class="ap-comment-header">
                        <?php

                        if(!empty($comment_author_user_data->display_name)):
                            ?>
                            <div class="ap-comment-author qa-user-card-loader" author_id="<?php echo $comment_author_user_data->ID; ?>" has_loaded="no">
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

	$current_user_ID = get_current_user_id();
	
	if ( $current_user_ID == 0 ){
        ?>
            <a class="qa-answer-reply" href="<?php echo wp_login_url( $_SERVER['REQUEST_URI'] ); ?> ">
                <i class="fa fa-sign-in"></i>
                <span><?php echo __('Sign in to Reply', 'question-answer'); ?></span>
            </a>
        <?php
	}
	else{
        ?>
        <div class="qa-answer-reply" post_id="<?php echo get_the_ID(); ?>">
            <i class="fa fa-reply"></i>
            <span><?php echo __('Reply on This', 'question-answer'); ?></span>
        </div>
        <?php
	}

		
		
}

?>
	

	<div class="qa-reply-popup qa-reply-popup-<?php echo get_the_ID(); ?>">
		<div class="qa-reply-form">
			<span class="close"><i class="fa fa-times"></i></span>
			<span class="qa-reply-header"><?php echo __('Replying as', 'question-answer'); ?> <?php echo $current_user->display_name; ?></span>
			<textarea rows="4" cols="40" id="qa-answer-reply-<?php echo get_the_ID(); ?>"></textarea>
			<span class="qa-reply-form-submit" id="<?php echo get_the_ID(); ?>"><?php echo __('Submit', 'question-answer'); ?></span>
		</div>	
	</div>
	