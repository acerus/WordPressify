<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QAWidgetCategories extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'qa_widget_categories', __('Question Answer - Categories', 'question-answer'), array( 'description' => __( 'Show Categories.', 'question-answer' ), ) );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$display_count = $instance['display_count'];	
		$display_type = $instance['display_type'];			
		
		//$display_count = apply_filters( 'widget_title', $instance['display_count'] );
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		

		
		echo '<div class="qa_widget_categories">';
		
		$question_cat = get_terms('question_cat', array('hide_empty' => false,) );
		
		//echo '<pre>'.var_export($question_cat, true).'</pre>';
		if($display_type=='list'):
		
		echo '<ul>';
		foreach($question_cat as $terms){
			
			$name = $terms->name;
			$term_id = $terms->term_id;			
			$count = $terms->count;				
			$term_link = get_term_link($term_id);	
			echo '<li>';
			echo '<a title="Question category." class="term-name" href="'.$term_link.'" >';
			echo $name;
			echo '</a>';
			
			if($display_count =='yes'){
				
				echo '<span title="Total question." class="count">('.$count.')</span>';
				
				}
			
			echo '</li>';
			
			}
		
		echo '</ul>';
		
		
		
		elseif($display_type=='drop_down'):
		
		
		echo '<select>';
		foreach($question_cat as $terms){
			
			$name = $terms->name;
			$term_id = $terms->term_id;			
			$count = $terms->count;				
			$term_link = get_term_link($term_id);	
			
			echo '<option class="term-name" href="'.$term_link.'" >';
			echo $name;
			
			if($display_count =='yes'){
				
				echo '<span class="count">('.$count.')</span>';
				
				}
			
			echo '</option>';
			

			
			}
		
		echo '</select>';
		
		
		
		
		endif;
		
		
		echo '</div>';
		
		// echo '<pre>'; print_r( $featured_author ); echo '</pre>';
		
		
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title = isset( $instance[ 'title' ] ) ? $title = $instance[ 'title' ] : __( 'Categories', 'question-answer' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		
		$display_count = isset( $instance[ 'display_count' ] ) ? $display_count = $instance[ 'display_count' ] : __( 'Categories', 'question-answer' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'display_count' ); ?>"><?php _e( 'Display count:' ); ?></label> 
        
        <select id="<?php echo $this->get_field_id( 'display_count' ); ?>" name="<?php echo $this->get_field_name( 'display_count' ); ?>">
        	<option <?php if($display_count=='yes') echo 'selected'; ?> value="yes">Yes</option>
            <option <?php if($display_count=='no') echo 'selected'; ?> value="no">No</option>
        </select>
        
	
		</p>
		<?php
		
		$display_type = isset( $instance[ 'display_type' ] ) ? $instance[ 'display_type' ] : '';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'display_type' ); ?>"><?php _e( 'Display type:' ); ?></label> 
        
        <select id="<?php echo $this->get_field_id( 'display_type' ); ?>" name="<?php echo $this->get_field_name( 'display_type' ); ?>">
        	<option <?php if($display_type=='list') echo 'selected'; ?> value="list">List</option>
            <option <?php if($display_type=='drop_down') echo 'selected'; ?> value="drop_down">Drop down</option>
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