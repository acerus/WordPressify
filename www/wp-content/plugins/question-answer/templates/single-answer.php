<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	get_header();
	do_action('qa_action_before_single_answer');

	while ( have_posts() ) : the_post(); 
	?>
	<div id="answer-<?php the_ID(); ?>" <?php post_class('single-answer entry-content'); ?>>
        
    <?php do_action('qa_action_single_answer_main'); ?>

    </div>
	<?php
	endwhile;
		
    do_action('qa_action_after_single_answer');
	
	get_sidebar();	
	get_footer();