<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 
	
	$class_qa_functions = new class_qa_functions();
	$question_per_page 	= get_option( 'qa_myaccount_question_per_page', 10 );
	
	
	if ( get_query_var('paged') ) { $paged = get_query_var('paged');} 
	elseif ( get_query_var('page') ) { $paged = get_query_var('page'); } 
	else { $paged = 1; }
		
	global $current_user;
	
	$wp_query = new WP_Query( array (
		
		'post_type' 	=> 'question',
		'post_status' 	=> array('publish', 'pending'),
		'author' 		=> $current_user->ID,
		'orderby' 		=> 'date',
		'order' 		=> 'DESC',
		'posts_per_page'=> $question_per_page,
		'paged'			=> $paged,
	
	) );
	
	?>
	<h2><?php echo __('Questions by You', 'question-answer'); ?></h2>
    
    
    
	<div class="front_question_container">
	
		<ul class="front_question_list_header"> <?php 
		
			foreach( $class_qa_functions->qa_question_list_sections() as $section_key => $section_details ) {
				
				echo '<li class="question_section '.$section_key.'"><strong>'.$section_details['title'].'</strong></li>';
				add_filter( "question_filter_$section_key" , "question_filter_function_$section_key" );
				
			} 
			
			
			?>
			
		</ul> <?php 
	
		if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();
			?>
			<ul class="front_question_list">
			
				<?php
				foreach( $class_qa_functions->qa_question_list_sections() as $section_key => $section_details ) {
					echo '<li class="'.$section_key.'">'.apply_filters( "question_filter_$section_key" , '' ).'</li>';
				} ?>

			</ul>
			<?php
		endwhile;
			$html = '<div class="paginate">';
			$big = 999999999;
			$html .= paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, $paged ),
				'total' => $wp_query->max_num_pages
			) );
			$html .= '</div >';	
			echo $html;

		wp_reset_query();
		endif; ?>
	
	</div> <!-- front_question_container -->
	
	
<?php 

	function question_filter_function_question_icon() {
		return '<i class="fa fa-angle-right"></i>';
	}
	
	function question_filter_function_question_title() {
		global $post;
		return '<a href="'.get_permalink().'">'.$post->post_title.'</a>';
	}
	
	function question_filter_function_question_status() {
		global $post;
		
		if( 'pending' === get_post_status( $post->ID ) ) {
				return '<div class="qa_queued">'.__('Pending', 'question-answer').' <i class="fa fa-caret-down"></i></div>';
		} else {
			$status_meta 	= get_post_meta( $post->ID, 'qa_question_status', true );
			
			if( 'processing' === $status_meta )
				return '<div class="qa_publish">'.__('On discussion', 'question-answer').' <i class="fa fa-caret-up"></i></div>';
			
			if( 'hold' === $status_meta )
				return '<div class="qa_hold">'.__('On Hold', 'question-answer').' <i class="fa fa-hand-paper-o"></i></div>';
			
			if( 'solved' === $status_meta ) 
				return '<div class="qa_solved">'.__('Solved', 'question-answer').' <i class="fa fa-check"></i></div>';
		}
	}
	
	function question_filter_function_question_date() {
		return get_the_date();
	}
	function question_filter_function_question_answer() {
		global $post;
		
		$meta_query = array();
		$meta_query[] = array(
			'key' 		=> 'qa_answer_question_id',
			'value' 	=> $post->ID,
			'compare'	=> '=',
		);

		$wp_query_answer = new WP_Query( array (
			'post_type' => 'answer',
			'post_status' => 'publish',
			'meta_query' => $meta_query,
		) );
	
		if( $wp_query_answer->found_posts > 0 )  return $wp_query_answer->found_posts. ' '.__('Answers', 'question-answer');
		
		return '-';
	}







?>