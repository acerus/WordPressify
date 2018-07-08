<p><?php _e( 'Installing a child theme allows for an easy way to add extra customization to your website, as well as ensures any changes made are not ovewritten when your parent theme is updated. <strong>This is highly recommended.</strong>', 'jobify' ); ?></p>

<p><button class="button button-primary button-large uct-activate" href="javascript:;"><?php esc_html_e( 'Use a Child Theme', 'jobify' ); ?></button></p>

<script>
jQuery(document).ready(function($) {
	$(document).on( 'uct_activated', function(event, response) {
		var $stepTitle = $( '#step-status-child-theme' );

		if ( response.success ) {
			$( '.uct-activate' ).attr( 'disabled', 'disabled' );
			$stepTitle.text( $stepTitle.data( 'string-complete' ) ).removeClass( 'step-incomplete' ).addClass( 'step-complete' );
		}
	});
});
</script>
