(function($) {
	'use strict';

	var JobifyWPJobManagerWith = {
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
				self.initApplyWith();
				self.initApplications();
			});
		},

		initApplyWith: function() {
			$( '.wp-job-manager-application-details' )
				.addClass( 'modal' )
				.on( 'wp-job-manager-application-details-show', function(e) {
					Jobify.App.popup({
						items : {
							src : $( e.delegateTarget )
						}
					});
				})
		},

		initApplications: function() {
			if ( $( '#apply-overlay.application_details' ).is( ':visible' ) ) {
				var $error = $( '.job-manager-applications-error' ).detach();

				$( '.job-manager-application-form fieldset:first-of-type' ).before( $error );

				Jobify.App.popup({
					items: {
						src: $( '#apply-overlay.application_details' )
					}
				});
			}

		}
	};

	JobifyWPJobManagerWith.init();

})(jQuery);
