<?php
/**
 * WC_Shipment_Tracking_List_Tracking_Items_Ability class file.
 *
 * @package WC_Shipment_Tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipment Tracking order tracking items ability definition.
 */
class WC_Shipment_Tracking_List_Tracking_Items_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID.
	 */
	const NAME = 'woocommerce-shipment-tracking/list-tracking-items';

	/**
	 * Ability category.
	 */
	const CATEGORY = 'woocommerce';

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return self::NAME;
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'List order shipment tracking items', 'woocommerce-shipment-tracking' ),
			'description'         => __( 'Retrieve curated shipment tracking items for an order, optionally filtered by tracking ID.', 'woocommerce-shipment-tracking' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read_tracking_items' ),
			'meta'                => self::get_read_only_meta(),
		);
	}

	/**
	 * Check whether the current user can read shipment tracking for the order.
	 *
	 * @param mixed $input Ability input.
	 *
	 * @return bool|\WP_Error
	 */
	public static function can_read_tracking_items( $input = null ) {
		$order_id = is_array( $input ) && isset( $input['order_id'] ) ? absint( $input['order_id'] ) : 0;

		if ( $order_id < 1 ) {
			return new WP_Error(
				'woocommerce_shipment_tracking_ability_invalid_order',
				__( 'Invalid order ID.', 'woocommerce-shipment-tracking' ),
				array( 'status' => 400 )
			);
		}

		if ( wc_rest_check_post_permissions( 'shop_order', 'read', $order_id ) ) {
			return true;
		}

		return new WP_Error(
			'woocommerce_shipment_tracking_ability_cannot_read_order',
			__( 'Sorry, you cannot read shipment tracking for this order.', 'woocommerce-shipment-tracking' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Execute the tracking items read ability.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array|\WP_Error
	 */
	public static function execute( array $input ) {
		$order_id = isset( $input['order_id'] ) ? absint( $input['order_id'] ) : 0;
		$order    = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return new WP_Error(
				'woocommerce_shipment_tracking_ability_invalid_order',
				__( 'Invalid order ID.', 'woocommerce-shipment-tracking' ),
				array( 'status' => 404 )
			);
		}

		$tracking_id    = isset( $input['tracking_id'] ) && is_scalar( $input['tracking_id'] ) ? wc_clean( (string) $input['tracking_id'] ) : '';
		$tracking_items = WC_Shipment_Tracking_Actions::get_instance()->get_tracking_items( $order_id, true );
		$tracking_items = array_values(
			array_filter(
				$tracking_items,
				static function ( $tracking_item ) use ( $tracking_id ) {
					if ( '' === $tracking_id ) {
						return true;
					}

					return isset( $tracking_item['tracking_id'] ) && $tracking_id === $tracking_item['tracking_id'];
				}
			)
		);

		return array(
			'order_id'       => $order->get_id(),
			'tracking_items' => array_map( array( __CLASS__, 'format_tracking_item' ), $tracking_items ),
			'count'          => count( $tracking_items ),
		);
	}

	/**
	 * Format a tracking item for the public ability contract.
	 *
	 * @param array $tracking_item Tracking item from the extension domain helper.
	 *
	 * @return array
	 */
	public static function format_tracking_item( array $tracking_item ) {
		return array(
			'tracking_id'     => isset( $tracking_item['tracking_id'] ) ? (string) $tracking_item['tracking_id'] : '',
			'provider_name'   => isset( $tracking_item['formatted_tracking_provider'] ) ? (string) $tracking_item['formatted_tracking_provider'] : '',
			'provider_type'   => ! empty( $tracking_item['custom_tracking_provider'] ) ? 'custom' : 'predefined',
			'tracking_number' => isset( $tracking_item['tracking_number'] ) ? (string) $tracking_item['tracking_number'] : '',
			'tracking_link'   => isset( $tracking_item['formatted_tracking_link'] ) ? (string) $tracking_item['formatted_tracking_link'] : '',
			'date_shipped'    => isset( $tracking_item['formatted_date_shipped_ymd'] ) ? (string) $tracking_item['formatted_date_shipped_ymd'] : '',
		);
	}

	/**
	 * Read-only metadata for externally visible abilities.
	 *
	 * @return array
	 */
	private static function get_read_only_meta() {
		return array(
			'show_in_rest' => true,
			'mcp'          => array(
				'public' => true,
				'type'   => 'tool',
			),
			'annotations'  => array(
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			),
		);
	}

	/**
	 * Get the input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'order_id'    => array(
					'type'        => 'integer',
					'description' => __( 'Order ID to read shipment tracking items from.', 'woocommerce-shipment-tracking' ),
					'minimum'     => 1,
				),
				'tracking_id' => array(
					'type'        => 'string',
					'description' => __( 'Optional tracking item ID to filter the order tracking items.', 'woocommerce-shipment-tracking' ),
					'pattern'     => '^[a-fA-F0-9]{32}$',
				),
			),
			'required'             => array( 'order_id' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get the output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'order_id'       => array( 'type' => 'integer' ),
				'tracking_items' => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'tracking_id'     => array( 'type' => 'string' ),
							'provider_name'   => array( 'type' => 'string' ),
							'provider_type'   => array(
								'type'        => 'string',
								'description' => __( 'Whether the provider comes from the built-in provider list or a custom order-level provider.', 'woocommerce-shipment-tracking' ),
								'enum'        => array( 'predefined', 'custom' ),
							),
							'tracking_number' => array( 'type' => 'string' ),
							'tracking_link'   => array(
								'type'        => 'string',
								'description' => __( 'Resolved tracking URL for this item when available.', 'woocommerce-shipment-tracking' ),
							),
							'date_shipped'    => array(
								'type'        => 'string',
								'description' => __( 'Shipment date formatted as YYYY-MM-DD.', 'woocommerce-shipment-tracking' ),
							),
						),
						'additionalProperties' => false,
					),
				),
				'count'          => array(
					'type'        => 'integer',
					'description' => __( 'Number of tracking items returned after applying the optional tracking ID filter.', 'woocommerce-shipment-tracking' ),
				),
			),
			'additionalProperties' => false,
		);
	}
}
