<?php

class Jobify_Widgetized_Pages {

	private $transient;

	public function __construct() {
		$this->transient = 'jobify_widgetized_pages';

		add_action( 'save_post', array( $this, 'flush_cache' ) );
		add_action( 'widgets_init', array( $this, 'register_widget_areas' ), 99 );
	}

	public function is() {
		return
			is_page_template( 'page-templates/jobify.php' ) ||
			is_page_template( 'page-templates/template-widgetized.php' );
	}

	public function register_widget_areas() {
		$pages = $this->get_pages();

		if ( empty( $pages ) ) {
			return;
		}

		foreach ( $pages as $page ) {
			if ( ! get_post( $page ) ) {
				return;
			}

			register_sidebar( array(
				'name'          => sprintf( __( 'Page: %s', 'jobify' ), get_the_title( $page ) ),
				'description'   => sprintf( __( 'Widgets that appear on the "%s" page.', 'jobify' ), get_the_title( $page ) ),
				'id'            => 'widget-area-page-' . $page,
				'before_widget' => '<section id="%1$s" class="widget widget--home %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title widget-title--home">',
				'after_title'   => '</h3>',
			) );
		}
	}

	public function get_pages() {
		if ( false === ( $pages = get_transient( $this->transient ) ) ) {
			$pages = array();

			$query = new WP_Query( array(
				'fields' => 'ids',
				'nopaging' => true,
				'post_type' => 'page',
				'meta_query' => array(
					array(
						'key' => '_wp_page_template',
						'value' => array( 'page-templates/template-widgetized.php' ),
						'compare' => 'IN',
					),
				),
			) );

			if ( ! empty( $query->posts ) ) {
				$pages = $query->posts;
			}

			set_transient( $this->transient, $pages, HOUR_IN_SECONDS * 12 );
		}

		return $pages;
	}

	public function flush_cache() {
		delete_transient( $this->transient );
	}
}
