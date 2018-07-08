jQuery(document).ready(function($)
	{


            $('.qa_date').datepicker({
                dateFormat : 'yy-mm-dd'
            });


		$(document).on('click', '.repatble .add-field', function()
			{	
			
				var option_id = $(this).attr('option-id');
				//alert(option_id);
				var id = $.now();

				var html = '<div class="single"><input type="text" name="'+option_id+'['+id+']" value="" /><input class="remove-field button" type="button" value="Remove"></div>';
				//alert(html);
					$(this).prev('.repatble-items').append(html);
					
					
				})

		$(document).on('click', '.repatble .remove-field', function()
			{	
				var is_confirm = $(this).attr('confirm');
				
				if(is_confirm=='yes'){
					
						$(this).prev().remove();
						$(this).remove();	
					
					}
				else{
						$(this).attr('confirm','yes');
						$(this).val('Confirm');
						$(this).css('color','#ff6d4b');
					
					}

				})












		$(document).on('click', '.tab-nav li', function()
			{
				$(".active").removeClass("active");
				$(this).addClass("active");
				
				var nav = $(this).attr("nav");
				
				$(".box li.tab-box").css("display","none");
				$(".box"+nav).css("display","block");
		
			})


		$(document).on('click', '.field-set .update-field-set', function()
			{

				if(confirm("Do you really want to update ?")){
					
					$.ajax(
						{
					type: 'POST',
					context: this,
					url:qa_ajax.qa_ajaxurl,
					data: {"action": "qa_admin_update_field_set", },
					success: function(data)
							{	
							
								$(this).html('Update Done');
							
								location.reload();
							}
						});
					
					}

				})




		$(document).on('click', '.field-set .reset-field-set', function()
			{

				if(confirm("Do you really want to reset ?")){
					
					$.ajax(
						{
					type: 'POST',
					context: this,
					url:qa_ajax.qa_ajaxurl,
					data: {"action": "qa_admin_reset_field_set", },
					success: function(data)
							{	
							
								$(this).html('Reset Done');
							
								location.reload();
							}
						});
					
					}

				})









	});	







