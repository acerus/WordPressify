<p><?php _e( 'Manage the appearance and behavior of various theme components with the live customizer.', 'jobify' ); ?></p>

<ul>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[section]=sidebar-widgets-widget-area-home' ) ); ?>"><?php _e( 'Update homepage content', 'jobify' ); ?></a></li>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[panel]=colors' ) ); ?>"><?php _e( 'Adjust colors', 'jobify' ); ?></a></li>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[panel]=listings' ) ); ?>"><?php _e( 'Adjust job styles and layout', 'jobify' ); ?></a></li>
</ul>

<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-primary button-large"><?php _e( 'Launch Customizer', 'jobify' ); ?></a></p>
