<?php
/*
 Plugin Name:The Events Calendar Shortcode and Templates
 Plugin URI:https://eventscalendartemplates.com
 Description:The Events Calendar Shortcode and Templates plugin provides events design templates and shortcode generator for The Events Calendar Plugin <a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar (by Modern Tribe)</a>.
 Version:1.0.7
 License:GPL2
 Author:Events Calendar Shortcode and Templates
 Author URI:https://eventscalendartemplates.com
 License URI:https://www.gnu.org/licenses/gpl-2.0.html
 Domain Path: /languages
 Text Domain:ect
*/

/*
  Copyright 2017  Narinder singh (email :narinder99143@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details."
 second testing code ok
 * 
 */

 
if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if (!defined('ECT_VERSION_CURRENT')){
    define('ECT_VERSION_CURRENT', '1.0.7');
}

define('ECT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('ECT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Cool EventsCalendarTemplates main class.
 */

if (!class_exists('EventsCalendarTemplates')) {

    class EventsCalendarTemplates {

    	/**
         * Construct the plugin object
         */
        public function __construct() {
        	
        	/*
        	Check The Event calender is installled or not
        	*/	
        	add_action( 'plugins_loaded', array( $this, 'check_event_calender_installed' ));

        	// Check whether the Titan Framework plugin is activated, and notify if it isn't
			require_once( 'titan-framework/titan-framework-embedder.php' );

			add_action( 'tf_create_options', array( $this, 'ect_Options' ) );

			if(is_admin()){
			add_action( 'admin_enqueue_scripts',array( $this,'ect_remove_wp_colorpicker'),99);
				}
        	/*
        	Enqueued script and styles
        	*/

       		 add_action('wp_enqueue_scripts', array($this, 'ect_styles'));
       		 add_action('after_setup_theme', array($this, 'ect_add_tc_button'));
        	add_shortcode('events-calendar-templates', array( $this,'ect_shortcodes'));
			
			add_action( 'admin_notices',array($this,'ect_admin_messages'));
            add_action( 'wp_ajax_hideRating',array($this,'ect_HideRating' )); 			

			add_action('admin_enqueue_scripts',array( $this,'ect_tc_css'));

			foreach (array('post.php','post-new.php') as $hook) {
    		add_action("admin_head-$hook", array( $this,'ect_cats'));
				}
			}
	
			public function ect_add_tc_button() {
			    global $typenow;
			    // check user permissions
			  if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
			    return;
			    } 
			  
			    // check if WYSIWYG is enabled
			    if ( get_user_option('rich_editing') == 'true') {
			        add_filter("mce_external_plugins",array($this,"ect_add_tinymce_plugin"));
			        add_filter('mce_buttons',array($this,'ect_register_tc_button'));
			    }
			}

			public function ect_remove_wp_colorpicker() {

				$current_screen = get_current_screen();
				if( $current_screen ->id === "tribe_events_page_edit?post_type=tribe_events-events-template-settings" )
					{
				 wp_dequeue_script( 'wp-color-picker-alpha' );
					}
			}

			public function ect_add_tinymce_plugin($plugin_array) 
			{
		    $plugin_array['ect_tc_button'] = plugins_url( '/js/shortcode-generator.js', __FILE__ ); 
		    return $plugin_array;
			}

			public function ect_register_tc_button($buttons) {
			   array_push($buttons, "ect_tc_button");
			   return $buttons;
			}

			public function ect_tc_css() {
			    wp_enqueue_style('sg-btn-css', plugins_url('/css/shortcode-generator.css', __FILE__));
			}

       	   /*
        	Check The Event calender is installled or not. If user has not installed yet then show notice 
        	
        	*/	
        public  function check_event_calender_installed(){

        	if ( ! class_exists( 'Tribe__Events__Main' ) or ! defined( 'Tribe__Events__Main::VERSION' )) {
				
				 add_action( 'admin_notices', array( $this, 'Install_ECT_Notice' ) );
			}
        }

        public function Install_ECT_Notice(){
        	if ( current_user_can( 'activate_plugins' ) ) {
        		$url = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';	

        		$title = __( 'The Events Calendar', 'tribe-events-ical-importer' );

        		echo '<div class="error CTEC_Msz"><p>' . sprintf( __( 'In order to use our plugin, Please first install the latest version of <a href="%s" class="thickbox" title="%s">%s</a> and add an event.', 'ect' ), esc_url( $url ), esc_attr( $title ),esc_attr( $title ) ) . '</p></div>';
        	}
        }
   
    public function ect_shortcodes($atts){
       	if ( !function_exists( 'tribe_get_events' ) ) {
			return;
		}
		global $wp_query, $post;
		global $more;
		$more = false;
		$output = '';$events_html='';
		
			$attribute = shortcode_atts( apply_filters( 'ect_shortcode_atts', array(
			'event_tax' => '',
			'order' => 'ASC',
			'category' => '',
			'start_date'=>'',
			'end_date'=>'',
			'month' => '',
			'limit' =>'',
			'time' =>'past',
			'hide-venue' => '',
			'template'=>'',
			'tags'=>'',
			'icons'=>'',
			'layout'=>'',
			'title'=>'',
			'design'=>'',
			'date_format'=>''
			), $atts ), $atts);

		$tabs_menu_html='';$tabs_cont_html='';	
		$activetb=1;
		//$design=$attribute['design']?$attribute['design'].'-design':'default-design';
		
		$design='default-design';

		$template=isset($attribute['template'])?$attribute['template']:'default';

		wp_enqueue_style('boot-cdn');
	//	wp_enqueue_style('ect-common-styles');
		if(in_array($template,array("timeline","classic-timeline"))){
			wp_enqueue_style('ect-timeline-styles');
		}elseif($template=="classic-list"){
			wp_enqueue_style('ect-classic-list-styles');
		}else{
			wp_enqueue_style('ect-default-styles');
		}	

		if($attribute['category']!="all"){
		if ( $attribute['category'] ) {
			if ( strpos( $attribute['category'], "," ) !== false ) {
				$attribute['category'] = explode( ",", $attribute['category'] );
				$attribute['category'] = array_map( 'trim',$attribute['category'] );
			} else {
				$attribute['category'] = $attribute['category'];
			}
		
		 $attribute['event_tax'] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'name',
					'terms' =>$attribute['category'],
				),
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' =>$attribute['category'],
				));
		 	}
		  }
		 $prev_event_month='';
		 $prev_event_year='';
		 $meta_date_compare = '>=';
		
		  if ($attribute['time']=='past') {
			$meta_date_compare = '<';
			}
		  $meta_date_date = current_time( 'Y-m-d H:i:s' );
		  $attribute['key']='_EventStartDate';
		  $attribute['meta_date'] = array(
			array(
				'key' =>'_EventStartDate',
				'value' => $meta_date_date,
				'compare' => $meta_date_compare,
				'type' => 'DATETIME'
			));
		 
		 	  if (!empty($attribute['start_date'])&& !empty($attribute['end_date'])) {
				    $attribute['meta_date'] = array(
					array(
						'key' =>'_EventStartDate',
						'value' => array($attribute['start_date'],$attribute['end_date']),
						'compare' => 'BETWEEN',
						'type' => 'DATETIME'));
		 	 		}

		 $all_events = tribe_get_events( apply_filters( 'ect_args_filter', array(
			'post_status' => 'publish',
			'hide_upcoming' => true,
			'posts_per_page' => $attribute['limit'],
			'tax_query'=> $attribute['event_tax'],
			'meta_key' => $attribute['key'],
			'orderby' => 'meta_value',
			'order' => $attribute['order'],
			'meta_query' =>$attribute['meta_date'],
		), $attribute, $meta_date_date, $meta_date_compare ) );
			$i=0;
			if ( $all_events ) {
		 		foreach( $all_events as $post ):setup_postdata( $post );
		 		$event_cost='';$event_title='';$event_schedule='';$event_venue='';$event_img='';$event_content='';$events_date_header='';$no_events='';

		 	
		 		$show_headers = apply_filters( 'tribe_events_list_show_date_headers', true );
			
				if ( $show_headers ) {
					$event_year= tribe_get_start_date( $post, false, 'Y' );
					$event_month= tribe_get_start_date( $post, false, 'm' );
					$month_year_format= tribe_get_date_option( 'monthAndYearFormat', 'F Y' );

					if ($prev_event_month != $event_month || ( $prev_event_month == $event_month && $prev_event_year != $event_year ) ) {
						
						$prev_event_month=$event_month;
						$prev_event_year= $event_year;

						$date_header= sprintf( "<span class='tribe-events-list-separator-month'><span>%s</span></span>", tribe_get_start_date( $post, false, $month_year_format ) );
						
					$events_date_header.='<!-- Month / Year Headers -->';
					$events_date_header.=$date_header;	
		 			
		 				}
		 			}
		 		
			$post_parent = '';
				if ( $post->post_parent ) {
					$post_parent = ' data-parent-post-id="' . absint( $post->post_parent ) . '"';
				}
				$event_type = tribe( 'tec.featured_events' )->is_featured( $post->ID ) ? 'ect-featured-event' : 'ect-simple-event';

					// Venue
				$venue_details = tribe_get_venue_details();
				$has_venue_address = (!empty( $venue_details['address'] ) ) ? ' location' : '';

				$venue_details_html='';

		 		// Setup an array of venue details for use later in the template
			if($attribute['hide-venue']!="yes"){
			$venue_details_html.='<div class="ect-list-venue  '.$template.'-venue">';
			
			if (tribe_has_venue()) :
				
				 $venue_details_html.='<span class="ect-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>';
				 $venue_details_html.='<!-- Venue Display Info -->
					<span class="ect-venue-details ect-address">';
					$venue_details_html.=implode(',', $venue_details );
					$venue_details_html.='</span>';
					endif ;
				
				if ( tribe_get_map_link() ) {
				$venue_details_html.='<span class="ect-google">'.tribe_get_map_link_html().'</span>';
				}
			
			if ( tribe_get_cost() ) : 
			$venue_details_html.='<div class="ect-rate-area"><span class="ect-rate-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
				<span class="ect-rate">'.tribe_get_cost( null, true ).'</span></div>';
			endif;
			$venue_details_html.='</div>';
			}	

			$ev_time=$this->ect_tribe_event_time(false);

			if($attribute['date_format']=="DM"){
				$event_schedule='<div class="ect-date-area '.$template.'-schedule">
			<span class="ev-day">'.tribe_get_start_date( null, false, 'd' ).'</span>
			<span class="ev-mo">'.tribe_get_start_date( null, false, 'F' ).'</span></div>';
		
			}elseif($attribute['date_format']=="MD"){
			
			$event_schedule='<div class="ect-date-area '.$template.'-schedule">
			<span class="ev-day">'.tribe_get_start_date( null, false, 'M' ).'</span>
			<span class="ev-mo">'.tribe_get_start_date( null, false, 'd' ).'</span></div>';

			}elseif($attribute['date_format']=="full"){
			$e_s_y_html='';$e_s_t_h='';
			$e_s_html='<div class="ect-date-area '.$template.'-schedule">
			<span class="ev-day">'.tribe_get_start_date( null, false, 'd' ).'</span>
			<span class="ev-mo">'.tribe_get_start_date( null, false, 'F' ).'</span>';
			
			if(!in_array($template,array("timeline","classic-timeline"))){
					$e_s_y_html='<span class="ev-yr">'.tribe_get_start_date( null, false, 'Y' ).'</span>';
				}
					if($template!="classic-list"){
			$e_s_t_h='<span class="ev-time"><small>'.$ev_time.'</small></span>';
				}
				$event_schedule=$e_s_html.$e_s_y_html.$e_s_t_h.'</div>';
			
			}else{

				$event_schedule='<div class="ect-date-area '.$template.'-schedule">
			<span class="ev-day">'.tribe_get_start_date( null, false, 'd' ).'</span>
			<span class="ev-mo">'.tribe_get_start_date( null, false, 'F' ).'</span> <span class="ev-yr">'.tribe_get_start_date( null, false, 'Y' ).'</span></div>';
			}
			
			
			

				// Organizer
				$organizer = tribe_get_organizer();

			if ( tribe_get_cost() ) : 
				$event_cost='<!-- Event Cost -->
				<div class="ect-event-cost">
					<span>'.tribe_get_cost( null, true ).'</span>
				</div>';
				endif;

				$event_title='<a class="ect-event-url" href="'.esc_url( tribe_get_event_link()).'" rel="bookmark">'. get_the_title().'</a>';
		
				
				$event_content='<!-- Event Content --><div class="ect-event-content">';
				  $event_content.=tribe_events_get_the_excerpt( null, wp_kses_allowed_html( 'post' ) );

 				$event_content.='<a href="'.esc_url( tribe_get_event_link() ).'" class="ect-events-read-more" rel="bookmark">'.esc_html__( 'Find out more', 'the-events-calendar' ).' &raquo;</a></div>';
		/*
		Timeline events content
		*/
 		
 		if(in_array($template,array("timeline","classic-timeline")))
 		{
			include(ECT_PLUGIN_DIR.'/templates/timeline-template.php');	
		}elseif($template=="classic-list"){
 			include(ECT_PLUGIN_DIR.'/templates/classic-list-template.php');	
		//default list view content	        	
		}else{
			include(ECT_PLUGIN_DIR.'/templates/list-template.php');

 			}
 
 				endforeach;
 			   wp_reset_postdata();
		 		
}else { 
	$no_events=__('There are no upcoming events at this time.','ect');
	} 

	if($attribute['title']!==''){
			  $main_title='<h2 class="ect-events-page-title">'.$attribute['title'].'</h2>';
			}else{
			  $main_title='<h2 class="ect-events-page-title">'.tribe_get_events_title() .'</h2>';
				}

	if(in_array($template,array("timeline","classic-timeline"))){
			 /*
             * Gerneral options
             */
             $wrp_cls='';
	
		    $layout_cls = '';
		    $layout_wrp = 'both-sided-wrapper';
		     $wrp_cls='default-layout';
		$wrapper_cls = 'white-timeline-wrapper';

		$output .='<!=========Timeline Template=========>';
		$output .= '<div class="cool_timeline cool-timeline-wrapper  ' . $layout_wrp . ' ' . $wrapper_cls .'">';
		$output .= '<div class="cool-timeline white-timeline ultimate-style ' . $layout_wrp . ' ' . $wrp_cls.
			' ' .$layout_cls.'">';
			$output .= '<div data-animations="" class="cooltimeline_cont  clearfix '.$template.'-lyot">';
			$output.=$main_title;
			$output .=$events_html;
			$output .= '</div></div></div>  <!-- end
 			================================================== -->';
	
		}elseif($template=="classic-list"){
		$output .='<!=========classic-list Template=========>';
		$output.='<div id="ect-classic-list-content" class="ect-classic-list">';
		$output.=$main_title;
		$output.='<div id="classic-list-wrp" class="ect-classic-list-wrapper  '.$design.'">';

			$output.=$events_html;
			$output.='</div></div>';	
	}else{
 			$output .='<!=========list Template=========>';
			$output.='<div id="ect-events-list-content" class="">';
			$output.=$main_title;
		$output.='<div id="list-wrp" class="ect-list-wrapper">';
			$output.=$events_html;
			$output.='</div></div>';	
		}
				return $output.$no_events;
		 	}//shortcode handler end

		 
  			public function ect_styles(){

  			wp_enqueue_style('font-awesome-icons',ECT_PLUGIN_URL . 'css/font-awesome-4.7.0/css/font-awesome.min.css',null, null,'all' );	
			wp_register_style('ect-common-styles', ECT_PLUGIN_URL . 'css/ect-common-styles.css',null, null,'all' );	
  			wp_register_style('ect-timeline-styles', ECT_PLUGIN_URL . 'css/ect-timeline.css',null, null,'all' );	

  			wp_register_style('ect-default-styles', ECT_PLUGIN_URL . 'css/ect-default.css',null, null,'all' );	

  			wp_register_style('ect-classic-list-styles', ECT_PLUGIN_URL . 'css/ect-classic-list.css',null, null,'all' );

  		//	wp_enqueue_script('ect-js',ECT_PLUGIN_URL.'js/ect.js',array('jquery'));

  			
  			}


  		/*
  		 ect Option panel 
  		*/
	  function ect_Options() {
			// Initialize Titan & options here
			$titan = TitanFramework::getInstance('ect' );		
			$panel = $titan->createAdminPanel( array(
		'name' => 'Events Template Settings',
		'title'=>'',
		'desc'=>'<img style="float: left;margin-right: 5px;" src="'.ECT_PLUGIN_URL.'/css/ect-icon.png">Events Calendar Templates plugin provides events templates design and shortcode builder facility for The Events Calendar (by Modern Tribe).',
		'parent' => 'edit.php?post_type=tribe_events',
		'position'=>'200',
		) );

			$stylingTab= $panel->createTab( array(
		'name' => 'Style Settings',

		) );
$extraTab= $panel->createTab( array(
'name' => 'Extra Settings',
) );

	 $stylingTab->createOption( array(
		'type' => 'save',
		) );
	 $extraTab->createOption( array(
		'type' => 'save',
		) );
		$stylingTab->createOption( array(
		'name' => 'Style Settings',
		'type' => 'heading',
		) );

		$stylingTab->createOption( array(
		'name' => 'Main Skin Color',
		'id' => 'main_skin_color',
		'type' => 'color',
		'desc' => 'It is a main color scheme for all designs',
		'default' => '#ff6a5c',
		'css'=>'.ect-list-post .ect-list-post-right .ect-list-venue, .cool-timeline.white-timeline .timeline-post.timeline-evt .timeline-meta {
			background: value;
		} 
		.ect-list-post .ect-list-post-right .ect-list-description .ect-event-content a { color: value; }
		.ect-list-post .ect-list-post-left .ect-list-date{
			background: rgba( $main_skin_color, .85 );
			}
		.cool-timeline.white-timeline .timeline-year { background: darken( $main_skin_color, 25% ); }
		.cool-timeline.white-timeline .timeline-post.even .timeline-meta:before { border-left-color: value; }
		.cool-timeline.white-timeline .timeline-post.odd .timeline-meta:before { border-right-color: value; }
		.ect-list-post .ect-list-img {background-color: lighten( $main_skin_color, 10% );}
		.cool-timeline.white-timeline .timeline-post.classic-timeline-evt .ect-date-area, .cool-timeline.white-timeline .timeline-post.classic-timeline-evt .ect-venue-details {color: value;}
		.cool-timeline .timeline-post.classic-timeline-evt .ect-rate-area, .cool-timeline .timeline-post.classic-timeline-evt .ect-google a, .cool-timeline .timeline-post.classic-timeline-evt span.ect-icon {color: darken( $main_skin_color, 10% ); }
		.ect-list-post .ect-list-post-right .ect-list-venue .ect-rate-area{ background: darken( $main_skin_color, 8% ); }
		'
		) );

	$stylingTab->createOption( array(
  'name' => 'Featured Event Skin Color',
  'id' => 'featured_event_skin_color',
  'type' => 'color',
  'desc' => 'This skin color applies on featured events',
  'default' => '#056571',
  'css'=>'.ect-list-post.ect-featured-event .ect-list-post-right .ect-list-venue, .cool-timeline.white-timeline .timeline-post.ect-featured-event.timeline-evt .timeline-meta{
   background: value;
  } 
  .ect-list-post.ect-featured-event .ect-list-post-right .ect-list-description .ect-event-content a { color: value; }
  #ect-events-list-content .ect-list-post.ect-featured-event .ect-list-post-right h2.ect-list-title, #ect-events-list-content .ect-list-post.ect-featured-event .ect-list-post-right h2.ect-list-title a.ect-event-url, .cool-timeline.white-timeline .timeline-post.ect-featured-event .timeline-content h2.content-title, .cool-timeline.white-timeline .timeline-post.ect-featured-event .timeline-content h2.content-title a.ect-event-url { color: value; }
  .ect-list-post.ect-featured-event .ect-list-post-left .ect-list-date{
   background: rgba( $featured_event_skin_color, .85 );
   }
  .cool-timeline.white-timeline .timeline-post.ect-featured-event.even .timeline-meta:before {
   border-left-color: value;
  }
  .cool-timeline.white-timeline .timeline-post.ect-featured-event.odd .timeline-meta:before {
   border-right-color: value;
  }
  .cool-timeline .timeline-post.classic-timeline-evt.ect-featured-event .ect-rate-area, .cool-timeline .timeline-post.classic-timeline-evt.ect-featured-event .ect-google a, .cool-timeline .timeline-post.classic-timeline-evt.ect-featured-event span.ect-icon {color: darken( $featured_event_skin_color, 10% );}
  .cool-timeline.white-timeline .timeline-post.ect-featured-event .timeline-content .content-details a {color: value;}
  .cool-timeline.white-timeline .timeline-post.ect-featured-event .timeline-content h2.content-title a:hover, .ect-list-post.ect-featured-event .ect-list-post-right h2.ect-list-title a:hover {
   color: darken( $featured_event_skin_color, 10% ); 
  }
  .ect-list-post.ect-featured-event .ect-list-post-right .ect-list-venue .ect-rate-area{ background: darken( $featured_event_skin_color, 8% ); }
  .cool-timeline.white-timeline .timeline-post.ect-featured-event.classic-timeline-evt .ect-date-area, .cool-timeline.white-timeline .timeline-post.ect-featured-event.classic-timeline-evt .ect-venue-details {color: value;}
  '
  ) );
		
		$stylingTab->createOption( array(
		'name' => 'Event Background Color',
		'id' => 'event_desc_bg_color',
		'type' => 'color',
		'desc' => 'This skin color applies on background of event description area.',
		'default' => '#f3fbf1',
		'css'=>'.ect-list-post .ect-list-post-right, .cool-timeline.white-timeline .timeline-post .timeline-content {
			background: value;
		}
		.cool-timeline.white-timeline .timeline-post .timeline-content, .cool-timeline .tribe-events-event-image img {
			border: 1px solid darken($event_desc_bg_color, 5%);
		}
		.cool-timeline.white-timeline .timeline-post.even .timeline-content .content-title:before { border-right-color: darken($event_desc_bg_color, 5%);}
		.cool-timeline.white-timeline .timeline-post.odd .timeline-content .content-title:before { border-left-color: darken($event_desc_bg_color, 5%);}
		.cool-timeline.white-timeline:before, .cool-timeline.white-timeline .timeline-post .icon-dot-full { background-color: darken($event_desc_bg_color, 10%); }
		.cool-timeline.white-timeline .timeline-year { 
		-webkit-box-shadow: 0 0 0 4px white, 0 0 0 8px darken($event_desc_bg_color, 10%);
		box-shadow: 0 0 0 4px white, 0 0 0 8px darken($event_desc_bg_color, 10%);
		}
		.ect-list-post .ect-list-post-right .ect-list-description { border:1px solid darken($event_desc_bg_color, 4%); }
		@media (max-width: 860px) {
			.cool-timeline.white-timeline .timeline-post.odd .timeline-content .content-title:before { border-right-color: darken($event_desc_bg_color, 5%);border-left-color:transparent;}
		}
			
		'
		) );
		

		$stylingTab->createOption( array(
	  'name' => 'Event Title Styles',
	  'id' => 'ect_title_styles',
	  'type' => 'font',
	  'desc' => 'Select a style',
	  'show_letter_spacing' => false,
	  'show_text_transform' => false,
	  'show_font_variant' => false,
	  'show_text_shadow' => false,
	  'default' => array(
	  'color' => '#ff6a5c',
	  'font-family' => 'Martel Sans',
	  'font-size' => '18px',
	  'line-height' => '1.4em',
	  'font-weight' => 'bold',
	  ),
	  'css'=>'#ect-events-list-content .ect-list-post .ect-list-post-right h2.ect-list-title, #ect-events-list-content .ect-list-post .ect-list-post-right h2.ect-list-title a.ect-event-url, .cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title, .cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title a.ect-event-url {
	   value
	  }
	  .ect-list-post .ect-list-post-right h2.ect-list-title a:hover, .cool-timeline.white-timeline .timeline-post .timeline-content h2.content-title a:hover {
	   color: darken(value-color, 10%); 
	  }
	  .ect-list-venue .ect-rate-area .ect-rate { font-family: value-font-family; }
	  .cool-timeline.white-timeline .timeline-post .timeline-content .content-details a {color: value-color;}
	  '
	  ) );
	/*	$panel->createOption( array(
		'name' => 'Event Title Color',
		'id' => 'ect_title_color',
		'type' => 'color',
		'desc' => 'Pick a color',
		'default' => '#555555',
		) );
		*/
		
		$stylingTab->createOption( array(
		'name' => 'Events Description Styles',
		'id' => 'ect_desc_styles',
		'type' => 'font',
		'desc' => 'Select Styles',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		//'show_color'=>false,
		'show_font_style'=>false,
		'show_line_height'=>false,
		'default' => array(
		'color' => '#414141',
		'font-family' => 'Open Sans',
		'font-size' => '14px',
		'line-height' => '1.4em',
		),
		'css'=>'.ect-list-post .ect-list-post-right .ect-list-description .ect-event-content p, .cool-timeline.white-timeline .timeline-post .timeline-content .content-details{
			value
		}'
		) );
		$stylingTab->createOption( array(
		'name' => 'Event Venue Styles',
		'id' => 'ect_desc_venue',
		'type' => 'font',
		'desc' => 'Select a style',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		'default' => array(
		'color' => '#ffffff',
		'font-family' => 'Open Sans',
		'font-size' => '14px',
		'line-height' => '1.3em',
		),
		'css'=>'.ect-list-post .ect-list-post-right .ect-list-venue .ect-venue-details, .ect-list-post .ect-list-post-right .ect-list-venue .ect-google a,  .ect-list-post .ect-list-post-right .ect-list-venue .ect-icon, .ect-list-post .ect-list-post-right .ect-list-venue .ect-rate-area, .cool-timeline.white-timeline .ect-venue-details {
			value
		}
		.ect-list-post .ect-list-post-right .ect-list-venue .ect-google a {color: darken(value-color, 3%);}
		.cool-timeline span.ect-icon { color: value-color; }
		.cool-timeline .ect-rate-area, .ect-list-post .ect-list-post-right .ect-list-venue .ect-rate-area { font-size: value-font-size + value-font-size/2 ;}
		.cool-timeline .ect-google a { font-size: value-font-size;}
		'
		) );

		
		
		/*$panel->createOption( array(
		'name' => 'Month/Year Circle Color',
		'id' => 'ect_my_circle_color',
		'type' => 'color',
		'desc' => 'This setting only for Timeline template.',
		'default' => '#555555',
		) );	
		$panel->createOption( array(
		'name' => 'Line Color',
		'id' => 'ect_timeline_line_color',
		'type' => 'color',
		'desc' => 'This setting only for Timeline template.',
		'default' => '#555555',
		) ); */	
		$stylingTab->createOption( array(
		'name' => 'Event Dates Styles',
		'id' => 'ect_dates_styles',
		'type' => 'font',
		'desc' => 'Select a style',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		'default' => array(
		'color' => '#ffffff',
		'font-family' => 'Martel Sans',
		'font-size' => '42px',
		'line-height' => '0.9em',
		),
			'css'=>'.ect-list-post .ect-list-post-left .ect-list-date .ect-date-area, .cool-timeline.white-timeline .timeline-post .ect-date-area {
				value
			}
			.cool-timeline.white-timeline .timeline-year .icon-placeholder span { font-family: value-font-family; }
			.cool-timeline .ect-rate-area, .cool-timeline .ect-google a {color: darken( value-color, 5% );}
			'
		) );
		
	$stylingTab->createOption( array(
  'name' => 'Main title styles',
  'id' => 'ect_mt_styles',
  'type' => 'font',
  'desc' => 'Select a style for top title - like - Upcoming Events',
  'show_letter_spacing' => false,
  'show_text_transform' => false,
  'show_font_variant' => false,
  'show_text_shadow' => false,
  'default' => array(
  'color' => '#414141',
  'font-family' => 'Martel Sans',
  'font-size' => '24px',
  'line-height' => '0.9em',
  ),
  'css'=>'#ect-events-list-content h2.ect-events-page-title , .cool_timeline h2.ect-events-page-title{
    value
   }'
  ) );
		
		$extraTab->createOption( array(
		'name' => 'Extra Settings',
		'type' => 'heading',
		) );
		$extraTab->createOption( array(
		'name' => 'Custom CSS',
		'id' => 'custom_css',
		'type' => 'code',
		'desc' => 'Put your custom CSS rules here',
		'lang' => 'css',
		) );
		$extraTab->createOption( array(
		'name' => 'Shortcodes',
		'type' => 'heading',
		) );
	$extraTab->createOption( array(
		'name'=>'Default List Template Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates template="default" category="all" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future" title=""]</code>'
		) );
	$extraTab->createOption( array(
		'name'=>'Timeline Template Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates template="timeline" category="all" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future" title=""]</code>'
		) );
	$extraTab->createOption( array(
		'name'=>'Classic Timeline Template Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates template="classic-timeline" category="all" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future" title=""]</code>'
		) );
	$extraTab->createOption( array(
		'name'=>'Custom Date formats Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates template="default" category="all" date_format="MD" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future" title=""]</code>'
		) );
	$extraTab->createOption( array(
		'name'=>'Past Events Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates template="default" category="all" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="past" title=""]</code>'
		) );
	$extraTab->createOption( array(
		'name'=>'Category based Events Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates template="default"  category="{{Category-slug}}" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future" title=""]</code>'
		) );
		

		$stylingTab->createOption( array(
		'type' => 'save'
		) );			
		$extraTab->createOption( array(
		'type' => 'save'
		) );	

		}

public function ect_tribe_event_time( $display = true ) {
	global $post;
	$event = $post;
	if ( tribe_event_is_all_day( $event ) ) { // all day event
		if ( $display ) {
			_e( 'All day', 'ect' );
		}
		else {
			return __( 'All day', 'ect' );
		}
	}
	elseif ( tribe_event_is_multiday( $event ) ) { // multi-date event
		$start_date = tribe_get_start_date( null, false );
		$end_date = tribe_get_end_date( null, false );
		if ( $display ) {
			printf( __( '%s - %s', 'ect' ), $start_date, $end_date );
		}
		else {
			return sprintf( __( '%s - %s', 'ect' ), $start_date, $end_date );
		}
	}
	else {
		$time_format = get_option( 'time_format' );
		$start_date = tribe_get_start_date( $event, false, $time_format );
		$end_date = tribe_get_end_date( $event, false, $time_format );
		if ( $start_date !== $end_date ) {
			if ( $display ) {
				printf( __( '%s - %s', 'ect' ), $start_date, $end_date );
			}
			else {
				return sprintf( __( '%s - %s', 'ect' ), $start_date, $end_date );
			}
		}
		else {
			if ( $display ) {
				printf( '%s', $start_date );
			}
			else {
				return sprintf( '%s', $start_date );
			}
		}
	}
}

public function ect_tribe_event_recurringinfo( $before = '', $after = '', $link_all = true ) {
	if ( !function_exists('tribe_is_recurring_event') ) {
		return false;
	}
	global $post;
	$info = '';
	if ( tribe_is_recurring_event( $post->ID ) ) {
		if ( function_exists( 'tribe_get_recurrence_text' ) ) {
			$info .= tribe_get_recurrence_text( $post->ID );
		}
		if ( $link_all && function_exists( 'tribe_all_occurences_link' ) ) {
			$info .= sprintf( ' <a href="%s">%s</a>', esc_url( tribe_all_occurences_link( $post->ID, false ) ), __( '(See All)', 'ect' ) );
		}
	}
	if ( $info ) {
		$info = $before.$info.$after;
	}
	return $info;
}	
	
	
   public function ect_admin_messages() {
  
         if( !current_user_can( 'update_plugins' ) ){
            return;
         }

     $install_date = get_option( 'ect-installDate' );
     $ratingDiv =get_option( 'ect-ratingDiv' )!=false?get_option( 'ect-ratingDiv'):"no";
	 $dynamic_msz='';
	 $dynamic_msz .= '<div class="cool_fivestar update-nag" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">
      <p>Awesome, you\'ve been using <strong>Events Calendar Templates Plugin</strong> '.$dynamic_msz .' Hopefully you\'re happy with it. <br> May I ask you to give it a <strong>5-star rating</strong> on Wordpress? 
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated.Thank you very much!
        <ul>
            <li class="float:left"><a href="https://wordpress.org/support/plugin/template-events-calendar/reviews/#new-post" class="thankyou button button-primary" target="_new" title="I Like Events Calendar Templates" style="color: #ffffff;-webkit-box-shadow: 0 1px 0 #256e34;box-shadow: 0 1px 0 #256e34;font-weight: normal;float:left;margin-right:10px;">I Like Events Calendar Templates</a></li>
            <li><a href="javascript:void(0);" class="coolHideRating button" title="I already did" style="">I already rated it</a></li>
            <li><a href="javascript:void(0);" class="coolHideRating" title="No, not good enough" style="">No, not good enough, i do not like to rate it!</a></li>
        </ul>
    </div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.coolHideRating\').click(function(){
        var data={\'action\':\'hideRating\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(\'.cool_fivestar\').slideUp(\'fast\');
         
            }
        }
         });
        })
    
    });
    </script>';
 	
    	if(get_option( 'ect-installDate' )==false && $ratingDiv== "no" )
       {
       echo $dynamic_msz;
       }else{
            $display_date = date( 'Y-m-d h:i:s' );
            $install_date= new DateTime( $install_date );
            $current_date = new DateTime( $display_date );
            $difference = $install_date->diff($current_date);
          $diff_days= $difference->days;
        if (isset($diff_days) && $diff_days>=15 && $ratingDiv == "no" ) {
            echo $dynamic_msz;
          }
      }


 }
 	   
  

	  public function ect_HideRating() {
	    update_option( 'ect-ratingDiv','yes' );
	    echo json_encode( array("success") );
	    exit;
	    }
  	 public static function activate() {
              update_option("ect-v",ECT_VERSION_CURRENT);
              update_option("ect-type","FREE");
              update_option("ect-installDate",date('Y-m-d h:i:s') );
              update_option("ect-ratingDiv","no");
        }

public function ect_cats() {

  if(version_compare(get_bloginfo('version'),'4.5.0', '>=') ){
	    $terms = get_terms(array(
	     'taxonomy' => 'tribe_events_cat',
	    'hide_empty' => false,
	     ));
    }else{
            $terms = get_terms('tribe_events_cat', array('hide_empty' => false,
        ) );
      }


    if (!empty($terms) || !is_wp_error($terms)) {
    	$ctl_terms_l['all']='All Cateogires';
        foreach ($terms as $term) {
            $ctl_terms_l[$term->slug] =$term->slug;
        }
    }
 if (isset($ctl_terms_l) && array_filter($ctl_terms_l) != null) {
		 $category =json_encode($ctl_terms_l);
    } else {
        $category = json_encode(array('0' => 'No category'));
    }
    ?>
    <!-- TinyMCE Shortcode Plugin -->
    <script type='text/javascript'>
        var ect_cat_obj = {
            'category':'<?php echo $category; ?>'
        };
    </script>
    <!-- TinyMCE Shortcode Plugin -->
    <?php
}

    } //class end here
 }   

	// Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('EventsCalendarTemplates', 'activate'));
  

 $ect=new EventsCalendarTemplates;
