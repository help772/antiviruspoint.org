<?php
/**
 * WooCoomerce software license keys importer class for managing the import
 * process of a CSV file.
 */
class WC_Software_CSV_Importer extends WP_Importer {

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	protected $plugin_file;

	/**
	 * Plugin directory name.
	 *
	 * @var string
	 */
	protected $plugin_dir;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file Main plugin file
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_dir  = dirname( $this->plugin_file );
	}

	/**
	 * Dispatches incoming request.
	 *
	 * Serves as importer callback.
	 *
	 * @return void
	 */
	public function dispatch() {
		$this->header();

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];
		switch ( $step ) {
			case 0:
				$this->greet();
				break;
			case 1:
				check_admin_referer( 'import-upload' );
				$result = $this->import();
				if ( is_wp_error( $result ) ) {
					$this->print_error_message(
						__( 'Sorry, there has been an error.', 'woocommerce-software-add-on' ),
						$result->get_error_message()
					);
				}
				break;
		}

		$this->footer();
	}

	/**
	 * Imports the CSV.
	 *
	 * @return WP_Error|bool Returns true if import ran successfully, otherwise
	 *                       WP_Error will be returned.
	 */
	public function import() {
		$file = wp_import_handle_upload();
		if ( isset( $file['error'] ) ) {
			return new WP_Error( 'woocommerce_software_keys_csv_importer_error', $file['error'] );
		}

		$license_keys = $this->retrieve_license_keys( $file['file'] );
		if ( empty( $license_keys ) ) {
			return new WP_Error(
				'woocommerce_software_keys_csv_importer_error',
				__( 'Could not found valid data from the file to import.', 'woocommerce-software-add-on' )
			);
		}

		$import_result = $this->import_license_keys( $license_keys );
		$this->show_import_result( $import_result );

		wp_import_cleanup( $file['id'] );

		do_action( 'import_done', 'woocommerce_software_keys_csv' );

		return true;
	}

	/**
	 * Retrieves list of license keys from CSV file.
	 *
	 * @param string $filepath The CSV filepath
	 *
	 * @return array License keys
	 */
	protected function retrieve_license_keys( $filepath ) {
		$license_keys = array();

		// Parse the CSV into array.
		require_once $this->plugin_dir . '/includes/class-wc-software-csv-parser.php';
		$parser = new WC_Software_CSV_Parser();
		$data   = $parser->parse( $filepath );

		if ( ! is_array( $data ) ) {
			return $license_keys;
		}

		if ( 1 >= count( $data ) ) {
			return $license_keys;
		}

		$header = array_shift( $data );
		if ( ! $this->is_valid_header( $header ) ) {
			return $license_keys;
		}

		// Map field name with array index in a row array.
		$field_pos = $this->get_field_pos_map( $header );

		foreach ( $data as $row ) {
			$license_key = array();
			foreach ( $field_pos as $field => $pos ) {
				if ( isset( $row[ $pos ] ) ) {
					$license_key[ $field ] = $row[ $pos ];
				}
			}
			$license_keys[] = $license_key;
		}

		return $license_keys;
	}

	/**
	 * Imports given array of license keys into DB.
	 *
	 * @param array $license_keys List of license keys
	 *
	 * @return array Returns array of import result
	 */
	protected function import_license_keys( $license_keys ) {
		global $wc_software;

		$defaults = array(
			'order_id'            => '',
			'activation_email'    => '',
			'license_key'         => '',
			'software_product_id' => '',
			'software_version'    => '',
			'activations_limit'   => '',
			'created'             => current_time( 'mysql' ),
		);

		// Holds the results of import process.
		$result = array(
			'items'    => array(),
			'imported' => 0,
			'skipped'  => 0,
		);

		foreach ( $license_keys as $license_key ) {
			$license_key = array_merge( $defaults, $license_key );
			$product_id  = $this->get_product_id( $license_key['software_product_id'] );
			if ( ! $product_id ) {
				$result['items'][]  = array(
					'product_id' => $product_id,
					'message'    => sprintf( __( 'Product ID %s is not a valid software product', 'woocommerce-software-add-on' ), $product_id ),
				);
				$result['skipped'] += 1;
				continue;
			}

			// Avoid to generate license_key if license_key is not supplied in CSV.
			// It's much better to skip and inform the user that it needs to be set.
			if ( empty( $license_key['license_key'] ) ) {
				$result['items'][]  = array(
					'product_id' => $product_id,
					'message'    => __( 'Missing license_key', 'woocommerce-software-add-on' ),
				);
				$result['skipped'] += 1;
				continue;
			}

			// Make sure email is valid.
			if ( ! is_email( $license_key['activation_email'] ) ) {
				$result['items'][]  = array(
					'product_id' => $product_id,
					'message'    => __( 'Invalid email', 'woocommerce-software-add-on' ),
				);
				$result['skipped'] += 1;
				continue;
			}

			if ( empty( $license_key['software_version'] ) ) {
				$license_key['software_version'] = get_post_meta( $product_id, '_software_version', true );
			}

			if ( empty( $license_key['activations_limit'] ) ) {
				$license_key['activations_limit'] = get_post_meta( $product_id, '_software_activations', true );
			}

			// Import a single license key. No need to prefix provided license_key
			// as user expecation would be to insert license key as it is.
			$key_id = $wc_software->save_license_key( $license_key );

			// License key is successfully inserted into DB.
			$result['items'][]   = array(
				'product_id' => $product_id,
				'key_id'     => $key_id,
				'message'    => sprintf( __( 'License key %1$s is successfully imported into product ID %2$d', 'woocommerce-software-add-on' ), $license_key['license_key'], $product_id ),
			);
			$result['imported'] += 1;
		}

		return $result;
	}

	/**
	 * Shows import result. Displayed after importer finished the job.
	 *
	 * @param array $result Import result's details
	 *
	 * @return void
	 */
	public function show_import_result( $result ) {
		// No imported data. Better to tell it as error and encourage user to retry.
		if ( 0 === $result['imported'] ) {
			$this->print_error_message(
				__( 'No license key being imported.', 'woocommerce-software-add-on' ),
				__( 'See detailed report below.', 'woocommerce-software-add-on' )
			);
		} elseif ( $result['skipped'] > 0 ) {
			$this->print_warning_message(
				sprintf(
					__( '%1$d license key(s) imported and %2$d license keys skipped.', 'woocommerce-software-add-on' ),
					$result['imported'],
					$result['skipped']
				),
				__( 'See detailed report below.', 'woocommerce-software-add-on' )
			);
		} else {
			$this->print_success_message(
				sprintf(
					__( 'All done. <a href="%s"> Have fun!</a>', 'woocommerce-software-add-on' ),
					admin_url( 'admin.php?page=wc_software_keys' )
				),
				'',
				false
			);
		}

		require_once $this->plugin_dir . '/templates/csv-import-result.php';
	}

	/**
	 * Get product ID from given software_product_id.
	 *
	 * @param string $software_product_id Software product ID
	 *
	 * @return mixed Result from `$wpdb->get_var`
	 */
	protected function get_product_id( $software_product_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT post_id FROM {$wpdb->postmeta}
				WHERE meta_key = '_software_product_id'
				AND meta_value = %s LIMIT 1
				",
				$software_product_id
			)
		);
	}

	/**
	 * Check if an array is a valid header. Header is valid if it contains
	 * required fields.
	 *
	 * @param array $header Header fields
	 *
	 * @return bool True if header is a valid header, false if not valid
	 */
	protected function is_valid_header( $header ) {
		if ( ! $header ) {
			return false;
		}

		$required_fields = array( 'software_product_id', 'activation_email', 'license_key' );
		foreach ( $required_fields as $field ) {
			if ( ! in_array( $field, $header ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get a map between field's name and its index position in row array. Passed
	 * header should be checked with `is_valid_header` first.
	 *
	 * @param array $header Valid header fields
	 *
	 * @return array Map of field's name and its index position
	 */
	protected function get_field_pos_map( $header ) {
		// Start all fields with invalid index.
		$fields = array(
			'software_product_id' => -1,
			'activation_email'    => -1,
			'license_key'         => -1,
		);

		// Check optional fields.
		$optional_fields = array( 'order_id', 'software_version', 'activations_limit' );
		foreach ( $optional_fields as $field ) {
			if ( in_array( $field, $header ) ) {
				$fields[ $field ] = -1;
			}
		}

		// Get the index of each field.
		foreach ( $header as $index => $field ) {
			if ( isset( $fields[ $field ] ) ) {
				$fields[ $field ] = $index;
			}
		}

		return $fields;
	}

	/**
	 * Get formatted message.
	 *
	 * @param string $subject     Subject message
	 * @param string $description Description of the message
	 * @param bool   $escape      Whether to escape the subject and description.
	 *                            Default to true.
	 *
	 * @return string
	 */
	public function get_message( $subject, $description = '', $escape = true ) {
		return sprintf(
			'<p><strong>%s</strong><br>%s</p>',
			$escape ? esc_html( $subject ) : $subject,
			$escape ? esc_html( $description ) : $description
		);
	}

	/**
	 * Print error message.
	 *
	 * @param string $subject     Subject message
	 * @param string $description Description of the message
	 * @param bool   $escape      Whether to escape the subject and description.
	 *                            Default to true.
	 *
	 * @return void
	 */
	public function print_error_message( $subject, $description = '', $escape = true ) {
		printf( '<div class="notice error">%s</div>', $this->get_message( $subject, $description, $escape ) );
	}

	/**
	 * Print success message.
	 *
	 * @param string $subject     Subject message
	 * @param string $description Description of the message
	 * @param bool   $escape      Whether to escape the subject and description.
	 *                            Default to true.
	 *
	 * @return void
	 */
	public function print_success_message( $subject, $description = '', $escape = true ) {
		printf( '<div class="notice updated">%s</div>', $this->get_message( $subject, $description, $escape ) );
	}

	/**
	 * Print warning message.
	 *
	 * @param string $subject     Subject message
	 * @param string $description Description of the message
	 * @param bool   $escape      Whether to escape the subject and description.
	 *                            Default to true.
	 *
	 * @return void
	 */
	public function print_warning_message( $subject, $description = '', $escape = true ) {
		printf( '<div class="notice update-nag">%s</div>', $this->get_message( $subject, $description, $escape ) );
	}

	/**
	 * Print the header of importer page.
	 *
	 * @return void
	 */
	public function header() {
		echo '<div class="wrap woocommerce-software-keys-csv-importer">';
		echo '<h2>' . __( 'Import License Keys', 'woocommerce-software-add-on' ) . '</h2>';
	}

	/**
	 * Print the footer of importer page.
	 *
	 * @return void
	 */
	public function footer() {
		echo '</div>';
	}

	/**
	 * Print the greet, which prompts info and upload form, of importer page.
	 *
	 * @return void
	 */
	public function greet() {
		require_once $this->plugin_dir . '/templates/csv-import-greet.php';
	}
}
