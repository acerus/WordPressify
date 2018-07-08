<?php
/**
 * Extra procsesing for Frontend Submissions
 *
 * @since 1.0.0
 */
class Astoundify_Plugin_FrontendSubmissions implements Astoundify_PluginInterface {

	/**
	 * Initialize the plugin processing
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		self::setup_actions();
	}

	/**
	 * Add any pre/post actions to processing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function setup_actions() {
		$pages = array( 'vendor', 'vendor-dashboard' );

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

		add_action(
			'astoundify_import_content_after_import_item_type_object',
			array( __CLASS__, 'set_form_settings' )
		);
	}

	/**
	 * Assign the relevant setting.
	 *
	 * @since 1.0.0
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function add_page_option( $ItemImport ) {
		edd_update_option( "fes-{$ItemImport->get_id()}-page", $ItemImport->get_processed_item()->ID );
	}

	/**
	 * Delete the relevant setting.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Import item context.
	 * @return void
	 */
	public static function delete_page_option( $ItemImport ) {
		edd_delete_option( "fes-{$ItemImport->get_id()}-page" );
	}

	/**
	 * Set form settings.
	 *
	 * @since 1.2.0
	 * @return true|WP_Error True if the terms can be set
	 */
	public static function set_form_settings( $ItemImport ) {
		$item_data = $ItemImport->item['data'];

		// do nothing if this is not relevant to the current object type
		if ( 'fes-forms' != $item_data['post_type'] ) {
			return false;
		}

		$error = new WP_Error(
			'set-form-settings',
			sprintf( 'Settings for %s was not set', $ItemImport->get_id() )
		);

		// only work with a valid processed object
		$object = $ItemImport->get_processed_item();

		if ( is_wp_error( $object ) ) {
			return $error;
		}

		$form = false;

		if ( isset( $item_data['form'] ) ) {
			$form = $item_data['form'];
		}

		if ( ! $form ) {
			return $error;
		}

		$edd_options = get_option( 'edd_settings' );
		$fes_form = isset( $edd_options['fes-submission-form'] ) ? $edd_options['fes-submission-form'] : false;

		// create the initial form from this object
		if ( ! $fes_form ) {
			update_post_meta( $object->ID, 'fes-form-name', 'submission' );
			update_post_meta( $object->ID, 'fes-form-type', 'post' );
			update_post_meta( $object->ID, 'fes-form-class', 'FES_Submission_Form' );

			// update the setting
			$fes_form = $edd_options['fes-submission-form'] = $object->ID;
		}

		update_post_meta( $fes_form, 'fes-form', $form );
		update_option( 'edd_settings', $edd_options );
	}
}

Astoundify_Plugin_FrontendSubmissions::init();
