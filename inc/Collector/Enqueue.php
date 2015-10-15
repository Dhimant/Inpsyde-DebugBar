<?php
namespace Inpsyde\DebugBar\Collector;

class Enqueue implements CollectorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		return __( 'Scripts & Styles', 'inpsyde' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		$this->render_styles();
		$this->render_scripts();

	}

	private function render_scripts() {

		global $wp_scripts;

		$wp_scripts->all_deps( $wp_scripts->queue );
		$loaded_scripts = $wp_scripts->to_do;
		?>

		<h2><?php _e( 'Enqueued Scripts', 'inpsyde' ); ?></h2>
		<table>
			<thead>
			<?php $this->the_table_head(); ?>
			</thead>
			<tbody>
			<?php
			$i = 1;
			foreach ( $loaded_scripts as $script ) :
				$script = $wp_scripts->registered[ $script ]; ?>

				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo esc_attr( $script->handle ); ?></td>
					<td>
						<?php
						if ( count( $script->deps ) > 0 ) :
							echo implode( ', ', $script->deps );
						endif;
						?>
					</td>
					<td><code><?php echo $script->src; ?></code></td>
				</tr>

				<?php
				$i ++;
			endforeach;
			?>
			</tbody>
		</table>

		<?php
	}

	private function render_styles() {

		global $wp_styles;

		$loaded_styles = $wp_styles->do_items();
		?>
		<h2><?php _e( 'Enqueued Styles', 'inpsyde' ); ?></h2>
		<table class="tablesorter">
			<?php $this->the_table_head(); ?>
			<tbody>
			<?php
			$i = 1;
			foreach ( $loaded_styles as $style ) :
				$style = $wp_styles->registered[ $style ];
				?>

				<tr>
					<td><?php echo $i; ?></td>
					<td><?php esc_attr_e( $style->handle ); ?></td>
					<td>
						<?php
						if ( count( $style->deps ) > 0 ) :
							echo implode( ', ', $style->deps );
						endif;
						?>
					</td>
					<td><code><?php echo $style->src; ?></code></td>
				</tr>
				<?php
				$i ++;
			endforeach;
			?>
			</tbody>
		</table>
		<?php
	}

	private function the_table_head() {

		?>
		<tr>
			<th><?php _e( 'Order', 'inpsyde' ); ?></th>
			<th><?php _e( 'Loaded', 'inpsyde' ); ?></th>
			<th><?php _e( 'Dependencies', 'inpsyde' ); ?></th>
			<th><?php _e( 'Path', 'inpsyde' ); ?></th>
		</tr>
		<?php
	}
}
