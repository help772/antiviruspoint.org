<?php

namespace WcPaysafe\Api;

use WcPaysafe\Api\Config\Direct;
use WcPaysafe\Api\Config\Redirect;
use WcPaysafe\Api\Data_Sources\Order_Source;
use WcPaysafe\Api\Data_Sources\User_Source;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to bootstrap the requests. It should only format the data presented to the requests.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class Request_Abstract {
	
	/**
	 * @var
	 */
	protected $sdk;
	protected $service;
	/**
	 * @var Redirect|Direct
	 */
	protected $configuration;
	
	/**
	 * Authorizations constructor.
	 *
	 * @param \WcPaysafe\Api\Service_Interface $service
	 */
	public function __construct( Service_Interface $service ) {
		$this->service       = $service;
		$this->sdk           = $this->service->sdk();
		$this->configuration = $this->service->get_configuration();
	}
	
	public function sdk() {
		return $this->sdk;
	}
	
	/**
	 * Method needs to be extended by the child classes
	 *
	 * @param Order_Source|User_Source $source
	 *
	 * @return bool
	 */
	public function get_request_builder( $source ) {
		return false;
	}
	
	public function service() {
		return $this->service;
	}
	
	public function get_configuration() {
		return $this->configuration;
	}
}