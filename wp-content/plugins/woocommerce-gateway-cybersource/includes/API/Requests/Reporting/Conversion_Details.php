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

namespace SkyVerge\WooCommerce\Cybersource\API\Requests\Reporting;

use SkyVerge\WooCommerce\Cybersource\API\Requests;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API reporting conversion details request.
 *
 * @since 2.3.0
 */
class Conversion_Details extends Requests\Reporting {


	/**
	 * Conversion_Details constructor.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {

		parent::__construct();

		$this->path .= 'conversion-details';
	}


	/**
	 * Sets the data needed for querying conversion details.
	 *
	 * @since 2.3.0
	 *
	 * @param string $organization_id organization ID
	 * @param int $start_time desired start time
	 * @param int|null $end_time desired end time
	 */
	public function set_conversion_details( $organization_id, $start_time, $end_time = null ) {

		$this->method = self::REQUEST_METHOD_GET;

		if ( ! $end_time ) {
			$end_time = time();
		}

		$this->params = [
			'startTime'      => $this->format_time( $start_time ),
			'endTime'        => $this->format_time( $end_time ),
			'organizationId' => $organization_id,
		];
	}


	/**
	 * Formats a given timestamp to API requirements.
	 *
	 * @since 2.3.0
	 *
	 * @param int $timestamp timestamp
	 * @return string
	 */
	private function format_time( $timestamp ) {

		return date( \DateTime::ATOM, $timestamp );
	}


}
