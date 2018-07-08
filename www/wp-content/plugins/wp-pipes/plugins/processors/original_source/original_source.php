<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: original_source.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright    2014 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_original_source {
	public static function process( $data, $params ) {
		if ( isset( $_GET['pos'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('<pre>');
			_e('Params: ');
			print_r( $params );
			_e('Data: ');
			print_r( $data );
			_e('</pre>'); //exit();
		}
		if(!$params->text){
			$params->text = 'Original Source';
		}
		$original_source = '<p><a href="' . $data->url . '" target="_blank">' . $params->text . '</a></p>';
		$res             = new stdClass();
		$res->html       = $data->html . "\n" . $original_source;

		return $res;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'url', 'html' );
		$data->output = array( 'html' );

		return $data;
	}
}