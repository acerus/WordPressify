<?php

class Jobify_WP_Job_Manager_Spotlight {

	private $transient_key;

	public function __construct() {
		add_action( 'save_post', array( $this, 'flush_featured_cache' ), 10, 2 );
	}

	public function get( $args = array() ) {
		$defaults = array(
			'number' => 1,
			'post_type' => 'job_listing',
		);

		$args = wp_parse_args( $args, $defaults );
		$featured = $this->_get_featured_ids( $args );

		shuffle( $featured );

		if ( empty( $featured ) ) {
			$featured = array( -1 );
		}

		$query_args = array(
			'post__in' => $featured,
			'post_type' => $args['post_type'],
			'post_status' => 'publish',
			'posts_per_page' => $args['number'],
			'orderby' => 'post__in',
			'no_found_rows' => true,
		);

		if ( 'job_listing' === $args['post_type'] && 1 === absint( get_option( 'job_manager_hide_filled_positions' ) ) ) {
			$query_args['meta_query'][] = array(
				'key'     => '_filled',
				'value'   => '1',
				'compare' => '!=',
			);
		}

		$featured = new WP_Query( $query_args );

		return $featured;
	}

	private function _get_featured_ids( $args ) {
		$this->transient_key = 'featured_' . $args['post_type'];

		if ( false === ( $featured = get_transient( $this->transient_key ) ) ) {
			$featured = new WP_Query( array(
				'post_type' => $args['post_type'],
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key'   => '_featured',
						'value' => 1,
					),
				),
				'nopaging' => true,
				'no_found_rows' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'fields' => 'ids',
			) );

			$featured = $featured->posts;

			set_transient( $this->transient_key, $featured, DAY_IN_SECONDS * 7 );
		}

		return $featured;
	}

	public function flush_featured_cache( $post_id, $post ) {
		if ( 'job_listing' == $post->post_type ) {
			delete_transient( 'featured_job_listing' );
		} elseif ( 'resume' == $post->post_type ) {
			delete_transient( 'featured_resume' );
		}
	}

}
