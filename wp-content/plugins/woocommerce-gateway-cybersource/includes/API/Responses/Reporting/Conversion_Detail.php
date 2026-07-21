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

namespace SkyVerge\WooCommerce\Cybersource\API\Responses\Reporting;

use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API reporting conversion details response.
 *
 * @since 2.3.0
 */
class Conversion_Detail {


	/** @var string the REVIEW decision code */
	const DECISION_REVIEW = 'REVIEW';

	/** @var string the ACCEPT decision code */
	const DECISION_ACCEPT = 'ACCEPT';

	/** @var string the REJECT decision code */
	const DECISION_REJECT = 'REJECT';


	/** @var \stdClass detail data */
	private $data;


	/**
	 * Conversion_Detail constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param \stdClass $data detail data
	 */
	public function __construct( $data ) {

		$this->data = $data;
	}


	/**
	 * Gets the request ID.
	 *
	 * This constitutes the transaction ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_request_id() {

		return $this->get_property( 'requestId' );
	}


	/**
	 * Gets the original decision.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_original_decision() {

		return $this->get_property( 'originalDecision' );
	}


	/**
	 * Gets the new decision.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_new_decision() {

		return $this->get_property( 'newDecision' );
	}


	/**
	 * Gets any detailed notes.
	 *
	 * @since 2.3.0
	 *
	 * @return null|array
	 */
	public function get_notes() {

		return $this->get_property( 'notes' );
	}


	/**
	 * Determines whether the associated transaction has been settled.
	 *
	 * Unfortunately they don't return a concrete flag for this, but we can look in the notes to see the settlement
	 * success message.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_settled() {

		$is_settled = false;
		$notes      = $this->get_notes();

		if ( is_array( $notes ) ) {

			foreach ( $notes as $note ) {

				if ( ! empty( $note->comments ) && Framework\SV_WC_Helper::str_starts_with( $note->comments, 'The Card Settlement succeeded' ) ) {
					$is_settled = true;
					break;
				}
			}
		}

		return $is_settled;
	}


	/**
	 * Gets one of the detail's properties.
	 *
	 * @since 2.3.0
	 *
	 * @param string $property property to get
	 * @return null|string|array
	 */
	private function get_property( $property ) {

		return ! empty( $this->data->$property ) ? $this->data->$property : null;
	}


}
