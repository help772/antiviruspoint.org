<?php
/**
 * WC_Product_Addons_Ability_Output class
 *
 * @package WooCommerce Product Add-Ons
 * @since   8.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared output helpers for Product Add-Ons abilities.
 *
 * @version 8.3.1
 */
class WC_Product_Addons_Ability_Output {

	/**
	 * Normalize add-ons before returning ability output.
	 *
	 * Product Add-Ons data can be extended or historically malformed. Only expose
	 * the reviewed Product Add-Ons configuration keys promised by the ability schema.
	 *
	 * @param mixed $add_ons Add-ons.
	 * @version 8.3.1
	 * @return array
	 */
	public static function normalize_add_ons( $add_ons ) {
		if ( ! is_array( $add_ons ) ) {
			return array();
		}

		$normalized = array();

		foreach ( $add_ons as $add_on ) {
			if ( ! is_array( $add_on ) ) {
				continue;
			}

			$normalized[] = self::normalize_add_on( $add_on );
		}

		return $normalized;
	}

	/**
	 * Get add-on output schema.
	 *
	 * @version 8.3.1
	 * @return array
	 */
	public static function get_add_on_schema() {
		return array(
			'type'                 => 'object',
			'properties'           => array(
				'id'                 => array( 'type' => 'integer' ),
				'name'               => array( 'type' => 'string' ),
				'type'               => array( 'type' => 'string' ),
				'field_name'         => array( 'type' => 'string' ),
				'title_format'       => array( 'type' => 'string' ),
				'default'            => array( 'type' => 'string' ),
				'description_enable' => array( 'type' => 'boolean' ),
				'description'        => array( 'type' => 'string' ),
				'placeholder_enable' => array( 'type' => 'boolean' ),
				'placeholder'        => array( 'type' => 'string' ),
				'display'            => array( 'type' => 'string' ),
				'position'           => array( 'type' => 'integer' ),
				'required'           => array( 'type' => 'boolean' ),
				'restrictions'       => array( 'type' => 'boolean' ),
				'restrictions_type'  => array( 'type' => 'string' ),
				'adjust_price'       => array( 'type' => 'boolean' ),
				'price'              => array( 'type' => 'string' ),
				'price_type'         => array( 'type' => 'string' ),
				'min'                => array( 'type' => 'string' ),
				'max'                => array( 'type' => 'string' ),
				'limit_file_types'   => array( 'type' => 'boolean' ),
				'allowed_file_types' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'string' ),
				),
				'options'            => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(
							'label'      => array( 'type' => 'string' ),
							'price'      => array( 'type' => 'string' ),
							'price_type' => array( 'type' => 'string' ),
							'image'      => array( 'type' => 'integer' ),
							'visibility' => array( 'type' => 'boolean' ),
							'min'        => array( 'type' => 'string' ),
							'max'        => array( 'type' => 'string' ),
						),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Normalize a single add-on before returning ability output.
	 *
	 * @param array $add_on Add-on.
	 * @version 8.3.1
	 * @return array
	 */
	private static function normalize_add_on( array $add_on ) {
		$normalized = array();

		foreach ( self::get_add_on_output_schema_types() as $key => $type ) {
			if ( ! array_key_exists( $key, $add_on ) ) {
				continue;
			}

			$value = self::normalize_schema_value( $add_on[ $key ], $type );

			if ( null !== $value ) {
				$normalized[ $key ] = $value;
			}
		}

		return $normalized;
	}

	/**
	 * Normalize add-on options before returning ability output.
	 *
	 * @param mixed $options Add-on options.
	 * @version 8.3.1
	 * @return array
	 */
	private static function normalize_add_on_options( $options ) {
		if ( ! is_array( $options ) ) {
			return array();
		}

		if ( ! wp_is_numeric_array( $options ) ) {
			$options = array( $options );
		}

		return array_values(
			array_filter(
				array_map(
					array( __CLASS__, 'normalize_add_on_option' ),
					array_filter( $options, 'is_array' )
				)
			)
		);
	}

	/**
	 * Normalize a single add-on option before returning ability output.
	 *
	 * @param array $option Add-on option.
	 * @version 8.3.1
	 * @return array
	 */
	private static function normalize_add_on_option( array $option ) {
		$normalized = array();

		foreach ( self::get_add_on_option_output_schema_types() as $key => $type ) {
			if ( ! array_key_exists( $key, $option ) ) {
				continue;
			}

			$value = self::normalize_schema_value( $option[ $key ], $type );

			if ( null !== $value ) {
				$normalized[ $key ] = $value;
			}
		}

		return $normalized;
	}

	/**
	 * Get output field types for add-ons.
	 *
	 * @version 8.3.1
	 * @return string[]
	 */
	private static function get_add_on_output_schema_types() {
		return array(
			'id'                 => 'integer',
			'name'               => 'string',
			'type'               => 'string',
			'field_name'         => 'string',
			'title_format'       => 'string',
			'default'            => 'string',
			'description_enable' => 'boolean',
			'description'        => 'string',
			'placeholder_enable' => 'boolean',
			'placeholder'        => 'string',
			'display'            => 'string',
			'position'           => 'integer',
			'required'           => 'boolean',
			'restrictions'       => 'boolean',
			'restrictions_type'  => 'string',
			'adjust_price'       => 'boolean',
			'price'              => 'string',
			'price_type'         => 'string',
			'min'                => 'string',
			'max'                => 'string',
			'limit_file_types'   => 'boolean',
			'allowed_file_types' => 'string_array',
			'options'            => 'options_array',
		);
	}

	/**
	 * Get output field types for add-on options.
	 *
	 * @version 8.3.1
	 * @return string[]
	 */
	private static function get_add_on_option_output_schema_types() {
		return array(
			'label'      => 'string',
			'price'      => 'string',
			'price_type' => 'string',
			'image'      => 'integer',
			'visibility' => 'boolean',
			'min'        => 'string',
			'max'        => 'string',
		);
	}

	/**
	 * Normalize a value for a schema type.
	 *
	 * @param mixed  $value Value.
	 * @param string $type Schema type.
	 * @version 8.3.1
	 * @return mixed|null
	 */
	private static function normalize_schema_value( $value, $type ) {
		switch ( $type ) {
			case 'boolean':
				if ( is_scalar( $value ) || null === $value ) {
					return wc_string_to_bool( $value );
				}
				return null;
			case 'integer':
				if ( is_numeric( $value ) || is_bool( $value ) ) {
					return (int) $value;
				}
				return null;
			case 'options_array':
				return self::normalize_add_on_options( $value );
			case 'string':
				if ( is_scalar( $value ) || null === $value ) {
					return (string) $value;
				}
				return null;
			case 'string_array':
				if ( is_scalar( $value ) ) {
					$value = explode( ',', (string) $value );
				}

				if ( ! is_array( $value ) ) {
					return null;
				}

				return array_values(
					array_map(
						'strval',
						array_filter( $value, 'is_scalar' )
					)
				);
		}

		return null;
	}
}
