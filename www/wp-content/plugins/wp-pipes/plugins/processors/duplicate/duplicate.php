<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: duplicate.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright    2014 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_duplicate {
	public static function process( $data, $params ) {
		if ( isset( $_GET['p'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('<pre>');
			print_r( $params );
			print_r( $data );
			_e('</pre>');
		}

		$stop        = new stdClass();
		$stop->state = true;
		$stop->state = false;
		$stop->msg   = 'Duplicate';

		$res       = new stdClass();
		$res->stop = $stop;

		return $res;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->input  = array( 'input' );
		$data->output = array( 'result' );

		return $data;
	}
}