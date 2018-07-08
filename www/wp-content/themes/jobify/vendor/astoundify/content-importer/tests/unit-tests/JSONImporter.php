<?php
/**
 * JSONImporter
 *
 * @since 1.0.0
 */
class Test_JSONImporter extends WP_UnitTestCase {

	public function test_parse_files_returns_wp_error_when_no_files_exist() {
		$json_importer = new Astoundify_JSONImporter();

		$this->assertTrue( is_wp_error( $json_importer->parse_files() ) );
	}

	public function test_parse_files_returns_array_with_paths() {
		$json_importer = new Astoundify_JSONImporter( array(
			SAMPLE_DATA_DIR . '/posts.json'
		) );
		$json_importer->parse_files();
	}

}
