<?php
/**
 * Import a term
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.0.0
 */
class Astoundify_ItemImport_Term extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function setup_actions() {
		// add extra object components
		$actions = array(
			'set_term_meta'
		);

		foreach ( $actions as $action ) {
			$tag = 'astoundify_import_content_after_import_item_type_term';

			if ( ! has_action( $tag, array( $this, $action ) ) ) {
				add_action( $tag, array( $this, $action ) );
			}
		}
	}

	/**
	 * Get the taxonomy the term is associated with
	 *
	 * @since 1.0.0
	 * @return string|false The taxonomy slug or false if the taxonomy does not exist
	 */
	private function get_taxonomy() {
		if ( isset( $this->item['data']['taxonomy'] ) ) {
			$tax = $this->item['data']['taxonomy'];

			return taxonomy_exists( $tax ) ? $tax : false;
		}

		return false;
	}

	/**
	 * Get the args to change the values of the imported term
	 *
	 * @since 1.1.0
	 * @return array $args
	 */
	private function get_args() {
		$args = array();

		if ( isset( $this->item['data']['parent'] ) && $this->get_parent() ) {
			$args['parent'] = $this->get_parent()->term_id;
		}

		return $args;
	}

	/**
	 * Get the parent term. Must be imported before
	 *
	 * @since 1.1.0
	 * @return object WP_Term
	 */
	private function get_parent() {
		$parent = get_term_by( 'name', $this->item['data']['parent'], $this->get_taxonomy() );

		return $parent;
	}

	/**
	 * Import a single item
	 *
	 * @since 1.0.0
	 * @return (WP_Term|WP_Error) WP_Term on success. WP_Error on failure.
	 */
	public function import() {
		if ( $this->get_previous_import() ) {
			return $this->get_previously_imported_error();
		}

		$taxonomy = $this->get_taxonomy();

		if ( ! $taxonomy ) {
			return $this->get_default_error();
		}

		$result = wp_insert_term( $this->item['data']['name'], $taxonomy, $this->get_args() );

		if ( ! is_wp_error( $result ) ) {
			$result = get_term( $result['term_id'], $taxonomy );
		}

		return $result;
	}

	/**
	 * Reset a single item
	 *
	 * @since 1.0.0
	 * @return (true|WP_Error) True on success, WP_Erorr on failure
	 */
	public function reset() {
		$term = $this->get_previous_import();

		if ( ! $term ) {
			return $this->get_not_found_error();
		}

		$result = wp_delete_term( $term->term_id, $this->get_taxonomy() );

		if ( is_wp_error( $result ) || ! $result || 0 == $result ) {
			return $this->get_default_error();
		}

		return $result;
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.0.0
	 * @uses $wpdb
	 * @return mixed Array if term is found or false
	 */
	public function get_previous_import() {
		$taxonomy = $this->get_taxonomy();

		if ( ! $taxonomy ) {
			return false;
		}

		if ( ! isset( $this->item['data']['name'] ) ) {
			return false;
		}

		$term_name = $this->item['data']['name'];

		$term = get_term_by( 'name', $term_name, $taxonomy );

		return $term;
	}

	/**
	 * Set term meta
	 *
	 * @since 1.3.0
	 * @return true|WP_Error True if all meta can be set
	 */
	public function set_term_meta() {
		$error = new WP_Error(
			'set-post-meta',
			sprintf( 'Meta for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$meta = false;

		if ( isset( $this->item['data']['meta'] ) ) {
			$meta = $this->item['data']['meta'];
		}

		if ( ! $meta ) {
			return $error;
		}

		return $this->add_metadata( 'term', $meta, $object ) ? true : $error;
	}

}
