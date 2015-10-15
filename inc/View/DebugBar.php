<?php
namespace Inpsyde\DebugBar\View;

class DebugBar {

	/**
	 * @var \Inpsyde\DebugBar\Collector\CollectorInterface[]
	 */
	private $tabs = array();

	/**
	 * @param \Inpsyde\DebugBar\Collector\CollectorInterface[] $tabs
	 */
	public function __construct( $tabs ) {

		$this->tabs = $tabs;
	}

	/**
	 * Callback to render all tabs to footer.
	 *
	 * @wp-hook wp_footer, admin_footer
	 *
	 * @return void
	 */
	public function render() {
		?>
		<div id="inpsyde-debugbar" class="inpsyde-debugbar inpsyde-debugbar--is-hidden">
			<?php $this->render_header(); ?>
			<?php $this->render_tabs(); ?>
		</div>
		<?php

	}

	/**
	 * Internal function to render the tabbing.
	 *
	 * @return void
	 */
	private function render_tabs() {
		?>
		<ul class="inpsyde-debugbar__tabs">
			<?php $i = 0; ?>
			<?php foreach ( $this->tabs as $tab ) : ?>
				<?php $class = ( $i === 0 ) ? 'inpsyde-debugbar__tab-item--is-selected' : ''; ?>
				<li class="inpsyde-debugbar__tab-item <?php echo $class; ?>">
					<a class="inpsyde-debugbar__tab-item-link" href="#" data-target="tab-<?php echo $i; ?>"><?php echo $tab->get_name(); ?></a></li>
				<?php $i ++; ?>
			<?php endforeach; ?>
		</ul>
		<section class="inpsyde-debugbar__content-wrapper">
			<?php $i = 0; ?>
			<?php foreach ( $this->tabs as $tab ) : ?>
				<?php $class = ( $i === 0 ) ? '' : 'inpsyde-debugbar__content--is-hidden'; ?>
				<div id="tab-<?php echo $i; ?>" class="inpsyde-debugbar__content <?php echo $class; ?>"><?php $tab->render(
					); ?></div>
				<?php $i ++; ?>
			<?php endforeach; ?>
		</section>
		<?php
	}

	/**
	 * Internal function to render the DebugBar-header
	 *
	 * @return void
	 */
	private function render_header() {
		?>
		<header class="inpsyde-debugbar__header">
			<div class="inpsyde-debugbar__memory">
				<?php
				$msg = __( '%s MB Memory Usage', 'inpsyde' );
				$memory = memory_get_peak_usage();
				$memory = $memory / pow( 1024, 2 );
				$memory = number_format( $memory, 1 );
				echo sprintf( $msg, $memory )
				?>
			</div>
			<h1 class="inpsyde-debugbar__headline"><?php _e( 'Inpsyde DebugBar', 'inpsyde' ); ?></h1>
		</header>
		<?php
	}

	/**
	 * Load CSS and JS for debug pane.
	 *
	 * @wp-hook wp_enqueue_scripts
	 *
	 * @return void
	 */
	public function load_assets() {

		$asset_uri = WP_CONTENT_URL . '/plugins/inpsyde-debugbar/assets/';
		$prefix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
			'inpsyde-debugger',
			$asset_uri . 'css/style' . $prefix . '.css',
			array(),
			NULL
		);

		wp_enqueue_script(
			'inpsyde-debugger',
			$asset_uri . 'js/inpsyde-debugger' . $prefix . '.js',
			array( 'jquery', 'backbone' ),
			NULL,
			TRUE
		);

	}
}