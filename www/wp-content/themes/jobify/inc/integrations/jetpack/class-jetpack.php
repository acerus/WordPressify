<?php
/**
 * Jetpack
 *
 * @package Jobify
 * @category Integration
 * @since 3.0.0
 */
class Jobify_Jetpack extends Jobify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {}

	public function setup_actions() {
		add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
		add_action( 'wp_head', array( $this, 'loop_start' ) );
		add_action( 'jobify_share_object', array( $this, 'output' ) );
		add_filter( 'sharing_enqueue_scripts', array( $this, 'sharing_enqueue_scripts' ) );
	}

	/**
	 * Add various theme support for some Jetpack goodness.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function add_theme_support() {
		add_theme_support( 'jetpack-responsive-videos' );
	}

	/**
	 * Remove the default sharing output so we can have total control.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function loop_start() {
		// we have custom output on these pages
		if ( is_singular( array( 'job_listing', 'resume', 'post' ) ) ) {
			remove_filter( 'the_content', 'sharing_display', 19 );
			remove_filter( 'the_excerpt', 'sharing_display', 19 );

			if ( class_exists( 'Jetpack_Likes' ) ) {
				remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
			}
		}
	}

	/**
	 * Sharing Output
	 *
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	public function output() {
		global $post;

		if ( ! function_exists( 'sharing_display' ) ) {
			return;
		}

		$buttons = sharing_display( '' );

		if ( '' == $buttons ) {
			return;
		}

		echo $buttons;
	}

	/**
	 * If we are on the Submit/Preview step of submitting a job enqueue
	 * the Jetpack assets.
	 *
	 * @since 3.5.0
	 *
	 * @param bool
	 * @return bool
	 */
	public function sharing_enqueue_scripts( $enqueue ) {
		if ( is_page( get_option( 'job_manager_submit_page_id' ) ) ) {
			$enqueue = true;
		}

		return $enqueue;
	}

}
