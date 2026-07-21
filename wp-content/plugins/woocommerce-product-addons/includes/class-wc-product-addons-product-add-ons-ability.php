<?php
/**
 * WC_Product_Addons_Product_Add_Ons_Ability class
 *
 * @package WooCommerce Product Add-Ons
 * @since   8.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Addons_Product_Add_Ons_Ability ability definition.
 *
 * @version 8.3.1
 */
class WC_Product_Addons_Product_Add_Ons_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID for product add-ons.
	 */
	const NAME = 'woocommerce-product-addons/get-product-add-ons';

	/**
	 * Ability category.
	 */
	const CATEGORY = 'woocommerce';

	/**
	 * Get the ability name.
	 *
	 * @version 8.3.1
	 * @return string
	 */
	public static function get_name(): string {
		return self::NAME;
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get product add-ons', 'woocommerce-product-addons' ),
			'description'         => __( 'Retrieve the Product Add-Ons configured for a product, optionally including inherited and matching global add-ons.', 'woocommerce-product-addons' ),
			'category'            => self::CATEGORY,
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read_add_ons' ),
			'meta'                => self::get_read_only_meta(),
		);
	}

	/**
	 * Check whether the current user may read Product Add-Ons configuration.
	 *
	 * @param mixed $input Ability input.
	 * @version 8.3.1
	 * @return bool
	 */
	public static function can_read_add_ons( $input = null ) {
		return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
	}

	/**
	 * Execute the product add-ons read ability.
	 *
	 * @param array $input Ability input.
	 * @version 8.3.1
	 * @return array|WP_Error
	 */
	public static function execute( array $input ) {
		$product_id = isset( $input['product_id'] ) ? absint( $input['product_id'] ) : 0;
		$product    = wc_get_product( $product_id );

		if ( ! $product ) {
			return new WP_Error(
				'woocommerce_product_addons_ability_invalid_product',
				__( 'Product not found.', 'woocommerce-product-addons' ),
				array( 'status' => 404 )
			);
		}

		$include_global = array_key_exists( 'include_global', $input ) ? wc_string_to_bool( $input['include_global'] ) : true;
		$include_parent = array_key_exists( 'include_parent', $input ) ? wc_string_to_bool( $input['include_parent'] ) : true;
		$add_ons        = WC_Product_Addons_Ability_Output::normalize_add_ons( WC_Product_Addons_Helper::get_product_addons( $product_id, false, $include_parent, $include_global ) );

		return array(
			'product'                => array(
				'id'     => $product->get_id(),
				'name'   => $product->get_name(),
				'type'   => $product->get_type(),
				'status' => $product->get_status(),
			),
			'exclude_global_add_ons' => wc_string_to_bool( $product->get_meta( '_product_addons_exclude_global' ) ),
			'include_global'         => $include_global,
			'include_parent'         => $include_parent,
			'has_add_ons'            => ! empty( $add_ons ),
			'add_ons'                => $add_ons,
		);
	}

	/**
	 * Read-only metadata for externally visible abilities.
	 *
	 * @version 8.3.1
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
	 * Get input schema for the product add-ons ability.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'product_id'     => array(
					'type'        => 'integer',
					'description' => __( 'Product ID to read add-ons for.', 'woocommerce-product-addons' ),
					'minimum'     => 1,
				),
				'include_global' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether matching global add-ons should be included.', 'woocommerce-product-addons' ),
					'default'     => true,
				),
				'include_parent' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether parent product add-ons should be included for variations.', 'woocommerce-product-addons' ),
					'default'     => true,
				),
			),
			'required'             => array( 'product_id' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get output schema for the product add-ons ability.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'product'                => array(
					'type'                 => 'object',
					'properties'           => array(
						'id'     => array( 'type' => 'integer' ),
						'name'   => array( 'type' => 'string' ),
						'type'   => array( 'type' => 'string' ),
						'status' => array( 'type' => 'string' ),
					),
					'additionalProperties' => false,
				),
				'exclude_global_add_ons' => array( 'type' => 'boolean' ),
				'include_global'         => array( 'type' => 'boolean' ),
				'include_parent'         => array( 'type' => 'boolean' ),
				'has_add_ons'            => array( 'type' => 'boolean' ),
				'add_ons'                => array(
					'type'        => 'array',
					'description' => __( 'Add-ons configured for the product.', 'woocommerce-product-addons' ),
					'items'       => WC_Product_Addons_Ability_Output::get_add_on_schema(),
				),
			),
			'additionalProperties' => false,
		);
	}
}
