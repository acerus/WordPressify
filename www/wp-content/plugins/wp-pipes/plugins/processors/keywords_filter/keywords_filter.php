<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: keywords_filter.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_keywords_filter {
	public static function process( $data, $params ) {
		if ( isset( $_GET['pkey'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('<pre>');
			_e('Params: ');
			print_r( $params );
			_e('Data: ');
			print_r( $data );
			_e('</pre>');
		}
		$valid = self::check( $data->html, $params->keywords );
		$stop  = new stdClass();
		if ( $valid ) {
			$stop->state = false;
			$stop->msg   = '';
		} else {
			$stop->state = true;
			$stop->msg   = 'keywords invalid';
		}
		$data->stop = $stop;

		return $data;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'html' );
		$data->output = array( 'html' );

		return $data;
	}

	public static function parseKeyword( $keywords ) {
		$sentences1 = null;
		$sentences  = null;
		$keywords   = str_replace( "\\", "", $keywords );
		// match with +"[word]" or -"[word]" or "[word]"
		preg_match_all( '#(\+?|\-?)"(.*)"#iU', $keywords, $sentences1 );
		// match with +'[word]' or -'[word]' or '[word]'
		preg_match_all( '#(\+?|\-?)\'(.*)\'#iU', $keywords, $sentences );

		// replace +"[word]" or -"[word]" or "[word]" by ""
		$tmp = preg_replace( '#(\+?|\-?)"(.*)"#iU', '', $keywords );
		// replace +'[word]' or -'[word]' or '[word]' by ''
		$tmp = preg_replace( '#(\+?|\-?)\'(.*)\'#iU', '', $tmp );

		// merge all +"[word]" or -"[word]" or "[word] with all +'[word]' or -'[word]' or '[word]'
		foreach ( $sentences1 as $key => $sen ) {
			$sentences[$key] = array_merge( $sentences[$key], $sen );
		}

		$tmp   = str_replace( "+", " +", $tmp );
		$tmp   = str_replace( "-", " -", $tmp );
		$words = explode( ' ', $tmp );
		foreach ( $words as $key => $word ) {
			if ( ! $word ) {
				continue;
			} // skip word is null
			$sign = substr( $word, 0, 1 );
			$pos  = 1;
			if ( $sign != '+' && $sign != '-' ) {
				$sign = '';
				$pos  = 0;
			}
			$sentences[0][] = $word; // word
			$sentences[1][] = $sign; // + or - or none
			$sentences[2][] = substr( $word, $pos ); // word with prefix + or - or none
		}

		return $sentences;
	}

	public static function trimSpace( $string ) {
		$string = preg_replace( '/\s+/', ' ', $string );

		return $string;
	}

	public static function check( $content, $keywords ) {
		//$keywords	= 'Ontario "Skills Required"';
		$aaa      = $keywords;
		$keywords = self::parseKeyword( $keywords );
		if ( count( $keywords[0] ) < 1 ) {
			return true;
		}
		$content = strip_tags( $content );
		$must    = 0;
		$tmust   = 0;
		$while   = 0;
		$twhile  = 0;
		$black   = 0;
		$tblack  = 0;
		$kExists = array();
		foreach ( $keywords[1] as $key => $value ) {
			if ( $value == '-' ) {
				$tblack ++;
			} elseif ( $value == '+' ) {
				$tmust ++;
			} else {
				$twhile ++;
			}

			$word   = self::trimSpace( $keywords[2][$key] );
			$kExist = preg_match( "#{$word}#iU", $content );
			if ( $kExist == 0 ) {
				$word   = htmlentities( $word );
				$kExist = preg_match( "#{$word}#iU", $content );
			}

			if ( $kExist ) {
				$kExists[] = $word;
				if ( $value == '-' ) {
					$black ++;
				} elseif ( $value == '+' ) {
					$must ++;
				} else {
					$while ++;
				}
			}
		}

		$res = true;
		if ( $tblack > 0 && $black > 0 ) {
			$res = false;
		}
		if ( $tmust > 0 && $must < $tmust ) {
			$res = false;
		}
		if ( $twhile > 0 && $while == 0 ) {
			$res = false;
		}

		if ( isset( $_GET['k'] ) ) {
			_e('<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n");
			_e('Keywords: ' . $aaa);
			_e('<hr />' . $content);
			_e('<pre>');
			print_r( $keywords );

			_e('<br />kExists: <br />');
			print_r( $kExists );

			_e('</pre>');
			_e('<br />tmust: ');
			var_dump( $tmust );
			_e('<br />must: ');
			var_dump( $must );
			_e('<br />twhile: ');
			var_dump( $twhile );
			_e('<br />while: ');
			var_dump( $while );
			_e('<br />tblack: ');
			var_dump( $tblack );
			_e('<br />black: ');
			var_dump( $black );
			_e('<br />-----<br />Result: ');
			var_dump( $res );
			_e('<br />');
		}

		return $res;
	}
}