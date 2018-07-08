/**
 * Widget Media (Admin).
 *
 * @since 3.8.0
 */
(function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;

	/**
	 * Bind items to to the DOM.
	 *
	 * @since 2.2.0
	 */
	$(function() {

		// Open media modal.
		$( document ).on( 'click', '.widget-jobify-media-open', function(e){
			e.preventDefault();

			var this_button = $( this );

			// If media frame doesn't exist, create it with some options.
			var media_frame = wp.media.frames.file_frame = wp.media({
				className: 'media-frame jobify-media-frame',
				frame: 'select',
				title: this_button.data( 'title' ),
				button: { text:  this_button.data( 'insert' ) },
				multiple: false,
			});

			// Insert URL.
			media_frame.on( 'select', function(){
				var this_attachment = media_frame.state().get('selection').first().toJSON();
				this_button.siblings( 'input[type="url"]' ).val( this_attachment.url ).trigger( 'change' );
			});

			// Open frame.
			media_frame.open();
		});

		// Clear input.
		$( document ).on( 'click', '.jobify-widget-media-clear', function(e){
			$( this ).siblings( 'input[type="url"]' ).val( '' ).trigger( 'change' );
		});

	});

})( window );
