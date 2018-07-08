/*

PickForm
PickPlugins.com

*/


jQuery(document).ready(function($)
	{
		

		
/*

		$(document).on('submit', '.pickform form', function(e)
			{		
				e.preventDefault();
				alert('Hello');
				var form_input = $('form').serializeArray();
				
				alert(fornm_input);
			})
		


*/
		
		
		
		

		$(function() {
			$( ".pickform .repatble" ).sortable();
			//$( ".items-container" ).disableSelection(); //{ handle: '.section-header' }
		});


		$(document).on('click', '.pickform .repatble .add-field', function()
			{	
				
				var option_id = $(this).attr('option-id');
				
				var id = $.now();

				var html = '<div class="single"><input type="text" name="'+option_id+'['+id+']" value="" /><input class="remove-field" type="button" value="Remove"></div>';
				//alert(html);
					$(this).prev('.repatble .items').append(html);
					
					
			})

		$(document).on('click', '.pickform .repatble .remove-field', function()
			{	
				if(confirm("Do you really want to remove ?")){
					$(this).prev().remove();
					$(this).remove();
					}

				
					
					
				})



	});	







