<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class um_my_following extends WP_Widget {

	function __construct() {
		
		parent::__construct(
		
		// Base ID of your widget
		'um_my_following', 

		// Widget name will appear in UI
		__('Ultimate Member - Following', 'um-followers'), 

		// Widget description
		array( 'description' => __( 'Shows users they follow in a widget.', 'um-followers' ), ) 
		);
	
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$max = $instance['max'];
		
		if ( !is_user_logged_in() ) return;

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		// This is where you run the code and display the output
		echo do_shortcode('[ultimatemember_following style=avatars max='.$max.']');
		
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Following', 'um-followers' );
		}
		
		if ( isset( $instance[ 'max' ] ) ) {
			$max = $instance[ 'max' ];
		} else {
			$max = 11;
		}
		
		// Widget admin form
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'max' ); ?>"><?php _e( 'Maximum number of users in first view:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" type="text" value="<?php echo esc_attr( $max ); ?>" />
		</p>
		
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['max'] = ( ! empty( $new_instance['max'] ) ) ? strip_tags( $new_instance['max'] ) : 11;
		return $instance;
	}

}

class um_my_followers extends WP_Widget {

	function __construct() {
		
		parent::__construct(
		
		// Base ID of your widget
		'um_my_followers', 

		// Widget name will appear in UI
		__('Ultimate Member - Followers', 'um-followers'), 

		// Widget description
		array( 'description' => __( 'Shows user followers in a widget.', 'um-followers' ), ) 
		);
	
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$max = $instance['max'];
		
		if ( !is_user_logged_in() ) return;

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		// This is where you run the code and display the output
		echo do_shortcode('[ultimatemember_followers style=avatars max='.$max.']');
		
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Followers', 'um-followers' );
		}
		
		if ( isset( $instance[ 'max' ] ) ) {
			$max = $instance[ 'max' ];
		} else {
			$max = 11;
		}
		
		// Widget admin form
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'max' ); ?>"><?php _e( 'Maximum number of users in first view:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" type="text" value="<?php echo esc_attr( $max ); ?>" />
		</p>
		
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['max'] = ( ! empty( $new_instance['max'] ) ) ? strip_tags( $new_instance['max'] ) : 11;
		return $instance;
	}

}