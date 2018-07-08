<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


global $current_user;



    $question_id = get_the_id();


	do_action('qa_action_before_single_question');


	if( isset( $_GET['question_edit'] ) ) :

		do_action( 'qa_action_question_edit' );


   elseif(isset($_GET['answer_edit'])):

       do_action('qa_action_answer_edit');


   else:
       ?>
       <div itemscope itemtype="http://schema.org/Question" id="question-<?php echo $question_id;  ?>" <?php post_class('single-question entry-content'); ?>>
        <?php


       do_action('qa_action_single_question_main');

       ?>
       </div>
       <?php

   endif;

       ?>


	<?php do_action('qa_action_after_single_question'); ?>

