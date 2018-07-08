<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


$question_id = get_the_ID();


	if ( get_query_var('paged') ) $paged = get_query_var('paged');
	elseif ( get_query_var('page') ) $paged = get_query_var('page');
	else $paged = 1;
	
	$qa_answer_item_per_page 	= get_option('qa_answer_item_per_page');
	$qa_show_answer_filter 		= get_option('qa_show_answer_filter');
	$qa_answer_filter_options 	= get_option('qa_answer_filter_options');
	$qa_sort_answer 			= isset( $_GET['qa_sort_answer'] ) ? sanitize_text_field($_GET['qa_sort_answer']) : '';
	
	$meta_query[] = array(
		'key' 		=> 'qa_answer_question_id',
		'value' 	=> get_the_ID(),
		'compare'	=> '=',
	);
	
	if( 'answers_older' === $qa_sort_answer ) $order = 'ASC';
	
	if( 'answers_voted' === $qa_sort_answer ) {
		
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' 		=> 'qa_answer_review_users_up',
				'value' 	=> 0,
				'compare'	=> '>',
			),
			array(
				'key' 		=> 'qa_answer_review_users_down',
				'value' 	=> 0,
				'compare'	=> '>',
			),
		);
	}
	
	if( 'answers_top_voted' === $qa_sort_answer ) {
		
		$order_by 	= 'meta_value';
		$meta_key 	= 'qa_answer_review_value';
	}
	
	
	$wp_query = new WP_Query( array (
		'post_type' => 'answer',
		'post_status' => 'publish',
		'orderby' => !empty( $order_by ) ? $order_by : 'date',
		'meta_key' => !empty( $meta_key ) ? $meta_key : '',
		'meta_query' => $meta_query,
		'order' => !empty( $order ) ? $order : 'DESC',
		'posts_per_page' => !empty( $qa_answer_item_per_page ) ? $qa_answer_item_per_page : 10,
		'paged' => $paged,
	) );
	
	?>
	
<div id="answer-of-<?php the_ID(); ?>" <?php post_class('container-answer-section entry-content'); ?>>
	
	
	<br>
	<div class="answer-section-header">
		<span class="fs_18"> <span itemprop="answerCount"><?php echo number_format_i18n($wp_query->found_posts); ?></span> <?php echo __('Answers', 'question-answer'); ?></span>
		
		<?php if( $qa_show_answer_filter == 'yes' ) { ?>
		<div class="float_right answer_header_status">
			<form enctype="multipart/form-data" id="qa_sort_answer_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="GET">
				<span><?php echo __('Sort By:', 'question-answer'); ?></span>
				<select name="qa_sort_answer" class="qa_sort_answer">
					<option value=""><?php echo __('All Answers', 'question-answer'); ?></option>
					
					<?php if( isset($qa_answer_filter_options['answers_voted']) ) { ?>
					<option <?php if( $qa_sort_answer == 'answers_voted' ) echo 'selected'; ?> value="answers_voted"><?php echo __('Voted Answers', 'question-answer'); ?></option> 
					<?php } ?>
					
					<?php if( isset($qa_answer_filter_options['answers_top_voted']) ) { ?>
					<option <?php if( $qa_sort_answer == 'answers_top_voted' ) echo 'selected'; ?> value="answers_top_voted"><?php echo __('Top Voted Answers', 'question-answer'); ?></option>
					<?php } ?>
					
					<?php if( isset($qa_answer_filter_options['answers_older']) ) { ?>
					<option <?php if( $qa_sort_answer == 'answers_older' ) echo 'selected'; ?> value="answers_older"><?php echo __('Older Answers', 'question-answer'); ?></option>
					<?php } ?>
					
				</select>
			</form>
		</div>
		<?php } ?>
		
		
	</div>	
	
	<div class="all-single-answer">
		<?php
		
		if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();
			
			$qa_answer_review_value 		= get_post_meta( get_the_ID(), 'qa_answer_review_value', true );
			$qa_answer_review_users_up 		= get_post_meta( get_the_ID(), 'qa_answer_review_users_up', true );
			$qa_answer_review_users_down 	= get_post_meta( get_the_ID(), 'qa_answer_review_users_down', true );
			
			$reviewd = ( $qa_answer_review_users_up > 0 || $qa_answer_review_users_down > 0 ) ? 'reviewd' : ''; 
			
			$question_id 		= get_post_meta( get_the_ID(), 'qa_answer_question_id', true );
			$best_answer_id		= get_post_meta( $question_id, 'qa_meta_best_answer', true );
			$best_answer_class 	= ( get_the_ID() == $best_answer_id ) ? 'list_best_answer' : ''; 
		
			?>
			
			<div id="single-answer-<?php echo get_the_ID(); ?>" <?php post_class("single-answer $reviewd $best_answer_class"); ?>>
                <div class="best-answer-ribbon"><span><i class="fa fa-trophy best-answer-icon" aria-hidden="true"></i> <?php echo __('Best Answer', 'question-answer'); ?></span></div>
                <?php do_action('qa_action_single_answer_content'); ?>
                <?php do_action('qa_action_single_answer_reply'); ?>
				
			</div> <?php 
			
			
		endwhile; 
		

		?>

            <div class="answer-pagination">



				<?php

				$total_pages = $wp_query->max_num_pages;
				$post_url = get_permalink($question_id);

				$previous_page = $paged + 1;
				$previous_page .= "/";
				if($paged > 0 && $paged < $total_pages): ?>
                    <div class="nav previous">
                        <a  href="<?php echo esc_url($post_url . $previous_page); ?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo __('Previuos', 'question-answer'); ?></a>
                    </div>
				<?php endif; ?>


				<?php
				$next_page = $paged - 1;
				if($next_page == 1) {
					$next_page = ""; // if the first page, don't include the "1/" at the end of the URL
				} else {
					$next_page .= "/";
				}
				if($paged > 1 && $paged <= $total_pages): ?>
                    <div class="nav next">
                        <a href="<?php echo esc_url($post_url . $next_page); ?>"><?php echo __('Next', 'question-answer'); ?> <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    </div>
				<?php endif; ?>

            </div>









        <?php
		
		wp_reset_query();
		endif;
		?>
	
	</div> <br>


</div>

<?php 

	global $qa_css;
	
	$qa_color_best_answer_background = get_option( 'qa_color_best_answer_background', true );
	
	if(!empty($qa_color_best_answer_background)){
		
		$qa_css .= '.all-single-answer .list_best_answer .qa-answer-details{ background: '.$qa_color_best_answer_background.'; }';
		
		}
	
	


