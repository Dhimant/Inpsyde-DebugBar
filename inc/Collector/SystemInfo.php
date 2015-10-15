<?php

namespace Inpsyde\DebugBar\Collector;

class SystemInfo implements CollectorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {

		return 'System-Info';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render() {

		$data = $this->get_data();
		?>
		<?php foreach ( $data as $name => $values ) : ?>
			<?php if ( empty( $values ) ) :
				continue;
			endif; ?>
			<h2><?php echo $name; ?></h2>
			<?php foreach ( $values as $key => $value ) : ?>
				<table>
					<tr>
						<th><?php echo $key; ?></th>
						<td>
							<?php
							if ( is_array( $value ) ) :
								foreach ( $value as $v ) :
									print_r( $v );
									echo '<br>';
								endforeach;
							else :
								print_r( $value );
							endif;
							?>
						</td>
					</tr>
				</table>
			<?php endforeach; ?>
		<?php endforeach;
	}

	/**
	 * Returns the WordPress-System information with installed plugins, themes
	 *
	 * @return array $system_info
	 */
	private function get_data() {

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$system_info = array();

		// WordPress information
		$system_info[ 'WordPress' ] = $this->get_wordpress_data();

		// WP constants
		$key                 = __( 'WP constants', 'inpsyde' );
		$system_info[ $key ] = $this->get_constants();

		// theme data
		$system_info[ 'Theme' ] = $this->get_theme_data();

		// PHP information
		$system_info[ 'PHP' ] = $this->get_php_data();

		// Server information
		$key                 = __( 'Server', 'inpsyde' );
		$system_info[ $key ] = $this->get_server_data();

		return $system_info;
	}

	/**
	 * @return array
	 */
	private function get_wordpress_data() {

		return array(
			'version'                => get_bloginfo( 'version' ),
			'multisite'              => $this->format_bool( is_multisite() ),
			'memory limit'           => ( $this->let_to_num( WP_MEMORY_LIMIT ) / 1024 ) . ' MB',
			'active plugins'         => implode( ', ', $this->get_plugins() ),
			'network active plugins' => $this->get_network_plugins(),
			'registered post types'  => implode( ', ', get_post_types( array( 'public' => TRUE ) ) ),
		);
	}

	/**
	 * @return array
	 */
	private function get_constants() {

		return array(
			'WP_DEBUG'            => $this->format_bool_constant( 'WP_DEBUG' ),
			'WP_DEBUG_DISPLAY'    => $this->format_bool_constant( 'WP_DEBUG_DISPLAY' ),
			'WP_DEBUG_LOG'        => $this->format_bool_constant( 'WP_DEBUG_LOG' ),
			'SCRIPT_DEBUG'        => $this->format_bool_constant( 'SCRIPT_DEBUG' ),
			'CONCATENATE_SCRIPTS' => $this->format_bool_constant( 'CONCATENATE_SCRIPTS' ),
			'COMPRESS_SCRIPTS'    => $this->format_bool_constant( 'COMPRESS_SCRIPTS' ),
			'COMPRESS_CSS'        => $this->format_bool_constant( 'COMPRESS_CSS' ),
			'WP_LOCAL_DEV'        => $this->format_bool_constant( 'WP_LOCAL_DEV' ),
		);
	}

	/**
	 * @return array
	 */
	private function get_theme_data() {

		$theme_data = wp_get_theme();

		return array(
			'name'       => $theme_data->get( 'Name' ),
			'version'    => $theme_data->get( 'Version' ),
			'author'     => $theme_data->get( 'Author' ),
			'textdomain' => $theme_data->get( 'TextDomain' ),
		);
	}

	/**
	 * @return array
	 */
	private function get_php_data() {

		return array(
			'version'      => PHP_VERSION,
			'memory limit' => ini_get( 'memory_limit' ),
			'modules'      => implode( ', ', get_loaded_extensions() ),
		);
	}

	/**
	 * @return array
	 */
	private function get_server_data() {

		$server = explode( ' ', $_SERVER[ 'SERVER_SOFTWARE' ] );
		$server = explode( '/', reset( $server ) );
		if ( isset( $server[ 1 ] ) ) {
			$server_version = $server[ 1 ];
		} else {
			$server_version = NULL;
		}

		if ( isset( $_SERVER[ 'SERVER_ADDR' ] ) ) {
			$address = $_SERVER[ 'SERVER_ADDR' ];
		} else {
			$address = NULL;
		}

		return array(
			'name'         => $server[ 0 ],
			'version'      => $server_version,
			'address'      => $address,
			'host'         => php_uname( 'n' ),
			'Current User' => $this->get_current_user()
		);
	}

	/**
	 * @return array
	 */
	private function get_plugins() {

		$installed_plugins = get_plugins();
		$active_plugins    = get_option( 'active_plugins', array() );
		$plugins           = array();
		foreach ( $installed_plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}
			array_push( $plugins, $plugin[ 'Name' ] . ' ' . $plugin[ 'Version' ] );
		}

		return $plugins;
	}

	/**
	 * @return array
	 */
	private function get_network_plugins() {

		$data = array();
		if ( ! is_multisite() ) {
			return $data;
		}

		$network_plugins        = wp_get_active_network_plugins();
		$active_network_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach ( $network_plugins as $plugin_path ) {
			$plugin_base    = plugin_basename( $plugin_path );
			$network_plugin = get_plugin_data( $plugin_path );
			if ( ! array_key_exists( $plugin_base, $active_network_plugins ) ) {
				continue;
			}
			$data[ ] = $network_plugin[ 'Name' ] . ' ' . $network_plugin[ 'Version' ];
		}

		return $data;
	}

	/**
	 * @return string
	 */
	private function get_current_user() {

		$php_u = NULL;
		if ( function_exists( 'posix_getpwuid' ) ) {
			$u     = posix_getpwuid( posix_getuid() );
			$g     = posix_getgrgid( $u[ 'gid' ] );
			$php_u = $u[ 'name' ] . ':' . $g[ 'name' ];
		}
		if ( empty( $php_u ) and isset( $_ENV[ 'APACHE_RUN_USER' ] ) ) {
			$php_u = $_ENV[ 'APACHE_RUN_USER' ];
			if ( isset( $_ENV[ 'APACHE_RUN_GROUP' ] ) ) {
				$php_u .= ':' . $_ENV[ 'APACHE_RUN_GROUP' ];
			}
		}
		if ( empty( $php_u ) and isset( $_SERVER[ 'USER' ] ) ) {
			$php_u = $_SERVER[ 'USER' ];
		}
		if ( empty( $php_u ) and function_exists( 'exec' ) ) {
			$php_u = exec( 'whoami' );
		}
		if ( empty( $php_u ) and function_exists( 'getenv' ) ) {
			$php_u = getenv( 'USERNAME' );
		}

		return $php_u;

	}

	/**
	 * @param   string $constant
	 *
	 * @return  string
	 */
	private function format_bool_constant( $constant ) {

		$value = defined( $constant ) && $constant;

		return $this->format_bool( $value );
	}

	/**
	 * @param   mixed $var
	 *
	 * @return  string
	 */
	private function format_bool( $var ) {

		$output = __( 'no', 'inpsyde' );
		if ( (bool) $var ) {
			$output = __( 'yes', 'inpsyde' );
		}

		return $output;
	}

	/**
	 * Convert sizes.
	 *
	 * @param   int $v
	 *
	 * @return  int|string
	 */
	private function let_to_num( $v ) {
		$l   = substr( $v, - 1 );
		$ret = substr( $v, 0, - 1 );
		switch ( strtoupper( $l ) ) {
			case 'P': // fall-through
			case 'T': // fall-through
			case 'G': // fall-through
			case 'M': // fall-through
			case 'K': // fall-through
				$ret *= 1024;
				break;
			default:
				break;
		}

		return $ret;
	}

}