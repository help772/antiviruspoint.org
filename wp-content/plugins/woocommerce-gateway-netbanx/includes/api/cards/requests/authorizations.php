<?php

namespace WcPaysafe\Api\Cards\Requests;

use Paysafe\CardPayments\Authorization;
use Paysafe\CardPayments\Settlement;
use Paysafe\CardPayments\Verification;
use WcPaysafe\Api\Cards\Responses\Verifications;
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
class Authorizations extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api\Cards\Parameters\Authorizations
	 */
	public function get_request_builder( $source = null ) {
		return new \WcPaysafe\Api\Cards\Parameters\Authorizations( new Card_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $params
	 *
	 * @return \WcPaysafe\Api\Cards\Responses\Authorizations
	 * @throws \Paysafe\PaysafeException
	 */
	public function get( $params ) {
		$result = new \WcPaysafe\Api\Cards\Responses\Authorizations( $this->sdk->cardPaymentService()->getAuth(
			new Authorization( $params )
		) );
		
		return $result;
	}
	
	/**
	 * @param $params
	 *
	 * @return array|\WcPaysafe\Api\Cards\Responses\Authorizations
	 * @throws \Paysafe\PaysafeException
	 */
	public function get_authorizations( $params ) {
		$results = $this->sdk->cardPaymentService()->getAuths(
			new Authorization( $params )
		);
		
		$formatted = array();
		if ( $results ) {
			foreach ( $results->getResults() as $result ) {
				$formatted[] = new \WcPaysafe\Api\Cards\Responses\Authorizations( $result );
			}
		}
		
		return $formatted;
	}
	
	/**
	 * @param $parameters
	 *
	 * @return \WcPaysafe\Api\Cards\Responses\Authorizations
	 * @throws \Paysafe\PaysafeException
	 */
	public function process( $parameters ) {
		$result = new \WcPaysafe\Api\Cards\Responses\Authorizations( $this->sdk->cardPaymentService()->authorize(
			new Authorization( $parameters )
		) );
		
		return $result;
	}
	
	/**
	 * @param $params
	 *
	 * @return Verifications
	 * @throws \Paysafe\PaysafeException
	 */
	public function verify( $params ) {
		$result = new \WcPaysafe\Api\Cards\Responses\Verifications( $this->sdk->cardPaymentService()->verify(
			new Verification( $params )
		) );
		
		return $result;
	}
}