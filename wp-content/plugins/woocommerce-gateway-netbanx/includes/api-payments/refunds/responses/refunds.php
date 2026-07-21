<?php

namespace WcPaysafe\Api_Payments\Refunds\Responses;

use WcPaysafe\Api_Payments\Response_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Refunds extends Response_Abstract {
	
	/**
	 * Response_Abstract constructor.
	 *
	 * @param string $response
	 */
	public function __construct( $response ) {
		parent::__construct( $response );
	}
	
	public function get_gateway_reconciliation_id() {
		return $this->data->gatewayReconciliationId;
	}
}