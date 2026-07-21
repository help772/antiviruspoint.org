<?php
/**
 * Admin handler class.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable Generic.Classes.DuplicateClassName.Found --- The other one only for mock

use Automattic\WooCommerce\Admin\BlockTemplates\BlockInterface;
use WooCommerce\RoyalMail\JSON_Rate_Loader;

/**
 * Admin handler.
 */
class WC_Shipping_Royalmail_Admin {
// phpcs:enable Generic.Classes.DuplicateClassName.Found

	const META_KEY_PRINTED_PAPERS = '_shipping-royalmail-printed-papers';

	const META_KEY_BOOK = '_shipping-royalmail-book';

	const META_KEY_TUBE = '_shipping-royalmail-tube';

	/**
	 * Label text for printed papers.
	 *
	 * @var string
	 */
	public $label_printed_papers;

	/**
	 * Label text for book.
	 *
	 * @var string
	 */
	public $label_book;

	/**
	 * Label text for tube.
	 *
	 * @var string
	 */
	public $label_tube;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'woocommerce_product_options_dimensions', array( $this, 'product_options' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta' ) );
			add_action( 'woocommerce_variation_options_dimensions', array( $this, 'variation_options' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'process_product_variation_meta' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		add_action( 'woocommerce_block_template_area_product-form_after_add_block_product-fee-and-dimensions-section', array( $this, 'add_block' ) );
		add_action( 'woocommerce_block_template_area_product-form_after_add_block_product-variation-fee-and-dimensions-section', array( $this, 'add_block' ) );
		add_filter( 'woocommerce_rest_pre_insert_product_object', array( $this, 'process_rest_product_meta' ) );
		add_filter( 'woocommerce_rest_pre_insert_product_variation_object', array( $this, 'process_rest_product_meta' ) );

		// Add rate sync admin functionality.
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'handle_rate_sync_action' ) );
			add_action( 'admin_notices', array( $this, 'rate_sync_admin_notices' ) );
		}
	}

	/**
	 * Enqueue script for edit product page.
	 */
	public function admin_enqueue_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		wp_enqueue_script( 'wc-royalmail-edit-product', plugins_url( 'assets/js/edit-product.js', WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ), array( 'jquery' ), WOOCOMMERCE_SHIPPING_ROYALMAIL_VERSION, true );
	}

	/**
	 * Add more option for simple product type.
	 */
	public function product_options() {
		$this->set_shipping_option_labels();

		// Printed Papers option.
		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_KEY_PRINTED_PAPERS,
				'label'       => $this->label_printed_papers,
				// translators: %s is a label printed papers.
				'description' => sprintf( __( 'Use %s rates to ship package', 'woocommerce-shipping-royalmail' ), $this->label_printed_papers ),
				'desc_tip'    => true,
			)
		);

		// Book option.
		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_KEY_BOOK,
				'label'       => $this->label_book,
				// translators: %1$s is a label book.
				'description' => sprintf( __( 'This product is a %1$s. Only %1$ss can be sent to the Republic of Ireland with Printed Papers rates.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_book ) ),
				'desc_tip'    => true,
			)
		);

		// Tube option.
		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_KEY_TUBE,
				'label'       => $this->label_tube,
				// translators: %1$s is a label tube.
				'description' => sprintf( __( 'Use %s rates to ship package. For rolled and cylinder-shaped parcels, the length of the item plus twice the diameter ( width &amp; height must be equal ) must not exceed 104cm, with the greatest dimension being no more than 90cm.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_tube ) ),
				'desc_tip'    => true,
			)
		);
	}

	/**
	 * Add more option for variation product type.
	 *
	 * @param int     $loop Loop index of the variation.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation Product variation object.
	 */
	public function variation_options( $loop, array $variation_data, WP_Post $variation ) {
		$this->set_shipping_option_labels();

		// Printed Papers option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'variable_' . self::META_KEY_PRINTED_PAPERS . $loop,
				'name'          => 'variable_' . self::META_KEY_PRINTED_PAPERS . '[' . $loop . ']',
				'label'         => $this->label_printed_papers,
				// translators: %s is a label printed papers.
				'description'   => sprintf( __( 'Use %s rates to ship package', 'woocommerce-shipping-royalmail' ), $this->label_printed_papers ),
				'desc_tip'      => true,
				'wrapper_class' => 'form-row form-row-last hide_if_variation_virtual',
				'value'         => get_post_meta( $variation->ID, self::META_KEY_PRINTED_PAPERS, true ),
			)
		);

		// Book option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'variable_' . self::META_KEY_BOOK . $loop,
				'name'          => 'variable_' . self::META_KEY_BOOK . '[' . $loop . ']',
				'label'         => $this->label_book,
				// translators: %1$s is a label book.
				'description'   => sprintf( __( 'This product is a %1$s. Only %1$ss can be sent to the Republic of Ireland with Printed Papers rates.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_book ) ),
				'desc_tip'      => true,
				'wrapper_class' => 'form-row form-row-last hide_if_variation_virtual',
				'value'         => get_post_meta( $variation->ID, self::META_KEY_BOOK, true ),
			)
		);

		// Tube option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'variable_' . self::META_KEY_TUBE . $loop,
				'name'          => 'variable_' . self::META_KEY_TUBE . '[' . $loop . ']',
				'label'         => $this->label_tube,
				// translators: %1$s is a label tube.
				'description'   => sprintf( __( 'Use %s rates to ship package. The length of the item plus twice the diameter must not exceed 104cm, with the greatest dimension being no more than 90cm. And the width and height of this item should be equal.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_tube ) ),
				'desc_tip'      => true,
				'wrapper_class' => 'form-row form-row-last hide_if_variation_virtual',
				'value'         => get_post_meta( $variation->ID, self::META_KEY_TUBE, true ),
			)
		);
	}

	/**
	 * Add a new block to the template after the product name field.
	 *
	 * @param BlockInterface $product_section The product section block.
	 */
	public function add_block( BlockInterface $product_section ) {
		$this->set_shipping_option_labels();

		$parent = $product_section->get_parent();

		if ( ! method_exists( $parent, 'add_section' ) ) {
			return;
		}

		// Basic Details Section.
		$royal_mail = $parent->add_section(
			array(
				'id'         => 'royal-mail-section',
				'order'      => 30,
				'attributes' => array(
					'title'       => __( 'Royal Mail Fields', 'woocommerce' ),
					'description' => __( 'Set up Royal Mail fields.', 'woocommerce' ),
				),
			)
		);

		$royal_mail->add_block(
			array(
				'id'         => self::META_KEY_PRINTED_PAPERS,
				'order'      => 5,
				'blockName'  => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'property'       => 'meta_data.' . self::META_KEY_PRINTED_PAPERS,
					'label'          => $this->label_printed_papers,
					'checkedValue'   => 'yes',
					'uncheckedValue' => '',
					// translators: %s is a label printed papers.
					'description'    => sprintf( __( 'Use %s rates to ship package', 'woocommerce-shipping-royalmail' ), $this->label_printed_papers ),
				),
			)
		);

		$royal_mail->add_block(
			array(
				'id'         => self::META_KEY_BOOK,
				'order'      => 10,
				'blockName'  => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'property'       => 'meta_data.' . self::META_KEY_BOOK,
					'label'          => $this->label_book,
					'checkedValue'   => 'yes',
					'uncheckedValue' => '',
					// translators: %1$s is a label book.
					'description'    => sprintf( __( 'This product is a %1$s. Only %1$ss can be sent to the Republic of Ireland with Printed Papers rates.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_book ) ),
				),
			)
		);

		$royal_mail->add_block(
			array(
				'id'         => self::META_KEY_TUBE,
				'order'      => 15,
				'blockName'  => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'property'       => 'meta_data.' . self::META_KEY_TUBE,
					'label'          => $this->label_tube,
					'checkedValue'   => 'yes',
					'uncheckedValue' => '',
					// translators: %1$s is a label tube.
					'description'    => sprintf( __( 'Use %s rates to ship package. For rolled and cylinder-shaped parcels, the length of the item plus twice the diameter ( width &amp; height must be equal ) must not exceed 104cm, with the greatest dimension being no more than 90cm.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_tube ) ),
				),
			)
		);
	}

	/**
	 * Save custom fields
	 *
	 * @param int $post_id Product ID.
	 */
	public function process_product_meta( $post_id ) {
		// No need to verify. It has been verified on `WC_Meta_Box_Product_Data::save()`.
		// phpcs:disable WordPress.Security.NonceVerification.Missing

		$checkbox_meta_fields = array(
			self::META_KEY_PRINTED_PAPERS, // Printed papers.
			self::META_KEY_BOOK, // Book.
			self::META_KEY_TUBE, // Tube.
		);

		// Save or delete checkbox meta fields value.
		foreach ( $checkbox_meta_fields as $checkbox_field ) {
			if ( ! empty( $_POST[ $checkbox_field ] ) ) {
				update_post_meta( $post_id, $checkbox_field, 'yes' );
			} else {
				delete_post_meta( $post_id, $checkbox_field );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Save variation custom fields
	 *
	 * @param int $post_id Post ID.
	 * @param int $loop Variation loop.
	 */
	public function process_product_variation_meta( $post_id, $loop ) {
		// No need to verify. It has been verified on `WC_AJAX::save_variations()`.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$checkbox_meta_fields = array(
			self::META_KEY_PRINTED_PAPERS, // Printed papers.
			self::META_KEY_BOOK, // Book.
			self::META_KEY_TUBE, // Tube.
		);

		// Save or delete checkbox meta fields value.
		foreach ( $checkbox_meta_fields as $checkbox_field ) {
			if ( ! empty( $_POST[ 'variable_' . $checkbox_field ][ $loop ] ) ) {
				update_post_meta( $post_id, $checkbox_field, 'yes' );
			} else {
				delete_post_meta( $post_id, $checkbox_field );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Process product meta through REST API.
	 *
	 * @param WC_Product $product Product object.
	 */
	public function process_rest_product_meta( $product ) {
		if ( 'yes' === $product->get_meta( self::META_KEY_TUBE ) && $product->get_width() !== $product->get_height() ) {
			return new WP_Error(
				'woocommerce_shipping_royalmail_rest_tube_error',
				__( 'Width and height must have the same value when tube is checked.', 'woocommerce-shipping-royalmail' ),
				array(
					'status' => 404,
				)
			);
		}

		return $product;
	}


	/**
	 * Set labels for shipping options.
	 */
	private function set_shipping_option_labels() {
		if ( ! empty( $this->label_printed_papers ) ) {
			return;
		}

		$this->label_printed_papers = esc_html__( 'Printed Papers', 'woocommerce-shipping-royalmail' );
		$this->label_book           = esc_html__( 'Book', 'woocommerce-shipping-royalmail' );
		$this->label_tube           = esc_html__( 'Tube/Rolls', 'woocommerce-shipping-royalmail' );
	}

	/**
	 * Handle rate sync action from admin.
	 */
	public function handle_rate_sync_action() {
		if ( ! isset( $_GET['royalmail_sync_rates'] ) || ! isset( $_GET['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'royalmail_sync_rates' ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( ! class_exists( 'WooCommerce\RoyalMail\JSON_Rate_Loader' ) ) {
			return;
		}

		$results = JSON_Rate_Loader::sync_rates_to_database();

		// Clear failure state if manual sync succeeds.
		if ( 0 === $results['failed'] ) {
			delete_option( 'royalmail_sync_failure' );
		}

		// Store results in transient for display.
		set_transient( 'royalmail_sync_results', $results, 60 );

		// Redirect to remove the action from URL.
		wp_safe_redirect( remove_query_arg( array( 'royalmail_sync_rates', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Display admin notices for rate sync.
	 */
	public function rate_sync_admin_notices() {
		// One-time feedback from manual sync.
		$results = get_transient( 'royalmail_sync_results' );
		if ( $results ) {
			delete_transient( 'royalmail_sync_results' );

			$class   = $results['failed'] > 0 ? 'notice-warning' : 'notice-success';
			$message = sprintf(
			/* translators: %1$d: successful syncs, %2$d: failed syncs */
				__( 'RoyalMail rates sync from local files completed: %1$d successful, %2$d failed.', 'woocommerce-shipping-royalmail' ),
				$results['success'],
				$results['failed']
			);

			$notice_html  = '<div class="notice ' . esc_attr( $class ) . ' is-dismissible">';
			$notice_html .= '<p>' . esc_html( $message ) . '</p>';

			if ( $results['failed'] > 0 && ! empty( $results['errors'] ) ) {
				$notice_html .= '<div style="margin-top: 10px;">';
				$notice_html .= '<p><strong>' . esc_html__( 'Error details:', 'woocommerce-shipping-royalmail' ) . '</strong></p>';
				$notice_html .= '<ul>';

				foreach ( $results['errors'] as $error ) {
					$notice_html .= '<li>' . esc_html( $error ) . '</li>';
				}

				$notice_html .= '</ul>';
				$notice_html .= '</div>';
			}

			$notice_html .= '</div>';

			echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped above.
		}

		// Persistent warning from failed automatic sync.
		$sync_state = get_option( 'royalmail_sync_failure' );
		if ( ! is_array( $sync_state ) || empty( $sync_state['errors'] ) ) {
			return;
		}

		// Per-user dismissal via WC's user meta pattern.
		if ( get_user_meta( get_current_user_id(), 'dismissed_royalmail_sync_failure_notice', true ) ) {
			return;
		}

		$dismiss_url  = wp_nonce_url( add_query_arg( 'wc-hide-notice', 'royalmail_sync_failure' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' );
		$settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=royal_mail' );
		$sync_url     = wp_nonce_url( add_query_arg( 'royalmail_sync_rates', '1', $settings_url ), 'royalmail_sync_rates' );

		$notice_html  = '<div class="notice notice-error is-dismissible">';
		$notice_html .= '<a class="notice-dismiss" href="' . esc_url( $dismiss_url ) . '"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice.', 'woocommerce' ) . '</span></a>';
		$notice_html .= '<p><strong>' . esc_html__( 'Royal Mail: Some rates could not be synced. The plugin will automatically retry up to 3 times.', 'woocommerce-shipping-royalmail' ) . '</strong></p>';
		$notice_html .= '<ul>';

		foreach ( $sync_state['errors'] as $error ) {
			$notice_html .= '<li>' . esc_html( $error ) . '</li>';
		}

		$notice_html .= '</ul>';
		$notice_html .= '<p><a class="button button-secondary" href="' . esc_url( $sync_url ) . '">' . esc_html__( 'Retry sync now', 'woocommerce-shipping-royalmail' ) . '</a></p>';
		$notice_html .= '</div>';

		echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped above.
	}
}
