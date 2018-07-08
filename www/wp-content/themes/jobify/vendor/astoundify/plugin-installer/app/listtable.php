<?php
/**
 * Create a custom list table for retrieving only the plugins we want.
 *
 * @package Astoundify
 * @subpackage PluginInstaller
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extend the existing WP_Plugin_Install_List_Table to override item preperation
 * and only include the items defined by the library.
 *
 * @since 1.0.0
 */
class Astoundify_PluginInstaller_ListTable extends WP_Plugin_Install_List_Table {

	/**
	 * Get all of the plugins we want to install.
	 *
	 * @todo see if this can be done with one query. Otherwise cache it until the
	 * end of time.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->groups = array();

		$this->set_pagination_args( array(
			'total_items' => 4,
			'per_page' => 1000,
		) );

		$this->items = $this->query_dotorg();
	}

	/**
	 * Query the WP.org API and get the information for each plugin.
	 *
	 * Cached with a hash of all required plugins. Updating the list will
	 * bust it and fetch them again.
	 *
	 * @since 1.0.0
	 *
	 * @return array $items
	 */
	public function query_dotorg() {
		$plugins = astoundify_plugininstaller_get_option( 'plugins' );
		$key = md5( serialize( $plugins ) );

		if ( false === ( $items = get_transient( $key ) ) ) {
			$items = array();

			$args = array(
				'locale' => function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale(),
				'fields' => array(
					'last_updated' => true,
					'icons' => true,
					'active_installs' => true,
					'short_description' => true,
				),
			);

			foreach ( $plugins as $plugin ) {
				$plugin = plugins_api( 'plugin_information', wp_parse_args( array(
					'slug' => $plugin,
				), $args ) );

				if ( ! is_wp_error( $plugin ) ) {
					$items[] = $plugin;
				}
			}

			set_transient( $key, $items, DAY_IN_SECONDS * 30 );
		}

		return $items;
	}

	/**
	 * Output the table. Wrap in a `#plugin-filter` ID so the updates.js
	 * thinks we are on a normal plugin page.
	 *
	 * @since 1.0.0
	 */
	public function display() {
?>

<form id="plugin-filter" class="astoundify-plugininstaller" method="post">

	<?php $this->display_rows_or_placeholder(); ?>

</form>

<?php
	}

}
