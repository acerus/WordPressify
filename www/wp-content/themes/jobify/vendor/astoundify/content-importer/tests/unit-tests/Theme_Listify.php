<?php
/**
 * WP Job Manager
 *
 * @since 1.0.0
 */
class Test_Theme_Listify extends WP_UnitTestCase {

	public function test_Theme_Listify_can_set_business_hours() {
		$data = array(
			'id' => 'custom-listing',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Listing',
				'post_type' => 'job_listing',
				'hours' => array(
					array( '08:00', '12:00' ),
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$listing = $item->get_processed_item();
		$hours = $listing->_job_hours;

		$this->assertEquals( '08:00', $hours[0]['start'] );
		$this->assertEquals( '12:00', $hours[0]['end'] );
	}

	public function test_Theme_Listify_can_set_gallery_images() {
		$data = array(
			'id' => 'custom-listing',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Listing',
				'post_type' => 'job_listing',
				'media' => array(
					'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
					'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$listing = $item->get_processed_item();

		$gallery_urls = $listing->_gallery_images;

		// this seems wrong
		$gallery = shortcode_parse_atts( str_replace( array( '[gallery ids=', ']' ), '', $listing->_gallery ) );
		$gallery = explode( ',', $gallery[0] );

		$image_ids = wp_list_pluck( get_attached_media( 'image', $listing->ID ), 'ID' );
		$image_urls = array();

		foreach ( $image_ids as $image ) {
			$image_urls[] = wp_get_attachment_url( $image );
		}

		$this->assertEqualSets( $gallery, $image_ids );
		$this->assertEqualSets( $gallery_urls, $image_urls );
	}

}
