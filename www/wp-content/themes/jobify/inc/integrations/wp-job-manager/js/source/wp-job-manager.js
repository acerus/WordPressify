(function($) {
	'use strict';

	var JobifyWPJobManager = {
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
				self.initApply();
				self.initIndeed();
				self.avoidSubmission();
				self.initContact();
				self.fileUploadButton();
				self.initSelectedPackage();
			});
		},

		initApply: function() {
			var $details = $( '.application_details, .resume_contact_details' );
			var $button = $( '.application_button, .resume_contact_button' );

			if ( ! $details.length ) {
				return;
			}

			$button.unbind( 'click' );

			$details
				.addClass( 'modal' )
				.attr( 'id', 'apply-overlay' );

			$button
				.addClass( 'popup-trigger' )
				.attr( 'href', '#apply-overlay' );
		},

		initIndeed: function() {
			$( '.job_listings' ).on( 'update_results', function() {
				$( '.indeed_job_listing' ).addClass( 'type-job_listing' );
			});
		},

		initContact: function() {
			$( '.resume_contact_button' ).click(function(e) {
				e.preventDefault();

				Jobify.App.popup({
					items : {
						src : $( '.resume_contact_details' )
					}
				});

				return false;
			});
		},

		avoidSubmission: function() {
			$( '.job_filters, .resume_filters' ).submit(function(e) {
				return false;
			});
		},

		fileUploadButton: function() {
			var $inputs = $( '.listify-file-upload' );

			$inputs.each(function(input) {
				var $input = $(this);
				var $label = $input.next();
				var labelDefault = $label.text();

				$input.on( 'change', function(e) {
					console.log('wat');
					console.log(this.files);
					var fileName = '';

					if ( this.files && this.files.length > 1 ) {
						fileName = ( $input.data( 'multiple-caption' ) || '' ).replace( '%d', this.files.length );
					} else {
						fileName = e.target.value.split( '\\' ).pop();
					}

					if ( fileName ) {
						$label.text( fileName );
					} else {
						$label.text( labelDefault );
					}
				});
			});

		},

		initSelectedPackage: function() {
			var selectedPackage = $( '#jobify_selected_package' );
			
			if ( selectedPackage.length == 0 ) {
				return;
			}
			
			var value = selectedPackage.val();

			var $packages = $( '.job_listing_packages, .resume_listing_packages' );
			var $form = $( '#job_package_selection, #resume_package_selection' );

			$packages.find( '#package-' + value ).attr( 'checked', 'checked' );
			$form.submit();
		}
	};

	JobifyWPJobManager.init();

})(jQuery);