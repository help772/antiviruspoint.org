<?php
/**
 * Deliver Order license key(s) to Customer.
 *
 * @package YourPackage
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Output the email header.
 *
 * @hooked WC_Emails::email_header()
 * @since 1.0.0
 */
do_action('woocommerce_email_header', $email_heading, $email);

/**
 * Adds the ordered license keys table.
 *
 * @hooked \LicenseManagerForWooCommerce\Emails\Main
 * @since 1.0.0
 */
do_action('lmfwc_email_order_license_keys', $order, $sent_to_admin, $plain_text, $email);

/**
 * Adds basic order details.
 *
 * @hooked \LicenseManagerForWooCommerce\Emails\Main
 * @since 1.0.0
 */
do_action('lmfwc_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Shows customer details and email address.
 *
 * @hooked WC_Emails::customer_details()
 * @hooked WC_Emails::email_address()
 * @since 1.0.0
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Output the email footer.
 *
 * @hooked WC_Emails::email_footer()
 * @since 1.0.0
 */
do_action('woocommerce_email_footer', $email);
