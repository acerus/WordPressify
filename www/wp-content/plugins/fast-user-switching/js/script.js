jQuery('document').ready(function(){

	$ = jQuery;

	var form = $('form#tikemp_usearch_form');

	form.submit(function(e) {

		e.preventDefault();

		var user = $('#tikemp_username').val();
		var nonce = $('input[name="tikemp_search_nonce"]').val();

		$.ajax({
	        type : 'POST',
	        url : tikemp_ajax_url,
	        data : {
		        action : 'tikemp_user_search',
		        username : user,
		        nonce : nonce
	        },
	        beforeSend : function() {
		        $('#tikemp_username').prop('disabled',true);
	        },
	        success : function( response ) {
		        $('#tikemp_username').prop( 'disabled', false );
		        $('#tikemp_usearch_result').html( response );
	        }
        });

		return false;
	});

	$('#wp-admin-bar-tikemp_impresonate_user').click(function(){
		$('input[id="tikemp_username"]').focus();
	});

	$('#tikemp_usearch_result').niceScroll({
		autohidemode:'leave'
	});

});