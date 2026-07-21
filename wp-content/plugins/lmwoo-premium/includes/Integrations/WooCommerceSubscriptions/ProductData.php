<?php
// nosemgrep: all

namespace LicenseManagerForWooCommerce\Integrations\WooCommerceSubscriptions;

use WC_Subscriptions_Product;
use WP_Post;

defined( 'ABSPATH' ) || exit;

class ProductData {
	
	public function __construct() {
		add_action( 'lmfwc_simple_product_data_panel', array( $this, 'simpleProductDataPanel' ), 10, 1 );
		add_action( 'lmfwc_simple_product_save', array( $this, 'simpleProductSave' ), 10, 1 );
		add_action( 'lmfwc_variable_product_data_panel', array( $this, 'variableProductDataPanel' ), 10, 3 );
		add_action( 'lmfwc_variable_product_save', array( $this, 'variableProductSave' ), 10, 2 );
	}

	
	public function simpleProductDataPanel( $post ) {
		
		$product = wc_get_product($post->ID);
		$renewalAction       = $product->get_meta('lmfwc_subscription_renewal_action', true );
		$renewalIntervalType = $product->get_meta('lmfwc_subscription_renewal_interval_type', true );
		$customInterval      = $product->get_meta('lmfwc_subscription_renewal_custom_interval', true );
		$customPeriod        = $product->get_meta('lmfwc_subscription_renewal_custom_period', true );

		$wrapperClass = array(
			'lmfwc_subscription_renewal_action'          => '',
			'lmfwc_subscription_renewal_interval_type'   => '',
			'lmfwc_subscription_renewal_custom_interval' => '',
			'lmfwc_subscription_renewal_custom_period'   => '',
		);

		if ( ! WC_Subscriptions_Product::is_subscription( $product->get_id() ) ) {
			$wrapperClass['lmfwc_subscription_renewal_action'] .= ' hidden';
		}

		if ( 'extend_existing_license' === $renewalAction ) {
			if ( 'subscription' === $renewalIntervalType ) {
				$wrapperClass['lmfwc_subscription_renewal_custom_interval'] .= ' hidden';
				$wrapperClass['lmfwc_subscription_renewal_custom_period']   .= ' hidden';
			}
		} else {
			$wrapperClass['lmfwc_subscription_renewal_interval_type']   .= ' hidden';
			$wrapperClass['lmfwc_subscription_renewal_custom_interval'] .= ' hidden';
			$wrapperClass['lmfwc_subscription_renewal_custom_period']   .= ' hidden';
		}

		echo '</div><div class="options_group">';

		// Dropdown "lmfwc_subscription_renewal_action"
		woocommerce_wp_select(
			array(
				'id'            => esc_html('lmfwc_subscription_renewal_action'),
				'class'         => esc_html('lmfwc_subscription_renewal_action select short'),
				'wrapper_class' => esc_html($wrapperClass['lmfwc_subscription_renewal_action']),
				'label'         => esc_html__( 'On subscription renewal', 'license-manager-for-woocommerce' ),
				'value'         => esc_html($renewalAction),
				'options'       => array(
					'issue_new_license'       => esc_html__( 'Issue a new license key on each subscription renewal', 'license-manager-for-woocommerce' ),
					'extend_existing_license' => esc_html__( 'Extend the existing license on each subscription renewal', 'license-manager-for-woocommerce' ),
				),
			)
		);

		// Dropdown "lmfwc_subscription_renewal_interval_type"
		woocommerce_wp_select(
			array(
				'id'            => esc_html('lmfwc_subscription_renewal_interval_type'),
				'class'         => esc_html('lmfwc_subscription_renewal_interval_type select short'),
				'wrapper_class' => $wrapperClass['lmfwc_subscription_renewal_interval_type'],
				'label'         => esc_html__( 'Extend by', 'license-manager-for-woocommerce' ),
				'options'       => array(
					'subscription' => esc_html__( 'WooCommerce Subscription interval', 'license-manager-for-woocommerce' ),
					'custom'       => esc_html__( 'Custom interval', 'license-manager-for-woocommerce' ),
				),
				'value'         => esc_html($renewalIntervalType),
			)
		);

		// Number "lmfwc_subscription_renewal_custom_interval"
		woocommerce_wp_text_input(
			array(
				'id'                => esc_html('lmfwc_subscription_renewal_custom_interval'),
				'class'             => esc_html('lmfwc_subscription_renewal_custom_interval short'),
				'wrapper_class'     => esc_html($wrapperClass['lmfwc_subscription_renewal_custom_interval']),
				'label'             => esc_html__( 'Interval', 'license-manager-for-woocommerce' ),
				'value'             => esc_html($customInterval),
				'type'              => esc_html('number'),
				'custom_attributes' => array(
					'step' => '1',
				),
			)
		);

		// Dropdown "lmfwc_subscription_renewal_custom_period"
		woocommerce_wp_select(
			array(
				'id'            => esc_html('lmfwc_subscription_renewal_custom_period'),
				'class'         => esc_html('lmfwc_subscription_renewal_custom_period select short'),
				'wrapper_class' => esc_html($wrapperClass['lmfwc_subscription_renewal_custom_period']),
				'label'         => esc_html__( 'Period', 'license-manager-for-woocommerce' ),
				'options'       => array(
					'hour'  => esc_html__( 'Hour(s)', 'license-manager-for-woocommerce' ),
					'day'   => esc_html__( 'Day(s)', 'license-manager-for-woocommerce' ),
					'week'  => esc_html__( 'Week(s)', 'license-manager-for-woocommerce' ),
					'month' => esc_html__( 'Month(s)', 'license-manager-for-woocommerce' ),
					'year'  => esc_html__( 'Year(s)', 'license-manager-for-woocommerce' ),
				),
				'value'         => esc_html($customPeriod),
			)
		);
	}


	public function simpleProductSave( $postId ) {
		// nosemgrep: scanner.php.wp.security.csrf.verify-nonce-inverted
		if ( isset($_POST['lmfwc_nonce']) && wp_verify_nonce(sanitize_text_field( $_POST['lmfwc_nonce'] ), 'lmfwc_nonce') ) {  // nosemgrep
			exit;
		}
		$product = wc_get_product($postId);
		// Update the extend subscription flag, according to checkbox.
		if ( array_key_exists( 'lmfwc_license_expiration_extendable_for_subscriptions', $_POST ) ) {
			$product->update_meta_data( 'lmfwc_license_expiration_extendable_for_subscriptions', 1 );
		} else {
			$product->update_meta_data( 'lmfwc_license_expiration_extendable_for_subscriptions', 0 );
		}

		// Update the subscription renewal action
		if ( isset( $_POST['lmfwc_subscription_renewal_action'] ) ) {
			
			$product->update_meta_data( 'lmfwc_subscription_renewal_action', sanitize_text_field( $_POST['lmfwc_subscription_renewal_action'] ) );
			
		}

		// Update the subscription renewal interval type
		if ( isset( $_POST['lmfwc_subscription_renewal_interval_type'] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_interval_type', sanitize_text_field( $_POST['lmfwc_subscription_renewal_interval_type'] ) );
		}

		// Update the subscription renewal custom interval
		if ( isset( $_POST['lmfwc_subscription_renewal_custom_interval'] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_custom_interval', (int) sanitize_text_field( $_POST['lmfwc_subscription_renewal_custom_interval'] ) );
		}

		// Update the subscription renewal custom period
		if ( isset( $_POST['lmfwc_subscription_renewal_custom_period'] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_custom_period', sanitize_text_field( $_POST['lmfwc_subscription_renewal_custom_period'] ) );
		}
		$product->save();
	}
	

	public function variableProductDataPanel( $loop, $variationData, $variation ) {

		$product_id          = $variation->ID;
		$product             = wc_get_product($product_id);
		$renewalAction       = $product->get_meta('lmfwc_subscription_renewal_action', true );
		$renewalIntervalType = $product->get_meta('lmfwc_subscription_renewal_interval_type', true );
		$customInterval      = $product->get_meta('lmfwc_subscription_renewal_custom_interval', true );
		$customPeriod        = $product->get_meta('lmfwc_subscription_renewal_custom_period', true );
			
		$wrapperClass = array(
			'lmfwc_subscription_renewal_action'          => 'lmfwc_subscription_renewal_action_field form-row form-row-full',
			'lmfwc_subscription_renewal_interval_type'   => 'lmfwc_subscription_renewal_interval_type_field form-row form-row-full',
			'lmfwc_subscription_renewal_custom_interval' => 'lmfwc_subscription_renewal_custom_interval_field form-field form-row form-row-first',
			'lmfwc_subscription_renewal_custom_period'   => 'lmfwc_subscription_renewal_custom_period_field form-field form-row form-row-last',
		);

		if ( ! WC_Subscriptions_Product::is_subscription( $product_id ) ) {
			$wrapperClass['lmfwc_subscription_renewal_action'] .= ' hidden';
		}

		if ( 'extend_existing_license' === $renewalAction ) {
			if ( 'subscription' === $renewalIntervalType ) {
				$wrapperClass['lmfwc_subscription_renewal_custom_interval'] .= ' hidden';
				$wrapperClass['lmfwc_subscription_renewal_custom_period']   .= ' hidden';
			}
		} else {
			$wrapperClass['lmfwc_subscription_renewal_interval_type']   .= ' hidden';
			$wrapperClass['lmfwc_subscription_renewal_custom_interval'] .= ' hidden';
			$wrapperClass['lmfwc_subscription_renewal_custom_period']   .= ' hidden';
		}

		// Dropdown "lmfwc_subscription_renewal_action"
		woocommerce_wp_select(
			array(
				'id'            => sprintf( 'lmfwc_subscription_renewal_action_%d', $loop ),
				'class'         => 'lmfwc_subscription_renewal_action',
				'wrapper_class' => $wrapperClass['lmfwc_subscription_renewal_action'],
				'name'          => sprintf( 'lmfwc_subscription_renewal_action[%d]', $loop ),
				'label'         => esc_html__( 'On subscription renewal', 'license-manager-for-woocommerce' ),
				'value'         => $renewalAction,
				'options'       => array(
					'issue_new_license'       => esc_html__( 'Issue a new license key on each subscription renewal', 'license-manager-for-woocommerce' ),
					'extend_existing_license' => esc_html__( 'Extend the existing license on each subscription renewal', 'license-manager-for-woocommerce' ),
				),
			)
		);

		// Dropdown "lmfwc_subscription_renewal_interval_type"
		woocommerce_wp_select(
			array(
				'id'            => sprintf( 'lmfwc_subscription_renewal_interval_type_%d', $loop ),
				'class'         => 'lmfwc_subscription_renewal_interval_type',
				'wrapper_class' => $wrapperClass['lmfwc_subscription_renewal_interval_type'],
				'name'          => sprintf( 'lmfwc_subscription_renewal_interval_type[%d]', $loop ),
				'label'         => esc_html__( 'Extend by', 'license-manager-for-woocommerce' ),
				'options'       => array(
					'subscription' => esc_html__( 'WooCommerce Subscription interval', 'license-manager-for-woocommerce' ),
					'custom'       => esc_html__( 'Custom interval', 'license-manager-for-woocommerce' ),
				),
				'value'         => $renewalIntervalType,
			)
		);

		// Number "lmfwc_subscription_renewal_custom_interval"
		woocommerce_wp_text_input(
			array(
				'id'                => sprintf( 'lmfwc_subscription_renewal_custom_interval_%d', $loop ),
				'class'             => 'lmfwc_subscription_renewal_custom_interval',
				'wrapper_class'     => $wrapperClass['lmfwc_subscription_renewal_custom_interval'],
				'name'              => sprintf( 'lmfwc_subscription_renewal_custom_interval[%d]', $loop ),
				'label'             => esc_html__( 'Interval', 'license-manager-for-woocommerce' ),
				'value'             => $customInterval,
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => '1',
				),
			)
		);

		// Dropdown "lmfwc_subscription_renewal_custom_period"
		woocommerce_wp_select(
			array(
				'id'            => sprintf( 'lmfwc_subscription_renewal_custom_period_%d', $loop ),
				'class'         => 'lmfwc_subscription_renewal_custom_period',
				'wrapper_class' => $wrapperClass['lmfwc_subscription_renewal_custom_period'],
				'name'          => sprintf( 'lmfwc_subscription_renewal_custom_period[%d]', $loop ),
				'label'         => esc_html__( 'Period', 'license-manager-for-woocommerce' ),
				'options'       => array(
					'hour'  => esc_html__( 'Hour(s)', 'license-manager-for-woocommerce' ),
					'day'   => esc_html__( 'Day(s)', 'license-manager-for-woocommerce' ),
					'week'  => esc_html__( 'Week(s)', 'license-manager-for-woocommerce' ),
					'month' => esc_html__( 'Month(s)', 'license-manager-for-woocommerce' ),
					'year'  => esc_html__( 'Year(s)', 'license-manager-for-woocommerce' ),
				),
				'value'         => $customPeriod,
			)
		);
	}


	public function variableProductSave( $variationId, $i ) {
		// nosemgrep: scanner.php.wp.security.csrf.verify-nonce-inverted
		if ( isset($_POST['lmfwc_nonce']) && wp_verify_nonce(sanitize_text_field( $_POST['lmfwc_nonce'] ), 'lmfwc_nonce') ) {  // nosemgrep
			exit;
		}
		$product = wc_get_product($variationId);
		// Update the subscription renewal action
		if ( isset( $_POST['lmfwc_subscription_renewal_action'][ $i ] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_action', sanitize_text_field( $_POST['lmfwc_subscription_renewal_action'][ $i ] ) );
		}

		// Update the subscription renewal interval type
		if ( isset( $_POST['lmfwc_subscription_renewal_interval_type'][ $i ] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_interval_type', sanitize_text_field( $_POST['lmfwc_subscription_renewal_interval_type'][ $i ] ) );
		}

		// Update the subscription renewal custom interval
		if ( isset( $_POST['lmfwc_subscription_renewal_custom_interval'][ $i ] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_custom_interval', (int) sanitize_text_field( $_POST['lmfwc_subscription_renewal_custom_interval'][ $i ] ) );
		}

		// Update the subscription renewal custom period
		if ( isset( $_POST['lmfwc_subscription_renewal_custom_period'][ $i ] ) ) {
			$product->update_meta_data( 'lmfwc_subscription_renewal_custom_period', sanitize_text_field( $_POST['lmfwc_subscription_renewal_custom_period'][ $i ] ) );
		}
		$product->save();
	}
}
