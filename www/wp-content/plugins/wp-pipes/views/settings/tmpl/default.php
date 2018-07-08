<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: default.php 170 2014-01-26 06:34:40Z thongta $
 * @author               thimpress.com
 * @copyright        2014 thimpress.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

wp_enqueue_style( 'pipes-font-awesome-css' );
wp_enqueue_style( 'pipes-bootstrap-min' );
wp_enqueue_style( 'pipes-bootstrap-extended' );
wp_enqueue_style( 'jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_script( 'pipes-bootstrap-min' );
$tab = filter_input( INPUT_GET, 'tab' );
if(!$tab){
	$tab = 'requirements';
}

require_once( OBGRAB_HELPERS . 'requirements.php' );
$requirements = new AppRequirements;
$requirements->checkRequirements();

?>
<script>
	jQuery( document ).ready(function() {
		jQuery('#pipes_settings a').click(function (e) {
			e.preventDefault()
			jQuery(this).tab('show')
		})
	});

</script>
<style>
	.tab-content {
		background-color: white;
		padding: 10px;
		border: 1px solid #ddd;
		border-top-width: 0px;
	}
</style>

<h2><?php _e( 'WP Pipes plugin Settings' ); ?></h2>

<?php
_e(PIPES::show_message( false ));
?>
<div class="foobla" style="padding-top:15px;">
	<form method="post" action="">
		<table class="form-table">
			<?php
			foreach ( $this->configs->items as $setting ) {
				_e('<tr valign="top"');
				// Start at settings seems to be not necessary
				if ($setting->option_name == 'pipes_start_at') {
					_e(' class="hidden"');
				}
				_e('>');

				switch ( $setting->option_name ) {
					case 'pipes_cronjob_active':
						_e('<th scope="row">' . __( 'Cronjob Active' ) . '</th>');
						_e('<td>');

						_e('<fieldset><legend class="screen-reader-text"><span>Cronjob Active</span></legend>
									<label title="Yes"><input type="radio" name="' . $setting->option_name . '" value="1" ' . ( ( $setting->option_value == 1 ) ? 'checked="checked"' : '' ) . '>
									<span>Yes, I want to run my Pipes automatically when someone access my Wordpress site.</span></label><br>
									<label title="No"><input type="radio" name="' . $setting->option_name . '" value="0" ' . ( ( $setting->option_value == 0 ) ? 'checked="checked"' : '' ) . '>
									<span>No, I will create a cronjob task myself to run the script <a href="'.get_site_url().'/?pipes=cron&task=callaio" target="_blank">'.get_site_url().'/?pipes=cron&task=callaio</a>. More instruction can be found at <a href="http://thimpress.com/schedule-auto-posting-pipes-cron-job/" target="_blank">this cronjob guideline</a></span>.</label><br />
									</fieldset>');
						if($this->area != '' && $this->area != 'Vietnam'){
							_e('<span style="color:red;">One of our partners released a plugin for you setting up cronjob with any specific pipes. Check it out at <a href="http://virtualstuff.info/product/cronjob-single-pipe/" alt="cronjob single pipe">http://virtualstuff.info/product/cronjob-single-pipe/</a></span>');
						}
						_e('</td>');
						break;
					case 'pipes_active':
						//						_e('<div class="alert alert-info">There are two methods to execute WPPipes Pipes automatically.
						//								<ol>
						//									<li>Activating "Auto Run" below to execute Pipes over your Joomla site. By using this method, your Pipes will be executed every time your Joomla site get accessed over Site or Admin area.</li>
						//									<li>Create a cronjob task to the URL: http://yourjoomlasite.com/wp-admin/pipes.xyz&amp;task=callaio<br>Details instruction can be found <a href="http://thimpress.com/kb/wppipes/4983-setup-server-side-cronjob-for-wppipes" target="_blank">here</a></li>
						//								</ol>
						//							</div>');
						_e('<th scope="row">' . __( 'Allow Auto Run' ) . '</th>');
						_e('<td>');

						_e('<fieldset><legend class="screen-reader-text"><span>Auto Run</span></legend>
									<label title="Yes"><input type="radio" name="' . $setting->option_name . '" value="1" ' . ( ( $setting->option_value == 1 ) ? 'checked="checked"' : '' ) . '>
									<span>Yes, I want to run my Pipes in both manually and automatically methods.</span></label><br>
									<label title="No"><input type="radio" name="' . $setting->option_name . '" value="0" ' . ( ( $setting->option_value == 0 ) ? 'checked="checked"' : '' ) . '>
									<span>No, I want to run my Pipes manually.</span></label><br>
									</fieldset>');
						_e('</td>');
						break;
					case 'pipes_schedule':
						_e('<th scope="row" style="display:none;"><label for="' . $setting->option_name . '">Run every</label></th>');
						_e('<td style="display:none;">');
						_e('<select name="' . $setting->option_name . '" id="' . $setting->option_name . '">
										<option ' . ( ( $setting->option_value == 'i5' ) ? 'selected="selected"' : '' ) . ' value="i5">5 minutes</option>
										<option ' . ( ( $setting->option_value == 'i10' ) ? 'selected="selected"' : '' ) . ' value="i10">10 minutes</option>
										<option ' . ( ( $setting->option_value == 'i15' ) ? 'selected="selected"' : '' ) . ' value="i15">15 minutes</option>
										<option ' . ( ( $setting->option_value == 'i20' ) ? 'selected="selected"' : '' ) . ' value="i20">20 minutes</option>
										<option ' . ( ( $setting->option_value == 'i25' ) ? 'selected="selected"' : '' ) . ' value="i25">25 minutes</option>
										<option ' . ( ( $setting->option_value == 'i30' ) ? 'selected="selected"' : '' ) . ' value="i30">30 minutes</option>
										<option ' . ( ( $setting->option_value == 'h1' ) ? 'selected="selected"' : '' ) . ' value="h1">1 hour</option>
										<option ' . ( ( $setting->option_value == 'h2' ) ? 'selected="selected"' : '' ) . ' value="h2">2 hours</option>
										<option ' . ( ( $setting->option_value == 'h3' ) ? 'selected="selected"' : '' ) . ' value="h3">3 hours</option>
										<option ' . ( ( $setting->option_value == 'h4' ) ? 'selected="selected"' : '' ) . ' value="h4">4 hours</option>
										<option ' . ( ( $setting->option_value == 'h6' ) ? 'selected="selected"' : '' ) . ' value="h6">6 hours</option>
										<option ' . ( ( $setting->option_value == 'h8' ) ? 'selected="selected"' : '' ) . ' value="h8">8 hours</option>
										<option ' . ( ( $setting->option_value == 'h12' ) ? 'selected="selected"' : '' ) . ' value="h12">12 hours</option>
										<option ' . ( ( $setting->option_value == 'h24' ) ? 'selected="selected"' : '' ) . ' value="h24">24 hours</option>
									</select>');
						_e('</td>');
						break;
					case 'pipes_start_at':
						if ( '' == $setting->option_value ) {
							$setting->option_value = '0';
						}
						$date   = date( 'Y-m-d');
						$hour   = 0;
						$minute = 0;
						_e('<th scope="row"><label for="' . $setting->option_name . '">' . __( 'Start from' ) . '</label></th>');
						_e('<td>');
						_e('<input style="max-width: 250px" name="' . $setting->option_name . '" type="text" id="' . $setting->option_name . '" value="' . $date . '" class="regular-text">');
						_e(' at <input type="number" min="0" max="23" id="pipes_hh" name="pipes_hh" value="' . $hour . '" size="2" maxlength="2" autocomplete="off">
										 : <input type="number" min="0" max="59" id="pipes_mn" name="pipes_mn" value="' . $minute . '" size="2" maxlength="2" autocomplete="off"></td>');
						_e("<script>
								jQuery(document).ready(function() {
									jQuery('#" . $setting->option_name . "').datepicker({
									dateFormat : 'yy-mm-dd'
									});
								});
								</script>");
					case 'pipes_not_use_cache':
						_e('<th scope="row">' . __( 'Not Use Cache' ) . '</th>');
						_e('<td>');

						_e('<fieldset><legend class="screen-reader-text"><span>Not Use Cache</span></legend>
									<label title="Yes"><input type="radio" name="' . $setting->option_name . '" value="1" ' . ( ( $setting->option_value == 1 ) ? 'checked="checked"' : '' ) . '>
									<span>Yes, I want the cronjob will be executed getting data directly from the source, not from Cache.</span></label><br>
									<label title="No"><input type="radio" name="' . $setting->option_name . '" value="0" ' . ( ( $setting->option_value == 0 ) ? 'checked="checked"' : '' ) . '>
									<span>No, cronjob will get data from the cache if the cache is not expired</span>.</label><br />
									</fieldset>');
						_e('</td>');
						break;
					default:
						break;
				}

				_e('</tr>');
			}
			?>
		</table>
		<input type="hidden" name="task" value="save" />

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
			<a href="admin.php?page=pipes.pipes&task=pipes_restore_default_options" class="btn btn-primary btn-default" style="float: right;">Restore</a>
			<a href="admin.php?page=pipes.pipes&task=delete_cache_folder" class="btn btn-primary btn-default" style="float: right; margin-right: 20px;">Clear Cache</a>
		</p>
	</form>
</div>
<div class="welcome-panel">
	<div class="col-wrap">
		<?php $requirements->displayResults(); ?>
	</div>
</div>
</div>