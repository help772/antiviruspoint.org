<?php
/**
 * Checkbox/radios field
 *
 * @version 8.3.0
 */
class WC_Product_Addons_Field_List extends WC_Product_Addons_Field {

	/**
	 * Validate an addon
	 *
	 * @return bool|WP_Error
	 */
	public function validate() {
		$posted = isset( $this->value ) ? $this->value : '';

		// Treat empty / blank-only arrays as missing so the required check fires consistently
		// regardless of whether the value arrives as '', [] or [''] from the request layer.
		if ( is_array( $posted ) ) {
			$posted_filtered = array_filter(
				$posted,
				static function ( $entry ) {
					return null !== $entry && '' !== $entry;
				}
			);
			if ( empty( $posted_filtered ) ) {
				$posted = '';
			}
		}

		if ( ! empty( $this->addon['required'] ) && '' === $posted ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
			/* translators: Add-on name */
			return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
		}

		if ( '' !== $posted && isset( $this->addon['options'] ) ) {

			// Index options by their sanitized labels for option lookup.
			$option_index = array();
			foreach ( $this->addon['options'] as $option ) {
				$option_index[ sanitize_title( $option['label'] ) ] = $option;
			}

			$posted_values = is_array( $posted ) ? $posted : array( $posted );

			foreach ( $posted_values as $posted_value ) {
				$key = sanitize_title( (string) $posted_value );

				// Reject values that don't map to any option, otherwise they would be silently
				// dropped downstream and bypass the "required" check (e.g. URL-based add-to-cart
				// with sentinel values from a headless frontend).
				if ( ! isset( $option_index[ $key ] ) ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
					/* translators: %s Add-on name */
					return new WP_Error( 'error', sprintf( __( '"%s" does not have a valid value.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
				}

				$matched_option = $option_index[ $key ];
				if ( isset( $matched_option['visibility'] ) && 0 === $matched_option['visibility'] ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
					/* translators: %s Selected add-on option label */
					return new WP_Error( 'error', sprintf( __( '"%s" is not available for purchase.', 'woocommerce-product-addons' ), $matched_option['label'] ) );
				}
			}
		}
		return true;
	}

	/**
	 * Process this field after being posted
	 *
	 * @return array|WP_Error Array on success and WP_Error on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data = array();
		$value          = $this->value;

		if ( empty( $value ) ) {
			return false;
		}

		if ( ! is_array( $value ) ) {
			$value = array( $value );
		}

		if ( is_array( current( $value ) ) ) {
			$value = current( $value );
		}

		foreach ( $this->addon['options'] as $option ) {
			if ( in_array( strtolower( sanitize_title( $option['label'] ) ), array_map( 'strtolower', array_values( $value ) ) ) ) {
				$cart_item_data[] = array(
					'name'       => sanitize_text_field( $this->addon['name'] ),
					'value'      => $option['label'],
					'price'      => floatval( sanitize_text_field( $this->get_option_price( $option ) ) ),
					'field_name' => $this->addon['field_name'],
					'field_type' => $this->addon['type'],
					'id'         => isset( $this->addon['id'] ) ? $this->addon['id'] : 0,
					'price_type' => $option['price_type'],
				);
			}
		}

		return $cart_item_data;
	}
}
