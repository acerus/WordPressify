<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class um_online_users extends WP_Widget {

	function __construct() {
		
		parent::__construct(
		
		// Base ID of your widget
		'um_online_users', 

		// Widget name will appear in UI
		__('Ultimate Member - Online Users', 'um-online'), 

		// Widget description
		array( 'description' => __( 'Shows your online users.', 'um-online' ), ) 
		);
	
	}


	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$max = $instance['max'];
		$role = $instance['role'];

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// This is where you run the code and display the output
		echo do_shortcode('[ultimatemember_online max="' . $max . '" roles="' . $role . '"]');
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Who is Online', 'um-reviews' );
		}
		
		if ( isset( $instance[ 'max' ] ) ) {
			$max = $instance[ 'max' ];
		} else {
			$max = 11;
		}
		
		if ( isset( $instance[ 'role' ] ) ) {
			$role = $instance['role'];
		} else {
			$role = 'all';
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
		
		<p>
			<label for="<?php echo $this->get_field_id( 'role' ); ?>"><?php _e( 'Show specific community role?' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'role' ); ?>" id="<?php echo $this->get_field_id( 'role' ); ?>">
				<option value="all" <?php echo "all" == $role ? "selected" : ""; ?> ><?php _e('All roles','um-reviews'); ?></option>
				<?php foreach( UM()->roles()->get_roles() as $key => $value ) { ?>
				<option value="<?php echo $key; ?>" <?php echo $key == $role ? "selected" : ""; ?> ><?php echo $value; ?></option>
				<?php } ?>
			</select>
		</p>
		
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['max'] = ( ! empty( $new_instance['max'] ) ) ? strip_tags( $new_instance['max'] ) : 11;
		$instance['role'] = ( ! empty( $new_instance['role'] ) ) ? strip_tags( $new_instance['role'] ) : 'all';
		return $instance;
	}

}