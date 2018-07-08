# Astoundify Module Loader

Easy autoloading and module dependency for PHP 5.2 (but supports 5.3+ as well).

## Why build this?

Manually requiring a ton of files is the worst.

## Usage

See all information in [../master/examples](../master/examples)

### PHP 5.2

[../master/examples/example-1/example-1.php](../master/examples/example-1/example-1.php)

### PHP 5.3+

[../master/examples/example-2/example-2.php](../master/examples/example-2/example-2.php)

### Defining Submodules

```php
class Your_Plugin_Test extends Astoundify_ModuleLoader_Module {
	/**
	 * @since 1.0.0
	 * @var array $modules
	 * @access protected
	 */
	protected $modules = array(
		'foo' => 'Your_Plugin_Foo_Manager', // PHP 5.2
		'bar' => 'Your\Plugin\Bar\Manager', // PHP 5.3
	);
}
```

This module will create a `foo` submodule dependency. The class will be called immediately (located in `/foo/manager.php` which means you likely should not have anything inside of your constructor.

```php
class Your_Plugin_Foo_Manager {
	/**
	 * Print another message
	 *
	 * @since 1.0.0
	 */
	public function hello() {
		var_dump( 'Hello' );
	}
}
```

### Accessing Submodules

Using the above example the `Your_Plugin_Foo_Manager` class can be accessed via the following:

```php
$plugin = new Your_Plugin_Test();
$plugin->foo()->hello();
```

### Hooking in to WordPress

Modules (and their submodules) support a method to appropriately hook in to WordPress. Simply define a `hook` method in your module.

```php
class Your_Plugin_Foo_Manager extends Astoundify_ModuleLoader_Module {
	/**
	 * Hook in to WordPress
	 *
	 * @since 1.0.0
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		add_action( 'init', array( $this, 'hello' ) );

		$this->is_hooked = true;
	}

	/**
	 * Print another message
	 *
	 * @since 1.0.0
	 */
	public function hello() {
		var_dump( 'Hello' );
	}
}
```

When this submodule is loaded the `hook` method is automatically called (after all of the current submodules own submodules have been loaded).

To use the `hook` method the class must be an instance of `Astoundify_ModuleLoader_Module`

### Loading other Dependencies

Some files are not classes but still need to be loaded.  Modules (and their submodules) support a method to appropriately load these files. Simply define a `load` method in your module.

```php
class Your_Plugin_Foo_Manager extends Astoundify_ModuleLoader_Module {
	/**
	 * Load other files.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		if ( $this->is_loaded() ) {
			return;
		}

		require_once( 'foo-functions.php' );

		$this->is_loaded = true;
	}

	/**
	 * Print another message
	 *
	 * @since 1.0.0
	 */
	public function hello() {
		echo foo_functions_hello();
	}
}
```

To use the `load` method the class must be an instance of `Astoundify_ModuleLoader_Module`

## Development

### Testing

https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/

```
$ phpunit
```

## Changelog

### 1.0.0

**November 4, 2016**

New: Initial release.
