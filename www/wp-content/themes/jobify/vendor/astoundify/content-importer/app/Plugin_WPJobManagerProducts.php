<?php
/**
 * Extra procsesing for WP Job Manager - Products
 *
 * @since 1.1.0
 */
class Astoundify_Plugin_WPJobManagerProducts implements Astoundify_PluginInterface {

	/**
	 * Initialize the plugin processing
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function init() {
		self::setup_actions();
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function setup_actions() {
		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_products' )
		);
	}

	/**
	 * Convert product slugs to IDs of previously imported products
	 * and link them to a listing.
	 *
	 * @since 1.1.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_products( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'job_listing' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-products',
			sprintf( 'Products for %s were not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$listing_id = $object->ID;

		if ( isset( $item_data['products'] ) ) {
			$_products = array();
			$products = $item_data['products'];

			foreach ( $products as $product_name ) {
				global $wpdb;

				$p_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = '%s'", $product_name ) );

				if ( $p_id ) {
					$_products[] = $p_id;
				}
			}

			update_post_meta( $listing_id, '_products', $_products );
		}

		// needs better error checking
		return true;
	}

}

Astoundify_Plugin_WPJobManagerProducts::init();
