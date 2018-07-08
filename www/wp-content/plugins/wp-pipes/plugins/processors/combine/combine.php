<?php
/**
 * @package		obGrabber - Joomla! Anything Grabber
 * @version		$Id: original_source.php 61 2013-12-14 01:19:15Z thongta $
 * @author		Kha Nguyen - foobla.com
 * @copyright	(c) 2007-2012 foobla.com. All rights reserved.
 * @license		GNU/GPL, see LICENSE
 */
defined('_JEXEC') or die( 'Restricted access' );
class WPPipesPro_combine {
	public static function process($data, $params) {
		if (isset($_GET['combine'])){
			_e('<br /><br /><i><b>File</b> '.__FILE__.' <b>Line</b> '.__LINE__."</i><br />\n");
			_e('<pre>');
			_e('Params: ');
			print_r($params);
			_e('Data: ');
			print_r($data);
			_e('</pre>');	//exit();
		}

		$res	= new stdClass();
		if( $params->combine != '' ) {
			$params->combine = stripcslashes( $params->combine );
			$combine = str_replace( '\"', '"', $params->combine );
			$combine = str_replace( "\'", "'", $combine );
			$data_noneed_oe = $data->no_need['oe'];
			$data_noneed_op = $data->no_need['op'];
			preg_match_all('/(?<={).*?(?=})/i', $combine, $matches );
			$inputs = array();
			if(is_array($matches[0]) && count($matches[0])> 0){
				foreach($matches[0] as $key=>$value){
					$value = str_replace('[so]', '[oe]', $value);
					$seperate_array = explode(' ',$value);
					$attribute = $seperate_array[1];
					if($seperate_array[0] == '[oe]'){
						$inputs[$matches[0][$key]] = $data_noneed_oe->$attribute;
					}else{
						preg_match('/(?<=\[).*?(?=\])/i', $seperate_array[0], $result);
						$processor = $data_noneed_op[ $result[0] ];
						if(isset($processor->$attribute)){
							$inputs[$matches[0][$key]] = $data_noneed_op[$result[0]]->$attribute;
						}
					}
				}
			}

			foreach($inputs as $key_ip=>$new_value){
				$combine = str_replace('{'.$key_ip.'}',$new_value,$combine);
			}
			//$static_value	= '<p>'.$params->static_value.'</p>';
			//echo '<pre>';print_r($params->combine);die;
			$html	= $combine;
		}else{
			$html	= '';
		}
		
		$res->html	= $html;
		return $res;
	}
	public static function getDataFields($params=false){
		$data	= new stdClass();
		$data->input	= array('');
		$data->output	= array('html');
		return $data;
	}
}