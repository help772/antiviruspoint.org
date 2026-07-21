<?php

class WC_Software_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( __( 'Software Add-On for WooCommerce', 'woocommerce-software-add-on' ) );

		$this->add_eraser( 'woocommerce-software-add-on-order-data', __( 'WooCommerce Software Data', 'woocommerce-software-add-on' ), array( $this, 'order_data_eraser' ) );
		$this->add_eraser( 'woocommerce-software-add-on-order-data', __( 'WooCommerce Software Data', 'woocommerce-software-add-on' ), array( $this, 'software_data_eraser' ) );
	}

	/**
	 * Returns a list of orders.
	 *
	 * @param string $email_address
	 * @param int    $page
	 *
	 * @return array WP_Post
	 */
	protected function get_orders( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$order_query = array(
			'limit' => 10,
			'page'  => $page,
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer_id'] = (int) $user->ID;
		} else {
			$order_query['billing_email'] = $email_address;
		}

		return wc_get_orders( $order_query );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-software-add-on' ), 'https://docs.woocommerce.com/document/marketplace-privacy/#woocommerce-software-add-on' ) );
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function software_data_eraser( $email_address, $page ) {
		global $wpdb;

		$rows_updated = $wpdb->update(
			$wpdb->wc_software_licenses,
			array(
				'order_id'         => 0,
				'activation_email' => '',
			),
			array( 'activation_email' => $email_address ),
			array(
				'%d',
				'%s',
			),
			array( '%s' )
		);

		$items_removed = $rows_updated > 0;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function order_data_eraser( $email_address, $page ) {
		$orders = $this->get_orders( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_order( $order );
			$items_removed                    |= $removed;
			$items_retained                   |= $retained;
			$messages                          = array_merge( $messages, $msgs );
		}

		// Tell core if we have more orders to work on still
		$done = count( $orders ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of data tied to Orders
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function maybe_handle_order( $order ) {
		global $wpdb;

		$order_id = $order->get_id();

		$rows_updated = $wpdb->update(
			$wpdb->wc_software_licenses,
			array(
				'order_id'         => 0,
				'activation_email' => '',
			),
			array( 'order_id' => $order_id ),
			array(
				'%d',
				'%s',
			),
			array( '%d' )
		);

		if ( 0 === $rows_updated || false === $rows_updated ) {
			return array( false, false, array() );
		}

		return array( true, false, array( __( 'Software Add-On personal data erased.', 'woocommerce-software-add-on' ) ) );
	}
}

new WC_Software_Privacy();
