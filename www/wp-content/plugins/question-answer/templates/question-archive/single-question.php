<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	$qa_question_excerpt_length = get_option('qa_question_excerpt_length' , 20 );
	$category 	= get_the_terms( get_the_ID(), 'question_cat' );			
	
	$qa_featured_questions = get_option('qa_featured_questions');
	
	if(empty($qa_featured_questions)){$qa_featured_questions = array(); }
	
	
	if(in_array(get_the_ID(), $qa_featured_questions)){
		
		$is_featured = 'featured';
		}
	else{
		$is_featured = '';
		}


    $question_post = get_post(get_the_ID());

    $author_id = $question_post->post_author;
	
	?>
	
	<div class="single-question <?php echo $is_featured; ?>">

		<div class="question-author-avatar qa-user-card-loader" author_id="<?php echo $author_id; ?>" has_loaded="no">
            <?php echo get_avatar( get_the_author_meta('ID'), "45" ); ?>
            <div class="qa-user-card">
                <div class="card-loading">
                    <i class="fa fa-cog fa-spin"></i>
                </div>
                <div class="card-data"></div>
            </div>
        </div>
		

		<?php 
			do_action( 'qa_action_question_archive_vote' );	
			do_action( 'qa_action_question_archive_answer_count' );
			do_action( 'qa_action_question_archive_view_count' );	
		?>   
		
		<div class="question-details">
        
        
			<div class="title"><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></div>
			<div class="excerpt"><?php 
			
			$post_status 	= get_post_status( get_the_ID());
			
			if($post_status=='private'){
						
				
				}
			else{
				echo wp_trim_words(get_the_content(), $qa_question_excerpt_length,'...');
				}
			
			
			?>
            </div>
			<div class="meta">
					

                <?php 
				$qa_question_status = get_post_meta( get_the_ID(), 'qa_question_status', true );
				if( $qa_question_status == 'solved' ){
					$is_solved_class = 'solved';
					$is_solved_icon = '<i class="fa fa-check-circle"></i>';					
					$is_solved_text = __('Solved', 'question-answer');
					}
				else{
					$is_solved_class = 'not-solved';
					$is_solved_icon = '<i class="fa fa-times"></i>';
					$is_solved_text = __('Not Solved', 'question-answer');
					}
				?>
                
                <a href="<?php echo '?filter_by=solved'; ?>" class="is-solved <?php echo $is_solved_class; ?>"><?php  echo $is_solved_icon.' '.$is_solved_text; ?></a>                
                <a href="<?php echo '?user_slug='.get_the_author_meta('user_login'); ?>" class="author"><i class="fa fa-user"></i> <?php echo get_the_author_meta('display_name'); ?></a>
				<?php if( !empty($category) ) { ?>
				<a href="<?php echo '?category='.$category[0]->slug; ?>" class="category"><i class="fa fa-folder-open"></i> <?php echo $category[0]->name; ?></a>
				<?php } ?>
                <a href="<?php echo '?date='.get_the_date('d-m-Y'); ?>" class="date"><i class="fa fa-clock-o"></i> <?php echo get_the_date('M d, Y'); ?></a>
			</div>
		</div>

	</div>