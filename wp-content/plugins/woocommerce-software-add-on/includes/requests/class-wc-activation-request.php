<?php

/**
 * WC_Activation_Request class.
 *
 * @extends WC_Software_API_Request
 *
 * @todo Email customer upon activation
 */
class WC_Activation_Request extends WC_Software_API_Request {

	/**
	 * do_request function.
	 *
	 * @access public
	 */
	public function do_request() {
		global $wc_software;

		$now = time();

		$this->check_required( array( 'email', 'license_key', 'product_id' ) );

		$input = $this->check_input( array( 'email', 'license_key', 'product_id', 'platform', 'secret_key', 'instance' ) );

		if ( ! isset( $input['instance'] ) || $input['instance'] == '' ) {
			$input['instance'] = $now;
		}

		// Validate email
		if ( ! is_email( $input['email'] ) ) {
			$this->wc_software_api->error( '100', __( 'The email provided is invalid', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
		}

		// Check if the license key is valid for this user and get the key
		$data = $wc_software->get_license_key( $input['license_key'], $input['product_id'], $input['email'] );

		if ( ! $data ) {
			$this->wc_software_api->error( '101', __( 'No matching license key exists', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
		}

		// Validate order if set.
		if ( $data->order_id ) {
			$order = wc_get_order( $data->order_id );
			if ( ! $order->has_status( 'completed' ) ) {
				$this->wc_software_api->error( '102', __( 'The purchase matching this product is not complete', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
			}
		}

		$activation_id         = $wc_software->get_activation_instance_id( $data->key_id, $input['instance'] );
		$existing_activation   = ( $activation_id > 0 );
		$activations_remaining = $wc_software->activations_remaining( $data->key_id );

		// Check remaining activations only if this is new activation.
		if ( ! $existing_activation && ! $activations_remaining ) {
			$this->wc_software_api->error( '103', __( 'Remaining activations is equal to zero', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
		}

		// Activation
		$result = $wc_software->activate_license_key( $data->key_id, $input['instance'], $input['platform'] );

		if ( ! $result ) {
			$this->wc_software_api->error( '104', __( 'Could not activate key', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
		}

		// Check remaining activations
		$activations_remaining = $wc_software->activations_remaining( $data->key_id );

		// Activation was successful - return json
		$output_data = get_object_vars( $data );

		$output_data['activated']  = true;
		$output_data['instance']   = $input['instance'];
		$output_data['message']    = sprintf( __( '%1$s out of %2$s activations remaining', 'woocommerce-software-add-on' ), $activations_remaining, $data->activations_limit );
		$output_data['time']       = $now;
		$output_data['secret_key'] = $input['secret_key'];

		$to_output              = array( 'activated', 'instance' );
		$to_output['message']   = 'message';
		$to_output['timestamp'] = 'time';

		return $this->prepare_output( $to_output, $output_data );
	}

}
