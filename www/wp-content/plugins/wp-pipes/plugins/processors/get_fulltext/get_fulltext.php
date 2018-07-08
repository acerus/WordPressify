<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: get_fulltext.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once 'lib' . DS . 'gcurl.php';

class WPPipesPro_get_fulltext extends ogb_get_CURL {
	public static function setStop( &$data, $msg = 'unknow', $state = true ) {
		$stop        = new stdClass();
		$stop->state = $state;
		$stop->msg   = $msg;
		$data->stop  = $stop;
	}

	/**
	 * Get Input & Out fields for the processor
	 *
	 * @param bool $params
	 *
	 * @return stdClass
	 */
	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'url', 'html' );
		$data->output = array( 'full_html', 'fulltext' );

		return $data;
	}

	public static function check_params_df( $params ) {
		$df                     = new stdclass();
		$df->input              = 0;
		$df->curl               = 1;
		$df->clear_tags         = 'script,style';
		$df->entities           = 0;
		$df->clear_space        = 1;
		$df->auto_fulltext      = 1;
		$df->code               = '';
		$df->origin_Site        = '';
		$df->atag               = 0;
		$df->clear_attribute    = 'id,class,style';
		$df->clear_html_comment = 1;

		foreach ( $df as $key => $val ) {
			if ( ! isset( $params->$key ) ) {
				@$params->$key = $val;
			}
		}

		return $params;
	}

	public static function process( $data, $params ) {
		$list_active_plg = get_option('active_plugins');
		$html_parser_plg_path = WP_PLUGIN_DIR . DS . 'pipes-processor-htmlparser' . DS . 'pipes-processor-htmlparser.php';

		$params = self::check_params_df( $params );
		if ( isset( $_GET['php1'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('<pre>');
			_e('Params: ');
			print_r( $params );
			_e('Data: ');
			print_r( $data );
			_e('</pre>'); //exit();
		}
		$url = str_replace( '&amp;', '&', @$data->url );
		$res = new stdclass();
		if ( $params->input == 0 && $url != '' ) {
			if ( isset( $_GET['curl'] ) ) {
				_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
				_e('- param: ');
				var_dump( $params->curl );
				_e('- GET: ');
				var_dump( $_GET['curl'] );
				$params->curl = (int) sanitize_text_field($_GET['curl']);
				$times        = array();
				$times[]      = date( 'Y-m-d H:i:s' ) . ' - ' . microtime();
			}
			if ( ! self::check_curl_init() ) {
				self::setStop( $res, 'CURL not available' );

				return $res;
			}

			$html = self::getCURL( $url, $params );

			if ( isset( $_GET['curl'] ) ) {
				_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
				$times[] = date( 'Y-m-d H:i:s' ) . ' - ' . microtime();
				_e('<pre>Time get CURL:');
				print_r( $times );
				_e('</pre>');
			}
			if ( $html[0] == 200 ) {
				$html = $html[1];
			} else {
				self::setStop( $res, "can't get content source.<br/>{$url}<br/> http_code: {$html[0]}" );

				return $res;
			}
		} else {
			$html = $data->html;
		}
		if ( @$params->charset != '' && @$params->charset != 'UTF-8' ) {
			$html = mb_convert_encoding( $html, "utf-8", $params->charset );
		}
		$fullhtml = $html;
		if ( isset( $_GET['php2'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('URL: ' . $url);
			_e($html);
			exit();
		}
		$tags = $params->clear_tags;
		if ( $tags != '' ) {
			$html = self::clear_tags( $html, $tags );
		}
		if ( isset( $_GET['php'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e($html);
			exit();
		}
		if ( $params->clear_space == 1 ) {
			$html = self::clear_space( $html );
		}
		$res->fulltext = $html;
		if ( $params->code != '' ) {
			$params->auto_fulltext = 0;
		}
		if ( $params->auto_fulltext == 1 ) {
			self::get_auto_fulltext( $res, $html );
			if ( isset( $res->stop ) ) {
				return $res;
			}
			$html = $res->fulltext;
		}
		if ( $params->code != '' ) {
			if(in_array('pipes-processor-htmlparser/pipes-processor-htmlparser.php', $list_active_plg) && is_file($html_parser_plg_path)){
				include_once($html_parser_plg_path);
				$params->code = stripslashes($params->code);
				$temp_params = $params;
				$temp_params->input = 1;
				WPPipesPro_htmlparser::run_parser_code($res, $html, $temp_params);
			}else{
				self::setStop( $res, "If you want to use Parser Code, you have to own HTML Parser Processor at http://thimpress.com/shop/ because it has not been supported with Get Fulltext Processor any more." );

				return $res;
			}
			if ( isset( $res->stop ) ) {
				return $res;
			}
		}
		if ( isset( $_GET['php4'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e($res->fulltext);
			exit();
		}
		$res->fulltext = self::process_atag( $res->fulltext, $params, $url );
		if ( $params->clear_attribute != '' ) {
			$html          = self::clear_attribs( $res->fulltext, $params->clear_attribute );
			$res->fulltext = $html[0];
		}
		if ( isset( $_GET['php41'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e($res->fulltext);
			exit();
		}

		/*Remove comment html*/
		if ( $params->clear_html_comment ) {
			$res->fulltext = preg_replace( '/<!--[\s\S]*?-->/', '', $res->fulltext );
		}

		if ( isset( $_GET['comment'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e("orin:");
			_e("<pre>");
			print_r( $res->fulltext );
			_e("</pre>");
		}

		$res->full_html = $fullhtml;
		$count_word     = self::count_words( $res->fulltext );
		if ( $params->minimum_word > 0 && $count_word < $params->minimum_word ) {
			self::setStop( $res, "The amount of words less than " . $params->minimum_word );

			return $res;
		}
		if ( $params->maximum_word > 0 && $count_word > $params->maximum_word ) {
			self::setStop( $res, "The amount of words greater than " . $params->maximum_word );

			return $res;
		}

		return $res;
	}

	public static function count_words( $text ) {
		$text  = strip_tags( $text );
		$text  = str_replace( array( '.', ',', '!', '?' ), '', $text );
		$text  = preg_replace( '/\n+|\r+|\t+/i', ' ', $text );
		$words = explode( " ", $text );

		return count( $words );
	}

	public static function process_atag( $html, $params, $url = '' ) {
		if ( $params->atag == 1 ) {
			$html = self::strip_tag( $html, 'a' );

			return $html;
		}
		if ( $params->origin_site == '' ) {
			$domain      = parse_url( $url, PHP_URL_HOST );
			$origin_site = 'http://' . $domain;
		} else {
			$origin_site = $params->origin_site;
		}
		$html = preg_replace( '/ href\s*=\s*"\//', " href=\"{$origin_site}/", $html );
		if ( $params->atag == 2 ) {
			$html = str_replace( '<a ', '<a target="_self" ', $html );
		}else{
			$html = str_replace( '<a ', '<a target="_blank" ', $html );
		}

		return $html;
	}

	public static function get_auto_fulltext( &$res, $html ) {
		require_once 'readability.php';
		$html = obgrabArticle( $html, true );
		if ( isset( $html[1] ) ) {
			self::setStop( $res, $html[1] );
		} else {
			$res->fulltext = $html[0];
		}

		return $res;
	}

	public static function clear_space( $html ) {
		$html = str_replace( '&nbsp;', ' ', $html );
		$html = str_replace( "\n", '', $html );
		$old_html = $html;
		$html = preg_replace( "/\s+/iu", " ", $old_html );
		if ( ! $html ) {
			$html = preg_replace( "/\s+/i", " ", $old_html );
		}
		$html = str_replace( "> <", '><', $html );
		$html = str_replace( "<div", "\n<div", $html );
		$html = str_replace( "<p", "\n<p", $html );
		$html = str_replace( "<br", "\n<br", $html );
		$html = str_replace( "<h", "\n<h", $html );

		return $html;
	}

	public static function html_decode( $html ) {
		return html_entity_decode( $html );
	}

	public static function html_encode( $html ) {
		return htmlentities( $html );
	}

	public static function addhttp( $url ) {
		if ( ! preg_match( "~^(?:f|ht)tps?://~i", $url ) ) {
			$url = "http://" . $url;
		}

		return $url;
	}
}