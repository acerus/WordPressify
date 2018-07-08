<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

if ( !class_exists('class_pickform') ) {

class class_pickform{
	
	public function __construct(){

		
		

		}




    public function kses($content){

        $filter = apply_filters( 'pickform_filter_kses', array(
            'a'             => array(
                'href'  => array(),
                'title' => array()
            ),
            'br'            => array(),
            'em'            => array(),
            'strong'        => array(),
            'code'          => array(
                'class' => array()
            ),
            'blockquote'    => array(),
            'quote'         => array(),
            'span'          => array(
                'style' 	=> array()
            ),
            'img'           => array(
                'src'    	=> array(),
                'alt'    	=> array(),
                'width'  	=> array(),
                'height' 	=> array(),
                'style'  	=> array()
            ),
            'ul'            => array(),
            'li'            => array(),
            'ol'            => array(),
            'pre'           => array()
        ));

        return wp_kses( $content, $filter );

    }



	public function validations($field_data, $value){

		if(!empty($field_data['required'])){
			$required = $field_data['required'];
			}
		else{
			$required = 'no';
			}


		$result = '';
		
		if($required=='yes') {
			if( !empty($value) ) { }
			else { return 'missing'; }
		}
		else { }	
		

	}
		
		
	public function sanitizations($input_value, $input_type){
		
		if($input_type=='text' || $input_type=='hidden' || $input_type=='date' || $input_type=='datetime' || $input_type=='number'|| $input_type=='range' ){
			return sanitize_text_field($input_value);
			}
			
		elseif($input_type=='text_multi'){
			return sanitize_text_multi($input_value);

			}			
			
		elseif($input_type=='email'){
			return sanitize_email($input_value);

			}
		elseif($input_type=='file'){
			return esc_url($input_value);
			}			
		elseif($input_type=='select_multi'){
			return stripslashes_deep($input_value);
			}				
		elseif($input_type=='textarea'){
			return $this->kses($input_value);
			}			
			
		elseif($input_type=='radio'){
			return stripslashes_deep($input_value);
			}			
			
		elseif($input_type=='select'){
			return stripslashes_deep($input_value);
			}			
			
		elseif($input_type=='checkbox'){
			return stripslashes_deep($input_value);
			}			
		
		elseif($input_type=='wp_editor'){


            return $this->kses($input_value);




			}		
		elseif($input_type=='recaptcha'){
			return sanitize_text_field($input_value);
			}			
		else{
			return sanitize_text_field($input_value);
			
			}
		
		
		
		
		
		}
		
		
		



	public function field_set($field_data){
		
			if(isset($field_data['input_type'])){
					$field_type = $field_data['input_type'];
				}
			else{
					$field_type = '';
				}		

			//var_dump($field_type);
			$field_html = array();
			
			
			if($field_type=='text')
			$field_html['text'] = array(

									'html'=>$this->field_text( $field_data ),														
		
									);
												
			if($field_type=='text_multi')
			$field_html['text_multi'] = array(

									'html'=>$this->field_text_multi( $field_data ),														
		
									);			
			
			if($field_type=='hidden')
			$field_html['hidden'] = array(

									'html'=>$this->field_hidden( $field_data ),														
		
									);
												
			if($field_type=='date')
			$field_html['date'] = array(

									'html'=>$this->field_date( $field_data ),														
		
									);	
									
			if($field_type=='datetime')
			$field_html['datetime'] = array(

									'html'=>$this->field_datetime( $field_data ),														
		
									);									
									
/*

			if($field_type=='select')						
			$field_html['datetime'] = array(

									'html'=>$this->field_select( $field_data ),														
		
									);

*/	
																	
			if($field_type=='email')						
			$field_html['email'] = array(

									'html'=>$this->field_email( $field_data ),														
		
									);	
																	
			if($field_type=='file')						
			$field_html['file'] = array(

									'html'=>$this->field_file( $field_data ),														
		
									);	
																		
			if($field_type=='number')								
			$field_html['number'] = array(

									'html'=>$this->field_number( $field_data ),														
		
									);	
											
			if($field_type=='password')
			$field_html['password'] = array(

									'html'=>$this->field_password( $field_data ),														
		
									);			
			
			if($field_type=='range')
			$field_html['range'] = array(

									'html'=>$this->field_range( $field_data ),														
		
									);			
			
			if($field_type=='textarea')
			$field_html['textarea'] = array(

									'html'=>$this->field_textarea( $field_data ),														
		
									);	
			
			if($field_type=='select')
			$field_html['select'] = array(

									'html'=>$this->field_select( $field_data ),														
		
									);
									
			if($field_type=='select_multi')							
			$field_html['select_multi'] = array(

									'html'=>$this->field_select_multi( $field_data ),														
		
									);									
			
			if($field_type=='checkbox')				
			$field_html['checkbox'] = array(

									'html'=>$this->field_checkbox( $field_data ),														
		
									);
									
			if($field_type=='radio')				
			$field_html['radio'] = array(

									'html'=>$this->field_radio( $field_data ),														
		
									);									
	
			if($field_type=='wp_editor')	
			$field_html['wp_editor'] = array(

									'html'=>$this->field_wp_editor( $field_data ),														
		
									);
																		
/*



*/
			if($field_type=='recaptcha')						
			$field_html['recaptcha'] = array(

									'html'=>$this->field_recaptcha( $field_data ),														
		
									);																		
			
			$field_html = apply_filters('pickform_filter_input_field_html', $field_html, $field_data);
			
			//var_dump($field_html[$field_type]);
			//return $field_html[$field_type]['html'];
			
			if(isset($field_html[$field_type]['html']))
			return $field_html[$field_type]['html'];			
			
		
		/*
            switch ($field_type) {
				

				
                case 'text':
                    $this->field_text( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
					//echo 'Hello 3';
                    break;
					
                case 'text_multi':
                    $this->field_text_multi( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
					//echo 'Hello 3';
                    break;					
					
					
                case 'hidden':
                    $this->field_hidden( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;				
	
                case 'date':
                    $this->field_date( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;					

                case 'datetime':
                    $this->field_datetime( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;
					
                case 'email':
                    $this->field_email( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;				

                case 'file':
                    $this->field_file( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;				

                case 'number':
                    $this->field_number( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;			

                case 'password':
                    $this->field_password( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;			

                case 'range':
                    $this->field_range( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;	

                case 'textarea':
                    $this->field_textarea( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;					
							
                case 'select':
                    $this->field_select( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;		
		
                case 'select_multi':
                    $this->field_select_multi( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;			
		
                case 'checkbox':
                    $this->field_checkbox( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;
					
                case 'radio':
                    $this->field_radio( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;

                case 'wp_editor':
                    $this->field_wp_editor( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;					
							
                case 'recaptcha':
                    $this->field_recaptcha( $field_data );
                    //$this->conditional_logic( $form_field, $form_id );
                    break;			

                default:

                    break;
					
			
				
				
				
				}		
		
		*/

		
		
		
		
		}


	public function field_text($field_data){
		
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
			
			
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}				
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}				
			
			
			
			$input_type = $field_data['input_type'];
			
			
			$field_values = $field_data['input_values'];
			
			if(!empty($field_data['attributes'])){
				$field_attributes = $field_data['attributes'];
				}
						
			
			
			//var_dump($field_id);
			//var_dump($input_type);
			
			
			
			//$required = $field_attributes['required'];			

				
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<input id="'.$field_id.'"  type="text" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';			
								
			return $html;
		
		}
		
		
		
	public function field_text_multi($field_data){
		
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
									
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	
			
					
			$field_values = $field_data['input_values'];

			//$field_attributes = $field_data['attributes'];			
			
			//$required = $field_attributes['required'];			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
				
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
				
			$html.= '<div class="repatble">';
			$html.= '<div class="items">';			
				
	
            if(!empty($field_values)){
                if(is_array($field_values)){
                    
                    foreach($field_values as $key=>$value){
                        
						$html.= '<div class="single">';
						$html.= '<input type="text" '.$required.' name="'.$field_id.'['.$key.']'.'" value="'.$field_values[$key].'" />';						
						$html.= '<input class="remove-field" type="button" value="'.__('Remove','question-answer').'" />';
						$html.= '</div>';
    
                        }
    
                    
                    }
                else{
                    
					
						$html.= '<div class="single">';
						$html.= '<input type="text" '.$required.' name="'.$field_id.'[]'.'" value="'.$field_values.'" />';						
						$html.= '<input class="remove-field" type="button" value="'.__('Remove','question-answer').'" />';
						$html.= '</div>';

    
                    }
                }
            else{
                
					$html.= '<div class="single">';
					$html.= '<input type="text" '.$required.' name="'.$field_id.'[]'.'" value="'.$field_values.'" />';						
					$html.= '<input class="remove-field" type="button" value="'.__('Remove','question-answer').'" />';
					$html.= '</div>';

    
                }
    
            $html.= '</div>';
            $html.= '<input class="add-field" option-id="'.$field_id.'" type="button" value="'.__('Add more','question-answer').'" />';
            $html.= '</div>';			
			
			return $html;

		}		
		
		
		
		
		
		
		
		
		
		

	public function field_hidden($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];		
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
				
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}		
			
					
			$field_values = $field_data['input_values'];
		
			$html = '';
			$html.= '<input id="'.$field_id.'"  type="hidden"  name="'.$field_id.'" value="'.$field_values.'" />';	
		
			return $html;
		
		}



		
	public function field_date($field_data){
		
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];	
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
				
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
					
					
					
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
		
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';

			$html.= '<input '.$required.' id="'.$field_id.'" type="date" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';	
			
			return $html;

		
		}
		
	public function field_datetime($field_data){
		
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];	
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
				
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	
					
				
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
				
				
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
		
		
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';

			//var_dump($field_values);

			$html.= '<input '.$required.' id="'.$field_id.'" type="datetime" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';	
		
			return $html;
		
		}		
		
	public function field_email($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];		
					
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
			
						
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];		
		
		
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<input '.$required.' id="'.$field_id.'" type="email" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';		
		 
		 	return $html;
		
		}		
		
	public function field_file($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
						
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	
			
					
					
						
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];	
		
			$html = '';
			//$field_values = array(5264,5263);
			
			//var_dump($field_values);
			
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			
			
			$html.= '
				   <div id="plupload-upload-ui-'.$field_id.'" class="plupload-upload-ui hide-if-no-js">
					 <div id="drag-drop-area-'.$field_id.'" class="drag-drop-area">
					   <div class="drag-drop-inside">';
	
						
			$html.= '<p>'.__("Drop files here",'question-answer').'</p>
						<p class="drag-drop-buttons"><input id="plupload-browse-'.$field_id.'" type="button" value="'.__("Select Files",'question-answer').'" class="button" /></p>
					  </div>
					 </div>
				  </div>

			';			
			
			
  $plupload_init = array(
    'runtimes'            => 'html5,silverlight,flash,html4',
    'browse_button'       => 'plupload-browse-'.$field_id.'',
	//'multi_selection'	  =>false,
    'container'           => 'plupload-upload-ui-'.$field_id.'',
    'drop_element'        => 'drag-drop-area-'.$field_id.'',
    'file_data_name'      => 'async-upload',
    'multiple_queues'     => true,
    'max_file_size'       => wp_max_upload_size().'b',
    'url'                 => admin_url('admin-ajax.php'),
    //'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
    //'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
    'filters'             => array(array('title' => __('Allowed Files','question-answer'), 'extensions' => 'gif,png,jpg,jpeg')),
    'multipart'           => true,
    'urlstream_upload'    => true,

    // additional post data to send to our ajax hook
    'multipart_params'    => array(
      '_ajax_nonce' => wp_create_nonce('photo-upload'),
      'action'      => 'photo_gallery_upload',            // the ajax action name
    ),
  );

  // we should probably not apply this filter, plugins may expect wp's media uploader...
  $plupload_init = apply_filters('plupload_init', $plupload_init);
			
			
	$html.= '
			
		 <script>
		
			jQuery(document).ready(function($){
		
			  // create the uploader and pass the config from above
			  var uploader_'.$field_id.' = new plupload.Uploader('.json_encode($plupload_init).');
		
			  // checks if browser supports drag and drop upload, makes some css adjustments if necessary
			  uploader_'.$field_id.'.bind("Init", function(up){
				var uploaddiv = $("#plupload-upload-ui-'.$field_id.'");
		
				if(up.features.dragdrop){
				  uploaddiv.addClass("drag-drop");
					$("#drag-drop-area-'.$field_id.'")
					  .bind("dragover.wp-uploader", function(){ uploaddiv.addClass("drag-over"); })
					  .bind("dragleave.wp-uploader, drop.wp-uploader", function(){ uploaddiv.removeClass("drag-over"); });
		
				}else{
				  uploaddiv.removeClass("drag-drop");
				  $("#drag-drop-area-'.$field_id.'").unbind(".wp-uploader");
				}
			  });
		
			  uploader_'.$field_id.'.init();
		
			  // a file was added in the queue
			  uploader_'.$field_id.'.bind("FilesAdded", function(up, files){
				var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
		
				plupload.each(files, function(file){
				  if (max > hundredmb && file.size > hundredmb && up.runtime != "html5"){
					// file size error?
					console.log("Error...");
				  }else{
		
					// a file was added, you may want to update your DOM here...
					//console.log(file);
					//alert(file);
					//
					
				  }
				});
		
				up.refresh();
				up.start();
			  });
		
			  // a file was uploaded 
			  uploader_'.$field_id.'.bind("FileUploaded", function(up, file, response) {
		
				// this is your ajax response, update the DOM with it or something...
				//console.log(response);
				
				
				var result = $.parseJSON(response.response);
				
				
		
				var attach_url = result.html.attach_url;
				var attach_id = result.html.attach_id;
				var attach_title = result.html.attach_title;
				
				var html_new = "<div class=item attach_id="+attach_id+"><img src="+attach_url+" /><span attach_id="+attach_id+" class=delete>Delete</span><input  type=hidden name='.$field_id.'[] value="+attach_id+" /></div>";
				
				$("#plupload-upload-ui-'.$field_id.' .drag-drop-inside").prepend(html_new); 
				 
			  });
		
			});   
		
		  </script>
			
			';		
			
			

			
			
			
			
			
			
			/*

			
			$html.= '<div class="file">';
			$html.= '<div id="pickfiles-'.$field_id.'" class="add-file">Select File</div>';			
			$html.= '<div id="file-list" class="file-list"></div>';
			$html.= '<div id="console" class="console"></div>';						
			
			$html.= '</div>';					
			
			$html.= "<script>
			jQuery(document).ready(function($){
				
				var uploader_".$field_id." = new plupload.Uploader({
					runtimes : 'html5,flash,silverlight,html4',
					browse_button : 'pickfiles-".$field_id."', // you can pass an id...
					container: document.getElementById('file-list'), // ... or DOM Element itself
					url : '".admin_url('admin-ajax.php')."',
					//flash_swf_url : '../js/Moxie.swf',
					//silverlight_xap_url : '../js/Moxie.xap',
					
					filters : {
						max_file_size : '2mb',
						mime_types: [
							{title : 'Image files', extensions : 'jpg,gif,png'},
							
						]
					},
				
					init: {
						PostInit: function() {
							document.getElementById('filelist').innerHTML = '';
				
							document.getElementById('uploadfiles').onclick = function() {
								uploader.start();
								return false;
							};
						},
				
						Error: function(up, err) {
							document.getElementById('console').appendChild(document.createTextNode('nError #' + err.code + ': ' + err.message));
						}
					}
				});
				
				uploader_".$field_id.".init();
			
			});	</script>";			
			
			
			
			
			*/
			
			
			
			
			//$html.= '<input id="'.$field_id.'" type="file" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';		
		
			return $html;

		
		}		
		
		
	public function field_number($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];	
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
					
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}	
						
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
		
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<input '.$required.' id="'.$field_id.'" type="number" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';			
			return $html;

		
		}			
		
		
		
	public function field_password($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
					
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}
			
						
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
		
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<input id="'.$field_id.'" type="password" placeholder="'.$field_placeholder.'" name="'.$field_id.'" value="'.$field_values.'" />';		
		
			return $html;		
		}		
		
	public function field_range($field_data){
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
					
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
			
					
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$field_args = $field_data['input_args'];
			
			$min = $field_args['min'];
			$max = $field_args['max'];	
			$step = $field_args['step'];								
			
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<input '.$required.' id="'.$field_id.'" type="range" min="'.$min.'" max="'.$max.'" step="'.$step.'" name="'.$field_id.'" value="'.$field_values.'" />';			
			
			
			return $html;		
		}		
			
		

	public function field_textarea($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];	
				
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
				
						
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
		
		
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<textarea '.$required.' id="'.$field_id.'" name="'.$field_id.'" placeholder="'.$field_placeholder.'"  >'.$field_values.'</textarea>';		
			
			return $html;
			
		}
		
		
	public function field_select($field_data){
		
			//var_dump($field_data);
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];		
				
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}		
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}	
					
					
			$input_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
			
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<select '.$required.' id="'.$field_id.'" name="'.$field_id.'" >';			
		
			//$html.= '<option value="">Please select</option>';
			
        	if(!empty($input_args))
			foreach($input_args as $input_args_key => $input_args_value){
				
				


				if($input_args_key== $input_values){
					$selected = 'selected';
					
					//var_dump('Hello');
					
					}
				else{
					$selected = '';
					}
					
					
					
					
					
				$html.= '<option '.$selected.' value="'.$input_args_key.'">'.$input_args_value.'</option>';
	
	
				}
			
			$html.= '</select>';
			
			return $html;
		
		}
		
		
		
	public function field_select_multi($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];			
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
					
						
			$input_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
			
			
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<select '.$required.' multiple="multiple" id="'.$field_id.'" name="'.$field_id.'[]" >';			

        	if(!empty($input_args))
			foreach($input_args as $input_args_key => $input_args_value){
				
				if(in_array($input_args_key, $input_values)){
					$selected = 'selected';
					
					
					}
				else{
					$selected = '';
					}
				
				$html.= '<option '.$selected.' value="'.$input_args_key.'">'.$input_args_value.'</option>';		
	
				}
			
			$html.= '</select>';		

			return $html;
		}		
		
		
		
		
		
		
		
	public function field_radio($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];	
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];			
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}
			
			if(!empty($field_data['required']) && $field_data['required']=='yes'){
				$required = 'required';
				}	
			else{
				$required = '';
				}
			
					
			$input_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];		


			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';



			if(!empty($input_args))
			foreach($input_args as $input_args_key => $input_args_values){
	

				//var_dump($input_values);
				if(($input_args_key== $input_values)){
					$checked = 'checked';
					}
				else{
					$checked = '';
					}
				
				$html.= '<label>';
				$html.= '<input '.$required.' '.$checked.' class="'.$field_id.'"  type="radio" name="'.$field_id.'" value="'.$input_args_key.'" />'.$input_args_values;
				$html.= '</label>';						
	
				
				}
			
			return $html;

		}		
		
		
	public function field_checkbox($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];				
			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}
			
						
			$input_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
			
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';

			if(!empty($input_args))
			foreach($input_args as $input_args_key => $input_args_values){
	
				//var_dump($input_values);
				if(in_array($input_args_key, $input_values)){
					$checked = 'checked';
					}
				else{
					$checked = '';
					}
				
				$html.= '<label>';
				$html.= '<input '.$checked.' id="'.$field_id.'"  type="checkbox" name="'.$field_id.'[]" value="'.$input_args_key.'" />'.$input_args_values;			
				$html.= '</label>';			
	
				
				}
			
			
			return $html;
			
		}
		
		

	public function field_wp_editor($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];				

			if(!empty($field_data['placeholder'])){
				$field_placeholder = $field_data['placeholder'];
				}	
			else{
				$field_placeholder = '';
				}	

			$input_type = $field_data['input_type'];		
			
							
			$field_values = $field_data['input_values'];
			
			if(isset($field_data['input_args']))
			$input_args = $field_data['input_args'];
			
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
				
			//var_dump($input_type);

			ob_start();
			wp_editor( stripslashes($field_values), $field_id, $settings = array('textarea_name'=>$field_id, 'media_buttons'=>false,'wpautop'=>true,'editor_height'=>'200px', ) );				
			$editor_contents = ob_get_clean();


			$html.= $editor_contents;

			return $html;
		
		}


	public function field_recaptcha($field_data){
		
			$field_id = $field_data['meta_key'];	
			$field_css_class = $field_data['css_class'];
			$field_title = $field_data['title'];
			$field_details = $field_data['option_details'];		
				
			//$field_placeholder = $field_data['placeholder'];
			$input_type = $field_data['input_type'];	
			
				
			$field_values = $field_data['input_values'];
			
			if(!empty($field_data['input_args']))
			$input_args = $field_data['input_args'];
			
			
			$html = '';
			$html.= '<div class="title">'.$field_title.'</div>';				
			$html.= '<div class="details">'.$field_details.'</div>';
			$html.= '<script src="https://www.google.com/recaptcha/api.js"></script>';			
			$html.= '<div class="g-recaptcha" data-sitekey="'.$field_values.'"></div>';				
			
			return $html;
		
		}



		
	
	}
	
	//new class_question_bm_functions();
	
	
}