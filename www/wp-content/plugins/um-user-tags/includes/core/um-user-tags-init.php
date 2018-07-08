<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class UM_User_Tags_API
 */
class UM_User_Tags_API {


	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * @var array
	 */
	public $filters = array();


	/**
	 * @return UM_User_Tags_API
	 */
	static public function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * UM_User_Tags_API constructor.
	 */
	function __construct() {
		// Global for backwards compatibility.
		$GLOBALS['um_user_tags'] = $this;
		add_filter( 'um_call_object_User_Tags_API', array( &$this, 'get_this' ) );

		$this->admin();
		if ( UM()->is_request( 'admin' ) ) {
			$this->admin_upgrade();
		}
		$this->taxonomies();
		$this->enqueue();
		$this->shortcode();

		add_action( 'plugins_loaded', array( &$this, 'init' ), 0);

		require_once um_user_tags_path . 'includes/core/um-user-tags-widget.php';
		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );

		add_filter( 'um_settings_default_values', array( &$this, 'default_settings' ), 10, 1 );
		add_filter( 'um_excluded_taxonomies', array( &$this, 'excluded_taxonomies' ), 10, 1 );
	}


	/**
	 * @param $defaults
	 *
	 * @return array
	 */
	function default_settings( $defaults ) {
		$defaults = array_merge( $defaults, $this->setup()->settings_defaults );
		return $defaults;
	}


	/**
	 * @param $taxes
	 *
	 * @return array
	 */
	function excluded_taxonomies( $taxes ) {
		$taxes[] = 'um_user_tag';
		return $taxes;
	}


	/**
	 * @return um_ext\um_user_tags\core\User_Tags_Setup()
	 */
	function setup() {
		if ( empty( UM()->classes['um_user_tags_setup'] ) ) {
			UM()->classes['um_user_tags_setup'] = new um_ext\um_user_tags\core\User_Tags_Setup();
		}
		return UM()->classes['um_user_tags_setup'];
	}


	/**
	 * @return $this
	 */
	function get_this() {
		return $this;
	}


	/**
	 * @return um_ext\um_user_tags\core\User_Tags_Shortcode()
	 */
	function shortcode() {
		if ( empty( UM()->classes['um_user_tags_shortcode'] ) ) {
			UM()->classes['um_user_tags_shortcode'] = new um_ext\um_user_tags\core\User_Tags_Shortcode();
		}
		return UM()->classes['um_user_tags_shortcode'];
	}


	/**
	 * @return um_ext\um_user_tags\core\User_Tags_Enqueue()
	 */
	function enqueue() {
		if ( empty( UM()->classes['um_user_tags_enqueue'] ) ) {
			UM()->classes['um_user_tags_enqueue'] = new um_ext\um_user_tags\core\User_Tags_Enqueue();
		}
		return UM()->classes['um_user_tags_enqueue'];
	}


	/**
	 * @return um_ext\um_user_tags\core\User_Tags_Admin()
	 */
	function admin() {
		if ( empty( UM()->classes['um_user_tags_admin'] ) ) {
			UM()->classes['um_user_tags_admin'] = new um_ext\um_user_tags\core\User_Tags_Admin();
		}
		return UM()->classes['um_user_tags_admin'];
	}


	/**
	 * @return um_ext\um_user_tags\admin\core\Admin_Upgrade()
	 */
	function admin_upgrade() {
		if ( empty( UM()->classes['um_user_tags_admin_upgrade'] ) ) {
			UM()->classes['um_user_tags_admin_upgrade'] = new um_ext\um_user_tags\admin\core\Admin_Upgrade();
		}
		return UM()->classes['um_user_tags_admin_upgrade'];
	}


	/**
	 * @return um_ext\um_user_tags\core\User_Tags_Taxonomies()
	 */
	function taxonomies() {
		if ( empty( UM()->classes['um_user_tags_taxonomies'] ) ) {
			UM()->classes['um_user_tags_taxonomies'] = new um_ext\um_user_tags\core\User_Tags_Taxonomies();
		}
		return UM()->classes['um_user_tags_taxonomies'];
	}


	/**
	 * Init actions/filters
	 */
	function init() {
		require_once um_user_tags_path . 'includes/core/actions/um-user-tags-fields.php';
		require_once um_user_tags_path . 'includes/core/actions/um-user-tags-profile.php';
		require_once um_user_tags_path . 'includes/core/actions/um-user-tags-admin.php';

		require_once um_user_tags_path . 'includes/core/filters/um-user-tags-fields.php';
		require_once um_user_tags_path . 'includes/core/filters/um-user-tags-settings.php';
	}


	/**
	 * Get user tags by metakey
	 *
	 * @param int $user_id
	 * @param string $metakey
	 *
	 * @return string
	 */
	function get_tags( $user_id, $metakey ) {
		um_fetch_user( $user_id );
		$tags = um_user( $metakey );
		if ( empty( $tags ) ) {
			return '';
		}

		$limit = UM()->options()->get( 'user_tags_max_num' );

		$value = '<span class="um-user-tags">';

		$i = 0;
		$remaining = 0;

		$link = um_get_core_page( 'members' );

		if ( is_array( $tags ) ) {
			foreach ( $tags as $tag ) {

				if ( is_numeric( $tag ) ) {
					$term = get_term_by( 'id', $tag, 'um_user_tag' );
				} else {
					$term = get_term_by( 'slug', $tag, 'um_user_tag' );

					if ( ! $term ) {
						$term = get_term_by( 'name', $tag, 'um_user_tag' );
					}
				}

				if ( $term ) {
					$i++;
					$class = 'um-user-tag um-tag-' . $term->term_id;
					if ( $term->description ) {
						$class .= ' um-user-tag-desc';
					}

					if ( $limit > 0 && $i > $limit ) {
						$class .= ' um-user-hidden-tag';
						$remaining++;
					}

					$tagname = sprintf( __( '%s', 'um-user-tags' ), $term->name );

					if ( UM()->options()->get( 'members_page' ) ) {
						$link = add_query_arg( $metakey, $term->term_id, $link );
						$link = add_query_arg( 'um_search', 1, $link );

						$show_tag_link = apply_filters( 'um_user_tag__show_tag_link', true );
						if ( $show_tag_link ) {
							$value .= '<span class="' . $class . '" title="' . $term->description . '"><a href="' . $link . '">' . $tagname . '</a></span>';
						} else {
							$value .= '<span class="' . $class . '" title="' . $term->description . '">' . $tagname . '</span>';
						}
					} else {
						$value .= '<span class="' . $class . '" title="' . $term->description . '">' . $tagname . '</span>';
					}
				}
			}
		}

		if ( $i <= 0 ) {
			return '';
		}

		if ( $remaining > 0 ) {
			$value .= '<span class="um-user-tag um-user-tag-more"><a href="#">' . sprintf(__('%s more', 'um-user-tags'), $remaining) . '</a></span>';
		}

		$value .= '</span><div class="um-clear"></div>';

		return $value;
	}


	/**
	 * UM Tags Widgets init
	 */
	function widgets_init() {
		register_widget( 'um_user_tags' );
	}
}

//create class var
add_action( 'plugins_loaded', 'um_init_user_tags', -10, 1 );
function um_init_user_tags() {
	if ( function_exists( 'UM' ) ) {
		UM()->set_class( 'User_Tags_API', true );
	}
}