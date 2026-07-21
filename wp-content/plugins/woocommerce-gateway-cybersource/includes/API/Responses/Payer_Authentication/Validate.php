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

namespace SkyVerge\WooCommerce\Cybersource\API\Responses\Payer_Authentication;

use SkyVerge\WooCommerce\Cybersource\API\Response;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;
use stdClass;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API payer authentication validate response.
 *
 * TODO: this class is not used at the moment, consider removing in the next release {@itambek 2024-02-15}
 *
 * @since 2.0.0
 */
class Validate extends Response {


	/**
	 * Gets the response status.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_status() : string {

		return $this->response_data->status ?? '';
	}


	/**
	 * Gets the consumer authentication information.
	 *
	 * @since 2.8.0
	 *
	 * @return stdClass|null
	 */
	public function get_consumer_authentication_information() : ?stdClass {

		return $this->response_data->consumerAuthenticationInformation ?? null;
	}


	/**
	 * Gets the CAVV value.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_cavv() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->cavv ) ? $this->response_data->consumerAuthenticationInformation->cavv : '';
	}


	/**
	 * Gets the CAVV algorithm.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_cavv_algorithm() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->cavvAlgorithm ) ? $this->response_data->consumerAuthenticationInformation->cavvAlgorithm : '';
	}


	/**
	 * Gets the raw ECI value
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_eci_raw() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->eciRaw ) ? $this->response_data->consumerAuthenticationInformation->eciRaw : '';
	}


	/**
	 * Gets the PARes status.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_pares_status() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->paresStatus ) ? $this->response_data->consumerAuthenticationInformation->paresStatus : '';
	}


	/**
	 * Gets the XID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_xid() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->xid ) ? $this->response_data->consumerAuthenticationInformation->xid : '';
	}


	/**
	 * Gets the 3D Secure specification version.
	 *
	 * This represents either v1 or v2.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_specification_version() {

		return ! empty( $this->response_data->consumerAuthenticationInformation->specificationVersion ) ? $this->response_data->consumerAuthenticationInformation->specificationVersion : '';
	}


}
