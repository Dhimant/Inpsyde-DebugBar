<?php
namespace Inpsyde\DebugBar\Collector;


interface CollectorInterface {

	/**
	 * Returns the name of the Collector.
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Renders the Collector tab-view.
	 *
	 * @return void
	 */
	public function render();

}