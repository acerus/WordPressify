<?php	


/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 



class class_qa_addons{
	
	
    public function __construct(){
		
    }
	
	

	public function addons_data($addons_data = array()){
		
		$addons_data_new = array(
							

			'question-answer-email'=>array(	'title'=>'Question Aswer - Email',
										'version'=>'1.0.0',
										'price'=>'0',
										'content'=>'Get email notification when any action occurred via Question Answer plugin.',										
										'item_link'=>'https://wordpress.org/plugins/question-answer-email',
										'thumb'=>QA_PLUGIN_URL.'assets/admin/images/addons/question-answer-email.png',							
			),	

			'import-question2answer'=>array('title'=>'Import Question2answer',
										'version'=>'1.0.0',
										'price'=>'0',
										'content'=>' Import Question and Answer from Question2answer CMS.',										
										'item_link'=>'https://wordpress.org/plugins/question-and-answer-import-question2answer/',
										'thumb'=>QA_PLUGIN_URL.'assets/admin/images/addons/import-question2answer.png',							
			),

			'dw-import'=>array(	'title'=>'Import DW Question & Answer',
										'version'=>'1.0.0',
										'price'=>'0',
										'content'=>' Import Question and Answer from DW Question & Answer plugin.',										
										'item_link'=>'https://wordpress.org/plugins/question-answer-dw-import/',
										'thumb'=>QA_PLUGIN_URL.'assets/admin/images/addons/dw-import.png',							
			),

			'import-anspress'=>array(	'title'=>'Import AnsPress - Question and answer',
										'version'=>'1.0.0',
										'price'=>'0',
										'content'=>' Import Question and Answer from AnsPress - Question and answer plugin.',										
										'item_link'=>'https://wordpress.org/plugins/question-answer-import-anspress/',
										'thumb'=>QA_PLUGIN_URL.'assets/admin/images/addons/import-anspress.png',							
			),


			'related-questions'=>array(	'title'=>'Related Questions',
										'version'=>'1.0.0',
										'price'=>'0',
										'content'=>'Automatically display related question by category and tags on single question page at bottom.',
										'item_link'=>'https://wordpress.org/plugins/question-answer-related-questions/',
										'thumb'=>QA_PLUGIN_URL.'assets/admin/images/addons/related-questions.png',
			),

		);
		
		$addons_data = array_merge($addons_data_new,$addons_data);
		
		$addons_data = apply_filters('qa_filters_addons_data', $addons_data);
		
		return $addons_data;
		
		
		}



	public function qa_addons_html(){
		
		$html = '';
		
		$addons_data = $this->addons_data();
		
		foreach($addons_data as $key=>$values){
			
			$html.= '<div class="single '.$key.'">';
			$html.= '<div class="thumb"><a href="'.$values['item_link'].'"><img src="'.$values['thumb'].'" /></a></div>';			
			$html.= '<div class="title"><a href="'.$values['item_link'].'">'.$values['title'].'</a></div>';
			$html.= '<div class="content">'.$values['content'].'</div>';						
			$html.= '<div class="meta version"><b>'.__('Version:', 'question-answer').'</b> '.$values['version'].'</div>';
			
			if($values['price']==0){
				
				$price = __('Free', 'question-answer');
				}
			else{
				$price = '$'.$values['price'];
				
				}		
			$html.= '<div class="meta price"><b>'.__('Price:', 'question-answer').'</b> '.$price.'</div>';							
			$html.= '<div class="meta download"><a href="'.$values['item_link'].'">'.__('Download', 'question-answer').'</a></div>';				
			
			
			
			$html.= '</div>';
			
		
			
			}
		
		$html.= '';		
		
		return $html;
		}







}

new class_qa_addons();




	
	
?>





<div class="wrap">

	<div id="icon-tools" class="icon32"><br></div><?php echo "<h2>".sprintf(__('%s - Addons', 'question-answer'), QA_PLUGIN_NAME)."</h2>";?>

		<div class="qa-addons">
        
			<?php
            
            $class_qa_addons = new class_qa_addons();
            
            echo $class_qa_addons->qa_addons_html();
            
            
            ?>
        
        
        </div>

</div>
