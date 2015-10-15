<?php

namespace Inpsyde\DebugBar\Collector;

class SqlQueries implements CollectorInterface {

	/**
	 * Contains the time in ms for slow queries
	 * @var float
	 */
	private $slow_query_time = 0.05;

	private $time_format;

	public function __construct() {

		$this->time_format = _x( '%s ms', '%s = time in milliseconds', 'inpsyde' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		$queries     = $this->get_data();
		$query_count = count( $queries );
		$msg         = __( 'SQL-Queries <span>%d</span>', 'inpsyde' );
		$msg         = sprintf( $msg, $query_count );

		return $msg;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		if ( ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) {

			$msg               = __( 'Query-logging is currently disabled. Please add %1$s to your %2$s-file to enable the query-collector',
			                         'inpsyde' );
			$save_query_string = '<code>define( "SAVEQUERIES", TRUE );</code>';
			$wp_config_string  = '<code>wp-config.php</code>';
			echo '<p><br>' . sprintf( $msg, $save_query_string, $wp_config_string ) . '</p>';

			return;
		}

		$data        = $this->get_data();
		$query_count = count( $data );
		$total_time  = 0;
		foreach ( $data as $query ) {
			$total_time = $total_time + $query[ 'time' ];
		}
		$avg_time = number_format_i18n( $total_time / $query_count, 4 );
		?>

		<h2><?php _e( 'Timing', 'inpsyde' ); ?></h2>
		<p>
			<strong><?php _e( 'Total Time:', 'inpsyde' ); ?></strong>
			<?php printf( $this->time_format, $total_time ); ?>
		</p>

		<p>
			<strong><?php _e( 'Average Time:', 'inpsyde' ); ?></strong>
			<?php printf( $this->time_format, $avg_time ); ?>
		</p>

		<h2><?php _e( 'Queries', 'inpsyde' ); ?></h2>
		<?php foreach ( $data as $query ) : ?>
			<table class="inpsyde-debugbar-log">
				<thead>
				<tr>
					<th>
						<?php if ( $query[ 'time' ] >= $this->slow_query_time ) : ?>
							<span class="inpsyde-debugbar-log-level inpsyde-debugbar-log-level--550"></span>
						<?php endif; ?>
						<?php echo $query[ 'caller' ]; ?>
					</th>
					<td><?php echo $query[ 'time' ]; ?></td>
					<td><a href="#" class="inpsyde-debugbar-log__detail-link"><?php _e( 'Details', 'inpsyde' ); ?></a></td>
				</tr>
				</thead>
				<tbody class="inpsyde-debugbar-log__detail-content inpsyde-debugbar-log__detail-content--is-hidden">
				<tr>
					<td colspan="2">
						<h3><?php _e( 'Query', 'inpsyde' ); ?></h3>
						<?php echo \SqlFormatter::format( $query[ 'query' ] ); ?>
					</td>
					<td>
						<h3><?php _e( 'Stack', 'inpsyde' ); ?></h3>
						<p>
							<?php foreach ( $query[ 'stack' ] as $stack ) : ?>
								<?php echo print_r( $stack, TRUE ) . '<br>'; ?>
							<?php endforeach; ?>
						</p>
					</td>
				</tr>
				</tbody>
			</table>
		<?php endforeach; ?>
	<?php
	}

	/**
	 * Returns the executed WordPress SQL-Queries.
	 *
	 * @return array
	 */
	private function get_data() {

		global $wpdb;

		if ( ! is_array( $wpdb->queries ) ) {
			return array();
		}

		$content = array();

		foreach ( $wpdb->queries as $query ) {
			$stack = explode( ', ', $query[ 2 ] );
			$time = number_format_i18n( $query[ 1 ], 5 );

			$content[ ] = array(
				'caller' => array_pop( $stack ),
				'query'  => $query[ 0 ],
				'stack'  => $stack,
				'time'   => sprintf( $this->time_format, $time ),
			);
		}

		return $content;
	}

}
