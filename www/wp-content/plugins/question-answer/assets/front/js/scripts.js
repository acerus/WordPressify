jQuery(document).ready(function($) {
	
	
	$(document).on('click', '.qa-breadcrumb .menu-box', function (){
		
		$('.qa-breadcrumb .menu-box .menu-box-hover').fadeIn();
	})
	
	jQuery('.submit_answer_button').mousedown( function() {
		tinyMCE.triggerSave();
	});

	$(document).on('click', '.admin_actions_submit', function (){
		
		$(this).html( "<i class='fa fa-cog fa-spin'></i>" );
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action" 	: "qa_ajax_admin_actions_submit", 
			"form_data" : $(this).parent().serializeArray(), 
		},
		success: function( question_permalink ) {
	
			if( question_permalink.length > 0 ) window.location.href = question_permalink;			
		} });		
	})
	
	
	
	$(document).on('click', '.submit_answer_button', function (){
		
		__html__	= $(this).html();
		form_data 	= $('.form-answer-post').serializeArray();
		current_url	= window.location.href;  
		url_arr		= current_url.split("#");
		last_char	= url_arr[0].substr( url_arr[0].length -1 );
		current_url	= last_char == "/" ? url_arr[0].slice(0, -1) : url_arr[0];
		
		// get_tinymce_content
		
		console.log( form_data );
		
		$(this).html( "<i class='fa fa-cog fa-spin'></i>" );
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action" 	: "qa_ajax_answer_posting", 
			"form_data" : form_data, 
		},
		success: function( response ) {
			
			var data = JSON.parse( response );
			
			$('.form-answer-post .answer_posting_notice').html( data['html'] );
			$(this).html( __html__ );
			
			if( data['answer_id'] ) {
				
				window.location.href = current_url + "#single-answer-" + data['answer_id'];
			}
		
			
			
		} });		
	})



    $(document).on('click', '.qa-load-comments', function(event){

        event.preventDefault();

        total_comments = parseInt($(this).attr('total_comments'));
        per_page = parseInt($(this).attr('per_page'));
        paged = parseInt($(this).attr('paged'));
        post_id = parseInt($(this).attr('post_id'));



        $.ajax({
			type: 'POST',
			context: this,
			url:qa_ajax.qa_ajaxurl,
			data: {
				"action" 	: "qa_ajax_load_more_comments",
				"post_id" : post_id,
				"paged" : paged,
				"per_page" : per_page,
				"total_comments" : total_comments
			},
			success: function( response ) {

                var data = JSON.parse( response );

			   html_output = data['html_output'];
			   has_comment = data['has_comment'];
			   comment_remain_count = data['comment_remain_count'];



				$('.qa-answer-comment-reply-'+post_id).append(html_output);
                $('.qa-question-comment-reply-'+post_id).append(html_output);

				$(this).children('.count').text(comment_remain_count);

				if(has_comment=='no' || comment_remain_count <= 0){
					$(this).fadeOut('slow');
				}

				$(this).attr('paged', (paged+1));

                setTimeout(function(){

                	$('.qa-answer-comment-reply .qa-single-comment').removeClass('loading');
                    $('.qa-question-comment-reply .qa-single-comment').removeClass('loading');

                	}, 1000);
				//console.log(comment_remain_count);




			}
		});
    })



























    $(document).on('click', '.update_answer_button', function (){

        __html__	= $(this).html();
        form_data 	= $('.form-answer-post').serializeArray();
        current_url	= window.location.href;
        url_arr		= current_url.split("#");
        last_char	= url_arr[0].substr( url_arr[0].length -1 );
        current_url	= last_char == "/" ? url_arr[0].slice(0, -1) : url_arr[0];

        // get_tinymce_content

        console.log( form_data );

        $(this).html( "<i class='fa fa-cog fa-spin'></i>" );

        $.ajax(
            {
                type: 'POST',
                context: this,
                url:qa_ajax.qa_ajaxurl,
                data: {
                    "action" 	: "qa_ajax_answer_update",
                    "form_data" : form_data,
                },
                success: function( response ) {

                    var data = JSON.parse( response );

                    url = data['url'];
                    console.log(data['answer_id']);

                    $('.form-answer-post .answer_posting_notice').html( data['html'] );
                    $(this).html( __html__ );

                    if( data['answer_id'] ) {

                        window.location.href = url;
                    }



                } });
    })






















    $(document).on('click', '.qa-breadcrumb .notifications .qa_breadcrumb_refresh, .qa-notifications .qa_breadcrumb_refresh', function (){
		
		//$('.qa-breadcrumb .menu-box .menu-box-hover .notifications').html( "<i class='fa fa-spin fa-cog'></i>" );

		$(this).children('.fa').addClass('fa-spin');
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action" : "qa_ajax_getnotifications", 
		},
		success: function(response) {
				
			var data	= JSON.parse(response)
			var count	= data['count'];	
			var html	= data['html'];	
			
			$('.qa-breadcrumb .menu-box .bubble').html( count );
            $('.qa-notifications .count').html( '('+count+')' );
			$('.qa-breadcrumb .notifications .list-items').html( html );
            $('.qa-notifications .list-items').html( html );
            $(this).children('.fa').removeClass('fa-spin');
			}
		});
		
		$('.qa-breadcrumb .menu-box .menu-box-hover').fadeIn();
	})
	
	$(document).on('mouseleave', '.qa-breadcrumb .menu-box', function (){
		
		$('.qa-breadcrumb .menu-box .menu-box-hover').fadeOut();
	})
	
	$(document).on('click', ".qa-polls li", function() {
		
		data_id = $(this).attr('data-id');
		q_id = $(this).attr('q_id');		
		$('.qa-polls li').removeClass('active');
		
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			}
		else{
			$(this).addClass('active');
			}
		//alert(data_id);
		$('.poll-result').fadeIn();
		$('.poll-result .loading').fadeIn();
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"		: "qa_ajax_poll", 
			"data_id"	: data_id,
			"q_id"	: q_id,			

		},
		success: function(data) {
				
						var response 		= JSON.parse(data)
						var html 	= response['html'];	
						var error 	= response['error'];								
				
				
					$('.poll-result .results').html(html);
					$('.poll-result .loading').fadeOut();
					
					if ( error ) {
						$('.toast').text(error);
						$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
						return;
					} 
					
					
					
				}
			});
		
		
		
		
		
		})

	$('.questions-archive #keyword').autocomplete({

        //search:keyword,
        classes: {
            "ui-autocomplete": "highlight"
        },

        source: function(keyword, response){
            console.log(keyword);

            $.ajax({
                type: 'POST',
                context: this,
                url:qa_ajax.qa_ajaxurl,
                data: {
                    "action": "qa_ajax_get_keyword_suggestion",
                    "keyword":keyword,
                },
                success: function(data){

                    data = JSON.parse(data);
                    response(data);
                    //alert(arr_html);
                    //console.log(data);
                    //console.log(data);
                }
            });

        }
    });

	$(document).on('keyup', ".question-submit #post_title", function() {
		

		$(this).attr('autocomplete','off');
		title = $(this).val();
		$('.suggestion-title, .loading').fadeIn();
		
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"		: "qa_ajax_question_suggestion", 
			"title"	: title,

		},
		success: function(data) {
				
					$('.suggestions-list').html(data);
					$('.loading').fadeOut();
				}
			});
		
		
		})
	
	$(document).on('click', '.notify-mark', function() {		
		
		var notify_id = $(this).attr('notify_id');
		//alert(notify_id);
		// return;
		
		bubble_count = parseInt($('.bubble').text());
		
		
		//alert(bubble_count);
		
		if(bubble_count==1){
			$('.bubble').fadeOut();
			}
		
		
		//alert(bubble_count);
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_ajax_notify_mark", 
			"notify_id"	: notify_id,
		},
		success: function(data) {
			
			//alert(data);
			
			var response = JSON.parse(data)
			
			status = response['status'];
			icon = response['icon'];			
			
			if(status=='read'){
				
				//$(this).parent().fadeOut();
				
				bubble_count = (bubble_count-1);
				$('.bubble').text(bubble_count);
				$(this).html(icon);
				}
			else if(status=='unread'){
				$(this).html(icon);
				
				bubble_count = (bubble_count+1);
				$('.bubble').text(bubble_count);
				
				}
			
			//alert(status);
		
			
			//$('.toast').html( response['toast'] );
			//$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
		
			}
		});
	})	

	$(document).on('click', '.qa-best-answer', function() {		
		
		$(this).find( 'i' ).addClass('fa-spin');
		
		var answer_id = $(this).attr('answer_id');
		
		// return;
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_ajax_best_answer", 
			"answer_id"	: answer_id,
		},
		success: function(data) {
			
			var response = JSON.parse(data)
			
			$(this).find( 'i' ).removeClass('fa-spin');
			$('body').find( '.best_answer' ).removeClass('best_answer');
			$('.all-single-answer').find( '.single-answer' ).removeClass('list_best_answer');
	
			if( response['status'] == 'updated' ) {
			
				$(this).addClass('best_answer');
				$( '#single-answer-' + answer_id ).addClass('list_best_answer');
			}
			$('.toast').html( response['toast'] );
			$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
		
		}
			});
	})	
	
	$(document).on('click', '.qa-featured', function() {		
		
		_HTML = $(this).html();
		//$(this).html( '<i class="fa fa-star fa-spin"></i>' );
		
		var post_id = $(this).attr('post_id');
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_ajax_featured_switch", 
			"post_id"	: post_id,
		},
		success: function(data) {
			
			var response = JSON.parse(data)
			
			
			if( $(this).hasClass('qa-featured-yes') ) {
				
				$(this).removeClass('qa-featured-yes');
				$(this).addClass( response['featured_class'] );
				
			}
			
			if( $(this).hasClass('qa-featured-no') ) {
			
				$(this).removeClass('qa-featured-no');
				$(this).addClass( response['featured_class'] );
				
			}

			//$(this).html(_HTML);
			
			$('.toast').html( response['toast'] );
			$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
		}
			});
	})	
	
	$(document).on('click', '.qa-breadcrumb .bubble ', function() {		
		
		if($(this).hasClass('pending')){
			$(this).removeClass('pending');
			$(this).addClass('hide');			
			}
	})	
	
	$(document).on('change', '.qa_sort_answer', function() {		
		$('#qa_sort_answer_form').submit();
	})
	
	$(document).on('click', '.qa_load_more', function() {
		
		var _paged 		= $(this).attr('_paged');
		var _answer_id 	= $(this).attr('_answer_id');
		var _HTML 		= $(this).html();
		
		$(this).html('<i class="fa fa-asterisk fa-spin"></i>');
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_ajax_load_more_answer", 
			"_paged"	: _paged,
			"_answer_id": _answer_id,
		},
		success: function(data) {
			
			if( data.length > 0 ) {
				
				_paged++;
				$('.all-single-answer').append(data);
				$(this).attr( '_paged', _paged );
				
				var div_pos		= _paged + 1;
				var answer_id 	= $('.single-answer:nth-child('+div_pos+')').attr('id');
				
				$('html, body').animate({
					scrollTop: $("#"+answer_id).offset().top
				}, 2000);
			
			}
			$(this).html(_HTML);
		}
			});
		

	})
	
	$(document).on('click', '.qa-add-comment', function() {

		$(this).fadeOut();
		$('.qa-comment-form').fadeIn();
		$('.qa-cancel-comment').fadeIn();

	})

	$(document).on('click', '.qa-cancel-comment', function() {

		$(this).fadeOut();
		$('.qa-comment-form').fadeOut();
		$('.qa-add-comment').fadeIn();

	})
	
	$(document).on('click', '.qa_answer_voted ', function() {

		$( ".all-single-answer .single-answer" ).each(function( index ) {
			
			if( !$(this).hasClass('reviewd') ) $(this).fadeOut();
			
		});

	})
	
	$(document).on('click', '.qa_answer_all ', function() {

		$( ".all-single-answer .single-answer" ).each(function( index ) {
			
			$(this).fadeIn();
			
		});

	})
		
	$(document).on('click', '.qa-comment-action', function() {

		var _status 	= $(this).attr('status');
		if( _status 	== 0 ) return;
		
		var comment_id 	= $(this).attr('comment_id');
		var user_id 	= $(this).attr('user_id');
		var action 		= $(this).attr('action');

		$(this).html('<i class="fa fa-cog red fa-spin"></i>');
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_do_comment_flag_action", 
			"act"		: action,
			"comment_id": comment_id,
			"user_id"	: user_id
		},
		success: function(data) {
			
			if( action == 'flag' ) $(this).attr('action','unflag');
			if( action == 'unflag' ) $(this).attr('action','flag');
			
			$(this).html(data);
		
		}
			});
	})

    $(document).on('click', '.qa-comment-flag-action', function() {

        $(this).children('.waiting').fadeIn();


        var comment_id 	= $(this).attr('comment_id');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url:qa_ajax.qa_ajaxurl,
                data: {
                    "action"	: "qa_ajax_comment_flag",
                    "comment_id": comment_id,
                },
                success: function(data) {

                    var html = JSON.parse(data)

                    var flag_text = html['flag_text'];
                    var is_error = html['is_error'];
                    var message = html['message'];
                    var flag_count = html['flag_count'];

                    $(this).children('.flag-text').html(flag_text);
                    $(this).children('.flag-count').html('('+flag_count+')');

                    $(this).children('.waiting').fadeOut();
                    $('.toast').text( message );
                    $('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);


                    //console.log(is_error);
                    //console.log(post_id);
                    // if( action == 'flag' ) $(this).attr('action','unflag');
                    //if( action == 'unflag' ) $(this).attr('action','flag');

                    //$(this).html(data);

                }
            });
    })

    $(document).on('click', '.comment-vote-action', function() {

    	$(this).parent().children('.comment-vote-action').removeClass('comment-votted');


    	$(this).parent().children('.comment-vote-count').html('<i class="fa fa-cog fa-spin"></i>');

        var vote_type 	= $(this).attr('vote_type');
        var comment_id 	= $(this).attr('comment_id');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url:qa_ajax.qa_ajaxurl,
                data: {
                    "action"	: "qa_ajax_comment_vote",
                    "comment_id": comment_id,
                    "vote_type": vote_type,
                },
                success: function(data) {

                    var html = JSON.parse(data)


                    var is_error = html['is_error'];
                    var message = html['message'];
                    var vote_count = html['vote_count'];

                    if(is_error=='no'){

                        $(this).addClass('comment-votted');
					}

                    $(this).parent().children('.comment-vote-count').html(vote_count);





                    $('.toast').text( message );
                    $('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
                    //$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);

                    console.log(vote_count);
                    //console.log(comment_id);
                    // if( action == 'flag' ) $(this).attr('action','unflag');
                    //if( action == 'unflag' ) $(this).attr('action','flag');

                    //$(this).html(data);

                }
            });
    })

    $(document).on('click', '.qa-flag-action', function() {

        $(this).children('.waiting').fadeIn();


        var post_id 	= $(this).attr('post_id');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url:qa_ajax.qa_ajaxurl,
                data: {
                    "action"	: "qa_ajax_post_flag",
                    "post_id": post_id,
                },
                success: function(data) {

                    var html = JSON.parse(data)

                    var flag_text = html['flag_text'];
                    var is_error = html['is_error'];
                    var message = html['message'];
                    var flag_count = html['flag_count'];

                    $(this).children('.flag-text').html(flag_text);
                    $(this).children('.flag-count').html('('+flag_count+')');

                    $(this).children('.waiting').fadeOut();
                    $('.toast').text( message );
                    $('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);


                    //console.log(is_error);
                	//console.log(post_id);
                   // if( action == 'flag' ) $(this).attr('action','unflag');
                    //if( action == 'unflag' ) $(this).attr('action','flag');

                    //$(this).html(data);

                }
            });
    })






    $(document).on('mouseover', '.qa-user-card-loader', function() {

        var author_id 	= $(this).attr('author_id');
        var has_loaded 	= $(this).attr('has_loaded');




       // if(has_loaded=='no'){
            $.ajax(
                {
                    type: 'POST',
                    context: this,
                    url:qa_ajax.qa_ajaxurl,
                    data: {
                        "action"	: "qa_ajax_user_card",
                        "author_id": author_id,
                    },
                    success: function(response){


                        $(this).children('.qa-user-card').children('.card-loading').fadeOut();

                        var data = JSON.parse(response);

                        var html = data['html'];

                        $(this).children('.qa-user-card').children('.card-data').html(html);

                        $(this).attr('has_loaded','yes');
                        //console.log(html);

                    }
                });

		//}


    })




    $(document).on('click', '.qa-follow', function() {

        var author_id 	= $(this).attr('author_id');


        //alert('Hello');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url:qa_ajax.qa_ajaxurl,
                data: {
                    "action"	: "qa_ajax_user_follow",
                    "author_id"	: author_id,
                },
                success: function(data) {

                    var html = JSON.parse(data)

                    //$(this).html( html['html'] );

                    toast_html = html['toast_html'];
                    action = html['action'];

                    if(action=='unfollow'){

                        if($(this).hasClass('following')){

                            $(this).removeClass('following');

                        }
                        $(this).text('Follow');
                        $(this).addClass(action);

                    }
                    else if(action=='following'){

                        if($(this).hasClass('unfollow')){

                            $(this).removeClass('unfollow');

                        }
                        $(this).text('Following');

                        $(this).addClass(action);
                    }
                    else{

                    }





                    console.log(html);

                    $('.toast').html(toast_html);
                    $('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);

                }
            });
    })

































    $(document).on('click', '.answer-post-header', function() {
		
		var _status = $(this).attr('_status');
		
		if( _status == 1 ) {
			$('.apost_header_status').removeClass('fa-compress');
			$('.apost_header_status').addClass('fa-expand');
			$('.answer-post form').fadeOut();
			$(this).attr('_status','0');
		} else {
			$('.apost_header_status').addClass('fa-compress');
			$('.apost_header_status').removeClass('fa-expand');
			$('.answer-post form').fadeIn();
			$(this).attr('_status','1');
		}

	})
	
	$(document).on('click', '.qa-answer-reply', function() {
		var post_id 	= $(this).attr('post_id');
		$('.qa-reply-popup-' + post_id ).fadeIn();
	})

	$(document).on('click', '.qa-reply-popup .close', function() {

		$('.qa-reply-popup').fadeOut();
	})

	$(document).on('click', '.qa-subscribe', function() {
		
		var post_id 	= $(this).attr('post_id');
		var _HTML 		= $(this).html();
		//$(this).html('<i class="fa fa-cog red fa-spin"></i>');
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_subscribe_action", 
			"post_id"	: post_id,
		},
		success: function(data) {
			
			var html = JSON.parse(data)
			
			//$(this).html( html['html'] );
						
			subscribe_class = html['subscribe_class'];
			//alert(is_solved);
			
			
			if(subscribe_class == 'not-subscribed'){
				$(this).removeClass('subscribed');
				$(this).addClass('not-subscribed');
		
				}
			else if(subscribe_class == 'subscribed'){
				
				$(this).removeClass('not-subscribed');
				$(this).addClass('subscribed');	

				}
				
				
			$('.toast').text( html['toast'] );
			$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
			
		}
			});
	})

	$(document).on('click', '.qa-is-solved', function() {
		
		var post_id 	= $(this).attr('post_id');
		var _HTML 		= $(this).html();
		//$(this).html('<i class="fa fa-cog red fa-spin"></i>');
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_is_solved_action", 
			"post_id"	: post_id,
		},
		success: function(data) {
			
			var html 	= JSON.parse(data)
						
			is_solved 	= html['is_solved'];

			if(is_solved == 'solved'){
				$(this).removeClass('unsolved');
				$(this).addClass('solved');
			}
			else if(is_solved == 'unsolved'){
				
				$(this).removeClass('solved');
				$(this).addClass('unsolved');
			}
				
				
			$('.toast').text( html['toast'] );
			$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
			
		}
			});
	})

	$(document).on('click', '.qa-reply-form-submit', function() {
		
		var post_id 	= $(this).attr('id');
		var reply_msg 	= $( '#qa-answer-reply-' +  post_id ).val();
		
		if( reply_msg.length === 0 ) {
			
			$(this).prev('textarea').css( 'border', '1px solid red');
			
			$('.toast').text('Empty data !');
			$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
			return;
		}
		
		
		var _HTML 		= $(this).html();
		$(this).html('<i class="fa fa-cog red fa-spin"></i>');		
		
		$.ajax(
			{
			type: 'POST',
			context: this,
			url:qa_ajax.qa_ajaxurl,
			data: {
				"action"	: "qa_answer_reply_action",
				"post_id"	: post_id,
				"reply_msg"	: reply_msg
			},
			success: function(data) {

				$(this).prev('textarea').val('');

				$('.qa-reply-popup').fadeOut();
				$('.qa-answer-comment-reply-' + post_id).append( data );
                $('.qa-question-comment-reply-' + post_id).append( data );
				$(this).html( _HTML );
			}
			});
	})

	$(document).on('click', '.qa-thumb-down', function() {
		
		var post_id 	= $(this).attr('post_id');
		var _HTML 		= $(this).html();
		$(this).html('<i class="fa fa-cog fa-spin"></i>');		
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_answer_thumbsdown_action", 
			"post_id"	: post_id,
		},
		success: function(data) {
			$(this).html(_HTML);
			var response 		= JSON.parse(data)
			var review_value 	= response['review_value'];
			var error 			= response['error'];
			var status 			= response['status'];
			
			if ( error ) {
				$('.toast').text(error);
				$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
				return;
			} 
			$('.net-vote-count-' + post_id).text( review_value );			
			
			_ID = '#' + $(this).parent().parent().attr('id');

			//console.log(_ID);

            $( '.qa-single-vote-'+ post_id + ' .qa-thumb-up').removeClass('votted');
            $( '.qa-single-vote-'+ post_id + ' .qa-thumb-down').removeClass('votted');

			//$( _ID + 'qa-single-vote .qa-thumb-up').removeClass('votted');
            $( '.qa-single-vote-'+ post_id + ' .qa-thumb-down').removeClass('votted');

			//$( _ID + ' .qa-thumb-down').removeClass('votted');
			
			( status == 'up' ) ? $( '.qa-single-vote-' + post_id + ' .qa-thumb-down').addClass('votted') : $( '.qa-single-vote-' + post_id + ' .qa-thumb-up').addClass('votted');
			
		}
			});
	})

	$(document).on('click', '.qa-thumb-up', function() {
		
		var post_id 	= $(this).attr('post_id');
		
		var _HTML 		= $(this).html();
		$(this).html('<i class="fa fa-cog fa-spin"></i>');		
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:qa_ajax.qa_ajaxurl,
		data: {
			"action"	: "qa_answer_thumbsup_action", 
			"post_id"	: post_id,
		},
		success: function(data) {
			$(this).html(_HTML);
			var response 		= JSON.parse(data)
			var review_value 	= response['review_value'];
			var error 			= response['error'];
			var status 			= response['status'];
		
			if ( error ) {
				$('.toast').text(error);
				$('.toast').stop().fadeIn(400).delay(3000).fadeOut(400);
				return;
			} 
			$('.net-vote-count-' + post_id).text( review_value );			
			
			//_ID = '#' + $(this).parent().parent().attr('id');
			
			$( '.qa-single-vote-'+ post_id + ' .qa-thumb-up').removeClass('votted');
			$( '.qa-single-vote-'+ post_id + ' .qa-thumb-down').removeClass('votted');
			
			
			( status == 'up' ) ?  $( '.qa-single-vote-'+ post_id + ' .qa-thumb-down').addClass('votted') : $( '.qa-single-vote-'+ post_id + ' .qa-thumb-up').addClass('votted');
			
			
			
			
		}
			});
	})

});	

