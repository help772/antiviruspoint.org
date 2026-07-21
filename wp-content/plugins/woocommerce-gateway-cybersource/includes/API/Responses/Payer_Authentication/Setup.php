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

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API payer authentication setup request.
 *
 * @since 2.0.0
 */
class Setup extends Response {


	/**
	 * Gets the JSON Web Token.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_jwt(): string {

		return $this->response_data->consumerAuthenticationInformation->accessToken ?? '';
	}


	/**
	 * Gets the reference ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_reference_id(): string {

		return $this->response_data->consumerAuthenticationInformation->referenceId ?? '';
	}


	/**
	 * Gets the device data collection URL.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_device_data_collection_url(): string {

		return $this->response_data->consumerAuthenticationInformation->deviceDataCollectionUrl ?? '';
	}


	/**
	 * Gets the string representation of this response with all sensitive information masked.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function to_string_safe() {

		$value = $this->get_jwt();

		return str_replace( $value, str_repeat( '*', strlen( $value ) ), $this->to_string() );
	}


}
