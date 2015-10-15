<?php
namespace Inpsyde\DebugBar\Collector;

class Cookie implements CollectorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		return 'Cookies';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {
		?>
		<table>
			<?php foreach ( $_COOKIE as $key => $value ) : ?>
				<tr>
					<th><?php echo $key; ?></th>
					<td><?php echo $value; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php
	}

}