<?php	


/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	
?>





<div class="wrap">

	<div id="icon-tools" class="icon32"><br></div><?php echo "<h2>".sprintf(__('%s Help', 'question-answer'), QA_PLUGIN_NAME)."</h2>";?>
		
	<div class="para-settings qa-admin-help">
    
        <ul class="tab-nav"> 
            <li nav="1" class="nav1 active"><i class="fa fa-hand-o-right"></i> <?php _e('Help & Support', 'question-answer'); ?></li>

        </ul> <!-- tab-nav end -->      
    
		<ul class="box">
        
            <li style="display: block;" class="box1 tab-box active">
            
            
				<div class="option-box">
                    <p class="option-title"><?php _e('Need Help ?', 'question-answer'); ?></p>
                    <p class="option-info"><?php _e('Feel free to contact with any issue for this plugin, Ask any question via forum', 'question-answer'); ?> <a href="<?php echo QA_PLUGIN_SUPPORT; ?>"><?php echo QA_PLUGIN_SUPPORT; ?></a><br />

                    </p>

                </div>     
            
                <div class="option-box">
                    <p class="option-title"><?php _e('FAQ','question-answer'); ?></p>
                    <p class="option-info"></p>
                    
                    
                    <div class="faq">
                    <?php
                    $class_qa_functions = new class_qa_functions();
                    $faq =  $class_qa_functions->faq();
                    
                   // echo '<ul>';
                    foreach($faq as $faq_data){
                       // echo '<li>';
                        $title = $faq_data['title'];
                        $items = $faq_data['items'];				
                        
                       // echo '<span class="group-title">'.$title.'</span>';
                        
                            echo '<ul>';
                            foreach($items as $item){
                                
                                    echo '<li class="item">';
                                    echo '<a href="'.$item['answer_url'].'"><i class="fa fa-question-circle-o"></i> '.$item['question'].'</a>';
                                
                                    
                                    echo '</li>';	
        
                            }		
                            echo '</ul>';
                    
                       // echo '</li>';
                        }
                        
                      //  echo '</ul>';
                    ?>
        
                    </div>
        
                </div>
            
            
            
            
            
            
            
            
            
            </li>
            
       </ul>
    
    </div>

</div>
