<?php

namespace WcPaysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Query {
	
	public function hooks() {
		add_filter( 'woocommerce_get_query_vars', array( $this, 'my_account_endpoints' ) );
		
		add_filter( 'woocommerce_endpoint_update-payment-method_title', array(
			$this,
			'endpoint_title'
		), 10, 2 );
	}
	
	/**
	 * Adds endpoint to the My Account page
	 *
	 * @since 3.3.0
	 *
	 * @param $query_vars
	 *
	 * @return mixed
	 */
	public function my_account_endpoints( $query_vars ) {
		$query_vars['update-payment-method'] = apply_filters( 'wc_paysafe_update_payment_method_endpoint', get_option( 'wc_paysafe_update_payment_method_endpoint', 'update-payment-method' ), $query_vars );
		
		return $query_vars;
	}
	
	/**
	 * Modifies the endpoint page title
	 *
	 * @since 3.3.0
	 *
	 * @param $title
	 * @param $endpoint
	 *
	 * @return string
	 */
	public function endpoint_title( $title, $endpoint ) {
		return apply_filters( 'wc_paysafe_update_payment_method_page_title', __( 'Update Payment Method', 'wc_paysafe' ), $title, $endpoint );
	}
}