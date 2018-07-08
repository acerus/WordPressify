<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


?>
	
	
<div class="qa-breadcrumb">
	
        <?php
		
		do_action('qa_action_breadcrumb_before');
		

		$total_found = qa_breadcrumb_total_count();

		if($total_found>0){
			$pending_class= 'pending';
			if($total_found>10 && $total_found<20){
				
				$total_found = '10+';
				}

        }

		else{
			$pending_class= '';
			}
		
		?>


    <div class="menu-box">
    	<i class="fa fa-bars"></i>
		<?php
        if(is_user_logged_in()){
			?>
            <i class="bubble <?php echo $pending_class; ?>"><?php echo $total_found; ?></i>    
            <?php
			
			}
		?>
        
        <div class="menu-box-hover">
        <?php do_action('qa_breadcrumb_menu'); ?>
        </div>
    </div>

    <?php

    $qa_ask_button_bg_color = get_option('qa_ask_button_bg_color');
    $qa_ask_button_text_color = get_option('qa_ask_button_text_color');

    $qa_page_question_post = get_option('qa_page_question_post');
    $qa_page_question_post_url = get_permalink($qa_page_question_post);

    if(!empty($qa_page_question_post)){

	    echo '<a class="add-question" href="'.$qa_page_question_post_url.'">'.__('Ask question', 'question-answer').'</a>';

	    global $qa_css;

	    $qa_ask_button_bg_color = get_option( 'qa_ask_button_bg_color' );
	    if( empty( $qa_ask_button_bg_color ) ) $qa_ask_button_bg_color = '';

	    $qa_ask_button_text_color = get_option( 'qa_ask_button_text_color' );
	    if( empty( $qa_ask_button_text_color ) ) $qa_ask_button_text_color = '';

	    $qa_css .= ".qa-breadcrumb .add-question{ color: $qa_ask_button_text_color !important; background: $qa_ask_button_bg_color; }";


    }

    ?>
    <div class="links">
    <?php do_action('qa_breadcrumb_links'); ?>
    </div>

	
</div>