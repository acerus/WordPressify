/**
 * Functions for ajaxified content importing inside the WordPress admin.
 *
 * @version 1.0.0
 *
 * @package Astoundify_ContentImporter
 */

/**
 * @param {jQuery} $ jQuery object.
 * @param {object} wp WP object.
 */
(function( $, wp ) {
	var $window = $( window );

	/**
	 * The Astoundify_ContentImporter object.
	 *
	 * @since 1.0.0
	 * @type {object}
	 */
	var Astoundify_ContentImporter = Astoundify_ContentImporter || {};

	/**
	 * Whether an import is currently processing.
	 *
	 * @since 1.0.0
	 * @type {bool}
	 */
	Astoundify_ContentImporter.importRunning = false;

	/**
	 * The form that triggers an import process.
	 *
	 * @todo Make this dynamic
	 *
	 * @since 1.0.0
	 * @type {bool}
	 */
	Astoundify_ContentImporter.$form = $( '#astoundify-content-importer' );

	/**
	 * Warn users they will cancel the import if they leave the page.
	 *
	 * @todo This string should be translatable.
	 */
	Astoundify_ContentImporter.beforeUnload = function() {
		if ( Astoundify_ContentImporter.importRunning ) {
			return 'Please do not leave while an import is in progress.';
		}
	};

	/**
	 * Stage an import process.
	 *
	 * @todo Make DOM traverssing more dynamic.
	 *
	 * - Hides any import groups that do not have any items
	 * - Adds an active spinner to groups that need processing
	 * - Resets the initial processed count to 0
	 * - Resets the total count to the new value
	 *
	 * @since 1.0.0
	 *
	 * @param {Array} groups
	 */
	Astoundify_ContentImporter.stageImport = function( groups ) {
		_.each( groups, function(items, type) {
			var total = items.length;

			if ( 0 === total ) {
				$( '#import-type-' + type ).hide();
			} else {
				Astoundify_ContentImporter.typeElement( type, 'spinner' ).addClass( 'is-active' );
				Astoundify_ContentImporter.typeElement( type, 'processed' ).text(0);
				Astoundify_ContentImporter.typeElement( type, 'total' ).text(total);
			}
		});
	};

	/**
	 * Run an import process.
	 *
	 * @todo Make DOM traverssing more dynamic.
	 *
	 * @since 1.0.0
	 *
	 * @param {Array} items
	 * @param {string} iterate_action
	 */
	Astoundify_ContentImporter.runImport = function( items, iterate_action ) {
		var $errors = $( '#import-errors' ).html( '' );
		var dfd = $.Deferred().resolve();
		var total_processed_count = 0;
		var total_to_process = items.length;
		var $stepTitle = $( '#step-status-import-content' );

		// notify that an import is running
		Astoundify_ContentImporter.importRunning = true;

		_.each(items, function(item) {
			dfd = dfd.then(function() {
				var type = item.type;
				var $processed = Astoundify_ContentImporter.typeElement( type, 'processed' );
				var $total = Astoundify_ContentImporter.typeElement( type, 'total' );

				args = {
					action: 'astoundify_importer_iterate_item',
					iterate_action: iterate_action,
					item: item
				};

				var request = $.ajax({
					type: 'POST',
					url: ajaxurl,
					data: args,
					dataType: 'json',

					/**
					 * @todo Split this out
					 */
					success: function(response) {
						// log error
						if ( response.success === false ) {
							$errors.show().prepend( '<li>' + response.data + '</li>' );
						}

						// update group info
						var processed_count = parseInt( $processed.text() );
						$processed.text( processed_count + 1);

						if ( $processed.text() == $total.text() ) {
							Astoundify_ContentImporter.typeElement( type, 'spinner' ).removeClass( 'is-active' );
						}

						// update action buttons and step title
						total_processed_count = total_processed_count + 1;

						if ( total_processed_count == total_to_process ) {
							// notify that the importer is done
							Astoundify_ContentImporter.importRunning = false;

							if ( 'import' == iterate_action ) {
								$stepTitle.text( $stepTitle.data( 'string-complete' ) ).removeClass( 'step-incomplete' ).addClass( 'step-complete' );
							} else {
								$stepTitle.text( $stepTitle.data( 'string-incomplete' ) ).removeClass( 'step-complete' ).addClass( 'step-incomplete' );
							}
						}
					}
				});

				return request;
			});
		});
	};

	/**
	 * Get an import group type element.
	 *
	 * @since 1.0.0
	 * 
	 * @param {string} type
	 * @param {string} element
	 */
	Astoundify_ContentImporter.typeElement = function( type, element ) {
		return $( '#' + type + '-' + element );
	};

	/**
	 * All packs
	 *
	 * @since 3.3.0
	 * @type string
	 */
	Astoundify_ContentImporter.packs = $( '#content-pack' ).find( 'input[name="demo_style"]' );

	Astoundify_ContentImporter.toggleRecommendedPlugins = function() {
		var pack = Astoundify_ContentImporter.packs.filter( ':checked' ).val();

		$( '#astoundify-recommended-plugins li' ).hide().filter( function() {
			var showFor = $(this).data( 'pack' ).split( ' ' );

			return showFor.indexOf( pack ) != -1;
		} ).show();
	};

	/**
	 * Alert users before leaving the page
	 *
	 * @since 1.0.0
	 */
	$window.bind( 'beforeunload', Astoundify_ContentImporter.beforeUnload );

	$(document).on( 'wp-updates-astoundify-plugininstaller-plugin-activate-success', function( event, response ) {
		var slug = response.slug;

		// correct some slugs
		if ( 'wp-job-manager' == slug ) {
			slug = 'wp-job-manager-base';
		}

		if ( 'wp-job-manager-locations' == slug ) {
			slug = 'wp-job-manager-regions';
		}

		$( '#plugins-to-import' ).find( '#' + slug ).removeClass( 'inactive' ).addClass( 'active' );
	} );

	/**
	 * Bind actions to DOM
	 *
	 * @since 1.0.0
	 */
	jQuery(document).ready(function($) {

		/**
		 * Content Packs
		 *
		 * @since 1.2.0
		 */
		Astoundify_ContentImporter.toggleRecommendedPlugins();

		Astoundify_ContentImporter.packs.on( 'change', function() {
			Astoundify_ContentImporter.toggleRecommendedPlugins();
		});

		/**
		 * Prevent the form event.
		 *
		 * @since 1.0.0
		 */
		Astoundify_ContentImporter.$form.on( 'submit', function(e) {
			e.preventDefault();
		});

		/**
		 * When a processing action buttin is clicked perform an action.
		 *
		 * @since 1.0.0
		 */
		Astoundify_ContentImporter.$form.find( 'input[type=submit]' ).on( 'click', function(e) {
			e.preventDefault();

			var $button = $(this);

			var args = {
				action: 'astoundify_content_importer',
				security: astoundifyContentImporter.nonces.stage,
				style: $( 'input[name=demo_style]:checked' ).val()
			};

			return $.ajax({
				type: 'POST',
				url: ajaxurl, 
				data: args, 
				dataType: 'json',
				success: function(response) {
					if ( response.success ) {
						$( '#plugins-to-import' ).hide();
						$( '#import-summary' ).show();

						groups = response.data.groups;
						items = response.data.items;
						
						// these should be callbacks
						Astoundify_ContentImporter.stageImport( groups );
						Astoundify_ContentImporter.runImport( items, $button.attr( 'name' ) );
					} else {
						$( '#plugins-to-import' ).hide();
						$( '#import-errors' ).show().html( '<li>' + response.data + '</li>' );
					}
				}
			});
		});
	});

})( jQuery, window.wp );
