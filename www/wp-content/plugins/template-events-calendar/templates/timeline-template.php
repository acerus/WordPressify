<?php
		$event_img.='<!-- Event Image -->';
		$event_img.=tribe_event_featured_image( null, 'full' );	
 		if ($i % 2 == 0) {
				$even_odd = "even";
				} else {
				$even_odd = "odd";
				}

			if($events_date_header !==''){
			$events_html .= '<div class="timeline-year">
						<div class="icon-placeholder">' . $events_date_header . '</div>
						<div class="timeline-bar"></div>
						</div>';
				}		
								
				$events_html.='<div id="post-'.get_the_ID().'" class="timeline-post '.$even_odd.' '.$event_type.' '.$post_parent.' '.$template.'-evt">';
				$events_html .='<div class="timeline-meta '.$template.'-mt">';
				$events_html .= '<div class="meta-details">' ;
				$events_html .=$event_schedule;
				$events_html.='</div>';
				$events_html.=$venue_details_html;
				$events_html.='</div>';	
				$events_html .='<div class="timeline-icon icon-dot-full">
                    	<div class="icon-placeholder"></div>
                        <div class="timeline-bar"></div>
                    </div>';
               $events_html .= '<div class="timeline-content clearfix ' .$even_odd.'">';
     			$events_html .= '<h2 class="content-title">' . $event_title .'</h2>';
			  	$events_html .= '<div class="ctl_info event-description">'; 
			  	$events_html .=$event_img;
			  	 $events_html.= '<div class="content-details">'.$event_content.' </div>';   
			  	
			  	$events_html .='</div></div>';
				$events_html.='</div>';
					$i++;	
