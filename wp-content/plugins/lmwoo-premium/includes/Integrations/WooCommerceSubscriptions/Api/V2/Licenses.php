<?php

namespace LicenseManagerForWooCommerce\Integrations\WooCommerceSubscriptions\Api\V2;

use WC_Subscription;

defined( 'ABSPATH' ) || exit;

class Licenses {

	public function __construct() {
		add_filter( 'lmfwc_rest_api_pre_response', array( $this, 'addSubscriptionId' ), 10, 3 );
	}

	public function addSubscriptionId( $data, $request_method, $route ) {
		$routes = array(
			'v2/licenses',
			'v2/licenses/{license_key}',
			'v2/licenses/activate/{license_key}',
			'v2/licenses/deactivate/{license_key}',
			'v2/licenses/validate/{license_key}',
		);

		if ( ! in_array( $route, $routes ) ) {
			return $data;
		}

		switch ( true ) {
			case ( 'v2/licenses/{license_key}' == $route && 'GET' === $request_method ):
			case ( 'v2/licenses' == $route && 'POST' === $request_method ):
			case ( 'v2/licenses/{license_key}' == $route && 'PUT' === $request_method ):
			case ( 'v2/licenses/activate/{license_key}' == $route && 'GET' === $request_method ):
			case ( 'v2/licenses/deactivate/{license_key}' == $route && 'GET' === $request_method ):
			case ( 'v2/licenses/validate/{license_key}' == $route && 'GET' === $request_method ):
				$data['subscriptionIds'] = array();

				if ( ! isset( $data['orderId'] ) ) {
					return $data;
				}

				$data['subscriptionIds'] = $this->getOrderSubscriptionIds( (int) $data['orderId'] );
				break;
			case 'v2/licenses' == $route && 'GET' === $request_method:
				foreach ( $data as $key => $license ) {
					if ( ! isset( $license['orderId'] ) ) {
						continue;
					}
					$data[$key]['subscriptionIds'] = $this->getOrderSubscriptionIds( $license['orderId'] );
				}
				break;
		}

	
		return $data;
	}

	private function getOrderSubscriptionIds( $order_id ) {
		$subscriptionIds = array();

		$subscriptions = wcs_get_subscriptions_for_order( $order_id );
		if ( ! $subscriptions || empty( $subscriptions ) ) {
			return $subscriptionIds;
		}
		
		foreach ( $subscriptions as $i => $subscription ) {
			$subscriptionIds[] = $subscription->get_id();
		
		}
		return $subscriptionIds;
	}
}
