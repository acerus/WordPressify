/**
 * WP Job Manager - Favorites
 *
 * @since 3.6.0
 */
(function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;
	var wp = window.wp;

	/**
	 * @since 1.0.0
	 */
	var $document = $(document);

	function openModal( el ) {
		var $button = el;

		var $form = $button.closest( $( '.wp-job-manager-favorites-form' ) );
		var $place = $form.parent();
		var detached = $form.detach();

		var src = $( '<div class="modal ' + $form.attr( 'class' ).replace( 'job-manager-form wp-job-manager-favorites-form', '' ) + '"><h2 class="modal-title">' + el.html() + '</h2></div>' ).append( detached );

		var $popup = $.magnificPopup.open({
			type: 'inline',
			fixedContentPos: false,
			fixedBgPos: true,
			overflowY: 'scroll',
			items: {
				src: src
			},
			callbacks: {
				close: function() {
					$place.append( detached );
				}
			}
		});
	}
	
	/**
	 * Wait for DOM ready.
	 *
	 * @since 1.0.0
	 */
	$document.ready(function() {

		if ( $( '.wpjmf-logged-out-notice' ).length ) {
			$( '.wpjmf-logged-out-notice .favorite-notice' ).addClass( 'popup-trigger-ajax' ).unbind( 'click' ).on( 'click', function() {

				/**
				 * Unfortunately this all needs to be duplicated currently.
				 */
				Jobify.App.popup({
					items: {
						src: $(this).attr( 'href' ),
						type: 'ajax'
					},
					callbacks: {
						parseAjax: function(mfpResponse) {
							mfpResponse.data = 
							'<div class="modal">' + 
								'<h2 class="modal-title">' + $(mfpResponse.data).find( '.page-title' ).text() + '</h2>' + 
								$(mfpResponse.data).find( 'article.hentry' ).html() + 
							'</div>';
						},
					}
				});

			} );
		} else {
			$( '.wp-job-manager-favorites-form .favorite-notice' ).unbind( 'click' ).on( 'click', function( e ) {
				e.preventDefault();

				openModal( $(this) );
			});
		}

	});

}( window ));
