(function( $ ) {
	'use strict';

	jQuery(document).ready(function(){


		// Init
		um_instagram_get_photos();

		/**
		 * Init Gallery
		 */
		function um_setup_instagram_gallery(){
			// Show first 5 instagram photos on load
			jQuery('#um-ig-photo-wrap ul li:gt(5)').addClass('hidden');
			var $ul_gallery 	= jQuery('ul#um-ig-show_photos');
			var $nav_previous 	= jQuery('#um-ig-content .um-ig-photo-navigation a.nav-left');
			var $nav_next 		= jQuery('#um-ig-content .um-ig-photo-navigation a.nav-right');
			var $um_paginate 	= jQuery('.um-ig-paginate span');
			var gallery_page 	= $ul_gallery.find('li:not(.hidden) img').length;
			var total_photos 	= $ul_gallery.attr('data-photos-count');
			var is_viewing      = $ul_gallery.attr('data-viewing');
			var paginate 		= 1;
			var max_photos 		= 18;
			var items_per_page 	= 6;
			
			// Hide previous button on load
			$nav_previous.addClass('nav-hide');

			// Show paginate on load
			$um_paginate.text( gallery_page +'/'+total_photos );
			
			// Hide Next button last page reached
			if( total_photos < items_per_page ){
				$nav_next.addClass('nav-hide');
			}
			
			// Previous
			$nav_previous.on('click',function() {
				
					paginate--;
				    
				    var current_visible_photos = $ul_gallery.find('li:not(.hidden) img').length;
				    
				    var first = $ul_gallery.find('li:not(.hidden)').first();
				    first.prevAll(':lt('+items_per_page+')').removeClass('hidden');
				    first.prev().nextAll().addClass('hidden');
					
				     gallery_page -= current_visible_photos;
				     
				    if( total_photos > items_per_page && total_photos > gallery_page  ){
			    		$nav_next.removeClass('nav-hide');
					}

					if( gallery_page == items_per_page ){
						$nav_previous.addClass('nav-hide');
					}
				     
					
				    $um_paginate.text( gallery_page +'/'+total_photos );

			});

			// Next
			$nav_next.on('click',function() {
				
				paginate++;

				var last = $ul_gallery.find('li:not(.hidden)').last();
			    last.nextAll(':lt('+items_per_page+')').removeClass('hidden');
				last.next().prevAll().addClass('hidden');

				var current_visible_photos = $ul_gallery.find('li:not(.hidden) img').length;
				gallery_page += current_visible_photos;
			   
			    
			    if( total_photos > gallery_page  ){
			    		$nav_next.removeClass('nav-hide');
				}

				if( total_photos == gallery_page  ){
			    		$nav_next.addClass('nav-hide');
				}
				
				$nav_previous.removeClass('nav-hide');
			    
			    $um_paginate.text(gallery_page+'/'+total_photos );
				
			});

			// Disconnecet instagram account
			jQuery('.um-ig-photos_disconnect').on('click',function(){
				jQuery('.um-ig-photos_metakey').val('');
				jQuery('.um-form form').submit();
			});


			// Add photo opacity effects on hover
			jQuery("#um-ig-photo-wrap a img").hover(function(){
			    jQuery(this).stop().animate({"opacity": 0.7});
			},function(){
			    jQuery(this).stop().animate({"opacity": 1});
			});
		}

		/**
		 * Ajax get instagram photos
		 */
		function um_instagram_get_photos(){
			
			var $um_photo_wrap = jQuery('#um-ig-photo-wrap');
			var metakey = $um_photo_wrap.attr('data-metakey');
			var viewing = $um_photo_wrap.attr('data-viewing');
			var $body_classes = jQuery('body').attr('class');
			var user_id = 0;
			var cl =  $body_classes.split(' ');
					
			if( cl.length > 0 ){
				jQuery.each(cl,function(i,cs){
					if( cs.substr(0,14) == "um-profile-id-" ){
						user_id = cs.substr(14);
					}
				})
			}

			jQuery('#um-ig-preload').css('background','url('+um_instagram.image_loader+') no-repeat  50% 50%');
			
			if( viewing == 'true' ){
				jQuery.ajax({
					method: 'POST',
					url: um_scripts.instagram_get_photos,
					data:{
						metakey: metakey,
						viewing: viewing,
						um_user_id: user_id
					},
					success: function( response ){
						$um_photo_wrap.html( response.photos );
						um_setup_instagram_gallery();
						um_instagram_preload();
					},
					error: function( e ){
						console.log(e);
					}

				});
			}else if( viewing == 'false' ){ // Edit profile view
				um_setup_instagram_gallery();
				um_instagram_preload();
			}
		}
		
		/**
		 * Lazy load
		 */
		function um_instagram_preload(){
			// Remove preload
			setTimeout( function(){
				jQuery("#um-ig-preload").fadeOut();
				jQuery("#um-ig-content").fadeIn();
			}, 5000);
		}
	});

	

})( jQuery );
