jQuery(document).ready(function() {
	
	/* Tooltip for tag */
	if( typeof tipsy !== 'undefined' ){
		jQuery('.um-user-tag-desc').tipsy({
			gravity: 'n',
			opacity: 0.95,
			offset: 5,
			fade: false,
		});
	}
	
	/* Show more tags */
	jQuery(document).on('click', '.um-user-tag-more', function(e){
		e.preventDefault();
		jQuery(this).hide();
		jQuery(this).parents('.um-user-tags').find('.um-user-hidden-tag').show();
		return false;
	});
	
	if( typeof select2 !== 'undefined' ){
		jQuery('.um-field-user_tags select').select2('destroy');
		jQuery(".um-field-user_tags select").each(function(){
			var $this = jQuery(this);
			$this.select2({
				allowClear: true,
				minimumResultsForSearch: 10,
				maximumSelectionSize: parseInt( $this.attr('data-maxsize') )
			});
		});
	}

});