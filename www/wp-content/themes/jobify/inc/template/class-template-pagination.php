<?php

class Jobify_Template_Pagination {

	public function __construct() {
		add_action( 'jobify_loop_after', array( $this, 'output' ) );
	}

	public function output() {
		the_posts_pagination( array(
			'prev_text' => '<span class="screen-reader-text">' . __( 'Next Page', 'jobify' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . __( 'Previous Page', 'jobify' ) . '</span>',
		) );
	}

}
