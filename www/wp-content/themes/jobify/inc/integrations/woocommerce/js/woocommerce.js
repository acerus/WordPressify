(function($) {
	'use strict';

	var jobifyWooCommerce = {
		cache: {
			$document: $(document),
			$window: $(window)
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.initSocialLogin(false);
			});

			$( 'body' ).on( 'popup-trigger-ajax', function() {
				self.initSocialLogin( $( '.modal' ) );
			});
		},

		initSocialLogin: function(search) {
			if ( ! search ) {
				search = $( 'body' );
			}

			var $social = search.find( $( '.woocommerce .wc-social-login' ) );

			if ( ! $social.length ) {
				return;
			}

			if ( $social.hasClass( 'wc-social-login-link-account' ) ) {
				return;
			}

			var $clone = $social.clone();
			var $container = $social.parent();

			$social.remove();
			$container.prepend( $clone );
		}
	};

	jobifyWooCommerce.init();

})(jQuery);
