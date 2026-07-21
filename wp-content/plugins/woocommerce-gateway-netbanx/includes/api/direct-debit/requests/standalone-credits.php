<?php

namespace WcPaysafe\Api\Direct_Debit\Requests;

use Paysafe\DirectDebit\Purchase;
use Paysafe\DirectDebit\StandaloneCredits;
use WcPaysafe\Api\Data_Sources\Data_Source_Interface;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;
use WcPaysafe\Api\Request_Abstract;
use WcPaysafe\Api\Request_Fields\Direct_Debit_Fields;

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
class Standalone_Credits extends Request_Abstract {
	
	/**
	 * @param Order_Source|User_Source|Data_Source_Interface $source
	 *
	 * @return bool|\WcPaysafe\Api\Direct_Debit\Parameters\Standalone_Credits
	 */
	public function get_request_builder( $source ) {
		return new \WcPaysafe\Api\Direct_Debit\Parameters\Standalone_Credits( new Direct_Debit_Fields( $source ), $this->get_configuration() );
	}
	
	/**
	 * @param $parameters
	 *
	 * @return \WcPaysafe\Api\Direct_Debit\Responses\Standalone_Credits
	 * @throws \Paysafe\PaysafeException
	 */
	public function process( $parameters ) {
		$result = new \WcPaysafe\Api\Direct_Debit\Responses\Standalone_Credits( $this->sdk->directDebitService()->standaloneCredits(
			new StandaloneCredits( $parameters )
		) );
		
		return $result;
	}
}