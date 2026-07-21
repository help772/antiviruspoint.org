<?php

namespace LicenseManagerForWooCommerce\Integrations\WooCommerce\Emails;

use WC_Email;
use WC_Order;

defined('ABSPATH') || exit;

class Templates {

	/**
	 * Templates constructor.
	 */
	public function __construct() {
		add_action('lmfwc_email_order_details', array( $this, 'addOrderDetails' ), 10, 4);
		add_action('lmfwc_email_order_license_keys', array( $this, 'addOrderLicenseKeys' ), 10, 4);
	}

	/**
	 * Adds the ordered license keys to the email body.
	 *
	 * @param WC_Order $order       WooCommerce Order
	 * @param bool     $sentToAdmin Determines if the email is sent to the admin
	 * @param bool     $plainText   Determines if a plain text or HTML email will be sent
	 * @param WC_Email $email       WooCommerce Email
	 */
	public function addOrderDetails( $order, $sentToAdmin, $plainText, $email ) {
		if ($plainText) {
			wc_get_template(
				'emails/plain/lmfwc-email-order-details.php',
				array(
					'order'         => $order,
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $email,
					/**
					* Filter lmfwc_template_args_emails_email_order_details
					* 
					* @since 1.0
					**/
					'args'          => apply_filters('lmfwc_template_args_emails_email_order_details', array()),
				),
				'',
				LMFWC_TEMPLATES_DIR
			);
		} else {
			wc_get_template(
				'emails/lmfwc-email-order-details.php',
				array(
					'order'         => $order,
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $email,
					/**
					* Filter lmfwc_template_args_emails_email_order_details
					* 
					* @since 1.0
					**/
					'args'          => apply_filters('lmfwc_template_args_emails_email_order_details', array()),
				),
				'',
				LMFWC_TEMPLATES_DIR
			);
		}
	}

	/**
	 * Adds basic order info to the email body.
	 *
	 * @param WC_Order $order       WooCommerce Order
	 * @param bool     $sentToAdmin Determines if the email is sent to the admin
	 * @param bool     $plainText   Determines if a plain text or HTML email will be sent
	 * @param WC_Email $email       WooCommerce Email
	 */
	public function addOrderLicenseKeys( $order, $sentToAdmin, $plainText, $email ) {
		/**
		* Filter lmfwc_get_customer_license_keys
		* 
		* @since 1.0
		**/
		$data = apply_filters('lmfwc_get_customer_license_keys', $order);
		if ( !$data ) {
			return;
		}
		if ($plainText) {
			wc_get_template(
				'emails/plain/lmfwc-email-order-license-keys.php',
				array(
					/**
					* Filter lmfwc_license_keys_table_heading
					* 
					* @since 1.0
					**/
					'heading'       => apply_filters('lmfwc_license_keys_table_heading', null),
					/**
					* Filter lmfwc_license_keys_table_valid_until
					* 
					* @since 1.0
					**/
					'valid_until'   => apply_filters('lmfwc_license_keys_table_valid_until', null),
					'data'          => $data,
					'date_format'   => get_option('date_format'),
					'order'         => $order,
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $email,
					/**
					* Filter lmfwc_template_args_emails_order_license_keys
					* 
					* @since 1.0
					**/
					'args'          => apply_filters('lmfwc_template_args_emails_order_license_keys', array()),
				),
				'',
				LMFWC_TEMPLATES_DIR
			);
		} else {
			wc_get_template(
				'emails/lmfwc-email-order-license-keys.php',
				array(
					/**
					* Filter lmfwc_license_keys_table_heading
					* 
					* @since 1.0
					**/
					'heading'       => apply_filters('lmfwc_license_keys_table_heading', null),
					/**
					* Filter lmfwc_license_keys_table_valid_until
					* 
					* @since 1.0
					**/
					'valid_until'   => apply_filters('lmfwc_license_keys_table_valid_until', null),
					'data'          => $data,
					'date_format'   => get_option('date_format'),
					'order'         => $order,
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $email,
					/**
					* Filter lmfwc_template_args_emails_order_license_keys
					* 
					* @since 1.0
					**/
					'args'          => apply_filters('lmfwc_template_args_emails_order_license_keys', array()),
				),
				'',
				LMFWC_TEMPLATES_DIR
			);
		}
	}
}
