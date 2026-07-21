<?php
/**
 * FedEx carrier codes for REST API rate requests.
 *
 * @package WC_Shipping_Fedex
 */

namespace WooCommerce\FedEx;

defined( 'ABSPATH' ) || exit;

/**
 * FedEx Express carrier code.
 *
 * @var string
 */
const CARRIER_CODE_EXPRESS = 'FDXE';

/**
 * FedEx Ground carrier code.
 *
 * @var string
 */
const CARRIER_CODE_GROUND = 'FDXG';

/**
 * FedEx SmartPost (Ground Economy) carrier code.
 *
 * @var string
 */
const CARRIER_CODE_SMARTPOST = 'FXSP';
