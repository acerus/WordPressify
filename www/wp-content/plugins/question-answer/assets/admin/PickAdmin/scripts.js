jQuery(document).ready(function($)
	{

		$(document).on('click', '.classified-maker-admin .expandable .header', function()
			{
				if($(this).parent().hasClass('active'))
					{
						$(this).parent().removeClass('active');
					}
				else
					{
						$(this).parent().addClass('active');	
					}
				
			
			})	




	});	







