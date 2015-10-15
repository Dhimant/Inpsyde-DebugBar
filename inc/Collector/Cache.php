<?php
namespace Inpsyde\DebugBar\Collector;

class Cache implements CollectorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		$cache_data  = $this->get_data();
		$cache_count = count( $cache_data );
		$msg         = __( 'WordPress Cache <span>%d</span>', 'inpsyde' );
		$msg         = sprintf( $msg, $cache_count );

		return $msg;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {
		global $wp_object_cache;

		$data = $this->get_data();
		?>

		<h2><?php _e( 'Cache Info', 'inpsyde' ); ?></h2>
		<p><strong><?php _e( 'Cache Hits:', 'inpsyde' ); ?></strong> <?php echo $wp_object_cache->cache_hits; ?></p>
		<p><strong><?php _e( 'Cache Misses:', 'inpsyde' ); ?></strong> <?php echo $wp_object_cache->cache_misses; ?></p>

		<h2><?php _e( 'Cache Data', 'inpsyde' ); ?></h2>
		<?php foreach ( $data as $group => $values ) : ?>
			<h3><?php echo $group; ?> - <?php echo $this->get_array_size( $values ); ?>kb</h3>
			<?php $this->render_details( $values ); ?>
		<?php endforeach; 
	}

	/**
	 * returns for a given array the size in kb.
	 *
	 * @param   array $args
	 * @return  string size in k
	 */
	private function get_array_size( $args ) {
		$cache_size = serialize( $args );
		$cache_size = strlen( $cache_size ) / 1024;

		return number_format_i18n( $cache_size, 2 );
	}

	/**
	 * @return array
	 */
	private function get_data(){
		global $wp_object_cache;

		return $wp_object_cache->cache;
	}

	/**
	 * Rendered details table of cache data.
	 *
	 * @param   array $values
	 *
	 * @return  void
	 */
	private function render_details( $values ) {

		foreach ( $values as $group_key => $cache ) : ?>
			<table class="inpsyde-debugbar-log">
				<thead>
				<tr>
					<th><?php _e( 'Cache Key: ', 'inpsyde' ); ?></th>
					<td><?php echo $group_key; ?></td>
					<td><a href="#" class="inpsyde-debugbar-log__detail-link">Details</a></td>
				</tr>
				</thead>
				<tbody class="inpsyde-debugbar-log__detail-content inpsyde-debugbar-log__detail-content--is-hidden">
				<tr>
					<td colspan="3">
						<table>
							<?php if ( is_scalar( $cache ) ) :
								echo '<tr><td>' . $cache . '</td></tr>';
							else: ?>
								<?php foreach( $cache as $key => $values ) : ?>
									<tr>
										<th><?php echo $key; ?></th>
										<td>
											<?php if ( is_scalar( $values ) ) :
												echo htmlentities( $values );
											else :
												echo '<pre>' . htmlentities( print_r( $values, true ) ) . '</pre>';
											endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		<?php endforeach;
	}

}
