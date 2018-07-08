<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/


function qa_author_total_question($author_id){

	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'question',
		'author'	   => $author_id,
		'post_status'      => 'publish',

	);
	$posts_array = get_posts( $args );

	return count($posts_array);


}


function qa_author_total_answer($author_id){

	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'answer',
		'author'	   => $author_id,
		'post_status'      => 'publish',

	);
	$posts_array = get_posts( $args );

	return count($posts_array);

}

function qa_author_total_comment($author_id){


	$comments_query = new WP_Comment_Query;

	$args = array(
		'user_id' => $author_id,

	);

	$comments = $comments_query->query( $args );

	return count($comments);

}





function qa_author_q_received_vote_count($author_id){

	$wp_query = new WP_Query( array (
		'post_type' => 'question',
		'author' => $author_id,
		'posts_per_page' => -1,

	) );

	$count = 0;
	if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();

			$post_id = get_the_id();
			$qa_answer_review 	= get_post_meta( $post_id, 'qa_answer_review', true );

			$cast_reviews = isset($qa_answer_review['reviews']) ? $qa_answer_review['reviews'] : 0;

			$count+=$cast_reviews;
			//var_dump($qa_answer_review);

		endwhile;
	endif;

	return $count;


}


function qa_author_a_received_vote_count($author_id){

	$wp_query = new WP_Query( array (
		'post_type' => 'answer',
		'author' => $author_id,
		'posts_per_page' => -1,

	) );

	$count = 0;
	if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();

			$post_id = get_the_id();
			$qa_answer_review 	= get_post_meta( $post_id, 'qa_answer_review', true );

			$cast_reviews = isset($qa_answer_review['reviews']) ? $qa_answer_review['reviews'] : 0;

			$count+=$cast_reviews;
			//var_dump($qa_answer_review);

		endwhile;
	endif;

	return $count;

}




function qa_author_vote_other_q_count($author_id){

	$wp_query = new WP_Query( array (
		'post_type' => 'question',
		'posts_per_page' => -1,

	) );

	$count = 0;
	if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();

			$post_id = get_the_id();
			$qa_answer_review 	= get_post_meta( $post_id, 'qa_answer_review', true );

			$cast_reviews = isset($qa_answer_review['reviews']) ? $qa_answer_review['reviews'] : array();
			$cast_users = isset($qa_answer_review['users']) ? $qa_answer_review['users'] : array();

			if(!empty($cast_users))
			if(array_key_exists($author_id, $cast_users)){

				$count+=1;
			}


			//var_dump($qa_answer_review);

		endwhile;
	endif;

	return $count;


}


function qa_author_vote_other_a_count($author_id){

	$wp_query = new WP_Query( array (
		'post_type' => 'answer',
		'posts_per_page' => -1,

	) );

	$count = 0;
	if ( $wp_query->have_posts() ) :
		while ( $wp_query->have_posts() ) : $wp_query->the_post();

			$post_id = get_the_id();
			$qa_answer_review 	= get_post_meta( $post_id, 'qa_answer_review', true );

			$cast_reviews = isset($qa_answer_review['reviews']) ? $qa_answer_review['reviews'] : array();
			$cast_users = isset($qa_answer_review['users']) ? $qa_answer_review['users'] : array();


			if(array_key_exists($author_id, $cast_users)){

				$count+=1;
			}


			//var_dump($qa_answer_review);

		endwhile;
	endif;

	return $count;


}







function qa_author_a_total_vote_count($author_id){


}


function qa_author_total_follower($author_id){

	global $wpdb;
	$table = $wpdb->prefix . "qa_follow";
	$follow_result = $wpdb->get_results("SELECT * FROM $table WHERE author_id = '$author_id'", ARRAY_A);
	$total_follower = $wpdb->num_rows;

	return $total_follower;
}

function qa_author_total_following($author_id){

	global $wpdb;
	$table = $wpdb->prefix . "qa_follow";
	$follow_result = $wpdb->get_results("SELECT * FROM $table WHERE follower_id = '$author_id'", ARRAY_A);
	$total_follower = $wpdb->num_rows;

	return $total_follower;
}