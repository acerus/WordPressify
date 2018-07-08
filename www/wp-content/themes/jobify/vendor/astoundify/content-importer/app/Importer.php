<?php
/**
 * Importer
 *
 * @since 1.0.0
 */
abstract class Astoundify_AbstractImporter implements Astoundify_SortableInterface {

	/**
	 * The order items should be imported
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $item_groups = array(
		'childtheme' => array(),
		'setting' => array(),
		'thememod' => array(),
		'term' => array(),
		'nav-menu' => array(),
		'nav-menu-item' => array(),
		'object' => array(),
		'widget' => array(),
	);

	/**
	 * A list of items to import.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $items = array();

	/**
	 * A list of files to parse.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $files = array();

	/**
	 * Set the importer's items
	 *
	 * @since 1.0.0
	 * @param array $items A list of items to set
	 * @return array A list of items
	 */
	public function set_items( $items ) {
		$this->items = $items;

		return $this->items;
	}

	/**
	 * Get the importer's items
	 *
	 * @since 1.0.0
	 * @return array A list of items
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Set the importer's files
	 *
	 * @since 1.0.0
	 * @param array $files A list of files to set
	 * @return array A list of files
	 */
	public function set_files( $files ) {
		$this->files = $files;

		return $this->files;
	}

	/**
	 * Get the importer's files
	 *
	 * @since 1.0.0
	 * @return array A list of files
	 */
	public function get_files() {
		return $this->files;
	}

	/**
	 * Sort the items
	 *
	 * First group and order based on $this->import_groups then sort by priority.
	 *
	 * @since 1.0.0
	 * @return array A sorted list of items
	 */
	public function sort() {
		$items = $this->get_items();

		if ( empty( $items ) ) {
			return $items;
		};

		// group by type
		foreach ( $items as $item ) {
			$this->item_groups[ $item['type'] ][] = $item;
		}

		// sort by priority
		foreach ( $this->item_groups as $type => $items ) {
			usort( $items, array( $this, 'sort_by_priority' ) );

			$this->item_groups[ $type ] = $items;
		}

		$_items = array();

		foreach ( $this->item_groups as $items ) {
			foreach ( $items as $item ) {
				$_items[] = $item;
			}
		}

		$this->set_items( $_items );

		return $this->get_items();
	}

	/**
	 * Sort by priority
	 *
	 * @since 1.0.0
	 * @param int $a
	 * @param int $b
	 * @return int Sort order
	 */
	public function sort_by_priority( $a, $b ) {
		if ( ! isset( $a['priority'] ) ) {
			$a['priority'] = 10;
		}

		if ( ! isset( $b['priority'] ) ) {
			$b['priority'] = 10;
		}

		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}

		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}

}
