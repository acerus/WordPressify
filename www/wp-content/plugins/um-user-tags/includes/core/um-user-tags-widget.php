<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class um_user_tags extends WP_Widget {

	function __construct() {
		
		parent::__construct(
		
		// Base ID of your widget
		'um_user_tags', 

		// Widget name will appear in UI
		__('Ultimate Member - User Tags', 'um-user-tags'), 

		// Widget description
		array( 'description' => __( 'Display user tags in a widget', 'um-user-tags' ), ) 
		);
	
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$term_id = $instance['term_id'];
        $user_field = ! empty( $instance['user_field'] ) ? $instance['user_field'] : '';
		$orderby = $instance['orderby'];
		$num = $instance['num'];
		
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		// This is where you run the code and display the output
		echo do_shortcode('[ultimatemember_tags term_id=' . $term_id .' user_field=' . $user_field .' number=' . $num . ' orderby='. $orderby . ']');
		
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'User Tags', 'um-user-tags' );
		}
		
		if ( isset( $instance[ 'user_field' ] ) ) {
			$user_field = $instance['user_field'];
		} else {
			$user_field = 0;
		}

		if ( isset( $instance[ 'term_id' ] ) ) {
			$term_id = $instance['term_id'];
		} else {
			$term_id = 0;
		}
		
		if ( isset( $instance[ 'orderby' ] ) ) {
			$orderby = $instance['orderby'];
		} else {
			$orderby = 'count';
		}
		
		if ( isset( $instance['num'] ) ) {
			$num = $instance['num'];
		} else {
			$num = 10;
		}
		
		$terms = get_terms( 'um_user_tag', array(
            'hide_empty' => 0,
            'parent' => 0
		) );
		
		// Widget admin form
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<?php if ( $terms ) { ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'term_id' ); ?>"><?php _e( 'Select the user tags type:' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'term_id' ); ?>" id="<?php echo $this->get_field_id( 'term_id' ); ?>">
				<?php foreach( $terms as $term ) { ?>
				<option value="<?php echo $term->term_id; ?>" <?php echo $term->term_id == $term_id ? "selected" : ""; ?> ><?php echo $term->name; ?></option>
				<?php } ?>
			</select>
		</p>

        <?php $tags = get_option( 'um_user_tags_filters' );

            if ( $tags ) { ?>

                <p>
                    <label for="<?php echo $this->get_field_id( 'user_field' ); ?>"><?php _e( 'Select the user field:' ); ?></label>
                    <select name="<?php echo $this->get_field_name( 'user_field' ); ?>" id="<?php echo $this->get_field_id( 'user_field' ); ?>">
                        <?php foreach( $tags as $tag => $term_id ) {
                            $data = UM()->fields()->get_field( $tag ); ?>
                            <option value="<?php echo $tag ?>" <?php echo $tag == $user_field ? "selected" : ""; ?> ><?php echo $data['title'] ?></option>
                        <?php } ?>
                    </select>
                </p>

            <?php } ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order tags by:' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
				<option value="name" <?php echo 'name' == $orderby ? "selected" : ""; ?> ><?php _e('Name','um-user-tags'); ?></option>
				<option value="count" <?php echo 'count' == $orderby ? "selected" : ""; ?> ><?php _e('Count','um-user-tags'); ?></option>
				<option value="id" <?php echo 'id' == $orderby ? "selected" : ""; ?> ><?php _e('ID','um-user-tags'); ?></option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'num' ); ?>"><?php _e( 'Maximum number of tags to show:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'num' ); ?>" name="<?php echo $this->get_field_name( 'num' ); ?>" type="text" value="<?php echo esc_attr( $num ); ?>" />
		</p>
		
		<?php } ?>
		
		<?php 
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['term_id'] = ( ! empty( $new_instance['term_id'] ) ) ? strip_tags( $new_instance['term_id'] ) : 0;
		$instance['user_field'] = ( ! empty( $new_instance['user_field'] ) ) ? strip_tags( $new_instance['user_field'] ) : 0;
		$instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? strip_tags( $new_instance['orderby'] ) : 'count';
		$instance['num'] = ( ! empty( $new_instance['num'] ) ) ? strip_tags( $new_instance['num'] ) : 0;
		return $instance;
	}

}