<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


$cookie_name = 'qa_view';
$q_id = get_the_ID();
$qa_view_count = get_post_meta(get_the_ID(),'qa_view_count', true);


if(isset($_COOKIE[$cookie_name.'_'.$q_id])){
	
		//var_dump($_COOKIE[$cookie_name.'_'.$q_id]);
		//var_dump($qa_view_count);		
		
	}
else{
		//var_dump('No');
	
		setcookie( $cookie_name.'_'.$q_id, $q_id, time() + (86400 * 30)); // 86400 = 1 day
		
		update_post_meta(get_the_ID(), 'qa_view_count', ($qa_view_count+1));
	}

?>