<?php
/**
 * Multiple Post Thumbnails
 *
 * @since 1.2.0
 */
class Test_Plugin_MultiplePostThumbnails extends WP_UnitTestCase {

	public function test_Plugin_MultiplePostThumbnails_can_set_thumbnail() {
		$data = array(
			'id' => 'custom-listing',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Custom Listing',
				'thumbnails' => array(
					'download_grid-image_thumbnail_id' => 'http://f6ca679df901af69ace6-d3d26a34307edc4f7eeb40d85a64c4a7.r91.cf5.rackcdn.com/marketify-xml-images/company-logo.png',
				),
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$item = $item->get_processed_item();
		$media = get_attached_media( 'image', $item->ID );

		$this->assertEquals( get_post_meta( $item->ID, 'download_grid-image_thumbnail_id', true ), current( $media )->ID );
	}

}
