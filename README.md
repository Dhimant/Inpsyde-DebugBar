# Inpsyde DebugBar

## Installation
1. Download or checkout repository into your Plugin-Folder.
2. Run `composer install` in `plugins/inpsyde-debugbar/`.
3. Activate Plugin.

## Enable/Disable DebugBar

To enable the DebugBar, add following definitions to your `wp-config.php`:

```php
define( 'WP_DEBUG', TRUE );
define( 'WP_DEBUG_DISPLAY', TRUE );

// Add SQL-query-tab to DebugBar - by default: FALSE
define( 'SAVEQUERIES', TRUE );
```

## Using the Logger

```php
// access to the logger
$logger = Inpsyde\DebugBar\logger();

$logger->info( 'message', $context_array );
$logger->notice( 'message', $context_array );
$logger->warning( 'message', $context_array );
$logger->error( 'message', $context_array );
$logger->critical( 'message', $context_array );
$logger->emergency( 'message', $context_array );
```

See more information about the logger and how to use here: https://github.com/Seldaek/monolog 

## Collectors

Following collectors are delivered by Plugin:

* Cache - WordPress internal Cache.
* Conditional - Conditional check which site currently viewed.
* Cookie - Cookies for the current user.
* Enqueue - All enqueues of script and styles.
* Request - The current Request with match of rewrite rules.
* Rewrite - All rewrite rules registered by WordPress and the current match.
* SQLQueries - All SQL-Queries for the current site.
* SystemInfo - Information about WordPress, Server, Theme and Plugins 
* Transient - Current executed transients.

### Add some custom Collectors

```php
class MyCollector implements Inpsyde\DebugBar\Collector\CollectorInterface {

	public function get_name() {
	
		return 'My collector';
	}
	
	public function render() {
		
		// your content goes here
	}

}

add_filter( 'inpsyde_debugbar_tabs', 'demo_filter_inpsyde_debugbar_tabs' );

/**
 *
 * @wp-hook inpsyde_debugbar_tabs
 *
 * @param   Inpsyde\DebugBar\Collector\CollectorInterface[] $tabs
 *
 * @return  Inpsyde\DebugBar\Collector\CollectorInterface[] $tabs
 */
function demo_filter_inpsyde_debugbar_tabs( $tabs ) {

	$tabs[] =  new MyCollector();

	return $tabs;
}
```