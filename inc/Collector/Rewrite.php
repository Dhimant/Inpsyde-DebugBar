<?php
namespace Inpsyde\DebugBar\Collector;

class Rewrite implements CollectorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		return __( 'Rewrite Rules', 'inpsyde' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		global $wp_rewrite;

		if ( ! is_a( $wp_rewrite, 'WP_Rewrite' ) ) {
			return;
		}

		?>
		<table>
			<?php foreach ( $wp_rewrite as $key => $value ) : ?>
				<tr>
					<th><?php echo $key ?></th>
					<td>
						<?php
						if ( is_array( $value ) ) :
							echo "<pre>" . print_r( $value, TRUE ) . "</pre>";
						else :
							echo $value;
						endif;
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
	}

}
