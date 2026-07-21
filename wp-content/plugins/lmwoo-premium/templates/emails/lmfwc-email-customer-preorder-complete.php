<?php
/**
 * Email template for notifying customers about a fulfilled preorder.
 *
 * @package YourThemeOrPlugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output the email header.
 *
 * @hooked WC_Emails::email_header()
 * @since 2.5.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

?>
	<p>
	<?php
	// Translators: %d is a placeholder for the order number.
	echo esc_html( sprintf( __( 'The preorder #%d has been fulfilled. Order Details:', 'license-manager-for-woocommerce' ), $order->get_order_number() ) );
	?>
	</p>


<?php
/**
 * Shows the order details table.
 *
 * @hooked WC_Emails::order_details()
 * @hooked WC_Emails::order_schema_markup()
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Shows order meta data.
 *
 * @hooked WC_Emails::order_meta()
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * Shows customer details.
 *
 * @hooked WC_Emails::customer_details()
 * @hooked WC_Emails::email_address()
 * @since 2.5.0
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Output the email footer.
 *
 * @hooked WC_Emails::email_footer()
 * @since 2.5.0
 */
do_action( 'woocommerce_email_footer', $email );
