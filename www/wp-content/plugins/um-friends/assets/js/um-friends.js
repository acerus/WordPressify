um_friends_ajax = false;

jQuery(document).ready(function() {
	
	if ( jQuery('.um-friends-m').length ) {
		
		jQuery('.um-friends-m').each(function(){
			
			var max = jQuery(this).attr('data-max');
			if ( max > 0 && jQuery(this).find('.um-friends-m-user').length > max ) {
				var n = max - 1;
				jQuery(this).find('.um-friends-m-user:gt('+n+')').hide();
				var more = jQuery(this).find('.um-friends-m-user').length - jQuery(this).find('.um-friends-m-user:visible').length;
				jQuery('<div class="um-friends-m-user show-all">+'+ more + '</div>').insertAfter( jQuery(this).find('.um-friends-m-user:visible:last') );
			}
			
		});

		jQuery(document).on('click', '.um-friends-m-user.show-all',function(e){
			e.preventDefault();
			jQuery(this).parents('.um-friends-m').find('.um-friends-m-user').show();
			jQuery(this).hide();
			return false;
		});
	}
	
	/* Mouse over of friend button */
	jQuery(document).on('mouseenter', '.um-unfriend-btn', function(e){
		if  ( !jQuery(this).hasClass('um_friends_ajax') ) {
		jQuery(this).addClass('um-unfriend-btn2');
		jQuery(this).html( jQuery(this).attr('data-unfriend') );
		}
	});
	
	/* Mouse out of friend button */
	jQuery(document).on('mouseleave', '.um-unfriend-btn2', function(e){
		if  ( !jQuery(this).hasClass('um_friends_ajax') ) {
		jQuery(this).removeClass('um-unfriend-btn2');
		jQuery(this).html( jQuery(this).attr('data-friends') );
		}
	});
	
	/* Mouse over of pending friend button */
	jQuery(document).on('mouseenter', '.um-friend-pending-btn', function(e){
		jQuery(this).addClass('cancel-friend-request');
		jQuery(this).html( jQuery(this).attr('data-cancel-friend-request') );
	});

	/* Mouse out of pending friend button */
	jQuery(document).on('mouseleave', '.um-friend-pending-btn', function(e){
		jQuery(this).removeClass('cancel-friend-request');
		jQuery(this).html( jQuery(this).attr('data-pending-friend-request') );
	});

	/* Add friend user */
	jQuery(document).on('click', '.um-friend-btn', function(e){
		e.preventDefault();
		if ( um_friends_ajax == true ) { 
			return false; 
		}
		um_friends_ajax = true;
		var btn = jQuery(this);
		btn.addClass('um_friends_ajax');
		var user_id1 = jQuery(this).attr('data-user_id1');
		var user_id2 = jQuery(this).attr('data-user_id2');

		jQuery.ajax({
			url: um_scripts.friends_add,
			type: 'post',
			data: { user_id1: user_id1, user_id2: user_id2 },
			dataType: 'json',
			success: function( response ) {
				if ( response.success ) {
					btn.replaceWith( response.data.btn );
					btn.removeClass( 'um_friends_ajax' );
				} else {
					console.log( response.success );
				}

				um_friends_ajax = false;
			},
			error: function( e ) {
				console.log( e );
			}
		});
		return false;
	});
	
	/* Confirm friend */
	jQuery(document).on('click', '.um-friend-accept-btn', function(e){
		e.preventDefault();
		in_dropdown = false;
		if ( um_friends_ajax == true ) { 
			return false; 
		}
		um_friends_ajax = true;
		var btn = jQuery(this);
		btn.addClass('um_friends_ajax');
		var user_id1 = jQuery(this).attr('data-user_id1');
		var user_id2 = jQuery(this).attr('data-user_id2');
		
		var btn2 = btn.parent().find('.um-friend-reject-btn');

		if ( btn.parents('.um-dropdown' ).length > 0 ) {
			in_dropdown = true;
		}

		jQuery.ajax({
			url: um_scripts.friends_approve,
			type: 'post',
			data: { user_id1: user_id1, user_id2: user_id2 },
			dataType: 'json',
			success: function(data){

				if ( in_dropdown == true ) {
					
					btn.parents('.um-friend-respond-zone').find('.um-friend-respond-btn').replaceWith( data.btn );
					UM_hide_menus();
					
				} else {
					
					btn.replaceWith( data.btn );
					btn2.remove();
					btn.removeClass('um_friends_ajax');
				}
	
				um_friends_ajax = false;
			},
			error: function( e ){
				console.log( e );
			}
		});
		return false;
	});
	
	/* Unfriend user */
	jQuery(document).on('click', '.um-unfriend-btn', function(e){
		e.preventDefault();
		if ( um_friends_ajax == true ) { return false; }
		um_friends_ajax = true;
		var btn = jQuery(this);
		btn.addClass('um_friends_ajax');
		var user_id1 = jQuery(this).attr('data-user_id1');
		var user_id2 = jQuery(this).attr('data-user_id2');
		jQuery.ajax({
			url: um_scripts.friends_unfriend,
			type: 'post',
			data: { user_id1: user_id1, user_id2: user_id2 },
			dataType: 'json',
			success: function(data){
				btn.replaceWith( data.btn );
				um_friends_ajax = false;
				btn.removeClass('um_friends_ajax');
			}
		});
		return false;
	});
	
	/* Reject friendship user */
	jQuery(document).on('click', '.um-friend-reject-btn', function(e){
		e.preventDefault();
		in_dropdown = false;
		if ( um_friends_ajax == true ) { return false; }
		um_friends_ajax = true;
		var btn = jQuery(this);
		btn.addClass('um_friends_ajax');
		var user_id1 = jQuery(this).attr('data-user_id1');
		var user_id2 = jQuery(this).attr('data-user_id2');
		
		var btn2 = btn.parent().find('.um-friend-accept-btn');
		
		if ( btn.parents('.um-dropdown' ).length > 0 ) {
			in_dropdown = true;
		}
		
		jQuery.ajax({
			url: um_scripts.friends_unfriend,
			type: 'post',
			data: { user_id1: user_id1, user_id2: user_id2 },
			dataType: 'json',
			success: function(data){
			
				if ( in_dropdown == true ) {
					
					btn.parents('.um-friend-respond-zone').find('.um-friend-respond-btn').replaceWith( data.btn );
					UM_hide_menus();
					
				} else {
					
					btn.replaceWith( data.btn );
					btn2.remove();
					btn.removeClass('um_friends_ajax');
				}
	
				um_friends_ajax = false;
			}
		});
		return false;
	});
	
	jQuery(document).on('click', '.um-friend-respond-btn', function(e){
		jQuery(this).parent().trigger('click');
	});
	
	/* Cancel pending friend */
	jQuery(document).on('click', '.um-friend-pending-btn.cancel-friend-request', function(e){
		e.preventDefault();
		if ( um_friends_ajax == true ) { return false; }
		um_friends_ajax = true;
		var btn = jQuery(this);
		btn.addClass('um_friends_ajax');
		var user_id1 = jQuery(this).attr('data-user_id1');
		var user_id2 = jQuery(this).attr('data-user_id2');
		jQuery.ajax({
			url: um_scripts.friends_cancel_request,
			type: 'post',
			data: { user_id1: user_id1, user_id2: user_id2 },
			dataType: 'json',
			success: function(data){
				btn.replaceWith( data.btn );
				um_friends_ajax = false;
				btn.removeClass('um_friends_ajax');
			}
		});
		return false;
	});
	
});