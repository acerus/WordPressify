<?php
/**
 * WooCommerce
 *
 * @since 1.0.0
 */
class Test_Plugin_WooCommerce extends WP_UnitTestCase {

	public function test_Plugin_WooCommerce_can_set_price() {
		$data = array(
			'id' => 'custom-product',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Product',
				'post_type' => 'product',
				'price' => '10.00',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$product = $item->get_processed_item();

		$this->assertEquals( '10.00', $product->_regular_price );
		$this->assertEquals( '10.00', $product->_price );
	}

	public function test_Plugin_WooCommerce_can_set_product_type() {
		register_post_type( 'product' );
		register_taxonomy( 'product_type', 'product' );

		$data = array(
			'id' => 'custom-product',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Product',
				'post_type' => 'product',
				'type' => 'simple',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$product = $item->get_processed_item();
		$types = wp_get_object_terms( $product->ID, 'product_type', array(
			'fields' => 'slugs',
		) );

		$this->assertEquals( 'simple', current( $types ) );
	}

	public function test_Plugin_WooCommerce_can_set_product_gallery() {
		$data = array(
			'id' => 'custom-product',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Product',
				'post_type' => 'product',
				'media' => array(
					'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
					'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$product = $item->get_processed_item();

		$gallery = explode( ',', $product->_product_image_gallery );
		$images = wp_list_pluck( get_attached_media( 'image', $product->ID ), 'ID' );

		$this->assertEqualsets( $images, $gallery );
	}

}
