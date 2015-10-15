<?php
namespace Inpsyde\DebugBar\Collector;

class Transient implements CollectorInterface {

	/**
	 * @var array
	 */
	private $data = array();

	public function __construct() {

		add_action( 'setted_site_transient', array( $this, 'collect_site_transients' ), 10, 3 );
		add_action( 'setted_transient', array( $this, 'collect_blog_transients' ), 10, 3 );
	}

	/**
	 * @wp-hook setted_site_transient
	 *
	 * @param   string $transient
	 * @param   string $values
	 * @param   int $expiration
	 *
	 * @return void
	 */
	public function collect_site_transients( $transient, $values = NULL, $expiration = NULL ) {
		$this->collect( $transient, 'site', $values, $expiration );
	}

	/**
	 * @wp-hook setted_transient
	 *
	 * @param   string $transient
	 * @param   string $values
	 * @param   int $expiration
	 *
	 * @return void
	 */
	public function collect_blog_transients( $transient, $values = NULL, $expiration = NULL ) {
		$this->collect( $transient, 'blog', $values, $expiration );
	}

	/**
	 *
	 * @param   string $transient
	 * @param   string $type
	 * @param   string $values
	 * @param   int $expiration
	 *
	 * @return void
	 */
	private function collect( $transient, $type, $values = NULL, $expiration = NULL ) {

		$this->data[ ] = array(
			'transient'  => $transient,
			'type'       => $type,
			'values'     => $values,
			'expiration' => $expiration,
		);

	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		$transients      = $this->get_data();
		$transient_count = count( $transients );

		$msg = __( 'Transient <span>%d</span>', 'inpsyde' );
		$msg = sprintf( $msg, $transient_count );

		return $msg;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		$data = $this->get_data();

		if ( empty ( $data ) ) :
			echo '<p><br>' . __( 'No transients running currently', 'inpsyde' ) . '</p>';
			return;
		endif;
		?>
		<table>
			<thead>
				<tr>
					<th><?php _e( 'Type', 'inpsyde' ); ?></th>
					<th><?php _e( 'Transient', 'inpsyde' ); ?></th>
					<th><?php _e( 'Values', 'inpsyde' ); ?></th>
					<th><?php _e( 'Expiration', 'inpsyde' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $data as $value ) : ?>
				<tr>
					<td><?php echo $value[ 'type' ]; ?></td>
					<td><?php echo $value[ 'transient' ]; ?></td>
					<td><?php print_r( $value[ 'values' ] ); ?></td>
					<td><?php echo $value[ 'expiration' ]; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php
	}

	/**
	 * @return array
	 */
	private function get_data() {

		return $this->data;
	}

}