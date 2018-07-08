(function( $ ) {

	var api = wp.customize || {};

	/**
	 * Multiselect Control
	 *
	 * @constructor
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.controlConstructor.Multiselect = api.Control.extend({

		/**
		 * When the control has been embedded in to the section.
		 *
		 * @since 1.0.0
		 * @param {int} id
		 * @param {Array} options
		 */
		ready: function( id, options ) {
			api.Control.prototype.ready.apply( this, id, options );

			var control = this;

			control.selection = control.selection;

			control.$select = control.container.find( 'select[multiple]' );

			control.$select.select2({
				placeholder: control.params.placeholder,
				allowClear: true
			});

			// update input value with current selection, this is what select2 reads from
			control.$select.val( control.selection );

			control.setting.bind(function( value ) {
				control.$select.trigger( 'change' );
			});

			// update the DOM so order is saved
			control.$select.on( 'select2:select', function(e) {
				$selectedElement = $(e.params.data.element);
				$selectedElementOptgroup = $selectedElement.parent( 'optgroup' );

				if ( $selectedElementOptgroup.length > 0 ) {
					$selectedElement.data( 'select2-originaloptgroup', $selectedElementOptgroup );
				}

				$selectedElement.detach().appendTo( $(e.target) );

				control.$select.trigger( 'change' );
			})

			// update the DOM so order is saved
			control.$select.on( 'select2:unselect', function(e) {
				var selected = control.$select.find( 'option:selected' );

				if ( 0 == selected.length ) {
					control.setting.set( '' );
					control.$select.select2( 'val', '' );
				}
			});
		}

	});

})( jQuery );
