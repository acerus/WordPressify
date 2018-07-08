<?php
/**
 * WP Job Manager
 *
 * @since 1.0.0
 */
class Test_Plugin_WPJobManager extends WP_UnitTestCase {

	public function test_Plugin_WPJobManager_can_set_all_location_data() {
		$data = array(
			'id' => 'custom-listing',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Listing',
				'post_type' => 'job_listing',
				'location' => array(
					'address' => '1650 N Mills Ave, Orlando, FL 32803',
					'latitude' => '28.5659791',
					'longitude' => '-81.3655687',
					'city' => 'Orlando',
					'state' => 'Florida',
					'state_short' => 'FL',
					'country_long' => 'United States',
					'country_short' => 'US',
					'postcode' => '32803',
					'street' => 'North Mills Avenue',
					'street_number' => '1650',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$listing = $item->get_processed_item();

		$this->assertEquals( '1', $listing->geolocated );
		$this->assertEquals( 'Orlando', $listing->geolocation_city );
		$this->assertEquals( 'United States', $listing->geolocation_country_long );
		$this->assertEquals( 'US', $listing->geolocation_country_short );
		$this->assertEquals( '1650 N Mills Ave, Orlando, FL 32803', $listing->geolocation_formatted_address );
		$this->assertEquals( '28.5659791', $listing->geolocation_lat );
		$this->assertEquals( '-81.3655687', $listing->geolocation_long );
		$this->assertEquals( '32803', $listing->geolocation_postcode );
		$this->assertEquals( 'Florida', $listing->geolocation_state_long );
		$this->assertEquals( 'North Mills Avenue', $listing->geolocation_street );
		$this->assertEquals( '1650', $listing->geolocation_street_number );
	}

	public function test_Plugin_WPJobManager_can_set_featured_job() {
		$data = array(
			'id' => 'custom-listing',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Listing',
				'post_type' => 'job_listing',
				'featured' => true,
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$listing = $item->get_processed_item();

		$this->assertEquals( -1, $listing->menu_order );
		$this->assertEquals( 1, $listing->_featured );
	}

}
