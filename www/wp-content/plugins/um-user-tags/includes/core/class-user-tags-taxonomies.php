<?php
namespace um_ext\um_user_tags\core;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class User_Tags_Taxonomies
 * @package um_ext\um_user_tags\core
 */
class User_Tags_Taxonomies {


	/**
	 * User_Tags_Taxonomies constructor.
	 */
	function __construct() {
		add_action( 'init',  array( &$this, 'setup_taxonomy' ), 2 );
	}


	/**
	 * Setup taxonomy
	 */
	function setup_taxonomy() {

		$labels = array(
			'name'                       => __( 'User Tags', 'um-user-tags' ),
			'singular_name'              => __( 'User Tag', 'um-user-tags' ),
			'search_items'               => __( 'Search User Tags','um-user-tags' ),
			'popular_items'              => __( 'Popular User Tags','um-user-tags' ),
			'all_items'                  => __( 'All User Tags','um-user-tags' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit User Tag','um-user-tags' ),
			'update_item'                => __( 'Update User Tag','um-user-tags' ),
			'add_new_item'               => __( 'Add New User Tag','um-user-tags' ),
			'new_item_name'              => __( 'New User Tag Name','um-user-tags' ),
			'separate_items_with_commas' => __( 'Separate user tags with commas','um-user-tags' ),
			'add_or_remove_items'        => __( 'Add or remove user tags','um-user-tags' ),
			'choose_from_most_used'      => __( 'Choose from the most used user tags','um-user-tags' ),
			'not_found'                  => __( 'No user tags found.','um-user-tags' ),
			'menu_name'                  => __( 'User Tags','um-user-tags' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => false,
			'query_var'         => false,
			'rewrite'           => false,
			'hierarchical'      => true,
			'show_ui'		    => true,
			'show_in_menu'      => false,
			'update_count_callback' => 'user_tag_update_count_callback'
		);

		register_taxonomy( 'um_user_tag', '', $args );

		$is_first_setup = $this->is_first_setup();
		if ( $is_first_setup ) {

			$terms = get_terms( 'um_user_tag', array( 'fields' => 'ids', 'hide_empty' => false ) );
			if ( $terms ) {
				foreach ( $terms as $value ) {
					wp_delete_term( $value, 'um_user_tag' );
				}
			}
			$term1 = wp_insert_term( 'Interests', 'um_user_tag', array( 'slug' => 'interests' ) );
			$term2 = wp_insert_term( 'Skills', 'um_user_tag', array( 'slug' => 'skills' ) );

			wp_insert_term( 'Racing', 'um_user_tag', array( 'slug' => 'racing', 'parent' => $term1['term_id'] ) );
			wp_insert_term( 'Painting', 'um_user_tag', array( 'slug' => 'painting', 'parent' => $term1['term_id'] ) );
			wp_insert_term( 'Video gaming', 'um_user_tag', array( 'slug' => 'video-gaming', 'parent' => $term1['term_id'] ) );
			wp_insert_term( 'Blogging', 'um_user_tag', array( 'slug' => 'blogging', 'parent' => $term1['term_id'] ) );
			wp_insert_term( 'Reading', 'um_user_tag', array( 'slug' => 'reading', 'parent' => $term1['term_id'] ) );
			
			wp_insert_term( 'PHP', 'um_user_tag', array( 'slug' => 'php', 'parent' => $term2['term_id'] ) );
			wp_insert_term( 'HTML5', 'um_user_tag', array( 'slug' => 'html5', 'parent' => $term2['term_id'] ) );
			wp_insert_term( 'CSS', 'um_user_tag', array( 'slug' => 'css', 'parent' => $term2['term_id'] ) );
			wp_insert_term( 'jQuery', 'um_user_tag', array( 'slug' => 'jquery', 'parent' => $term2['term_id'] ) );
			wp_insert_term( 'MySQL', 'um_user_tag', array( 'slug' => 'mysql', 'parent' => $term2['term_id'] ) );

			// declare plugin as installed
			update_option('um_user_tags_defaults', 1);
		}
	}


	/**
	 * The first time to install this add-on?
	 *
	 * @return bool
	 */
	function is_first_setup() {
		if ( ! get_option( 'um_user_tags_defaults' ) ) {
			return true;
		}
		return false;
	}

}