jQuery(document).ready(function() {
	
	if ( jQuery('.um-social-login-overlay').length ) {
		jQuery('body,html').css("overflow", "hidden");
	}

	jQuery(document).on('click','a.um-social-login-avatar-change', function(e){
		var provider = jQuery(this).data('provider');
		var user_id = jQuery('input[type=hidden][name=user_id]').val();
		var profile_photo = jQuery('.um-profile-photo-img img');
		jQuery.ajax({
			url: um_scripts.social_login_change_photo,
			type: 'post',
			dataType: 'json',
			data: {
				provider: provider,
				user_id: user_id
			},

			success: function( d ){
				if( typeof  d.source !== 'undefined' && d.source != '' ){
					profile_photo.attr('src', d.source );
					jQuery('a.um-dropdown-hide').trigger('click');
				}
			},
			error: function( e ){
				console.log( e );
			}
		});

	});

});

function um_social_login_popup() {
	if ( jQuery('.um-social-login-overlay').length ) {
		
		jQuery('.um-social-login-wrap .um').css({
			'max-height':  jQuery('.um-social-login-overlay').height() - 80 + 'px'
		});
		
		var p_top = ( jQuery('.um-social-login-overlay').height() - jQuery('.um-social-login-wrap').innerHeight() ) / 2;
		
		jQuery('.um-social-login-wrap').animate({
			top: p_top + 'px',
		});
		
	}
}

jQuery(window).load(function() {
	
	um_social_login_popup();

});

jQuery(window).resize(function() {
	
	um_social_login_popup();

});