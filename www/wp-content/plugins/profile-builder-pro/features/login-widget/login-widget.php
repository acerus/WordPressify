<?php
function wppb_register_login_widget() {
	register_widget( 'wppb_login_widget' );
}
add_action( 'widgets_init', 'wppb_register_login_widget' );

class wppb_login_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'login', 'description' => __( 'This login widget lets you add a login form in the sidebar.', 'profile-builder' ) );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wppb-login-widget' );

		do_action( 'wppb_login_widget_settings', $widget_ops, $control_ops);

		parent::__construct( 'wppb-login-widget', __('Profile Builder Login Widget', 'profile-builder'), $widget_ops, $control_ops );

	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'wppb_login_widget_title', ( isset( $instance['title'] ) ? $instance['title'] : '' ) );
		$redirect = ( isset( $instance['redirect'] ) ? trim( $instance['redirect'] ) : '' );
		$register = ( isset( $instance['register'] ) ? trim( $instance['register'] ) : '' );
		$lostpass = ( isset( $instance['lostpass'] ) ? trim( $instance['lostpass'] ) : '' );

		echo $before_widget;

		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		echo do_shortcode('[wppb-login display="false" register_url="'.$register.'" lostpassword_url="'.$lostpass.'" redirect="'.$redirect.'"]');

		do_action( 'wppb_login_widget_display', $args, $instance);
			
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['redirect'] = strip_tags( $new_instance['redirect'] );
		$instance['register'] = strip_tags( $new_instance['register'] );
		$instance['lostpass'] = strip_tags( $new_instance['lostpass'] );

		do_action( 'wppb_login_widget_update_action', $new_instance, $old_instance);
		
		return $instance;
	
	}


	function form( $instance ) {

		$defaults = array( 'title' => __('Login', 'profile-builder'), 'redirect' => '', 'register' => '', 'lostpass' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'profile-builder' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'redirect' ); ?>"><?php _e( 'After login redirect URL (optional):', 'profile-builder' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'redirect' ); ?>" class="widefat" type="url" name="<?php echo $this->get_field_name( 'redirect' ); ?>" value="<?php echo $instance['redirect']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e( 'Register page URL (optional):', 'profile-builder' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'register' ); ?>" class="widefat" type="url" name="<?php echo $this->get_field_name( 'register' ); ?>" value="<?php echo $instance['register']; ?>" style="width:100%;" />
		</p>		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e( 'Password Recovery page URL (optional):', 'profile-builder' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'lostpass' ); ?>" class="widefat" type="url" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" value="<?php echo $instance['lostpass']; ?>" style="width:100%;" />
		</p>

	<?php
	
		do_action( 'wppb_login_widget_after_display', $instance);
	}
}

// we can apply this easily, if we need it
function wppb_scroll_down_to_widget($content){
	return "<script> jQuery('html, body').animate({scrollTop: jQuery('#wppb_login').offset().top }) </script>" . $content;
}
//add_filter('wppb_login_wp_error_message', 'wppb_scroll_down_to_widget');

function wppb_require_jquery(){
	wp_enqueue_script( 'jquery' );
}
//add_action( 'wp_enqueue_scripts', 'wppb_require_jquery' );