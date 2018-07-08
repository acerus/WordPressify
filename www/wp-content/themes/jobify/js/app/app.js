/**
 * Functionality specific to Jobify
 *
 * Provides helper functions to enhance the theme experience.
 */

var Jobify = {}

Jobify.App = ( function($) {
	var currentPopup;
	
	function fixedHeader() {
		$window = $(window);

		var offsetHeader = function() {
			var $body = $( 'body' );

			if ( ! $body.hasClass( 'fixed-header' ) ) {
				return;
			}

			var $header = $( '#masthead' );
			var headerHeight = $header.outerHeight();
			var headerOffset = 0;

			if ( $body.hasClass( 'admin-bar' ) ) {
				headerOffset = $( '#wpadminbar' ).outerHeight();
			}

			if ( $window.outerWidth() < 1200 ) {
				$body.css( 'padding-top', 0 );
				$header.css( 'top', 0 );

				return;
			}

			$header.css( 'top', headerOffset );
			$body.css( 'padding-top', headerHeight );
		}

		offsetHeader();

		$window.resize(function() {
			offsetHeader();
		});
	}

	function mobileMenu() {
		$( '.js-primary-menu-toggle' ).click(function(e){
			e.preventDefault();

			$( '.js-primary-menu-toggle' ).toggleClass( 'primary-menu-toggle--opened' );

			$( '.site-primary-navigation' ).toggleClass( 'site-primary-navigation--open' );
		});

		var resizeWindow = function() {
			if ( ! $( '.primary-menu-toggle' ).is( ':visible' ) ) {
				$( '.site-primary-navigation' ).removeClass( 'site-primary-navigation--open' );
			}
		}

		resizeWindow();

		$(window).resize(function() {
			resizeWindow();
		});
	}

	function equalHeights( elements ) {
		var tallest = 0;

		$.each( elements, function(key, elements) {
			$.each( elements, function() {
				if ( $(this).outerHeight() > tallest ) {
					tallest = $(this).outerHeight();
				}
			});

			$(elements).css( 'height', tallest );

			if ( $(window).width() < 992 ) {
				$(elements).css( 'height', 'auto' );
			}

			tallest = 0;
		});
	}

	function initSelects() {
		var avoid = [
			'.country_select',
			'.state_select',
			'.feedFormField',
			'.job-manager-category-dropdown[multiple]',
			'.job-manager-multiselect',
			'#search_categories',
			'.search_region',
			'.comment-form-rating #rating',
			'.iti-mobile-select',
			'#job_region'
		];

		$( 'select' ).each(function() {
			if ( $(this).parent().hasClass( 'select' ) ) {
				return;
			}

			if ( $(this).is( avoid.join( ',' ) ) ) {
				return;
			}

			var existingClass = null;

			if ( $(this).attr( 'class' ) ) {
				var existingClass = $(this).attr( 'class' ).split(' ')[0];
			}

			$(this)
				.wrap( '<span class="select ' + existingClass + '-wrapper"></span>' );
		});
	}

	function initEqualHeight() {
		var equalHeighters = [
			$( '.footer-widget' ),
			$( '.jobify_widget_jobs_spotlight .single-job-spotlight' )
		];

		equalHeights( equalHeighters );

		$(window).resize(function() {
			equalHeights( equalHeighters );
		});
	}

	function initPopups() {
		$( 'body' ).on( 'click', '.popup-trigger-ajax', function(e) {
			e.preventDefault();

			var class = $(this).attr( 'class' );

			class = class.replace( 'popup-trigger-ajax', '' );

			Jobify.App.popup({
				items: {
					src: $(this).attr( 'href' ),
					type: 'ajax'
				},
				callbacks: {
					parseAjax: function(mfpResponse) {
						mfpResponse.data = 
						'<div class="modal' + class + '">' + 
							'<h2 class="modal-title">' + $(mfpResponse.data).find( '.page-title' ).text() + '</h2>' + 
							$(mfpResponse.data).find('article.hentry').html() + 
						'</div>';
					},
					ajaxContentAdded: function() {
						$( 'body' ).trigger( 'popup-trigger-ajax' );
						initForms();
					}
				}
			});
		});

		$( 'body' ).on( 'click', '.popup-trigger', function(e) {
			e.preventDefault();

			Jobify.App.popup({
				items: {
					src: $(this).attr( 'href' )
				}
			});
		});
	}

	function initForms() {
		initSelects();

		$(document).on( 'submit', '.modal form.login, .modal form.register', function(e) {
			var form = $(this);
			var error = false;

			var base = $(this).serialize();
			var button = $(this).find( '[type=submit]' );

			var data = base + '&' + button.attr("name") + "=" + button.val();

			var request = $.ajax({
				url: jobifySettings.homeurl, 
				data: data,
				type: 'POST',
				cache: false,
				async: false
			}).done(function(response) {
				form.find( $( '.woocommerce-error' ) ).remove();

				var $response = $( '#ajax-response' );
				var html = $.parseHTML(response);

				$response.append(html);
				error = $response.find( $( '.woocommerce-error' ) );

				if ( error.length > 0 ) {
					form.prepend( error.clone() );
					$response.html('');

					e.preventDefault();
				}
			});
		});
	}

	function resizeChosen() {
		$( '.chosen-container' ).each(function() {
			$(this).attr( 'style', 'width: 100%' );
		});
	}

	return {
		init : function() {
			fixedHeader();
			mobileMenu();
			initSelects();
			initEqualHeight();
			initPopups();
			initForms();

			$( 'div.job_listings' ).on( 'updated_results', function() {
				initSelects();
			});

			$( '.bookmark-notice' ).on( 'click', function(e) {
				e.preventDefault();

				$.magnificPopup.open({
					type: 'inline',
					fixedContentPos: false,
					verticalFit: false,
					fixedBgPos: true,
					overflowY: 'scroll',
					items: {
						src:
							'<div class="modal">' +
							'<h2 class="modal-title">' + $(this).text() + '</h2>' +
								$( '.wp-job-manager-bookmarks-form' ).prop( 'outerHTML' ) +
							'</div>'
					}
				});
			});

			$(window).on( 'resize', resizeChosen );
			resizeChosen();

			// Geo My WP Association
			$( '.search_jobs' ).each(function() {
				$gjm_use = $(this).find( 'input[name="gjm_use"]' );

				if ( ! $gjm_use.length ) {
					return;
				}

				$(this).addClass( 'gjm_use' );
			});
		},

		popup : function( args ) {
			return $.magnificPopup.open( $.extend( args, {
				type            : 'inline',
				fixedContentPos : false,
				tClose: jobifySettings.i18n.magnific.tClose,
				tLoading: jobifySettings.i18n.magnific.tLoading,
				ajax: {
					tError: jobifySettings.i18n.magnific.tError
				},
				zoom: {
					enabled: true
				}
			} ) );
		},

		/**
		 * Check if we are on a mobile device (or any size smaller than 980).
		 * Called once initially, and each time the page is resized.
		 */
		isMobile : function( width ) {
			var isMobile = false;

			var width = 1180;

			if ( $(window).width() <= width )
				isMobile = true;

			return isMobile;
		}
	}
} )(jQuery);


jQuery( document ).ready(function($) {
	Jobify.App.init();
});
