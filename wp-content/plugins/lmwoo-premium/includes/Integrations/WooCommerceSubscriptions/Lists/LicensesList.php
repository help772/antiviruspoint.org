<?php


namespace LicenseManagerForWooCommerce\Integrations\WooCommerceSubscriptions\Lists;

use WC_Subscription;

defined( 'ABSPATH' ) || exit;

class LicensesList {
	
	public function __construct() {
		add_filter( 'lmfwc_table_licenses_column_name', array( $this, 'addSubscriptionColumn' ), 10, 1 );
		add_filter( 'lmfwc_table_licenses_column_value', array( $this, 'addSubscriptionColumnValue' ), 10, 2 );
	}

	public function addSubscriptionColumn( $columns ) {
		$order_idPosition = array_search( 'order_id', array_keys( $columns ) );

		return array_slice( $columns, 0, $order_idPosition, true )
			   + array( 'subscriptions' => esc_html__( 'Subscriptions', 'license-manager-for-woocommerce' ) )
			   + array_slice( $columns, $order_idPosition, count( $columns ), true );
	}

	public function addSubscriptionColumnValue( $item, $columnName ) {
		if ( 'subscriptions' !== $columnName ) {
			return $item;
		}

		$html     = '';
		$order_id = $item['order_id'];

		if ( $order_id ) {
			
			$subscriptions = wcs_get_subscriptions_for_order( $order_id );

			if ( $subscriptions ) {
				foreach ( $subscriptions as $i => $subscription ) {
					$html .= '<a href="' . esc_url( $subscription->get_edit_order_url() ) . '">#' . $subscription->get_id() . '</a>';

					if ( count( $subscriptions ) !== $i ) {
						$html .= '<br>';
					}
				}
			}
		}

		$item[ $columnName ] = $html;

		return $item;
	}
}
