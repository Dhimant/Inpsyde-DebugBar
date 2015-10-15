<?php

namespace Inpsyde\DebugBar\Formatter;
use Monolog\Formatter\NormalizerFormatter;

class Html extends NormalizerFormatter {

	/**
	 * Format a set of log records.
	 *
	 * @param array $records A set of records to format
	 *
	 * @return mixed The formatted set of records
	 */
	public function formatBatch( array $records ) {
		$message = '';
		foreach ( $records as $record ) {
			$message .= $this->format( $record );
		}

		return $message;
	}

	/**
	 * Format a log record.
	 *
	 * @param array $record A record to format
	 *
	 * @return mixed The formatted record
	 */
	public function format( array $record ) {

		$output = '';
		$output .= '<table class="inpsyde-debugbar-log">';

		// generate thead of log record
		$output .= $this->add_head_row( (string) $record['message'], $record['level'] );

		// generate tbody of log record with details
		$output .= '<tbody class="inpsyde-debugbar-log__detail-content inpsyde-debugbar-log__detail-content--is-hidden">';
		$output .= '<tr><td colspan="2"><table>';

		if ( ! empty( $record['context'] ) ) {
			foreach ( $record['context'] as $key => $value ) {
				$output .= $this->add_row( $key, $this->convert_to_string( $value ) );
			}
		}

		if ( ! empty( $record['extra'] ) ) {
			foreach ( $record['extra'] as $key => $value ) {
				$output .= $this->add_row( $key, $this->convert_to_string( $value ) );
			}
		}

		$output .= '</td></tr></table>';
		$output .= '</tbody>';
		$output .= '</table>';

		return $output;
	}

	/**
	 * Create the header row for a log record.
	 *
	 * @param string   $message  log message
	 * @param int      $level    log level
	 *
	 * @return string
	 */
	private function add_head_row( $message = '', $level ) {

		$show_details_link = '<a href="#" class="inpsyde-debugbar-log__detail-link">' . __( 'Details', 'inpsyde' ) . '</a>';

		$html = "<thead>
                    <tr>
                        <td><span class=\"inpsyde-debugbar-log-level inpsyde-debugbar-log-level--$level\"></span>$message</td>
                        <td>$show_details_link</td>
                    </tr>
                </thead>";

		return $html;
	}

	/**
	 * Create an HTML table row.
	 *
	 * @param  string $th       Row header content
	 * @param  string $td       Row standard cell content
	 * @param  bool   $escapeTd false if td content must not be HTML escaped
	 *
	 * @return string
	 */
	private function add_row( $th, $td = ' ', $escapeTd = true ) {
		$th = htmlspecialchars( $th, ENT_NOQUOTES, 'UTF-8' );

		if ( $escapeTd ) {
			$td = htmlspecialchars( $td, ENT_NOQUOTES, 'UTF-8' );
		}

		$html = "<tr>";
		$html .= "<th title=\"$th\">$th</th>";
		$html .= "<td>";
		if ( is_scalar( $td ) ) {
			$html .= $td;
		} else {
			$html .= "<pre>" . print_r( $td, true ). "</pre>";
		}
		$html .= "</td>";
		$html .= "</tr>";

		return $html;
	}

	protected function convert_to_string( $data ) {
		if ( null === $data || is_scalar( $data ) ) {
			return (string) $data;
		}

		$data = $this->normalize( $data );
		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		}

		return str_replace( '\\/', '/', json_encode( $data ) );
	}
}