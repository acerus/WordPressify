<?php
/**
 * Import a child theme.
 *
 * @uses Astoundify_AbstractItemImport
 * @implements Astoundify_ItemImportInterface
 *
 * @since 1.3.0
 */
class Astoundify_ItemImport_ChildTheme extends Astoundify_AbstractItemImport implements Astoundify_ItemImportInterface {

	/**
	 * @since 1.3.0
	 * @access public
	 * @var WP_Theme $theme
	 */
	public $theme;

	/**
	 * @since 1.3.0
	 * @access public
	 * @var string $child_theme_slug
	 */
	public $child_theme_slug;

	/**
	 * Get the current theme
	 *
	 * @since 1.3.0
	 *
	 * @return WP_Theme
	 */
	public function get_theme() {
		if ( ! isset( $this->theme ) ) {
			$this->theme = wp_get_theme();
		}

		return $this->theme;
	}

	/**
	 * Get the contents of style.css
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_stylesheet() {
		if ( ! isset( $this->item['data']['stylesheet'] ) ) {
			return false;
		}

		return $this->item['data']['stylesheet'];
	}

	/**
	 * Get the contents of functions.php
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_functions() {
		if ( ! isset( $this->item['data']['functions'] ) ) {
			return false;
		}

		return $this->item['data']['functions'];
	}

	/**
	 * Get (and download) the screenshot if set.
	 *
	 * @since 1.3.0
	 *
	 * @return string The path to the image.
	 */
	public function get_screenshot() {
		if ( ! isset( $this->item['data']['screenshot'] ) ) {
			return false;
		}

		$screenshot = Astoundify_Utils::upload_asset( $this->item['data']['screenshot'] );
		$screenshot = get_attached_file( $screenshot );

		return $screenshot;
	}

	/**
	 * Create the child theme
	 *
	 * @since 1.3.0
	 *
	 * @return mixed False on failure or child theme slug
	 */
	public function create_child_theme() {
		$parent_dir = $this->get_theme()->get_stylesheet_directory();
		$child_dir = trailingslashit( get_theme_root() ) . $this->get_id();

		if ( ! $child_dir ) {
			return false;
		}

		$funcs = $this->get_functions();
		$style = $this->get_stylesheet();
		$screenshot = $this->get_screenshot();

		if ( ! wp_mkdir_p( $child_dir ) ) {
			return false;
		}

		$creds = request_filesystem_credentials( admin_url() );
		WP_Filesystem( $creds ); // we already have direct access

		global $wp_filesystem;

		if ( $style ) {
			$wp_filesystem->put_contents( $child_dir . '/style.css', $style );
		}

		if ( $funcs ) {
			$wp_filesystem->put_contents( $child_dir . '/functions.php', $funcs );
		}

		if ( $screenshot ) {
			$wp_filesystem->copy( $screenshot, "$child_dir/" . 'screenshot.png' );
		}

		return $this->get_id();
	}

	/**
	 * Import a child theme.
	 *
	 * @since 1.3.0
	 * @return bool True on success
	 */
	public function import() {
		$result = $this->get_default_error();

		// switch to previously created child theme
		if ( $this->get_previous_import() ) {
			switch_theme( $this->get_id() );

			return true;
		} else {
			$child_theme = $this->create_child_theme();

			if ( $child_theme ) {
				switch_theme( $child_theme );

				$settings = get_option( 'theme_mods_' . $this->get_id() );

				// only update if they do not exist
				if ( false === $settings ) {
					$parent_settings = get_option( 'theme_mods_' . $this->get_theme()->get_stylesheet() );
					update_option( 'theme_mods_' . $child_theme, $parent_settings );
				}

				return true;
			}
		}

		return $result;
	}

	/**
	 * Reset a single item
	 *
	 * @since 1.3.0
	 * @return bool True on success
	 */
	public function reset() {
		switch_theme( wp_get_theme()->parent() );
	}

	/**
	 * Retrieve a previously imported item
	 *
	 * @since 1.3.0
	 * @uses $wpdb
	 * @return bool True if a child theme already exists
	 */
	public function get_previous_import() {
		$themes = wp_get_themes();
		$has_import = false;

		if ( ! $this->get_id() ) {
			return $has_import;
		}

		foreach ( $themes as $theme ) {
			if ( $this->get_id() === $theme->get_template() ) {
				return $has_import = true;
			}
		}

		return $has_import;
	}

}
