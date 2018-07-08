=== The Events Calendar Shortcode ===
Contributors: brianhogg
Tags: event, events, calendar, shortcode, modern tribe
Requires at least: 4.1
Tested up to: 4.9
Stable tag: 1.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds shortcode functionality to The Events Calendar Plugin (Free Version) by Modern Tribe, so you can list your events anywhere.

== Description ==

This plugin adds a shortcode for use with The Events Calendar Plugin (by Modern Tribe).

With this plugin, just add the shortcode on a page to display a list of your events. For example to show next 8 events in the category festival:

`[ecs-list-events cat="festival" limit="8"]`

= Shortcode Options: =
* Basic shortcode: `[ecs-list-events]`
* cat - Represents single event category. `[ecs-list-events cat='festival']`  Use commas when you want multiple categories `[ecs-list-events cat='festival, workshops']`
* limit - Total number of events to show. Default is 5. `[ecs-list-events limit='3']`
* order - Order of the events to be shown. Value can be 'ASC' or 'DESC'. Default is 'ASC'. Order is based on event date. `[ecs-list-events order='DESC']`
* date - To show or hide date. Value can be 'true' or 'false'. Default is true. `[ecs-list-events eventdetails='false']`
* venue - To show or hide the venue. Value can be 'true' or 'false'. Default is false. `[ecs-list-events venue='true']`
* excerpt - To show or hide the excerpt and set excerpt length. Default is false. 
  * `[ecs-list-events excerpt='true']` //displays excerpt with length 100
  * `[ecs-list-events excerpt='300']` //displays excerpt with length 300
* thumb - To show or hide thumbnail image. Default is false. `[ecs-list-events thumb='true']` //displays post thumbnail in default thumbnail dimension from media settings.
* You can use thumbwidth and thumbheight to customize the thumbnail size `[ecs-list-events thumb='true' thumbwidth='150' thumbheight='150']` or thumbsize for a registered WordPress size `[ecs-list-events thumb='true' thumbsize='large']`
* message - Message to show when there are no events. Defaults to 'There are no upcoming events at this time.'
* viewall - Determines whether to show 'View all events' or not. Values can be 'true' or 'false'. Default to 'true' `[ecs-list-events cat='festival' limit='3' order='DESC' viewall='false']`
* contentorder - Manage the order of content with commas. Default to `title, thumbnail, excerpt, date, venue`. `[ecs-list-events cat='festival' limit='3' order='DESC' viewall='false' contentorder='title, thumbnail, excerpt, date, venue']`
* month - Show only specific Month. Type `'current'` for displaying current month only or `'next'` for next month `[ecs-list-events cat='festival' month='2015-06']`
* past - Show Outdated Events. `[ecs-list-events cat='festival' past='yes']`
* key - Hide the event when the start date/time has passed `[ecs-list-events cat='festival' key='start date']`
* orderby - Order by end date `[ecs-list-events orderby='enddate']`

<blockquote>
<h4>Additional options and benefits in the pro version</h4>
<ul>
<li>design - Shows <a href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode/?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme-design&utm_content=description#designs" target="_blank">improved design by default</a>, 'compact' for a more compact listing, 'calendar' for a monthly calendar view, 'columns' to show events horizontally in columns, or 'grouped' to group events by day</li>
<li>days - Specify how many days in the future, for example [ecs-list-events days="1"] for one day or [ecs-list-events days="7"] for one week</li>
<li>date - Show only events for a specific day [ecs-list-events date='2017-04-16']</li>
<li>tag - Filter by one or more tags.  Use commas when you want to filter by multiple tags.</li>
<li>city, state, country - Display events by location.</li>
<li>featured only - Show only events marked as "featured"</li>
<li>id - Show a single event, useful for displaying details of the event on a blog post or page</li>
<li>description - Use the full description instead of the excerpt of an event in the listing</li>
<li>raw_description - Avoid filtering any HTML (spacing, links, bullet points, etc) in the description</li>
<li>raw_excerpt - Avoid filtering any HTML (spacing, links, etc) in the excerpt</li>
<li>year - Show only events for a specific year</li>
<li>date range - Show only events between certain days</li>
<li>timeonly - To show just the start time of the event. [ecs-list-events timeonly='true']</li>
<li>offset - Skip a certain number of events from the beginning, useful for using multiple shortcodes on the same page (with ads in between) or splitting into columns</li>
<li>custom design - Create one or more of your own templates for use with the shortcode</li>
<li>hiderecurring - To only show the first instance of a recurring event, set to 'true'</li>
</ul>
<p><a href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme&utm_content=description">View more Pro features</a></p>
</blockquote>

This plugin is not developed by or affiliated with The Events Calendar or Modern Tribe in any way.

== Installation ==

1. Install The Events Calendar Shortcode Plugin from the WordPress.org repository or by uploading the-events-calendar-shortcode folder to the /wp-content/plugins directory. You must also install The Event Calendar Plugin by Modern Tribe and add your events to the calendar.

2. Activate the plugin through the Plugins menu in WordPress

3. If you don't already have The Events Calendar (the calendar you add your events to) you will be prompted to install it

You can then add the `[ecs-list-events]` shortcode to the page or post you want to list events on.  [Full list of options available in the documentation](https://eventcalendarnewsletter.com/events-calendar-shortcode-pro-options/?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme-install-docs&utm_content=description).


== Frequently Asked Questions ==

= What are the shortcode options? =

* Basic shortcode: `[ecs-list-events]`
* cat - Show events from an event category `[ecs-list-events cat='festival']` or specify multiple categories `[ecs-list-events cat='festival, workshops']`
* limit - Total number of events to show. Default is 5. `[ecs-list-events limit='3']`
* order - Order of the events to be shown. Value can be 'ASC' or 'DESC'. Default is 'ASC'. Order is based on event date. `[ecs-list-events order='DESC']`
* date - To show or hide date. Value can be 'true' or 'false'. Default is true. `[ecs-list-events eventdetails='false']`
* venue - To show or hide the venue. Value can be 'true' or 'false'. Default is false. `[ecs-list-events venue='true']`
* excerpt - To show or hide the excerpt and set excerpt length. Default is false. `[ecs-list-events excerpt='true']` //displays excerpt with length 100
 excerpt='300' //displays excerpt with length 300
* thumb - To show or hide thumbnail image. Default is false. `[ecs-list-events thumb='true']` //displays post thumbnail in default thumbnail dimension from media settings.
* thumbsize - Specify the size of the thumbnail. `[ecs-list-events thumb='true' thumbsize='large']`
* thumbwidth / thumbheight - Customize the thumbnail size in pixels `[ecs-list-events thumb='true' thumbwidth='150' thumbheight='150']`
* message - Message to show when there are no events. Defaults to 'There are no upcoming events at this time.'
* viewall - Determines whether to show 'View all events' or not. Values can be 'true' or 'false'. Default to 'true' `[ecs-list-events cat='festival' limit='3' order='DESC' viewall='false']`
* contentorder - Manage the order of content with commas. Default to `title, thumbnail, excerpt, date, venue`. `[ecs-list-events cat='festival' limit='3' order='DESC' viewall='false' contentorder='title, thumbnail, excerpt, date, venue']`
* month - Show only specific month (in YYYY-MM format). Type `'current'` for displaying current month only or `'next'` for next month. `[ecs-list-events cat='festival' month='2015-06']`
* past - Show Outdated Events. `[ecs-list-events cat='festival' past='yes']`
* key - Hide events when the start date has passed `[ecs-list-events cat='festival' key='start date']`
* orderby - Change the ordering to the end date `[ecs-list-events orderby="enddate"]`

With [The Events Calendar Shortcode PRO](https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme-faq-options&utm_content=description) you also get the following options:

* design - Shows improved design by default. Set to 'standard' for the regular one, 'compact' for a more compact listing, 'calendar' for a monthly calendar view, 'columns' to show a horizontal/columns/photo view, or 'grouped' to group events by day
* days - Specify how many days in the future, for example `[ecs-list-events days="1"]` for one day or `[ecs-list-events days="7"]` for one week
* tag - Filter by one or more tags.  Use commas when you want to filter by multiple tags.
* id - Show a single event, useful for displaying details of the event on a blog post or page
* location (city, state/province, country) - Display events by location.  Use commas when you want to include events from multiple (ie. country='United States, Canada')
* description - Use the full description instead of the excerpt of an event in the listing
* raw_description - Avoid filtering any HTML (spacing, links, bullet points, etc) in the description
* raw_excerpt - Avoid filtering any HTML (spacing, links, etc) in the excerpt
* featured only - Show only events marked as "featured"
* date - Show only events for a specific day `[ecs-list-events date='2017-04-16']`
* year - Show only events for a specific year `[ecs-list-events year='2017']`
* date range - Show only events between certain days `[ecs-list-events fromdate='2017-05-31' todate='2017-06-15']`
* timeonly - To show just the start time of the event. `[ecs-list-events timeonly='true']`
* offset - Skip a certain number of events from the beginning, useful for using multiple shortcodes on the same page (with ads in between) or splitting into columns
* custom design - Create one or more of your own templates for use with the shortcode
* hiderecurring - To only show the first instance of a recurring event, set to 'true'

[Get The Events Calendar Shortcode PRO](https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme-faq-options-bottom&utm_content=description)

= How do I use this shortcode in a widget? =

You can put the shortcode in a text widget, though not all themes support use of a shortcode in a widget.

If a regular text widget doesn't work, put the shortcode in a <a href="https://wordpress.org/plugins/black-studio-tinymce-widget/">Visual Editor Widget</a>.

= What are the classes for styling the list of events? =

By default the plugin does not include styling. Events are listed in ul li tags with appropriate classes for styling with a bit of CSS.

* ul class="ecs-event-list"
* li class="ecs-event" and "ecs-featured-event" (if featured)
* event title link is H4 class="entry-title summary"
* date class is time
* venue class is venue
* span .ecs-all-events
* p .ecs-excerpt

Want a better looking design without knowing any CSS?  Check out [The Events Calendar Shortcode PRO](https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme-faq-design&utm_content=description)

= How do I include a list of events in a page template? =

`include echo do_shortcode("[ecs-list-events]");`

Put this in the template where you want the events list to display.

= How do I include a monthly calendar view instead of a list? =

The [pro version of the plugin](https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=wordpress.org&utm_medium=link&utm_campaign=tecs-readme-faq-calendar&utm_content=description) has the option to put `design="calendar"` in the shortcode to show a calendar view of the events you want.

== Screenshots ==

1. After adding the plugin, add the shortcode where you want the list of events to appear in the page
2. Events will appear in a list
3. Many settings you can use in the shortcode to change what details appear in the events listing

== Upgrade Notice ==

= 1.9 =
* Adds check for minimum WordPress and PHP version
* Adds a link to a short tutorial video
* Changes first example shortcode so it's easier to copy/paste

= 1.8 =
* Adds new orderby='title' option
* Fixes resetting the WordPress global query instead of just the post data

= 1.7.3 =
* Hide the "at" when using venue='true' and an event has no venue
* Adds additional WordPress filters to hide certain events

= 1.7.2 =
* Adds the ability to use schema='false' in the shortcode to hide the schema output

= 1.7.1 =
* Fix for month option where there's an all-day event the first day of the next month
* Fix for "There are no events" string not being translated automatically into other languages

= 1.7 =
* Adds structured data to the shortcode output (great for SEO and people finding your events)

= 1.6.1 =
* Added ecs-featured-event class if event is featured
* Internal changes to filtering by one or more categories

= 1.6 =
* Changes default ordering by the start date, use orderby="enddate" for previous default ordering

= 1.5.3 =
* Fixes translation of the "View all events" link into other languages
* Adds orderby parameter to order by start date, but still show events until the end date has passed

= 1.5.2 =
* Adds 'next' option for showing the next month of events

= 1.5.1 =
* Adds thumbsize option (ie. medium, large, thumbnail, full)

= 1.5 =
* Adds ability to translate the plugin into local languages
* Additional description of options

= 1.4.2 =
* Additional filter for changing the link for an event
* Adds category CSS classes for each event, so you can format each category differently

= 1.4.1 =
* Additional filters for formatting a single event

= 1.4 =
* Checks for whether The Events Calendar is installed
* Additional filters
* Improved design of shortcode help page

= 1.3 =
* Fixes issue with "viewall" showing the events twice
* Fixes time zone issue by using current_time() instead of date()
* Hides events that are marked 'hide from listing'
* Switches to tribe_get_events() to get the events
* Removes the ... from the end of the excerpt if less than the excerpt length
* Adds date_thumb option
* Adds additional filters

= 1.2 =
* Updates author/description (Event Calendar Newsletter / Brian Hogg Consulting)

= 1.0.11 =
Add Link to Thumbnail
merge pull request from d4mation -Replaced extracted variables with $atts as using extract was deprecated
=1.0.10 =
Minor Error Change - fix  name and slug 
= 1.0.9 =
Minor Error Change - Multiple Categories
= 1.0.8 =
Add options : multi-categories - Thanks to sujin2f
= 1.0.7 =
Add options : contentorder, month, past, key  - Thanks to sujin2f
= 1.0.6 =
Fix missing ul
= 1.0.5 =
* Add excerpt and thumbnail - Thanks to ankitpokhrel
= 1.0.2 =
* Add venue to shortcode - Thanks to ankitpokhrel
= 1.0.1 =
* Fix Firefox browser compatibility issue
= 1 =
* Initial Release

== Changelog ==

= 1.9 =
* Adds check for minimum WordPress and PHP version
* Adds a link to a short tutorial video
* Changes first example shortcode so it's easier to copy/paste

= 1.8 =
* Adds new orderby='title' option
* Fixes resetting the WordPress global query instead of just the post data

= 1.7.3 =
* Hide the "at" when using venue='true' and an event has no venue
* Adds additional WordPress filters to hide certain events

= 1.7.2 =
* Adds the ability to use schema='false' in the shortcode to hide the schema output

= 1.7.1 =
* Fix for month option where there's an all-day event the first day of the next month
* Fix for "There are no events" string not being translated automatically into other languages

= 1.7 =
* Adds structured data to the shortcode output (great for SEO and people finding your events)

= 1.6.1 =
* Added ecs-featured-event class if event is featured
* Internal changes to filtering by one or more categories

= 1.6 =
* Changes default ordering by the start date, use orderby="enddate" for previous default ordering

= 1.5.3 =
* Fixes translation of the "View all events" link into other languages
* Adds orderby parameter to order by start date, but still show events until the end date has passed

= 1.5.2 =
* Adds 'next' option for showing the next month of events

= 1.5.1 =
* Adds thumbsize option (ie. medium, large, thumbnail, full)

= 1.5 =
* Adds ability to translate the plugin into local languages
* Additional description of options

= 1.4.2 =
* Additional filter for changing the link for an event
* Adds category CSS classes for each event, so you can format each category differently

= 1.4.1 =
* Additional filters for formatting a single event

= 1.4 =
* Checks for whether The Events Calendar is installed
* Additional filters
* Improved design of shortcode help page

= 1.3 =
* Fixes issue with "viewall" showing the events twice
* Fixes time zone issue by using current_time() instead of date()
* Hides events that are marked 'hide from listing'
* Switches to tribe_get_events() to get the events
* Removes the ... from the end of the excerpt if less than the excerpt length
* Adds date_thumb option
* Adds additional filters

= 1.2 =
* Updates author/description (Event Calendar Newsletter / Brian Hogg Consulting)

= 1.0.11 =
Add Link to Thumbnail
merge pull request from d4mation -Replaced extracted variables with $atts as using extract was deprecated
=1.0.10 =
Minor Error Change - fix  name and slug
= 1.0.9 =
Minor Error Change - Multiple Categories
= 1.0.8 =
Add options : multi-categories - Thanks to sujin2f
= 1.0.7 =
Add options : contentorder, month, past, key  - Thanks to sujin2f
= 1.0.6 =
Fix missing ul
= 1.0.5 =
* Add excerpt and thumbnail - Thanks to ankitpokhrel
= 1.0.2 =
* Add venue to shortcode - Thanks to ankitpokhrel
= 1.0.1 =
* Fix Firefox browser compatibility issue
= 1 =
* Initial Release
