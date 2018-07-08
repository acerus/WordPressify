<?php
/**
 * Customize API: Astoundify_ThemeCustomizer_TermMetaSetting class
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.2.0
 */

/**
 * Customize Setting to represent term meta.
 *
 * Subclass of WP_Customize_Setting to represent an arbitrary taxonomy term
 *
 * @since 1.2.0
 *
 * @see wp_get_nav_menu_object()
 * @see WP_Customize_Setting
 */
class Astoundify_ThemeCustomizer_TermMetaSetting extends WP_Customize_Setting {

	const SETTING_ID_PATTERN = '/(.*)\[(?P<taxonomy>[^\]]+)\]\[(?P<term_id>\d+)\]\[(?P<meta_key>.+)\]$/';

	const TYPE = 'termmeta';

	/**
	 * Type of setting.
	 *
	 * @access public
	 * @var string
	 */
	public $type = self::TYPE;

	/**
	 * Taxonomy.
	 *
	 * @access public
	 * @var string
	 */
	public $taxonomy;

	/**
	 * Term ID.
	 *
	 * @access public
	 * @var string
	 */
	public $term_id;

	/**
	 * Meta key.
	 *
	 * @access public
	 * @var string
	 */
	public $meta_key;

	/**
	 * Whether the value is mapped to a single postmeta row.
	 *
	 * If false, the value is expected to be an array and mapped to multiple postmeta rows.
	 *
	 * @todo This should be automatically sniffed from get_registered_meta_keys() since register_meta() now includes a 'single' param.  See https://github.com/xwp/wp-customize-posts/pull/232
	 *
	 * @var bool
	 */
	public $single = true;

	/**
	 * Posts component.
	 *
	 * @access public
	 * @var WP_Customize_Posts
	 */
	public $posts_component;

	/**
	 * WP_Customize_Post_Setting constructor.
	 *
	 * @access public
	 *
	 * @param WP_Customize_Manager $manager Manager.
	 * @param string               $id      Setting ID.
	 * @param array                $args    Setting args.
	 * @throws Exception If the ID is in an invalid format.
	 */
	public function __construct( WP_Customize_Manager $manager, $id, $args = array() ) {
		if ( ! preg_match( self::SETTING_ID_PATTERN, $id, $matches ) ) {
			throw new Exception( 'Illegal setting id: ' . $id );
		}
		$args['term_id'] = intval( $matches['term_id'] );
		$args['taxonomy'] = $matches['post_type'];
		$args['meta_key'] = $matches['meta_key'];
		$taxonomy_obj = get_taxonomy( $args['taxonomy'] );
		if ( ! $taxonomy_obj ) {
			throw new Exception( 'Unrecognized taxonomy: ' . $args['taxonomy'] );
		}

		if ( ! $this->single || ( isset( $args['single'] ) && false === $args['single'] ) ) {
			if ( '' === $this->default ) {
				$this->default = array();
			}

			$args['default'] = array();
		}

		if ( empty( $args['capability'] ) ) {
			$args['capability'] = 'edit_terms';
		}

		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Get setting ID for a given postmeta.
	 *
	 * @access public
	 *
	 * @param WP_Post $post     Post.
	 * @param string  $meta_key Meta key.
	 * @return string Setting ID.
	 */
	static function get_term_meta_setting_id( $taxonomy, $term_id, $meta_key ) {
		return sprintf( 'termmeta[%s][%d][%s]', $taxonomy, $term_id, $meta_key );
	}

	/**
	 * Return a post's setting value.
	 *
	 * @access public
	 *
	 * @return mixed Meta value.
	 */
	public function value() {
		$meta_key = $this->meta_key;
		$object_id = $this->term_id;
		$single = false; // For the sake of disambiguating empty values in filtering.
		$values = get_term_meta( $object_id, $meta_key, $single );

		if ( $this->single ) {
			$value = array_shift( $values );
			if ( ! isset( $value ) ) {
				$value = $this->default;
			}
			return $value;
		} else {
			return $values;
		}
	}

	/**
	 * Sanitize (and validate) an input.
	 *
	 * Note for non-single postmeta, the validation and sanitization callbacks will be applied on each item in the array.
	 *
	 * @see update_metadata()
	 * @access public
	 *
	 * @param string $meta_value The value to sanitize.
	 * @return mixed|WP_Error|null Sanitized post array or WP_Error if invalid (or null if not WP 4.6-alpha).
	 */
	public function sanitize( $meta_value ) {
		$has_setting_validation = method_exists( 'WP_Customize_Setting', 'validate' );

		$meta_type = 'post';
		$object_id = $this->post_id;
		$meta_key = $this->meta_key;
		$prev_value = ''; // Updating plural meta is not supported.

		if ( $this->single ) {
			$values = array( $meta_value );
		} else {
			if ( ! is_array( $meta_value ) ) {
				return $has_setting_validation ? new WP_Error( 'expected_array', sprintf( __( 'Expected array value for non-single "%s" meta.', 'jobify' ), $meta_key ) ) : null;
			}
			$values = $meta_value;
		}

		if ( $this->single ) {
			return array_shift( $values );
		} else {
			return $values;
		}
	}

	/**
	 * Flag this setting as one to be previewed.
	 *
	 * Note that the previewing logic is handled by WP_Customize_Posts_Preview.
	 *
	 * @access public
	 *
	 * @return bool
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return true;
		}

		$this->is_previewed = true;

		return true;
	}

	/**
	 * Update the post.
	 *
	 * Please note that the capability check will have already been done.
	 *
	 * @see WP_Customize_Setting::save()
	 *
	 * @param string $meta_value The value to update.
	 * @return bool The result of saving the value.
	 */
	protected function update( $meta_value ) {
		if ( $this->single ) {
			$result = update_term_meta( $this->term_id, $this->meta_key, $meta_value );

			return ( false !== $result );
		} else {
			if ( ! is_array( $meta_value ) ) {
				return false;
			}

			// Non Serialized $meta_value Sync to reduce SQL overhead.
			$meta_update = get_term_meta( $this->term_id, $this->meta_key, false );

			$delete = array_diff( $meta_update, $meta_value );

			if ( ! empty( $delete ) ) {
				$delete = array_values( $delete );
			}

			$add = array_diff( $meta_value, $meta_update );

			if ( ! empty( $add ) ) {
				$add = array_values( $add );
			}

			$delete_count = count( $delete );
			$add_count = count( $add );

			// Update is faster than delete + insert (SQL).
			for ( $i = 0; $i < $delete_count && $i < $add_count; $i ++ ) {
				update_term_meta( $this->term_id, $this->meta_key, $add[ $i ], $delete[ $i ] );

				unset( $add[ $i ], $delete[ $i ] );
			}

			// Delete if not updated.
			foreach ( $delete as $id ) {
				delete_term_meta( $this->term_id, $this->meta_key, $id );
			}

			// Add if not updated.
			foreach ( $add as $item ) {
				add_term_meta( $this->term_id, $this->meta_key, $item, false );
			}

			return true;
		}// End if().
	}

}
