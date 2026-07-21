<?php

namespace WcPaysafe\Api\Alternate_Payments\Requests;

use Paysafe\AlternatePayments\Payment;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Request_Abstract;
use WcPaysafe\Api\Request_Fields\Alternate_Payments_Fields;

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
class Payments extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api\Alternate_Payments\Parameters\Payments
	 */
	public function get_request_builder( $source ) {
		return new \WcPaysafe\Api\Alternate_Payments\Parameters\Payments( new Alternate_Payments_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $parameters
	 *
	 * @return \WcPaysafe\Api\Alternate_Payments\Responses\Payments
	 * @throws \Paysafe\PaysafeException
	 */
	public function process( $parameters ) {
		$result = new \WcPaysafe\Api\Alternate_Payments\Responses\Payments( $this->sdk->alternatePaymentService()->createPayment(
			new Payment( $parameters )
		) );
		
		return $result;
	}
	
	/**
	 * @param $parameters
	 *
	 * @return \WcPaysafe\Api\Alternate_Payments\Responses\Payments
	 * @throws \Paysafe\PaysafeException
	 */
	public function get( $parameters ) {
		$result = new \WcPaysafe\Api\Alternate_Payments\Responses\Payments( $this->sdk->alternatePaymentService()->getPayment(
			new Payment( $parameters )
		) );
		
		return $result;
	}
	
	/**
	 * @param $parameters
	 *
	 * @return \Paysafe\AlternatePayments\Pagerator
	 * @throws \Paysafe\PaysafeException
	 */
	public function get_all_by_ref_number( $parameters ) {
		$result = $this->sdk->alternatePaymentService()->getPaymentsByMerchantRefNumber(
			new Payment( $parameters )
		);
		
		return $result;
	}
}