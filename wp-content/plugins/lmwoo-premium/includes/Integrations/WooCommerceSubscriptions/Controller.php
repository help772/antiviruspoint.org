<?php


namespace LicenseManagerForWooCommerce\Integrations\WooCommerceSubscriptions;

use LicenseManagerForWooCommerce\Abstracts\IntegrationController as AbstractIntegrationController;
use LicenseManagerForWooCommerce\Interfaces\IntegrationController as IntegrationControllerInterface;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use WC_Order;
use WC_Order_Item_Product;
use WC_Product;
use WC_Subscriptions_Renewal_Order;

defined( 'ABSPATH' ) || exit;

class Controller extends AbstractIntegrationController implements IntegrationControllerInterface {

	public function __construct() {
		$this->bootstrap();

		add_filter( 'lmfwc_get_customer_license_keys', array( $this, 'getCustomerLicenseKeys' ), 11, 1 );
	}

	public function bootstrap() {
		new Api\V2\Licenses();
		new Lists\LicensesList();
		new Order();
		new ProductData();
	}

	public function getCustomerLicenseKeys( $args ) {

		$order = isset($args['order']) ? $args['order'] : null ;
		$data  = array();

		if ( ! $order ) {
			return;
		}

		foreach ( $order->get_items() as $itemData ) {

			$product  = $itemData->get_product();
			$order_id = $order->get_id();

			// Check if the product has been activated for selling.
			if ( ! lmfwc_is_licensed_product( $product->get_id() ) ) {
				continue;
			}

			// Check if the original license should have been extended, and
			// include it instead.
			if ( wcs_order_contains_renewal( $order_id ) 
				 && lmfwc_is_license_expiration_extendable_for_subscriptions( $product->get_id() )
			) {
				$parentOrder = WC_Subscriptions_Renewal_Order::get_parent_order( $order_id );

				if ( $parentOrder ) {
					$order_id = $parentOrder->get_id();
				}
			}


			$licenses = LicenseResourceRepository::instance()->findAllBy(
				array(
					'order_id'   => $order_id,
					'product_id' => $product->get_id(),
				)
			);

			$data[ $product->get_id() ]['name'] = $product->get_name();
			$data[ $product->get_id() ]['keys'] = $licenses;
		}

		$args['data'] = $data;

		return $args;
	}
}
