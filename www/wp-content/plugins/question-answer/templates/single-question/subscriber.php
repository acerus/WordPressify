<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


$q_subscriber = get_post_meta(  get_the_ID(), 'q_subscriber', true );


?>

<div class="subscribers">

<div class="title"><?php  echo count($q_subscriber).' '.__('Subscribers', 'question-answer'); ?></div>
<?php

$max_subscriber = 10;

$i = 1;
if(is_array($q_subscriber))
foreach($q_subscriber as $subscriber) {
	
	$user = get_user_by( 'ID', $subscriber );
	
	
	
	if(!empty($user->display_name))
	echo '<div title="'.$user->display_name.'" class="subscriber">'.get_avatar( $subscriber, "45" ).'</div>';
	
	if($i>=$max_subscriber){
		return;
		}
	
	
	}



?>


</div>

