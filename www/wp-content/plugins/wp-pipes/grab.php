<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: grab.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

defined( '_JEXEC' ) or die( 'Restricted access' );

if ( isset( $_GET['x1'] ) ) {
	$c = $_GET['x1'];
	if ( $c == 1 ) {
		$c = 'utf-8';
	}
	_e('<meta http-equiv="content-type" content="text/html; charset=' . $c . '"/>');
}
require_once OBGRAB_HELPERS . 'common.php';
require_once OBGRAB_SITE . 'cronjob.php';

class obGrab {
	var $_aclass = null;
	var $_item = null;
	var $_aparams = null;
	var $_inputs = null;
	var $_outputs = null;
	var $_idata = null;
	var $_odata = null;

	function getItems( $id = 0 ) {
		global $wpdb;
		$id   = (int) $id;
		$qry  = "SELECT * FROM `{$wpdb->prefix}wppipes_items` WHERE `id`={$id}";
		$item = $wpdb->get_results( $qry, OBJECT );

		return $item;
	}

	function cache_oeData( $item, $rows ) {
		for ( $i = 0; $i < count( $rows ); $i ++ ) {
			$cache = serialize( $rows[$i] );
			$path  = OGRAB_EDATA . 'item-' . $item->id . DS . 'row-' . $i . '.dat';
			if ( isset( $_GET['x2'] ) ) {
				//echo "\n\n<br /><i><b>File:</b>".__FILE__.' <b>Line:</b>'.__LINE__."</i><br />\n\n";
				_e('<br>Path: ' . $path);
			}
			ogbFile::write( $path, $cache );
		}
		//$this->setTotalRow($item->id,"$i");
	}

	function setTotalRow( $id, $i ) {
		ogbFile::write( OGRAB_MEDATA . 'item-' . $id . '.txt', "$i" );
	}

	function getTotalRow( $id ) {
		$path = OGRAB_MEDATA . 'item-' . $id . '.txt';
		if ( ! is_file( $path ) ) {
			return 2;
		}

		return ogbFile::get_content( $path );
	}

	function get_cache_oeData( $item, $i ) {
		$path = OGRAB_EDATA . 'item-' . $item->id . DS . 'row-' . $i . '.dat';
		if ( ! is_file( $path ) ) {
			$row        = new stdClass();
			$row->error = 'item not found. check Limit Items of engine';

			return $row;
		}
		$row = file_get_contents( $path );
		if(!class_exists('SimplePie_Enclosure')){
			require_once(OBGRAB_SITE.'plugins'.DS.'engines'.DS.'rssreader'.DS.'helpers'.DS.'library'.DS.'SimplePie'.DS.'Enclosure.php');
		}
		if(!class_exists('SimplePie_Restriction')){
			require_once(OBGRAB_SITE.'plugins'.DS.'engines'.DS.'rssreader'.DS.'helpers'.DS.'library'.DS.'SimplePie'.DS.'Restriction.php');
		}
		if(!class_exists('SimplePie_Author')){
			require_once(OBGRAB_SITE.'plugins'.DS.'engines'.DS.'rssreader'.DS.'helpers'.DS.'library'.DS.'SimplePie'.DS.'Author.php');
		}
		if(!class_exists('SimplePie_Credit')){
			require_once(OBGRAB_SITE.'plugins'.DS.'engines'.DS.'rssreader'.DS.'helpers'.DS.'library'.DS.'SimplePie'.DS.'Credit.php');
		}
		$row = unserialize( $row );
		if ( isset( $_GET['x2'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			_e('<br />get_cache_oeData() <br />path: ' . $path);
			_e('<pre>');
			print_r( $row );
			_e('</pre>');
		}

		return $row;
	}

	function getItemInfo( $id = 0 ) {
		global $wpdb;
		if ( $this->_item ) {
			return $this->_item;
		}
		$id          = (int) $id;
		$qry         = "SELECT * FROM `{$wpdb->prefix}wppipes_items` WHERE `id`={$id}";
		$item        = $wpdb->get_row( $qry );
		$this->_item = $item;

		return $item;
	}

	function getPipes( $item ) {
		global $wpdb;

		if ( ! isset( $item->inherit ) ) {
			$item->inherit = 0;
		}
		if ( $item->inherit > 0 ) {
			$qry  = "SELECT * FROM " . $wpdb->prefix . "wppipes_items WHERE `id`={$item->inherit}";
			$inhe = $wpdb->get_results( $qry, OBJECT_K );

			$inputs  = $inhe->inputs;
			$outputs = $inhe->outputs;
			$item_id = $inhe->id;
		} else {
			$inputs  = $item->inputs;
			$outputs = $item->outputs;
			$item_id = $item->id;
		}
		$this->_inputs  = json_decode( $inputs );
		$this->_outputs = json_decode( $outputs );

		$qry = "SELECT * FROM " . $wpdb->prefix . "wppipes_pipes WHERE `item_id`={$item_id} ORDER BY `ordering`";

		$pipes = $wpdb->get_results( $qry );

		return $pipes;
	}

	function need_oeData_new( $item ) {
		if ( isset( $_GET['u'] ) ) {
			return true;
		}
		$path = OGRAB_EDATA . 'item-' . $item->id . DS . 'row-0.dat';
		if ( ! is_file( $path ) ) {
			return true;
		}
		$cache_mtime = filemtime( $path );
		$diff        = time() - $cache_mtime;

		if ( isset( $_GET['x'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('Engine lats run  - s: ');
			var_dump( $diff );
			if ( $diff > 600 ) {
				$a = $diff / 60;
				_e(' - m: ');
				var_dump( $a );
			}
			if ( $a > 60 ) {
				$a = $diff / 60;
				_e(' - h: ');
				var_dump( $a );
			}
		}

		$diff = $diff / 60;

		return $diff > 60;
	}

	function start( $id ) {
		$cronclass = new ogbPlugCron();
		$pipecf    = $cronclass::getGbParams();
		$item      = $this->getItemInfo( $id );
		if ( $pipecf->not_use_cache ) {
			$this->remove_cache_file( $item );
		}
		$res      = new stdclass();
		$info     = "\nItem id:" . $item->id . ' name:' . $item->name;
		$new_data = self::need_oeData_new( $item );
		if ( $new_data ) {
			$oeData = $this->getEngineData( $item->engine, $item->engine_params, $item );
			$total  = count( $oeData );
			$this->setTotalRow( $item->id, $total );
		} else {
			$total = self::getTotalRow( $item->id );
		}

		if ( $total < 1 ) {
			$info .= "<h3>ERROR ENGINE: NONE DATA</h3>";
		} else {
			if ( $new_data ) {
				$this->cache_oeData( $item, $oeData );
			}
			$info .= " found:{$total} rows";
		}
		$json = '{"error":"0","msg":"","found":' . $total . '}';

		if ( isset( $_GET['json'] ) ) {
			_e($res->json);
		}
		if ( isset( $_GET['x'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('get Data Engine Info: ' . $info);
			_e('<br />new_data: ');
			var_dump( $new_data );
			_e('<br />' . $json);
		}
		$limit = self::getItemInfo( $id );
		$limit = json_decode( $limit->engine_params );

		if ( property_exists($limit, 'limit_items') && $limit->limit_items < $total ) {
			$total = $limit->limit_items;
		}

		$res        = new stdclass();
		$res->pipe  = $item->name;
		$res->name  = $item->engine;
		$res->total = $total;
		$res->json  = $json;
		$res->msg   = $info;
		$res->log   = '';

		if ( isset( $_GET['json'] ) ) {
			_e($json);
		}

		return $res;
	}

	function remove_cache_file( $item ) {
		$path = OGRAB_EDATA . 'item-' . $item->id;
		if ( ! is_dir( $path ) ) {
			return;
		}
		$dir = opendir( $path );
		while ( $item = readdir( $dir ) ) {
			if ( is_file( $sub = $path . DS . $item ) ) {
				$file = $path . DS . $item;
				unlink( $file );
			}
		}

		return;
	}

	function storeItems( $id, $rid ) {
		$item    = $this->getItemInfo( $id );
		$pipes   = $this->getPipes( $item );
		$adapter = $this->getAdapter( $item->adapter, $item->adapter_params );
		if ( $adapter->error != '' ) {
			$msg = "<h3>ERROR adapter: {$adapter->error}</h3>";
			_e($msg);

			return $adapter->error;
		}
		$pipes = $this->importProcess( $pipes );
		$row   = $this->get_cache_oeData( $item, $rid );
		if ( ! isset( $row->error ) ) {
			$store = $this->process( $row, $pipes, $adapter );
			$this->saveLogStore( $store );

			return $store;
		} else {
			//echo $row->error;//exit();
			$res           = new stdclass();
			$res->action   = 'Error';
			$res->msg      = $row->error;
			$res->src_url  = '#';
			$res->src_name = 'None';
			$res->item_id  = $id;

			return $res;
		}
	}

	function getObjParam( $pStr ) {
		$params = json_decode( $pStr );

		//$params	= new JParameter($pStr);
		//$params	= $params->toObject();
		return $params;
	}

	function get_real_class( $class ) {
		$class_separate = explode( '_', $class );
		$prefix_class   = $class_separate[0];
		unset( $class_separate[0] );
		$temp      = implode( '_', $class_separate );
		$real_name = explode( '-', $temp );
		$class     = $prefix_class . '_' . end( $real_name );

		return $class;
	}

	function getEngineData( $name, $strParams, $pipe = null ) {
		$eclass = 'WPPipesEngine_' . $name;
		$error  = $this->importAddon( $name, OBGRAB_ENGINES, $eclass );
		if ( $error != '' ) {
			_e("<h3>ERROR Engine: {$error}</h3>");

			return array();
		}
		if ( ! class_exists( $eclass ) ) {
			$eclass = $this->get_real_class( $eclass );
		}
		$eParams = $this->getObjParam( $strParams );

		$rows = ogbLib::call_method( $eclass, 'getData', array( $eParams, $pipe ) );
		//$rows		= $eclass::getData($eParams);

		if ( isset( $_GET['x2'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			_e('getEngineData: ');
			_e('<pre>');
			_e('eParams: ');
			print_r( $eParams );
			_e('rows: ');
			print_r( $rows );
			_e('</pre>');
			//exit('<h4>stop</h4>');
		}

		return $rows;
	}

	function getAdapter( $name, $strParams ) {
		global $adapter;
		$adapter         = new stdClass();
		$adapter->classn = 'WPPipesAdapter_' . $name;
		$adapter->params = $this->getObjParam( $strParams );
		$adapter->error  = $this->importAddon( $name, OBGRAB_ADAPTERS, $adapter->classn );

		$this->_aclass  = $adapter->classn;
		$this->_aparams = $adapter->params;

		return $adapter;
	}

	function importAddon( $name, $path, $class ) {
		$file = $path . $name . DS . $name . '.php';
		if ( ! is_file( $file ) ) {
			$file = OB_PATH_PLUGIN . $name . DS . $name . '.php';
			if ( ! is_file( $file ) ) {
				$file = OB_PATH_PLUGIN_OTHER . $name . DS . $name . '.php';
			}
			$class = $this->get_real_class( $class );
		}
		if ( ! is_file( $file ) ) {
			return 'FILE_NOT_EXIST : ' . $file;
		}
		require_once $file;
		if ( ! class_exists( $class ) ) {
			return 'CLASS_NOT_EXIST';
		}

		return '';
	}

	function saveLogGen( $info ) {
		if ( isset( $_GET['x2'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			_e('saveLogGen()');
			_e('<pre>');
			print_r( $info );
			_e('</pre>');
		}

	}

	function saveLogStore( $store ) {
		if ( isset( $_GET['x2'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			_e('saveLogStore()');
			_e('<hr /><pre>');
			print_r( $store );
			_e('</pre>');
		}

	}

//--- Process --------------------
	function process( $edata, $pipes, $adapter ) {
		$stop        = new stdClass();
		$stop->state = false;
		$stop->msg   = '';

		$data         = array();
		$data['stop'] = $stop;
		$data['oe']   = $edata;
		$data['op']   = array();
		$time         = array();
		for ( $i = 0; $i < count( $pipes ); $i ++ ) {
			$pipe = $pipes[$i];
			if ( isset( $_GET['x5'] ) ) {
				$time[] = date( 'Y-m-d H:i:s' ) . ' - ' . microtime() . " - {$pipe->classn}";
			}
			$this->callProcessors( $data, $pipe );
			if ( $data['stop']->state ) {
				$stop = $data['stop'];
				break;
			}
		}

		if ( isset( $_GET['x5'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			$time[] = date( 'Y-m-d H:i:s' ) . ' - ' . microtime() . " - end";
			_e('<pre>');
			_e('Time Run processors: ');
			print_r( $time );
			_e('</pre>');
		}
		if ( isset( $_GET['x3'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('Before store()');

			_e('<pre>');
			_e('Data: ');
			print_r( $data );
			_e('</pre>');
		}
		if ( $stop->state ) {
			$store           = new stdClass();
			$store->name     = $this->_aclass;
			$store->action   = isset( $stop->act ) ? $stop->act : 'Stop';
			$store->msg      = isset( $stop->msg ) ? $stop->msg : 'Unknow';
			$store->id       = 0;
			$store->viewLink = '#none';
			$store->editLink = '#none';
		} else {
			$store = $this->store( $data );
		}
		$store = self::addInfoStore( $store, $data );

		return $store;
	}

	function addInfoStore( $store, $data ) {
		if( !is_object($store) ){
			$store = new stdClass();
		}
		$src_url        = isset( $data['oe']->src_url ) ? $data['oe']->src_url : '#None';
		$store->src_url = $src_url;

		$src_name        = isset( $data['oe']->src_name ) ? $data['oe']->src_name : 'None Name';
		$store->src_name = $src_name;

		$item           = $this->getItemInfo();
		$store->item_id = $item->id;

		if ( isset( $_GET['x'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('After store()<br />');
			$viewLink = get_site_url() . $store->viewLink;
			_e('View result: [ <a href="' . $viewLink . '" target="_blank">' . $store->name . ' Item - ' . $store->id . '</a> ]<br />');
			_e('View source: [ <a href="' . $src_url . '" target="_blank">' . $src_name . '</a> ]<br />');
			_e('<pre>');
			_e('Store result: ');
			print_r( $store );
			_e('</pre>');
		}

		return $store;
	}

	function store( $data ) {
		$ia     = $this->getInputs( $data, 'ia' );
		$aclass = $this->_aclass;
		if ( ! class_exists( $aclass ) ) {
			$aclass = $this->get_real_class( $aclass );
		}

		if ( isset( $_GET['x3'] ) ) {
			_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
			_e('$aclass: ' . $aclass);
			_e('<pre>');
			_e('Input Adapter: ');
			print_r( $ia );
			_e('</pre>');
		}
		$store = ogbLib::call_method( $aclass, 'store', array( $ia, $this->_aparams ) );

		//$store	= &$aclass::store($ia, $this->_aparams);
		return $store;
	}

	function importProcess( $pipes ) {
		$nPipes   = array();
		$errPipes = array();
		for ( $i = 0; $i < count( $pipes ); $i ++ ) {
			$pipe   = $pipes[$i];
			$pClass = 'WPPipesPro_' . $pipe->code;
			$err    = $this->importAddon( $pipe->code, OBGRAB_PROCESSORS, $pClass );
			if ( $err != '' ) {
				$errPipes[] = $pipe->code . " [ Processor ERROR: {$err}]";
			} else {
				$nPipe           = new stdClass();
				$nPipe->classn   = $pClass;
				$nPipe->params   = $this->getObjParam( $pipe->params );
				$nPipe->ordering = $pipe->ordering;
				$nPipes[]        = $nPipe;
			}
		}
		if ( count( $errPipes ) > 0 && isset( $_GET['x'] ) ) {
			_e("\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n");
			_e('<pre>');
			print_r( $errPipes );
			_e('</pre>');
		}

		return $nPipes;
	}

	function callProcessors( &$data, $pipe ) {
		$pInput = $this->getInputs( $data, $pipe->ordering );
		if ( ! class_exists( $pipe->classn ) ) {
			$pipe->classn = $this->get_real_class( $pipe->classn );
		}
		$pOutput = ogbLib::call_method( $pipe->classn, 'process', array( $pInput, $pipe->params ) );
		if ( isset( $pOutput->stop ) && $pOutput->stop->state ) {
			$data['stop'] = $pOutput->stop;

			return false;
		}
		$data['op'][$pipe->ordering] = $pOutput;
	}

	function getInputs( $data, $key ) {
		if ( $key == 'ia' ) {
			$ip = $this->_inputs->$key;
			if ( isset( $_GET['x4'] ) ) {
				_e('<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n");
				_e('<pre>');
				_e('IP: ');
				print_r( $ip );
				_e('</pre>');
			}
		} elseif ( isset( $this->_inputs->ip[$key] ) ) {
			$ip = $this->_inputs->ip[$key];
		}
		$input = new stdClass();
		for ( $i = 0; $i < count( @$ip ); $i ++ ) {
			$obj = $ip[$i];
			$st  = $obj->st == 'e' ? 'oe' : $obj->st;
			$if  = $obj->if == '' ? 'no_need' : $obj->if;
			$of  = $obj->of;
			if ( $st == 'oe' ) {
				$input->$if = $data['oe']->$of;
			} elseif ( $st == '' ) {
				$input->$if = '';
			} else {
				$input->$if = @$data['op'][$st]->$of;
			}
			if ( $if == 'no_need' ) {
				$input->$if = $data;
			}
		}

		return $input;
	}
}