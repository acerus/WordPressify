<?php
/**
 * Bootstrap a namespaced app.
 */

namespace Your\Plugin;

/**
 * Autoloads file test.php
 *
 * Autoloads submodule `bar` in `Bar/Manager.php`
 * `bar` autohooks in to the `init` action of WordPress
 */
$test = new Test();

/**
 * Or call it directly
 */

// remove_action( 'init', array( $test->bar(), 'hello' ) );
// $test->bar()->hello();
