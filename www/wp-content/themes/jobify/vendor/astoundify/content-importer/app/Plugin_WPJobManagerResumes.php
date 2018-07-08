<?php
/**
 * Extra procsesing for WP Job Manager - Resume Manager
 *
 * @since 1.2.0
 */
class Astoundify_Plugin_WPJobManagerResumes implements Astoundify_PluginInterface {

	/**
	 * Initialize the plugin processing
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function init() {
		self::setup_actions();
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function setup_actions() {
		$pages = array( 'resumes', 'submit_resume_form', 'candidate_dashboard' );

		foreach ( $pages as $page ) {
			add_action(
				'astoundify_import_content_after_import_item_' . $page,
				array( __CLASS__, 'add_page_option' )
			);

			add_action(
				'astoundify_import_content_after_reset_item_' . $page,
				array( __CLASS__, 'delete_page_option' )
			);
		}
	}

	/**
	 * Assign the relevant setting.
	 *
	 * @since 1.2.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function add_page_option( $ItemImport ) {
		update_option( "resume_manager_{$ItemImport->get_id()}_page_id", $ItemImport->get_processed_item()->ID );
	}

	/**
	 * Delete the relevant setting.
	 *
	 * @since 1.2.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function delete_page_option( $ItemImport ) {
		delete_option( "resume_manager_{$ItemImport->get_id()}_page_id" );
	}

}

Astoundify_Plugin_WPJobManagerResumes::init();
