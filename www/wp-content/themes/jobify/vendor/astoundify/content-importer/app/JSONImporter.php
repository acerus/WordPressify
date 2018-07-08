<?php
/**
 * JSON Importer
 *
 * @uses Astoundify_ImporterAbstract
 * @implements Astoundify_ImporterInterface
 * @implements Astoundify_SortableInterface
 *
 * @since 1.0.0
 */
class Astoundify_JSONImporter extends Astoundify_AbstractImporter implements Astoundify_ImporterInterface {

	public function __construct( $files = array() ) {
		$this->files = $files;
	}

	public function stage() {
		$this->parse_files();
		$this->sort();
	}

	/**
	 * Parse a JSON file(s)
	 *
	 * Decode the JSON and add each item to the list of items to import.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function parse_files() {
		$error = new WP_Error( 'cannot-parse-files', 'Files could not be parsed for import' );

		$files = $this->get_files();

		if ( empty( $files ) ) {
			return $error;
		}

		$file_items = false;

		foreach ( $files as $file ) {
			$parsed_url = parse_url( $file );

			if ( isset( $parsed_url['path'] ) ) {
				$filesystem_error = new WP_Error( 'cannot-parse-files', 'Cannot read files on system. Try changing your <code>FS_METHOD</code> to <code>direct</code>. <a href="https://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants">More information</a>' );

				if ( 'direct' != get_filesystem_method() ) {
					return $filesystem_error;
				}

				$creds = request_filesystem_credentials( admin_url() );

				if ( ! WP_Filesystem( $creds ) ) {
					return $filesystem_error;
				}

				global $wp_filesystem;

				$file = $wp_filesystem->get_contents( $file );

				if ( $file ) {
					$file_items = json_decode( $file, true );
				}
			} elseif ( isset( $parsed_url['scheme'] ) ) {
				$file = wp_remote_fopen( $file );

				if ( $file ) {
					$file_items = json_decode( $file, true );
				}
			}

			if ( $file_items ) {
				foreach ( $file_items as $item ) {
					$this->items[] = $item;
				}
			}
		}// End foreach().

		$items = $this->get_items();

		if ( empty( $items ) ) {
			return $filesystem_error;
		}

		return $items;
	}

}
