<?php

/**
 * WC_Check_Request class.
 *
 * @extends WC_Software_API_Request
 */
class WC_Check_Request extends WC_Software_API_Request {

	/**
	 * Function do_request().
	 */
	public function do_request() {
		global $wc_software;

		$this->check_required( array( 'email', 'license_key', 'product_id' ) );

		$input = $this->check_input( array( 'email', 'license_key', 'product_id' ) );

		// Validate email.
		if ( ! is_email( $input['email'] ) ) {
			$this->wc_software_api->error( '100', __( 'The email provided is invalid', 'woocommerce-software-add-on' ), null, array( 'success' => false ) );
		}

		// Check if the license key is valid for this user and get the key.
		$data = $wc_software->get_license_key( $input['license_key'], $input['product_id'], $input['email'] );

		if ( ! $data ) {
			$this->wc_software_api->error( '101', __( 'No matching license key exists', 'woocommerce-software-add-on' ), null, array( 'success' => false ) );
		}

		// Validate order if set.
		if ( $data->order_id ) {
			$order        = wc_get_order( $data->order_id );
			$order_status = $order->get_status();
			$order_status = 'wc-' === substr( $order_status, 0, 3 ) ? substr( $order_status, 3 ) : $order_status;
			if ( 'completed' !== $order_status ) {
				$this->wc_software_api->error( '102', __( 'The purchase matching this product is not complete', 'woocommerce-software-add-on' ), null, array( 'success' => false ) );
			}
		}

		// Check was successful - return json.
		$output_data = get_object_vars( $data );

		$activations_rows = $wc_software->get_license_activations( $input['license_key'] );
		$activations      = array();
		foreach ( $activations_rows as $row ) {
			if ( ! $row->activation_active ) {
				continue;
			}

			$activations[] = array(
				'activation_id'       => $row->activation_id,
				'instance'            => $row->instance,
				'activation_platform' => $row->activation_platform,
				'activation_time'     => $row->activation_time,
			);
		}

		$output_data['success']     = true;
		$output_data['time']        = time();
		$output_data['remaining']   = $wc_software->activations_remaining( $data->key_id );
		$output_data['activations'] = $activations;

		$to_output                = array( 'success' );
		$to_output['message']     = 'message';
		$to_output['timestamp']   = 'time';
		$to_output['remaining']   = 'remaining';
		$to_output['activations'] = 'activations';

		return $this->prepare_output( $to_output, $output_data );
	}

}
