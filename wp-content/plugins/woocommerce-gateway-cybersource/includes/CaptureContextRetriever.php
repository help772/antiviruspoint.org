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
 * @copyright   Copyright (c) 2012-2025, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource;

/** @since 2.9.0 */
class CaptureContextRetriever
{
	protected static string $captureContext;

	/**
	 * Retrieves the capture context, while storing the results in memory.
	 * This can be used to ensure we only end up with one call per page load.
	 *
	 * @since 2.9.0
	 */
	public static function getCaptureContext(): string
	{
		if (! isset(static::$captureContext)) {
			static::$captureContext = Plugin::instance()->get_gateway(Plugin::CREDIT_CARD_GATEWAY_ID)->get_api()->generate_public_key()->get_key_id();
		}

		return static::$captureContext;
	}
}
