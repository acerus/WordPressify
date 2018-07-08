<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class QAWidgetTopQuestions extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'qa_widget_top_questions', __('Question Answer - Top Questions', 'question-answer'), array( 'description' => __( 'Show Top Questions.', 'question-answer' ), ) );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$limit = $instance['limit'];
        $date_range =  isset( $instance[ 'date_range' ] ) ? $date_range = $instance[ 'date_range' ] : 'this_month';
        $date_ranges_start = $instance['date_ranges_start'];
        $date_ranges_end = $instance['date_ranges_end'];
        $sort_by = $instance['sort_by'];
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		
		//$limit_count = ( (int)$limit > 0 ) ? (int)$limit : 1;

		$query_args['post_type'] = 'question';
        $query_args['posts_per_page'] = $limit;
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
                    'inclusive' => true,
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
        elseif($date_range=='last_30_days'){

            $start_date_day = date("d", strtotime("30 days ago"));
            $start_date_month = date("m", strtotime("30 days ago"));
            $start_date_year = date("Y", strtotime("30 days ago"));

            $end_date_day = date("d");
            $end_date_month = date("m");
            $end_date_year = date("Y");

            $query_args['date_query'] = array(
                array(
                    'inclusive' => true,
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
                    'inclusive' => true,
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
                    'inclusive' => true,
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
                    'inclusive' => true,
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

        //echo '<pre>'.var_export( $query_args, true).'</pre>';
        echo '<div class="qa_top_questions">';

        $wp_query = new WP_Query($query_args);

        if ( $wp_query->have_posts() ) :

            $i = 0;
            while ( $wp_query->have_posts() ) : $wp_query->the_post();

            $up_vote_total = 0;
            $up_down_total = 0;
            $answer_count =0;
            $qa_view_count = get_post_meta(get_the_id(),'qa_view_count', true);
            $qa_answer_review = get_post_meta(get_the_id(),'qa_answer_review', true);



            if(!empty($qa_answer_review['users'])){
                $vote_count = count($qa_answer_review['users']);
                $vote_data = $qa_answer_review['users'];
                //$vote_data_count = array_count_values($vote_data);
                //print_r(array_count_values($vote_data));


                foreach ($vote_data as $vote){


                    $type = $vote['type'];
                    if($type=='up'){
                        $up_vote_total += 1;
                    }
                    elseif($type=='down'){
                        $up_down_total += 1;
                    }


                }

            }
            else{
                $vote_count = 0;
                $vote_data = array();
            }







            $question_data[get_the_id()] = array(
                'question_id'=>get_the_id(),
                'view_count'=>$qa_view_count,
                'vote_count'=>$vote_count,
                'up_vote'=>$up_vote_total,
                'down_vote'=>$up_down_total,
                'answer_count'=>$answer_count,
            );



            //$author_data = get_user_by('login', get_the_author());
            //$author_id = $author_data->ID;

            //$answers_count_data[$author_id][$i] = get_the_id();





                //echo '<pre>'.var_export( $qa_answer_review_vote, true).'</pre>';

            $i++;
            endwhile;

            //echo '<pre>'.var_export( $question_data, true).'</pre>';




            usort($question_data, function($arr1, $arr2) use ($sort_by) {
                return ($arr1[$sort_by] > $arr2[$sort_by]) ? 1 : -1;
            });


            //echo '<pre>'.var_export( $question_data, true).'</pre>';



            foreach ($question_data as $key=>$question_data){

                $question_id = $question_data['question_id'];
                $qa_view_count = $question_data['view_count'];
                $vote_count = $question_data['vote_count'];
                $up_vote = $question_data['up_vote'];
                $down_vote = $question_data['down_vote'];
                //$qa_view_count = get_post_meta($question_id,'qa_view_count', true);

                echo '<div class="single-q">';

                echo '<div class="q-title"><a href="'.get_permalink($question_id).'">'.get_the_title($question_id).'</a></div>';
                echo '<div class="meta-items">';
                echo '<span title="'.__('View count.', 'question-answer').'" class="view-count"><i class="fa fa-eye"></i> '.$qa_view_count.'</span>';
                echo '<span title="'.__('Total vote.', 'question-answer').'" class="vote-count"><i class="fa fa-hand-peace-o"></i> '.$vote_count.'</span>';
                echo '<span title="'.__('Total up vote.', 'question-answer').'" class="up-count"><i class="fa fa-thumbs-up"></i> '.$up_vote.'</span>';
                echo '<span title="'.__('Total down vote.', 'question-answer').'" class="down-count"><i class="fa fa-thumbs-down"></i> '.$down_vote.'</span>';
                echo '</div>';

                echo '</div>';

            }











        else:
            echo __('No question found.', 'question-answer');

        endif;

        //echo '<pre>'.var_export( $answers_count_data, true).'</pre>';


		echo '</div>';
		
		// echo '<pre>'; print_r( $featured_author ); echo '</pre>';
		
		
		
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title = isset( $instance[ 'title' ] ) ? $title = $instance[ 'title' ] : __( 'Top Questions', 'question-answer' );
        $date_range = isset( $instance[ 'date_range' ] ) ? $date_range = $instance[ 'date_range' ] : 'last_7_days';
        $date_ranges_start = isset( $instance[ 'date_ranges_start' ] ) ? $date_ranges_start = $instance[ 'date_ranges_start' ] : '';
        $date_ranges_end = isset( $instance[ 'date_ranges_end' ] ) ? $date_ranges_end = $instance[ 'date_ranges_end' ] : '';
        $sort_by = isset( $instance[ 'sort_by' ] ) ? $sort_by = $instance[ 'sort_by' ] : 'view_count';



		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		
		$limit = isset( $instance[ 'limit' ] ) ? $limit = $instance[ 'limit' ] : 10;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" value="<?php echo esc_attr( $limit ); ?>" />
		</p>

        <p>
            <label for="<?php echo $this->get_field_id( 'sort_by' ); ?>"><?php _e( 'Sort by:' ); ?></label>

            <select name="<?php echo $this->get_field_name( 'sort_by' ); ?>">
                <option value="view_count" <?php if($sort_by=='view_count') echo esc_attr('selected'); ?> >View count</option>
                <option value="vote_count" <?php if($sort_by=='vote_count') echo esc_attr('selected'); ?>>Vote count</option>
                <option value="up_vote" <?php if($sort_by=='up_vote') echo esc_attr('selected'); ?>>Up vote</option>
                <option value="down_vote" <?php if($sort_by=='down_vote') echo esc_attr('selected'); ?>>Down vote</option>
                <!--
                 <option value="answer_count" <?php if($sort_by=='answer_count') echo esc_attr('selected'); ?>>Answer count</option>
-->

            </select>
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
            <input class="widefat qa_date" id="<?php echo $this->get_field_id( 'date_ranges_start' ); ?>" name="<?php echo $this->get_field_name( 'date_ranges_start' ); ?>" type="text" value="<?php echo esc_attr( $date_ranges_start ); ?>" />
        </p>
        <p>
            End date:<br>
            <input class="widefat qa_date" id="<?php echo $this->get_field_id( 'date_ranges_end' ); ?>" name="<?php echo $this->get_field_name( 'date_ranges_end' ); ?>" type="text" value="<?php echo esc_attr( $date_ranges_end ); ?>" />
        </p>



		<?php
	}


    public function sort_by_order( $a, $b ) {

        return $a['view_count'] - $b['view_count'];

    }

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';
        $instance['sort_by'] = ( ! empty( $new_instance['sort_by'] ) ) ? strip_tags( $new_instance['sort_by'] ) : '';
        $instance['date_range'] = ( ! empty( $new_instance['date_range'] ) ) ? strip_tags( $new_instance['date_range'] ) : '';
        $instance['date_ranges_start'] = ( ! empty( $new_instance['date_ranges_start'] ) ) ? strip_tags( $new_instance['date_ranges_start'] ) : '';
        $instance['date_ranges_end'] = ( ! empty( $new_instance['date_ranges_end'] ) ) ? strip_tags( $new_instance['date_ranges_end'] ) : '';


		return $instance;
	}
}