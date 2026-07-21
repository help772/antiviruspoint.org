<?php

namespace LicenseManagerForWooCommerce\Api\V2;

use Exception;
use LicenseManagerForWooCommerce\Abstracts\RestController as LMFWC_REST_Controller;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class Customers extends LMFWC_REST_Controller {

	protected $namespace = 'lmfwc/v2';


	protected $rest_base = '/customers';

	
	protected $settings = array();

	
	public function __construct() {
		$this->settings = get_option( 'lmfwc_settings_general', array() );
	}

	
	public function register_routes() {
	
		register_rest_route(
			$this->namespace,
			$this->rest_base . '/(?P<customer_id>[\d]+)/licenses',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'getLicenses' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'customer_id' => array(
							'description' => 'Unique identifier of the customer',
							'type'        => 'integer',
						),
					),
				),
			)
		);
	}


	public function getLicenses( WP_REST_Request $request ) {
		if ( ! $this->isRouteEnabled( $this->settings, '023' ) ) {
			return $this->routeDisabledError();
		}

		if ( ! $this->permissionCheck( 'license', 'read') ) {
			return new WP_Error( 'lmfwc_rest_cannot_view', esc_html__( 'Sorry, you cannot view this resource.', 'license-manager-for-woocommerce' ), array( 'status' => $this->authorizationRequiredCode() ) );
		}

		$customer_id = absint( $request->get_param( 'customer_id' ) );

		if ( empty( $customer_id ) ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'Customer ID invalid.', array( 'status' => 404 ) );
		}

		try {
			$licenses = LicenseResourceRepository::instance()->findAllBy( array( 'user_id' => $customer_id ) );

		} catch ( Exception $e ) {
			return new WP_Error( 'lmfwc_rest_data_error', $e->getMessage(), array( 'status' => 404 ) );
		}

		if ( null === $licenses ) {
			return new WP_Error( 'lmfwc_rest_data_error', sprintf( 'License keys could not be found for customer %s.', $customer_id ), array( 'status' => 404 ) );
		}

		$response = array();

		foreach ( $licenses as $license ) {
			$license_data = $license->toArray();
			
			// Ensure expiresAt is properly set before using it
			$expiresAt = $license->getExpiresAt();
		
			if (!empty($expiresAt)) {
				$dateExpiresAt = new \DateTime($expiresAt);
			} else {
				$dateExpiresAt = null; // Handle the case where expiration date is missing
			}
		
			$dateNow = new \DateTime('now', new \DateTimeZone('UTC'));
		
			// Remove the hash, decrypt the license key, and add it to the response
			unset( $license_data['hash'] );
			$license_data['licenseKey'] = $license->getDecryptedLicenseKey();
			
			$license_data['is_expired'] = false;
			$license_data['current_date_time'] = $dateNow->format('Y-m-d H:i:s');
		
			if ($dateExpiresAt && $dateNow > $dateExpiresAt) {
				$license_data['is_expired'] = true;
			}
			$response[] = $license_data;
		}

		return $this->response( true, $response, 200, 'v2/customers/{customer_id}/licenses' );
	}
}
