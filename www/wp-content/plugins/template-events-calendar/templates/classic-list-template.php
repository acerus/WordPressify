<?php
	$event_img.=tribe_event_featured_image( null, 'full' );	
$events_html.='<div id="event-'.get_the_ID().'" class="ect-classic-list-event '.$event_type.'" '.$post_parent.'>';

			$events_html.='<div class="ect-classic-list-event-left ">';
			$events_html .='<div class="ect-classic-list-date">'.$event_schedule.'</div>';
			$events_html.='</div><!-- left-event close -->';
 

			$events_html.='<div class="ect-classic-list-event-right">
			<div class="ect-list-event-right-table">
			<div class="ect-list-description">';
		$events_html.='<h2 class="ect-classic-list-title">'.$event_title.'</h2>';
			$events_html.='<div class="cls-list-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <span class="cls-list-time">'.$ev_time.'</span></div>';
			$events_html.='<div class="classic-list-venue">'.$venue_details_html.'</p>';
			$events_html.='</div>';

			$events_html.='<a href="'.esc_url( tribe_get_event_link()).'" class="tribe-events-read-more" rel="bookmark">'.esc_html__( 'Find out more ', 'the-events-calendar' ).'<i class="fa fa-angle-double-right" aria-hidden="true"></i></a>';
	
			$events_html.='</div></div><!-- right-wrapper close -->';
			 $events_html.='</div><!-- event-loop-end -->';
