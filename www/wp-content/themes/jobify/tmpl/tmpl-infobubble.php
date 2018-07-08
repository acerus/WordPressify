<?php
/**
 * Info Bubble JS Template
 *
 * @package Jobify
 * @since 3.0.0
 * @package 3.8.0
 */
?>
<script id="tmpl-infoBubble" type="text/template">
	<# if ( typeof( data.title ) != 'undefined') { #>
		<a href="{{{ data.href }}}">{{{ data.title }}}</a>
	<# } #>
</script>
