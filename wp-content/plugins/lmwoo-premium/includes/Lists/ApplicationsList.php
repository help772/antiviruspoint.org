<?php

namespace LicenseManagerForWooCommerce\Lists;

use Exception;
use LicenseManagerForWooCommerce\Repositories\Resources\Application as ApplicationResourceRepository;
use LicenseManagerForWooCommerce\AdminNotice;
use LicenseManagerForWooCommerce\Setup;
use LicenseManagerForWooCommerce\AdminMenus;
use WP_List_Table;

/**
 * Class ApplicationsList
 *
 * @package LicenseManagerForWooCommercePro\ListTables
 */
class ApplicationsList extends WP_List_Table {

	/**
	 * Table
	 * 
	 * @var string
	 */
	protected $table;

	/**
	 * GeneratorsList constructor.
	 */
	public function __construct() {
		global $wpdb;

		parent::__construct(
			array(
				'singular' => __( 'Application', 'license-manager-for-woocommerce' ),
				'plural'   => __( 'Applications', 'license-manager-for-woocommerce' ),
				'ajax'     => false,
			)
		);

		$this->table = $wpdb->prefix . Setup::APPLICATION_TABLE_NAME;
	}

	/**
	 * Retrieves the Application from the database.
	 *
	 * @param int $perPage Default amount of Application per page
	 * @param int $pageNumber Default page number
	 *
	 * @return array
	 */
	public function queryData( $perPage = 20, $pageNumber = 1 ) {
		global $wpdb;
		$data = $_REQUEST;
		$wpdb->applicationtable = $this->table;
		
		$results = $wpdb->get_results( $wpdb->prepare(' SELECT * FROM `' . $wpdb->applicationtable . '` ORDER BY %s %s LIMIT %d OFFSET %d', array( empty( $data['orderby'] ) ? 'id' : esc_sql( $data['orderby'] ), empty( $data['order'] ) ? 'DESC' : esc_sql( $data['order'] ), $perPage, ( $pageNumber - 1 ) * $perPage ) ), ARRAY_A );

		return $results;
	}

	/**
	 * Retrieves the application table row count.
	 *
	 * @return int
	 */
	private function getCount() {
		global $wpdb;
		$wpdb->applicationtable = $this->table;
		return $wpdb->get_var( 'SELECT COUNT(*) FROM `' . $wpdb->applicationtable . '`' );
	}

	/**
	 * Output in case no items exist.
	 */
	public function no_items() {
		echo esc_html__( 'No application found.', 'license-manager-for-woocommerce' );
	}

	/**
	 * Checkbox column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
	}

	/**
	 * Name column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	
	public function column_name( $item ) {

		$actions = array();
		$title   = '<strong>' . $item['name'] . '</strong>';
		/* translators: %d: Item ID. */
		$actions['id']     = sprintf( __( 'ID: %d', 'license-manager-for-woocommerce' ), (int) $item['id'] );
		$actions['edit'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				wp_nonce_url(
					sprintf(
						'%s&page=%s&action=edit&id=%d', AdminMenus::PRODUCT_PAGE,
						AdminMenus::APPLICATIONS_PAGE,
						intval($item['id'])
					),
					'edit'
				)
			),
			__('Edit', 'license-manager-for-woocommerce')
		);
		$actions['delete'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				sprintf(
					'%s&page=%s&action=delete&id=%d&_wpnonce=%s', AdminMenus::PRODUCT_PAGE,
					AdminMenus::APPLICATIONS_PAGE,
					intval($item['id']),
					wp_create_nonce('delete')
				)
			),
			__('Delete', 'license-manager-for-woocommerce')
		);
		return $title . $this->row_actions( $actions );
	}

	/**
	 * Prefix column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_prefix( $item ) {
		$value = '';

		if ( $item['type'] ) {
			$value = sprintf( '<code>%s</code>', $item['prefix'] );
		}

		return $value;
	}

	/**
	 * Default column value.
	 *
	 * @param array $item Associative array of column name and value pairs
	 * @param string $column_name Name of the current column
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : __( 'N/a', 'license-manager-for-woocommerce' );
	}

	/**
	 * Set the table columns.
	 */
	public function get_columns() {
		return array(
			'cb'   => '<input type="checkbox" />',
			'name' => __( 'Name', 'license-manager-for-woocommerce' ),
			'type' => __( 'Type', 'license-manager-for-woocommerce' ),
		);
	}

	/**
	 * Defines sortable columns and their sort value.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'name' => array( 'name', true ),
		);
	}

	/**
	 * Defines items in the bulk action dropdown.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'license-manager-for-woocommerce' ),
		);

		return $actions;
	}

	/**
	 * Handle bulk action requests.
	 *
	 * @throws Exception
	 */
	private function processBulkActions() {
		$action = $this->current_action();

		switch ( $action ) {
			case 'delete':
				$this->verifyNonce( 'delete' );
				$this->verifySelection();
				$this->delete();
				break;
			default:
				break;
		}
	}

	/**
	 * Initialization function.
	 *
	 * @throws Exception
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		$this->processBulkActions();

		$perPage     = $this->get_items_per_page( 'application_per_page', 10 );
		$currentPage = $this->get_pagenum();
		$totalItems  = $this->getCount();

		$this->set_pagination_args(
			array(
				'total_items' => $totalItems,
				'per_page'    => $perPage,
				'total_pages' => ceil( $totalItems / $perPage ),
			)
		);

		$this->items = $this->queryData( $perPage, $currentPage );
	}

	/**
	 * Checks if the given nonce is valid.
	 *
	 * @param string $nonceAction The nonce to check
	 *
	 * @throws Exception
	 */
	private function verifyNonce( $nonceAction ) {
		if ( empty( $_REQUEST['_wpnonce'] ) || ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), $nonceAction ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'bulk-' . $this->_args['plural'] ) )
		) {
			AdminNotice::error( __( 'The nonce is invalid or has expired.', 'license-manager-for-woocommerce' ) );
			wp_safe_redirect( admin_url( sprintf( 'admin.php?page=%s', AdminMenus::APPLICATIONS_PAGE ) ) );

			exit();
		}
	}

	/**
	 * Makes sure that Application were selected for the bulk action.
	 */
	private function verifySelection() {
		// No ID's were selected, show a warning and redirect
		if ( ! array_key_exists( 'id', $_REQUEST ) ) {
			$message = sprintf( esc_html__( 'No application items were selected.', 'license-manager-for-woocommerce' ) );
			AdminNotice::warning( $message );

			wp_safe_redirect(
				admin_url(
					sprintf( 'admin.php?page=%s', AdminMenus::APPLICATIONS_PAGE )
				)
			);

			exit();
		}
	}

	/**
	 * Bulk delete application items.
	 *
	 * @throws \Exception
	 */
	private function delete() {
		$data = $_REQUEST;
		$selected = (array) $data['id'];

		$result = ApplicationResourceRepository::instance()->delete( $selected );
		
		if ( $result ) {
			/* translators: %d: Number of application items. */
			AdminNotice::success( sprintf( __( '%d application item(s) permanently deleted.', 'license-manager-for-woocommerce' ), $result ) );

			wp_safe_redirect(
				admin_url(
					sprintf( 'admin.php?page=%s', AdminMenus::APPLICATIONS_PAGE )
				)
			);
		} else {
			AdminNotice::error( __( 'There was a problem deleting the application items.', 'license-manager-for-woocommerce' ) );

			wp_safe_redirect(
				admin_url(
					sprintf( 'admin.php?page=%s', AdminMenus::APPLICATIONS_PAGE )
				)
			);
		}
	}
}
