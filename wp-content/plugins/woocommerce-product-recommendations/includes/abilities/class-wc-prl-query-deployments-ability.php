<?php
/**
 * Query Product Recommendations deployments ability.
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.3.4
 * @version  4.3.4
 */

use Automattic\WooCommerce\Abilities\AbilityDefinition;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Queries recommendation deployments.
 *
 * @class    WC_PRL_Query_Deployments_Ability
 * @version  4.3.4
 */
class WC_PRL_Query_Deployments_Ability implements AbilityDefinition {

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-product-recommendations/query-deployments';
	}

	/**
	 * Get ability registration args.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Query recommendation deployments', 'woocommerce-product-recommendations' ),
			'description'         => __( 'Find Product Recommendations deployments and return their curated placement summaries.', 'woocommerce-product-recommendations' ),
			'category'            => 'woocommerce',
			'input_schema'        => self::get_input_schema(),
			'output_schema'       => self::get_output_schema(),
			'execute_callback'    => array( __CLASS__, 'execute' ),
			'permission_callback' => array( __CLASS__, 'can_read' ),
			'meta'                => array(
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
			),
		);
	}

	/**
	 * Query recommendation deployments.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( array $input = array() ) {
		if ( ! empty( $input['id'] ) ) {
			$deployment = WC_PRL()->db->deployment->get( absint( $input['id'] ) );

			if ( ! $deployment ) {
				return new WP_Error(
					'woocommerce_product_recommendations_deployment_not_found',
					__( 'Recommendation deployment not found.', 'woocommerce-product-recommendations' ),
					array( 'status' => 404 )
				);
			}

			return array(
				'deployments' => array( self::format_deployment( $deployment ) ),
				'total'       => 1,
				'total_pages' => 1,
				'page'        => 1,
				'per_page'    => 1,
			);
		}

		$page     = isset( $input['page'] ) ? max( 1, absint( $input['page'] ) ) : 1;
		$per_page = isset( $input['per_page'] ) ? min( 100, max( 1, absint( $input['per_page'] ) ) ) : 10;
		$orderby  = ! empty( $input['orderby'] ) && in_array( $input['orderby'], array( 'id', 'display_order' ), true ) ? $input['orderby'] : 'display_order';
		$order    = ! empty( $input['order'] ) && 'DESC' === strtoupper( $input['order'] ) ? 'DESC' : 'ASC';

		$query_args = array(
			'return'   => 'objects',
			'limit'    => $per_page,
			'offset'   => ( $page - 1 ) * $per_page,
			'order_by' => array(
				$orderby => $order,
				'id'     => 'ASC',
			),
		);

		if ( isset( $input['active'] ) ) {
			$query_args['active'] = rest_sanitize_boolean( $input['active'] ) ? 'on' : 'off';
		}

		foreach ( array( 'engine_id', 'location_id', 'hook' ) as $field ) {
			if ( ! empty( $input[ $field ] ) ) {
				$query_args[ $field ] = 'engine_id' === $field ? absint( $input[ $field ] ) : wc_clean( $input[ $field ] );
			}
		}

		$count_args           = $query_args;
		$count_args['return'] = 'count';
		unset( $count_args['limit'], $count_args['offset'], $count_args['order_by'] );

		$total       = (int) WC_PRL()->db->deployment->query( $count_args );
		$deployments = WC_PRL()->db->deployment->query( $query_args );

		return array(
			'deployments' => array_map( array( __CLASS__, 'format_deployment' ), $deployments ),
			'total'       => $total,
			'total_pages' => $per_page > 0 ? (int) ceil( $total / $per_page ) : 0,
			'page'        => $page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Check whether the current user can read recommendation configuration.
	 *
	 * @param mixed $input Ability input.
	 * @return bool
	 */
	public static function can_read( $input = array() ): bool {
		return current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Get ability input schema.
	 *
	 * @return array
	 */
	private static function get_input_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'          => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'active'      => array( 'type' => 'boolean' ),
				'engine_id'   => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'location_id' => array(
					'type'        => 'string',
					'description' => __( 'Recommendation location identifier, such as shop or product_details.', 'woocommerce-product-recommendations' ),
				),
				'hook'        => array(
					'type'        => 'string',
					'description' => __( 'WordPress or WooCommerce hook used by the deployment placement.', 'woocommerce-product-recommendations' ),
				),
				'orderby'     => array(
					'type'        => 'string',
					'description' => __( 'Sort field for deployment results. display_order follows the storefront placement order.', 'woocommerce-product-recommendations' ),
					'enum'        => array( 'display_order', 'id' ),
					'default'     => 'display_order',
				),
				'order'       => array(
					'type'    => 'string',
					'enum'    => array( 'ASC', 'DESC' ),
					'default' => 'ASC',
				),
				'page'        => array(
					'type'    => 'integer',
					'default' => 1,
					'minimum' => 1,
				),
				'per_page'    => array(
					'type'    => 'integer',
					'default' => 10,
					'minimum' => 1,
					'maximum' => 100,
				),
			),
			'additionalProperties' => false,
			'default'              => array(),
		);
	}

	/**
	 * Get ability output schema.
	 *
	 * @return array
	 */
	private static function get_output_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'deployments' => array(
					'type'  => 'array',
					'items' => self::get_deployment_schema(),
				),
				'total'       => array( 'type' => 'integer' ),
				'total_pages' => array( 'type' => 'integer' ),
				'page'        => array( 'type' => 'integer' ),
				'per_page'    => array( 'type' => 'integer' ),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get deployment item output schema.
	 *
	 * @return array
	 */
	private static function get_deployment_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                => array( 'type' => 'integer' ),
				'active'            => array( 'type' => 'boolean' ),
				'engine_id'         => array( 'type' => 'integer' ),
				'engine_name'       => array( 'type' => 'string' ),
				'engine_type'       => array( 'type' => 'string' ),
				'engine_type_label' => array( 'type' => 'string' ),
				'title'             => array( 'type' => 'string' ),
				'description'       => array( 'type' => 'string' ),
				'display_order'     => array(
					'type'        => 'integer',
					'description' => __( 'Relative order for deployments rendered in the same location.', 'woocommerce-product-recommendations' ),
				),
				'columns'           => array(
					'type'        => 'integer',
					'description' => __( 'Number of product columns configured for this deployment.', 'woocommerce-product-recommendations' ),
				),
				'limit'             => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of recommendations this deployment renders.', 'woocommerce-product-recommendations' ),
				),
				'location_id'       => array(
					'type'        => 'string',
					'description' => __( 'Recommendation location identifier.', 'woocommerce-product-recommendations' ),
				),
				'location_label'    => array( 'type' => 'string' ),
				'hook'              => array(
					'type'        => 'string',
					'description' => __( 'WordPress or WooCommerce hook used by the deployment placement.', 'woocommerce-product-recommendations' ),
				),
				'hook_label'        => array( 'type' => 'string' ),
				'conditions'        => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'id'       => array( 'type' => 'string' ),
							'label'    => array( 'type' => 'string' ),
							'settings' => array(
								'type'  => 'array',
								'items' => self::get_setting_schema(),
							),
						),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get normalized settings schema.
	 *
	 * @return array
	 */
	private static function get_setting_schema(): array {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'key'   => array( 'type' => 'string' ),
				'value' => array(
					'type'        => 'string',
					'description' => __( 'Normalized string value for this condition setting.', 'woocommerce-product-recommendations' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Format a deployment for ability output.
	 *
	 * @param WC_PRL_Deployment_Data $deployment Recommendation deployment.
	 * @return array
	 */
	public static function format_deployment( WC_PRL_Deployment_Data $deployment ): array {
		$engine    = new WC_PRL_Engine( $deployment->get_engine_id() );
		$location  = WC_PRL()->locations->get_location( $deployment->get_location_id() );
		$hook_data = $location ? $location->get_hook_data( $deployment->get_hook() ) : array();

		return array(
			'id'                => $deployment->get_id(),
			'active'            => $deployment->is_active(),
			'engine_id'         => $deployment->get_engine_id(),
			'engine_name'       => $engine->get_id() ? $engine->get_name() : '',
			'engine_type'       => $deployment->get_engine_type(),
			'engine_type_label' => wc_prl_get_engine_type_label( $deployment->get_engine_type() ),
			'title'             => $deployment->get_title(),
			'description'       => wp_strip_all_tags( $deployment->get_description() ),
			'display_order'     => $deployment->get_display_order(),
			'columns'           => $deployment->get_columns(),
			'limit'             => $deployment->get_limit(),
			'location_id'       => $deployment->get_location_id(),
			'location_label'    => $location ? $location->get_title() : '',
			'hook'              => $deployment->get_hook(),
			'hook_label'        => isset( $hook_data['label'] ) ? $hook_data['label'] : '',
			'conditions'        => self::format_conditions( $deployment->get_conditions_data() ),
		);
	}

	/**
	 * Format deployment conditions.
	 *
	 * @param array $conditions_data Conditions data.
	 * @return array
	 */
	private static function format_conditions( array $conditions_data ): array {
		$conditions = array();

		foreach ( $conditions_data as $condition_data ) {
			if ( empty( $condition_data['id'] ) ) {
				continue;
			}

			$condition = WC_PRL()->conditions->get_condition( $condition_data['id'] );

			$conditions[] = array(
				'id'       => sanitize_key( $condition_data['id'] ),
				'label'    => $condition ? $condition->get_title() : '',
				'settings' => self::format_settings( $condition_data, array( 'id' ) ),
			);
		}

		return $conditions;
	}

	/**
	 * Format component settings as string key/value pairs.
	 *
	 * @param array $data Component data.
	 * @param array $excluded_keys Keys omitted from settings.
	 * @return array
	 */
	private static function format_settings( array $data, array $excluded_keys ): array {
		$settings = array();

		foreach ( $data as $key => $value ) {
			if ( in_array( $key, $excluded_keys, true ) ) {
				continue;
			}

			$settings[] = array(
				'key'   => sanitize_key( $key ),
				'value' => self::stringify_value( $value ),
			);
		}

		return $settings;
	}

	/**
	 * Convert a setting value to a safe string.
	 *
	 * @param mixed $value Setting value.
	 * @return string
	 */
	private static function stringify_value( $value ): string {
		if ( is_bool( $value ) ) {
			return $value ? 'yes' : 'no';
		}

		if ( is_scalar( $value ) || is_null( $value ) ) {
			return (string) $value;
		}

		$encoded = wp_json_encode( $value );

		return is_string( $encoded ) ? $encoded : '';
	}
}
