<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QAWidgetLatestQuestions extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'qa_widget_latest_questions', __('Question Answer - Latest Questions', 'question-answer'), array( 'description' => __( 'Show Latest Questions.', 'question-answer' ), ) );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$limit = $instance['limit'];
		//$title_word_limit =  $instance['title_word_limit'] ;	
			
		if(!empty( $instance['title_word_limit'] )){ $title_word_limit = $instance['title_word_limit']; }
		else{ $title_word_limit =5; };
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		
		$limit_count 		= ( (int)$limit > 0 ) ? (int)$limit : 10;
		$latest_questions 	= wp_get_recent_posts( array( 'post_type'=>'question', 'post_status' => array( 'publish' ),'numberposts' => $limit) );
		
		echo '<div class="qa_latest_questions">';
		foreach( $latest_questions as $question ) {
			
			$wp_answer_query = new WP_Query( array (
				'post_type' => 'answer',
				'post_status' => array( 'publish' ),
				'meta_query' => array( array(
					'key'     => 'qa_answer_question_id',
					'value'   => $question['ID'],
					'compare' => '='
				) ),
				'posts_per_page' => -1
			) );
			
			echo '<div class="single_qs">';
			echo  '<div class="qs_title"><a href="'.get_permalink($question['ID']).'">'.wp_trim_words(get_the_title( $question['ID'] ), $title_word_limit).'</a></div>';
			echo  '<div class="qs_answer">'.sprintf(__('<b>%s</b> Answer(s)', 'question-answer' ), $wp_answer_query->found_posts).'</div>';
			echo '</div>';
			wp_reset_query();
		} 
		echo '</div>';
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title = isset( $instance[ 'title' ] ) ? $title = $instance[ 'title' ] : __( 'Latest Questions', 'question-answer' );
		$limit = isset( $instance[ 'limit' ] ) ? $limit = $instance[ 'limit' ] : 10;
		$title_word_limit = isset( $instance[ 'title_word_limit' ] ) ? $title_word_limit = $instance[ 'title_word_limit' ] : 5;		
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        
		<p>
			<label for="<?php echo $this->get_field_id( 'title_word_limit' ); ?>"><?php _e( 'Title Word Limit:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title_word_limit' ); ?>" name="<?php echo $this->get_field_name( 'title_word_limit' ); ?>" type="number" value="<?php echo esc_attr( $title_word_limit ); ?>" />
		</p> 
        
        
        
        
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Total Question:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
        
 
        
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['title_word_limit'] = ( ! empty( $new_instance['title_word_limit'] ) ) ? strip_tags( $new_instance['title_word_limit'] ) : '';		
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';
		return $instance;
	}
}