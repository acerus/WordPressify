<div class="um-notification-header">
	<div class="um-notification-left"><?php _e('Notifications','um-notifications'); ?></div>
	<div class="um-notification-right">
		<a href="<?php echo UM()->account()->tab_link( 'webnotifications' ); ?>" class="um-notification-i-settings"><i class="um-faicon-cog"></i></a>
		<a href="#" class="um-notification-i-close"><i class="um-icon-android-close"></i></a>
	</div>
	<div class="um-clear"></div>
</div>

<div class="um-notification-ajax">

</div>

<div class="um-notifications-none">
	<i class="um-icon-ios-bell"></i>
	<?php _e('No new notifications','um-notifications'); ?>
</div>