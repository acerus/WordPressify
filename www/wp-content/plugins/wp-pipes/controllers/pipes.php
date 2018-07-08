<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: pipes.php 141 2014-01-24 10:36:21Z tung $
 * @author           thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class PIPESControllerPipes extends Controller {

	public function __construct() {

	}

	function display( $cachable = false, $urlparams = false ) {
		return;
	}

	public function edit() {
		$id  = isset( $_GET['id'] ) ? (int) sanitize_text_field( $_GET['id'] ) : '';
		$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
		header( 'Location: ' . $url );
	}

	public function delete() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? array_map( 'sanitize_text_field', $_GET['id'] ) : 0;
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->delete( $id );
		}
		PIPES::add_message( $res );
		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
		//$this->display();
	}

	public function copy() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? array_map( 'sanitize_text_field', $_GET['id'] ) : 0;
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->copy( $id );
		}
		PIPES::add_message( $res );

		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
	}

	public function publish() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? array_map( 'sanitize_text_field', $_GET['id'] ) : 0;
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->change_status( $id, 1 );
		}
		PIPES::add_message( $res );
		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
		//$this->display();
	}

	public function create_tables() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

#--------------------------------------------------
# Add user_meta for all admins
#--------------------------------------------------
		$users     = get_users();
		$user_meta = array( 'pipes_help_box' => 1, 'pipes_per_page' => 20, 'addons_per_page' => 20 );
		foreach ( $users as $user ) {
			if ( is_super_admin( $user->ID ) ) {
				foreach ( $user_meta as $meta_key => $value ) {
					$meta_value = get_user_meta( $user->ID, $meta_key, true );
					if ( $meta_value == '' ) {
						update_user_meta( $user->ID, $meta_key, $value );
					}
				}
			}
		}

#--------------------------------------------------
# Create Items table
#--------------------------------------------------


		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collation .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collation .= " COLLATE $wpdb->collate";
			}
		}

		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'wppipes_items` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL,
			`published` tinyint(1) NOT NULL,
			`engine` varchar(100) NOT NULL,
			`engine_params` text NOT NULL,
			`adapter` varchar(100) NOT NULL,
			`adapter_params` text NOT NULL,
			`inherit` int(11) NOT NULL DEFAULT "0",
			`inputs` text NOT NULL,
			`outputs` text NOT NULL,
			PRIMARY KEY (`id`)
	  	) ' . $collation;
		dbDelta( $sql );


#--------------------------------------------------
# Create Pipes table
#--------------------------------------------------
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'wppipes_pipes` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`code` varchar(100) NOT NULL,
			`name` varchar(100) NOT NULL,
			`item_id` int(11) NOT NULL,
			`params` text NOT NULL,
			`ordering` int(11) NOT NULL,
			PRIMARY KEY (`id`)
		) ' . $collation;
		dbDelta( $sql );
		header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
		exit();
	}

	public function pipes_restore_default_options() {
		global $pipes_settings;
		include_once( dirname( dirname( __FILE__ ) ) . DS . 'settings-init.php' );
		foreach ( $pipes_settings as $section ) {
			foreach ( $section as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					update_option( $value['id'], $value['default'] );
				}
			}
		}
		header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
		exit();
	}

	public function delete_cache_folder() {
		$dirPath = OGRAB_CACHE;
		$this->deleteDirCache( $dirPath );
		header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
		exit();
	}

	function deleteDirCache( $dirPath ) {
		if ( ! is_dir( $dirPath ) ) {
			throw new InvalidArgumentException( "$dirPath must be a directory" );
		}
		if ( substr( $dirPath, strlen( $dirPath ) - 1, 1 ) != '/' ) {
			$dirPath .= '/';
		}
		$files = glob( $dirPath . '*', GLOB_MARK );
		foreach ( $files as $file ) {
			if ( is_dir( $file ) ) {
				self::deleteDirCache( $file );
			} else {
				unlink( $file );
			}
		}
		rmdir( $dirPath );
	}

	public function move_to_draft() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? array_map( 'sanitize_text_field', $_GET['id'] ) : 0;
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->change_status( $id, 0 );
		}
		PIPES::add_message( $res );

		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
	}

	public function update_meta() {
		if ( isset( $_POST['uid'] ) ) {
			$user  = sanitize_text_field( $_POST['uid'] );
			$value = sanitize_text_field( $_POST['select'] );
			update_user_meta( $user, 'pipes_help_box', $value );

			return 'Success!';
		}
	}

	public function export_to_share() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? ( is_array( $_GET['id'] ) ? array_map( 'sanitize_text_field', $_GET['id'] ) : $_GET['id'] ) : 0;
		if ( $id == '' ) {
			PIPES::add_message( "Please pick up at least 1 pipe first!" );
			$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
			header( 'Location: ' . $url );
			exit();
		}
		$set_template = isset( $_GET['set_template'] ) ? sanitize_text_field( $_GET['set_template'] ) : 0;
		$res          = $mod->export_to_share( $id );
		//PIPES::add_message($res->msg);
		if ( count( $res->result ) == 1 ) {
			$file_name = sanitize_title( $res->result[0]->name ) . '.pipe';
		} else {
			$file_name = 'pipes-' . date( 'd-m-Y', time() ) . '.pipe';
		}
		$upload_dir = wp_upload_dir();
		if ( $set_template ) {
			$file_name = $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . $file_name;
			if ( ! is_file( $file_name ) ) {
				ogbFolder::create( $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' );
			}
		}
		/*$fp = fopen( $file_name, 'w' );
		foreach ( $res->result as $result ) {
			fwrite( $fp, json_encode( $result ) . "\n" );
		}
//var_dump(filesize("$file_name"));die;
		fclose( $fp );*/
		$output_content = '';
		foreach ( $res->result as $result ) {
			$output_content .= json_encode( $result ) . "\n";
		}
		if ( $set_template ) {
			PIPES::add_message( $res->msg );
			$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
			header( 'Location: ' . $url );
		}
		ob_start();
		header( "Cache-Control: public" );
		header( "Content-Description: File Transfer" );
		//header( "Content-Length: " . filesize( "$file_name" ) . ";" );
		header( "Content-Disposition: attachment; filename=$file_name" );
		header( "Content-Transfer-Encoding: binary" );

		//readfile( $file_name );
		echo $output_content;
		ob_end_flush();
		exit();
	}

	public function import_from_file() {
		$upload_dir = wp_upload_dir();
		$mod        = $this->getModel( 'pipes' );
		$id         = isset( $_GET['id'] ) ? (int) sanitize_text_field( $_GET['id'] ) : 0;
		$file_name  = isset( $_GET['file_name'] ) ? sanitize_text_field( $_GET['file_name'] ) : '';
		if ( isset ( $_FILES["file_import"]["name"] ) ) {
			$filename = $_FILES["file_import"]["tmp_name"];
		} elseif ( isset( $_GET['url'] ) ) {
			$filename = sanitize_text_field( $_GET['url'] );
		} elseif ( is_file( $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . $file_name ) ) {
			$filename = $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . $file_name;
		}
		$file_content = file_get_contents( $filename );
		$items        = explode( "\n", $file_content );
		$new_pipes    = array();
		if ( $file_content == '' ) {
			$new_pipes[] = "The file has not content!";
		}
		foreach ( $items as $value ) {
			if ( $value != '' ) {
				if ( substr( $value, 0, 1 ) == '{' ) {
					$item = json_decode( $value );
				} else {
					$item = json_decode( substr( $value, 3 ) );
				}
				if ( ! is_object( $item ) ) {
					$new_pipes[] = "There is something wrong with the structure of file's content!";
					continue;
				}
				$item->current_id = $id;
				$new_pipes[]      = $mod->import_from_file( $item );
			}
		}
		$message = implode( "</br>", $new_pipes );
		PIPES::add_message( $message );
		if ( isset( $_GET['url'] ) ) {
			$url = remove_query_arg( array( 'task', 'url' ), $_SERVER['HTTP_REFERER'] );
			header( 'Location: ' . $url );
			exit();
		} elseif ( $id > 0 ) {
			$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
			header( 'Location: ' . $url );
			exit();
		}
	}
}