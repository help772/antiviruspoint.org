<?php

namespace WcPaysafe\Api\Alternate_Payments\Requests;

use Paysafe\AlternatePayments\Settlement;
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
class Settlements extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api\Alternate_Payments\Parameters\Settlements
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api\Alternate_Payments\Parameters\Settlements( new Alternate_Payments_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Alternate_Payments\Responses\Settlements
	 * @throws \Paysafe\PaysafeException
	 */
	public function process( $params ) {
		$result = new \WcPaysafe\Api\Alternate_Payments\Responses\Settlements( $this->service->sdk()->alternatePaymentService()->settlement(
			new Settlement( $params )
		) );
		
		return $result;
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Alternate_Payments\Responses\Settlements
	 * @throws \Paysafe\PaysafeException
	 */
	public function get( $params ) {
		$result = new \WcPaysafe\Api\Alternate_Payments\Responses\Settlements( $this->service->sdk()->alternatePaymentService()->getSettlement(
			new Settlement( $params )
		) );
		
		return $result;
	}
}