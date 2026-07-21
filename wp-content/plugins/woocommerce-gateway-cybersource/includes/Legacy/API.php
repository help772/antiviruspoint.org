<?php
/**
 * WooCommerce CyberSource
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource\Legacy;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

/**
 * API class for communicating with the CyberSource SOAP gateway
 *
 * @since 1.0
 *
 * @deprecated since 2.8.0 this class and its methods are kept only for legacy purposes and shouldn't be relied upon
 */
class API extends \SoapClient {


	/** @var string username */
	private $user;

	/** @var string password */
	private $pass;


	/**
	 * Initializes the gateway API.
	 *
	 * @since 1.0
	 *
	 * @param mixed $wsdl
	 * @param null|array $options
	 *
	 * @throws \SoapFault
	 */
	public function __construct( $wsdl, $options = null ) {

		if ( $options ) {
			parent::__construct( $wsdl, $options );
		} else {
			parent::__construct( $wsdl );
		}
	}


	/**
	 * Sets the WS Security vars: $user and $pass.
	 *
	 * @since 1.0
	 *
	 * @param string $user username (merchant id)
	 * @param string $pass password (transaction key)
	 */
	public function set_ws_security( $user, $pass ) {

		$this->user = $user;
		$this->pass = $pass;
	}


	/**
	 * Overrides the SoapClient method.
	 * Inserts the Username/Password Tokens (WS Security) in the outgoing SOAP message.
	 *
	 * @since 1.0
	 *
	 * @param string $request
	 * @param string $location
	 * @param string $action
	 * @param int $version
	 * @param null|bool $one_way
	 * @return string
	 */
	public function __doRequest( $request, $location, $action, $version, $one_way = NULL ) {

		// create the soap header element, containing the user/pass tokens
		$soap_header = "<SOAP-ENV:Header xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\"><wsse:Security SOAP-ENV:mustUnderstand=\"1\"><wsse:UsernameToken><wsse:Username>{$this->user}</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">{$this->pass}</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>";

		$request_dom     = new \DOMDocument( '1.0' );
		$soap_header_dom = new \DOMDocument( '1.0' );

		try {

			$request_dom->loadXML( $request );
			$soap_header_dom->loadXML( $soap_header );

			$node = $request_dom->importNode( $soap_header_dom->firstChild, true );
			$request_dom->firstChild->insertBefore( $node, $request_dom->firstChild->firstChild );

			$request = $request_dom->saveXML();

		} catch ( \DOMException $e ) {
			die( 'Error adding UsernameToken: ' . $e->code);
		}

		return parent::__doRequest( $request, $location, $action, $version );
	}


}
