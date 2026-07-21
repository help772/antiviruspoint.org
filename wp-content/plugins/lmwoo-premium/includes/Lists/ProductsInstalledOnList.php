<?php
// phpcs:ignoreFile

namespace LicenseManagerForWooCommerce\Lists;

use DateTime;
use Exception;
use LicenseManagerForWooCommerce\AdminMenus;
use LicenseManagerForWooCommerce\AdminNotice;
use LicenseManagerForWooCommerce\Models\Resources\ProductInstalledOn as ProductInstalledOnResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\ProductInstalledOn as ProductInstalledOnResourceRepository;
use LicenseManagerForWooCommerce\Settings;
use LicenseManagerForWooCommerce\Setup;
use WP_List_Table;
use wpdb;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ProductsInstalledOnList extends WP_List_Table {

	const SPINNER_URL = '/wp-admin/images/loading.gif';

	protected $table;

	private $wpdb;

	protected $gmtOffset;

	protected $dateFormat;

	protected $timeFormat;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;

		parent::__construct(
			array(
				'singular' => esc_html__( 'Products installed on', 'license-manager-for-woocommerce' ),
				'plural'   => esc_html__( 'Products installed on', 'license-manager-for-woocommerce' ),
				'ajax'     => false,
			)
		);

		$this->table      = $wpdb->prefix . Setup::PRODUCTS_INSTALLED_ON_TABLE_NAME;
		$this->gmtOffset  = get_option( 'gmt_offset' );
		$this->dateFormat = get_option( 'date_format' );
		$this->timeFormat = get_option( 'time_format' );
	}

	public function get_products_installed_on( $perPage = 20, $pageNumber = 1 ) {
		global $wpdb;
		$request = $_REQUEST;

		$order_by = ( empty( $request['orderby'] ) ? 'id' : sanitize_key( $request['orderby'] ) );

		if (isset($request['order'])) {
			$order = ( empty(sanitize_sql_orderby($request['order'])) ? 'DESC' : sanitize_sql_orderby($request['order']) );
		} else {
			$order = 'DESC';
		}       
		$offset   = ( $pageNumber - 1 ) * $perPage;

		return $this->wpdb->get_results( $wpdb->prepare('SELECT * FROM %1$s ORDER BY %2$s %3$s LIMIT %4$s OFFSET %5$s', $this->table, $order_by, $order, $perPage, $offset ) , ARRAY_A );
	}

	private function get_products_installed_on_count() {
		global $wpdb;
		return $this->wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %1$s', $this->table ) );
	}

	public function no_items() {
		esc_html_e( 'No products installed on found.', 'license-manager-for-woocommerce' );
	}

	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
	}

	public function column_product_id( array $item ) {
		$html = '';

		if ( ! empty( $item['license_id'] ) ) {
			$license = LicenseResourceRepository::instance()->find( $item['license_id'] );

			if ( ! empty( $license ) ) {
				$product_id = $license->getProductId();
				$product    = wc_get_product( $product_id );
				if ( $product ) {
					$parentId = $product->get_parent_id();
					if ( $parentId ) {
						$html   = sprintf( '<span>#%s - %s</span>', $product->get_id(), $product->get_name() );
						$parent = wc_get_product( $parentId );
						if ( $parent ) {
							$html .= sprintf( '<br><small>%s <a href="%s" target="_blank">#%s - %s</a></small>', esc_html__( 'Variation of', 'license-manager-for-woocommerce' ), get_edit_post_link( $parent->get_id() ), $parent->get_id(), $parent->get_name() );
						}
					} else {
						$html = sprintf( '<a href="%s" target="_blank">#%s - %s</a>', get_edit_post_link( $product_id ), $product->get_id(), $product->get_name() );
					}
				}
			}
		} elseif ( ! empty( $item['product_name'] ) ) {
			$html = $item['product_name'];
		}

		// translators: %d Delete.
		$actions['delete'] = sprintf( '<a href="%s">%s</a>', admin_url( sprintf( 'admin.php?page=%s&action=delete&id=%d&_wpnonce=%s', AdminMenus::PRODUCTS_INSTALLED_ON_PAGE, (int) $item['id'], wp_create_nonce( 'delete' ) ) ), esc_html__( 'Delete', 'license-manager-for-woocommerce' ) );

		return $html . $this->row_actions( $actions );
	}

	public function column_order_id( array $item ) {
		$html = '';

		if ( ! empty( $item['license_id'] ) ) {
			$license = LicenseResourceRepository::instance()->find( $item['license_id'] );

			if ( ! empty( $license ) ) {
				$order_id = $license->getOrderId();

				$order_custom = wc_get_order( $order_id );
				if ( $order_custom ) {
					$html = sprintf( '<a href="%s" target="_blank">#%s</a>', get_edit_post_link( $order_id ), $order_custom->get_order_number() );
				}
			}
		}

		return $html;
	}

	public function column_license_key( array $item ) {
		if ( empty( $item['license_id'] ) ) {
			return '';
		}

		$license = LicenseResourceRepository::instance()->find( $item['license_id'] );

		if ( null === $license ) {
			return '';
		}

		if ( Settings::get( 'lmfwc_hide_license_keys' ) ) {
			$title = '<code class="lmfwc-placeholder empty"></code>';
			// translators: %d License ID.
			$title .= sprintf( '<img class="lmfwc-spinner" data-id="%d" src="%s">', $item['license_id'], self::SPINNER_URL );
		} else {
			$title = sprintf( '<code class="lmfwc-placeholder">%s</code>', $license->getDecryptedLicenseKey() );
			// translators: %d License ID.
			$title .= sprintf( '<img class="lmfwc-spinner" data-id="%d" src="%s">', $item['license_id'], self::SPINNER_URL );
		}

		// translators: %d License ID.
		$actions['id'] = sprintf( esc_html__( 'ID: %d', 'license-manager-for-woocommerce' ), (int) $item['license_id'] );

		// translators: %d License ID.
		$actions['show'] = sprintf( '<a class="lmfwc-license-key-show" data-id="%d">%s</a>', $item['license_id'], esc_html__( 'Show', 'license-manager-for-woocommerce' ) );
		// translators: %d License ID.
		$actions['hide'] = sprintf( '<a class="lmfwc-license-key-hide" data-id="%d">%s</a>', $item['license_id'], esc_html__( 'Hide', 'license-manager-for-woocommerce' ) );

		return $title . $this->row_actions( $actions );
	}

	public function column_host( array $item ) {
		$html = '';

		if ( ! empty( $item['host'] ) ) {
			$html = sprintf( '<a href="%s" target="_blank">%s</a>', $item['host'], $item['host'] );
		}

		return $html;
	}

	public function column_last_ping( array $item ) {
		$html = '';

		if ( ! empty( $item['last_ping'] ) ) {
			$offsetSeconds = (float) $this->gmtOffset * 60 * 60;
			$timestamp     = strtotime( $item['last_ping'] ) + $offsetSeconds;
			$result        = gmdate( 'Y-m-d H:i:s', $timestamp );
			$date          = new DateTime( $result );

			$html = sprintf( '<span>%s <b>%s, %s</b></span>', esc_html__( 'at', 'license-manager-for-woocommerce' ), $date->format( $this->dateFormat ), $date->format( $this->timeFormat ) );
		}

		return $html;
	}

	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'product_id'  => esc_html__( 'Product', 'license-manager-for-woocommerce' ),
			'order_id'    => esc_html__( 'Order', 'license-manager-for-woocommerce' ),
			'license_key' => esc_html__( 'License key', 'license-manager-for-woocommerce' ),
			'host'        => esc_html__( 'Installed on', 'license-manager-for-woocommerce' ),
			'last_ping'   => esc_html__( 'Last ping', 'license-manager-for-woocommerce' ),
		);
	}

	public function get_sortable_columns() {
		return array(
			'product_id'  => array( 'product_id', true ),
			'order_id'    => array( 'order_id', true ),
			'license_key' => array( 'license_key', true ),
			'last_ping'   => array( 'last_ping', true ),
		);
	}

	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		$this->processBulkActions();

		$per_page     = $this->get_items_per_page( 'products_installed_on_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->get_products_installed_on_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);

		$this->items = $this->get_products_installed_on( $per_page, $current_page );
	}

	private function processBulkActions() {
		$action = $this->current_action();

		if ( 'delete' === $action ) {
			$this->deleteProductsInstalledOn();
		}
	}

	private function deleteProductsInstalledOn() {
		$this->verifyNonce( 'delete' );
		$this->verifySelection();
		$request = $_REQUEST;

		$product_installed_onIds = (array) $request['id'];
		$count                   = 0;

		foreach ( $product_installed_onIds as $product_installed_onId ) {

			$product_installed_on = ProductInstalledOnResourceRepository::instance()->find( $product_installed_onId );

			if ( ! $product_installed_on ) {
				continue;
			}

			$result = ProductInstalledOnResourceRepository::instance()->delete( (array) $product_installed_onId );

			if ( $result ) {
				$count += $result;
			}
		}
		// translators: %d Products Installed.
		$message = sprintf( esc_html__( '%d product(s) installed on permanently deleted.', 'license-manager-for-woocommerce' ), $count );

		// Set the admin notice
		AdminNotice::success( $message );

		// Redirect and exit
		wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s', AdminMenus::PRODUCTS_INSTALLED_ON_PAGE ) ) );
	}

	private function verifyNonce( $nonce ) {

		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$currentNonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
		}
		
		if ( ! wp_verify_nonce( $currentNonce, $nonce ) && ! wp_verify_nonce( $currentNonce, 'bulk-' . $this->_args['plural'] ) ) {
			AdminNotice::error( esc_html__( 'The nonce is invalid or has expired.', 'license-manager-for-woocommerce' ) );
			wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s', AdminMenus::PRODUCTS_INSTALLED_ON_PAGE ) ) );

			exit;
		}
	}

	private function verifySelection() {
		// No ID's were selected, show a warning and redirect
		$request = $_REQUEST;
		if ( ! array_key_exists( 'id', $request ) ) {
			$message = sprintf( esc_html__( 'No products installed on were selected.', 'license-manager-for-woocommerce' ) );
			AdminNotice::warning( $message );

			wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s', AdminMenus::PRODUCTS_INSTALLED_ON_PAGE ) ) );

			exit;
		}
	}
}
