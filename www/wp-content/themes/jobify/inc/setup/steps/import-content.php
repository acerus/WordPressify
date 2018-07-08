<?php
/**
 * Nasty
 */

// lib needs better helper functions
$strings = astoundify_contentimporter_get_strings();

function jobify_import_content_plugin( $plugin ) {
?>
<span class="active"><span class="dashicons dashicons-yes"></span></span>
<span class="inactive"><span class="dashicons dashicons-no"></span></span>

<?php echo $plugin['label']; ?>

<span class="inactive">&mdash; <?php _e( 'Demo content for this plugin will not be imported.', 'jobify' ); ?></span>
<?php
}
?>

<form id="astoundify-content-importer" action="" method="">

	<div id="content-pack">
		<p style="margin: 30px 0 0 -15px">
			<label for="classic" class="content-pack">
				<span class="content-pack-label">Classic</span>
				<input type="radio" value="classic" name="demo_style" id="classic" checked="checked" />
				<span class="content-pack-img">
					<span class="dashicons dashicons-yes"></span>
					<img src="<?php echo get_template_directory_uri(); ?>/inc/setup/assets/images/content-classic.png" />
				</span>
				<span class="screen-reader-text"><?php _e( 'Classic', 'jobify' ); ?></span>
			</label>
			<label for="extended" class="content-pack">
				<span class="content-pack-label">Extended</span>
				<input type="radio" value="extended" name="demo_style" id="extended" />
				<span class="content-pack-img">
					<span class="dashicons dashicons-yes"></span>
					<img src="<?php echo get_template_directory_uri(); ?>/inc/setup/assets/images/content-extended.png" />
				</span>
				<span class="screen-reader-text"><?php _e( 'Extended', 'jobify' ); ?></span>
			</label>
			<label for="coming-soon" class="content-pack">
				<span class="content-pack-label">Coming Soon</span>
				<input type="radio" value="coming-soon" name="" disabled="disabled" id="coming-soon" />
				<span class="content-pack-img">
					<img src="<?php echo get_template_directory_uri(); ?>/inc/setup/assets/images/content-coming-soon.png" />
				</span>
				<span class="screen-reader-text"><?php _e( 'Coming Soon...', 'jobify' ); ?></span>
			</label>
		</p>
	</div>

	<div id="import-summary" style="display: none;">
		<p><?php _e( 'Please do not navigate away from this page. This process may take a few minutes depending on your server capabilities and internet connection.', 'jobify' ); ?></p>

		<p><strong id="import-status"><?php _e( 'Summary:', 'jobify' ); ?></strong></p>

		<?php foreach ( $strings['type_labels'] as $key => $labels ) : ?>
		<p id="import-type-<?php echo esc_attr( $key ); ?>" class="import-type">
			<span class="dashicons import-type-<?php echo esc_attr( $key ); ?>"></span>&nbsp;
			<strong class="process-type"><?php echo esc_attr( $labels[1] ); ?>:</strong>
			<span class="process-count">
				<span id="<?php echo esc_attr( $key ); ?>-processed">0</span> / <span id="<?php echo esc_attr( $key ); ?>-total">0</span>
			</span>
			<span id="<?php echo esc_attr( $key ); ?>-spinner" class="spinner"></span>
		</p>
		<?php endforeach; ?>
	</div>

	<ul id="import-errors"></ul>

	<div id="plugins-to-import">
		<p><?php _e( 'Jobify requires the following plugins to be active in order to import content.', 'jobify' ); ?></p>

		<ul>
		<?php foreach ( astoundify_contentimporter_get_required_plugins() as $key => $plugin ) : ?>
		<li id="<?php echo esc_attr( $key ); ?>" class="<?php echo $plugin['condition'] ? 'active' : 'inactive'; ?>">
			<?php jobify_import_content_plugin( $plugin ); ?>
		</li>
		<?php endforeach; ?>
		</ul>

		<p><?php _e( 'Want extra features on your site? Activate the following plugins for even more demo content; saving you setup time!', 'jobify' ); ?></p>

		<ul id="astoundify-recommended-plugins">
		<?php foreach ( astoundify_contentimporter_get_recommended_plugins() as $key => $plugin ) : ?>
		<li id="<?php echo esc_attr( $key ); ?>" class="<?php echo $plugin['condition'] ? 'active' : 'inactive'; ?>" data-pack="<?php echo implode( ' ', $plugin['pack'] ); ?>">
			<?php jobify_import_content_plugin( $plugin ); ?>
		</li>
		<?php endforeach; ?>
		</ul>
	</div>

	<p>
		<?php submit_button( __( 'Import Content', 'jobify' ), 'primary', 'import', false ); ?>
		&nbsp;
		<?php submit_button( __( 'Reset Content', 'jobify' ), 'secondary', 'reset', false ); ?>
	</p>

</form>
