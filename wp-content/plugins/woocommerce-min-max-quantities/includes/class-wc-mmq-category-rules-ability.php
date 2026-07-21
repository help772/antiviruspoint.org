<?php
/**
 * WC_MMQ_Category_Rules_Ability class
 *
 * @package WooCommerce Min/Max Quantities
 * @since   5.2.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MMQ_Category_Rules_Ability ability definition.
 *
 * @version 5.2.9
 */
class WC_MMQ_Category_Rules_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-min-max-quantities/get-category-rules';
	}

	/**
	 * Get the ability registration arguments.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Get category Min/Max Quantities rules', 'woocommerce-min-max-quantities' ),
			'description'         => __( 'Returns the Min/Max Quantities rules configured directly on a product category.', 'woocommerce-min-max-quantities' ),
			'category'            => 'woocommerce',
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => self::get_meta(),
		);
	}

	/**
	 * Checks whether the current user can read category rules.
	 *
	 * @param array $input Ability input.
	 * @return bool
	 */
	public static function can_read( $input = array() ) {
		return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_product_terms' );
	}

	/**
	 * Returns Min/Max Quantities rules configured on a product category.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( $input = array() ) {
		$category_id = absint( $input['category_id'] ?? 0 );
		$category    = $category_id ? get_term( $category_id, 'product_cat' ) : null;

		if ( ! $category || is_wp_error( $category ) ) {
			return new WP_Error(
				'woocommerce_min_max_quantities_category_not_found',
				__( 'The requested product category was not found.', 'woocommerce-min-max-quantities' ),
				array( 'status' => 404 )
			);
		}

		return array(
			'category_id'    => (int) $category->term_id,
			'taxonomy'       => $category->taxonomy,
			'name'           => $category->name,
			'slug'           => $category->slug,
			'quantity_rules' => array(
				'minimum_quantity'  => absint( get_term_meta( $category->term_id, 'minimum_allowed_quantity', true ) ),
				'maximum_quantity'  => absint( get_term_meta( $category->term_id, 'maximum_allowed_quantity', true ) ),
				'group_of_quantity' => absint( get_term_meta( $category->term_id, 'group_of_quantity', true ) ),
			),
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
				'category_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Product category term ID.', 'woocommerce-min-max-quantities' ),
				),
			),
			'required'             => array( 'category_id' ),
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
				'category_id'    => array(
					'type'        => 'integer',
					'description' => __( 'Product category term ID these rules are configured on.', 'woocommerce-min-max-quantities' ),
				),
				'taxonomy'       => array(
					'type'        => 'string',
					'description' => __( 'Taxonomy for the configured category rules.', 'woocommerce-min-max-quantities' ),
				),
				'name'           => array(
					'type'        => 'string',
					'description' => __( 'Product category name.', 'woocommerce-min-max-quantities' ),
				),
				'slug'           => array(
					'type'        => 'string',
					'description' => __( 'Product category slug.', 'woocommerce-min-max-quantities' ),
				),
				'quantity_rules' => self::get_quantity_rules_schema(),
			),
			'required'             => array( 'category_id', 'taxonomy', 'name', 'slug', 'quantity_rules' ),
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
}
