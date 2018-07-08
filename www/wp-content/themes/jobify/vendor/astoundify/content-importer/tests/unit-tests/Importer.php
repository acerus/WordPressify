<?php
/**
 * Importer
 *
 * @since 1.0.0
 */
class Test_Importer extends WP_UnitTestCase {

	public function test_sort_groups_by_type_and_sorts_by_priority() {
		$items = array(
			array(
				'type' => 'post',
				'priority' => 20,
			),
			array(
				'type' => 'nav-menu',
				'priority' => 10,
			),
			array(
				'type' => 'nav-menu-item',
				'priority' => 10,
			),
			array(
				'type' => 'post',
				'priority' => 10,
			),
		);

		$importer = new Astoundify_JSONImporter();
		$importer->set_items( $items );
		$importer->sort();

		$sorted_items = $importer->get_items();

		$this->assertEquals( 10, $sorted_items[0]['priority'] );
		$this->assertEquals( 'post', $sorted_items[2]['type'] );
	}

}
