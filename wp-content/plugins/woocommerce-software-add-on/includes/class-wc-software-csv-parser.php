<?php
/**
 * CSV Parser.
 */
class WC_Software_CSV_Parser {

	/**
	 * Parser options.
	 *
	 * @var array
	 */
	protected $options = array(
		'delimeter' => ',',
		'enclosure' => '"',
		'escape'    => '\\',
	);

	/**
	 * Class constructor.
	 *
	 * @param array $options Parser options
	 */
	public function __construct( $options = array() ) {
		$this->options = array_merge( $this->options, $options );
		$this->options = apply_filters( 'woocommerce_software_keys_csv_parser_options', $this->options );
	}

	/**
	 * Parses a CSV file.
	 *
	 * @param string $filepath The CSV filepath
	 *
	 * @return array|WP_Error Returns parsed CSV in array on success, WP_Error
	 *                        on error
	 */
	public function parse( $filepath ) {
		$file_content = '';
		if ( file_exists( $filepath ) ) {
			$file_content = $this->read_file( $filepath );
		} else {
			return new WP_Error(
				'woocommerce_software_keys_csv_parser_invalid_file',
				sprintf( __( 'File <code>%s</code> does not exist', 'woocommerce-software-add-on' ), $filepath )
			);
		}

		if ( ! $file_content ) {
			return new WP_Error(
				'woocommerce_software_keys_csv_parser_no_data',
				sprintf( __( 'No data to parse from <code>%s</code>', 'woocommerce-software-add-on' ), $filepath )
			);
		}

		$data = array();
		$rows = str_getcsv( $file_content, "\n" );
		foreach ( $rows as $row ) {
			// Parses CSV string of a row into an array.
			$data[] = str_getcsv(
				$row,
				$this->options['delimeter'],
				$this->options['enclosure'],
				$this->options['escape']
			);
		}

		return $data;
	}

	/**
	 * Reads CSV file.
	 *
	 * @param string $filepath The CSV filepath
	 *
	 * @return array|WP_Error Return data from file on success, WP_Error on error.
	 */
	protected function read_file( $filepath ) {
		if ( is_readable( $filepath ) ) {
			if ( ! ( $fh = fopen( $filepath, 'r' ) ) ) {
				return new WP_Error(
					'woocommerce_software_keys_csv_parser_fopen_file_failed',
					sprintf( __( 'Unable to open file <code>%s</code>', 'woocommerce-software-add-on' ), $filepath )
				);
			}

			$data = fread( $fh, filesize( $filepath ) );
			fclose( $fh );

			return $data;
		}

		return new WP_Error(
			'woocommerce_software_keys_csv_parser_unreadable_file',
			sprintf( __( 'File <code>%s</code> is not readable', 'woocommerce-software-add-on' ), $filepath )
		);
	}
}
