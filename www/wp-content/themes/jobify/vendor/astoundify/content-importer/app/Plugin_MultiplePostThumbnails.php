<?php
/**
 * Extra procsesing for Multiple Post Thumbnails
 *
 * @since 1.2.0
 */
class Astoundify_Plugin_MultiplePostThumbnails implements Astoundify_PluginInterface {

	/**
	 * Initialize the plugin processing
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function init() {
		self::setup_actions();
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function setup_actions() {
		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_thumbnails' )
		);
	}

	/**
	 * Add thumbnails to an object.
	 *
	 * If `thumbnails` is passed loop through each item and apply an ID for an
	 * attachment for each thumbnail key.
	 *
	 * @since 1.2.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_thumbnails( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		$error = new WP_Error(
			'set-thumbnails',
			sprintf( 'Thumbnails for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		if ( ! isset( $item_data['thumbnails'] ) ) {
			return $error;
		}

		$passed = true;

		foreach ( $item_data['thumbnails'] as $key => $asset ) {
			if ( false !== ( $id = Astoundify_Utils::upload_asset( $asset, $object->ID ) ) ) {
				$passed = add_post_meta( $object->ID, $key, $id, true );
			}
		}

		if ( ! $passed ) {
			return $error;
		}

	}

}

Astoundify_Plugin_MultiplePostThumbnails::init();
