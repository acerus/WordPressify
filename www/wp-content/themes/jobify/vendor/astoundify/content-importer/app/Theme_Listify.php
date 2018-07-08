<?php
/**
 * Extra procsesing for Listify
 *
 * @since 1.0.0
 */
class Astoundify_Theme_Listify implements Astoundify_PluginInterface {

	/**
	 * Initialize the plugin processing
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		self::setup_actions();
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function setup_actions() {
		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_hours' )
		);

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_gallery' ),
			20
		);
	}

	/**
	 * Add business hours to a listing
	 *
	 * If `hours` is set update the _job_hours meta.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_hours( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'job_listing' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-hours',
			sprintf( 'Business hours for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$listing_id = $object->ID;

		if ( isset( $item_data['hours'] ) ) {
			$_hours = array();
			$hours = $item_data['hours'];

			foreach ( $hours as $day => $times ) {
				$_hours[ $day ] = array(
					'start' => $times[0],
					'end' => $times[1],
				);
			}

			update_post_meta( $listing_id, '_job_hours', $_hours );
		}

		// needs better error checking
		return true;
	}

	/**
	 * Set the listing gallery
	 *
	 * If `media` is defined the uploaded images will be set as the gallery
	 * images for the current product.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_gallery( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'job_listing' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-gallery',
			sprintf( 'Gallery for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$listing_id = $object->ID;

		// single price
		if ( isset( $item_data['media'] ) ) {
			$images = get_attached_media( 'image', $listing_id );

			if ( ! empty( $images ) ) {
				$image_ids = implode( ',', wp_list_pluck( $images, 'ID' ) );
				$image_urls = array();

				foreach ( $images as $image ) {
					$image_urls[] = wp_get_attachment_url( $image->ID );
				}

				update_post_meta( $listing_id, '_gallery', "[gallery ids={$image_ids}]" );
				update_post_meta( $listing_id, '_gallery_images', $image_urls );
			}
		}

		// needs better error checking
		return true;
	}

}

Astoundify_Theme_Listify::init();
