<?php
/**
 * Import an object
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.0.0
 */
class Astoundify_ItemImport_Object extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	public function __construct( $item ) {
		parent::__construct( $item );
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup_actions() {
		// add extra object components
		$actions = array(
			'inline_assets',
			'set_parent',
			'set_post_format',
			'set_featured_image',
			'set_post_meta',
			'set_post_terms',
			'set_post_media',
			'set_menu_item',
			'add_comments',
		);

		foreach ( $actions as $action ) {
			$tag = 'astoundify_import_content_after_import_item_type_object';

			if ( ! has_action( $tag, array( $this, $action ) ) ) {
				add_action( $tag, array( $this, $action ) );
			}
		}

		// remove attachments
		add_action(
			'astoundify_import_content_after_reset_item_type_object',
			array( $this, 'delete_attachments' )
		);

		// actually delete the post
		add_action(
			'astoundify_import_content_after_reset_item_type_object',
			array( $this, 'delete_post' ),
			99
		);

		// set homepage and blog
		add_action(
			'astoundify_import_content_after_import_item_home',
			array( $this, 'set_page_on_front' )
		);

		add_action(
			'astoundify_import_content_after_import_item_blog',
			array( $this, 'set_page_for_posts' )
		);
	}

	/**
	 * Import a single item
	 *
	 * @since 1.0.0
	 * @return (WP_Post|WP_Error)
	 */
	public function import() {
		if ( $this->get_previous_import() ) {
			return $this->get_previously_imported_error();
		}

		if ( ! isset( $this->item['data']['post_content'] ) ) {
			$this->item['data']['post_content'] = Astoundify_Utils::get_lipsum_content();
		} elseif ( filter_var( $this->item['data']['post_content'], FILTER_VALIDATE_URL ) ) {
			$this->item['data']['post_content'] = Astoundify_Utils::get_lipsum_content( $this->item['data']['post_content'] );
		}

		$defaults = array(
			'post_type' => 'object' == $this->get_type() ? 'post' : $this->item['data']['post_type'],
			'post_status' => 'publish',
			'post_name' => $this->get_id(),
		);

		$object_atts = wp_parse_args( $this->item['data'], $defaults );

		$object_id = wp_insert_post( $object_atts );

		$result = $this->get_default_error();

		if ( $object_id && 0 !== $object_id ) {
			$result = get_post( $object_id );
		}

		return $result;
	}

	/**
	 * Reset a single item
	 *
	 * This actually does not reset anything as it will destroy and
	 * relationships with parent or child items. Instead we actually reset
	 * in a post processing action that fires at the very end.
	 *
	 * @since 1.0.0
	 * @see delete_post()
	 * @return WP_Error|WP_Post
	 */
	public function reset() {
		$object = $this->get_previous_import();

		if ( ! $object ) {
			return $this->get_not_found_error();
		}

		return get_post( $object->ID );
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.0.0
	 * @uses $wpdb
	 * @return mixed Object ID if found or false.
	 */
	public function get_previous_import() {
		global $wpdb;

		if ( ! isset( $this->item['data'] ) || ! isset( $this->item['data']['post_type'] ) ) {
			$this->item['data']['post_type'] = 'post';
		}

		$post_name = $this->item['id'];

		if ( isset( $this->item['data']['post_name'] ) ) {
			$post_name = $this->item['data']['post_name'];
		}

		$object = $wpdb->get_row( $wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE post_name = '%s' AND post_type = '%s'",
			$post_name,
			$this->item['data']['post_type']
		) );

		if ( null == $object ) {
			return false;
		}

		return $object;
	}

	/**
	 * Delete the object.
	 *
	 * This happens at the very end so we have access to the full object
	 * and no related objects are altered. If this is deleted first all
	 * children (including attachments) get orphaned and cannot be accessed.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error
	 */
	public function delete_post() {
		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $this->get_not_found_error();
		}

		$result = wp_delete_post( $object->ID, true );

		if ( ! $result ) {
			return $this->get_default_error();
		}

		return $result;
	}

	/**
	 * When an object is reset all attachments should be removed as well.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if attachments are removed.
	 */
	public function delete_attachments() {
		global $wpdb;

		$error = new WP_Error(
			'delete-attachments',
			sprintf( 'Attachments for %s not deleted', $this->get_id() )
		);

		$attachments = $wpdb->get_results( $wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_parent = '%s'",
			$this->get_processed_item()->ID
		) );

		// this isn't really an error
		if ( empty( $attachments ) ) {
			return true;
		}

		$passed = true;

		foreach ( $attachments as $attachment ) {
			if ( false === wp_delete_attachment( $attachment->ID, true ) ) {
				$passed = false;
			}
		}

		if ( $passed ) {
			return true;
		}

		return $error;
	}

	/**
	 * Extract inline assets and download.
	 *
	 * @since 1.2.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public function inline_assets() {
		$error = new WP_Error(
			'set-inline-assets',
			sprintf( 'Inline assets for %s not converted.', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$urls = wp_extract_urls( $object->post_content );

		if ( empty( $urls ) ) {
			return;
		}

		$replaced = array();

		foreach ( $urls as $url ) {
			if ( false !== ( $asset = Astoundify_Utils::upload_asset( $url, $object->ID ) ) ) {
				$replaced[] = wp_get_attachment_url( $asset );
			}
		}

		if ( ! empty( $replaced ) ) {
			$content = str_replace( $urls, $replaced, $object->post_content );

			wp_update_post( array(
				'ID' => $object->ID,
				'post_content' => $content,
			) );

			return true;
		}

		return;
	}

	/**
	 * Set the object's parent
	 *
	 * @since 1.1.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public function set_parent() {
		$error = new WP_Error(
			'set-post-parent',
			sprintf( 'Parent for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$parent = false;

		if ( isset( $this->item['data']['post_parent'] ) ) {
			global $wpdb;

			$parent_name = $this->item['data']['post_parent'];

			$parent = $wpdb->get_row( $wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_name = '%s' AND post_type = '%s'",
				$parent_name,
				$this->item['data']['post_type']
			) );
		}

		if ( ! $parent ) {
			return $error;
		}

		return wp_update_post( array(
			'ID' => $object->ID,
			'post_parent' => $parent->ID,
		), $error );
	}

	/**
	 * Set the object's format
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public function set_post_format() {
		$error = new WP_Error(
			'set-post-format',
			sprintf( 'Format for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$format = false;

		if ( isset( $this->item['data']['post_format'] ) ) {
			$format = $this->item['data']['post_format'];
		}

		if ( ! $format ) {
			return $error;
		}

		$format = esc_attr( $format );

		if ( post_type_supports( $object->post_type, 'post-formats' ) ) {
			return set_post_format( $object->ID, $format );
		}

		return $error;
	}

	/**
	 * Set the featured image
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the format can be set.
	 */
	public function set_featured_image() {
		$error = new WP_Error(
			'set-post-featured-image',
			sprintf( 'Featured image for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$featured_image = false;

		if ( isset( $this->item['data']['featured_image'] ) ) {
			$featured_image = $this->item['data']['featured_image'];
		}

		if ( ! $featured_image ) {
			return $error;
		}

		$featured_image = esc_url( $featured_image );

		$image_id = Astoundify_Utils::upload_asset( $featured_image, $object->ID );

		if ( $image_id ) {
			return set_post_thumbnail( $object->ID, $image_id );
		}

		return $error;
	}

	/**
	 * Set post meta
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if all meta can be set
	 */
	public function set_post_meta() {
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

		return $this->add_metadata( 'post', $meta, $object ) ? true : $error;
	}

	/**
	 * Set post terms
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the terms can be set
	 */
	public function set_post_terms() {
		$error = new WP_Error(
			'set-post-terms',
			sprintf( 'Terms for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$terms = false;

		if ( isset( $this->item['data']['terms'] ) ) {
			$terms = $this->item['data']['terms'];
		}

		if ( ! $terms ) {
			return $error;
		}

		$passed = true;

		foreach ( $terms as $tax => $terms ) {
			if ( ! taxonomy_exists( $tax ) ) {
				$passed = false;
				continue;
			}

			$passed = wp_set_object_terms( $object->ID, $terms, $tax, false );
		}

		if ( $passed && ! is_wp_error( $passed ) ) {
			return true;
		}

		return $error;
	}

	/**
	 * Set post media
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the media was added
	 */
	public function set_post_media() {
		$error = new WP_Error(
			'set-post-media',
			sprintf( 'Media for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$media = false;

		if ( isset( $this->item['data']['media'] ) ) {
			$media = $this->item['data']['media'];
		}

		if ( ! $media ) {
			return $error;
		}

		$passed = true;

		foreach ( $media as $file ) {
			$passed = Astoundify_Utils::upload_asset( $file, $object->ID );
		}

		if ( $passed ) {
			return true;
		}

		return $error;
	}

	/*
	 * Set menu item
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the media was added
	 */
	public function set_menu_item() {
		$error = new WP_Error(
			'set-menu-item',
			sprintf( 'Menu item for %s was not set', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$menus = false;

		if ( isset( $this->item['data']['menus'] ) ) {
			$menus = $this->item['data']['menus'];
		}

		if ( ! $menus ) {
			return $error;
		}

		$passed = true;

		foreach ( $menus as $menu => $args ) {
			if ( ! isset( $args['menu-item-title'] ) ) {
				$args['menu-item-title'] = $object->post_title;
			}

			$args['menu-item-object'] = $object->post_type;
			$args['menu-item-object-id'] = $object->ID;
			$args['menu-item-type'] = 'post_type';

			if ( ! is_numeric( $menu ) ) {
				$args['menu_name'] = $menu;
			}

			// mock out a menu item that can be imported
			$item = array(
				'id' => $this->get_id() . '-nav-menu-item',
				'type' => 'nav-menu-item',
				'data' => $args,
			);

			$item = new Astoundify_ItemImport_NavMenuItem( $item );
			$passed = $item->iterate( 'import' );
		}

		if ( $passed ) {
			return true;
		}

		return $error;
	}

	/**
	 * Add comments
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if all meta can be set
	 */
	public function add_comments( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		if ( ! isset( $item_data['comments'] ) ) {
			return;
		}

		$error = new WP_Error(
			'set-location',
			sprintf( 'Location for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$passed = true;
		$comments = $item_data['comments'];

		foreach ( $comments as $key => $comment_data ) {
			$comment_data = array_merge( array(
				'comment_post_ID' => $object->ID,
			), $comment_data );

			$item = array(
				'id' => sprintf( 'comment-%d-%d', $object->ID, $key ),
				'type' => 'comment',
				'data' => $comment_data,
			);

			$item = new Astoundify_ItemImport_Comment( $item );

			$passed = ! is_wp_error( $item->iterate( 'import' ) );
		}

		return $passed;
	}

	/**
	 * Set the homepage.
	 *
	 * When an item with a key of `home` is processed.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the media was added
	 */
	public function set_page_on_front() {
		$error = new WP_Error(
			'set-homepage',
			sprintf( 'Page %s was not set as homepage', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$post_id = $object->ID;

		if ( $post_id ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $post_id );

			return true;
		}

		return $error;
	}

	/**
	 * Set the blog.
	 *
	 * When an item with a key of `blog` is processed.
	 *
	 * @since 1.0.0
	 * @return true|WP_Error True if the media was added
	 */
	public function set_page_for_posts() {
		$error = new WP_Error(
			'set-blog',
			sprintf( 'Page %s was not set as blog', $this->get_id() )
		);

		// only work with a valid processed object
		$object = $this->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$post_id = $object->ID;

		if ( $post_id ) {
			update_option( 'page_for_posts', $post_id );

			return true;
		}

		return $error;
	}

}
