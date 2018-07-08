<?php 
global $post;
$current_url = get_permalink( $post->ID );
?>
<div class="um-social-login-overlay">

	<a href="<?php echo $current_url; ?>" class="um-social-login-cancel"><i class="um-icon-ios-close-empty"></i></a>
	
</div>

<div class="um-social-login-wrap">
	<?php echo do_shortcode('[ultimatemember form_id=' . $this->form_id() . ']'); ?>
</div>