<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name: Inpsyde DebugBar
 * Plugin URI:  https://github.com/inpsyde/Inpsyde-DebugBar
 * Description: DebugBar for WordPress.
 * Author:      Inpsyde GmbH
 * Author URI:  http://inpsyde.com
 * Version:     1.0
 * Text Domain: inpsyde-debugbar
 * Domain Path: /languages
 * License:     GPLv2+
 */

namespace Inpsyde\DebugBar;

if ( ! function_exists( 'add_action' ) ) {
	return;
}

$autoload_file = __DIR__ . '/vendor/autoload.php';
if ( ! file_exists( $autoload_file ) || ! is_readable( $autoload_file ) ) {

	add_action(
		'admin_notices', function () {

		$dir = dirname( __FILE__ );
		$msg = sprintf(
			'Please exec <code>composer install</code> in <code>%s</code> before using the plugin <strong>%s</strong> or change the rights to read access.',
			$dir,
			basename( $dir )
		);

		echo '<div class="error"><p>' . $msg . '</p></div>';
	}
	);

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
function logger() {
	return Plugin::get_logger();
}
/**
 * Creates and returns the config of the plugin.
 *
 * @return \Inpsyde\DebugBar\Model\Config $config
 */
function get_config() {
	$config = wp_cache_get( 'config', 'inpsyde-debugbar' );
	if ( is_array( $config ) ) {
		return $config;
	}
	$file            = __FILE__;
	$config          = new Model\Config();
	$plugin_dir_path = plugin_dir_path( $file );
	$config->set( 'plugin_dir_path', $plugin_dir_path );
	$config->set( 'plugin_file_path', $file );
	$plugin_base_name = plugin_basename( $file );
	$config->set( 'plugin_base_name', $plugin_base_name );
	$plugin_url = plugins_url( '/', $file );
	$config->set( 'plugin_url', $plugin_url );
	$asset_config = array(
		// asset urls
		'css_url'   => $plugin_url . 'assets/css/',
		'js_url'    => $plugin_url . 'assets/js/',
		'image_url' => $plugin_url . 'assets/img/',
		// asset dirs
		'css_dir'   => $plugin_dir_path . 'assets/css/',
		'js_dir'    => $plugin_dir_path . 'assets/js/',
		'image_dir' => $plugin_dir_path . 'assets/img/',
	);
	$config->import( $asset_config );
	// plugin headers
	$default_headers = array(
		'plugin_name'     => 'Plugin Name',
		'plugin_uri'      => 'Plugin URI',
		'description'     => 'Description',
		'author'          => 'Author',
		'version'         => 'Version',
		'author_uri'      => 'Author URI',
		'textdomain'      => 'Text Domain',
		'textdomain_path' => 'Domain Path',
	);
	$plugin_headers = get_file_data( $file, $default_headers );
	$config->import( $plugin_headers );
	// creating some default nonce and nonce.name
	$text_domain  = $config->get( 'textdomain' );
	$nonce_config = array(
		'nonce'      => $text_domain,
		'nonce_name' => $text_domain . '_nonce'
	);
	$config->import( $nonce_config );
	// set the correct text domain path
	$text_domain_path = dirname( $config->get( 'plugin_base_name' ) );
	$text_domain_path .= $config->get( 'textdomain_path' );
	$config->set( 'textdomain_path', $text_domain_path );
	wp_cache_set( 'config', $config, 'inpsyde-debugbar' );
	return $config;
}
