<?php
namespace um_ext\um_messaging\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Messaging_GDPR {

	function __construct() {
		add_action( 'um_messaging_after_conversation_links', array( &$this, 'render_download_chat_link' ), 99, 2 );
		add_action( 'um_messaging_after_conversations_list', array( &$this, 'render_download_all_chats_link' ), 99 );

		add_action( 'template_redirect', array( &$this, 'download_file' ), 0 );
	}


	/**
	 * Get GDPR upload dir
	 *
	 * @param string $dir
	 *
	 * @return string
	 */
	function get_upload_dir( $dir = '' ) {

		$uploads    = wp_upload_dir();
		$upload_dir = str_replace( '/', DIRECTORY_SEPARATOR, $uploads['basedir'] . DIRECTORY_SEPARATOR );

		$dir = str_replace( '/', DIRECTORY_SEPARATOR, $dir );

		//check and create folder
		if ( ! empty( $dir ) ) {
			$folders = explode( DIRECTORY_SEPARATOR, $dir );
			$cur_folder = '';
			foreach ( $folders as $folder ) {
				$prev_dir = $cur_folder;
				$cur_folder .= $folder . DIRECTORY_SEPARATOR;

				if ( ! is_dir( $upload_dir . $cur_folder ) && wp_is_writable( $upload_dir . $prev_dir ) ) {

					mkdir( $upload_dir . $cur_folder, 0777 );

					if ( 'gdpr' == $folder ) {
						$htp = fopen( $upload_dir . $cur_folder . DIRECTORY_SEPARATOR . '.htaccess', 'w' );
						fputs( $htp, 'deny from all' ); // $file being the .htpasswd file
					}

				}
			}
		}

		//return dir path
		return $upload_dir . $dir;
	}



	/**
	 * Get download URL by file type
	 *
	 * @param string $conversation_id
	 *
	 * @return string
	 */
	function get_download_url( $conversation_id ) {
		$args = array(
			'download_chat_history' => 'true',
			'conversation_id'       => $conversation_id,
		);

		return add_query_arg( $args, get_bloginfo( 'url' ) );
	}


	/**
	 * Render HTML for GDPR section in General account page
	 *
	 * @param $message_to
	 * @param $user_id
	 */
	function render_download_chat_link( $message_to, $user_id ) {
		$response = UM()->Messaging_API()->api()->get_conversation_id( $message_to, $user_id ); ?>
		<a href="<?php echo $this->get_download_url( $response['conversation_id'] ) ?>" class="um-gdpr-donwload-link um-tip-e" title="<?php esc_attr_e( 'Download History', 'um-messaging' ); ?>"><i class="um-faicon-download"></i></a>
		<?php
	}


	/**
	 *
	 */
	function render_download_all_chats_link() {
		?>
		<a href="<?php echo $this->get_download_url( 'all' ) ?>" class="um-link um-gdpr-donwload-link" title="<?php esc_attr_e( 'Download History', 'um-messaging' ); ?>"><?php esc_html_e( 'Download Chats History', 'um-messaging' ) ?></a>
		<?php
	}


	/**
	 * Download file by parts
	 *
	 * @param string $filename
	 * @param bool $retbytes
	 *
	 * @return bool|int
	 */
	function readfile_chunked( $filename, $retbytes = true ) {
		$chunksize = 1 *( 1024 * 1024 ); // how many bytes per chunk
		$cnt = 0;
		$handle = fopen( $filename, 'rb' );
		if ( $handle === false ) {
			return false;
		}

		while ( ! feof( $handle ) ) {
			$buffer = fread( $handle, $chunksize );
			echo $buffer;
			if ( $retbytes ) {
				$cnt += strlen( $buffer );
			}
		}
		$status = fclose( $handle );
		if ( $retbytes && $status ) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;

	}


	/**
	 *
	 */
	function download_file() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! isset( $_GET['download_chat_history'] ) || 'true' != $_GET['download_chat_history'] ) {
			return;
		}

		if ( empty( $_GET['conversation_id'] ) ) {
			return;
		}

		@ignore_user_abort( true );
		@set_time_limit( 0 );

		$user_id = get_current_user_id();

		$gdpr_folder = $this->get_upload_dir( 'ultimatemember/gdpr' );
		$filename = md5( $user_id .  $_GET['conversation_id'] . 'gdpr_data_salt' );
		$filepath = $gdpr_folder . DIRECTORY_SEPARATOR . $filename . '.txt';

		global $wpdb;
		$file_content = '';
		$user_id = um_user('ID');
		if ( $_GET['conversation_id'] == 'all' ) {
			$conversations = UM()->Messaging_API()->api()->get_conversations( $user_id );

			if ( ! empty( $conversations ) ) {
				foreach ( $conversations as $conversation ) {
					$messages = $wpdb->get_results( $wpdb->prepare(
						"SELECT * 
							FROM {$wpdb->prefix}um_messages
							WHERE conversation_id = %d 
							ORDER BY time ASC",
						$conversation->conversation_id
					), ARRAY_A );

					if ( ! empty( $messages ) ) {

						um_fetch_user( $conversation->user_b );
						$from = um_user( 'display_name' );
						um_fetch_user( $conversation->user_a );
						$to = um_user( 'display_name' );

						if ( ! empty( $file_content ) ) {
							$file_content .= "\r\n\r\n";
						}
						$file_content .= "============= Conversation \"{$from}\" -> \"{$to}\" =============\r\n\r\n";

						foreach ( $messages as $message ) {
							if ( $message['author'] == get_current_user_id() ) {
								$author = __( 'Me', 'um-messaging' );
							} else {
								um_fetch_user( $message['author'] );
								$author = um_user( 'display_name' );
							}

							$file_content .= "[{$author} - {$message['time']}]\r\n{$message['content']}\r\n\r\n";
						}
					}
				}
			}
		} else {
			$messages = $wpdb->get_results( $wpdb->prepare(
				"SELECT * 
					FROM {$wpdb->prefix}um_messages
					WHERE conversation_id = %d 
					ORDER BY time ASC",
				$_GET['conversation_id']
			), ARRAY_A );

			if ( ! empty( $messages ) ) {
				foreach ( $messages as $message ) {
					if ( $message['author'] == get_current_user_id() ) {
						$author = __( 'Me', 'um-messaging' );
					} else {
						um_fetch_user( $message['author'] );
						$author = um_user( 'display_name' );
					}

					$file_content .= "[{$author} - {$message['time']}]\r\n{$message['content']}\r\n\r\n";
				}
			}
		}

		um_fetch_user( $user_id );

		if ( empty( $file_content ) ) {
			return;
		}


		$gdpr_file   = fopen( $filepath, 'w+' );
		fwrite( $gdpr_file, $file_content );
		fclose( $gdpr_file );

		$fsize = filesize( $filepath );

		$content_type = 'text/plain';
		$filename = "conversation-data-{$_GET['conversation_id']}.txt";

		header( "Pragma: no-cache" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Robots: none" );
		header( "Content-Description: File Transfer" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-type: {$content_type}" );
		header( "Content-Disposition: attachment; filename=\"{$filename}\"" );
		header( "Content-length: $fsize" );

		$levels = ob_get_level();
		for ( $i = 0; $i < $levels; $i++ ) {
			@ob_end_clean();
		}

		$this->readfile_chunked( $filepath );
		exit;
	}
}