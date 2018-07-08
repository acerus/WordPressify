<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QAWidgetTags extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'qa_widget_tags', __('Question Answer - Tags', 'question-answer'), array( 'description' => __( 'Show Tags.', 'question-answer' ), ) );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$display_count = $instance['display_count'];	
		$display_type = $instance['display_type'];
		$max_count = $instance['max_count'];					
		if(empty($max_count)) $max_count = 10;
		
		
		
		//$display_count = apply_filters( 'widget_title', $instance['display_count'] );
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		

		
		echo '<div class="qa_widget_tags '.$display_type.'">';
		
		$question_tags = get_terms('question_tags');
		
		$question_tags_cont = count($question_tags);
		
		//echo '<pre>'.var_export($question_cat, true).'</pre>';

		
		echo '<ul>';
		
		
		$i = 1;
		foreach($question_tags as $terms){
			
			if ($i > $max_count) break;
			$i++;
			
			$name = $terms->name;
			$term_id = $terms->term_id;			
			$count = $terms->count;				
			$term_link = get_term_link($term_id);	
			echo '<li>';
			echo '<a title="Question tags." class="term-name" href="'.$term_link.'" >';
			echo $name;
			echo '</a>';
			
			if($display_count =='yes'){
				
				echo '<span title="Total question." class="count">('.$count.')</span>';
				
				}
			
			echo '</li>';
			
			}
		
		echo '</ul>';

		
		
		echo '</div>';
		
		// echo '<pre>'; print_r( $featured_author ); echo '</pre>';
		
		
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title = isset( $instance[ 'title' ] ) ? $title = $instance[ 'title' ] : __( 'Tags', 'question-answer' );
		$max_count = isset( $instance[ 'max_count' ] ) ? $instance[ 'max_count' ] : 10;		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        
        
		<p>
		<label for="<?php echo $this->get_field_id( 'max_count' ); ?>"><?php _e( 'Max Count:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'max_count' ); ?>" name="<?php echo $this->get_field_name( 'max_count' ); ?>" type="text" value="<?php echo esc_attr( $max_count ); ?>" />
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
		$instance['max_count'] = ( ! empty( $new_instance['max_count'] ) ) ? strip_tags( $new_instance['max_count'] ) : 10;				
		return $instance;
	}
}