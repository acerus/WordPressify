<?php
/**
 * Import manager
 *
 * @since 1.0.0
 */
class Astoundify_ImportManager {

	public static function init() {
		add_action( 'wp_ajax_astoundify_content_importer', array( __CLASS__, 'ajax_stage_import' ) );
		add_action( 'wp_ajax_astoundify_importer_iterate_item', array( __CLASS__, 'ajax_iterate_item' ) );
	}

	/**
	 * Stage an import.
	 *
	 * @since 1.3.0
	 */
	public static function ajax_stage_import() {
		check_ajax_referer( 'setup-guide-stage-import', 'security' );

		if ( ! current_user_can( 'import' ) ) {
			wp_send_json_error( astoundify_contentimporter_get_string( 'cap_check_fail', 'errors' ) );
		}

		// the style to use
		$style = isset( $_POST['style'] ) ? $_POST['style'] : false;

		if ( ! $style ) {
			wp_send_json_error();
		}

		// remove any inactive plugins
		$files = glob( trailingslashit( astoundify_contentimporter_get_config( 'definitions' ) ) . esc_attr( $style ) . '/*.json' );
		$files = self::get_importable_files( $files );

		$importer = Astoundify_ImporterFactory::create( $files );

		if ( ! is_wp_error( $importer ) ) {
			$stage = $importer->stage();

			if ( is_wp_error( $stage ) ) {
				wp_send_json_error( $stage->get_error_message() );
			}

			$items = $importer->get_items();

			if ( empty( $items ) ) {
				return wp_send_json_error( astoundify_contentimporter_get_string( 'file_reading', 'errors' ) );
			}

			return wp_send_json_success( array(
				'total' => count( $importer->get_items() ),
				'groups' => $importer->item_groups,
				'items' => $importer->get_items(),
			) );
		} else {
			return wp_send_json_error( $importer->get_error_message() );
		}

		exit();
	}

	/**
	 * AJAX iterate a single item.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function ajax_iterate_item() {
		if ( ! current_user_can( 'import' ) ) {
			wp_send_json_error( $strings['errors']['cap_check_fail'] );
		}

		$iterate_action = esc_attr( $_POST['iterate_action'] );

		if ( ! in_array( $iterate_action, array( 'import', 'reset' ) ) ) {
			wp_send_json_error( astoundify_contentimporter_get_string( 'process_action', 'errors' ) );
		}

		// clean up http request
		$item = wp_unslash( $_POST['item'] );

		if ( is_array( $item['data'] ) ) {
			$item['data'] = array_map( array( 'Astoundify_Utils', 'numeric_to_int' ), $item['data'] );
		} else {
			$item['data'] = Astoundify_Utils::numeric_to_int( $item['data'] );
		}

		$item = Astoundify_ItemImportFactory::create( $item );

		if ( is_wp_error( $item ) ) {
			wp_send_json_error( astoundify_contentimporter_get_string( 'process_type', 'errors' ) );
		}

		$item = $item->iterate( $iterate_action );

		if ( ! $item ) {
			wp_send_json_error( astoundify_contentimporter_get_string( 'iterate', 'errors' ) );
		}

		if ( ! is_wp_error( $item->get_processed_item() ) ) {
			wp_send_json_success( array(
				'item' => $item,
			) );
		} else {
			wp_send_json_error( $item->get_processed_item()->get_error_message() );
		}
	}

	/**
	 * Can't properly use array_filter in PHP < 5.3
	 *
	 * @since 1.3.0
	 * @return array $files
	 */
	public function get_importable_files( $files ) {
		$plugins = self::get_active_plugins();

		foreach ( $files as $k => $v ) {
			if ( false == strpos( $v, 'plugin' ) ) {
				continue;
			}

			if ( ! Astoundify_Utils::strposa( $v, $plugins ) ) {
				unset( $files[ $k ] );
			}
		}

		return $files;
	}

	/**
	 * Cant properly use array_filter in PHP < 5.3
	 *
	 * @since 1.3.0
	 * @return array $plugins
	 */
	public static function get_active_plugins() {
		$plugins = self::get_importable_plugins();

		foreach ( $plugins as $k => $plugin ) {
			if ( false == $plugin['condition'] ) {
				unset( $plugins[ $k ] );
			}
		}

		$plugins = array_keys( $plugins );

		return $plugins;
	}

	/**
	 * Merge required and recommended plugins.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public static function get_importable_plugins() {
		return array_merge( astoundify_contentimporter_get_required_plugins(), astoundify_contentimporter_get_recommended_plugins() );
	}

}

Astoundify_ImportManager::init();
