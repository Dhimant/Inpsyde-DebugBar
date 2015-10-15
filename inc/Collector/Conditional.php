<?php

namespace Inpsyde\DebugBar\Collector;

class Conditional implements CollectorInterface {

	/**
	 * @var array
	 */
	private $conditionals = array(
		'is_404',
		'is_archive',
		'is_admin',
		'is_attachment',
		'is_author',
		'is_blog_admin',
		'is_category',
		'is_comments_popup',
		'is_date',
		'is_day',
		'is_feed',
		'is_front_page',
		'is_home',
		'is_main_network',
		'is_main_site',
		'is_month',
		'is_network_admin',
		'is_page',
		'is_page_template',
		'is_paged',
		'is_post_type_archive',
		'is_preview',
		'is_robots',
		'is_rtl',
		'is_search',
		'is_single',
		'is_singular',
		'is_ssl',
		'is_sticky',
		'is_tag',
		'is_tax',
		'is_time',
		'is_trackback',
		'is_year'
	);

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		return __( 'Conditional', 'inpsyde' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		$data = $this->get_data();
		?>
		<table>
			<?php foreach ( $data as $key => $values ) : ?>
				<?php foreach ( $values as $value ) : ?>
					<tr>
						<th><?php echo $value; ?></th>
						<td><?php echo $key; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</table>
	<?php
	}

	/**
	 * @return array
	 */
	private function get_data() {

		$true  = array();
		$false = array();
		$na    = array();

		foreach ( $this->conditionals as $cond ) {
			if ( function_exists( $cond ) ) {
				if ( ( 'is_sticky' == $cond ) and ! get_post( $id = NULL ) ) {
					# Special case for is_sticky to prevent PHP notices
					$false[ ] = $cond;
				} else if ( ( 'is_main_site' == $cond ) and ! is_multisite() ) {
					# Special case for is_main_site to prevent it from being annoying on single site installs
					$na[ ] = $cond;
				} else {
					if ( call_user_func( $cond ) ) {
						$true[ ] = $cond;
					} else {
						$false[ ] = $cond;
					}
				}
			} else {
				$na[ ] = $cond;
			}
		}

		$data = compact( 'true', 'false', 'na' );

		return $data;
	}

}