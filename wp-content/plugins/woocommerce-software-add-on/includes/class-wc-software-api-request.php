<?php

class WC_Software_API_Request {
	protected $wc_software_api;

	public function __construct( $wc_software_api ) {
		$this->wc_software_api = $wc_software_api;
	}

	/**
	 * Check requires arguments are set.
	 *
	 * @param  array $required
	 */
	protected function check_required( $required ) {
		$missing = array();

		foreach ( $required as $req ) {
			if ( ! isset( $_REQUEST[ $req ] ) ) {
				// BW compat
				if ( 'license_key' === $req && isset( $_REQUEST['licence_key'] ) ) {
					 $_REQUEST[ $req ] = $_REQUEST['licence_key'];
					 continue;
				}
				$missing[] = $req;
			}
		}

		if ( ! empty( $missing ) ) {
			$this->wc_software_api->error( '100', __( 'The following required information is missing', 'woocommerce-software-add-on' ) . ': ' . implode( ', ', $missing ), null, array( 'activated' => false ) );
		}
	}

	protected function check_input( $input ) {
		$return = array();

		foreach ( $input as $key ) {
			$return[ $key ] = ( isset( $_REQUEST[ $key ] ) ) ? $_REQUEST[ $key ] : '';
		}

		return $return;
	}

	protected function prepare_output( $to_output = array(), $data = array() ) {
		$secret    = ( isset( $data['secret_key'] ) ) ? $data['secret_key'] : 'null';
		$sig_array = array( 'secret' => $secret );

		foreach ( $to_output as $k => $v ) {
			if ( isset( $data[ $v ] ) ) {
				if ( is_string( $k ) ) {
					$output[ $k ] = $data[ $v ];
				} else {
					$output[ $v ] = $data[ $v ];
				}
			}
		}

		$sig_out   = $output;
		$sig_array = array_merge( $sig_array, $sig_out );

		foreach ( $sig_array as $k => $v ) {
			if ( $v === false ) {
				$v = 'false';
			}

			if ( $v === true ) {
				$v = 'true';
			}

			if ( is_array( $v ) ) {
				continue;
			}

			$sigjoined[] = "$k=$v";
		}

		$sig = implode( '&', $sigjoined );

		if ( ! $this->wc_software_api->debug ) {
			$sig = md5( $sig );
		}

		$output['sig'] = $sig;
		return $output;
	}
}
