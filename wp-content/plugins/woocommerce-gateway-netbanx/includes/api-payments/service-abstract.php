<?php

namespace WcPaysafe\Api_Payments;

use Paysafe\PaysafeApiClient;
use WcPaysafe\Api_Payments\Config\Configuration_Abstract;
use WcPaysafe\Api_Payments\Config\Redirect;

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
abstract class Service_Abstract {
	
	/**
	 * @var Configuration_Abstract|Redirect
	 */
	protected $config;
	
	/**
	 * Services constructor.
	 *
	 * @param Configuration_Abstract|Redirect $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}
	
	public function get_configuration() {
		return $this->config;
	}
}