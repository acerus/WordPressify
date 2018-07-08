<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

$qa_css = '';

class class_qa_dynamic_css { 
	
	protected $global_css;
	
	public function __construct(){
	
		global $qa_css;
		$this->global_css = &$qa_css;
		
		add_action('wp_footer', array( $this, 'qa_dynamic_css_loading' ) );

	}
	
	public function qa_dynamic_css_loading() {
		
		echo '<style>'.$this->global_css.'</style>';
	}
	
	
} new class_qa_dynamic_css();