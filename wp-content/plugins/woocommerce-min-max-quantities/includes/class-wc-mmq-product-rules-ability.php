<?php
/**
 * WC_MMQ_Product_Rules_Ability class
 *
 * @package WooCommerce Min/Max Quantities
 * @since   5.2.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MMQ_Product_Rules_Ability ability definition.
 *
 * @version 5.2.9
 */
class WC_MMQ_Product_Rules_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-min-max-quantities/get-product-rules';
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get product Min/Max Quantities rules', 'woocommerce-min-max-quantities' ),
			'description'         => __( 'Returns the effective Min/Max Quantities rules and rule flags for a product or variation.', 'woocommerce-min-max-quantities' ),
			'category'            => 'woocommerce',
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => self::get_meta(),
		);
	}

	/**
	 * Checks whether the current user can read product rules.
	 *
	 * @param array $input Ability input.
	 * @return bool
	 */
	public static function can_read( $input = array() ) {
		$product_id = absint( $input['product_id'] ?? 0 );
		$product    = $product_id ? wc_get_product( $product_id ) : false;

		if ( ! $product ) {
			return current_user_can( 'manage_woocommerce' );
		}

		if ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'edit_product', $product->get_id() ) ) {
			return true;
		}

		if ( $product->is_type( 'variation' ) ) {
			return current_user_can( 'edit_product', $product->get_parent_id() );
		}

		return false;
	}

	/**
	 * Returns effective Min/Max Quantities rules for a product or variation.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( $input = array() ) {
		$product_id = absint( $input['product_id'] ?? 0 );
		$product    = $product_id ? wc_get_product( $product_id ) : false;

		if ( ! $product instanceof WC_Product ) {
			return new WP_Error(
				'woocommerce_min_max_quantities_product_not_found',
				__( 'The requested product was not found.', 'woocommerce-min-max-quantities' ),
				array( 'status' => 404 )
			);
		}

		$rules              = WC_Min_Max_Quantities::get_instance()->get_quantity_rules_for_product( $product );
		$is_variation       = $product->is_type( 'variation' );
		$parent_product     = $is_variation ? wc_get_product( $product->get_parent_id() ) : null;
		$combine_variations = self::product_combines_variations( $product, $parent_product );

		return array(
			'product_id'                         => $product->get_id(),
			'parent_id'                          => $parent_product instanceof WC_Product ? $parent_product->get_id() : 0,
			'product_type'                       => $product->get_type(),
			'quantity_rules'                     => array(
				'minimum_quantity'  => absint( $rules[ WC_Min_Max_Quantities_Quantity_Rules::MINIMUM ] ?? 0 ),
				'maximum_quantity'  => absint( $rules[ WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM ] ?? 0 ),
				'group_of_quantity' => absint( $rules[ WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF ] ?? 0 ),
			),
			'variation_rules_enabled'            => $is_variation && ! $combine_variations && 'yes' === $product->get_meta( 'min_max_rules', true ),
			'combine_variations'                 => $combine_variations,
			'do_not_count_order_rules'           => self::product_has_effective_yes_meta( $product, $parent_product, 'minmax_do_not_count', 'variation_minmax_do_not_count' ),
			'exclude_order_quantity_value_rules' => self::product_has_effective_yes_meta( $product, $parent_product, 'minmax_cart_exclude', 'variation_minmax_cart_exclude' ),
			'exclude_category_quantity_rules'    => self::product_has_effective_yes_meta( $product, $parent_product, 'minmax_category_group_of_exclude', 'variation_minmax_category_group_of_exclude' ),
		);
	}

	/**
	 * Returns the input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'product_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Product or variation ID.', 'woocommerce-min-max-quantities' ),
				),
			),
			'required'             => array( 'product_id' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'product_id'                         => array(
					'type'        => 'integer',
					'description' => __( 'Product or variation ID these effective rules were calculated for.', 'woocommerce-min-max-quantities' ),
				),
				'parent_id'                          => array(
					'type'        => 'integer',
					'description' => __( 'Parent product ID when the requested product is a variation; otherwise 0.', 'woocommerce-min-max-quantities' ),
				),
				'product_type'                       => array(
					'type'        => 'string',
					'description' => __( 'WooCommerce product type for the requested product or variation.', 'woocommerce-min-max-quantities' ),
				),
				'quantity_rules'                     => self::get_quantity_rules_schema(),
				'variation_rules_enabled'            => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the requested variation uses its own variation-level Min/Max rules instead of parent-combined variation rules.', 'woocommerce-min-max-quantities' ),
				),
				'combine_variations'                 => array(
					'type'        => 'boolean',
					'description' => __( 'Whether purchased variation quantities are combined when evaluating parent product Min/Max rules.', 'woocommerce-min-max-quantities' ),
				),
				'do_not_count_order_rules'           => array(
					'type'        => 'boolean',
					'description' => __( 'Whether this product or variation quantity and value are excluded from store-level order rule totals.', 'woocommerce-min-max-quantities' ),
				),
				'exclude_order_quantity_value_rules' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether store-level order quantity and value rules are skipped for this product or variation.', 'woocommerce-min-max-quantities' ),
				),
				'exclude_category_quantity_rules'    => array(
					'type'        => 'boolean',
					'description' => __( 'Whether category quantity rules are skipped for this product or variation.', 'woocommerce-min-max-quantities' ),
				),
			),
			'required'             => array(
				'product_id',
				'parent_id',
				'product_type',
				'quantity_rules',
				'variation_rules_enabled',
				'combine_variations',
				'do_not_count_order_rules',
				'exclude_order_quantity_value_rules',
				'exclude_category_quantity_rules',
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the quantity rule object schema.
	 *
	 * @return array
	 */
	private static function get_quantity_rules_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'minimum_quantity'  => array(
					'type'        => 'integer',
					'description' => __( 'Minimum required quantity. A value of 0 means no minimum rule is configured.', 'woocommerce-min-max-quantities' ),
				),
				'maximum_quantity'  => array(
					'type'        => 'integer',
					'description' => __( 'Maximum allowed quantity. A value of 0 means no maximum rule is configured.', 'woocommerce-min-max-quantities' ),
				),
				'group_of_quantity' => array(
					'type'        => 'integer',
					'description' => __( 'Required quantity multiple. A value of 0 means no group-of rule is configured.', 'woocommerce-min-max-quantities' ),
				),
			),
			'required'             => array( 'minimum_quantity', 'maximum_quantity', 'group_of_quantity' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns metadata for a read-only, externally visible ability.
	 *
	 * @return array
	 */
	private static function get_meta() {
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
	 * Checks whether a product combines variation quantities.
	 *
	 * @param WC_Product      $product Product object.
	 * @param WC_Product|null $parent_product Parent product, if available.
	 * @return bool
	 */
	private static function product_combines_variations( WC_Product $product, $parent_product ) {
		if ( $parent_product instanceof WC_Product ) {
			return 'yes' === $parent_product->get_meta( 'allow_combination', true );
		}

		return 'yes' === $product->get_meta( 'allow_combination', true );
	}

	/**
	 * Checks effective yes/no rule flags, including parent inheritance for variations.
	 *
	 * @param WC_Product      $product Product object.
	 * @param WC_Product|null $parent_product Parent product, if available.
	 * @param string          $product_meta_key Product-level meta key.
	 * @param string          $variation_meta_key Variation-level meta key.
	 * @return bool
	 */
	private static function product_has_effective_yes_meta( WC_Product $product, $parent_product, $product_meta_key, $variation_meta_key ) {
		if ( $product->is_type( 'variation' ) ) {
			return 'yes' === $product->get_meta( $variation_meta_key, true )
				|| ( $parent_product instanceof WC_Product && 'yes' === $parent_product->get_meta( $product_meta_key, true ) );
		}

		return 'yes' === $product->get_meta( $product_meta_key, true );
	}
}
