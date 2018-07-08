<?php
$events_html.='<div id="event-'.get_the_ID().'" class="ect-list-post '.$event_type.' '.$post_parent.'">';
				$ev_post_img='';
				$feat_img_url = wp_get_attachment_image_src( get_post_thumbnail_id(),'large');
				if($feat_img_url[0]){
					$ev_post_img=$feat_img_url[0];
				}else{
					$ev_post_img=ECT_PLUGIN_URL."images/event-template-bg.png";
					}
			$bg_styles="background-image:url('$ev_post_img');background-size:cover;background-position:center center;";
			$events_html.='<div class="ect-list-post-left ">';
			$bg_styles="background-image:url('$ev_post_img');background-size:cover;background-position:center center;";
			$events_html.='<div class="ect-list-img" style="'.$bg_styles.'">';
			$events_html.='<a href="'.esc_url( tribe_get_event_link()).'" alt="'.get_the_title().'" rel="bookmark">';
			$events_html .='<div class="ect-list-date">'.$event_schedule.'</div></a>';
			$events_html.='</div></div><!-- left-post close -->';
			$events_html.='<div class="ect-list-post-right">
			<div class="ect-list-post-right-table">';
		if (tribe_has_venue()) {
			$events_html.='<div class="ect-list-description">';
		}else{
			$events_html.='<div class="ect-list-description" style="width:100%;">';
		}
		$events_html.='<h2 class="ect-list-title">'.$event_title.'</h2>';
			$events_html.=$event_content;
			$events_html.='</div>';
			if (tribe_has_venue()) {
			$events_html.=$venue_details_html;
		}else{
			$events_html.='';
		}
			
			$events_html.='</div></div><!-- right-wrapper close -->';
		 	$events_html.='</div><!-- event-loop-end -->';