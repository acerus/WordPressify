<?php
/**
 * Manage a page header.
 *
 * @since 3.3.0
 */
class Jobify_Page_Header {

	/**
	 * Should the page header be displayed?
	 *
	 * @since 3.3.0
	 * @return bool
	 */
	public static function show_page_header() {
		return '' == get_post()->page_show_header;
	}

}
