<?php
/**
 * Import a comment
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.1.0
 */
class Astoundify_ItemImport_Comment extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function setup_actions() {
		// add extra object components
		$actions = array(
			'set_comment_meta'
		);

		foreach ( $actions as $action ) {
			$tag = 'astoundify_import_content_after_import_item_type_comment';

			if ( ! has_action( $tag, array( $this, $action ) ) ) {
				add_action( $tag, array( $this, $action ) );
			}
		}
	}

	/**
	 * Import a single item
	 *
	 * @since 1.1.0
	 * @return (WP_Post|WP_Error)
	 */
	public function import() {
		if ( $this->get_previous_import() ) {
			return $this->get_previously_imported_error();
		}

		$defaults = array(
			'comment_parent' => 0,
			'comment_approved' => 1,
		);

		$comment_atts = wp_parse_args( $this->item['data'], $defaults );

		$comment_id = wp_insert_comment( $comment_atts );

		$result = $this->get_default_error();

		if ( $comment_id && 0 !== $comment_id ) {
			$result = get_comment( $comment_id );
		}

		return $result;
	}

	/**
	 * Reset a single item
	 *
	 * This does not need to anything because resetting the parent object
	 * will automatically remove all comments.
	 *
	 * @since 1.1.0
	 * @see delete_post()
	 * @return WP_Error|WP_Post
	 */
	public function reset() {
		return true;
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.1.0
	 * @uses $wpdb
	 * @return mixed Object ID if found or false.
	 */
	public function get_previous_import() {
		global $wpdb;

		$comment = $wpdb->get_row( $wpdb->prepare(
			"SELECT comment_ID FROM $wpdb->comments WHERE comment_content = '%s'",
			$this->item['data']['comment_content']
		) );

		if ( null == $comment ) {
			return false;
		}

		return $comment;
	}

	/**
	 * Set comment meta
	 *
	 * @since 1.1.0
	 * @return true|WP_Error True if all meta can be set
	 */
	public function set_comment_meta() {
		$error = new WP_Error(
			'set-comment-meta',
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

		return $this->add_metadata( 'comment', $meta, $object ) ? true : $error;
	}

}
