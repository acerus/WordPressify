<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	do_action('qa_action_breadcrumb');

	$class_qa_functions = new class_qa_functions();
	
	if( empty($qa_post_per_page) ) 
	$qa_post_per_page = get_option( 'qa_question_item_per_page', 10 );
	
	
	if ( get_query_var('paged') ) { $paged = get_query_var('paged');} 
	elseif ( get_query_var('page') ) { $paged = get_query_var('page'); } 
	else { $paged = 1; }
	
	?>
<div class="questions-archive"> 
	
	<?php 

	if( !empty($_GET['keywords']) ){
		$keywords = sanitize_text_field($_GET['keywords']);

		do_action('qa_action_submit_search');
	}
	
	$filter_by 	= isset( $_GET['filter_by'] ) ? sanitize_text_field( $_GET['filter_by'] ) : '';
	$user_slug 	= isset( $_GET['user_slug'] ) ? sanitize_text_field( $_GET['user_slug'] ) : '';
	$order_by	= empty( $_GET['order_by'] ) ? '' : sanitize_text_field( $_GET['order_by'] );
	$order		= ( $order_by == 'title' ) ? 'ASC' : sanitize_text_field($order);
	

	if( $order_by === 'date_older' ) {
		
		$order_by = 'date';
		$order = 'ASC';
		
	}
	
	$tax_query = array();
	if( !empty($_GET['category']) || !empty($category) ){
				
		$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : $category;
		$tax_query[] = array(
			array(
			   'taxonomy' => 'question_cat',
			   'field' => 'slug',
			   'terms' => $category,
			)
		);
	}
	
	$date_query = array();
	if( !empty( $_GET['date'] ) || !empty( $date ) ) {
			
		$date = isset( $_GET['date'] ) ? sanitize_text_field($_GET['date']) : '';
		$arr_date = explode( "-", $date );
		
		$date_query = array(
			'year'  => intval($arr_date[2]),
			'month' => intval($arr_date[1]),
			'day'   => intval($arr_date[0]),
		);
	}
	
	$meta_query = array();
	if( !empty( $_GET['filter_by'] ) || !empty( $filter_by ) ) {
			
		$filter_by = isset( $_GET['filter_by'] ) ? sanitize_text_field($_GET['filter_by']) : '';
		
		$meta_query[] = array(
			'key'     => 'qa_question_status',
			'value'   => $filter_by,
			'compare' => '=',
		);
	}
	
	?>


	<form class="question_serch_filter" method="GET">
		
        <div class="form-meta">
		<input autocomplete="off" class="ui-autocomplete-input" type="search" id="keyword" name="keywords" placeholder="<?php echo __('keywords', 'question-answer'); ?>" value="<?php echo $keywords; ?>" />
		</div>
        
        <div class="form-meta">

		<select id="category" name="category">
			<option value=""><?php echo __('Select a category', 'question-answer'); ?></option> <?php

			foreach( qa_get_categories() as $cat_id => $cat_info ) { ksort($cat_info);



				$this_category = get_term( $cat_id );

				//var_dump($this_category);

				//if(!empty($this_category))
				foreach( $cat_info as $key => $value ) {

					//var_dump($category);

					if( $key == 0 )  {
						?><option <?php selected( $this_category->slug, $category ); ?> value="<?php echo $this_category->slug; ?>"><?php echo $value; ?></option><?php
					} else {
						$this_category = get_category( $key );

						?><option <?php selected( $this_category->slug, $category ); ?> value="<?php echo $this_category->slug; ?>">  - <?php echo $value; ?></option> <?php
					}
				}
			} ?>
		</select>
        </div>
		
        <div class="form-meta">
            <select id="order_by" name="order_by"> <?php
                $sorter = $class_qa_functions->qa_question_archive_filter_options();
                foreach( $sorter as $key => $value ) {
                    ?><option <?php selected( $key, $order_by ); ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                } ?>
            </select>
		</div>
   	 	<div class="form-meta">
            <select id="filter_by" name="filter_by"> <?php
                $status = $class_qa_functions->qa_question_status();
                foreach( $status as $key => $value ) {
                    ?><option <?php selected( $key, $filter_by ); ?> value="<?php echo $key; ?>"><?php echo $value; ?></option><?php
                } ?>
            </select>
		</div>
        <div class="form-meta">
		    <input type="submit" value="<?php echo __('Search', 'question-answer'); ?>" />
		</div>
	</form>
	<?php 
	
	if( !empty( $keywords ) ) 	{ ?> <div class="filter"><span><?php echo __('Keyword:', 'question-answer'); ?> </span> <span class="value"><?php echo $keywords; ?></span></div><?php }
	if( !empty( $date ) ) 		{ ?> <div class="filter"><span><?php echo __('Date:', 'question-answer'); ?> </span> <span class="value"><?php echo $date; ?></span></div><?php }
	if( !empty( $category) ) 	{ ?> <div class="filter"><span><?php echo __('Category:', 'question-answer'); ?> </span><span class="value"><?php echo ucwords($category); ?></span></div><?php }
	if( !empty( $order_by) ) 	{ ?> <div class="filter"><span><?php echo __('Sort By:', 'question-answer'); ?> </span><span class="value"><?php echo $order_by; ?></span></div><?php }
	if( !empty( $user_slug) ) 		{ ?> <div class="filter"><span><?php echo __('Author:', 'question-answer'); ?> </span><span class="value"><?php echo $user_slug; ?></span></div><?php }

    $qa_featured_questions = get_option( 'qa_featured_questions', array('') );


    $wp_query_sticky = new WP_Query( array (
        'post_type' => 'question',
        'post__in' => $qa_featured_questions,
        'ignore_sticky_posts'=>true,
        'posts_per_page' => $qa_post_per_page,
        'tax_query' => $tax_query,
        'meta_query' => $meta_query,

    ) );


	$wp_query = new WP_Query( array (
		'post_type' => 'question',
		'post_status' => empty( $filter_by ) ? array( 'publish', 'private' ) : $filter_by,
		'author_name' => $user_slug,
        'post__not_in' => $qa_featured_questions,
        'ignore_sticky_posts'=>true,
		's' => $keywords,
		'order' => empty( $order ) ? 'DESC' : $order,
		'orderby' => empty( $order_by ) ? 'date' : $order_by,
		'tax_query' => $tax_query,
		'meta_query' => $meta_query,
		'date_query' => $date_query,		
		'posts_per_page' => $qa_post_per_page,
		'paged' => $paged,
	) );
			
	?> 
	
	<!-- 
    <div class="question_found"><b><?php echo __('Total:', 'question-answer'); ?> <em><?php echo $wp_query->found_posts; ?> <?php echo __('Questions', 'question-answer'); ?></em></b></div>
    --> 
	
	<?php

    if($paged==1 && !empty($qa_featured_questions)):

        if ( $wp_query_sticky->have_posts() ) :
            while ( $wp_query_sticky->have_posts() ) : $wp_query_sticky->the_post();

                do_action( 'qa_action_question_archive_single', $wp_query_sticky );

            endwhile;
        endif;

    endif;



	if ( $wp_query->have_posts() ) : 
	while ( $wp_query->have_posts() ) : $wp_query->the_post();
			
		do_action( 'qa_action_question_archive_single', $wp_query );
	
	endwhile;

	$big = 999999999;
	$paginate = array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, $paged ),
		'total' => $wp_query->max_num_pages
	);
			
	?><div class="paginate"> <?php echo paginate_links($paginate); ?> </div> <?php		
	
	wp_reset_query();
			
	else: ?><span><?php echo __('No question found', 'question-answer'); ?></span> <?php
	endif;

    global $qa_css;

    $qa_vote_button_bg_color = get_option( 'qa_vote_button_bg_color' );
    if( empty( $qa_vote_button_bg_color ) ) $qa_vote_button_bg_color = '';


    $qa_css .= ".qa-single-vote .qa-thumb-up, .qa-single-vote .qa-thumb-reply, .qa-single-vote .qa-thumb-down{ background: $qa_vote_button_bg_color;border:1px solid ".$qa_vote_button_bg_color." } .votted{background: rgba(0, 0, 0, 0) linear-gradient(to bottom, ".$qa_vote_button_bg_color." 5%, #fff 60%) repeat scroll 0 0 !important; color:".$qa_vote_button_bg_color." !important; border:1px solid ".$qa_vote_button_bg_color." !important;}";


	?>
			
</div>
			