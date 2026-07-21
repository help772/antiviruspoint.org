<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handle the checkout-specific functionality.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr {

	public const RETRY_COUNT = 5;
	public const WC_AVATAX_ELR_PAYMENT_STATUS = "_wc_avatax_elr_payment_status";

	/**
	 * Order meta key holding the AR-Outbound (Application Response) document
	 * processing status. One of: invoice_sent | complete | error.
	 *
	 * AR-Outbound is a separate Avalara document for the same order, so it is
	 * tracked independently of the order invoice meta (`_wc_avatax_elr_status`).
	 */
	public const WC_AVATAX_AR_OUTBOUND_STATUS = "_wc_avatax_ar_outbound_status";

	/**
	 * Order meta key holding the AR-Outbound document ID returned by Avalara
	 * when the Application Response is submitted.
	 */
	public const WC_AVATAX_AR_OUTBOUND_DOCUMENT_ID = "_wc_avatax_ar_outbound_document_id";

	/**
	 * Order meta key holding the AR-Outbound status messages returned by the
	 * invoice-status API.
	 */
	public const WC_AVATAX_AR_OUTBOUND_STATUS_MESSAGES = "_wc_avatax_ar_outbound_status_messages";

	/**
	 * Order meta key holding the AR-Outbound downloadable media types returned
	 * by the invoice-status API once the document reaches "Complete". Stored
	 * separately from the order invoice media types (`_wc_avatax_elr_media_type`)
	 * so the AR document download dropdown does not collide with the invoice.
	 */
	public const WC_AVATAX_AR_OUTBOUND_MEDIA_TYPE = "_wc_avatax_ar_outbound_media_type";

	/**
	 * Order meta key holding the locally cached AR-Outbound document files,
	 * keyed by media type. Kept separate from the invoice download cache
	 * (`_wc_avatax_elr_downloaded_files`).
	 */
	public const WC_AVATAX_AR_OUTBOUND_DOWNLOADED_FILES = "_wc_avatax_ar_outbound_downloaded_files";

	/**
	 * Construct the class.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Adds handler actions and filters.
	 *
	 * @since 3.0.0
	 * @codeCoverageIgnore
	 */
	protected function add_hooks() {
		if(wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled()) {
			//Add new option to order action to send ivoice to ELR
			add_action( 'woocommerce_order_actions', array( $this, 'add_order_action' ) );

			// Add custom meta box to WooCommerce orders page
			add_action( 'add_meta_boxes', array( $this, 'elr_order_meta_box' ), 999, 1);

			// Send to Avalara E-invoicing and Live Reporting manually through the admin action
			add_action( 'woocommerce_order_action_wc_avatax_elr_send', array( $this, 'process_elr' ));

			// Report payment to Avalara E-invoicing and Live Reporting manually through the admin action
			add_action('woocommerce_order_action_wc_avatax_elr_report_payment', array($this, 'reportPaymentToELR'));

			// Send AR-Outbound (Application Response) to Avalara manually through the admin action
			add_action('woocommerce_order_action_wc_avatax_elr_send_ar_outbound', array($this, 'process_ar_outbound'));

			// Send to Avalara E-invoicing and Live Reporting when status change
			$this->setOrderStatusActions();

			// Process refund to ELR
			add_action( 'woocommerce_order_refunded', [ $this, 'process_refund_to_elr' ], 10, 2 );

			// Add cron schedule methods
			add_filter('cron_schedules', [$this, 'add_cron_schedules']);
			add_action('wc_avatax_elr_status_refresh', [$this, 'handle_status_refresh']);
			add_action('update_option_wc_avatax_elr_status_refresh_frequency', [$this, 'schedule_status_refresh_event']);
			
			// Refresh the status of queued invoice
			add_action('wc_avatax_elr_check_invoice_status', [$this, 'handle_invoice_status_check']);

			add_action('woocommerce_payment_complete', array($this, 'reportPaymentToELR'), 998, 1);

		}
	}

	/**
	 * Sets up WooCommerce order status action hooks for ELR (E-Legal Requirements) processing.
	 * 
	 * Retrieves selected order statuses from WordPress options and registers action hooks
	 * to automatically process ELR when orders transition to these statuses.
	 * 
	 * @since 3.8.4
	 * 
	 * @return void
	 */
	public function setOrderStatusActions() {
		$selectedOrderStatuses = get_option("wc_avatax_elr_selected_status", []);
		if(!empty($selectedOrderStatuses)) {
			foreach($selectedOrderStatuses as $orderStatus) {
				add_action('woocommerce_order_status_' . str_replace("wc-", "", $orderStatus) , array($this, 'auto_process_elr'), 11, 1);
			}
		}

		$paymentReportingOrderStatuses = get_option("wc_avatax_elr_payment_reporting_status", []);
		if(!empty($paymentReportingOrderStatuses)) {
			foreach($paymentReportingOrderStatuses as $orderStatus) {
				add_action('woocommerce_order_status_' . str_replace("wc-", "", $orderStatus) , array($this, 'maybeReportPaymentOnStatusChange'), 998, 1);
			}
		}

		$arOutboundOrderStatuses = get_option("wc_avatax_elr_ar_outbound_status", []);
		if(!empty($arOutboundOrderStatuses)) {
			foreach($arOutboundOrderStatuses as $orderStatus) {
				// Run AR-Outbound at a later priority than the invoice auto-send
				// (priority 11 above). When the same order status is selected for
				// both invoice reporting and AR-Outbound reporting, this guarantees
				// the ELR invoice is generated first — process_elr() synchronously
				// stores _wc_avatax_invoice_id — so the is_invoice_posted() guard in
				// can_send_ar_outbound() passes. The invoice document is a hard
				// prerequisite for sending the Application Response, so without this
				// ordering AR-Outbound could fire first and be silently skipped.
				add_action('woocommerce_order_status_' . str_replace("wc-", "", $orderStatus) , array($this, 'auto_process_ar_outbound'), 12, 1);
			}
		}
	}

	/**
	 * Add custom meta box.
	 *
	 * @since 3.0.0
	 * @codeCoverageIgnore
	 */
	public function elr_order_meta_box() {
		//If ELR not connected, bail out.
		if(!wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled()) return;

		// Get current screen
		if ( ! function_exists( 'wc_get_page_screen_id' ) ) {
			return false;
		}
		$screen = get_current_screen();
    
		// Check if we're on the shop_order edit page
		if ($screen && $screen->id === wc_get_page_screen_id( 'shop_order' )) {
			add_meta_box(
				'elr-order-meta-box',
				__( 'ELR order', 'woocommerce-avatax' ),
				function() {
					// Get the saved value
					$order = $this->get_current_order();

					$status_details = $this->get_invoice_status_details($order);
					$invoice_status = $status_details['status'];
					$invoice_status_messages = $status_details['messages'];
					$processing_id = $status_details['processing_id'];
					
					// Output the input field
					$allowed_html = array_merge(
						wp_kses_allowed_html( 'post' ),
						array(
							'select' => array(
								'id'    => true,
								'class' => true,
								'name'  => true,
							),
							'option' => array(
								'value'    => true,
								'selected' => true,
							),
							'button' => array(
								'type'         => true,
								'id'           => true,
								'class'        => true,
								'title'        => true,
								'data-order_id' => true,
							),
						)
					);
					echo wp_kses( wc_avatax()->wc_avatax_elr_utilities()->get_elr_status_html( $order->get_id(), $invoice_status, $processing_id, $this->get_invoice_status_messages( $invoice_status_messages ) ), $allowed_html );
				},
				null,
				'side',
				'core'
			);
			$this->elr_order_refund_meta_box();
			$this->ar_outbound_order_meta_box();
		}
	}

	/**
	 * Add custom meta box for AR-Outbound (Application Response).
	 *
	 * Mirrors the ELR order meta box but tracks the AR-Outbound document
	 * independently (its own status, document ID and messages) so a single
	 * order can carry both an invoice and an Application Response.
	 *
	 * @since 3.8.4
	 * @codeCoverageIgnore
	 */
	public function ar_outbound_order_meta_box() {
		$order = $this->get_current_order();

		// AR-Outbound applies to orders only (not refunds).
		if (! $order instanceof WC_Order || $order->get_parent_id()) {
			return;
		}

		add_meta_box(
			'ar-outbound-meta-box',
			__( 'AR Outbound', 'woocommerce-avatax' ),
			function() {
				$order = $this->get_current_order();

				$status_details = $this->get_ar_outbound_status_details($order);

				$allowed_html = array_merge(
					wp_kses_allowed_html( 'post' ),
					array(
						'select' => array(
							'id'    => true,
							'class' => true,
							'name'  => true,
						),
						'option' => array(
							'value'    => true,
							'selected' => true,
						),
						'button' => array(
							'type'                 => true,
							'id'                   => true,
							'class'                => true,
							'title'                => true,
							'data-order_id'        => true,
							'data-document_context' => true,
						),
					)
				);

				echo wp_kses(
					wc_avatax()->wc_avatax_elr_utilities()->get_ar_outbound_status_html(
						$order->get_id(),
						$status_details['status'],
						$status_details['document_id'],
						$this->get_invoice_status_messages( $status_details['messages'] )
					),
					$allowed_html
				);
			},
			null,
			'side',
			'core'
		);
	}

	/**
	 * Add custom meta box for Refund.
	 *
	 * @since 3.0.0
	 * @codeCoverageIgnore
	 */
	public function elr_order_refund_meta_box() {
		// Get the saved value
		$order = $this->get_current_order();
		$order_refunds = $order->get_refunds();

		if(!empty($order_refunds)){
			add_meta_box(
				'elr-refund-meta-box',
				__( 'ELR refund', 'woocommerce-avatax' ),
				function() {
					
						// Get the saved value
						$order = $this->get_current_order();
						$order_refunds = $order->get_refunds();
						// Loop through the order refunds array
						foreach( $order_refunds as $refund ){
							$status_details = $this->get_invoice_status_details($refund);
							$invoice_status = $status_details['status'];
							$invoice_status_messages = $status_details['messages'];
							$processing_id = $status_details['processing_id'];
							// Output the input field
							$allowed_html = array_merge(
								wp_kses_allowed_html( 'post' ),
								array(
									'select' => array(
										'id'    => true,
										'class' => true,
										'name'  => true,
									),
									'option' => array(
										'value'    => true,
										'selected' => true,
									),
									'button' => array(
										'type'         => true,
										'id'           => true,
										'class'        => true,
										'title'        => true,
										'data-order_id' => true,
									),
								)
							);
							echo wp_kses( wc_avatax()->wc_avatax_elr_utilities()->get_elr_refund_status_html( $refund->get_id(), $invoice_status, $processing_id, $this->get_invoice_status_messages( $invoice_status_messages ) ), $allowed_html );
						}
				},
				null,
				'side',
				'core'
			);
		}
	}

	/**
	 * Gets invoice status details from an order.
	 * 
	 * Retrieves and processes the invoice status information for a given order.
	 * If the invoice is posted, gets the current status from Avalara and updates
	 * the order meta accordingly.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order The order object to get invoice status for
	 * @return array {
	 *     Array containing invoice status details
	 *     
	 *     @type string $status   The current status of the invoice
	 *     @type string $messages Any status messages associated with the invoice
	 * }
	 */
	public function get_invoice_status_details($order) {
		$invoice_status = "";
		$invoice_status_messages = "";
		$invoice_id="";
		$mediaTypes = "";
		$response_keys = [];

		$invoice_status_response = $this->get_invoice_status($order);
		
		if ($invoice_status_response) {
			$invoice_status = $invoice_status_response->get_status();
			$mediaTypes = $invoice_status_response->get_downloadable_media_types();
			$invoice_status_messages = $invoice_status_response->get_status_messages();
			$response_keys = $invoice_status_response->get_response_key_map();
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_invoice_id');

			wc_avatax()->wc_avatax_utilities()->update_order_meta($order->get_id(), '_wc_avatax_business_status', $invoice_status_response->get_business_status());
			
			if ($invoice_status == "Complete") {
				wc_avatax()->wc_avatax_utilities()->update_order_meta(
					$order->get_id(), 
					"_wc_avatax_elr_status", 
					"complete"
				);
				// Code to get media type from API response and save order meta table
				if (isset($mediaTypes)) {
					wc_avatax()->wc_avatax_utilities()->update_order_meta(
						$order->get_id(),
						"_wc_avatax_elr_media_type",
						$mediaTypes
					);

					if ('yes' === get_option('wc_avatax_elr_download_documents', 'no')) {
						// Code to download and save invoice documents for all available media types
						$this->download_and_save_invoice_documents($order, $invoice_id, $mediaTypes);
					}
				}
			} elseif ($invoice_status == "Error") {
				wc_avatax()->wc_avatax_utilities()->update_order_meta(
					$order->get_id(),
					"_wc_avatax_elr_status", 
					"error"
				);
			}
			
			// Update invoice status message to order meta
			wc_avatax()->wc_avatax_utilities()->update_order_meta(
				$order->get_id(), 
				"_wc_avatax_elr_status_messages", 
				$invoice_status_messages
			);

			

			if (!empty($response_keys)) {
				wc_avatax()->wc_avatax_utilities()->update_order_meta(
					$order->get_id(),
					"_wc_avatax_elr_response_keys",
					$response_keys
				);
			}
		} elseif ($this->is_invoice_completed($order)) {
			$invoice_status = "Complete";
			$invoice_status_messages = wc_avatax()->wc_avatax_utilities()->get_order_meta(
				$order->get_id(), 
				"_wc_avatax_elr_status_messages"
			);
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_invoice_id');
		} elseif ($this->has_invoice_error($order)) {
			$invoice_status = "Error";
			$invoice_status_messages = wc_avatax()->wc_avatax_utilities()->get_order_meta(
				$order->get_id(), 
				"_wc_avatax_elr_status_messages"
			);
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_invoice_id');
		}
		
		return [
			'status' => $invoice_status,
			'messages' => $invoice_status_messages,
			'processing_id' => $invoice_id
		];
	}

	/**
	 * Downloads and saves invoice documents for all available media types.
	 *
	 * @param WC_Order $order The order object
	 * @param string $invoice_id The invoice ID
	 * @param array $media_types Array of available media types
	 */
	public function download_and_save_invoice_documents($order, $invoice_id, $media_types) {
		if (empty($media_types) || !is_array($media_types)) {
			return;
		}

		$stored_files = [];

		foreach ($media_types as $media_type) {
			try {
				$download_response = wc_avatax()->get_elr_api()->download_invoice($invoice_id, $media_type);

				if ($download_response && isset($download_response["data"])) {
					$file_info = $this->save_downloaded_elr_document($invoice_id, $media_type, $download_response);

					if (!empty($file_info)) {
						$stored_files[$media_type] = $file_info;
					}
				}
			
			} catch (Exception $e) {
				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr("Failed to download {$media_type} file for invoice {$invoice_id}: " . $e->getMessage());
				}
			}
		}

		// Store file links in order meta
		if (!empty($stored_files)) {
			wc_avatax()->wc_avatax_utilities()->update_order_meta(
				$order->get_id(),
				'_wc_avatax_elr_downloaded_files',
				$stored_files
			);
		}
	}

	/**
	 * Saves a downloaded ELR document to the WordPress uploads directory.
	 *
	 * @param string $invoice_id The invoice ID
	 * @param string $media_type The media type of the document
	 * @param array $download_response The API response containing file data
	 * @return array $file_info The Saved file info
	 */
	public function save_downloaded_elr_document($invoice_id, $media_type, $download_response) {
		// Extract file extension and base64 data from API response
		$fileExtension = strtolower(trim($download_response["data"]->fileExtension, '. '));
		$base64Data = $download_response["data"]->data ?? '';

		// Generate filename based on media type (UBL files get special naming)
		$suffix = '_' . sanitize_title_with_dashes(str_replace('/', '_', strtolower((string) $media_type)));

		// Change fileExtension for UBL media_type (UBL files get special extension)
		$fileExtension = stripos($fileExtension, "ubl") !== false ? 'xml' : $fileExtension;

		$filename = sanitize_file_name($invoice_id . $suffix . '.' . $fileExtension);

		// Validate that file data exists
		if (empty($base64Data)) {
			if (wc_avatax()->elr_logging_enabled()) {
                wc_avatax()->log_elr("No data available for invoice {$invoice_id}.");
            }
			return;
		}

		// Create AvaTax upload directory
		$upload_dir = $this->get_avatax_upload_directory();
		if (!$upload_dir) {
			return;
		}

		$upload_path = $upload_dir['path'] . '/' . $filename;

		// Decode base64 data
		$decoded = base64_decode($base64Data);

		// Save file to uploads directory
		if ($this->write_file($upload_path, $decoded) === false) {
			if (wc_avatax()->elr_logging_enabled()) {
                wc_avatax()->log_elr("Failed to save {$media_type} file for invoice {$invoice_id}.");
            }

			return;
		}

		// Generate and return public URL for the saved file
		$file_url  = $upload_dir['url'] . '/' . $filename;

		$file_info = [
				'file_name' => $filename,
				'file_path' => $upload_path,
				'file_url' => $file_url,
				'downloaded_at' => current_time('mysql')
			];

		return $file_info;
	}

	/**
	 * Wrapper method for file_put_contents to allow testing
	 *
	 * @param string $path File path
	 * @param mixed $data Data to write
	 * @return int|false Number of bytes written or false on failure
	 */
	protected function write_file($path, $data) {
		return file_put_contents($path, $data);
	}

	/**
	 * Wrapper method for file_exists to allow testing
	 *
	 * @param string $path File path
	 * @return bool True if file exists, false otherwise
	 */
	protected function file_exists($path) {
		return file_exists($path);
	}

	/**
	 * Wrapper method for wp_mkdir_p to allow testing
	 *
	 * @param string $path Directory path
	 * @return bool True on success, false on failure
	 */
	protected function wp_mkdir_p($path) {
		return wp_mkdir_p($path);
	}

	/**
	 * Get or create AvaTax upload directory
	 *
	 * @return array|false Upload directory info or false on failure
	 */
	public function get_avatax_upload_directory() {
		$wp_upload_dir = wp_upload_dir();
		
		if ($wp_upload_dir['error']) {
			return false;
		}
		
		$avatax_dir = $wp_upload_dir['basedir'] . '/avatax-elr';
		$avatax_url = $wp_upload_dir['baseurl'] . '/avatax-elr';
		
		// Create directory if it doesn't exist
		if (!$this->file_exists($avatax_dir)) {
			$this->wp_mkdir_p($avatax_dir);
			
			// Add .htaccess for security
			$htaccess_content = "Options -Indexes\n<Files *.php>\nDeny from all\n</Files>";
			$this->write_file($avatax_dir . '/.htaccess', $htaccess_content);
		}
		
		return [
			'path' => $avatax_dir,
			'url' => $avatax_url
		];
	}


	/**
	 * Get the current order object.
	 * 
	 * Retrieves the current order object while maintaining compatibility with both
	 * traditional post-based storage and HPOS (High-Performance Order Storage).
	 * Checks for order in the following sequence:
	 * 1. Global $theorder variable (WC_Order object)
	 * 2. Global $theorder variable (WP_Post object for legacy storage)
	 * 3. Direct order ID
	 * 4. URL parameter 'id'
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Order|false WC_Order object if found, false otherwise.
	 */
	public function get_current_order() {
		global $theorder;
		$order = false;
	
		if (isset($theorder)) {
			if ($theorder instanceof WC_Order) {
				$order = $theorder;
			} elseif ($theorder instanceof WP_Post) {
				// Handle legacy post-based storage
				$order_id = $theorder->ID;
			if ('shop_order' === get_post_type($order_id)) {
					$order = wc_get_order($order_id);
			}
			} elseif (is_numeric($theorder)) {
				// Handle direct order ID
				$order = wc_get_order($theorder);
			}
		}
	
		// If order is still not found, try getting from request
		if (!$order && !empty($_GET['id'])) {
			$order = wc_get_order((int) $_GET['id']);
		}
	
		return $order;
	}

	/**
	 * Sends order to Avalara E-invoicing and Live Reporting after an order is marked complete.
	 *
	 * Handles the ELR processing for a completed order. The function checks if the invoice hasn't been posted
	 * or if there was a previous error, and if either condition is true, it triggers the ELR processing.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param int|WC_Order $order The order ID or WC_Order object to process
	 * @return void
	 */
	public function auto_process_elr($order){
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		if ( ! $order ) {
			return;
		}
		$this->process_elr($order);
	}

	/**
	 * Sends invoice to Avalara.
	 *
	 * @since 3.0.0
	 * @param WC_Order $order The order object.
	 * @return \WC_Order|bool $order The processed order or false on failure.
	 */
	public function process_elr( WC_Order $order ) {
		try{
			if(!$this->can_send_invoice($order)) return;

			//Performance log variables
			$execution_start = hrtime(true);
			$api_time = $execution_end = 0.0;

			// Call the API
			$order_id = $order->get_id();
			wc_avatax()->wc_avatax_utilities()->delete_order_meta($order_id, "_wc_avatax_elr_status", wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, "_wc_avatax_elr_status"));
			wc_avatax()->wc_avatax_utilities()->delete_order_meta($order_id, "_wc_avatax_invoice_id", wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, "_wc_avatax_invoice_id"));
			$invoiceData = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($order_id, $entity_type = 'order');
			// Send the invoice
			$response = wc_avatax()->get_elr_api()->submit_invoice($order, $invoiceData);

			if($response && $invoice = $response->process_response()){
				$api_time = $response->get_response_time();
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, "_wc_avatax_elr_status", "invoice_sent");
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, "_wc_avatax_invoice_id", $invoice->id);
				$order->add_order_note( sprintf( __( 'Order #%s sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ), $order->get_id() ) );

				// Add to pending status queue
    			$this->add_to_status_queue($order_id, 'order');

				$execution_end = hrtime(true);
				$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
				$connector_time = $execution_time - $api_time;
				wc_avatax()->elr_logger()->log_performance_elr("SubmitInvoice", "process_elr", "Submitting order invoice.", $order->get_order_key('edit'), "SalesInvoice", $connector_time, $api_time, count($order->get_items()), $invoice->id,$invoice->mandate);
			}
		} catch ( \Exception $e ) {

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() );
			}

			return new Framework\SV_WC_API_Exception( $e->getMessage() );
		}
	}

	/**
	 * Process order refunds to ELR.
	 *
	 * @since 2.9.0
	 *
	 * @param int $order_id The order ID.
	 * @param int $refund_id The refund ID.
	 */
	public function process_refund_to_elr( $order_id, $refund_id ) {
		//Performance log variables
		$execution_start = hrtime(true);
		$api_time = $execution_end = 0.0;
		wc_avatax()->wc_avatax_utilities()->delete_order_meta($refund_id, "_wc_avatax_elr_status", wc_avatax()->wc_avatax_utilities()->get_order_meta($refund_id, "_wc_avatax_elr_status"));
		wc_avatax()->wc_avatax_utilities()->delete_order_meta($refund_id, "_wc_avatax_invoice_id", wc_avatax()->wc_avatax_utilities()->get_order_meta($refund_id, "_wc_avatax_invoice_id"));
		
		$order  = wc_get_order( $order_id );
		$refund = wc_get_order( $refund_id );

		if ( ! $order || ! $refund ) {
			wc_avatax()->log_elr( 'Order or refund not found' );
			return;
		}

		try {
			// Prepare refund payload data
			$payloadData = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($refund_id, $entity_type = 'refund');
			// Send the invoice
			$response = wc_avatax()->get_elr_api()->submit_invoice($order, $payloadData);

			if($response && $invoice = $response->process_response()){
				$api_time = $response->get_response_time();
				// Add the posted status to the refund
				wc_avatax()->wc_avatax_utilities()->update_order_meta($refund_id, "_wc_avatax_elr_status", "invoice_sent");
				wc_avatax()->wc_avatax_utilities()->update_order_meta($refund_id, "_wc_avatax_invoice_id", $invoice->id);
				$order->add_order_note( sprintf( __( 'Refund #%s sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ), $refund->get_id() ) );

				// Add to pending status queue
    			$this->add_to_status_queue($refund_id, 'refund');

				$execution_end = hrtime(true);
				$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
				$connector_time = $execution_time - $api_time;
				wc_avatax()->elr_logger()->log_performance_elr("SubmitInvoice", "process_refund_to_elr", "Submitting refund invoice.", $order->get_order_key('edit'), "SalesInvoice", $connector_time, $api_time, count($refund->get_items()), $invoice->id,$invoice->mandate);
			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() );
			}

			$message  = '<strong>' . __( 'Invoice not sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ) . '</strong> ';
			$message .= $e->getMessage();

			$order->add_order_note( $message );
		} finally {
			$this->reportPaymentToELR($refund);
		}
	}

	/**
	 * Sends an AR-Outbound (Application Response) document to Avalara when an
	 * order transitions into a status selected for AR-Outbound reporting.
	 *
	 * Wired in {@see setOrderStatusActions()} from the
	 * `wc_avatax_elr_ar_outbound_status` option, mirroring the auto invoice
	 * path ({@see auto_process_elr()}).
	 *
	 * @since 3.8.4
	 *
	 * @param int|WC_Order $order The order ID or WC_Order object.
	 * @return void
	 */
	public function auto_process_ar_outbound($order) {
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if (! $order) {
			return;
		}

		$this->process_ar_outbound($order);
	}

	/**
	 * Submits the AR-Outbound (Application Response) document for an order.
	 *
	 * Builds the payload from the `application_response_outbound` mapper
	 * (`getEinvoiceCollectionByInvoiceId()`), posts it to Avalara via the same
	 * `/documents` endpoint used by invoices, then persists the returned
	 * document ID and status on dedicated AR-Outbound meta keys so it does not
	 * collide with the order invoice meta.
	 *
	 * @since 3.8.4
	 *
	 * @param int|WC_Order $order The order ID or WC_Order object.
	 * @return void|Framework\SV_WC_API_Exception
	 */
	public function process_ar_outbound($order) {
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if (! $order instanceof WC_Order || $order->get_parent_id()) {
			return;
		}

		try {
			if (! $this->can_send_ar_outbound($order)) {
				return;
			}

			$execution_start = hrtime(true);
			$api_time = 0.0;

			$order_id = $order->get_id();

			wc_avatax()->wc_avatax_utilities()->delete_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS, wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS));
			wc_avatax()->wc_avatax_utilities()->delete_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_DOCUMENT_ID, wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_DOCUMENT_ID));

			$invoiceData = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($order_id, WC_AvaTax_Elr_Utilities::AR_OUTBOUND_ENTITY_TYPE);

			$response = wc_avatax()->get_elr_api()->submit_invoice($order, $invoiceData);

			if ($response && $invoice = $response->process_response()) {
				$api_time = $response->get_response_time();

				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS, "invoice_sent");
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_DOCUMENT_ID, $invoice->id);

				$order->add_order_note( sprintf( __( 'AR-Outbound (Application Response) for order #%s sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ), $order_id ) );

				$execution_end = hrtime(true);
				$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
				$connector_time = $execution_time - $api_time;
				wc_avatax()->elr_logger()->log_performance_elr("SubmitInvoice", "process_ar_outbound", "Submitting AR-Outbound application response.", $order->get_order_key('edit'), "ApplicationResponse", $connector_time, $api_time, count($order->get_items()), $invoice->id, $invoice->mandate);
			}
		} catch ( \Exception $e ) {

			wc_avatax()->wc_avatax_utilities()->update_order_meta($order->get_id(), self::WC_AVATAX_AR_OUTBOUND_STATUS, "error");

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() );
			}

			return new Framework\SV_WC_API_Exception( $e->getMessage() );
		}
	}

	/**
	 * Fetches and persists the AR-Outbound document status from Avalara.
	 *
	 * Uses the AR-Outbound document ID stored on the order to call the invoice
	 * status API directly (the AR document is separate from the order invoice).
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order $order The order object.
	 * @return array{status:string, messages:mixed, document_id:string}
	 */
	public function get_ar_outbound_status_details($order) {
		$status      = '';
		$messages    = '';
		$document_id = '';

		if (! $order instanceof WC_Order) {
			return array(
				'status'      => $status,
				'messages'    => $messages,
				'document_id' => $document_id,
			);
		}

		$order_id    = $order->get_id();
		$document_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_DOCUMENT_ID, true);

		// Nothing has been sent yet.
		if (empty($document_id)) {
			return array(
				'status'      => $status,
				'messages'    => $messages,
				'document_id' => $document_id,
			);
		}

		$current_meta_status = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS, true);

		// Always re-query the status so the merchant can refresh as often as
		// they like, even after the document has reached "Complete".
		$status_response = wc_avatax()->get_elr_api()->get_invoice_status($document_id);

		if ($status_response) {
			$status   = $status_response->get_status();
			$messages = $status_response->get_status_messages();

			if ('Complete' === $status) {
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS, "complete");

				// Persist the downloadable media types so the AR-Outbound status
				// box can offer the document download dropdown, mirroring the
				// order invoice flow in get_invoice_status_details().
				$media_types = $status_response->get_downloadable_media_types();
				if (isset($media_types)) {
					wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_MEDIA_TYPE, $media_types);
				}
			} elseif ('Error' === $status) {
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS, "error");
			}

			wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS_MESSAGES, $messages);

			return array(
				'status'      => $status,
				'messages'    => $messages,
				'document_id' => $document_id,
			);
		}

		// Fall back to the stored status/messages when the API isn't queried.
		$messages = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, self::WC_AVATAX_AR_OUTBOUND_STATUS_MESSAGES, true);

		if ('complete' === $current_meta_status) {
			$status = 'Complete';
		} elseif ('error' === $current_meta_status) {
			$status = 'Error';
		} elseif ('invoice_sent' === $current_meta_status) {
			$status = 'Pending';
		}

		return array(
			'status'      => $status,
			'messages'    => $messages,
			'document_id' => $document_id,
		);
	}

	/**
	 * Whether an AR-Outbound document has already been submitted for the order.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|int $order The order object or ID.
	 * @return bool
	 */
	public function is_ar_outbound_posted($order) {
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if (! $order instanceof WC_Order) {
			return false;
		}

		return ! empty(wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), self::WC_AVATAX_AR_OUTBOUND_DOCUMENT_ID, true));
	}

	/**
	 * Whether the AR-Outbound submission errored for the order.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|int $order The order object or ID.
	 * @return bool
	 */
	public function has_ar_outbound_error($order) {
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if (! $order instanceof WC_Order) {
			return false;
		}

		return "error" === wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), self::WC_AVATAX_AR_OUTBOUND_STATUS, true);
	}

	/**
	 * Whether an AR-Outbound document can be (re)sent for the order.
	 *
	 * Allowed when it has not been sent yet, or a previous attempt errored.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|int $order The order object or ID.
	 * @return bool
	 */
	public function can_send_ar_outbound($order) {
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if (! $order instanceof WC_Order || $order->get_parent_id()) {
			return false;
		}

		// AR-Outbound can only be sent once the ELR invoice has been generated.
		if (! $this->is_invoice_posted($order)) {
			return false;
		}

		return ! $this->is_ar_outbound_posted($order) || $this->has_ar_outbound_error($order);
	}

	/**
	 * Routes a `woocommerce_order_status_<status>` transition into
	 * {@see reportPaymentToELR()} for orders paid via an offline payment
	 * method.
	 *
	 * The triggering status is admin-configured via
	 * `wc_avatax_elr_payment_reporting_status` and wired in
	 * {@see setOrderStatusActions()} — so this method is only invoked when
	 * the order has just transitioned into one of those selected statuses.
	 *
	 * Online gateways already report payment via the
	 * `woocommerce_payment_complete` hook (which fires from
	 * `WC_Order::payment_complete()`), so we skip them here to avoid double
	 * reporting. Offline methods (BACS, Cheque, COD, and similar gateways
	 * that never call `payment_complete()`) need this status-driven path
	 * because the merchant manually marks the order as paid by flipping its
	 * status from the admin. The offline list is filterable via
	 * `wc_avatax_elr_offline_payment_methods` so add-on gateways (e.g. wire
	 * transfer, purchase order) can join it without a code change.
	 *
	 * Belt-and-braces: {@see isPaymentReported()} is consulted before
	 * delegating, which guards against a previously-reported order being
	 * re-flipped through a selected status (e.g. processing -> completed
	 * when both are selected for payment reporting).
	 *
	 * @since 3.8.4
	 *
	 * @param int|WC_Order $order The order id or order object provided by
	 *                            the `woocommerce_order_status_<status>` hook.
	 * @return void
	 */
	public function maybeReportPaymentOnStatusChange($order)
	{
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if (! $order instanceof WC_Order) {
			return;
		}

		if (! $this->isOfflinePaymentMethod($order)) {
			return;
		}

		if (! $this->canReportPaymentToELR($order)) {
			return;
		}

		$this->reportPaymentToELR($order);
	}

	/**
	 * Determines whether the order was paid with an offline payment method.
	 *
	 * Offline gateways never call `WC_Order::payment_complete()`, so the
	 * `woocommerce_payment_complete` hook never fires for them. This is the
	 * signal {@see maybeReportPaymentOnStatusChange()} uses to decide
	 * whether the status-driven payment-reporting path applies.
	 *
	 * The default list covers the three offline gateways shipped in
	 * WooCommerce core (`bacs`, `cheque`, `cod`); third-party offline
	 * gateways register through the `wc_avatax_elr_offline_payment_methods`
	 * filter.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order $order
	 * @return bool
	 */
	protected function isOfflinePaymentMethod(WC_Order $order)
	{
		/**
		 * Filter the list of payment method ids treated as offline for
		 * ELR payment reporting.
		 *
		 * @since 3.8.4
		 *
		 * @param string[] $methods Offline payment method ids.
		 * @param WC_Order $order   The order being evaluated.
		 */
		$offline_methods = (array) apply_filters(
			'wc_avatax_elr_offline_payment_methods',
			array('bacs', 'cheque', 'cod'),
			$order
		);

		return in_array($order->get_payment_method(), $offline_methods, true);
	}

	/**
	 * Reports payment to Avalara E-invoicing and Live Reporting.
	 *
	 * @since 3.8.4
	 * @param WC_Order|WC_Order_Refund $order The order or refund object.
	 * @return void
	 */
	public function reportPaymentToELR($order)
	{
		$order = $this->normalizePaymentReportingOrder($order);

		if (! $order || ! $this->canReportPaymentToELR($order)) {
			return;
		}

		$this->submitPaymentReporting($order);
	}

	/**
	 * Determines the ELR entity type for a payment-reporting flow.
	 *
	 * French payment e-reporting rules (DGFiP / FR DBNA mandate):
	 *  - `b2bpayment-ereporting` (B2B cross-border): source country EQ `FR`
	 *    AND destination country NOT EQ `FR` AND the buyer is a business
	 *    (`wc_avatax_Buyer_Is_Business` user meta truthy on the order's customer).
	 *  - `b2cpayment-ereporting` (B2C from France): source country EQ `FR`
	 *    AND the buyer is NOT a business. Destination country is not constrained
	 *    for B2C — FR→FR and FR→abroad both fall here.
	 *  - Anything else (source not FR, FR→FR B2B, guest treated as B2C only if
	 *    source FR, etc.) returns an empty string so the caller can short-circuit.
	 *
	 * Source country comes from the store base location (`WC()->countries->get_base_country()`).
	 * Destination country comes from the order's shipping country, falling back to
	 * the billing country when shipping is empty (matches the pattern used in
	 * `WC_AvaTax_Order_Handler::get_destination_address()`).
	 *
	 * The returned value is read from {@see WC_AvaTax_Elr_Utilities::ARR_ELR_DOCUMENT_TYPE}
	 * so the document-type strings (`xml-b2bpayment-ereporting` /
	 * `xml-b2cpayment-ereporting`) stay defined in a single source of truth.
	 *
	 * @since 3.10.0
	 *
	 * @param WC_Order|WC_Order_Refund $order The order or refund object.
	 * @return string The ELR document type value, or empty string when no rule matches.
	 */
	protected function determineEntityType($order)
	{
		if (! $this->isPaymentReportingEntityOrder($order)) {
			return 'order';
		}

		$sourceCountry = strtoupper($this->getSourceCountry());

		if ('FR' !== $sourceCountry) {
			return 'order';
		}

		return $this->resolvePaymentReportingEntityType(
			$this->isBusinessBuyer($order),
			$this->getPaymentReportingDestinationCountry($order)
		);
	}

	/**
	 * Normalize the order/refund argument for payment reporting.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $order Order id or object.
	 * @return mixed
	 */
	protected function normalizePaymentReportingOrder($order)
	{
		if (is_numeric($order) && $order > 0) {
			$order = wc_get_order($order);
		}

		return $order;
	}

	/**
	 * Determine whether payment reporting is allowed for the given entity.
	 *
	 * This is the single source of truth used by the report entrypoint, order
	 * actions, and metabox buttons so UI visibility stays aligned with the
	 * actual reporting guard.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $order Order or refund object.
	 * @return bool
	 */
	public function canReportPaymentToELR($order)
	{
		$order = $this->normalizePaymentReportingOrder($order);

		if (! $order) {
			return false;
		}

		$sourceCountry = $this->getSourceCountry();
		$destinationCountry = $this->getPaymentReportingDestinationCountry($order);
		$isBusiness = $this->isBusinessBuyer($order);

		return
			('FR' === $sourceCountry && $isBusiness && 'FR' !== $destinationCountry && $this->canSendPayment($order)) ||
			('FR' === $sourceCountry && !$isBusiness && $this->canSendPayment($order));
	}

	/**
	 * Determine whether payment reporting should be skipped.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $order Order or refund object.
	 * @return bool
	 */
	protected function shouldSkipPaymentReporting($order)
	{
		return ! $this->canReportPaymentToELR($order);
	}

	/**
	 * Submit payment reporting and handle success/error side effects.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @return void
	 */
	protected function submitPaymentReporting($order)
	{
		$orderId      = $order->get_id();
		$isRefund     = $order instanceof WC_Order_Refund;
		$executionStart = hrtime(true);
		$apiTime      = 0.0;

		try {
			$response = $this->submitPaymentReportingRequest($order, $orderId);
			$invoice  = $this->extractPaymentReportingInvoice($response);

			if (! $invoice) {
				$this->markPaymentReportingError($orderId);
				return;
			}

			$apiTime = $response->get_response_time();
			$this->markPaymentReported($orderId);
			$this->addPaymentReportingSuccessNote($order, $isRefund);
			$this->logPaymentReportingSuccess($order, $invoice, $executionStart, $apiTime, $isRefund);
		} catch (\Exception $e) {
			$this->handlePaymentReportingException($order, $orderId, $isRefund, $e);
		}
	}

	/**
	 * Build and submit the payment-reporting request.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param int $orderId Order or refund id.
	 * @return mixed
	 */
	protected function submitPaymentReportingRequest($order, $orderId)
	{
		$entityType = $this->determineEntityType($order);
		$invoiceData = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($orderId, $entityType);

		return wc_avatax()->get_elr_api()->submit_invoice(
			$order,
			$invoiceData,
			WC_AvaTax_Elr_API::OPERATION_PAYMENT_REPORTING
		);
	}

	/**
	 * Extract the invoice object from the ELR response.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $response Submit invoice response.
	 * @return mixed
	 */
	protected function extractPaymentReportingInvoice($response)
	{
		if (! $response || $response->has_error_code()) {
			return false;
		}

		return $response->process_response();
	}

	/**
	 * Persist the successful payment-reporting status.
	 *
	 * @since 3.8.4
	 *
	 * @param int $orderId Order or refund id.
	 * @return void
	 */
	protected function markPaymentReported($orderId)
	{
		wc_avatax()->wc_avatax_utilities()->update_order_meta(
			$orderId,
			self::WC_AVATAX_ELR_PAYMENT_STATUS,
			"payment_reported"
		);
	}

	/**
	 * Persist the failed payment-reporting status.
	 *
	 * @since 3.8.4
	 *
	 * @param int $orderId Order or refund id.
	 * @return void
	 */
	protected function markPaymentReportingError($orderId)
	{
		wc_avatax()->wc_avatax_utilities()->update_order_meta(
			$orderId,
			self::WC_AVATAX_ELR_PAYMENT_STATUS,
			"error"
		);
	}

	/**
	 * Add the success note after payment reporting.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param bool $isRefund Whether the entity is a refund.
	 * @return void
	 */
	protected function addPaymentReportingSuccessNote($order, $isRefund)
	{
		$this->getPaymentReportingNoteTarget($order, $isRefund)->add_order_note(
			$this->getPaymentReportingSuccessMessage($order, $isRefund)
		);
	}

	/**
	 * Build the success note message for payment reporting.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param bool $isRefund Whether the entity is a refund.
	 * @return string
	 */
	protected function getPaymentReportingSuccessMessage($order, $isRefund)
	{
		return $isRefund
			? sprintf(
				/* translators: %d refund number */
				__('Refund #%d payment reported to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax'),
				$order->get_id()
			)
			: __('Payment reported to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax');
	}

	/**
	 * Record the performance log for successful payment reporting.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param object $invoice Submitted invoice response entity.
	 * @param float|int $executionStart High-resolution start time.
	 * @param float $apiTime API response time.
	 * @param bool $isRefund Whether the entity is a refund.
	 * @return void
	 */
	protected function logPaymentReportingSuccess($order, $invoice, $executionStart, $apiTime, $isRefund)
	{
		$executionEnd = hrtime(true);
		$executionTime = wc_avatax()->wc_avatax_utilities()->microtime_diff($executionStart, $executionEnd);
		$connectorTime = $executionTime - $apiTime;
		$docCode = $isRefund ? (string) $order->get_id() : $order->get_order_key('edit');

		wc_avatax()->elr_logger()->log_performance_elr(
			"ReportPayment",
			"reportPaymentToELR",
			$isRefund
				? "Reporting refund payment to Avalara E-invoicing and Live Reporting."
				: "Reporting payment to Avalara E-invoicing and Live Reporting.",
			$docCode,
			$isRefund ? "CreditNote" : "SalesInvoice",
			$connectorTime,
			$apiTime,
			count($order->get_items()),
			$invoice->id,
			$invoice->mandate
		);
	}

	/**
	 * Handle payment-reporting exceptions.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param int $orderId Order or refund id.
	 * @param bool $isRefund Whether the entity is a refund.
	 * @param \Exception $e Exception raised during reporting.
	 * @return void
	 */
	protected function handlePaymentReportingException($order, $orderId, $isRefund, \Exception $e)
	{
		$this->markPaymentReportingError($orderId);
		$this->getPaymentReportingNoteTarget($order, $isRefund)->add_order_note(
			$this->getPaymentReportingExceptionMessage($order, $isRefund, $e->getMessage())
		);

		if (wc_avatax()->elr_logging_enabled()) {
			wc_avatax()->log_elr($e->getMessage() . ' - Order ID: ' . $orderId);
		}
	}

	/**
	 * Build the exception note message for payment reporting.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param bool $isRefund Whether the entity is a refund.
	 * @param string $message Exception message.
	 * @return string
	 */
	protected function getPaymentReportingExceptionMessage($order, $isRefund, $message)
	{
		return $isRefund
			? sprintf(
				/* translators: 1: refund number 2: error message */
				__('Refund #%1$d payment not reported to Avalara E-invoicing and Live Reporting. Error: %2$s', 'woocommerce-avatax'),
				$order->get_id(),
				$message
			)
			: __('Payment not reported to Avalara E-invoicing and Live Reporting. Error: ' . $message, 'woocommerce-avatax');
	}

	/**
	 * Resolve the order object that should receive payment-reporting notes.
	 *
	 * Refund notes are surfaced on the parent order because WooCommerce shows
	 * the order notes there for merchants during refund review.
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @param bool $isRefund Whether the entity is a refund.
	 * @return WC_Order|WC_Order_Refund
	 */
	protected function getPaymentReportingNoteTarget($order, $isRefund)
	{
		if ($isRefund && $order instanceof WC_Order_Refund && $order->get_parent_id()) {
			$parentOrder = wc_get_order($order->get_parent_id());

			if ($parentOrder instanceof WC_Order) {
				return $parentOrder;
			}
		}

		return $order;
	}

	/**
	 * Determine whether the entity can be evaluated for payment reporting.
	 *
	 * @since 3.10.0
	 *
	 * @param mixed $order Order or refund object.
	 * @return bool
	 */
	protected function isPaymentReportingEntityOrder($order)
	{
		return $order instanceof WC_Order || $order instanceof WC_Order_Refund;
	}

	/**
	 * Resolve the destination country used by payment-reporting rules.
	 *
	 * @since 3.10.0
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @return string
	 */
	protected function getPaymentReportingDestinationCountry($order)
	{
		if ($order instanceof WC_Order_Refund) {
			return $this->getRefundDestinationCountry($order);
		}

		return $this->getDestinationCountryFromOrder($order);
	}

	/**
	 * Resolve the destination country for a refund.
	 *
	 * @since 3.10.0
	 *
	 * @param WC_Order_Refund $refund Refund object.
	 * @return string
	 */
	protected function getRefundDestinationCountry(WC_Order_Refund $refund)
	{
		if (! $refund->get_parent_id()) {
			return $this->getDestinationCountryFromOrder($refund);
		}

		$parentOrder = wc_get_order($refund->get_parent_id());

		if ($parentOrder instanceof WC_Order) {
			return $this->getDestinationCountryFromOrder($parentOrder);
		}

		return $this->getDestinationCountryFromOrder($refund);
	}

	/**
	 * Resolve the shipping/billing destination country from an order-like entity.
	 *
	 * @since 3.10.0
	 *
	 * @param WC_Order|WC_Order_Refund $order Order-like object.
	 * @return string
	 */
	protected function getDestinationCountryFromOrder($order)
	{
		return strtoupper((string) ($order->get_shipping_country('edit') ?: $order->get_billing_country('edit')));
	}

	/**
	 * Determine whether the buyer is flagged as a business.
	 *
	 * @since 3.10.0
	 *
	 * @param WC_Order|WC_Order_Refund $order Order or refund object.
	 * @return bool
	 */
	protected function isBusinessBuyer($order)
	{
		if ($order instanceof WC_Order_Refund && $order->get_parent_id()) {
			$parentOrder = wc_get_order($order->get_parent_id());

			if ($parentOrder instanceof WC_Order) {
				$order = $parentOrder;
			}
		}

		return 'yes' === get_user_meta($order->get_user_id(), 'wc_avatax_Buyer_Is_Business', true);
	}

	/**
	 * Resolve the payment-reporting entity key from business + destination inputs.
	 *
	 * @since 3.10.0
	 *
	 * @param bool $isBusiness Whether the buyer is a business.
	 * @param string $destinationCountry Destination country code.
	 * @return string
	 */
	protected function resolvePaymentReportingEntityType($isBusiness, $destinationCountry)
	{
		if ($isBusiness && 'FR' !== $destinationCountry) {
			return 'b2bpayment-ereporting';
		}

		if (! $isBusiness) {
			return 'b2cpayment-ereporting';
		}

		return 'order';
	}

	/**
	 * Resolves the source country code used by ELR payment-reporting rules.
	 *
	 * When AvaTax tax calculation is enabled the plugin's configured origin
	 * address (`wc_avatax_origin_address` option, populated from the AvaTax
	 * company profile via {@see WC_AvaTax_Utilities::update_origin_address()})
	 * is authoritative — that's the address Avalara uses for sourcing rules
	 * and it is what we must report as the seller country. When AvaTax is
	 * disabled there is no synced origin, so we fall back to the WooCommerce
	 * store base country (`Settings → General → Store address`).
	 *
	 * @since 3.10.0
	 *
	 * @return string Uppercase ISO-3166 alpha-2 country code, or empty string when none is configured.
	 */
	protected function getSourceCountry()
	{
		if (wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
			$originAddress = get_option('wc_avatax_origin_address', []);

			if (is_array($originAddress) && ! empty($originAddress['country'])) {
				return (string) $originAddress['country'];
			}

			return '';
		}

		return (string) WC()->countries->get_base_country();
	}

	/**
	 * Gets invoice status.
	 *
	 * @since 3.0.0
	 * @param WC_Order $order The order object.
	 * @return \WC_AvaTax_Elr_API_Invoice_Status_Response invoice status.
	 */
	protected function get_invoice_status($order){
		
		if($this->can_fetch_invoice_status($order)){
			$order_id = $order->get_id();
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, '_wc_avatax_invoice_id');
		
			return wc_avatax()->get_elr_api()->get_invoice_status($invoice_id);
		}
		else
			return false;
	}

	/**
	 * Fetches the current invoice status from Avalara and stores its
	 * businessStatus on the order meta (`_wc_avatax_business_status`).
	 *
	 * Uses the invoice id stored in `_wc_avatax_invoice_id` for the order/refund.
	 *
	 * @since 3.8.4
	 *
	 * @param int $order_id order or refund ID
	 */
	protected function update_business_status_meta($order_id){
		$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, '_wc_avatax_invoice_id');

		if (empty($invoice_id)) {
			return;
		}

		$response = wc_avatax()->get_elr_api()->get_invoice_status($invoice_id);

		if ($response) {
			wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, '_wc_avatax_business_status', $response->get_business_status());
		}
	}

	/**
	 * Gets invoice status messages.
	 *
	 * @since 3.0.0
	 * @param WC_Order $order The order object.
	 * @return \string invoice status messages.
	 */
	public function get_invoice_status_messages($messages) {
		$message_string = '<ul class="order_notes">';

		if (!empty($messages)) {
			foreach ((array) $messages as $message) {
				$date = new DateTime($this->get_message_date($message->eventDateTime));
				$formatted_dt = date_format($date, wc_date_format()) . ' at ' . date_format($date, wc_time_format());

				$message_string .= '<li class="note">
										<div class="note_content">
											<p>' . esc_html($message->message) . '</p>';

				// Add responseKey/Value inside the same note_content box
				if (!empty($message->responseKey) || !empty($message->responseValue)) {
					$message_string .= '<div class="elr_response_info" style="margin-top: 8px; background: #f1f1f1; border: 1px solid #ddd; padding: 8px; border-radius: 4px;">';

					if (!empty($message->responseKey)) {
						$message_string .= '<p><strong>' . esc_html($message->responseKey) . ':</strong></p>';
					}

					if (!empty($message->responseValue)) {
						$message_string .= '<p style="word-break: break-word;">' . esc_html($message->responseValue) . '</p>';
					}

					$message_string .= '</div>';
				}

				$message_string .= '	</div>
										<p class="meta">
											<abbr class="exact-date" title="' . esc_attr($message->eventDateTime) . '">' . esc_html($formatted_dt) . '</abbr>
										</p>
									</li>';
			}
		}

		$message_string .= '</ul>';

		return $message_string;
	}

	/**
	 * Determine if an order invoice has already been sent to AvaTax.
	 *
	 * @since 3.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order invoice has already been sent to AvaTax.
	 */
	public function is_invoice_posted( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		return (!empty(wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), "_wc_avatax_invoice_id", true)));
	}

	/**
	 * Determines if an invoice can be sent for the given order.
	 * 
	 * @since 3.0.0
	 * 
	 * @param mixed $order Order ID (integer) or WC_Order object
	 * @return boolean Returns true if the invoice hasn't been posted yet or if there was a previous error,
	 *                 false otherwise
	 */
	public function can_send_invoice($order) {
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		return !$this->is_invoice_posted($order) || $this->has_invoice_error($order);
	}

	/**
	 * Determines if a payment can be sent for the given order.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $order Order ID (integer) or WC_Order/WC_Order_Refund object
	 * @return boolean Returns true if the order/refund is eligible for payment reporting
	 *                 and the payment hasn't been reported yet (or a previous report errored);
	 *                 false otherwise.
	 */
	public function canSendPayment($order)
	{
		if (is_numeric($order)) {
			$order = wc_get_order($order);
		}

		if ($order instanceof WC_Order_Refund) {
			return $this->canSendRefundPayment($order);
		}

		if ( ! $order instanceof WC_Order || ! $order->is_paid() ) {
			return false;
		}

		return !$this->isPaymentReported($order) || $this->hasPaymentError($order);
	}

	/**
	 * Determines if a refund payment can be sent for the given refund.
	 *
	 * Refund payment reporting is only allowed after the refund credit note
	 * has been posted to ELR, and only when the payment has not yet been
	 * reported (or a previous report errored).
	 *
	 * @since 3.8.4
	 *
	 * @param WC_Order_Refund $refund Refund object.
	 * @return bool
	 */
	protected function canSendRefundPayment(WC_Order_Refund $refund)
	{
		return ! $this->isPaymentReported($refund) || $this->hasPaymentError($refund);
	}

	/**
	 * Determines if a payment has been reported for the given order.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $order Order ID (integer) or WC_Order object
	 * @return boolean Returns true if the payment has been reported, false otherwise
	 */
	public function isPaymentReported($order)
	{
		return
			wc_avatax()->wc_avatax_utilities()->get_order_meta(
				$order->get_id(),
				self::WC_AVATAX_ELR_PAYMENT_STATUS, true)
				=== "payment_reported";
	}

	/**
	 * Determines if a payment has an error status for the given order.
	 *
	 * @since 3.8.4
	 *
	 * @param mixed $order Order ID (integer) or WC_Order object
	 * @return boolean Returns true if the payment has an error status, false otherwise
	 */
	public function hasPaymentError($order)
	{
		return
		wc_avatax()->wc_avatax_utilities()->get_order_meta(
			$order->get_id(),
			self::WC_AVATAX_ELR_PAYMENT_STATUS,
			true
		) === "error";
	}

	/**
	 * Determines if the invoice status can be fetched for the given order.
	 * 
	 * @since 3.0.0
	 * 
	 * @param mixed $order Order ID (integer) or WC_Order object
	 * @return boolean Returns true if the invoice has been posted AND (has an error OR is not completed),
	 *                 false otherwise. This indicates whether the status needs to be checked/updated.
	 */
	public function can_fetch_invoice_status($order){
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ($this->is_invoice_posted($order) || ($this->has_invoice_error($order)));
	}

	/**
	 * Checks if the order has an invoice error status.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order|int $order Order object or order ID.
	 * @return bool Returns true if the order has an invoice error status, false otherwise.
	 */
	public function has_invoice_error( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( wc_avatax()->wc_avatax_elr_utilities()->order_has_elr_status( $order->get_id(), 'error' ) );
	}

	/**
	 * Determine if an order invoice submission has completed or not.
	 *
	 * @since 3.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order invoice has already been sent to AvaTax.
	 */
	public function is_invoice_completed( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( wc_avatax()->wc_avatax_elr_utilities()->order_has_elr_status( $order->get_id(), 'complete' ) );
	}

	/**
	 * Formats a date string into a standardized datetime format.
	 * 
	 * @param string $date_string The input date string to be formatted
	 * @return string Formatted date in 'Y-m-d H:i:s' format
	 */
	protected function get_message_date($date_string){
		$lastIndex = strripos($date_string, ':');

		return date('Y-m-d H:i:s', strtotime(substr($date_string, 0, $lastIndex )));
	}

	/**
	 * Add a "Send to Avalara E-invoicing and Live Reporting" action to the order action options.
	 *
	 * @since 3.0.0
	 * @global WC_Order $theorder The current order object.
	 * @param array $actions The available order actions.
	 * @return array $actions
	 */
	public function add_order_action( $actions ) {
		global $theorder;
		
		if (wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled() && $this->can_send_invoice($theorder)){
			$actions['wc_avatax_elr_send'] = __( 'Send to Avalara E-invoicing and Live Reporting', 'woocommerce-avatax' );
		}

		if (wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled() && $this->canReportPaymentToELR($theorder)) {
			$actions['wc_avatax_elr_report_payment'] = __(
															'Report payment to Avalara E-invoicing and Live Reporting',
															'woocommerce-avatax'
														);
		}

		if (
			wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled()
			&& $theorder instanceof WC_Order
			&& ! $theorder->get_parent_id()
			&& $this->can_send_ar_outbound($theorder)
		) {
			$actions['wc_avatax_elr_send_ar_outbound'] = __(
															'Send AR-Outbound (Application Response) to Avalara E-invoicing and Live Reporting',
															'woocommerce-avatax'
														);
		}

		return $actions;
	}

	/**
	 * Validates if a string is base64 encoded.
	 *
	 * Performs multiple checks to determine if the input string is a valid base64 encoded string:
	 * 1. Checks if the string is empty
	 * 2. Verifies if the string length is valid (divisible by 4)
	 * 3. Validates if the string contains only valid base64 characters
	 * 4. Attempts to decode the string in strict mode
	 * 5. Verifies if the encode-decode round trip matches the original string
	 *
	 * @since 0.0.0
	 * @param string $string The string to check for base64 encoding
	 * @return boolean Returns true if the string is valid base64, false otherwise
	 */
	public function isBase64($string)
	{
		// Check if empty
		if (empty($string)) {
			return false;
		}
	
		// Check if string length is valid for base64
		if (strlen($string) % 4 !== 0) {
			return false;
		}
	
		// Check for valid base64 characters
		if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) {
			return false;
		}
	
		// Decode and check if successful
		$decoded = base64_decode($string, true);
		if ($decoded === false) {
			return false;
		}
	
		// Encode and compare
		if (base64_encode($decoded) !== $string) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * Schedule status refresh event based on frequency setting
	 *
	 * @since 3.8.4
	 */
	public function schedule_status_refresh_event()
	{
		$frequency = get_option('wc_avatax_elr_status_refresh_frequency', 'never');
		
		// Clear existing scheduled event
		wp_clear_scheduled_hook('wc_avatax_elr_status_refresh');
		
		if ($frequency !== 'never') {
			$intervals = array(
				'15min' => 15 * MINUTE_IN_SECONDS,
				'30min' => 30 * MINUTE_IN_SECONDS,
				'1hr'   => HOUR_IN_SECONDS,
				'6hr'   => 6 * HOUR_IN_SECONDS,
				'12hr'  => 12 * HOUR_IN_SECONDS,
				'1day'  => DAY_IN_SECONDS,
			);
			
			if (isset($intervals[$frequency])) {
				wp_schedule_event(time(), 'wc_avatax_elr_' . $frequency, 'wc_avatax_elr_status_refresh');
			}
		}
	}

	/**
	 * Add custom cron schedules for ELR status refresh
	 *
	 * @since 0.0.0
	 */
	public function add_cron_schedules($schedules)
	{
		$schedules['wc_avatax_elr_15min'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS,
			'display'  => __('Every 15 minutes', 'woocommerce-avatax')
		);
		$schedules['wc_avatax_elr_30min'] = array(
			'interval' => 30 * MINUTE_IN_SECONDS,
			'display'  => __('Every 30 minutes', 'woocommerce-avatax')
		);
		$schedules['wc_avatax_elr_1hr'] = array(
			'interval' => HOUR_IN_SECONDS,
			'display'  => __('Every hour', 'woocommerce-avatax')
		);
		$schedules['wc_avatax_elr_6hr'] = array(
			'interval' => 6 * HOUR_IN_SECONDS,
			'display'  => __('Every 6 hours', 'woocommerce-avatax')
		);
		$schedules['wc_avatax_elr_12hr'] = array(
			'interval' => 12 * HOUR_IN_SECONDS,
			'display'  => __('Every 12 hours', 'woocommerce-avatax')
		);
		$schedules['wc_avatax_elr_1day'] = array(
			'interval' => DAY_IN_SECONDS,
			'display'  => __('Every day', 'woocommerce-avatax')
		);
		return $schedules;
	}

	/**
	 * Handle the scheduled status refresh event
	 *
	 * @since 3.8.4
	 */
	public function handle_status_refresh()
	{
		if (wc_avatax()->elr_logging_enabled()) {
			wc_avatax()->log_elr('ELR status refresh event triggered');
		}
		
		$queue = $this->get_status_queue();
		
		foreach ($queue as $item) {
			$id = $item['id'];
			$type = $item['type'];
			$retry = $item['retry'];
			
			// Get the order/refund object
			$entity = wc_get_order($id);
			
			if (!$entity) {
				// Remove invalid entries from queue
				$this->remove_from_status_queue($id, $type);
				continue;
			}
			
			// Check if status is already complete
			if ($this->is_invoice_completed($entity)) {
				$this->remove_from_status_queue($id, $type);
				continue;
			}
			
			// Schedule individual status check action
			try {
				$action_queue = new WC_Action_Queue();
				$action_queue->schedule_single(
					time() + 5, // Schedule 5 seconds from now to spread the load
					'wc_avatax_elr_check_invoice_status',
					array('order' =>
						array(
							'id' => $id,
							'type' => $type,
							'retry' => $retry
						)
					),
					'wc_avatax_elr_status_check'
				);
				
				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr("Scheduled status check for {$type} ID: {$id}");
				}
				
			} catch (Exception $e) {
				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr("Failed to schedule status check for {$type} ID: {$id} - " . $e->getMessage());
				}
			}
		}
	}

	/**
	 * Handle individual invoice status check
	 *
	 * @since 0.0.0
	 * @param array $args Contains 'id' and 'type'
	 */
	public function handle_invoice_status_check($args)
	{
		$id = $args['id'];
		$type = $args['type'];
		$retry = $args['retry'];
		
		// Get the order/refund object
		$entity = wc_get_order($id);
		
		if (!$entity) {
			// Remove invalid entries from queue
			$this->remove_from_status_queue($id, $type);
			return;
		}
		
		// Check if status is already complete
		if ($this->is_invoice_completed($entity)) {
			$this->remove_from_status_queue($id, $type);
			return;
		}
		
		// Get current status from API
		$status_details = $this->get_invoice_status_details($entity);
		
		if ($status_details['status'] === 'Complete') {
			// Remove from queue when complete
			$this->remove_from_status_queue($id, $type);
			
			if (wc_avatax()->elr_logging_enabled()) {
				wc_avatax()->log_elr("Status refresh completed for {$type} ID: {$id}");
			}
		} else {
			// Increment retry count
			$new_retry = $retry + 1;
			
			if ($new_retry >= self::RETRY_COUNT) {
				// Remove from queue if retry limit exceeded
				$this->remove_from_status_queue($id, $type);
				
				if (wc_avatax()->elr_logging_enabled()) {
					wc_avatax()->log_elr("Retry limit exceeded for {$type} ID: {$id}, removing from queue");
				}
			} else {
				// Update retry count in queue
				$this->update_retry_count($id, $type, $new_retry);
			}
		}
	}

	/**
	 * Add order/refund ID to pending status queue
	 *
	 * @since 0.0.0
	 * @param int $id Order or refund ID
	 * @param string $type 'order' or 'refund'
	 */
	private function add_to_status_queue($id, $type)
	{
		$queue = get_option('wc_avatax_elr_pending_invoice_status', array());
		
		$queue[] = array(
			'id' => $id,
			'type' => $type,
			'retry' => 0,
			'added_time' => time()
		);
		
		update_option('wc_avatax_elr_pending_invoice_status', $queue);
	}

	/**
	 * Remove item from pending status queue
	 *
	 * @since 0.0.0
	 * @param int $id Order or refund ID
	 * @param string $type 'order' or 'refund'
	 */
	private function remove_from_status_queue($id, $type)
	{
		$queue = get_option('wc_avatax_elr_pending_invoice_status', array());
		
		$queue = array_filter($queue, function ($item) use ($id, $type) {
			return !($item['id'] == $id && $item['type'] == $type);
		});
		
		update_option('wc_avatax_elr_pending_invoice_status', array_values($queue));
	}

	/**
	 * Get pending status queue
	 *
	 * @since 0.0.0
	 * @return array
	 */
	private function get_status_queue()
	{
		return get_option('wc_avatax_elr_pending_invoice_status', array());
	}

	/**
	 * Update retry count for item in pending status queue
	 *
	 * @since 0.0.0
	 * @param int $id Order or refund ID
	 * @param string $type 'order' or 'refund'
	 * @param int $retry_count New retry count
	 */
	private function update_retry_count($id, $type, $retry_count)
	{
		$queue = get_option('wc_avatax_elr_pending_invoice_status', array());
		
		foreach ($queue as &$item) {
			if ($item['id'] == $id && $item['type'] == $type) {
				$item['retry'] = $retry_count;
				break;
			}
		}
		
		update_option('wc_avatax_elr_pending_invoice_status', $queue);
	}
}
