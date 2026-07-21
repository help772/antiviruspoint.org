<?php

namespace WcPaysafe\Api;

use Paysafe\PaysafeApiClient;
use WcPaysafe\Api\Config\Configuration_Abstract;
use WcPaysafe\Api\Config\Redirect;

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
	 * @var PaysafeApiClient
	 */
	protected $sdk;
	/**
	 * @var Configuration_Abstract|Redirect
	 */
	protected $config;
	
	/**
	 * Services constructor.
	 *
	 * TODO: Should receive the SDK, Configuration and Complex_Fields
	 *
	 * @param PaysafeApiClient                $sdk
	 * @param Configuration_Abstract|Redirect $config
	 */
	public function __construct( $sdk, $config ) {
		$this->sdk    = $sdk;
		$this->config = $config;
	}
	
	/**
	 * @return PaysafeApiClient
	 */
	public function sdk() {
		return $this->sdk;
	}
	
	public function get_configuration() {
		return $this->config;
	}
}