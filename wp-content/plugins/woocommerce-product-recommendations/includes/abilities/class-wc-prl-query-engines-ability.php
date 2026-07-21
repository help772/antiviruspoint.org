<?php
/**
 * Query Product Recommendations engines ability.
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
 * Queries recommendation engines.
 *
 * @class    WC_PRL_Query_Engines_Ability
 * @version  4.3.4
 */
class WC_PRL_Query_Engines_Ability implements AbilityDefinition {

	/**
	 * Get the ability name.
	 *
	 * @return string
	 */
	public static function get_name(): string {
		return 'woocommerce-product-recommendations/query-engines';
	}

	/**
	 * Get ability registration args.
	 *
	 * @return array
	 */
	public static function get_registration_args(): array {
		return array(
			'label'               => __( 'Query recommendation engines', 'woocommerce-product-recommendations' ),
			'description'         => __( 'Find Product Recommendations engines and return their curated configuration summaries.', 'woocommerce-product-recommendations' ),
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
	 * Query recommendation engines.
	 *
	 * @param array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function execute( array $input = array() ) {
		if ( ! empty( $input['id'] ) ) {
			$engine = new WC_PRL_Engine( absint( $input['id'] ) );

			if ( ! $engine->get_id() ) {
				return new WP_Error(
					'woocommerce_product_recommendations_engine_not_found',
					__( 'Recommendation engine not found.', 'woocommerce-product-recommendations' ),
					array( 'status' => 404 )
				);
			}

			return array(
				'engines'     => array( self::format_engine( $engine ) ),
				'total'       => 1,
				'total_pages' => 1,
				'page'        => 1,
				'per_page'    => 1,
			);
		}

		$page             = isset( $input['page'] ) ? max( 1, absint( $input['page'] ) ) : 1;
		$per_page         = isset( $input['per_page'] ) ? min( 100, max( 1, absint( $input['per_page'] ) ) ) : 10;
		$status           = ! empty( $input['status'] ) ? sanitize_key( $input['status'] ) : array( 'publish', 'draft', 'pending', 'private' );
		$orderby          = ! empty( $input['orderby'] ) && in_array( $input['orderby'], array( 'date', 'id', 'title', 'modified' ), true ) ? $input['orderby'] : 'date';
		$wp_query_orderby = 'id' === $orderby ? 'ID' : $orderby;
		$order            = ! empty( $input['order'] ) && 'ASC' === strtoupper( $input['order'] ) ? 'ASC' : 'DESC';

		$query_args = array(
			'post_type'      => 'prl_engine',
			'post_status'    => $status,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => $wp_query_orderby,
			'order'          => $order,
		);

		if ( ! empty( $input['search'] ) ) {
			$query_args['s'] = sanitize_text_field( $input['search'] );
		}

		if ( ! empty( $input['type'] ) ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Engine type is stored in the prl_engine_type taxonomy.
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'prl_engine_type',
					'field'    => 'slug',
					'terms'    => array( sanitize_key( $input['type'] ) ),
				),
			);
		}

		$query   = new WP_Query( $query_args );
		$engines = array();

		foreach ( $query->posts as $post ) {
			$engine = new WC_PRL_Engine( $post );

			if ( $engine->get_id() ) {
				$engines[] = self::format_engine( $engine );
			}
		}

		return array(
			'engines'     => $engines,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
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
				'id'       => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'type'     => array(
					'type'        => 'string',
					'description' => __( 'Recommendation engine type, such as cart, product, archive, or order.', 'woocommerce-product-recommendations' ),
					'enum'        => array_keys( wc_prl_get_engine_types() ),
				),
				'status'   => array(
					'type' => 'string',
					'enum' => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
				),
				'search'   => array( 'type' => 'string' ),
				'orderby'  => array(
					'type'        => 'string',
					'description' => __( 'Sort field for engine results. The id value maps to the engine post ID.', 'woocommerce-product-recommendations' ),
					'enum'        => array( 'date', 'id', 'title', 'modified' ),
					'default'     => 'date',
				),
				'order'    => array(
					'type'    => 'string',
					'enum'    => array( 'ASC', 'DESC' ),
					'default' => 'DESC',
				),
				'page'     => array(
					'type'    => 'integer',
					'default' => 1,
					'minimum' => 1,
				),
				'per_page' => array(
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
				'engines'     => array(
					'type'  => 'array',
					'items' => self::get_engine_schema(),
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
	 * Get engine item output schema.
	 *
	 * @return array
	 */
	private static function get_engine_schema(): array {
		$component_schema = array(
			'type'                 => 'object',
			'properties'           => array(
				'id'         => array( 'type' => 'string' ),
				'label'      => array( 'type' => 'string' ),
				'contextual' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether this filter depends on the current page, product, cart, or order context.', 'woocommerce-product-recommendations' ),
				),
				'settings'   => array(
					'type'  => 'array',
					'items' => self::get_setting_schema(),
				),
			),
			'additionalProperties' => false,
		);

		$amplifier_schema = array(
			'type'                 => 'object',
			'properties'           => array(
				'id'         => array( 'type' => 'string' ),
				'label'      => array( 'type' => 'string' ),
				'contextual' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether this amplifier depends on the current page, product, cart, or order context.', 'woocommerce-product-recommendations' ),
				),
				'weight'     => array(
					'type'        => 'number',
					'description' => __( 'Relative strength of this amplifier in recommendation scoring.', 'woocommerce-product-recommendations' ),
				),
				'settings'   => array(
					'type'  => 'array',
					'items' => self::get_setting_schema(),
				),
			),
			'additionalProperties' => false,
		);

		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                        => array( 'type' => 'integer' ),
				'name'                      => array( 'type' => 'string' ),
				'slug'                      => array( 'type' => 'string' ),
				'status'                    => array( 'type' => 'string' ),
				'type'                      => array(
					'type'        => 'string',
					'description' => __( 'Recommendation engine type identifier.', 'woocommerce-product-recommendations' ),
				),
				'type_label'                => array(
					'type'        => 'string',
					'description' => __( 'Human-readable label for the engine type.', 'woocommerce-product-recommendations' ),
				),
				'description'               => array( 'type' => 'string' ),
				'short_description'         => array( 'type' => 'string' ),
				'date_created'              => array( 'type' => 'string' ),
				'date_modified'             => array( 'type' => 'string' ),
				'has_contextual_filters'    => array(
					'type'        => 'boolean',
					'description' => __( 'Whether any configured filters depend on the current page, product, cart, or order context.', 'woocommerce-product-recommendations' ),
				),
				'has_contextual_amplifiers' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether any configured amplifiers depend on the current page, product, cart, or order context.', 'woocommerce-product-recommendations' ),
				),
				'filters'                   => array(
					'type'  => 'array',
					'items' => $component_schema,
				),
				'amplifiers'                => array(
					'type'  => 'array',
					'items' => $amplifier_schema,
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
					'description' => __( 'Normalized string value for this setting.', 'woocommerce-product-recommendations' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Format an engine for ability output.
	 *
	 * @param WC_PRL_Engine $engine Recommendation engine.
	 * @return array
	 */
	private static function format_engine( WC_PRL_Engine $engine ): array {
		return array(
			'id'                        => $engine->get_id(),
			'name'                      => $engine->get_name(),
			'slug'                      => $engine->get_slug(),
			'status'                    => $engine->get_status(),
			'type'                      => $engine->get_type(),
			'type_label'                => wc_prl_get_engine_type_label( $engine->get_type() ),
			'description'               => wp_strip_all_tags( $engine->get_description() ),
			'short_description'         => wp_strip_all_tags( $engine->get_short_description() ),
			'date_created'              => self::format_datetime( $engine->get_date_created() ),
			'date_modified'             => self::format_datetime( $engine->get_date_modified() ),
			'has_contextual_filters'    => $engine->has_contextual_filters(),
			'has_contextual_amplifiers' => $engine->has_contextual_amplifiers(),
			'filters'                   => self::format_filters( $engine->get_filters_data() ),
			'amplifiers'                => self::format_amplifiers( $engine->get_amplifiers_data() ),
		);
	}

	/**
	 * Format filter configuration.
	 *
	 * @param array $filters_data Engine filter data.
	 * @return array
	 */
	private static function format_filters( array $filters_data ): array {
		$filters = array();

		foreach ( $filters_data as $filter_data ) {
			if ( empty( $filter_data['id'] ) ) {
				continue;
			}

			$filter = WC_PRL()->filters->get_filter( $filter_data['id'] );

			$filters[] = array(
				'id'         => sanitize_key( $filter_data['id'] ),
				'label'      => $filter ? $filter->get_title() : '',
				'contextual' => isset( $filter_data['context'] ) && 'yes' === $filter_data['context'],
				'settings'   => self::format_settings( $filter_data, array( 'id', 'context' ) ),
			);
		}

		return $filters;
	}

	/**
	 * Format amplifier configuration.
	 *
	 * @param array $amplifiers_data Engine amplifier data.
	 * @return array
	 */
	private static function format_amplifiers( array $amplifiers_data ): array {
		$amplifiers = array();

		foreach ( $amplifiers_data as $amplifier_data ) {
			if ( empty( $amplifier_data['id'] ) ) {
				continue;
			}

			$amplifier = WC_PRL()->amplifiers->get_amplifier( $amplifier_data['id'] );

			$amplifiers[] = array(
				'id'         => sanitize_key( $amplifier_data['id'] ),
				'label'      => $amplifier ? $amplifier->get_title() : '',
				'contextual' => isset( $amplifier_data['context'] ) && 'yes' === $amplifier_data['context'],
				'weight'     => isset( $amplifier_data['weight'] ) ? (float) $amplifier_data['weight'] : 1.0,
				'settings'   => self::format_settings( $amplifier_data, array( 'id', 'context', 'weight' ) ),
			);
		}

		return $amplifiers;
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
	 * Format date/time values as ISO8601 strings.
	 *
	 * @param mixed $datetime Date/time value.
	 * @return string
	 */
	private static function format_datetime( $datetime ): string {
		if ( $datetime instanceof WC_DateTime ) {
			return $datetime->date( DATE_ATOM );
		}

		return '';
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
