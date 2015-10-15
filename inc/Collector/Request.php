<?php

namespace Inpsyde\DebugBar\Collector;

class Request implements CollectorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		// Unknown, but we have a queried object
		$message = __( 'Unknown queried object', 'inpsyde' );
		$type    = 'unknown';

		$queried_object = get_queried_object();
		switch ( TRUE ) {
			case is_null( $queried_object ):
				// Nada
				break;
			case is_a( $queried_object, 'WP_Post' ):
				// Single post
				$type = 'post';

				$post_type = get_post_type_object( $queried_object->post_type );
				$name      = $post_type->labels->singular_name;

				$message_template = __( 'Single %s: #%d', 'inpsyde' );
				$message          = sprintf( $message_template, $name, $queried_object->ID );
				break;
			case is_a( $queried_object, 'WP_User' ):
				// Author archive
				$type    = 'user';
				$msg     = __( 'Author archive: %s', 'inpsyde' );
				$message = sprintf( $msg, $queried_object->user_nicename );
				break;
			case property_exists( $queried_object, 'term_id' ):
				// Term archive
				$type    = 'term';
				$msg     = __( 'Term archive: %s', 'inpsyde' );
				$message = sprintf( $msg, $queried_object->slug );

				break;
			case property_exists( $queried_object, 'has_archive' ):
				// Post type archive
				$type    = 'archive';
				$msg     = __( 'Post type archive: %s', 'inpsyde' );
				$message = sprintf( $msg, $queried_object->name );
				break;
		}

		$msg = __( 'Request [%s]: <strong>%s</strong>', 'inpsyde' );
		$msg = sprintf( $msg, $type, $message );

		return $msg;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		$data = $this->get_data();
		?>
		<table>
			<?php foreach ( $data as $key => $value ) : ?>
				<?php if ( empty( $value ) ) :
					continue;
				endif; ?>
				<tr>
					<th><?php echo $key; ?></th>
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

	private function get_data() {

		global $wp, $wp_query;

		$data = array();

		if ( is_admin() ) {
			$data[ 'Request' ] = $_SERVER[ 'REQUEST_URI' ];
			foreach ( array( 'query_string' ) as $item ) {
				$data[ $item ] = $wp->$item;
			}
		} else {
			foreach ( array( 'request', 'matched_rule', 'matched_query', 'query_string' ) as $item ) {
				$data[ $item ] = $wp->$item;
			}
		}

		$query_vars = $wp_query->query_vars;
		foreach ( $query_vars as $key => $value ) {
			$data[ $key ] = $value;
		}

		return $data;
	}

}