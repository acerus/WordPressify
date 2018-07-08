<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QAWidgetWebsiteStats extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'qa_widget_website_stats', __('Question Answer - Website Stats', 'question-answer'), array( 'description' => __( 'Website Stats.', 'question-answer' ), ) );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$display_count = $instance['display_count'];	
		$display_type = $instance['display_type'];

		$count_posts_question = wp_count_posts('question');
		$published_posts_question = $count_posts_question->publish;
		
		$count_posts_answer = wp_count_posts('answer');
		$published_posts_answer = $count_posts_answer->publish;		
		
		$comments = wp_count_comments();
		$published_comments =  $comments->approved;

		$count_users = count_users();
		$total_users = $count_users['total_users'];
		//$display_count = apply_filters( 'widget_title', $instance['display_count'] );
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		

		
		echo '<div class="qa_widget_website_stats '.$display_type.'">';
		
		echo '<div class="item">';
		echo '<div class="wrap">';
		echo '<span class="count">'.$published_posts_question.'</span>';
		echo '<span class="title">'.__( 'Total Question', 'question-answer' ).'</span>';
		echo '</div>';	
		echo '</div>';
		
		
		echo '<div class="item">';
		echo '<div class="wrap">';
		echo '<span class="count">'.$published_posts_answer.'</span>';
		echo '<span class="title">'.__( 'Total Answer', 'question-answer' ).'</span>';
		echo '</div>';		
		echo '</div>';		
		
	
		
		echo '<div class="item">';
		echo '<div class="wrap">';
		echo '<span class="count">'.$published_comments.'</span>';
		echo '<span class="title">'.__( 'Total Comments', 'question-answer' ).'</span>';
		echo '</div>';			
		echo '</div>';		
		
		echo '<div class="item">';
		echo '<div class="wrap">';
		echo '<span class="count">'.$total_users.'</span>';
		echo '<span class="title">'.__( 'Total user', 'question-answer' ).'</span>';
		echo '</div>';	
		echo '</div>';	
		
		
		// echo '<pre>'; print_r( $featured_author ); echo '</pre>';
		
		
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title = isset( $instance[ 'title' ] ) ? $title = $instance[ 'title' ] : __( 'Website Stats', 'question-answer' );
		$max_count = isset( $instance[ 'max_count' ] ) ? $instance[ 'max_count' ] : 10;		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        
       
        
        
        
		<?php
		
		$display_count = isset( $instance[ 'display_count' ] ) ? $instance[ 'display_count' ] : 'yes';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'display_count' ); ?>"><?php _e( 'Display count:' ); ?></label> 
        
        <select id="<?php echo $this->get_field_id( 'display_count' ); ?>" name="<?php echo $this->get_field_name( 'display_count' ); ?>">
        	<option <?php if($display_count=='yes') echo 'selected'; ?> value="yes">Yes</option>
            <option <?php if($display_count=='no') echo 'selected'; ?> value="no">No</option>
        </select>
        
	
		</p>
		<?php
		
		$display_type = isset( $instance[ 'display_type' ] ) ? $instance[ 'display_type' ] : 'list';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'display_type' ); ?>"><?php _e( 'Display type:' ); ?></label> 
        
        <select id="<?php echo $this->get_field_id( 'display_type' ); ?>" name="<?php echo $this->get_field_name( 'display_type' ); ?>">
        	<option <?php if($display_type=='list') echo 'selected'; ?> value="list">List</option>
            <option <?php if($display_type=='grid') echo 'selected'; ?> value="grid">Grid</option>
        </select>
        
	
		</p>        
        
        
        
        
        
        
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['display_count'] = ( ! empty( $new_instance['display_count'] ) ) ? strip_tags( $new_instance['display_count'] ) : '';
		$instance['display_type'] = ( ! empty( $new_instance['display_type'] ) ) ? strip_tags( $new_instance['display_type'] ) : '';
		
		return $instance;
	}
}