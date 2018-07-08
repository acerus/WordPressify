<?php
/**
 * Extra procsesing for WooCommerce
 *
 * @since 1.0.0
 */
class Astoundify_Plugin_WooCommerce implements Astoundify_PluginInterface {

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
		$pages = array( 'myaccount', 'checkout', 'cart', 'shop' );

		foreach ( $pages as $page ) {
			add_action(
				'astoundify_import_content_after_import_item_' . $page,
				array( __CLASS__, 'add_page_option' )
			);

			add_action(
				'astoundify_import_content_after_reset_item_' . $page,
				array( __CLASS__, 'delete_page_option' )
			);
		}

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_product_defaults' )
		);

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_price' )
		);

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_product_type' )
		);

		// this needs to happen after images have had a chance to be attached
		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_product_gallery' ),
			20
		);
	}

	/**
	 * Assign the relevant setting.
	 *
	 * @since 1.0.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function add_page_option( $ItemImport ) {
		update_option( "woocommerce_{$ItemImport->get_id()}_page_id", $ItemImport->get_processed_item()->ID );
	}

	/**
	 * Delete the relevant setting.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function delete_page_option( $ItemImport ) {
		delete_option( "woocommerce_{$ItemImport->get_id()}_page_id" );
	}

	/**
	 * Set product defaults.
	 *
	 * @since 1.1.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_product_defaults( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'product' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-product-defaults',
			sprintf( 'Product options for %s were not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$product_id = $object->ID;

		update_post_meta( $product_id, '_manage_stock', 'no' );
		update_post_meta( $product_id, '_tax_status', 'taxable' );
		update_post_meta( $product_id, '_downloadable', 'no' );
		update_post_meta( $product_id, '_virtual', 'taxable' );
		update_post_meta( $product_id, '_visibility', 'visible' );
		update_post_meta( $product_id, '_stock_status', 'instock' );
		update_post_meta( $product_id, 'total_sales', 0 );
		update_post_meta( $product_id, '_purchase_note', '' );
		update_post_meta( $product_id, '_featured', 'no' );
		update_post_meta( $product_id, '_weight', '' );
		update_post_meta( $product_id, '_length', '' );
		update_post_meta( $product_id, '_width', '' );
		update_post_meta( $product_id, '_height', '' );
		update_post_meta( $product_id, '_sku', '' );
		update_post_meta( $product_id, '_product_attributes', array() );
		update_post_meta( $product_id, '_sale_price', '' );
		update_post_meta( $product_id, '_sale_price_date_from', '' );
		update_post_meta( $product_id, '_sale_price_date_to', '' );
		update_post_meta( $product_id, '_sold_individually', '' );
	}

	/**
	 * Add a price to the product
	 *
	 * If `price` is defined a single price is used.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_price( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'product' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-pricing',
			sprintf( 'Pricing for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$product_id = $object->ID;

		// single price
		if ( isset( $item_data['price'] ) ) {
			$price = $item_data['price'];

			update_post_meta( $product_id, '_regular_price', $price );
			update_post_meta( $product_id, '_price', $price );
		}

		// needs better error checking
		return true;
	}

	/**
	 * Set the product type
	 *
	 * If `type` is defined the product type taxonomy will be set
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_product_type( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'product' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-type',
			sprintf( 'Product type for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$product_id = $object->ID;

		// single price
		if ( isset( $item_data['type'] ) ) {
			$type = $item_data['type'];

			wp_set_object_terms( $product_id, $type, 'product_type' );
		} else {
			wp_set_object_terms( $product_id, 'simple', 'product_type' );
		}

		// needs better error checking
		return true;
	}

	/**
	 * Set the product gallery
	 *
	 * If `media` is defined the uploaded images will be set as the gallery
	 * images for the current product.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public static function set_product_gallery( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'product' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-gallery',
			sprintf( 'Product gallery for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$product_id = $object->ID;

		// single price
		if ( isset( $item_data['media'] ) ) {
			$images = get_attached_media( 'image', $product_id );

			if ( ! empty( $images ) ) {
				$image_ids = implode( ',', wp_list_pluck( $images, 'ID' ) );

				update_post_meta( $product_id, '_product_image_gallery', $image_ids );
			}
		}

		// needs better error checking
		return true;
	}

}

Astoundify_Plugin_WooCommerce::init();
