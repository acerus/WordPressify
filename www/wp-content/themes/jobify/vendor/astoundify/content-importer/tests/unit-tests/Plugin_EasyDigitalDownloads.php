<?php
/**
 * Easy Digital Downloads
 *
 * @since 1.0.0
 */
class Test_Plugin_EasyDigitalDownloads extends WP_UnitTestCase {

	public function test_Plugin_EasyDigitalDownloads_can_set_purchase_page() {
		$data = array(
			'id' => 'purchase',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Checkout',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$edd_settings = get_option( 'edd_settings', array() );

		$this->assertEquals( $item->get_processed_item()->ID, $edd_settings['purchase_page'] );
	}

	public function test_Plugin_EasyDigitalDownloads_can_set_success_page() {
		$data = array(
			'id' => 'success',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Purchase Confirmation',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$edd_settings = get_option( 'edd_settings', array() );

		$this->assertEquals( $item->get_processed_item()->ID, $edd_settings['success_page'] );
	}

	public function test_Plugin_EasyDigitalDownloads_can_set_failure_page() {
		$data = array(
			'id' => 'failure',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Transaction Failed',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$edd_settings = get_option( 'edd_settings', array() );

		$this->assertEquals( $item->get_processed_item()->ID, $edd_settings['failure_page'] );
	}

	public function test_Plugin_EasyDigitalDownloads_can_set_purchase_history_page() {
		$data = array(
			'id' => 'purchase_history',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Purchase History',
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$edd_settings = get_option( 'edd_settings', array() );

		$this->assertEquals( $item->get_processed_item()->ID, $edd_settings['purchase_history_page'] );
	}

	public function test_Plugin_EasyDigital_Downloads_can_set_variable_prices() {
		$data = array(
			'id' => 'custom-download',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Download',
				'post_type' => 'download',
				'prices' => array(
					'Price Option 1' => '10.00',
					'Price Option 2' => '20.00',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		// check prices set
		$prices = $item->get_processed_item()->edd_variable_prices;

		$this->assertEquals( 'Price Option 1', $prices[0]['name'] );
		$this->assertEquals( '10.00', $prices[0]['amount'] );

		// check variable checkbox
		$this->assertEquals( 1, $item->get_processed_item()->_variable_pricing );

		// check single price is 0
		$this->assertEquals( 0.00, $item->get_processed_item()->edd_price );
	}

	public function test_Plugin_EasyDigital_Downloads_can_set_single_price() {
		$data = array(
			'id' => 'custom-download',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Download',
				'post_type' => 'download',
				'price' => 4.00,
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		// check prices set
		$price = $item->get_processed_item()->edd_price;

		$this->assertEquals( 4.00, $price );
	}

}
