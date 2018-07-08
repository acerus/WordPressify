<?php
/**
 * Single item import
 *
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.0.0
 */
abstract class Astoundify_AbstractItemImport {

	/**
	 * The item data that is being acted upon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $item;

	/**
	 * The action that is being taken on the item data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $action;

	/**
	 * The processed item.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var WP_Error|mixed
	 */
	public $processed_item;

	/**
	 * Set the current item data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct( $item ) {
		$this->set_item( $item );

		$this->setup_actions();
	}

	/**
	 * Set the current item data.
	 *
	 * @since 1.0.0
	 * @param array $item Item data.
	 * @return array Item data.
	 */
	public function set_item( $item = false ) {
		if ( ! $item ) {
			return false;
		}

		$this->item = $item;

		return $this->get_item();
	}

	/**
	 * Get the current item data.
	 *
	 * @since 1.0.0
	 * @return array Item data.
	 */
	public function get_item() {
		return $this->item;
	}

	/**
	 * Set the current action.
	 *
	 * @since 1.0.0
	 * @param string $action The action to take on the item.
	 * @return string The action to take on the item.
	 */
	public function set_action( $action ) {
		$this->action = $action;

		return $this->get_action();
	}

	/**
	 * Get the current action.
	 *
	 * @since 1.0.0
	 * @return string The action to take on the item.
	 */
	public function get_action() {
		return $this->action ? $this->action : 'import';
	}

	/**
	 * Set the processed item.
	 *
	 * @since 1.0.0
	 * @param mixed $item The procesesed item.
	 * @return mixed The processed item.
	 */
	public function set_processed_item( $item ) {
		$this->processed_item = $item;

		return $this->get_processed_item();
	}

	/**
	 * Get the processed item.
	 *
	 * @since 1.0.0
	 * @return mixed The processed item.
	 */
	public function get_processed_item() {
		// get a clean version of the object
		if ( isset( $this->processed_item->ID ) && true == ( $clean = get_post( $this->processed_item->ID ) ) ) {
			clean_post_cache( $clean );
			$this->processed_item = $clean;
		}

		return $this->processed_item;
	}

	/**
	 * Get the ID of the current item.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_id() {
		if ( isset( $this->item['id'] ) ) {
			return esc_attr( $this->item['id'] );
		}

		return false;
	}

	/**
	 * Get the type of the current item.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_type() {
		return isset( $this->item['type'] ) ? esc_attr( $this->item['type'] ) : false;
	}

	/**
	 * Get the type label of the current item.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_type_label() {
		$strings = astoundify_contentimporter_get_string( $this->get_type(), 'type_labels' );
		return esc_attr( $strings[0] );
	}

	/**
	 * Generate a WP_Error instance for the current item when a generic error occurs
	 *
	 * @since 1.0.0
	 * @return WP_Error
	 */
	public function get_default_error() {
		return new WP_Error(
			sprintf( '%s-%s-failed', $this->get_type(), $this->get_action() ),
			sprintf(
				'<strong>%1$s</strong> <code>%2$s</code> was unable to %3$s.',
				$this->get_type_label(),
				$this->get_id(),
				$this->get_action()
			)
		);
	}

	/**
	 * Generate a WP_Error instance for the current item when an item is already imported
	 *
	 * @since 1.0.0
	 * @return WP_Error
	 */
	public function get_previously_imported_error() {
		return new WP_Error(
			sprintf( '%s-%s-failed', $this->get_type(), $this->get_action() ),
			sprintf(
				'<strong>%1$s</strong> <code>%2$s</code> was unable to %3$s. <strong>Duplicate detected.</strong>',
				$this->get_type_label(),
				$this->get_id(),
				$this->get_action()
			)
		);
	}

	/**
	 * Generate a WP_Error instance for the current item when a generic error occurs
	 *
	 * @since 1.0.0
	 * @return WP_Error
	 */
	public function get_not_found_error() {
		return new WP_Error(
			sprintf( '%s-%s-failed', $this->get_type(), $this->get_action() ),
			sprintf(
				'<strong>%1$s</strong> <code>%2$s</code> was unable to %3$s. <strong>Item not found.</strong>',
				$this->get_type_label(),
				$this->get_id(),
				$this->get_action()
			)
		);
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup_actions() {}

	/**
	 * Act on a specific item
	 *
	 * @since 1.0.0
	 * @param string $action The action to take
	 * @return mixed
	 */
	public function iterate( $action = 'import' ) {
		$this->set_action( $action );

		// allow things to happen before
		$this->iterate_actions( 'before' );

		// process
		$this->process();

		// allow things to happen after
		$this->iterate_actions( 'after' );

		return $this;
	}

	/**
	 * Process a specific item.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function process() {
		$action = $this->get_action();

		$result = $this->$action();

		$this->set_processed_item( $result );

		return $result;
	}

	/**
	 * Hooks for a single item process.
	 *
	 * @since 1.0.0
	 * @param string $when Context for before/after.
	 * @param string $what Context for the action being taken.
	 * @param array  $args
	 * @return void
	 */
	private function iterate_actions( $when ) {
		// general
		do_action( "astoundify_import_content_{$when}_{$this->get_action()}_item", $this );

		// type
		do_action( "astoundify_import_content_{$when}_{$this->get_action()}_item_type_{$this->get_type()}", $this );

		// object type
		if ( isset( $this->item['data']['post_type'] ) ) {
			$object_type = $this->item['data']['post_type'];

			do_action( "astoundify_import_content_{$when}_{$this->get_action()}_item_type_{$object_type}", $this );
		}

		// item
		do_action( "astoundify_import_content_{$when}_{$this->get_action()}_item_{$this->get_id()}", $this );
	}

	/**
	 * Set metadata for a specified object in WordPress
	 *
	 * @since 1.3.0
	 *
	 * @param string $meta_type The type of jobject the metadata is for
	 * @param array  $meta The list of meta keys and values to process
	 * @param object $object The previously imported item
	 */
	public function add_metadata( $meta_type, $meta, $object ) {
		$passed = true;

		// adjust object id if needed
		switch ( $meta_type ) {
			case 'term':
				$object_id = $object->term_id;
				break;
			case 'comment':
				$object_id = $object->comment_ID;
				break;
			default:
				$object_id = $object->ID;
		}

		foreach ( $meta as $k => $v ) {
			$k = $k;
			$v = $maybe_v = sanitize_meta( $k, $v, $meta_type );

			if ( is_string( $v ) ) {
				$maybe_v = array( $v );
			}

			// determine what we are returning
			$return = 'urls';
			$parts = explode( '|', $k );

			if ( count( $parts ) > 1 ) {
				$k = $parts[0];
				$return = $parts[1];
			}

			// potentially upload some assets
			foreach ( $maybe_v as $sub_k => $sub_v ) {
				if ( false === ( $asset = Astoundify_Utils::upload_asset( $sub_v, $object->ID ) ) ) {
					continue;
				}

				unset( $maybe_v[ $sub_k ] );

				switch ( $return ) {
					case 'urls':
					case 'url':
						$maybe_v[ $sub_k ] = wp_get_attachment_url( $asset );
						break;
					default:
						$maybe_v[ $sub_k ] = $asset;
				}
			}

			if ( is_string( $v ) ) {
				$maybe_v = $maybe_v[0];
			}

			$passed = add_metadata( $meta_type, $object_id, sanitize_key( $k ), $maybe_v, true );
		}// End foreach().

		return $passed;
	}

}
