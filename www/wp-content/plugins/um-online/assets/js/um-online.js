jQuery(document).ready(function() {

	if ( jQuery('.um-online').length ) {
		
		jQuery('.um-online').each(function(){
			
			var max = jQuery(this).attr('data-max');
			if ( max > 0 && jQuery(this).find('.um-online-user').length > max ) {
				var n = max - 1;
				jQuery(this).find('.um-online-user:gt('+n+')').hide();
				var more = jQuery(this).find('.um-online-user').length - jQuery(this).find('.um-online-user:visible').length;
				jQuery('<div class="um-online-user show-all">+'+ more + '</div>').insertAfter( jQuery(this).find('.um-online-user:visible:last') );
			}
			
		});

		jQuery(document).on('click', '.um-online-user.show-all',function(e){
			e.preventDefault();
			jQuery(this).parents('.um-online').find('.um-online-user').show();
			jQuery(this).hide();
			return false;
		});
	
	}
	
});