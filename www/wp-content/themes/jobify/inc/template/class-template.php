<?php
/**
 * Template output.
 *
 * @since 3.0.0
 * @package Jobify
 * @category Frontend
 */
class Jobify_Template {

	public function __construct() {
		$this->includes();
		$this->setup();
	}

	private function includes() {
		$files = array(
			'class-template-assets.php',
			'class-template-navigation.php',
			'class-template-pagination.php',
			'class-template-comments.php',
			'class-template-header.php',
			'class-template-page-templates.php',
		);

		foreach ( $files as $file ) {
			require( get_template_directory() . '/inc/template/' . $file );
		}
	}

	private function setup() {
		$this->assets = new Jobify_Template_Assets();
		$this->navigation = new Jobify_Template_Navigation();
		$this->pagination = new Jobify_Template_Pagination();
		$this->comments = new Jobify_Template_Comments();
		$this->header = new Jobify_Template_Header();
	}

}
