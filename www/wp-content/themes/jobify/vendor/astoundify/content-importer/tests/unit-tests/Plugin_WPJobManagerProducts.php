<?php
/**
 * WP Job Manager - Products
 *
 * @since 1.0.0
 */
class Test_Plugin_WPJobManagerProducts extends WP_UnitTestCase {

	public function test_Plugin_WPJobManagerProducts_can_assign_product_ids() {
		$data = array(
			'id' => 'product-coupon',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Product Coupon',
				'post_type' => 'product',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$product = $item->get_processed_item();

		$data = array(
			'id' => 'custom-listing',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Listing',
				'post_type' => 'job_listing',
				'products' => array( 'product-coupon' ),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$listing = $item->get_processed_item();

		$this->assertEqualSets( array( $product->ID ), (array) $listing->_products );
	}

}
