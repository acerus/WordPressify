<?php
/**
 * Easy Digital Downloads
 *
 * @since 1.0.0
 */
class Test_Plugin_FrontendSubmissions extends WP_UnitTestCase {

	public function test_Plugin_FrontendSubmissions_can_import_form() {
		$form = array(
			array(
				'template' => 'post_title',
				'public' => '',
				'required' => 'yes',
				'label' => 'Download Title',
				'name' => 'post_title',
				'help' => '',
				'css' => '',
				'size' => '40',
			),
		);
		$data = array(
			'id' => 'form-id',
			'type' => 'object',
			'data' => array(
				'post_title' => 'Submission Form',
				'post_type' => 'fes-forms',
				'form' => $form,
			),
		);

		$item_import = new Astoundify_ItemImport_Object( $data );
		$item = $item_import->iterate( 'import' );

		$edd_options = get_option( 'edd_settings' );

		$this->assertEquals( $item->get_processed_item()->ID, $edd_options['fes-submission-form'] );
		$this->assertEquals( get_post_meta( $item->get_processed_item()->ID, 'fes-form', true ), $form );
	}

}
