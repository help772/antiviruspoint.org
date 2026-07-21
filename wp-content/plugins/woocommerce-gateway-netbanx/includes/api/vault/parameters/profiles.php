<?php

namespace WcPaysafe\Api\Vault\Parameters;

use WcPaysafe\Api\Parameters_Abstract;
use WcPaysafe\Helpers\Formatting;
use WcPaysafe\Paysafe_Customer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Profiles extends Parameters_Abstract {
	
	/**
	 * @return array
	 */
	public function get_create_profile_parameters() {
		
		$fields      = $this->get_fields();
		$data_source = $fields->get_source();
		
		$paysafe_customer = new Paysafe_Customer( $data_source->get_user() );
		
		$configuration = $this->get_configuration();
		
		$params = array(
			'merchantCustomerId' => $paysafe_customer->get_merchant_customer_id( $configuration->get_user_prefix() ),
			'locale'             => $configuration->get_locale(),
			'firstName'          => $fields->get_billing_first_name(),
			'lastName'           => $fields->get_billing_last_name(),
			'email'              => $fields->get_billing_email(),
			'phone'              => Formatting::format_string( $fields->get_billing_phone(), 40 ),
		);
		
		if ( $configuration->send_customer_ip() ) {
			$params['ip'] = $configuration->get_user_ip_addr();
		}
		
		$params = apply_filters( 'wc_paysafe_create_profile_parameters', $params, $data_source );
		
		wc_paysafe_add_debug_log( 'Vault create profile parameters: ' . print_r( $params, true ) );
		
		return $params;
	}
}