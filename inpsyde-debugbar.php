<?php
/**
 * Plugin Name: Inpsyde DebugBar
 * Plugin URI:
 * Description: DebugBar for WordPress.
 * Author:      Inpsyde GmbH
 * Author URI:  http://inpsyde.com
 * Version:     1.0
 * Text Domain: inpsyde-debugbar
 * Domain Path: /languages
 * License:     GPLv3
 */

namespace Inpsyde\DebugBar;


if ( ! function_exists( 'add_action' ) ) {
	return;
}

$autoload_file = __DIR__ . '/vendor/autoload.php';
if ( ! file_exists( $autoload_file ) ) {

	add_action( 'admin_notices', function() {

		$dir = dirname( __FILE__ );
		$msg = sprintf(
			'Please exec <code>composer install</code> in <code>%s</code> before using the plugin <strong>%s</strong>.',
			$dir,
			basename( $dir )
		);

		echo '<div class="error"><p>' . $msg . '</p></div>';
	} );

	return;
}
include_once( $autoload_file );

add_action( 'plugins_loaded', __NAMESPACE__ . '\run' );

/**
 * Init and run plugin.
 *
 * @wp-hook init
 *
 * @return void
 */
function run() {

	// init the debug-bar
	$debug_bar = new Plugin();
	$debug_bar->run();
}

/**
 * @return \Monolog\Logger
 */
function logger(){
	return Plugin::get_logger();
}

