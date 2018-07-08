<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	
echo '<div class="meta-list clearfix">';

/*
 Title: Post Category HTML
 Filter: qa_filter_single_question_meta_category
*/		


?>

<?php
	
	
	
	
/*
 Title: Post Date HTML
 Filter: qa_filter_single_question_meta_post_date
*/	


  // echo apply_filters( 'qa_filter_single_question_meta_post_date', sprintf( __('<span title="'.get_the_date().'" class="qa-meta-item">%s %s</span>', 'question-answer'), '<i class="fa fa-clock-o"></i>', qa_post_duration(get_the_ID()) ) );
	
	//var_dump(get_the_date());
	
    //echo qa_post_duration(get_the_ID());
	
	
	
/*
 Title: Answer Count HTML
 Filter: qa_filter_single_question_meta_answer_count
*/	
	

   
   
   
/*
 Title: Is Solved HTML
 Filter: qa_filter_single_question_meta_is_solved_html
*/	

	$current_user 	= wp_get_current_user();
	$author_id 		= get_post_field( 'post_author', get_the_ID() );
	
	$is_solved = get_post_meta( get_the_ID(), 'qa_question_status', true );
	
	if( $is_solved == 'solved' ){

		$is_solved_class = 'solved';

	} else {

		$is_solved_class 	= 'unsolved';

	}

	if( $current_user->ID == $author_id || in_array( 'administrator', $current_user->roles ) ) {

		?>
		<div class="qa-meta-item qa-is-solved <?php echo $is_solved_class; ?>" post_id="<?php echo get_the_ID(); ?>">
			<?php //echo $is_solved_icon; ?> <?php //echo $is_solved_status; ?>
			<span class="unsolved"><i class="fa fa-times"></i> <?php echo __('Unsolved','question-answer'); ?></span>
			<span class="solved"><i class="fa fa-check"></i> <?php echo __('Solved','question-answer'); ?></span>
			<span class="mark-solved"><i class="fa fa-check"></i> <?php echo __('Mark as Solved','question-answer'); ?></span>
			<span class="mark-unsolved"><i class="fa fa-times"></i> <?php echo __('Mark as Unsolved','question-answer'); ?></span>

		</div>
		<?php



    	//echo apply_filters( 'qa_filter_single_question_meta_is_solved_html', sprintf( '', $is_solved_class, get_the_ID(), $is_solved_icon, $is_solved_status, $qa_ttt ) );
	}
	
	
	
	
	
/*
 Title: Subscriber HTML
 Filter: qa_filter_single_question_meta_subscriber_html
*/	

	$q_subscriber = get_post_meta( get_the_ID(), 'q_subscriber', true );

	if(is_array($q_subscriber) && in_array($current_user->ID, $q_subscriber)){

		$subscribe_icon = '<i class="fa fa-bell"></i>';		
		$subscribe_class = 'subscribed';
		$qa_ttt_text = __('Subscribed', 'question-answer');
	
	} else {
	
		$subscribe_icon = '<i class="fa fa-bell-slash"></i>';	
		$subscribe_class = 'not-subscribed';
		$qa_ttt_text = __('Subscribe', 'question-answer');
	}


	?>
		<div class="qa-meta-item qa-subscribe <?php echo $subscribe_class; ?>" post_id="<?php echo get_the_ID(); ?>">
            <span class="subscribed-done"><i class="fa fa-bell"></i> Subscribed</span>
			<span class="start-subscribe"><i class="fa fa-bell"></i> Subscribe</span>
			<span class="stop-subscribe"><i class="fa fa-bell-slash"></i> Not subscribe</span>
            <span class="cancel-subscribe"><i class="fa fa-bell-slash"></i> Cancel subscribe</span>
		</div>
	<?php


	//echo apply_filters( 'qa_filter_single_question_meta_subscriber_html', sprintf( '', $subscribe_class, get_the_ID(), $subscribe_icon, $qa_ttt_text ) );


	$qa_flag 	= get_post_meta( get_the_ID(), 'qa_flag', true );

	if(empty($qa_flag)) $qa_flag = array();
	$flag_count 		= sizeof($qa_flag);
	$user_ID		= get_current_user_id();


	if( array_key_exists($user_ID, $qa_flag) && $qa_flag[$user_ID]['type']=='flag'  ) {

		$flag_text = __('Unflag', 'question-answer');

	} else {

		$flag_text = __('Flag', 'question-answer');
	}

	echo '<div class="qa-meta-item qa-flag qa-flag-action" post_id="'.get_the_ID().'"><i class="fa fa-flag flag-icon"></i> <span class="flag-text">'.$flag_text.'</span><span class="flag-count">('.$flag_count.')</span> <span class="waiting"><i class="fa fa-cog fa-spin"></i></span> </div>';









	/*
	 Title: Featured HTML
	 Filter: qa_filter_single_question_meta_featured_html
	*/
	
	if(current_user_can('administrator') ){
		
	$qa_featured_questions 	= get_option( 'qa_featured_questions', array() );
	$featured_class 		= in_array( get_the_ID(), $qa_featured_questions ) ? 'qa-featured-yes' : 'qa-featured-no';
	$featured_icon 			= '<i class="fa fa-star" aria-hidden="true"></i>';

	?>
        <div class="qa-meta-item qa-featured <?php echo $featured_class; ?>" post_id="<?php echo  get_the_ID(); ?>">
            <span class="featured"><i class="fa fa-star" aria-hidden="true"></i> Featured</span>
            <span class="not-featured"><i class="fa fa-star" aria-hidden="true"></i> Not featured</span>
            <span class="make-featured"><i class="fa fa-star" aria-hidden="true"></i> Make featured</span>
            <span class="cancel-featured"><i class="fa fa-times" aria-hidden="true"></i> Cancel featured</span>
        </div>
<?php



	echo apply_filters( 'qa_filter_single_question_meta_featured_html', sprintf( '', $featured_class, get_the_ID(), $featured_icon ) );
		
		
		}
	



    
	//echo ' <br class="clearfix" />';


?>
</div>