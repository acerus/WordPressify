<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: plugins.php 170 2014-01-26 06:34:40Z thongta $
 * @author           thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
// require_once dirname(dirname(dirname(__FILE__))).DS.'includes'.DS.'model.php';
// require_once '';
class PIPESModelPlugins extends Model {
	public function getTable() {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'tables' . DS . 'plugins.php';
		$itemsListTable = new PIPES_Plugins_List_Table();
		$user             = get_current_user_id();
		$current_per_page = get_user_meta( $user, 'addons_per_page', true );
		if ( isset( $current_per_page ) && $current_per_page > 0 ) {
			$value = $current_per_page;
		}
		//Fetch, prepare, sort, and filter our data...
		if ( isset( $_POST['wp_screen_options']['option'] ) && $_POST['wp_screen_options']['option'] == 'addons_per_page' ) {
// get the current admin screen
			$option = sanitize_text_field($_POST['wp_screen_options']['option']);
			$value  = sanitize_text_field($_POST['wp_screen_options']['value']);

			update_user_meta( $user, $option, $value );
		}
		if ( isset( $value ) ) {
			$itemsListTable->per_page = $value;
		}
		//Fetch, prepare, sort, and filter our data...
		$itemsListTable->prepare_items();

		return $itemsListTable;
	}

	public function uninstall( $addons ) {
		$addons   = ( is_array( $addons ) ) ? $addons : array( $addons );
		$message = array();
		foreach ($addons as $addon) {
			$name_and_type = explode("-", $addon);
			$name = $name_and_type[1];
			$type = $name_and_type[0];
			$path = OBGRAB_ADDONS . $type . 's' . DS . $name;
			if (! is_dir($path)) {
				$message[] = "$path must be a directory";
				continue;
			}
			if( self::check_addon_in_used($name) ){
				$message[] = "$name was in used, can not remove it!";
				continue;
			}
			self::deleteDir($path);
			$message[] = $name . ' uninstalled successful!';
		}
		$message = implode("</br>", $message);

		return $message;
	}

	public function check_addon_in_used($code){
		global $wpdb;
		$sql    = "SELECT *
					FROM " . $wpdb->prefix . "wppipes_pipes
					WHERE `code` = '" . $code."'";
		$result = $wpdb->get_row( $sql, ARRAY_A );
		return $result;
	}

	public function deleteDir( $path ) {
		if (substr($path, strlen($path) - 1, 1) != '/') {
			$path .= '/';
		}
		$files = glob($path . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($path);
	}
	
	public function getPipesPlugins(){
		$addon_types = array('engine'=>'source', 'adapter'=>'destination', 'processor'=>'processor');
		// get core & installed plugins
		$plugins_installed	= PIPES_Helper_Plugins::getPlugins( true );
		
		// get list pipes plugin available on thimpress.com
		$data = array();
		$data['avaiable'] = array();
		$data['service'] = array();
		$data['update_count'] = 0;
		$t = (array)$this->getAllPipesPlugins();
		if(count($t) > 0) {
			$plugins = array_merge($t['destinations'], $t['sources'], $t['processors']);
			foreach ($plugins as $p) {
				$plugin = (array)$p;
				if ($plugin['element'] && isset($plugins_installed[$plugin['element']])) {
					$plugins_installed[$plugin['element']]['last_version'] = isset($plugin['last_version']) ? $plugin['last_version'] : '';
					$plugins_installed[$plugin['element']]['img'] = isset($plugin['img']) ? $plugin['img'] : '';
					$plugins_installed[$plugin['element']]['url'] = isset($plugin['url']) ? $plugin['url'] : '';
					$version = isset($plugins_installed[$plugin['element']]['version']) ? $plugins_installed[$plugin['element']]['version'] : '0';
					$last_version = isset($plugin['last_version']) ? $plugin['last_version'] : '0';
					if ($last_version && version_compare($last_version, $version, '>')) {
						$plugins_installed[$plugin['element']]['update'] = 1;
						$data['update_count']++;
					}
				} else {
					preg_match('/^pipes-([^-]+)-(.+)/i', $plugin['element'], $result);
					$addon_type = isset($result[1]) ? $result[1] : $addon_types[$plugin['addon']];
					$plugin['addon_type'] = $addon_type;
					$data['avaiable'][] = (object)$plugin;
				}
			}
		}

		$elements_installed = array();
		// convert type
		
		$data['installed'] = array();
		foreach ( $plugins_installed as $key => $plugin ) {
			// check is core
			preg_match( '/^pipes-([^-]+)-(.+)/i', $plugin['element'], $result );
			
			$is_core = empty( $result );

			if( $is_core ) {
				$addon_type = isset( $addon_types[$plugin['addon']] ) ? $addon_types[$plugin['addon']] : '';
			} else {
				$addon_type = isset( $result[1] ) ? $result[1] : $addon_types[$plugin['addon']];
			}

			if ( $plugin['group'] == 'wppipes-processor' ) {
				$elements_installed['processors'][]= $key;
			} elseif ( $plugin['group'] == 'wppipes-engine' ) {
				$elements_installed['sources'][]= $key;
			} elseif ( $plugin['group'] == 'wppipes-adapter' ) {
				$elements_installed['destinations'][]= $key;
			}
			$plugin['update']		= isset($plugin['update']) ? $plugin['update'] : 0;
			$plugin['is_core']		= $is_core? 1: 0;
			$plugin['addon_type']	= $addon_type;
			$data['installed'][]	=(object)$plugin;
		}
		
		return $data;
		// plugins 
		
	}

	public function getAllPipesPlugins(){
		$plugins_cache_file = OGRAB_CACHE . 'plugins.txt';
		$plugins_json		= '';
		$plugins			= array();
		if ( is_file( $plugins_cache_file ) ) {
			$mtime = filemtime($plugins_cache_file);
			if( (int)(time()-$mtime) < 86400 ) {
				$plugins_json = file_get_contents($plugins_cache_file);
				$plugins = json_decode($plugins_json);
			}
		}

		if (!$plugins || empty($plugins)) {
			$url = 'https://thimpress.com/?thim_get_pipes_products=1';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			curl_close($ch);
			$plugins = json_decode($result, true);
			if(is_array($plugins) && !empty($plugins)) {
				file_put_contents($plugins_cache_file, $result);
			} else {
				$plugins = array();
			}
		}

		return $plugins;
	}
}