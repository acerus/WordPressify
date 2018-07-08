(function( $ ) {
	'use strict';

	jQuery(document).ready(function(){
		jQuery('.xt_datepicker').datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd'
		});
	});
	
	jQuery(document).ready(function(){
		jQuery('#import_type').on('change', function(){
			if( jQuery(this).val() != 'onetime' ){
				jQuery('.hide_frequency .import_frequency').show();
			}else{
				jQuery('.hide_frequency .import_frequency').hide();
			}
		});

		jQuery("#import_type").trigger('change');
	});	

	// Render Dynamic Terms.
	jQuery(document).ready(function() {
	    jQuery('.event_import_plugin').on( 'change', function() {

	    	var event_plugin = jQuery(this).val();
	    	var data = {
	            'action': 'ime_render_terms_by_plugin',
	            'event_plugin': event_plugin
	        };

	        var terms_space = jQuery('.event_taxo_terms_wraper');
	        terms_space.html('<span class="spinner is-active" style="float: none;"></span>');
	        // send ajax request.
	        jQuery.post(ajaxurl, data, function(response) {
	            if( response != '' ){
	            	terms_space.html( response );
	            }else{
	            	terms_space.html( '' );
	            }	            
	        });    
	    });
	    jQuery(".event_import_plugin").trigger('change');                  
	});

})( jQuery );
