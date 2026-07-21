<?php

namespace LicenseManagerForWooCommerce\Lists;

use DateTime;
use Exception;
use LicenseManagerForWooCommerce\AdminMenus;
use LicenseManagerForWooCommerce\AdminNotice;
use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\LicenseActivations as ActivationsResourceRepository;
use LicenseManagerForWooCommerce\Settings;
use LicenseManagerForWooCommerce\Setup;
use WC_Product;
use WP_List_Table;
use WP_User;

defined('ABSPATH') || exit;

if (!class_exists('WP_List_Table')) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class LicensesList extends WP_List_Table {

	/**
	 * Path to spinner image.
	 */
	const SPINNER_URL = '/wp-admin/images/loading.gif';

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * DateFormat
	 *
	 * @var string
	 */
	protected $dateFormat;

	/**
	 * DateTimeFormat
	 *
	 * @var string
	 */
	protected $dateTimeFormat;

	/**
	 * TimeFormat
	 *
	 * @var string
	 */
	protected $timeFormat;

	/**
	 * GmtOffset
	 *
	 * @var string
	 */
	protected $gmtOffset;

	/**
	 * LicensesList constructor.
	 */
	public function __construct() {
		global $wpdb;

		parent::__construct(
			array(
				'singular' => __('License key', 'license-manager-for-woocommerce'),
				'plural'   => __('License keys', 'license-manager-for-woocommerce'),
				'ajax'     => false,
			)
		);

		$this->table      = $wpdb->prefix . Setup::LICENSES_TABLE_NAME;
		$this->dateFormat = get_option('date_format');
		$this->timeFormat = get_option('time_format');
		$this->gmtOffset  = get_option('gmt_offset');
		$this->dateTimeFormat = lmfwc_expiration_format();
	}

	/**
	 * Creates the different status filter links at the top of the table.
	 *
	 * @return array
	 */
	protected function get_views() {
		$data = $_REQUEST;
		$statusLinks = array();
		$current     = !empty($data['status']) ? $data['status'] : 'all';

		// All link
		$class = 'all' ==  $current ? ' class="current"' :'';
		$allUrl = remove_query_arg('status');
		$statusLinks['all'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$allUrl,
			$class,
			__('All', 'license-manager-for-woocommerce'),
			LicenseResourceRepository::instance()->count()
		);

		// Sold link
		$class = LicenseStatus::SOLD == $current   ? ' class="current"' :'';
		$soldUrl = esc_url(add_query_arg('status', LicenseStatus::SOLD));
		$statusLinks['sold'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$soldUrl,
			$class,
			__('Sold', 'license-manager-for-woocommerce'),
			LicenseResourceRepository::instance()->countBy(array( 'status' => LicenseStatus::SOLD ))
		);

		// Delivered link
		$class = LicenseStatus::DELIVERED == $current  ? ' class="current"' :'';
		$deliveredUrl = esc_url(add_query_arg('status', LicenseStatus::DELIVERED));
		$statusLinks['delivered'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$deliveredUrl,
			$class,
			__('Delivered', 'license-manager-for-woocommerce'),
			LicenseResourceRepository::instance()->countBy(array( 'status' => LicenseStatus::DELIVERED ))
		);

		// Active link
		$class = LicenseStatus::ACTIVE == $current  ? ' class="current"' :'';
		$activeUrl = esc_url(add_query_arg('status', LicenseStatus::ACTIVE));
		$statusLinks['active'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$activeUrl,
			$class,
			__('Active', 'license-manager-for-woocommerce'),
			LicenseResourceRepository::instance()->countBy(array( 'status' => LicenseStatus::ACTIVE ))
		);

		// Inactive link
		$class = LicenseStatus::INACTIVE == $current   ? ' class="current"' :'';
		$inactiveUrl = esc_url(add_query_arg('status', LicenseStatus::INACTIVE));
		$statusLinks['inactive'] = sprintf(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$inactiveUrl,
			$class,
			__('Inactive', 'license-manager-for-woocommerce'),
			LicenseResourceRepository::instance()->countBy(array( 'status' => LicenseStatus::INACTIVE ))
		);

		return $statusLinks;
	}

	/**
	 * Adds the order and product filters to the licenses list.
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			echo '<div class="alignleft actions">';
			$this->orderDropdown();
			$this->productDropdown();
			$this->userDropdown();
			submit_button(__('Filter', 'license-manager-for-woocommerce'), '', 'filter-action', false);
			echo '</div>';
		}
	}

	/**
	 * Displays the order dropdown filter.
	 */
	public function orderDropdown() {
		$order = false;

		if (isset($_REQUEST['order-id'])) {
			$order = wc_get_order((int) $_REQUEST['order-id']);
		}

		?>
		<label for="filter-by-order-id" class="screen-reader-text">
			<span><?php esc_html_e('Filter by order', 'license-manager-for-woocommerce'); ?></span>
		</label>
		<select name="order-id" id="filter-by-order-id">
			<?php if ($order) : ?>
				<option selected="selected" value="<?php echo esc_attr($order->get_id()); ?>">
					<?php echo esc_attr( $order->get_formatted_billing_full_name() ); ?>
				</option>
			<?php endif; ?>
		</select>
		<?php
	}

	/**
	 * Displays the product dropdown filter.
	 */
	public function productDropdown() {
		$product = false;

		if (isset($_REQUEST['product-id'])) {
			$product = wc_get_product((int) $_REQUEST['product-id']);
		}

		?>
		<label for="filter-by-product-id" class="screen-reader-text">
			<span><?php esc_html_e('Filter by product', 'license-manager-for-woocommerce'); ?></span>
		</label>
		<select name="product-id" id="filter-by-product-id">
			<?php if ($product) : ?>
				<option selected="selected" value="<?php echo esc_attr($product->get_id()); ?>">
					<?php echo esc_attr( $product->get_name() ); ?>
				</option>
			<?php endif; ?>
		</select>
		<?php
	}

	/**
	 * Displays the user dropdown filter.
	 */
	public function userDropdown() {
		$user = false;

		if (isset($_REQUEST['user-id'])) {
			$user = get_user_by('ID', (int) $_REQUEST['user-id']);
		}
		?>
		<label for="filter-by-user-id" class="screen-reader-text">
			<span><?php esc_html_e('Filter by user', 'license-manager-for-woocommerce'); ?></span>
		</label>
		<select name="user-id" id="filter-by-user-id">
			<?php 
			if ($user) {
				printf(
					'<option value="%d" selected="selected">%s (#%d - %s)</option>',
					esc_attr( $user->ID ) ,
					esc_attr( $user->display_name ),
					esc_attr( $user->ID ),
					esc_attr( $user->user_email )
				);
			} 
			?>
		</select>
		<?php
	}

	/**
	 * Checkbox column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * License key column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_license_key( $item ) {
		if (Settings::get('lmfwc_hide_license_keys')) {
			$title = '<code class="lmfwc-placeholder empty"></code>';
			$title .= sprintf(
				'<img class="lmfwc-spinner" data-id="%d" src="%s">',
				$item['id'],
				self::SPINNER_URL
			);
		} else {
			$title = sprintf(
				'<code class="lmfwc-placeholder">%s</code>',
				/**
				* Filter lmfwc_decrypt
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_decrypt', $item['license_key'])
			);
			$title .= sprintf(
				'<img class="lmfwc-spinner" data-id="%d" src="%s">',
				$item['id'],
				self::SPINNER_URL
			);
		}

		// ID
		$actions['id'] = sprintf(
			/* translators: %s is the licenseID */
			__('ID: %d', 'license-manager-for-woocommerce'), intval($item['id']));

		// Edit
		$actions['edit'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				wp_nonce_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::LICENSES_PAGE,
						intval($item['id'])
					),
					'lmfwc_edit_license_key'
				)
			),
			__('Edit', 'license-manager-for-woocommerce')
		);

		// Hide/Show
		$actions['show'] = sprintf(
			/* translators: %s is the licenseID */
			'<a class="lmfwc-license-key-show" data-id="%d">%s</a>',
			$item['id'],
			__('Show', 'license-manager-for-woocommerce')
		);
		$actions['hide'] = sprintf(
			/* translators: %s is the licenseID */
			'<a class="lmfwc-license-key-hide" data-id="%d">%s</a>',
			$item['id'],
			__('Hide', 'license-manager-for-woocommerce')
		);

		// Activate, Deactivate
		if ( LicenseStatus::SOLD != $item['status']   && LicenseStatus::DELIVERED != $item['status']   ) {
			if ( LicenseStatus::ACTIVE  != $item['status']  ) {
				$actions['activate'] = sprintf(
					'<a href="%s">%s</a>',
					admin_url(
						sprintf(
							'admin.php?page=%s&action=activate&id=%d&_wpnonce=%s',
							AdminMenus::LICENSES_PAGE,
							intval($item['id']),
							wp_create_nonce('activate')
						)
					),
					__('Activate', 'license-manager-for-woocommerce')
				);
			}

			if ( LicenseStatus::INACTIVE != $item['status']  ) {
				$actions['deactivate'] = sprintf(
					'<a href="%s">%s</a>',
					admin_url(
						sprintf(
							'admin.php?page=%s&action=deactivate&id=%d&_wpnonce=%s',
							AdminMenus::LICENSES_PAGE,
							intval($item['id']),
							wp_create_nonce('deactivate')
						)
					),
					__('Deactivate', 'license-manager-for-woocommerce')
				);
			}
		}

		// Delete
		$actions['delete'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				sprintf(
					'admin.php?page=%s&action=delete&id=%d&_wpnonce=%s',
					AdminMenus::LICENSES_PAGE,
					intval($item['id']),
					wp_create_nonce('delete')
				)
			),
			__('Delete', 'license-manager-for-woocommerce')
		);

		return $title . $this->row_actions($actions);
	}

	/**
	 * Order ID column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_order_id( $item ) {
		$html = '';
		$order = wc_get_order($item['order_id']);
		if ( $order ) {
			$html = sprintf(
				'<a href="%s" target="_blank">#%s</a>',
				get_edit_post_link($item['order_id']),
				$order->get_order_number()
			);
		}

		return $html;
	}

	/**
	 * Product ID column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_product_id( $item ) {
		$html = '';
		$product = wc_get_product($item['product_id']);
		if ( ! is_object( $product) ) {
			return;
		}
		$parentId = $product->get_parent_id();
		
		if ( $parentId ) {
			$html = sprintf(
				'<span>#%s - %s</span>',
				$product->get_id(),
				$product->get_name()
			);
			$parent = wc_get_product($parentId);
			if ( $parent ) {
				$html .= sprintf(
					'<br><small>%s <a href="%s" target="_blank">#%s - %s</a></small>',
					__('Variation of', 'license-manager-for-woocommerce'),
					get_edit_post_link($parent->get_id()),
					$parent->get_id(),
					$parent->get_name()
				);
			}
		} else {
			$html = sprintf(
				'<a href="%s" target="_blank">#%s - %s</a>',
				get_edit_post_link($item['product_id']),
				$product->get_id(),
				$product->get_name()
			);
		}

		return $html;
	}

	/**
	 * User ID column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_user_id( $item ) {
		$html = '';

		if ( null !== $item['user_id']  ) {
			$user = get_userdata($item['user_id']);

			if ($user instanceof WP_User) {
				if (current_user_can('manage_options')) {
					$html .= sprintf(
						'<a href="%s">%s (#%d - %s)</a>',
						get_edit_user_link($user->ID),
						$user->display_name,
						$user->ID,
						$user->user_email
					);
				} else {
					$html .= sprintf(
						'<span>%s</span>',
						$user->display_name
					);
				}
			}
		}

		return $html;
	}

	/**
	 * Activation column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_activation( $item ) {
		$html = '';

		if ( null === $item['times_activated_max']  ) {
			$timesActivatedMax = null;
		} else {
			$timesActivatedMax = intval($item['times_activated_max']);
		}

		if ( null === $item['times_activated']  ) {
			$timesActivated = null;
		} else {
			$timesActivated = intval($item['times_activated']);
		}

		if ( null  === $timesActivatedMax ) {
			return sprintf(
				'<div class="lmfwc-status %s"><small>%d</small> / <b>%s</b></div>',
				'activation done',
				intval($timesActivated),
				'&infin;'
			);
		}

		if ($timesActivated == $timesActivatedMax) {
			$icon = '<span class="dashicons dashicons-yes"></span>';
			$status = 'activation done';
		} else {
			$icon = '';
			$status = 'activation pending';
		}

		if ($timesActivated || $timesActivatedMax) {
			$html = sprintf(
				'<div class="lmfwc-status %s">%s <small>%d</small> / <b>%d</b></div>',
				$status,
				$icon,
				$timesActivated,
				$timesActivatedMax
			);
		}

		return $html;
	}

	/**
	 * Created column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @throws Exception
	 * @return string
	 */
	public function column_created( $item ) {
		$html = '';

		if ($item['created_at']) {
			$offsetSeconds = floatval($this->gmtOffset) * 60 * 60;
			$timestamp     = strtotime($item['created_at']) + $offsetSeconds;
			$result        = gmdate('Y-m-d H:i:s', $timestamp);
			$date          = new DateTime($result);

			$html .= sprintf(
				'<span>%s <b>%s, %s</b></span>',
				__('at', 'license-manager-for-woocommerce'),
				$date->format($this->dateFormat),
				$date->format($this->timeFormat)
			);
		}

		if ($item['created_by']) {
			$user = get_user_by('id', $item['created_by']);

			if ($user instanceof WP_User) {
				if (current_user_can('manage_options')) {
					$html .= sprintf(
						'<br>%s <a href="%s">%s</a>',
						__('by', 'license-manager-for-woocommerce'),
						get_edit_user_link($user->ID),
						$user->display_name
					);
				} else {
					$html .= sprintf(
						'<br><span>%s %s</span>',
						__('by', 'license-manager-for-woocommerce'),
						$user->display_name
					);
				}
			}
		}

		return $html;
	}

	/**
	 * Updated column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @throws Exception
	 * @return string
	 */
	public function column_updated( $item ) {
		$html = '';

		if ($item['updated_at']) {
			$offsetSeconds = floatval($this->gmtOffset) * 60 * 60;
			$timestamp     = strtotime($item['updated_at']) + $offsetSeconds;
			$result        = gmdate('Y-m-d H:i:s', $timestamp);
			$date          = new DateTime($result);

			$html .= sprintf(
				'<span>%s <b>%s, %s</b></span>',
				__('at', 'license-manager-for-woocommerce'),
				$date->format($this->dateFormat),
				$date->format($this->timeFormat)
			);
		}

		if ($item['updated_by']) {
			$user = get_user_by('id', $item['updated_by']);

			if ($user instanceof WP_User) {
				if (current_user_can('manage_options')) {
					$html .= sprintf(
						'<br>%s <a href="%s">%s</a>',
						__('by', 'license-manager-for-woocommerce'),
						get_edit_user_link($user->ID),
						$user->display_name
					);
				} else {
					$html .= sprintf(
						'<br><span>%s %s</span>',
						__('by', 'license-manager-for-woocommerce'),
						$user->display_name
					);
				}
			}
		}

		return $html;
	}

	/**
	 * Expires at column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @throws Exception
	 * @return string
	 */
	public function column_expires_at( $item ) {
		if (!$item['expires_at']) {
			return '';
		}

		$offsetSeconds      = floatval($this->gmtOffset) * 60 * 60;
		$timestampExpiresAt = strtotime($item['expires_at']) + $offsetSeconds;
		$timestampNow       = strtotime('now') + $offsetSeconds;
		$datetimeString     = gmdate('Y-m-d H:i:s', $timestampExpiresAt);
		$date               = new DateTime($datetimeString);

		if ($timestampNow > $timestampExpiresAt) {
			return sprintf(
				'<span class="lmfwc-date lmfwc-status expired" title="%s">%s</span><br>',
				__('Expired'),
				wp_date($this->dateTimeFormat, strtotime($item['expires_at']))
			);
		}

		return sprintf(
			'<span class="lmfwc-date lmfwc-status">%s</span>',
			wp_date($this->dateTimeFormat, strtotime($item['expires_at']))
		);
	}

	/**
	 * Valid for column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_valid_for( $item ) {
		$html = '';

		if ($item['valid_for']) {
			$html = sprintf(
				'<b>%d</b> %s<br><small>%s</small>',
				intval($item['valid_for']),
				__('day(s)', 'license-manager-for-woocommerce'),
				__('After purchase', 'license-manager-for-woocommerce')
			);
		}

		return $html;
	}

	/**
	 * Status column.
	 *
	 * @param array $item Associative array of column name and value pairs
	 *
	 * @return string
	 */
	public function column_status( $item ) {
		switch ($item['status']) {
			case LicenseStatus::SOLD:
				$status = sprintf(
					'<div class="lmfwc-status sold"><span class="dashicons dashicons-yes"></span> %s</div>',
					__('Sold', 'license-manager-for-woocommerce')
				);
				break;
			case LicenseStatus::DELIVERED:
				$status = sprintf(
					'<div class="lmfwc-status delivered"><span class="lmfwc-icons delivered"></span> %s</div>',
					__('Delivered', 'license-manager-for-woocommerce')
				);
				break;
			case LicenseStatus::ACTIVE:
				$status = sprintf(
					'<div class="lmfwc-status active"><span class="dashicons dashicons-marker"></span> %s</div>',
					__('Active', 'license-manager-for-woocommerce')
				);
				break;
			case LicenseStatus::INACTIVE:
				$status = sprintf(
					'<div class="lmfwc-status inactive"><span class="dashicons dashicons-marker"></span> %s</div>',
					__('Inactive', 'license-manager-for-woocommerce')
				);
				break;
			default:
				$status = sprintf(
					'<div class="lmfwc-status unknown">%s</div>',
					__('Unknown', 'license-manager-for-woocommerce')
				);
				break;
		}

		return $status;
	}

	/**
	 * Default column value.
	 *
	 * @param array  $item        Associative array of column name and value pairs
	 * @param string $column_name Name of the current column
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		/**
		* Filter lmfwc_table_licenses_column_value
		* 
		* @since 1.0
		**/
		$item = apply_filters('lmfwc_table_licenses_column_value', $item, $column_name);

		return $item[$column_name];
	}

	/**
	 * Defines sortable columns and their sort value.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortableColumns = array(
			'id'         => array( 'id', true ),
			'order_id'   => array( 'order_id', true ),
			'product_id' => array( 'product_id', true ),
			'user_id'    => array( 'user_id', true ),
			'expires_at' => array( 'expires_at', true ),
			'status'     => array( 'status', true ),
			'created'    => array( 'created_at', true ),
			'updated'    => array( 'updated_at', true ),
			'activation' => array( 'times_activated_max', true ),
		);
		/**
		* Filter lmfwc_table_licenses_column_sortable
		* 
		* @since 1.0
		**/
		return apply_filters('lmfwc_table_licenses_column_sortable', $sortableColumns);
	}

	/**
	 * Defines items in the bulk action dropdown.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'activate'          => __('Activate', 'license-manager-for-woocommerce'),
			'deactivate'        => __('Deactivate', 'license-manager-for-woocommerce'),
			'mark_as_sold'      => __('Mark as sold', 'license-manager-for-woocommerce'),
			'mark_as_delivered' => __('Mark as delivered', 'license-manager-for-woocommerce'),
			'delete'            => __('Delete', 'license-manager-for-woocommerce'),
			'export_csv'        => __('Export (CSV)', 'license-manager-for-woocommerce'),
			'export_pdf'        => __('Export (PDF)', 'license-manager-for-woocommerce'),
		);

		return $actions;
	}

	/**
	 * Processes the currently selected action.
	 */
	private function processBulkActions() {
		$action = $this->current_action();
		switch ($action) {
			case 'activate':
				$this->toggleLicenseKeyStatus(LicenseStatus::ACTIVE);
				break;
			case 'deactivate':
				$this->toggleLicenseKeyStatus(LicenseStatus::INACTIVE);
				break;
			case 'mark_as_sold':
				$this->toggleLicenseKeyStatus(LicenseStatus::SOLD);
				break;
			case 'mark_as_delivered':
				$this->toggleLicenseKeyStatus(LicenseStatus::DELIVERED);
				break;
			case 'delete':
				$this->deleteLicenseKeys();
				break;
			case 'export_pdf':
				$this->exportLicenseKeys('PDF');
				break;
			case 'export_csv':
				$this->exportLicenseKeys('CSV');
				break;
			default:
				break;
		}
	}

	/**
	 * Initialization function.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		$this->processBulkActions();

		$perPage     = $this->get_items_per_page('lmfwc_licenses_per_page', 10);
		$currentPage = $this->get_pagenum();
		$totalItems  = $this->getLicenseKeyCount();

		$this->set_pagination_args(
			array(
				'total_items' => $totalItems,
				'per_page'    => $perPage,
				'total_pages' => ceil($totalItems / $perPage),
			)
		);

		$this->items = $this->getLicenseKeys($perPage, $currentPage);
	}

	/**
	 * Retrieves the licenses from the database.
	 *
	 * @param int $perPage    Default amount of licenses per page
	 * @param int $pageNumber Default page number
	 *
	 * @return array
	 */
	private function getLicenseKeys( $perPage = 20, $pageNumber = 1 ) {
		global $wpdb;
		$lmfwc_data = $_REQUEST;

		$sql = '';

		// Applies the view filter
		if ($this->isViewFilterActive()) {
			$sql .= $wpdb->prepare(' AND status = %d', intval($lmfwc_data['status']));
		}

		// Applies the search box filter
		if (array_key_exists('s', $lmfwc_data) && $lmfwc_data['s']) {
			$sql .= $wpdb->prepare(
				' AND hash = %s',
				/**
				* Filter lmfwc_hash
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_hash', sanitize_text_field($lmfwc_data['s']))
			);
		}

		// Applies the order filter
		if (isset($lmfwc_data['order-id']) && is_numeric($lmfwc_data['order-id'])) {
			$sql .= $wpdb->prepare(' AND order_id = %d', intval($lmfwc_data['order-id']));
		}

		// Applies the product filter
		if (isset($lmfwc_data['product-id']) && is_numeric($lmfwc_data['product-id'])) {
			$sql .= $wpdb->prepare(' AND product_id = %d', intval($lmfwc_data['product-id']));
		}

		// Applies the user filter
		if (isset($lmfwc_data['user-id']) && is_numeric($lmfwc_data['user-id'])) {
			$sql .= $wpdb->prepare(' AND user_id = %d', intval($lmfwc_data['user-id']));
		}

	   $sql .= isset($lmfwc_data['orderby']) && !empty(sanitize_sql_orderby($lmfwc_data['orderby'])) ?  ' ORDER BY ' . sanitize_sql_orderby($lmfwc_data['orderby']) : ' ORDER BY ' . sanitize_sql_orderby('id');
		$sql .= isset($lmfwc_data['order']) && !empty(sanitize_sql_orderby($lmfwc_data['order']))   ? ' ' . sanitize_sql_orderby($lmfwc_data['order']) : sanitize_sql_orderby(' DESC');
		$sql .= " LIMIT {$perPage}";
		$sql .= ' OFFSET ' . ( $pageNumber - 1 ) * $perPage;
		$wpdb->sqlQueryCondition = $sql;
		// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM %1s WHERE 1 = 1 {$wpdb->sqlQueryCondition}", $this->table), ARRAY_A);
	}

	private function getLicenseKeyCount() {
		$lmfwc_data = $_REQUEST;
		global $wpdb;

		$sql = '';

		if ($this->isViewFilterActive()) {
			$sql .= $wpdb->prepare(' AND status = %d', intval($lmfwc_data['status']));
		}

		if (isset($lmfwc_data['order-id'])) {
			$sql .= $wpdb->prepare(' AND order_id = %d', intval($lmfwc_data['order-id']));
		}

		if (array_key_exists('s', $lmfwc_data) && $lmfwc_data['s']) {
			$sql .= $wpdb->prepare(
				' AND hash = %s',
				/**
				* Filter lmfwc_hash
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_hash', sanitize_text_field($lmfwc_data['s']))
			);
		}
		$wpdb->sqlQueryCondition = $sql;
		// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %1s WHERE 1 = 1 {$wpdb->sqlQueryCondition}", $this->table ) );
	}


	/**
	 * Output in case no items exist.
	 */
	public function no_items() {
		esc_html_e('No license keys found.', 'license-manager-for-woocommerce');
	}

	/**
	 * Set the table columns.
	 */
	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'license_key' => __('License key', 'license-manager-for-woocommerce'),
			'order_id'    => __('Order', 'license-manager-for-woocommerce'),
			'product_id'  => __('Product', 'license-manager-for-woocommerce'),
			'user_id'     => __('Customer', 'license-manager-for-woocommerce'),
			'activation'  => __('Activation', 'license-manager-for-woocommerce'),
			'expires_at'  => __('Expires at', 'license-manager-for-woocommerce'),
			'valid_for'   => __('Valid for', 'license-manager-for-woocommerce'),
			'status'      => __('Status', 'license-manager-for-woocommerce'),
			'created'     => __('Created', 'license-manager-for-woocommerce'),
			'updated'     => __('Updated', 'license-manager-for-woocommerce'),
		);
		/**
		* Filter lmfwc_table_licenses_column_name
		* 
		* @since 1.0
		**/
		return apply_filters('lmfwc_table_licenses_column_name', $columns);
	}

	/**
	 * Checks if the given nonce is (still) valid.
	 *
	 * @param string $nonce The nonce to check
	 * @throws Exception
	 */
	private function verifyNonce( $nonce ) {
		$data = $_REQUEST;
		$currentNonce = $data['_wpnonce'];

		if (!wp_verify_nonce($currentNonce, $nonce)
			&& !wp_verify_nonce($currentNonce, 'bulk-' . $this->_args['plural'])
		) {
			AdminNotice::error(__('The nonce is invalid or has expired.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(sprintf('admin.php?page=%s', AdminMenus::LICENSES_PAGE))
			);

			exit();
		}
	}

	/**
	 * Makes sure that license keys were selected for the bulk action.
	 */
	private function verifySelection() {
		// No ID's were selected, show a warning and redirect
		if (!array_key_exists('id', $_REQUEST)) {
			$message = sprintf(esc_html__('No license keys were selected.', 'license-manager-for-woocommerce'));
			AdminNotice::warning($message);

			wp_safe_redirect(
				admin_url(
					sprintf('admin.php?page=%s', AdminMenus::LICENSES_PAGE)
				)
			);

			exit();
		}
	}

	/**
	 * Changes the license key status
	 *
	 * @param int $status
	 * @throws Exception
	 */
	private function toggleLicenseKeyStatus( $status ) {
		$data = $_REQUEST;
		switch ($status) {
			case LicenseStatus::SOLD:
				$nonce = 'sell';
				break;
			case LicenseStatus::DELIVERED:
				$nonce = 'deliver';
				break;
			case LicenseStatus::ACTIVE:
				$nonce = 'activate';
				break;
			default:
				$nonce = 'deactivate';
				break;
		}

		$this->verifyNonce($nonce);
		$this->verifySelection();

		$licenseKeyIds = (array) $data['id'];
		$count         = 0;

		foreach ($licenseKeyIds as $licenseKeyId) {
			$license = LicenseResourceRepository::instance()->find($licenseKeyId);

			LicenseResourceRepository::instance()->update($licenseKeyId, array( 'status' => $status ));

			// The license has a product assigned to it, perhaps a stock update is necessary
			if ( null !== $license->getProductId()  ) {
				// License was active, but no longer is
				if (  LicenseStatus::ACTIVE === $license->getStatus()  && LicenseStatus::ACTIVE  !== $status ) {
					// Update the stock
					/**
					* Filter lmfwc_stock_decrease
					* 
					* @since 1.0
					**/
					apply_filters('lmfwc_stock_decrease', $license->getProductId());
				}

				// License was not active, but is now
				if ( LicenseStatus::ACTIVE !==  $license->getStatus()  && LicenseStatus::ACTIVE === $status ) {
					// Update the stock
					/**
					* Filter lmfwc_stock_increase
					* 
					* @since 1.0
					**/
					apply_filters('lmfwc_stock_increase', $license->getProductId());
				}
			}

			$count++;
		}

		// Set the admin notice, redirect and exit
		AdminNotice::success(sprintf(
			/* translators: %s is the page of licences */
			esc_html__('%d license key(s) updated successfully.', 'license-manager-for-woocommerce'), $count));
		wp_safe_redirect(admin_url(sprintf('admin.php?page=%s', AdminMenus::LICENSES_PAGE)));
		exit();
	}

	/**
	 * Removes the license key(s) permanently from the database.
	 *
	 * @throws Exception
	 */
	private function deleteLicenseKeys() {
		$data = $_REQUEST;
		$this->verifyNonce('delete');
		$this->verifySelection();

		$licenseKeyIds = (array) $data['id'];
		$count         = 0;

		foreach ($licenseKeyIds as $licenseKeyId) {
			$license = LicenseResourceRepository::instance()->find($licenseKeyId);

			if (!$license) {
				continue;
			}

			$result = LicenseResourceRepository::instance()->delete((array) $licenseKeyId);

			if ($result) {
				// Update the stock
				if ($license->getProductId() !== null && $license->getStatus() === LicenseStatus::ACTIVE) {
					/**
					* Filter lmfwc_stock_decrease
					* 
					* @since 1.0
					**/
					apply_filters('lmfwc_stock_decrease', $license->getProductId());
				}
				$activations = ActivationsResourceRepository::instance()->deleteBy(
				array(
					'license_id' => $license->getId(),
				  )
				);
			   
				$count += $result;
			}
		}

		$message = sprintf(
			/* translators: %s is the page of Licenses */
			esc_html__('%d license key(s) permanently deleted.', 'license-manager-for-woocommerce'), $count);

		// Set the admin notice
		AdminNotice::success($message);

		// Redirect and exit
		wp_safe_redirect(
			admin_url(
				sprintf('admin.php?page=%s', AdminMenus::LICENSES_PAGE)
			)
		);
	}

	/**
	 * Initiates a file download of the exported licenses (PDF or CSV).
	 *
	 * @param string $type
	 * @throws Exception
	 */
	private function exportLicenseKeys( $type ) {
		$data = $_REQUEST;
		$this->verifySelection();
		if ( 'PDF' === $type ) {
			$this->verifyNonce('export_pdf');
			/**
			 * Action lmfwc_export_license_keys_pdf
			 * 
			 * @since 1.0
			**/
			do_action('lmfwc_export_license_keys_pdf', (array) $data['id']);
		}

		if ( 'CSV'  === $type ) {
			$this->verifyNonce('export_csv');
			/**
			 * Action lmfwc_export_license_keys_csv
			 * 
			 * @since 1.0
			**/
			do_action('lmfwc_export_license_keys_csv', (array) $data['id']);
		}
	}

	/**
	 * Checks if there are currently any license view filters active.
	 *
	 * @return bool
	 */
	private function isViewFilterActive() {
		if (array_key_exists('status', $_GET)
			&& in_array($_GET['status'], LicenseStatus::$status)
		) {
			return true;
		}

		return false;
	}

	/**
	 * Displays the search box.
	 *
	 * @param string $text
	 * @param string $input_id
	 */
	public function search_box( $text, $input_id ) {
		if (empty($_REQUEST['s']) && !$this->has_items()) {
			return;
		}

		$input_id    = $input_id . '-search-input';
		$searchQuery = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';

		echo '<p class="search-box">';
		echo '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html( $text ) . ':</label>';
		echo '<input type="search" id="' . esc_attr($input_id) . '" name="s" value="' . esc_attr($searchQuery) . '" />';

		submit_button(
			$text, '', '', false,
			array(
				'id' => 'search-submit',
			)
		);

		echo '</p>';
	}
}