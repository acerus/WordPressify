<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: slug.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright    2014 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_slug {
	public static function process( $data, $params ) {
		if ( isset( $_GET['pal'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('<pre>');
			_e('Params: ');
			print_r( $params );
			_e('Data: ');
			print_r( $data );
			_e('</pre>');
		}
		$res        = new stdClass();
		$res->slug = '';
		if ( $data->text == '' ) {
			return $res;
		}

		$slug = $data->text;
		$slug = self::replace_chars( $slug, $params );

		if ( isset( $_GET['pslug'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('Alias: ' . $slug . '<br />');
		}
		$slug = wp_strip_all_tags($slug);
		/*$slug = preg_replace( '/[^a-zA-Z0-9\-\s]/', '', $slug );
		$slug = preg_replace( '/\s+/', '-', $slug );
		$slug = preg_replace( '/\-+/', '-', trim( $slug ) );*/
		$slug = sanitize_title($slug);
		$slug = strtolower( $slug );

		if ( isset( $_GET['pslug'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('Alias: ' . $slug . '<br />');
		}

		$res->slug = $slug;

		return $res;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'text' );
		$data->output = array( 'slug' );

		return $data;
	}

	public static function replace_chars( $slug, $params ) {
		$chars = self::getChars( $params );

		if ( is_array( $chars ) ) {
			foreach ( $chars as $key => $vals ) {
				$slug = str_replace( $vals, $key, $slug );
			}
		}
		if ( isset( $_GET['pal1'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('<pre>');
			_e('Chars: ');
			print_r( $chars );
			_e('</pre>');
		}

		return $slug;
	}

	public static function getChars( $params ) {
		global $ogb_chars;
		if ( ! $ogb_chars ) {
			$ogb_chars = array();
			if ( @$params->replace_chars != '' ) {
				$chars = $params->replace_chars;
			} else {
				$file  = dirname( __FILE__ ) . DS . 'chars.txt';
				$chars = file_get_contents( $file );
			}
			str_replace( "\n", '', $chars );

			$chars = explode( ";", $chars );
			for ( $i = 0; $i < count( $chars ); $i ++ ) {
				$char = explode( ":", $chars[$i] );
				if ( ! isset( $char[1] ) ) {
					continue;
				}
				$vals = explode( ",", $char[1] );
				if ( ! isset( $ogb_chars[$char[0]] ) ) {
					$ogb_chars[$char[0]] = $vals;
				} else {
					$ogb_chars[$char[0]] = array_merge( $ogb_chars[$char[0]], $vals );
				}
			}
		}

		return $ogb_chars;
	}
}