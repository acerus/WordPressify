<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: pipe.php 147 2014-01-25 04:25:54Z tung $
 * @author               thimpress.com
 * @copyright            2014 thimpress.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
jimport('joomla.filesystem.wrapper.path');
class PIPESControllerPipe extends Controller {

	public function __construct() {

	}

	public function display() {
		$id    = filter_input( INPUT_GET, 'id' );
		$model = $this->getModel( 'pipe' );
		if ( ! $id ) {
			$temp_id  = $model->create_temp();
			$redirect = add_query_arg( 'id', $temp_id, 'admin.php?page=pipes.pipe' );
			wp_redirect( $redirect );
		} else {
			return;
		}

	}

	public function apply() {
		$this->save();
	}

	function add_temp() {
		$model    = $this->getModel( 'pipes' );
		$return   = $model->create_temp();
		$temp_id  = $return;
		$redirect = 'admin.php?page=pipes.pipe&task=edit&cid[]=' . $temp_id;
		//$this->setRedirect($redirect);
		wp_redirect( $redirect );
	}

	function qadd() {
		$page = 'quickadd';
		require_once OBGRAB_SITE . 'pages' . DS . $page . DS . 'index.php';
		$pageCl  = 'ogb_page_' . $page;
		$control = new $pageCl;
		$control->display();
	}

	function qedit() {
		$page = 'quickedit';
		require_once OBGRAB_SITE . 'pages' . DS . $page . DS . 'index.php';
		$pageCl  = 'ogb_page_' . $page;
		$control = new $pageCl;
		$control->display();
	}

	function inhe() {
		$page = 'inherit';
		require_once OBGRAB_SITE . 'pages' . DS . $page . DS . 'index.php';
		$pageCl  = 'ogb_page_' . $page;
		$control = new $pageCl;
		$control->display();
	}

	function copy() {
		$id     = JRequest::getInt( 'id', 0 );
		$mod    = $this->getModel( 'pipes' );
		$cop_id = $mod->copyItem( $id );
		global $mainframe, $option;
		$mainframe->redirect( "index.php?option={$option}&controller=items&task=edit&cid[]={$cop_id}" );
	}

	function cfdf() {
		_e('<pre>');
		//print_r( $_REQUEST );
		exit();
	}

	function getUrls( $urls, $id = 0 ) {
		$urls  = explode( "\nhttp", $urls );
		$srcs  = array();
		$pipes = array();
		for ( $i = 0; $i < count( $urls ); $i ++ ) {
			$url  = trim( $urls[$i] );
			$info = "[ ";
			if ( $i > 0 ) {
				$url = 'http' . $url;
				$info .= "--- {$i}";
			} else {
				$info .= "{$id}";
			}
			$a      = str_replace( 'http://www.', 'http://', $url ) . " [{$id}]";
			$srcs[] = str_replace( 'http://', '', $a );

			$info .= " ][ <a href=\"{$url}\" target=\"_blank\">{$url}</a> ]";
			$pipes[] = $info;
		}
		$res = array( 'pipes' => $pipes, 'srcs' => $srcs );

		return $res;
	}

	//=== POST ===
	function post() {
		require_once PIPES_PATH . DS . 'post.php';
		ogbPost::Post();
	}

	function gengin() {
		require_once PIPES_PATH . DS . 'post.php';
		ogbPost::getEngin();
	}

	function asave() {
		require_once PIPES_PATH . DS . 'post.php';
		ogbPost::saveAdapter();
	}

	//=== END POST ===

	function viewlog() {
		require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'cronlog.php';
	}

	function getioaddon() {
		$type = filter_input( INPUT_GET, 'type' );
		$name = filter_input( INPUT_GET, 'name' );
		$id   = filter_input( INPUT_GET, 'id' );

		$mod    = $this->getModel( 'pipe' );
		$params = $mod->getAddonParam( $type, $name, $id, false );
		$res    = $mod->getIOaddon( $type, $name, $params );
		$txt    = json_encode( $res );
		_e($txt);
		exit();
	}

	function remove_pipe() {
		$pid   = filter_input( INPUT_GET, 'pid', FILTER_VALIDATE_INT );
		$itid  = filter_input( INPUT_GET, 'itid', FILTER_VALIDATE_INT );
		$count = filter_input( INPUT_GET, 'count', FILTER_VALIDATE_INT );
		$mod   = $this->getModel( 'pipe' );
		$msg   = $mod->removePipe( $pid, $itid );
		_e($count);
		exit();
	}

	function addprocess() {
		$code     = filter_input( INPUT_GET, 'code' );
		$id       = filter_input( INPUT_GET, 'id' );
		$ordering = filter_input( INPUT_GET, 'order' );

		$mod  = $this->getModel( 'pipe' );
		$res  = $mod->addProcess( $code, $id, $ordering );
		$json = json_encode( $res );
		_e($json);
		exit();
	}

	function gaparam() {
		$type = filter_input( INPUT_GET, 'type' );
		$name = filter_input( INPUT_GET, 'name' );
		$id   = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );

		$mod = $this->getModel( 'pipe' );
		$txt = $mod->getAddonParam( $type, $name, $id );
		_e($txt);
		exit();
	}

	function save() {
		/*echo "\n\n<br /><i><b>File:</b>".__FILE__.' <b>Line:</b>'.__LINE__."</i><br />\n\n";
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';exit();*/

		global $mainframe, $option;
		$mod = $this->getModel( 'pipe' );
		$res = $mod->save();
		$msg = $res->msg;
		PIPES::add_message( $msg );

		$task = filter_input( INPUT_POST, 'task' );
		//$apply	= $task=='apply'?'&task=edit&id[]='.$res->id:'';
		$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $res->id;
		header( 'Location: ' . $url );
		exit();
//		$mainframe->enqueueMessage($msg, $res->typemsg);
//		$mainframe->redirect("index.php?option={$option}&controller=items".$apply);
	}

	function cancel() {
		$mode = $this->getModel( 'pipe' );
		$mode->remove_if_no_ip();
		$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipes';
		header( 'Location: ' . $url );
//		$mainframe->redirect( "index.php?option={$option}&controller=items");
	}

	function remove() {
		global $mainframe, $option;
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		JArrayHelper::toInteger( $cid );
		$row = JTable::getInstance( 'Pipes', 'wppipesTable' );
		$msg = '';
		foreach ( $cid as $id ) {
			$row->load( $id );
			if ( $row->delete() ) {
				$msg .= $row->getError();
			} else {
				$msg .= "Delete success [{$id}]";
			}
			$msg .= '<br />';
		}
		//echo $msg;exit();
		$mainframe->enqueueMessage( $msg );
		$mainframe->redirect( "index.php?option={$option}&controller=items" );
	}

	function itemspublish() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$this->setPublish( '1', $cid );
	}

	function publish() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$this->setPublish( '1', $cid );
	}

	function unpublish() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$this->setPublish( '0', $cid );
	}

	function itemsunpublish() {
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$this->setPublish( '0', $cid );
	}

	function savenote() {
		global $mainframe, $option;
		$new_note = empty( $_REQUEST['new_note'] ) ? '' : sanitize_text_field($_REQUEST['new_note']);
		$id       = empty( $_REQUEST['id'] ) ? 0 : (int) sanitize_text_field($_REQUEST['id']);

		$mod  = $this->getModel( 'pipe' );
		$res  = $mod->savenote( $id, $new_note );
		$json = json_encode( $res );
		_e($json);
		exit();
	}

	function save_b4_post() {
		$mod = $this->getModel( 'pipe' );
		$res = $mod->save_b4_post();
		_e(json_encode( $res ));
		exit();
	}

	/*function iwant() { //back later
		$cur_url    = urldecode( JRequest::getVar( 'cur_url' ) );
		$from_name  = $config->get( 'fromname' );
		$from_email = $config->get( 'mailfrom' );
		$to_email   = 'iwant@thimpress.com';
		$mailer->isHTML( true );
		$message = JRequest::getVar( 'mess' );
		$mes_arr = explode( ' ', $message );
		if ( count( $mes_arr ) > 6 ) {
			$mes_sub = '';
			for ( $i = 0; $i <= 5; $i ++ ) {
				$mes_sub .= $mes_arr[$i] . ' ';
			}
		} else {
			$mes_sub = $message;
		}
		$subject  = $mes_sub . '...';
		$mailBody = 'Dear obTeam,<br/><br/>';
		$mailBody .= '<p>' . $message . '</p>';
		$mailBody .= '<p>From link: ' . $cur_url . '</p>';
		$return = $mailer->sendMail( $from_email, $from_name, $to_email, $subject, $mailBody );
		if ( $return ) {
			echo JText::_( 'OBGRABBER_YOUR_MESSAGE_WAS_SENT' );
		} else {
			echo $return;
		}
		exit();
	}*/

	function write_down_input_processor() {
		$mod             = $this->getModel( 'pipe' );
		$id              = filter_input( INPUT_GET, 'id' );
		$processor_id    = filter_input( INPUT_GET, 'process_id' );
		$ordering        = filter_input( INPUT_GET, 'ordering' );
		$input_type      = filter_input( INPUT_GET, 'input_type' );
		$input_value     = filter_input( INPUT_GET, 'input_value' );
		$input_name      = filter_input( INPUT_GET, 'input_name' );
		$current_default = ogb_common::get_default_data( '', $id );
		if ( $input_type == 'e' ) {
			$input_type = 'so';
		} else {
			$stt        = $input_type;
			$input_type = 'po';
		}

		if( !$current_default ) {
			$current_default = new stdClass();
			$current_default->pi = array();
			$current_default->pi[$ordering] = new stdClass();
		} else {
			if ( ! is_array( $current_default->pi ) ) {
				$current_default->pi = array();
			}
			if ( ! is_object( $current_default->pi[$ordering] ) ) {
				$current_default->pi[$ordering] = new stdClass();
			}
		}

		$current_default->pi[$ordering]->$input_name = $input_type . ',' . $input_value . ',' . $processor_id;

		if ( isset( $stt ) ) {
			$current_default->pi[$ordering]->$input_name .= ',' . $stt;
		}

		$test_default = $mod->get_first_output_processor( $current_default, $ordering, $processor_id );
		if(!$test_default){
			$test_default = $current_default;
		}

		if(!json_encode( $test_default->po[$ordering] )){
			$keys = array_keys(get_object_vars($test_default->po[$ordering]));
			foreach( $keys as $key ) {
				$test_default->po[$ordering]->$key = null;
			}
		}

		$cache = serialize( $test_default );
		$path  = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
		ogbFile::write( $path, $cache );
		_e(json_encode( $test_default->po[$ordering] ));
		exit();
		/*echo '<pre>';print_r( $current_default );die;
		$pipe   = $mod->get_one_pipe( $processor_id );
		$name   = $pipe->code;
		$params = json_decode( $pipe->params );
		$class  = 'WPPipesPro_' . $name;
		$datas  = ogbLib::call_method( $class, 'process', array( $pipe->params ) );*/
	}

	function execaddonmethod() {
		$type        = filter_input( INPUT_POST, 'type' ) ? filter_input( INPUT_POST, 'type' ) : filter_input( INPUT_GET, 'type' );
		$name        = filter_input( INPUT_POST, 'name' ) ? filter_input( INPUT_POST, 'name' ) : filter_input( INPUT_GET, 'name' );
		$id          = filter_input( INPUT_POST, 'id' ) ? filter_input( INPUT_POST, 'id' ) : filter_input( INPUT_GET, 'id' );
		$ajax        = filter_input( INPUT_POST, 'ajax' ) ? filter_input( INPUT_POST, 'ajax' ) : filter_input( INPUT_GET, 'ajax' );
		$method      = filter_input( INPUT_POST, 'method' ) ? filter_input( INPUT_POST, 'method' ) : filter_input( INPUT_GET, 'method' );
		$res         = new stdClass();
		$path        = PIPES_PATH . DS . 'plugins' . DS . $type . 's' . DS . $name . DS . $name . '.php';
		$path_plugin = OB_PATH_PLUGIN . $name . DS . $name . '.php';
		switch ( $type ) {
			case 'engine':
				$class = 'WPPipesEngine_';
				break;
			case 'processor':
				$class = 'WPPipesPro_';
				break;
			case 'adapter':
				$class = 'WPPipesAdapter_';
				break;
			default:
				_e("Unknow addon type [{$type} {$name}]");
				exit();
		}
		if ( is_file( $path ) ) {
			include_once $path;
		} elseif ( ! is_file( $path_plugin ) ) {
			$res->err = "File not found [{$type} {$name}]";
			if ( $ajax ) {
				print_r( json_encode( $res ) );
				exit();
			}

			return $res;
		} else {
			$temp = explode( '-', $name );
			$name = end( $temp );
			include_once $path_plugin;
		}

		$class .= $name;

		if ( ! method_exists( $class, $method ) ) {
			$res->err = "not found method " . $method . "  [{$type} {$name}]";

			return $res;
		}
		$data = call_user_func( array( $class, $method ) );
		if ( $ajax ) {
			exit();
		}

		return $data;
	}

	public function delete_template() {
		$upload_dir = wp_upload_dir();
		if ( isset( $_POST['filename'] ) ) {
			$path = $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . sanitize_text_field($_POST['filename']);
			if ( ! is_file( $path ) ) {
				_e('File not exists!');
				exit();
			} else {
				unlink( $path );
				//print_r(error_get_last());
				_e('The template remove success!');
				exit();
				//echo '<pre>';print_r($_POST['filename']);die;
			}
		} else {
			_e('false');
			exit();
		}
	}

	public function quick_edit(){
		$mod = $this->getModel( 'pipe' );
		$res = $mod->quick_edit_pipe();
		_e(json_encode( $res ));
		exit();
	}

	function preview_engine() {
		require_once PIPES_PATH . DS . 'grab.php';
		$grab  = new obGrab;
		$style = $this->set_style_preview();
		if(!isset($_GET['id'])){
			_e('The pipe does not existed!');
			exit();
		}
		$pipe_id = (int) sanitize_text_field($_GET['id']);
		$pipe = $grab->getItemInfo( $pipe_id );
		$oeData = $grab->getEngineData( $pipe->engine, $pipe->engine_params );
		if(count($oeData) < 1){
			_e('There is not any items found!');
			exit();
		}
		$html = '<h2 class="ob_preview_title">' . sprintf( 'The output fields\'s value of %s. This is the stream #%d.', 'source', 1) . '</h2>';
		if(isset($oeData[0])) {
			$html .= '<ul>';
			foreach ( $oeData[0] as $key_oe => $value_oe ) {
				if($key_oe == 'src_url' || $key_oe == 'src_name'){
					continue;
				}
				$html .= '<li><h4>' . $key_oe . ':</h4> <pre class="ob_preview_value">' . print_r($value_oe, true) . '</pre></li>';
			}
			$html .= '</ul>';
			_e($style . $html);
		}
		exit();
	}

	function set_style_preview(){
		$style = "<style>
					.ob_preview_title{
						background-color: yellowgreen;
						text-align: center;
					}
					ul{
						list-style: none;
						background-color: lightgray;
						padding: 5px;
					}
					h4{
						text-transform: capitalize;
					}
					.ob_preview_value{
						background-color: gray;
						border-radius: 5px;
						box-shadow: 1px 2px yellowgreen;
						padding: 5px;
						white-space: pre-wrap;
					}
				</style>";
		return $style;
	}

	function preview_processor(){
		require_once PIPES_PATH . DS . 'grab.php';
		$grab  = new obGrab;
		$style = $this->set_style_preview();
		if(!isset($_GET['id'])){
			_e('The pipe does not exist!');
			exit();
		}
		if(!isset($_GET['pipe_id']) || ! isset($_GET['ordering'])){
			_e('The processor does not exist!');
			exit();
		}
		$pipe_id = (int) sanitize_text_field($_GET['id']);
		$ordering = sanitize_text_field($_GET['ordering']);
		$item    = $grab->getItemInfo( $pipe_id );
		$processors   = $grab->getPipes( $item );
		$pipes = $grab->importProcess( $processors );
		$oeData = $grab->getEngineData( $item->engine, $item->engine_params );
		if(count($oeData) < 1){
			_e(JText::_( 'There is not any items found' ));
			exit();
		}
		$data         = array();
		$data['oe']   = $oeData[0];
		$data['op']   = array();
		for ( $i = 0; $i < count( $pipes ); $i ++ ) {
			$pipe = $pipes[$i];
			$grab->callProcessors( $data, $pipe );
		}
		$html = '<h2 class="ob_preview_title">' . sprintf( 'The output fields\'s value of %s. This is the stream #%d.', $processors[$ordering]->name . ' Processor', 1) . '</h2>';
		$html .= '<ul>';
		foreach ( $data['op'][$ordering] as $key_oe => $value_oe ) {
			$html .= '<li><h4>' . $key_oe . ': <h4><pre class="ob_preview_value">' . print_r($value_oe, true) . '</pre></li>';
		}
		$html .= '</ul>';
		_e($style . $html);
		exit();
	}
}
