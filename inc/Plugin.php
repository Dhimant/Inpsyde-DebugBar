<?php
namespace Inpsyde\DebugBar;

use Monolog;

/**
 * Class Plugin
 *
 * @package Inpsyde\DebugBar
 */
class Plugin {

	/**
	 * @var Monolog\Logger
	 */
	private static $logger;

	/**
	 * @var View\DebugBar
	 */
	private $view;

	/**
	 * @var Collector\CollectorInterface[]
	 */
	private $tabs = array();

	/**
	 * Run plugin.
	 *
	 * @return void
	 */
	public function run() {

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		// init the logger
		$handlers   = array();
		$processors = array();

		$wp_handler = new Handler\WordPress();
		$wp_handler->setFormatter( new Formatter\Html() );
		$handlers[] = $wp_handler;

		$this->add_collector( $wp_handler );
		self::$logger = new Monolog\Logger( 'default', $handlers, $processors );

		// adding collectors to debug-bar
		$this->add_collector( new Collector\SQLQueries() );
		$this->add_collector( new Collector\Request() );
		$this->add_collector( new Collector\Cookie() );
		$this->add_collector( new Collector\SystemInfo() );
		$this->add_collector( new Collector\Transient() );
		$this->add_collector( new Collector\Conditional() );
		$this->add_collector( new Collector\Cache() );
		$this->add_collector( new Collector\Rewrite() );
		$this->add_collector( new Collector\Enqueue() );

		$this->view = new View\DebugBar( $this->tabs );
		if ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {

			add_action( 'wp_footer', array( $this->view, 'render' ), 99999 );
			add_action( 'admin_footer', array( $this->view, 'render' ), 99999 );
			add_action( 'wp_enqueue_scripts', array( $this->view, 'load_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this->view, 'load_assets' ) );

		}


		/**
		 * Runs after internal actions have been registered.
		 *
		 * @param Plugin
		 */
		do_action( 'inpsyde_debugbar_loaded', $this );

		add_action( 'wp_loaded', array( $this, 'late_load' ), 0 );
	}

	/**
	 * Late load callback for our Plugin to trigger some actions or change the plugin
	 *
	 * @wp-hook wp_loaded
	 *
	 * @return void
	 */
	public function late_load() {

		/**
		 * Late loading event for our Plugin.
		 *
		 * @param Plugin
		 */
		do_action( 'inpsyde_debugbar_late_load', $this );

		/**
		 * Filter to add some custom Tabs or remove existing ones.
		 *
		 * @param Collector\CollectorInterface[]
		 */
		$this->tabs = apply_filters( 'inpsyde_debugbar_tabs', $this->tabs );
	}

	/**
	 * @param Collector\CollectorInterface $tab
	 *
	 * @return void
	 */
	public function add_collector( Collector\CollectorInterface $tab ) {

		$this->tabs[] = $tab;
	}

	/**
	 * @return Monolog\Logger
	 */
	public static function get_logger() {
		return self::$logger;
	}


}
