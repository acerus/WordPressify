<?php 
?>
<div class="um um-shortcode-social" id="um-shortcode-social-<?php echo $id; ?>" style="padding:<?php echo $padding; ?>;margin:<?php echo $margin; ?>!important;">

		<div class="um-field">
			
			<div class="um-col-alt">
		
				<?php
				$i = 0; foreach( $o_networks as $provider => $arr ) {
					$i++;
					$class = 'um-left';
					if ( $i % 2 == 0 ) {
						$class = 'um-right';
					}
				?>
				
				<div <?php if ( $button_style == 'floated' ) { echo 'style="display:inline"'; } ?> class="<?php if ( $button_style == 'responsive' ) echo $class . ' um-half'; ?>">
					<a href="<?php echo UM()->Social_Login_API()->login_url( $provider ); ?>" title="<?php echo $arr['button']; ?>" class="um-button um-alt um-button-social um-button-<?php echo $provider; ?>" data-redirect-url="<?php echo esc_url( UM()->Social_Login_API()->get_redirect_url() ); ?>">
					<?php if ( $show_icons ) { ?>
						<i class="<?php echo $arr['icon']; ?>" <?php if ( $show_labels ) { echo 'style="margin-right: 8px;"'; } ?>></i>
					<?php } ?>
					<?php if ( $show_labels ) { ?>
					<span><?php echo $arr['button']; ?></span>
					<?php } ?>
					</a>
				</div>

				<?php if ( $button_style == 'default' ) { ?><div class="um-clear"></div><?php } ?>

				<?php 
					if ( $i % 2 == 0 && count($o_networks) != $i && $button_style == 'responsive' ) {
						echo '<div class="um-clear"></div></div><div class="um-col-alt um-col-alt-s">';
					}
				}
				?>
				
				<div class="um-clear"></div>
			
			</div>
			
		</div>

		<style type="text/css">
		
			div#um-shortcode-social-<?php echo $id; ?> div.um-field {padding: 0}
			
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-social {
				font-size: <?php echo ( $fontsize ) ? $fontsize : '15px'; ?>;
				padding: <?php echo ( !empty( $button_padding ) ) ? $button_padding : '16px 20px'; ?> !important;
			}
			
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-social i {
				font-size: <?php echo ( $iconsize ) ? $iconsize : '18px'; ?>;
				width: <?php echo ( $iconsize ) ? $iconsize : '18px'; ?>;
				top: auto;
				vertical-align: baseline !important;
				margin-right: 0;
			}
			
			<?php if ( $button_style == 'responsive' ) { ?>
			
			div#um-shortcode-social-<?php echo $id; ?> div.um-field {margin:0 auto; max-width: <?php echo $container_max_width; ?>}
			
			<?php } ?>
			
			<?php if ( $button_style == 'floated' ) { ?>
			
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-social {
				display: inline-block !important;
				float: none !important;
				margin-right: 5px !important;
				margin-left: 5px !important;
				margin-bottom: 10px !important;
				width: auto;
				<?php if ( isset( $button_min_width ) && !empty( $button_min_width ) ) { ?>
				min-width: <?php echo $button_min_width; ?>;
				<?php } ?>
			}
			
			div#um-shortcode-social-<?php echo $id; ?> div.um-field {text-align: center}
			
			<?php } ?>
			
			<?php if ( $button_style == 'default' ) { ?>
			
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-social {
				display: inline-block !important;
				float: none !important;
				margin-bottom: 10px !important;
				width: auto;
				<?php if ( isset( $button_min_width ) && !empty( $button_min_width ) ) { ?>
				min-width: <?php echo $button_min_width; ?>;
				<?php } ?>
			}
			
			div#um-shortcode-social-<?php echo $id; ?> div.um-field {text-align: center}
			
			<?php } ?>
			
			<?php foreach( $o_networks as $provider => $arr ) { ?>
			
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-<?php echo $provider; ?> {background-color: <?php echo $arr['bg']; ?>!important}
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-<?php echo $provider; ?>:hover {background-color: <?php echo $arr['bg_hover']; ?>!important}
			div#um-shortcode-social-<?php echo $id; ?> a.um-button.um-button-<?php echo $provider; ?> {color: <?php echo $arr['color']; ?>!important}
			
			<?php } ?>

		</style>

</div>