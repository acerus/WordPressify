<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

$keyword = sanitize_text_field($_GET['keywords']);

if(!empty($keyword)){

    global $wpdb;
    $search_post = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_type = 'qa_keyword' AND post_title = '" . $keyword . "' " );

    //echo '<pre>'.var_export($search_post, true).'</pre>';

    if ( $search_post == NULL ){

        $action_post = array(
            'post_type'   	=> 'qa_keyword',
            'post_title'	=> $keyword,
            'post_status'	=> 'pending',
        );
        $search_post_ID = wp_insert_post($action_post, true);
        //$search_post_ID = job_bm_add_keyword($keyword);
    }
    else{

        $search_post_ID = $search_post->ID;
        $search_count = (int) get_post_meta($search_post_ID, 'search_count', true);
        $search_count +=1;
        update_post_meta($search_post_ID, 'search_count', $search_count);
    }



}

