(function( $ ) {

	$(document).on("click", ".fig_search", function(){

		var search = $("input[name='fig_search_txt']").val();
		if ( search ){
			getPic(search, '');
		}

		return false;
	});

	$(document).on("keypress", ".fig_search_txt", function(e){var 
		keyCode = (event.keyCode ? event.keyCode : event.which);   
		if (keyCode == 13) {
			var thisEle = $(this);
			var search = $(this).parents('.fig_search_body').find("input[name='fig_search_txt']").val();
			if ( search ){
				getPic(search, '');
			}

			return false;
		}
	});

	$(document).on("click", ".fig_save", function(){

		if( confirm("Please Confirm, The image will be import to Library.") ) {
			var save_button = $(this);
			save_button.attr("disabled", true);
			save_button.val("Saving...");

			var dataURL = document.getElementById("fig_canvas").toDataURL('image/jpeg');
			$.ajax({
				type: 'POST',
				url: figAjax.url,
				data: {
					action: 'fig_save_after_generate_image',
					nonce: figAjax.nonce,
					imgBase64: dataURL
				},
				success:function(data){

					$(".fig_wrap").hide();
					$("body").removeClass("modal-open");		

					save_button.attr("disabled", false);
					save_button.val("Save to Media");

					setTimeout(function(){
						$("#set-post-thumbnail").trigger("click");
					},1000);					

				}
			});
		}

		return false;
	});

	$(document).on("click", ".fig_cancel", function(){

    	$("input[name='fig_canvas_image']").val("");
    	figToggle();
    	canvasDraw();

		return false;
	});
	
	$(document).on("click", ".fig_thumb_link", function(){

		var thumb_id = $(this).attr("id");
		var thumb_url = $(this).attr("attr-org");

		$(".fig_bg").fadeIn();	
		$(".fig_result").fadeIn();		

		$(".fig_gen").html('<img src="'+figAjax.fig_gen_url+'font=superstore&text=Hi&url='+encodeURIComponent(thumb_url)+'" class="fig_editor_area">');		

		if ( !$(this).hasClass("downloaded") ){
			$(this).find(".fig_thumb_overlay").find("div").text("Downloaded");
			$(this).append("<i class='dashicons-before dashicons-yes downloaded-icon'></i>");
		}    	

        var data = {
            action: 'fig_save_image',
            nonce: figAjax.nonce,
            data: {
            	id : thumb_id,
            	url : thumb_url,
            }
        };

        $.post( figAjax.url, data, function( response ) 
        {	
        	if( response.success ) {
        		$("input[name='fig_canvas_image']").val( response.file );
        		figToggle();
        		canvasDraw();

				$(".fig_tab a").removeClass("active");
				$(".fig_tab a[for='editor']").addClass("active");
				$("[name='fig_active']").val('editor');
        	}else{
        		$(".fig_search_result").slideDown().html("Photo couldn't download. Please check your <a href='https://codex.wordpress.org/Changing_File_Permissions'>host permission</a>");			
        	}
        });

		return false;
	});

	$(document).on("click", ".layer", function(){

		$(this).toggleClass("active");
		var layerContainer = $(this).attr("for");
			
		$( "#" + layerContainer).slideToggle();

		return false;
	});

	$(document).on("click", ".wrap_hide", function(){

		$(".fig_editor_wrap").addClass("hide");
		$(".thumb_area").addClass("hide");

		return false;
	});

	$(document).on("click", ".wrap_show", function(){

		$(".fig_editor_wrap").removeClass("hide");
		$(".thumb_area").removeClass("hide");

		return false;
	});
	
	

    $(document).on("click", "#upload-btn", function(e) {

        e.preventDefault();
        var image = wp.media({ 
            title: 'Select / Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            
            var uploaded_image = image.state().get('selection').first();            
            var image_url = uploaded_image.toJSON().url;

        	figToggle();
            $("input[name='fig_canvas_image']").val(image_url);

            canvasDraw();
        })
        .on('escape', function(){
        	$("body").addClass("modal-open");
        });    

		return false;
    });

	$(document).on("keyup click change", ".fig_value", function(){
		canvasDraw();
	});	

	$(window).load(function(){
		if( $("#postimagediv.postbox").length > 0 ) {
			$("#postimagediv.postbox").append("<div class='fig-inside'><p class='fig_call_popup_container'><a href='#' class='fig_call_popup'>Featured Image Generator</a></p></div>");					
		}
	});

	$(document).on("click", ".fig_call_popup", function(){

		if ( $(".fig_container").length == 0 ) {
			var data = {
				action: 'fig_load_popup',
				nonce: figAjax.nonce,
			};

			$.post( figAjax.url, data, function( response ) 
			{
				$("body").append(response);
				$("body").addClass("modal-open");
				$(".fig_wrap").show();
				$(".fig_search_txt").select();

				$(".fig_save").attr("disabled", false);
				$(".fig_save").val("Save to Media");

			});

			wp.media.featuredImage.frame().on('open', function(){
				if(wp.media.frame.content.get()!==null){
					wp.media.frame.content.get().collection.props.set({ignore: (+ new Date())});
					wp.media.frame.content.get().options.selection.reset();
				}else{
					wp.media.frame.library.props.set({ignore: (+ new Date())});
				}	
			});

		}else{			
			$("body").addClass("modal-open");
			$(".fig_wrap").show();
		}

		return false;
	});

	$(document).on("click", ".fig_popup_bg, .fig_popup_close", function(){

		$(".fig_wrap").hide();
		$("body").removeClass("modal-open");

		return false;
	});

	$(document).on("click", ".fig_search_result", function(){
		$(this).slideUp();

		return false;
	});

	function figToggle() {		

    	$(".fig_search_body, .fig_select_container").fadeToggle();
    	$(".fig_editor").fadeToggle();
	}

	function canvasDraw() {

		var fig_font_family = figAjax.fig_font_family;
		var fig_font_call = fig_font_family;
		var fig_font_link = fig_font_family;

		if ( fig_font_family.indexOf("+") != -1 ){
			fig_font_call = fig_font_family.replace(/\+/g, ' ');
			fig_font_link = fig_font_family.replace(/\+/g, '-').toLowerCase();
		}

		if( $("#font-attach-"+fig_font_link).length == 0 ) {
			var link = document.createElement('link');
			link.rel = 'stylesheet';
			link.type = 'text/css';
			link.id = 'font-attach-'+fig_font_link;
			link.href = 'https://fonts.googleapis.com/css?family='+fig_font_family;		
			document.getElementsByTagName('head')[0].appendChild(link);
		}

		var url = $("input[name='fig_canvas_image']").val();

		var fig_caption = $("input[name='fig_caption']").val();
		var fig_caption_x = $("input[name='fig_caption_x']").val();
		var fig_caption_y = $("input[name='fig_caption_y']").val();
		var fig_caption_size = $("input[name='fig_caption_size']").val();
		var fig_caption_color = $("input[name='fig_caption_color']").val();

		fig_caption_size === 'number' ? fig_caption_size : 30;
		fig_caption_x === 'number' ? fig_caption_x : 50;
		fig_caption_y === 'number' ? fig_caption_y : 50;

		if ( typeof fig_caption_color == "undefined" ) {
			fig_caption_color = "#ffffff";
		}

		var fig_filter = $("input[name='fig_filter']").is(":checked");		
		var fig_filter_color = $("input[name='fig_filter_color']").val();	
		var fig_filter_opacity = $("input[name='fig_filter_opacity']").val();	

		if ( typeof fig_filter == "undefined" ) {
			fig_filter = "";
		}

		if ( typeof fig_filter_opacity == "undefined" ) {
			fig_filter_opacity = 100;
		}

		if ( typeof fig_filter_color == "undefined" ) {
			fig_filter_color = "#000000";
		}

		fig_filter_opacity = ( fig_filter_opacity / 100 );
		fig_filter_color = hexToRgb(fig_filter_color, fig_filter_opacity);

		if ( fig_filter ) {
			$(".fig_filter_container").slideDown();
		}else{
			$(".fig_filter_container").slideUp();
		}

		$("#fig_canvas").attr( 'width', $("input[name='fig_canvas_width']").val() );
		$("#fig_canvas").attr( 'height', $("input[name='fig_canvas_height']").val() );

		var canvas = document.getElementById("fig_canvas");
		var context = canvas.getContext("2d");
		context.clearRect(0, 0, canvas.width, canvas.height);

		if ( url ) {
			var imageObj = new Image();
			imageObj.onload = function(){
				drawImageProp(context, this, 0, 0, canvas.width, canvas.height, 0.5, 0.5);

				if( fig_filter ){
					context.fillStyle = fig_filter_color;
					context.fillRect(0, 0, canvas.width, canvas.height);
				}

				context.font = fig_caption_size + "px "+fig_font_call;
				context.textAlign = "center"
				context.fillStyle = fig_caption_color;
				context.fillText(fig_caption, fig_caption_x , fig_caption_y);

				$(".fig_bg").fadeOut();	
			};

			imageObj.src = url; 
		} else {
			if( fig_filter ){
				context.fillStyle = fig_filter_color;
				context.fillRect(0, 0, canvas.width, canvas.height);
			}

			context.font = fig_caption_size + "px "+fig_font_call;
			context.textAlign = "center"
			context.fillStyle = fig_caption_color;
			context.fillText(fig_caption, fig_caption_x , fig_caption_y);
		}
		

		return false;
	}

	function hexToRgb(hex, opacity) {

		var h=hex.replace('#', '');
		h =  h.match(new RegExp('(.{'+h.length/3+'})', 'g'));

		if( h.length > 0 ){
			for(var i=0; i<h.length; i++)
				h[i] = parseInt(h[i].length==1? h[i]+h[i]:h[i], 16);

			if (typeof opacity != 'undefined')  h.push(opacity);
		}

		return 'rgba('+h.join(',')+')';
	}

	function drawImageProp(ctx, img, x, y, w, h, offsetX, offsetY) {

	    if (arguments.length === 2) {
	        x = y = 0;
	        w = ctx.canvas.width;
	        h = ctx.canvas.height;
	    }

	    offsetX = typeof offsetX === 'number' ? offsetX : 0.5;
	    offsetY = typeof offsetY === 'number' ? offsetY : 0.5;

	    if (offsetX < 0) offsetX = 0;
	    if (offsetY < 0) offsetY = 0;
	    if (offsetX > 1) offsetX = 1;
	    if (offsetY > 1) offsetY = 1;

	    var iw = img.width,
	        ih = img.height,
	        r = Math.min(w / iw, h / ih),
	        nw = iw * r,   
	        nh = ih * r,
	        cx, cy, cw, ch, ar = 1;

	    if (nw < w) ar = w / nw;
	    if (nh < h) ar = h / nh;
	    nw *= ar;
	    nh *= ar;

	    cw = iw / (nw / w);
	    ch = ih / (nh / h);

	    cx = (iw - cw) * offsetX;
	    cy = (ih - ch) * offsetY;

	    if (cx < 0) cx = 0;
	    if (cy < 0) cy = 0;
	    if (cw > iw) cw = iw;
	    if (ch > ih) ch = ih;

	    ctx.drawImage(img, cx, cy, cw, ch,  x, y, w, h);
	}

	function getPic(search, url){

		$(".fig_bg").fadeIn();	
		$('.fig_result').html("");

		if ( url == "" ){
			var url = "https://api.unsplash.com/photos/search?client_id="+figAjax.fig_unsplash_api+"&per_page=30&query="+search;
		}

		$.getJSON(url, function(result,status,xhr) {				
			
			// console.log('Time Use Left ( Per/Hour ) : ' + xhr.getResponseHeader('X-Ratelimit-Remaining'));		

			var link = xhr.getResponseHeader('Link');		

			if ( link ){
				if ( link.search(',') > 1 ){

					link = link.split(',');
					var next = "";
					var prev = "";

					$(link).each(function(k,v){

						if ( v.search('next') > 1 ){
							next = v;
						}else if ( v.search('prev') > 1 ){
							prev = v;
						}

					});

					if ( next.search(';') > 1 ){
						var next_link = next.split(';');
						var next_url = next_link[0].slice(0,-1).substring(2);

						$(".fig_next").attr("disabled", false);
						$(".fig_next").unbind();
						$(".fig_next").bind("click", function(){
							$(".fig_next").attr("disabled", true);
							getPic("", next_url);
							$(".fig_result").animate({ scrollTop: "0px" });

							return false;
						});
					}

					if ( prev.search(';') > 1 ){
						var prev_link = prev.split(';');
						var prev_url = prev_link[0].slice(0,-1).substring(2);

						$(".fig_prev").attr("disabled", false);
						$(".fig_prev").unbind();
						$(".fig_prev").bind("click", function(){
							$(".fig_prev").attr("disabled", true);
							getPic("", prev_url);

							return false;
						});
					}
				}
			}

			var fig_downloaded = figAjax.fig_downloaded;
			var downloaded = "";
			var save_txt = "";
			var icon = "";
			var status = "";

			$(".fig_result_pagination").fadeIn();
			
			if ( result.length > 0 ) {

				$(".fig_result").html("");

				$(result).each(function(k,v){

					if ( fig_downloaded != "" ) {
						downloaded = fig_downloaded.indexOf(v.id);
						if ( downloaded > -1 ){
							save_txt = "Downloaded";
							status = "downloaded";
							icon = "<i class='dashicons-before dashicons-yes downloaded-icon'></i>";
						}else{
							save_txt = "Choose";
							status = "";
							icon = "";
						}
					}else{
						save_txt = "Choose";
					}

					var img_src = v.urls.small;
					var img_org = v.urls.regular;
					var html = "<div attr-org='"+img_org+"' class='fig_thumb_link "+status+"' id='"+v.id+"'>";
					html += "<img src='"+img_src+"' class='fig_thumb_result'>";
					html += "<div class='fig_thumb_overlay'><div>"+save_txt+"</div></div>";
					html += "<a class='fig_thumb_credit' target='_blank' title='See Credit & Download' href='"+v.user.links.html+"'>"+v.user.name+"</a>";
					html += icon;
					html += "</div>";

					$(".fig_result").append(html);

				});

			}else{
				$(".fig_result").html('<div class="fig_result_placeholder"><h3>Nothing found. Please try another keyword.</h3></div>');				
				$(".fig_bg").fadeOut();	
			}

			$("img").bind('load', function() { 
				$(".fig_bg").fadeOut();	
				$(".fig_search_result").slideUp();
			});

		}).fail(function() { 
			$(".fig_bg").fadeOut();	
			$(".fig_search_result").slideDown().html("Please Check Your Unsplash API. Might be incorrected API or reached the limit 100 time per hour.  ( Settings > Featured Image Generator )");			
		});
	}	

/* 2.0 */

// Tab switch
$(document).on("click", ".fig_tab a", function(){
	$(".fig_tab a").removeClass("active");
	$(this).addClass("active");

	var tab_for = $(this).attr("for");
	$("[name='fig_active']").val(tab_for);

	switch ( tab_for ) {
		case 'editor' :
			$(".fig_editor").show();
			$(".fig_search_body, .fig_select_container").hide();
		break;

		case 'unsplash' :
			$(".fig_search_body, .fig_select_container").show();
			$(".fig_editor").hide();
		break;

		return false;
	}
});

})( jQuery );
