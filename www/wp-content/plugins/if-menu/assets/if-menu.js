jQuery(function($) {


	// Show or hide conditions section
	$('body').on('change', '.menu-item-if-menu-enable', function() {
		$( this ).closest( '.if-menu-enable' ).next().toggle( $( this ).prop( 'checked' ) );

		if ( ! $( this ).prop( 'checked' ) ) {
			var firstCondition = $( this ).closest( '.if-menu-enable' ).next().find('p:first');
			firstCondition.find('.menu-item-if-menu-enable-next').val('false');
			firstCondition.nextAll().remove();
		}
	});


	// Show or hide conditions section for multiple rules
	$('body').on( 'change', '.menu-item-if-menu-enable-next', function() {
		var elCondition = $( this ).closest( '.if-menu-condition' );

		if ($(this).val() === 'false') {
			elCondition.nextAll().remove();
		} else if (!elCondition.next().length) {
			var newCondition = elCondition.clone().appendTo(elCondition.parent());
			newCondition.find('select').removeAttr('data-val').find('option:selected').removeAttr('selected');
			newCondition.find('.menu-item-if-menu-options, .select2').remove();
		}
	});


	// Check if menu extra fields are actually displayed
	if ($('#menu-to-edit li').length !== $('#menu-to-edit li .if-menu-enable').length) {
		$('<div class="notice error is-dismissible if-menu-notice"><p>' + IfMenu.conflictErrorMessage + '</p></div>').insertAfter('.wp-header-end');
	}


	// Store current value in data-val attribute (used for CSS styling)
	$('body').on('change', '.menu-item-if-menu-condition-type', function() {
		$(this).attr('data-val', $(this).val());
	});


	// Display multiple options
	$('.menu-item-if-menu-options').select2();
	$('body').on('change', '.menu-item-if-menu-condition', function() {
		var options = $(this).find('option:selected').data('options'),
			elCondition = $(this).closest('.if-menu-condition');

		elCondition.find('.menu-item-if-menu-options').select2('destroy').remove();

		if (options && !!IfMenu.plan && IfMenu.plan.plan === 'premium') {
			$('<select class="menu-item-if-menu-options" name="menu-item-if-menu-options[' + elCondition.data('menu-item-id') + '][' + elCondition.index() + '][]" style="width: 305px" multiple></select>')
				.appendTo(elCondition)
				.select2({data: $.map(options, function(value, index) {
					return {
						id:		index,
						text:	value
					};
				})});
		} else if (options && (!IfMenu.plan || IfMenu.plan.plan !== 'premium')) {
			$(this).find(':selected').removeAttr('selected');
			$(this).find(':first').attr('selected', true);
			$('.if-menu-dialog-premium').dialog({
				dialogClass:	'if-menu-dialog',
				draggable:		false,
				modal:			true,
				width:			450,
				open:			function(event, ui) {
					console.log(event);
					console.log(ui);
				}
			});
		}
	});


	// Store current value in data-val attribute (used for CSS styling)
	$('.if-menu-dialog-btn').click(function() {
		if ($(this).data('action') === 'get-premium') {
			window.onbeforeunload = function() {};
		}

		$(this).closest('.ui-dialog-content').dialog('close');
	});


});
