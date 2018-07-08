<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	
	$class_qa_functions = new class_qa_functions();
	$items = $class_qa_functions->qa_social_share_items();
	
	//var_dump($items);
	
	$image_url = '';
	
	echo '<div class="qa-social-share">';
	foreach( $items as $key => $item ) { ?>

		<a style="background-color:<?php echo $item['bg_color']; ?>; color:#fff" class="qa-social-single <?php echo $item['class']; ?>" href="<?php echo $item['share'] . get_the_permalink(); ?>" ><?php echo $item['icon']; ?></a>


		
	<?php }
	echo '</div>';