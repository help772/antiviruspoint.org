<?php

namespace WcPaysafe\Api_Payments\Customers\Parameters;

use WcPaysafe\Api_Payments\Config\Redirect;
use WcPaysafe\Api_Payments\Parameters_Abstract;
use WcPaysafe\Api_Payments\Request_Fields\Card_Fields;
use WcPaysafe\Paysafe_Customer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since  4.0.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2024 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Customers_Single_Use_Tokens extends Parameters_Abstract {
	
	/**
	 * Get single-use token parameters
	 *
	 * @return array
	 */
	public function get_single_use_token_parameters($payment_type = 'CARD') {
		/**
		 * @var \WC_Order|\WP_User $source
		 * @var Card_Fields        $fields
		 * @var Redirect           $configuration
		 */
		$fields        = $this->get_fields();
		$source        = $fields->get_source();
		
		$paysafe_customer = new Paysafe_Customer($source->get_user());
		
		$params = array(
			'merchantRefNum' => $paysafe_customer->get_payments_merchant_customer_id(),
		);
		
		
		return $params;
	}
}