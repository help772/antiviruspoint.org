<?php

class WC_Deactivation_Request extends WC_Software_API_Request {
	public function do_request() {
		global $wc_software;

		$required = array( 'email', 'license_key', 'product_id' );
		$this->check_required( $required );

		$input = $this->check_input( array( 'email', 'license_key', 'product_id', 'platform', 'instance', 'activation_id' ) );

		// Validate email
		if ( ! is_email( $input['email'] ) ) {
			$this->wc_software_api->error( '100', __( 'The email provided is invalid', 'woocommerce-software-add-on' ), null, array( 'reset' => false ) );
		}

		$data = $wc_software->get_license_key( $input['license_key'], $input['product_id'], $input['email'] );

		if ( ! $data ) {
			$this->wc_software_api->error( '101', __( 'No matching license key exists', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
		}

		// reset number of activations
		$is_deactivated = $wc_software->deactivate_license_key( $data->key_id, $input['instance'], $input['activation_id'] );

		if ( ! $is_deactivated ) {
			$this->wc_software_api->error( '104', __( 'No matching instance exists', 'woocommerce-software-add-on' ), null, array( 'activated' => false ) );
		}

		$output_data              = get_object_vars( $data );
		$output_data['reset']     = true;
		$output_data['timestamp'] = time();
		$to_output                = array();
		$to_output['reset']       = 'reset';
		$to_output['timestamp']   = 'timestamp';
		return $this->prepare_output( $to_output, $output_data );
	}
}
