<?php
/**
 * File Upload field
 *
 * @version 8.2.0
 */
class WC_Product_Addons_Field_File_Upload extends WC_Product_Addons_Field {
	public $addon;
	public $value;
	public $test;

	/**
	 * Constructor
	 */
	public function __construct( $addon, $value = '', $test = false ) {
		$this->addon = $addon;
		$this->value = $value;
		$this->test  = $test;
	}

	/**
	 * Validate an addon
	 *
	 * @return bool pass, or WP_Error
	 */
	public function validate() {
		$field_name = $this->get_field_name();

		if ( ! empty( $this->addon['required'] ) ) {
			if ( empty( $this->value ) && ( empty( $_FILES[ $field_name ] ) || empty( $_FILES[ $field_name ]['name'] ) ) ) {
				/* translators: %s Addon name */
				return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
			}
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $_FILES[ $field_name ] ) && WC_Product_Addons_Helper::is_filesize_over_limit( $_FILES[ $field_name ] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception message is escaped later.
			return new WP_Error( 'error', __( 'Filesize exceeds the limit.', 'woocommerce-product-addons' ) );
		}

		/**
		 * File type validation strategy
		 *
		 * This block validates uploaded files against both their extension and their
		 * detected MIME type when the merchant has configured file type restrictions.
		 *
		 * 1) Extension + MIME validation:
		 *    - The configured "allowed file types" represent extensions the merchant
		 *      is willing to accept (e.g. "jpg", "png", "pdf").
		 *    - WordPress core utilities are then used with the temporary file path
		 *      to determine the actual MIME type based on file contents, not only
		 *      the extension in the original file name.
		 *    - A file must match both the allowed extension list and the MIME type
		 *      determined by WordPress to be considered valid.
		 *
		 * 2) Preventing file type spoofing:
		 *    - Attackers may try to upload a dangerous file (e.g. PHP script or
		 *      executable) with a safe-looking extension (e.g. ".jpg").
		 *    - By comparing the extension from the original file name with the
		 *      MIME type derived from the actual file contents, mismatches can be
		 *      detected and rejected, reducing the risk of file type spoofing.
		 *
		 * 3) Relation to WordPress allowed MIME types:
		 *    - WordPress maintains a global list of allowed MIME types; uploads
		 *      outside this list are rejected by core, regardless of addon settings.
		 *    - The merchant's "allowed file types" act as an additional, narrower
		 *      filter on top of WordPress' allowed MIME types.
		 *    - As a result, the effective allowed set is the intersection between
		 *      WordPress' allowed MIME types and the merchant's configured types.
		 */
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_FILES[ $field_name ] ) && ! empty( $_FILES[ $field_name ]['name'] ) ) {
			$restriction_data = WC_Product_Addons_Helper::get_restriction_data( $this->addon );

			if ( ! empty( $restriction_data['allowed_file_types'] ) && is_array( $restriction_data['allowed_file_types'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.NonceVerification.Missing
				$file_name = sanitize_file_name( $_FILES[ $field_name ]['name'] );

				// Temporary file path on server - needed to validate actual file content/MIME type, not just extension.
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
				$file_tmp_name = isset( $_FILES[ $field_name ]['tmp_name'] ) ? $_FILES[ $field_name ]['tmp_name'] : '';
				$file_ext      = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );

				// Normalize allowed file types (remove dots, convert to lowercase).
				$allowed_types = array_map(
					function ( $type ) {
						return strtolower( ltrim( trim( $type ), '.' ) );
					},
					$restriction_data['allowed_file_types']
				);

				// First check extension.
				if ( ! in_array( $file_ext, $allowed_types, true ) ) {
					return new WP_Error(
						'error',
						sprintf(
							/* translators: %1$s: Addon name, %2$s: Comma-separated list of allowed file types */
							__( '"%1$s" file type is not allowed. Allowed file types: %2$s', 'woocommerce-product-addons' ),
							$this->addon['name'],
							implode( ', ', $restriction_data['allowed_file_types'] )
						)
					);
				}

				// Validate MIME type matches the extension to prevent file type spoofing.
				if ( ! empty( $file_tmp_name ) && file_exists( $file_tmp_name ) ) {
					// Build mimes array from allowed file types.
					$all_mimes      = get_allowed_mime_types();
					$filtered_mimes = array();

					// Filter mimes to only include those matching our allowed extensions.
					foreach ( $allowed_types as $allowed_ext ) {
						foreach ( $all_mimes as $ext_pattern => $mime_type ) {
							// Convert WordPress mime pattern (e.g., "jpg|jpeg|jpe") to array.
							$pattern_exts = explode( '|', $ext_pattern );
							if ( in_array( $allowed_ext, $pattern_exts, true ) ) {
								$filtered_mimes[ $ext_pattern ] = $mime_type;
							}
						}
					}

					// Use WordPress function to validate both extension and MIME type.
					$wp_filetype    = wp_check_filetype_and_ext( $file_tmp_name, $file_name, $filtered_mimes );
					$validated_ext  = ! empty( $wp_filetype['ext'] ) ? strtolower( $wp_filetype['ext'] ) : false;
					$validated_type = ! empty( $wp_filetype['type'] ) ? $wp_filetype['type'] : false;

					// Check if validation failed or extension doesn't match.
					if ( ! $validated_ext || ! $validated_type || $validated_ext !== $file_ext ) {
						return new WP_Error(
							'error',
							sprintf(
								/* translators: %1$s: Addon name, %2$s: Comma-separated list of allowed file types */
								__( '"%1$s" file type is not allowed. Allowed file types: %2$s', 'woocommerce-product-addons' ),
								$this->addon['name'],
								implode( ', ', $restriction_data['allowed_file_types'] )
							)
						);
					}
				}
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
		$adjust_price   = $this->addon['adjust_price'];
		$field_name     = $this->get_field_name();
		$this_data      = array(
			'name'       => sanitize_text_field( $this->addon['name'] ),
			'price'      => '1' != $adjust_price ? 0 : floatval( sanitize_text_field( $this->addon['price'] ) ),
			'value'      => '',
			'display'    => '',
			'field_name' => $this->addon['field_name'],
			'field_type' => $this->addon['type'],
			'id'         => isset( $this->addon['id'] ) ? $this->addon['id'] : 0,
			'price_type' => $this->addon['price_type'],
		);

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $_FILES[ $field_name ] ) && ! empty( $_FILES[ $field_name ]['name'] ) && ! $this->test ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$upload = $this->handle_upload( $_FILES[ $field_name ] );

			if ( empty( $upload['error'] ) && ! empty( $upload['file'] ) ) {
				$value                = wc_clean( $upload['url'] );
				$this_data['value']   = wc_clean( $upload['url'] );
				$this_data['display'] = basename( wc_clean( $upload['url'] ) );
				$cart_item_data[]     = $this_data;
			} else {
				return new WP_Error( 'error', $upload['error'] );
			}
		} elseif ( ! empty( $this->value ) ) {
			$this_data['value']   = wc_clean( $this->value );
			$this_data['display'] = basename( wc_clean( $this->value ) );
			$cart_item_data[]     = $this_data;
		}

		return $cart_item_data;
	}

	/**
	 * Handle file upload
	 *
	 * @param  string $file
	 * @return array
	 */
	public function handle_upload( $file ) {
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/media.php';

		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );

		$upload = wp_handle_upload( $file, array( 'test_form' => false ) );

		remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );

		return $upload;
	}

	/**
	 * upload_dir function.
	 *
	 * @access public
	 * @param mixed $pathdata
	 * @return void
	 */
	public function upload_dir( $pathdata ) {
		if ( empty( $pathdata['subdir'] ) ) {
			$pathdata['path']   = $pathdata['path'] . '/product_addons_uploads/' . md5( WC()->session->get_customer_id() );
			$pathdata['url']    = $pathdata['url'] . '/product_addons_uploads/' . md5( WC()->session->get_customer_id() );
			$pathdata['subdir'] = '/product_addons_uploads/' . md5( WC()->session->get_customer_id() );
		} else {
			$subdir             = '/product_addons_uploads/' . md5( WC()->session->get_customer_id() );
			$pathdata['path']   = str_replace( $pathdata['subdir'], $subdir, $pathdata['path'] );
			$pathdata['url']    = str_replace( $pathdata['subdir'], $subdir, $pathdata['url'] );
			$pathdata['subdir'] = str_replace( $pathdata['subdir'], $subdir, $pathdata['subdir'] );
		}

		return apply_filters( 'woocommerce_product_addons_upload_dir', $pathdata );
	}
}
