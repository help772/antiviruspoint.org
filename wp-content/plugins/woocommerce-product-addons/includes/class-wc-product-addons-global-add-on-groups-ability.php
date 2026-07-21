<?php
/**
 * WC_Product_Addons_Global_Add_On_Groups_Ability class
 *
 * @package WooCommerce Product Add-Ons
 * @since   8.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Addons_Global_Add_On_Groups_Ability ability definition.
 *
 * @version 8.3.1
 */
class WC_Product_Addons_Global_Add_On_Groups_Ability implements \Automattic\WooCommerce\Abilities\AbilityDefinition {

	/**
	 * Ability ID for global add-on groups.
	 */
	const NAME = 'woocommerce-product-addons/get-global-add-on-groups';

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
			'label'               => __( 'Get global add-on groups', 'woocommerce-product-addons' ),
			'description'         => __( 'Retrieve configured global Product Add-Ons groups, optionally by group ID.', 'woocommerce-product-addons' ),
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
	 * Execute the global add-on groups read ability.
	 *
	 * @param array $input Ability input.
	 * @version 8.3.1
	 * @return array|WP_Error
	 */
	public static function execute( array $input = array() ) {
		self::load_v2_api_classes();

		if ( ! empty( $input['id'] ) ) {
			$group = self::format_global_group_by_id( absint( $input['id'] ) );

			if ( is_wp_error( $group ) ) {
				return new WP_Error(
					'woocommerce_product_addons_ability_invalid_global_group',
					__( 'Global add-on group not found.', 'woocommerce-product-addons' ),
					array( 'status' => 404 )
				);
			}

			return array(
				'global_add_on_groups' => array( $group ),
				'total_pages'          => 1,
				'page'                 => 1,
				'per_page'             => 1,
			);
		}

		$page     = isset( $input['page'] ) ? absint( $input['page'] ) : 1;
		$per_page = isset( $input['per_page'] ) ? absint( $input['per_page'] ) : 25;
		$page     = max( 1, $page );
		$per_page = max( 1, min( 100, $per_page ) );

		$all_groups   = WC_Product_Addons_Api_V2_Global_Group::get_all();
		$total_groups = count( $all_groups );
		$total_pages  = $total_groups > 0 ? (int) ceil( $total_groups / $per_page ) : 0;
		$all_groups   = array_slice( $all_groups, ( $page - 1 ) * $per_page, $per_page );
		$groups       = array();

		foreach ( $all_groups as $group ) {
			$groups[] = self::format_global_group( $group );
		}

		return array(
			'global_add_on_groups' => $groups,
			'total_pages'          => $total_pages,
			'page'                 => $page,
			'per_page'             => $per_page,
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
	 * Format a global group for ability output.
	 *
	 * @param WC_Product_Addons_Api_V2_Global_Group $group Global add-on group.
	 * @version 8.3.1
	 * @return array
	 */
	private static function format_global_group( WC_Product_Addons_Api_V2_Global_Group $group ) {
		return array(
			'id'                      => $group->get_id(),
			'name'                    => $group->get_name(),
			'priority'                => $group->get_priority(),
			'applies_to_all_products' => self::global_group_applies_to_all_products( $group->get_id() ),
			'restrict_to_categories'  => $group->get_restrict_to_categories(),
			'fields'                  => WC_Product_Addons_Ability_Output::normalize_add_ons( $group->get_fields() ),
		);
	}

	/**
	 * Format a global group by ID for ability output.
	 *
	 * @param int $group_id Global add-on group ID.
	 * @version 8.3.1
	 * @return array|WP_Error
	 */
	private static function format_global_group_by_id( $group_id ) {
		if ( ! WC_Product_Addons_Api_V2_Global_Group::is_a_global_group_id( $group_id ) ) {
			return new WP_Error(
				'woocommerce_product_addons_ability_invalid_global_group',
				__( 'Global add-on group not found.', 'woocommerce-product-addons' ),
				array( 'status' => 404 )
			);
		}

		return self::format_global_group( new WC_Product_Addons_Api_V2_Global_Group( $group_id ) );
	}

	/**
	 * Check whether a global add-on group applies to all products.
	 *
	 * @param int $group_id Global add-on group ID.
	 * @version 8.3.1
	 * @return bool
	 */
	private static function global_group_applies_to_all_products( $group_id ) {
		return '1' === (string) get_post_meta( $group_id, '_all_products', true );
	}

	/**
	 * Load v2 API model classes used by the ability.
	 *
	 * @version 8.3.1
	 */
	private static function load_v2_api_classes() {
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/api/v2/class-wc-product-addons-api-v2-abstract-group.php';
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/api/v2/class-wc-product-addons-api-v2-validation.php';
		require_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/api/v2/class-wc-product-addons-api-v2-global-group.php';
	}

	/**
	 * Get input schema for the global group ability.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	private static function get_input_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'       => array(
					'type'        => 'integer',
					'description' => __( 'Optional global add-on group ID. When omitted, groups are returned as a paged collection.', 'woocommerce-product-addons' ),
					'minimum'     => 1,
				),
				'page'     => array(
					'type'        => 'integer',
					'description' => __( 'Result page for collection queries.', 'woocommerce-product-addons' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page' => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of groups to return for collection queries.', 'woocommerce-product-addons' ),
					'default'     => 25,
					'minimum'     => 1,
					'maximum'     => 100,
				),
			),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Get output schema for the global group ability.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	private static function get_output_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'global_add_on_groups' => array(
					'type'        => 'array',
					'description' => __( 'Global Product Add-Ons groups for the requested page.', 'woocommerce-product-addons' ),
					'items'       => self::get_global_add_on_group_schema(),
				),
				'total_pages'          => array( 'type' => 'integer' ),
				'page'                 => array( 'type' => 'integer' ),
				'per_page'             => array( 'type' => 'integer' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get global add-on group output schema.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	private static function get_global_add_on_group_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                      => array( 'type' => 'integer' ),
				'name'                    => array( 'type' => 'string' ),
				'priority'                => array( 'type' => 'integer' ),
				'applies_to_all_products' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether this global add-on group applies to all products instead of only selected product categories.', 'woocommerce-product-addons' ),
				),
				'restrict_to_categories'  => array(
					'type'        => 'array',
					'description' => __( 'Product categories this global add-on group is restricted to when it does not apply to all products.', 'woocommerce-product-addons' ),
					'items'       => array(
						'type'                 => 'object',
						'properties'           => array(
							'id'   => array( 'type' => 'integer' ),
							'name' => array( 'type' => 'string' ),
						),
						'additionalProperties' => false,
					),
				),
				'fields'                  => array(
					'type'  => 'array',
					'items' => WC_Product_Addons_Ability_Output::get_add_on_schema(),
				),
			),
			'additionalProperties' => false,
		);
	}
}
