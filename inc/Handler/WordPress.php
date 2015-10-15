<?php

namespace Inpsyde\DebugBar\Handler;

use Inpsyde\DebugBar\Collector\CollectorInterface;
use Monolog\Logger;
use Monolog\Handler;

class WordPress extends Handler\AbstractHandler implements CollectorInterface {

	/**
	 *
	 * @var array
	 */
	protected $records = array();

	/**
	 * @param integer $level The minimum logging level at which this handler will be triggered
	 */
	public function __construct( $level = Logger::DEBUG ) {
		parent::__construct( $level, FALSE );
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle( array $record ) {
		if ( $record[ 'level' ] < $this->level ) {
			return FALSE;
		}
		$this->records[ ] = $record;

		return TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		$record_count = count( $this->records );
		$msg = __( 'Messages <span>%d</span>', 'inpsyde-debugbar' );
		$msg = sprintf( $msg, $record_count );

		return $msg;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		echo $this->getFormatter()->formatBatch( $this->records );
	}

}