<div class="wrap">
	<h2><?php _e( 'The Events Calendar Shortcode' ); ?></h2>

	<p><?php echo sprintf( esc_html__( 'The shortcode displays lists of your events. For example the shortcode to show next 8 events in the category "%s" in ASC order with date showing:', 'the-events-calendar-shortcode' ), 'festival' ); ?></p>

	<p class="shortcode">[ecs-list-events cat='festival' limit='8']</p>

    <p><a href="https://youtu.be/0okrUs-xOq4" target="_blank"><?php echo esc_html( __( 'Watch a Short Walk Through Video', 'the-events-calendar-shortcode' ) ) ?></a></p>

	<table>
		<tbody>
		<tr valign="top">
			<td valign="top">

				<div>
					<h2><?php echo esc_html( __( 'Basic shortcode', 'the-events-calendar-shortcode' ) ); ?></h2>
						<blockquote>[ecs-list-events]</blockquote>

					<h2><?php echo esc_html( __( 'Shortcode Options', 'the-events-calendar-shortcode' ) ); ?></h2>
					<?php do_action( 'ecs_admin_page_options_before' ); ?>

					<h3>cat</h3>
					<p><?php echo esc_html( __( 'Represents single event category.  Use commas when you want multiple categories', 'the-events-calendar-shortcode' ) ); ?>
						<blockquote>[ecs-list-events cat='festival']</blockquote>
						<blockquote>[ecs-list-events cat='festival, workshops']</blockquote>

					<?php do_action( 'ecs_admin_page_options_after_cat' ); ?>

					<h3>limit</h3>
					<p><?php echo esc_html( __( 'Total number of events to show. Default is 5.', 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events limit='3']</blockquote>
					<h3>order</h3>
					<p><?php echo esc_html( __( "Order of the events to be shown. Value can be 'ASC' or 'DESC'. Default is 'ASC'. Order is based on event date.", 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events order='DESC']</blockquote>
					<h3>date</h3>
					<p><?php echo esc_html( __( "To show or hide date. Value can be 'true' or 'false'. Default is true.", 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events eventdetails='false']</blockquote>
					<h3>venue</h3>
					<p><?php echo esc_html( __( "To show or hide the venue. Value can be 'true' or 'false'. Default is false.", 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events venue='true']</blockquote>
					<h3>excerpt</h3>
					<p><?php echo esc_html( __( 'To show or hide the excerpt and set excerpt length. Default is false.', 'the-events-calendar-shortcode' ) ); ?><p>
						<blockquote>[ecs-list-events excerpt='true']</blockquote>
						<blockquote>[ecs-list-events excerpt='300']</blockquote>
					<h3>thumb</h3>
					<p><?php echo esc_html( __( 'To show or hide thumbnail/featured image. Default is false.', 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events thumb='true']</blockquote>
					<p><?php echo sprintf( esc_html( __( 'You can use 2 other attributes: %s and %s to customize the thumbnail size', 'the-events-calendar-shortcode' ) ), 'thumbwidth', 'thumbheight' ); ?></p>
						<blockquote>[ecs-list-events thumb='true' thumbwidth='150' thumbheight='150']</blockquote>
					<p><?php echo sprintf( esc_html( __( 'or use %s to specify the pre-set size to use, for example:', 'the-events-calendar-shortcode' ) ), 'thumbsize' ); ?></p>
						<blockquote>[ecs-list-events thumb='true' thumbsize='large']</blockquote>

					<h3>message</h3>
					<p><?php echo esc_html( sprintf( __( "Message to show when there are no events. Defaults to '%s'", 'the-events-calendar-shortcode' ), translate( 'There are no upcoming events at this time.', 'tribe-events-calendar' ) ) ); ?></p>
					<h3>viewall</h3>
					<?php if ( function_exists( 'tribe_get_event_label_plural' ) ): ?>
						<p><?php echo esc_html( sprintf( __( "Determines whether to show '%s' or not. Values can be 'true' or 'false'. Default to 'true'", 'the-events-calendar-shortcode' ), sprintf( __( 'View All %s', 'the-events-calendar' ), tribe_get_event_label_plural() ) ) ); ?></p>
					<?php endif; ?>
						<blockquote>[ecs-list-events cat='festival' limit='3' order='DESC' viewall='false']</blockquote>
					<h3>contentorder</h3>
					<p><?php echo esc_html( sprintf( __( 'Manage the order of content with commas. Defaults to %s', 'the-events-calendar-shortcode' ), 'title, thumbnail, excerpt, date, venue' ) ); ?> </p>
						<blockquote>[ecs-list-events cat='festival' limit='3' order='DESC' viewall='false' contentorder='title, thumbnail, excerpt, date, venue']</blockquote>
					<h3>month</h3>
					<p><?php echo esc_html( sprintf( __( "Show only specific Month. Type '%s' for displaying current month only or '%s' for next month, ie:", 'the-events-calendar-shortcode' ), 'current', 'next' ) ); ?></p>
						<blockquote>[ecs-list-events cat='festival' month='2015-06']</blockquote>
					<h3>past</h3>
					<p><?php echo esc_html( __( 'Show outdated events (ie. events that have already happened)', 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events cat='festival' past='yes']</blockquote>
					<h3>key</h3>
					<p><?php echo esc_html( __( 'Use to hide events when the start date has passed, rather than the end date.  Will also change the order of events by start date instead of end date.', 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events cat='festival' key='start date']</blockquote>
					<h3>orderby</h3>
					<p><?php echo esc_html( __( 'Used to order by the end date instead of the start date.', 'the-events-calendar-shortcode' ) ); ?></p>
						<blockquote>[ecs-list-events orderby='enddate']</blockquote>
                    <p><?php echo esc_html( __( 'You can also use this to order by title if you wish:', 'the-events-calendar-shortcode' ) ); ?></p>
                        <blockquote>[ecs-list-events orderby='title']</blockquote>
					<?php do_action( 'ecs_admin_page_options_after' ); ?>

				</div>

			</td>
			<td valign="top" class="styling">
				<h3>Styling/Design</h3>

				<?php do_action( 'ecs_admin_page_styling_before' ); ?>

				<?php if ( apply_filters( 'ecs_show_upgrades', true ) ): ?>

					<p><?php echo esc_html( __( 'By default the plugin does not include styling. Events are listed in ul li tags with appropriate classes for styling and you can add your own CSS:', 'the-events-calendar-shortcode' ) ) ?></p>

					<ul>
						<li>ul class="ecs-event-list"</li>
						<li>li class="ecs-event" &amp; "ecs-featured-event" <?php echo esc_html( __( '(if featured)', 'the-events-calendar-shortcode' ) ) ?></li>
						<li><?php echo esc_html( sprintf( __( 'event title link is %s', 'the-events-calendar-shortcode' ), 'H4 class="entry-title summary"' ) ); ?> </li>
						<li><?php echo esc_html( sprintf( __( 'date class is %s', 'the-events-calendar-shortcode' ), 'time' ) ); ?></li>
						<li><?php echo esc_html( sprintf( __( 'venue class is %s', 'the-events-calendar-shortcode' ), 'venue' ) ); ?></li>
						<li>span .ecs-all-events</li>
						<li>p .ecs-excerpt</li>
					</ul>

					<div id="ecs-pro-description">

						<h3><?php echo esc_html__( 'Want a better looking design without adding any CSS?', 'the-events-calendar-shortcode' ) ?></h3>
						<p><?php echo sprintf( esc_html__( 'Check out %sThe Events Calendar Shortcode PRO%s. Some examples of the designs:', 'the-events-calendar-shortcode' ), '<a target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-design&utm_content=description">', '</a>' ); ?></p>
						<div id="ecs-pro-designs">
							<p><a target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-design-image-1&utm_content=description"><img alt="" style="width: 300px;" src="<?php echo plugins_url( '/static/shortcode-default-design-2.png', TECS_CORE_PLUGIN_FILE ) ?>"><br><?php echo esc_html( __( 'Pro version default design example', 'the-events-calendar-shortcode' ) ); ?></a></p>
							<p><a target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-design-image-2&utm_content=description"><img alt="" style="width: 300px;" src="<?php echo plugins_url( '/static/event-calendar-shortcode-compact-design.png', TECS_CORE_PLUGIN_FILE ) ?>"><br><?php echo esc_html( __( 'Pro version compact design example', 'the-events-calendar-shortcode' ) ); ?></a></p>
                            <p><a target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-design-image-calendar&utm_content=description"><img alt="" style="width: 300px;" src="<?php echo plugins_url( '/static/the-events-calendar-shortcode-calendar-demo.gif', TECS_CORE_PLUGIN_FILE ) ?>"><br><?php echo esc_html( __( 'Pro version calendar design example', 'the-events-calendar-shortcode' ) ); ?></a></p>
                            <p><a target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-design-image-columns&utm_content=description"><img alt="" style="width: 300px;" src="<?php echo plugins_url( '/static/the-events-calendar-shortcode-columns-photo-horizontal-design.png', TECS_CORE_PLUGIN_FILE ) ?>"><br><?php echo esc_html( __( 'Pro version horizontal/columns/photos design example', 'the-events-calendar-shortcode' ) ); ?></a></p>
                            <p><a target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-design-image-grouped&utm_content=description"><img alt="" style="width: 300px;" src="<?php echo plugins_url( '/static/the-events-calendar-shortcode-grouped-design.png', TECS_CORE_PLUGIN_FILE ) ?>"><br><?php echo esc_html( __( 'Pro version grouped design example', 'the-events-calendar-shortcode' ) ); ?></a></p>
						</div>

						<h3 class="additional-options"><?php echo esc_html__( "In addition to designs, you'll get more options including:", 'the-events-calendar-shortcode' ); ?></h3>
						<h4><?php echo esc_html__( 'Number of days', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Choose how many days to show events from, ie. 1 day or a week', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Tag', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Filter events listed by one or more tags', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Location', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Display events by city, state/province, or country', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Single Event', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'List the details of a single event by ID, for example on a blog post', 'the-events-calendar-shortcode' ) ?></p>
                        <h4><?php echo esc_html__( 'Featured', 'the-events-calendar-shortcode' ) ?></h4>
                        <p><?php echo esc_html__( 'Show only events marked as "featured"', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Button', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Add an easy to see button link to your event, and customize the colors/text', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Date', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Show only events for a specific day (ie. 2017-04-16), great for conferences', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Year', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Show only events for a specific year', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Offset', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Skip a certain number of events from the beginning, useful for using multiple shortcodes on the same page or splitting into columns.', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Full Description', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Use the full description instead of the excerpt (short description) of an event in the listing', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Future Only', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Only show events in the future even when using the month or year option.', 'the-events-calendar-shortcode' ) ?></p>
						<h4><?php echo esc_html__( 'Custom Design', 'the-events-calendar-shortcode' ) ?></h4>
						<p><?php echo esc_html__( 'Use the new default or compact designs, or create your own using one or more templates in your theme folder', 'the-events-calendar-shortcode' ) ?></p>
						<p><?php echo sprintf( esc_html__( '%sGet The Events Calendar Shortcode PRO%s', 'the-events-calendar-shortcode' ), '<a class="ecs-button" target="_blank" href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode?utm_source=plugin&utm_medium=link&utm_campaign=tecs-help-after-options&utm_content=description">', '</a>' ); ?> or <a href="https://demo.eventcalendarnewsletter.com/the-events-calendar-shortcode/">see it in action</p>
					</div>
				<?php endif; ?>
			</td>
		</tr>
		</tbody>
	</table>

	<p><small><?php echo sprintf( esc_html__( 'This plugin is not developed by or affiliated with The Events Calendar or %s in any way.', 'the-events-calendar-shortcode' ), 'Modern Tribe' ); ?></small></p>
</div>