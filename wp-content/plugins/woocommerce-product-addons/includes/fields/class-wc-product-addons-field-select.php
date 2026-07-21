<?php
/**
 * Select field
 *
 * @version 8.3.0
 */
class WC_Product_Addons_Field_Select extends WC_Product_Addons_Field {

	/**
	 * Validate an addon
	 *
	 * @return bool pass or fail, or WP_Error
	 */
	public function validate() {
		$posted = isset( $this->value ) ? $this->value : '';

		if ( ! empty( $this->addon['required'] ) && '' === $posted ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
			/* translators: Add-on name */
			return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
		}

		if ( '' !== $posted && isset( $this->addon['options'] ) ) {

			// Posted value format is "label-INDEX" (matches get_cart_item_data).
			$matched_option = null;
			$loop           = 0;

			foreach ( $this->addon['options'] as $option ) {
				++$loop;
				if ( sanitize_title( $option['label'] ) . '-' . $loop === $posted ) {
					$matched_option = $option;
					break;
				}
			}

			// Reject values that don't map to any option, otherwise they would be silently dropped
			// downstream and bypass the "required" check (e.g. URL-based add-to-cart with sentinel
			// values from a headless frontend).
			if ( null === $matched_option ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
				/* translators: %s Add-on name */
				return new WP_Error( 'error', sprintf( __( '"%s" does not have a valid value.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
			}

			if ( isset( $matched_option['visibility'] ) && 0 === $matched_option['visibility'] ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
				/* translators: %s Selected add-on option name */
				return new WP_Error( 'error', sprintf( __( '"%s" is not available for purchase.', 'woocommerce-product-addons' ), $matched_option['label'] ) );
			}
		}
		return true;
	}

	/**
	 * Process this field after being posted
	 *
	 * @return array on success, WP_ERROR on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data = array();

		if ( empty( $this->value ) ) {
			return false;
		}

		$chosen_option = '';
		$loop          = 0;

		foreach ( $this->addon['options'] as $option ) {
			++$loop;
			if ( sanitize_title( $option['label'] ) . '-' . $loop === $this->value ) {
				$chosen_option = $option;
				break;
			}
		}

		if ( ! $chosen_option ) {
			return false;
		}

		$cart_item_data[] = array(
			'name'       => sanitize_text_field( $this->addon['name'] ),
			'value'      => $chosen_option['label'],
			'price'      => floatval( sanitize_text_field( $this->get_option_price( $chosen_option ) ) ),
			'field_name' => $this->addon['field_name'],
			'field_type' => $this->addon['type'],
			'id'         => isset( $this->addon['id'] ) ? $this->addon['id'] : 0,
			'price_type' => $chosen_option['price_type'],
		);

		return $cart_item_data;
	}
}
