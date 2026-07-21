<?php

namespace WcPaysafe\Api\Cards\Requests;

use Paysafe\CardPayments\Settlement;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Request_Abstract;
use WcPaysafe\Api\Request_Fields\Card_Fields;

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
	 * @return bool|\WcPaysafe\Api\Cards\Parameters\Settlements
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api\Cards\Parameters\Settlements( new Card_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Cards\Responses\Settlements
	 * @throws \Paysafe\PaysafeException
	 */
	public function process( $params ) {
		$result = new \WcPaysafe\Api\Cards\Responses\Settlements( $this->service->sdk()->cardPaymentService()->settlement(
			new Settlement( $params )
		) );
		
		return $result;
	}
	
	/**
	 * @param array $params
	 *
	 * @return \WcPaysafe\Api\Cards\Responses\Settlements
	 * @throws \Paysafe\PaysafeException
	 */
	public function get( $params ) {
		$result = new \WcPaysafe\Api\Cards\Responses\Settlements( $this->service->sdk()->cardPaymentService()->getSettlement(
			new Settlement( $params )
		) );
		
		return $result;
	}
	
	/**
	 * Returns an array of Settlements. To ensure that we can keep the response classes consistent
	 * we elected to use only the settlement classes not the Pagerator class.
	 *
	 * @param $params
	 *
	 * @return array|\WcPaysafe\Api\Cards\Responses\Settlements
	 * @throws \Paysafe\PaysafeException
	 */
	public function get_settlements( $params ) {
		$results = $this->service->sdk()->cardPaymentService()->getSettlements(
			new Settlement( $params )
		);
		
		$formatted = array();
		if ( $results ) {
			foreach ( $results->getResults() as $result ) {
				$formatted = new \WcPaysafe\Api\Cards\Responses\Settlements( $result );
			}
		}
		
		return $formatted;
	}
}