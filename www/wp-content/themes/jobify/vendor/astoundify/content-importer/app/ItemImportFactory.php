<?php
/**
 * Item Import factory
 *
 * @since 1.0.0
 */
class Astoundify_ItemImportFactory {

	/**
	 * Instantiate a new item import class depending on the type of item
	 *
	 * @since 1.0.0
	 * @param array $item The item to import
	 * @return object|WP_Error The instantiated importer or WP_Error if type is invalid
	 */
	public static function create( $item ) {
		if ( false == ( $type = self::is_valid_type( $item ) ) ) {
			return new WP_Error( 'invalid-type', 'Invalid item type cannot be imported' );
		}

		$type = str_replace( '-', '', $type );

		$classname = "Astoundify_ItemImport_{$type}";

		$import = new $classname( $item );

		return $import;
	}

	/**
	 * Determine if the item to be imported is a supported item type
	 *
	 * @since 1.0.0
	 * @param array $item The item to import
	 * @return bool True if the item is valid
	 */
	public static function is_valid_type( $item ) {
		$valid = array(
			'childtheme',
			'setting',
			'thememod',
			'object',
			'nav-menu',
			'nav-menu-item',
			'term',
			'widget',
			'comment',
		);

		$type = isset( $item['type'] ) ? esc_attr( $item['type'] ) : false;

		return in_array( $type, $valid ) ? $type : false;
	}

}
