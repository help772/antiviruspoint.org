<?php
/**
 * WooCommerce CyberSource
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource\API;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Abstract Request Class
 *
 * Provides functionality common to all requests
 *
 * @since 2.0.0
 */
abstract class Request extends Framework\SV_WC_API_JSON_Request {


	const REQUEST_METHOD_POST = 'POST';

	const REQUEST_METHOD_PUT = 'PUT';

	const REQUEST_METHOD_PATCH = 'PATCH';

	const REQUEST_METHOD_GET = 'GET';

	const REQUEST_METHOD_DELETE = 'DELETE';


	/** @var \WC_Order order associated with the request, if any */
	protected $order;


	/**
	 * Gets an order's address data, formatted for the CyberSource API.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @param string $type address type - billing or shipping
	 * @return array
	 */
	protected function get_order_address_data( \WC_Order $order, $type = 'billing' ) {

		$address = [];
		$fields  = [
			'first_name',
			'last_name',
			'company',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
			'email',
			'phone',
		];

		foreach ( $fields as $field ) {

			$method = "get_{$type}_{$field}";

			if ( is_callable( [ $order, $method ] ) ) {
				$address[ $field ] = $order->$method();
			}
		}

		return $this->get_formatted_address_data( $address );
	}


	/**
	 * Formats a WooCommerce address for the CyberSource API.
	 *
	 * @since 2.0.0
	 *
	 * @param array $address WooCommerce address data
	 * @return array
	 */
	protected function get_formatted_address_data( array $address = [] ) {

		$address = wp_parse_args( $address, [
			'first_name' => '',
			'last_name'  => '',
			'company'  => '',
			'address_1' => '',
			'address_2' => '',
			'city'      => '',
			'state'     => '',
			'postcode'  => '',
			'country'   => '',
			'email'     => '',
			'phone'   => '',
		] );

		if ( 'MX' == mb_strtoupper( $address['country'] ?? '' ) ) {
			$address = $this->map_mexican_state( $address );
		}

		return [
			'firstName'          => Framework\SV_WC_Helper::str_truncate( $address['first_name'], 60, '' ),
			'lastName'           => Framework\SV_WC_Helper::str_truncate( $address['last_name'], 60, '' ),
			'company'            => Framework\SV_WC_Helper::str_truncate( $address['company'], 60, '' ),
			'address1'           => Framework\SV_WC_Helper::str_truncate( $address['address_1'], 60, '' ),
			'address2'           => Framework\SV_WC_Helper::str_truncate( $address['address_2'], 60, '' ),
			'locality'           => Framework\SV_WC_Helper::str_truncate( $address['city'], 50, '' ),
			'administrativeArea' => Framework\SV_WC_Helper::str_truncate( $address['state'], 20, '' ),
			'postalCode'         => Framework\SV_WC_Helper::str_truncate( $address['postcode'], 10, '' ),
			'country'            => Framework\SV_WC_Helper::str_truncate( $address['country'], 3, '' ),
			'email'              => Framework\SV_WC_Helper::str_truncate( $address['email'], 320, '' ),
			'phoneNumber'        => Framework\SV_WC_Helper::str_truncate( preg_replace( '/[^-\d().]/', '', $address['phone'] ), 32, '' ),
		];
	}


	/**
	 * Maps Mexican state codes to ISO 3166-2 codes, as required by CyberSource.
	 *
	 * @since 2.8.3
	 *
	 * @param array<string, string> $address address data
	 * @return array<string, string>
	 */
	protected function map_mexican_state( array $address ) : array {

		$state_code_map = [
			'AG' => 'AGU',
			'BN' => 'BCN',
			'BS' => 'BCS',
			'CP' => 'CAM',
			'CS' => 'CHP',
			'CI' => 'CHH',
			'CH' => 'COA',
			'CL' => 'COL',
			'DF' => 'CMX',
			'DG' => 'DUR',
			'GJ' => 'GUA',
			'GE' => 'GRO',
			'HD' => 'HID',
			'JA' => 'JAL',
			'MX' => 'MEX',
			'MC' => 'MIC',
			'MR' => 'MOR',
			'NA' => 'NAY',
			'NL' => 'NLE',
			'OA' => 'OAX',
			'PU' => 'PUE',
			'QE' => 'QUE',
			'QI' => 'ROO',
			'SL' => 'SLP',
			'SI' => 'SIN',
			'SO' => 'SON',
			'TB' => 'TAB',
			'TA' => 'TAM',
			'TL' => 'TLA',
			'VC' => 'VER',
			'YU' => 'YUC',
			'ZA' => 'ZAC',
		];

		if (! $state_code = mb_strtoupper( $address['state'] ?? '' ) ) {
			return $address;
		}

		$address['state'] = $state_code_map[ $state_code ] ?? $state_code;

		return $address;
	}


	/**
	 * Gets the request data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_data() {

		/**
		 * CyberSource API Request Data.
		 *
		 * Allow actors to modify the request data before it's sent to CyberSource
		 *
		 * @since 2.0.0
		 *
		 * @param array|mixed $data request data to be filtered
		 * @param Request $this, API request class instance
		 */
		$this->data = apply_filters( 'wc_cybersource_api_request_data', $this->data, $this );

		return $this->data;
	}


	/**
	 * Gets the order associated with the request, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Order|null
	 */
	public function get_order() {

		return $this->order;
	}


}
