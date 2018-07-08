<?php
/**
 * Generate CSS programatically.
 *
 * ❤️  Make
 *
 * @link https://github.com/thethemefoundry/make/blob/master/src/inc/style/css.php
 *
 * @package Astoundify
 * @subpackage ThemeCustomizer
 * @since 1.0.0
 */
class Astoundify_ThemeCustomizer_Output_CSSGenerator {

	/**
	 * The array for storing added CSS rule data.
	 *
	 * @since 1.0.0
	 * @var array Holds the data to be printed out.
	 */
	public static $data = array();

	/**
	 * Optional line ending character for debug mode.
	 *
	 * @since 1.0.0
	 * @var string Line ending character used to better style the CSS.
	 */
	private static $line_ending = '';

	/**
	 * Optional tab character for debug mode.
	 *
	 * @since 1.0.0
	 * @var string Tab character used to better style the CSS.
	 */
	private static $tab = '';

	/**
	 * Optional space character for debug mode.
	 *
	 * @since 1.0.0
	 * @var string Space character used to better style the CSS.
	 */
	private static $space = '';

	/**
	 * Initialize the object.
	 *
	 * @since 1.0.0
	 * @return self
	 */
	function __construct() {
		// Set line ending and tab
		if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) {
			self::$line_ending = "\n";
			self::$tab = "\t";
			self::$space = ' ';
		}
	}

	/**
	 * Add a new CSS rule to the array.
	 *
	 * Accepts data to eventually be turned into CSS. Usage:
	 *
	 * self::$add( array(
	 *     'selectors'    => array( '.site-header-main' ),
	 *     'declarations' => array(
	 *         'background-color' => '#00ff00',
	 *     ),
	 *     'media' => 'screen and (min-width: 800px)',
	 * ) );
	 *
	 * Selectors represent the CSS selectors; declarations are the CSS properties and values with keys being properties
	 * and values being values. 'media' can also be declared to specify the media query.
	 *
	 * Note that data *must* be sanitized when adding to the data array. Because every piece of CSS data has special
	 * sanitization concerns, it must be handled at the time of addition, not at the time of output. The theme handles
	 * this in the the other helper files, i.e., the data is already sanitized when `add()` is called.
	 *
	 * @since 1.0.0
	 * @param array $data The selectors and properties to add to the CSS.
	 * @return void
	 */
	public static function add( $data ) {
		$entry = array();

		// Bail if the required properties aren't present
		if ( ! isset( $data['selectors'] ) || ! isset( $data['declarations'] ) ) {
			return;
		}

		// Sanitize selectors
		$entry['selectors'] = array_map( 'trim', (array) $data['selectors'] );
		$entry['selectors'] = array_unique( $entry['selectors'] );

		// Sanitize declarations
		$entry['declarations'] = array_map( 'trim', (array) $data['declarations'] );

		// Check for media query
		if ( isset( $data['media'] ) ) {
			$media = $data['media'];
		} else {
			$media = 'all';
		}

		// Create new media query if it doesn't exist yet
		if ( ! isset( self::$data[ $media ] ) || ! is_array( self::$data[ $media ] ) ) {
			self::$data[ $media ] = array();
		}

		// Look for matching selector sets
		$match = false;
		foreach ( self::$data[ $media ] as $key => $rule ) {
			$diff1 = array_diff( $rule['selectors'], $entry['selectors'] );
			$diff2 = array_diff( $entry['selectors'], $rule['selectors'] );
			if ( empty( $diff1 ) && empty( $diff2 ) ) {
				$match = $key;
				break;
			}
		}

		// No matching selector set, add a new entry
		if ( false === $match ) {
			self::$data[ $media ][] = $entry;
		} // End if().
		else {
			self::$data[ $media ][ $match ]['declarations'] = array_merge( self::$data[ $media ][ $match ]['declarations'], $entry['declarations'] );
		}
	}

	/**
	 * Check if there are any items in the private data property array.
	 *
	 * @since 1.0.0
	 * @return bool True if there are items.
	 */
	public static function has_rules() {
		return ! empty( self::$data );
	}

	/**
	 * Compile the data array into standard CSS syntax
	 *
	 * @since 1.0.0.
	 * @return string The CSS that is built from the data.
	 */
	public static function build() {
		if ( ! self::has_rules() ) {
			return '';
		}

		$n = self::$line_ending;
		$s = self::$space;

		// Make sure the 'all' array is first
		if ( isset( self::$data['all'] ) && count( self::$data ) > 1 ) {
			$all = array(
				'all' => self::$data['all'],
			);
			unset( self::$data['all'] );
			self::$data = array_merge( $all, self::$data );
		}

		$output = '';

		foreach ( self::$data as $query => $ruleset ) {
			$t = '';

			if ( 'all' !== $query ) {
				$output .= "\n@media " . $query . $s . '{' . $n;
				$t = self::$tab;
			}

			// Build each rule
			foreach ( $ruleset as $rule ) {
				$output .= self::parse_selectors( $rule['selectors'], $t ) . $s . '{' . $n;
				$output .= self::parse_declarations( $rule['declarations'], $t );
				$output .= $t . '}' . $n;
			}

			if ( 'all' !== $query ) {
				$output .= '}' . $n;
			}
		}

		return $output;
	}

	/**
	 * Compile the selectors in a rule into a string.
	 *
	 * @since 1.0.0.
	 * @param array  $selectors Selectors to combine into single selector.
	 * @param string $tab Tab character.
	 * @return string Results of the selector combination.
	 */
	private static function parse_selectors( $selectors, $tab = '' ) {
		/**
		 * Note that these selectors are hardcoded in the code base. They are never the result of user input and can
		 * thus be trusted to be sane.
		 */
		$n      = self::$line_ending;
		$output = $tab . implode( ",{$n}{$tab}", $selectors );

		return $output;
	}

	/**
	 * Compile the declarations in a rule into a string.
	 *
	 * @since 1.0.0
	 * @param array  $declarations Declarations for a selector.
	 * @param string $tab Tab character.
	 * @return string The combines declarations.
	 */
	private static function parse_declarations( $declarations, $tab = '' ) {
		$n = self::$line_ending;
		$t = self::$tab . $tab;
		$s = self::$space;

		$output = '';

		/**
		 * Note that when this output is prepared, it is not escaped, sanitized or otherwise altered.
		 * They should be sanitized when added via the `add()` method.
		 */
		foreach ( $declarations as $property => $value ) {
			// Exception for px/rem font size
			if ( 'font-size-px' === $property || 'font-size-rem' === $property ) {
				$property = 'font-size';
			}

			$parsed_value  = "{$t}{$property}:{$s}{$value};$n";

			$output .= $parsed_value;
		}

		return $output;
	}

	/**
	 * Darken a HEX value.
	 *
	 * @since unknown
	 * @return string
	 */
	public static function darken( $hex, $steps ) {
		$steps = max( -255, min( 255, $steps ) );

		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$hex = str_repeat( substr( $hex,0,1 ), 2 ) . str_repeat( substr( $hex,1,1 ), 2 ) . str_repeat( substr( $hex,2,1 ), 2 );
		}

		$r = hexdec( substr( $hex,0,2 ) );
		$g = hexdec( substr( $hex,2,2 ) );
		$b = hexdec( substr( $hex,4,2 ) );

		$r = max( 0,min( 255,$r + $steps ) );
		$g = max( 0,min( 255,$g + $steps ) );
		$b = max( 0,min( 255,$b + $steps ) );

		$r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
		$g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
		$b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );

		return '#' . $r_hex . $g_hex . $b_hex;
	}
}
