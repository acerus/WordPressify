<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QAWidgetLeaderboard extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'qa_widget_leaderboard', __('Question Answer - Leaderboard', 'question-answer'), array( 'description' => __( 'Show Leaderboard.', 'question-answer' ), ) );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$limit = (int) $instance['limit'];
        $date_range =  isset( $instance[ 'date_range' ] ) ? $date_range = $instance[ 'date_range' ] : 'this_month';
        $date_ranges_start = $instance['date_ranges_start'];
        $date_ranges_end = $instance['date_ranges_end'];
		$exclude_users = $instance['exclude_users'];

		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		
		$limit = ( (int)$limit > 0 ) ? (int)$limit : 5;

		$query_args['post_type'] = 'answer';
       // $query_args['limit'] = $limit_count;
        $query_args['post_status'] = array('publish');



        if($date_range=='last_7_days'){

            $start_date_day = date("d", strtotime("7 days ago"));
            $start_date_month = date("m", strtotime("7 days ago"));
            $start_date_year = date("Y", strtotime("7 days ago"));

            $end_date_day = date("d");
            $end_date_month = date("m");
            $end_date_year = date("Y");

            $query_args['date_query'] = array(
                array(
                    'before'  => array(
                        'year'  => $end_date_year,
                        'month' => $end_date_month,
                        'day'   => $end_date_day,
                    ),
                    'after' => array(
                        'year'  => $start_date_year,
                        'month' => $start_date_month,
                        'day'   => $start_date_day,
                    ),
                    'inclusive' => true,

                ),
            );



        }
        elseif($date_range=='last_30_days'){

            $start_date_day = date("d", strtotime("30 days ago"));
            $start_date_month = date("m", strtotime("30 days ago"));
            $start_date_year = date("Y", strtotime("30 days ago"));

            $end_date_day = date("d");
            $end_date_month = date("m");
            $end_date_year = date("Y");

            $query_args['date_query'] = array(
                array(
                    'before'  => array(
                        'year'  => $end_date_year,
                        'month' => $end_date_month,
                        'day'   => $end_date_day,
                    ),
                    'after' => array(
                        'year'  => $start_date_year,
                        'month' => $start_date_month,
                        'day'   => $start_date_day,
                    ),
                    'inclusive' => true,

                ),
            );


        }

        elseif($date_range=='this_month'){

            $total_day = date('t');
            $start_date_day = 1;
            $start_date_month = date("m");
            $start_date_year = date("Y");

            $end_date_day = $total_day;
            $end_date_month = date("m");
            $end_date_year = date("Y");

            $query_args['date_query'] = array(
                array(
                    'before'  => array(
                        'year'  => $end_date_year,
                        'month' => $end_date_month,
                        'day'   => $end_date_day,
                    ),
                    'after' => array(
                        'year'  => $start_date_year,
                        'month' => $start_date_month,
                        'day'   => $start_date_day,
                    ),

                ),
            );





        }
        elseif($date_range=='prev_month'){

            $total_day = date('t');

            $start_date_day = 1;
            $start_date_month = date("m")-1;
            $start_date_year = date("Y");

            $end_date_day = $total_day;
            $end_date_month = date("m")-1;
            $end_date_year = date("Y");

            $query_args['date_query'] = array(
                array(
                    'before'  => array(
                        'year'  => $end_date_year,
                        'month' => $end_date_month,
                        'day'   => $end_date_day,
                    ),
                    'after' => array(
                        'year'  => $start_date_year,
                        'month' => $start_date_month,
                        'day'   => $start_date_day,
                    ),

                ),
            );


        }

        elseif($date_range=='date_ranges'){

            $start_date = $date_ranges_start;
            $start_date_array =  explode('-',  $start_date);


            $end_date = $date_ranges_end;
            $end_date_array = explode('-',  $end_date);

            $start_date_year = $start_date_array[0];
            $start_date_month = $start_date_array[1];
            $start_date_day = $start_date_array[2];

            $end_date_year = $end_date_array[0];
            $end_date_month = $end_date_array[1];
            $end_date_day = $end_date_array[2];




            $query_args['date_query'] = array(
                array(
                    'before'  => array(
                        'year'  => $end_date_year,
                        'month' => $end_date_month,
                        'day'   => $end_date_day,
                    ),
                    'after' => array(
                        'year'  => $start_date_year,
                        'month' => $start_date_month,
                        'day'   => $start_date_day,
                    ),


                ),
            );



        }

		$exclude_users_arr = explode(',', $exclude_users);
        $wp_query = new WP_Query($query_args);

        if ( $wp_query->have_posts() ) :

            $i = 0;
            while ( $wp_query->have_posts() ) : $wp_query->the_post();
                $answers_count_data[get_the_author_meta("ID")][$i] = array('answer_id'=>get_the_id());
                $i++;
            endwhile;
        endif;

        echo '<div class="qa_featured_author">';

		if(!empty($answers_count_data))
			foreach( $answers_count_data as $user_id => $answers ) {

				$author_data = get_user_by('ID', $user_id);
				$answer_count = count($answers);
				$leader_board_data[$user_id] = array('total_answer'=>$answer_count);
			}

        $i=1;

        if(!empty($leader_board_data))
        foreach( $leader_board_data as $user_id => $leader_user ) {
            if($limit >= $i){

                if(!in_array($user_id, $exclude_users_arr)):

	                $author_data = get_user_by('ID', $user_id);
	                $answer_count = $leader_user['total_answer'];

	                echo '<div class="single_author">';
	                echo '<div class="author_avatar">'.get_avatar( $user_id, "45" ).'</div>';
	                echo '<div class="author_name">'.$author_data->display_name.'</div>';
	                echo '<div class="author_answered">'.sprintf( ''.__('Total answers: %s', 'question-answer'), $answer_count ).'</div>';
	                echo '</div>';
	                //$i--;
                endif;

            }
            else{

                break;
            }

            $i++;

        }


		echo '</div>';

		
		
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title = isset( $instance[ 'title' ] ) ? $title = $instance[ 'title' ] : __( 'Leaderboard', 'question-answer' );
        $date_range = isset( $instance[ 'date_range' ] ) ? $date_range = $instance[ 'date_range' ] : 'last_7_days';
        $date_ranges_start = isset( $instance[ 'date_ranges_start' ] ) ? $date_ranges_start = $instance[ 'date_ranges_start' ] : '';
        $date_ranges_end = isset( $instance[ 'date_ranges_end' ] ) ? $date_ranges_end = $instance[ 'date_ranges_end' ] : '';
		$exclude_users = isset( $instance[ 'exclude_users' ] ) ? $exclude_users = $instance[ 'exclude_users' ] : '';


		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		
		$limit = isset( $instance[ 'limit' ] ) ? $limit = $instance[ 'limit' ] : __( 'Leaderboard', 'question-answer' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" placeholder="5" />
		</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'date_range' ); ?>"><?php _e( 'Date Ranges:' ); ?></label>

            <select name="<?php echo $this->get_field_name( 'date_range' ); ?>">
                <option value="last_7_days" <?php if($date_range=='last_7_days') echo esc_attr('selected'); ?> >Last 7 Days</option>
                <option value="last_30_days" <?php if($date_range=='last_30_days') echo esc_attr('selected'); ?>>Last 30 Days</option>
                <option value="this_month" <?php if($date_range=='this_month') echo esc_attr('selected'); ?>>This month</option>
                <option value="prev_month" <?php if($date_range=='prev_month') echo esc_attr('selected'); ?>>Last month</option>
                <option value="date_ranges" <?php if($date_range=='date_ranges') echo esc_attr('selected'); ?>>Date range</option>

            </select>
        </p>
        <p>
            Start date:<br>
            <input class="widefat qa_date" id="<?php echo $this->get_field_id( 'date_ranges_start' ); ?>" name="<?php echo $this->get_field_name( 'date_ranges_start' ); ?>" type="text" value="<?php echo esc_attr( $date_ranges_start ); ?>" placeholder="2018-05-04" />
        </p>
        <p>
            End date:<br>
            <input class="widefat qa_date" id="<?php echo $this->get_field_id( 'date_ranges_end' ); ?>" name="<?php echo $this->get_field_name( 'date_ranges_end' ); ?>" type="text" value="<?php echo esc_attr( $date_ranges_end ); ?>" placeholder="2018-05-04" />
        </p>

        <p>
            Exclude users(Comma "," separated):<br>
            <input class="widefat" id="<?php echo $this->get_field_id( 'exclude_users' ); ?>" name="<?php echo $this->get_field_name( 'exclude_users' ); ?>" type="text" value="<?php echo esc_attr( $exclude_users ); ?>" placeholder="1,2,3" />
        </p>



		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';
        $instance['date_range'] = ( ! empty( $new_instance['date_range'] ) ) ? strip_tags( $new_instance['date_range'] ) : '';
        $instance['date_ranges_start'] = ( ! empty( $new_instance['date_ranges_start'] ) ) ? strip_tags( $new_instance['date_ranges_start'] ) : '';
        $instance['date_ranges_end'] = ( ! empty( $new_instance['date_ranges_end'] ) ) ? strip_tags( $new_instance['date_ranges_end'] ) : '';
		$instance['exclude_users'] = ( ! empty( $new_instance['exclude_users'] ) ) ? strip_tags( $new_instance['exclude_users'] ) : '';

		return $instance;
	}
}