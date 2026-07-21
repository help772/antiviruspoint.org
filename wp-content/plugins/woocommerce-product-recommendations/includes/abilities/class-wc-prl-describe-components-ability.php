<?php
/**
 * Describe Product Recommendations components ability.
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
 * Describes recommendation components.
 *
 * @class    WC_PRL_Describe_Components_Ability
 * @version  4.3.4
 */
class WC_PRL_Describe_Components_Ability implements AbilityDefinition {

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-product-recommendations/describe-components';
	}

	/**
	 * Get ability registration args.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Describe recommendation components', 'woocommerce-product-recommendations' ),
			'description'         => __( 'Describe available Product Recommendations engine types, locations, filters, amplifiers, and conditions.', 'woocommerce-product-recommendations' ),
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
	 * Describe recommendation components.
	 *
	 * @param array $input Ability input.
	 * @return array
	 */
	public static function execute( array $input = array() ): array {
		$engine_type = ! empty( $input['engine_type'] ) ? sanitize_key( $input['engine_type'] ) : '';

		return array(
			'engine_types' => self::format_engine_types(),
			'locations'    => self::format_locations( $engine_type ),
			'filters'      => self::format_filters( $engine_type ),
			'amplifiers'   => self::format_amplifiers( $engine_type ),
			'conditions'   => self::format_conditions( $engine_type ),
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
				'engine_type' => array(
					'type'        => 'string',
					'description' => __( 'Restrict returned locations, filters, amplifiers, and conditions to one recommendation engine type.', 'woocommerce-product-recommendations' ),
					'enum'        => array_keys( wc_prl_get_engine_types() ),
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
				'engine_types' => array(
					'type'  => 'array',
					'items' => self::get_id_label_schema(
						array(
							'contextual' => array( 'type' => 'boolean' ),
						)
					),
				),
				'locations'    => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'id'        => array( 'type' => 'string' ),
							'label'     => array( 'type' => 'string' ),
							'cacheable' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether recommendation output at this location can be cached safely.', 'woocommerce-product-recommendations' ),
							),
							'hooks'     => array(
								'type'  => 'array',
								'items' => array(
									'type'                 => 'object',
									'properties'           => array(
										'id'           => array( 'type' => 'string' ),
										'label'        => array( 'type' => 'string' ),
										'engine_types' => array(
											'type'        => 'array',
											'description' => __( 'Engine types supported by this placement hook.', 'woocommerce-product-recommendations' ),
											'items'       => array( 'type' => 'string' ),
										),
									),
									'additionalProperties' => false,
								),
							),
						),
						'additionalProperties' => false,
					),
				),
				'filters'      => array(
					'type'  => 'array',
					'items' => self::get_component_schema(
						array(
							'needs_value' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this filter requires a configured value.', 'woocommerce-product-recommendations' ),
							),
							'static'      => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this filter can be evaluated without shopper or request context.', 'woocommerce-product-recommendations' ),
							),
						)
					),
				),
				'amplifiers'   => array(
					'type'  => 'array',
					'items' => self::get_component_schema(),
				),
				'conditions'   => array(
					'type'  => 'array',
					'items' => self::get_component_schema(
						array(
							'needs_value' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this condition requires a configured value.', 'woocommerce-product-recommendations' ),
							),
							'complexity'  => array(
								'type'        => 'integer',
								'description' => __( 'Relative evaluation cost used by Product Recommendations when ordering condition checks.', 'woocommerce-product-recommendations' ),
							),
						)
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for id/label items.
	 *
	 * @param array $extra_properties Extra schema properties.
	 * @return array
	 */
	private static function get_id_label_schema( array $extra_properties = array() ): array {
		return array(
			'type'                 => 'object',
			'properties'           => array_merge(
				array(
					'id'    => array( 'type' => 'string' ),
					'label' => array( 'type' => 'string' ),
				),
				$extra_properties
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Get schema for component items.
	 *
	 * @param array $extra_properties Extra schema properties.
	 * @return array
	 */
	private static function get_component_schema( array $extra_properties = array() ): array {
		return self::get_id_label_schema(
			array_merge(
				array(
					'engine_types' => array(
						'type'        => 'array',
						'description' => __( 'Engine types that support this component.', 'woocommerce-product-recommendations' ),
						'items'       => array( 'type' => 'string' ),
					),
				),
				$extra_properties
			)
		);
	}

	/**
	 * Format engine types.
	 *
	 * @return array
	 */
	private static function format_engine_types(): array {
		$contextual_engine_types = wc_prl_get_contextual_engine_types();
		$engine_types            = array();

		foreach ( wc_prl_get_engine_types() as $id => $label ) {
			$engine_types[] = array(
				'id'         => $id,
				'label'      => $label,
				'contextual' => in_array( $id, $contextual_engine_types, true ),
			);
		}

		return $engine_types;
	}

	/**
	 * Format locations.
	 *
	 * @param string $engine_type Optional engine type filter.
	 * @return array
	 */
	private static function format_locations( string $engine_type = '' ): array {
		$locations = array();

		foreach ( WC_PRL()->locations->get_locations( 'edit' ) as $location ) {
			$hooks = array();

			foreach ( $location->get_hooks( 'edit' ) as $hook_id => $hook_data ) {
				$engine_types = isset( $hook_data['engine_type'] ) ? (array) $hook_data['engine_type'] : array();

				if ( $engine_type && ! in_array( $engine_type, $engine_types, true ) ) {
					continue;
				}

				$hooks[] = array(
					'id'           => $hook_id,
					'label'        => isset( $hook_data['label'] ) ? $hook_data['label'] : '',
					'engine_types' => array_values( $engine_types ),
				);
			}

			if ( $engine_type && empty( $hooks ) ) {
				continue;
			}

			$locations[] = array(
				'id'        => $location->get_location_id(),
				'label'     => $location->get_title(),
				'cacheable' => $location->is_cacheable(),
				'hooks'     => $hooks,
			);
		}

		return $locations;
	}

	/**
	 * Format supported filters.
	 *
	 * @param string $engine_type Optional engine type filter.
	 * @return array
	 */
	private static function format_filters( string $engine_type = '' ): array {
		$filters = array();

		foreach ( WC_PRL()->filters->get_supported_filters( $engine_type ) as $filter ) {
			$filters[] = array(
				'id'           => $filter->get_id(),
				'label'        => $filter->get_title(),
				'engine_types' => array_values( $filter->get_supported_engine_types() ),
				'needs_value'  => (bool) $filter->needs_value,
				'static'       => $filter->is_static(),
			);
		}

		return $filters;
	}

	/**
	 * Format supported amplifiers.
	 *
	 * @param string $engine_type Optional engine type filter.
	 * @return array
	 */
	private static function format_amplifiers( string $engine_type = '' ): array {
		$amplifiers = array();

		foreach ( WC_PRL()->amplifiers->get_supported_amplifiers( $engine_type ) as $amplifier ) {
			$amplifiers[] = array(
				'id'           => $amplifier->get_id(),
				'label'        => $amplifier->get_title(),
				'engine_types' => array_values( $amplifier->get_supported_engine_types() ),
			);
		}

		return $amplifiers;
	}

	/**
	 * Format supported conditions.
	 *
	 * @param string $engine_type Optional engine type filter.
	 * @return array
	 */
	private static function format_conditions( string $engine_type = '' ): array {
		$conditions = array();

		foreach ( WC_PRL()->conditions->get_supported_conditions( $engine_type ) as $condition ) {
			$conditions[] = array(
				'id'           => $condition->get_id(),
				'label'        => $condition->get_title(),
				'engine_types' => array_values( $condition->get_supported_engine_types() ),
				'needs_value'  => (bool) $condition->needs_value,
				'complexity'   => $condition->get_complexity(),
			);
		}

		return $conditions;
	}
}
