(function( $ ) {

	var api = wp.customize || {};

	api.BigChoices = {},
	api.BigChoices.htmlCache = {};

	api.BigChoices.primeCache = function( choices_id ) {
		if ( ! _.has( api.BigChoices.htmlCache, choices_id ) ) {
			var choicesObj = astoundifyThemeCustomizer.BigChoices[ choices_id ];

			api.BigChoices.htmlCache[ choices_id ] = api.BigChoices.buildHtmlOptions( choicesObj );
		}
	}

	/**
	 * Build a string of HTML to be inserted inside the <select>
	 *
	 * This only happens once per chocie-type.
	 */
	api.BigChoices.buildHtmlOptions = function( choices ) {
		var options = '';

		_.each(choices, function(label, value) {
			options += '<option value="' + value + '">' + label + '</option>';
		});

		return options;
	}

	/**
	 * Create the select2 instance
	 */
	api.BigChoices.setupChoices = function( input, choices, val ) {
		// add items to cache if needed
		api.BigChoices.primeCache( choices );

		input.html( api.BigChoices.htmlCache[ choices ])

		if ( val ) {
			input.val( val );
		}

		input.select2();
	}

	/**
	 * BigChoices Control
	 *
	 * Select options that have a lot of repeated choices shoudl be handled
	 * more gracefully. The choices used by these controls are set once in 
	 * the page content and the control loads what is needed
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	wp.customize.controlConstructor.BigChoices = wp.customize.Control.extend({
		ready: function() {
			var control = this;

			var choices_id = control.params.choices_id;
			
			// cache the select element
			control.$select = control.container.find( 'select' );

			// add choices to input
			api.BigChoices.setupChoices( control.$select, choices_id, control.setting.get() );

			control.setting.bind(function( value ) {
				control.$select.trigger( 'change' );
			});
		}
	});

})( jQuery );
