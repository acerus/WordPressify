<?php

class Jobify_Widgets {

	public function __construct() {
		$widgets = array(
			'class-widget-callout.php',
			'class-widget-video.php',
			'class-widget-blog-posts.php',
			'class-widget-slider-generic.php',
			'class-widget-stats.php',
			'class-widget-feature-callout.php',
		);

		foreach ( $widgets as $widget ) {
			require_once( trailingslashit( dirname( __FILE__ ) ) . '_widgets/' . $widget );
		}

		$this->widgetized_pages = new Jobify_Widgetized_Pages();

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
	}

	function register_widgets() {
		register_widget( 'Jobify_Widget_Callout' );
		register_widget( 'Jobify_Widget_Video' );
		register_widget( 'Jobify_Widget_Blog_Posts' );
		register_widget( 'Jobify_Widget_Slider_Generic' );
		register_widget( 'Jobify_Widget_Feature_Callout' );
	}

	public function register_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Sidebar', 'jobify' ),
			'id'            => 'sidebar-blog',
			'description'   => __( 'Choose what should display on blog pages.', 'jobify' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Homepage Widget Area', 'jobify' ),
			'id'            => 'widget-area-front-page',
			'description'   => __( 'Choose what should display on the custom static homepage.', 'jobify' ),
			'before_widget' => '<section id="%1$s" class="widget widget--home %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title widget-title--home">',
			'after_title'   => '</h3>',
		) );

		for ( $i = 1; $i <= 4; $i++ ) {
			register_sidebar( array(
				'name'          => sprintf( __( 'Footer Widget Area Column %d', 'jobify' ), absint( $i ) ),
				'id'            => 'widget-area-footer' . ( $i > 1 ? ( '-' . absint( $i ) ) : '' ),
				'description'   => __( 'Choose what should display in this footer widget column.', 'jobify' ),
				'before_widget' => '<aside id="%1$s" class="widget widget--footer %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title widget-title--footer">',
				'after_title'   => '</h3>',
			) );
		}

		if ( ! (
				jobify()->get( 'wp-job-manager-wc-paid-listings' ) ||
				jobify()->get( 'restrict-content-pro' ) ||
				jobify()->get( 'wp-job-manager-wc-advanced-paid-listings' )
			) ||
			apply_filters( 'jobify_legacy_price_table', false )
		) {
			require_once( trailingslashit( dirname( __FILE__ ) ) . '_widgets/class-widget-price-option.php' );
			require_once( trailingslashit( dirname( __FILE__ ) ) . '_widgets/class-widget-price-table.php' );

			register_widget( 'Jobify_Widget_Price_Table' );
			register_widget( 'Jobify_Widget_Price_Option' );

			register_sidebar( array(
				'name'          => __( 'Price Table', 'jobify' ),
				'id'            => 'widget-area-price-options',
				'description'   => __( 'Drag multiple "Price Option" widgets here. Then drag the "Pricing Table" widget to the "Homepage Widget Area".', 'jobify' ),
				'before_widget' => '<div class="price-option">',
				'after_widget'  => '</div>',
			) );
		}
	}

}
