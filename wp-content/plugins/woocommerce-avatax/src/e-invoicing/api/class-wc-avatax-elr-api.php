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

use SkyVerge\WooCommerce\AvaTax\API\Requests\Companies_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\Nexus_List_Request;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Companies_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Nexus_List_Response;

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_Abstract_API;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr_API extends WC_AvaTax_Abstract_API {

	/**
	 * Operation type for {@see submit_invoice()} calls made by the
	 * e-invoice path (`process_elr()` / `process_refund_to_elr()`).
	 *
	 * Selects the "Order/Refund not sent to Avalara…" wording for the
	 * failure-path order note centralised inside `submit_invoice()`.
	 */
	const OPERATION_INVOICE = 'invoice';

	/**
	 * Operation type for {@see submit_invoice()} calls made by the
	 * offline-payment reporting path (`reportPaymentToELR()`).
	 *
	 * Selects the "Payment not reported to Avalara…" wording so the
	 * merchant-facing note matches the action the admin actually took.
	 * The payment-reporting caller is responsible for the
	 * `_wc_avatax_elr_payment_status = "error"` side effect — that lives
	 * outside the API layer.
	 */
	const OPERATION_PAYMENT_REPORTING = 'payment_reporting';

	/** @var string Avalara E-invoicing and Live Reporting client ID */
	protected $client_id;

	/** @var string Avalara E-invoicing and Live Reporting client secret */
	protected $client_secret;

	/** @var string Avalara Environment */
	protected $environment;
	// protected $sbx_env_token_url = 'https://ai-awsfqa.avlr.sh/';
	// protected $prd_env_token_url = 'https://ai-awsfqa.avlr.sh/';
	protected $sbx_env_token_url = 'https://ai-sbx.avlr.sh/';
	protected $prd_env_token_url = 'https://identity.avalara.com/';
	protected $token_expiry_seconds = 3540;
	// protected $sbx_elr_api_url = 'https://router.studio.stg.us-west-2.avalara.io/studio-router/apps/1aaaf9a1-65b9-4b68-9660-dbb40148e25d';
	protected $sbx_elr_api_url = 'https://router.studio.sbx.us-west-2.avalara.com/studio-router/apps/1aaaf9a1-65b9-4b68-9660-dbb40148e25d';
	protected $prd_elr_api_url = 'https://router.studio.us-west-2.avalara.com/studio-router/apps/1aaaf9a1-65b9-4b68-9660-dbb40148e25d';

	/**
	 * Construct the API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $client_id ELR client ID
	 * @param string $client_secret ELR client secret
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function __construct( $client_id, $client_secret, $environment ) {

		$this->client_id   = $client_id;
		$this->client_secret  = $client_secret;
		$this->environment  = $environment;

		$this->request_uri = ( 'production' === $environment ) ? $this->prd_elr_api_url : $this->sbx_elr_api_url;

		$this->set_request_headers( [
			'avalara-version' => '1.0.0'
		] );
		
		parent::__construct(wc_avatax()::ELR_CONNECTOR_ID);
	}

	/**
	 * Allow child classes to validate a response prior to instantiating the
	 * response object. Useful for checking response codes or messages, e.g.
	 * throw an exception if the response code is not 200.
	 *
	 * A child class implementing this method should simply return true if the response
	 * processing should continue, or throw a Framework\SV_WC_API_Exception with a
	 * relevant error message & code to stop processing.
	 *
	 * Note: Child classes *must* sanitize the raw response body before throwing
	 * an exception, as it will be included in the broadcast_request() method
	 * which is typically used to log requests.
	 *
	 * @since 3.0.0
	 */
	protected function do_pre_parse_response_validation() {

		// TODO

		return true;
	}


	/**
	 * Validate the parsed response data.
	 *
	 * Primarily checks for errors returned by the AvaTax API.
	 *
	 * @since 3.0.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 * @return bool
	 */
	protected function do_post_parse_response_validation() {

		$response = $this->get_response();

		if ( $response->has_errors() ) {

			$messages = array();
			$errors   = $response->get_errors();

			foreach ( $errors->get_error_codes() as $code ) {
				$messages[] = '[' . $code . '] ' . $errors->get_error_message( $code );
			}

			$message = implode( ' ', $messages );

			throw new Framework\SV_WC_API_Exception( $message );
			//wc_avatax()->log($message);
			//return false;
		}

		return true;
	}


	/**
	 * Builds and returns a new API request object
	 *
	 * @see Framework\SV_WC_API_Base::get_new_request()
	 *
	 * @since 3.0.0
	 *
	 * @param string $type the desired request type
	 * @param mixed $args optional argument(s) to be passed to the request
	 * @return WC_AvaTax_Elr_API_Get_Companies_Request
	 * @throws Framework\SV_WC_API_Exception for invalid request types
	 */
	protected function get_new_request( $type = '', $args = null ) {
		$this->set_bearer_token_auth($this->get_elr_auth_token());
		switch ( $type ) {

			case 'submit_invoice' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Submit_Invoice_Response::class );
				return new WC_Avatax_Elr_API_Submit_Invoice_Request($args);
			case 'invoice_status' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Invoice_Status_Response::class );
				return new WC_Avatax_Elr_API_Invoice_Status_Request($args);
			case 'download_invoice' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Download_Invoice_Response::class );
				return new WC_Avatax_Elr_API_Download_Invoice_Request($args);
			case 'companies' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Get_Companies_Response::class );
				return new WC_AvaTax_Elr_API_Get_Companies_Request($args);
			case 'get_inbound_documents' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Get_Documents_Response::class );
				return new WC_AvaTax_Elr_API_Get_Documents_Request($args);
			case 'download_inbound_mapper' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Inbound_Mapper_Download_Response::class );
				return new WC_AvaTax_Elr_API_Inbound_Mapper_Download_Request( $args );
			case 'invoice_condition_payload' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Condition_Payload_Response::class );
				return new WC_AvaTax_Elr_API_Condition_Payload_Request($args);
			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid request type' );
		}
	}

	/**
	 * Pings the AvaTax API.
	 *
	 * Primarily used to test for a valid connection.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_AvaTax_API_Utility_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function test() {

		return $this->get_elr_auth_token();
	}

	/**
	 * Sets the authentication for E-Invoicing APIs.
	 *
	 * @since 3.0.0
	 *
	 * @param string $client_id ELR client ID
	 * @param string $client_secret ELR client secret
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function get_elr_auth_token() {
		try{
			$token = get_transient('wc_avatax_elr_token');

			if(! get_transient('wc_avatax_elr_token')){

				$api_url = ( 'production' === $this->environment  ? $this->prd_env_token_url : $this->sbx_env_token_url) . "connect/token";
				$options = array(
					'body' => array( 
										'useragent' => sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])),
										'grant_type' => 'client_credentials', 
										'client_id' => $this->client_id, 
										'client_secret' => $this->client_secret 
									),
				);

				if ( wp_http_supports( array( 'ssl' ) ) ) {
					$api_url = set_url_scheme( $api_url, 'https' );
				}

				$response       = wp_remote_post( $api_url, $options );

				$response_code  = wp_remote_retrieve_response_code( $response );
				$response_body  = json_decode( wp_remote_retrieve_body( $response ), true );
				$response_error = null;

				if ( (is_wp_error( $response ) && 200 !== wp_remote_retrieve_response_code( $response )) || isset($response_body['error'])) {
					$response_error = $response;
					if ( wc_avatax()->elr_logging_enabled()) {
						wc_avatax()->log_elr( sprintf( '%1$s: %2$s', $response_code ?? 'Error', is_array($response_error) ? json_encode($response_error) : $response_error->get_error_message() ) );
					}
					return false;
				} elseif (is_array($response_body) && isset($response_body["access_token"])
					&& isset($response_body["expires_in"])) {
					$token = $response_body["access_token"];
					$exp_time = ($response_body["expires_in"] - 300);

					set_transient('wc_avatax_elr_token', $token, $exp_time);
					return $token;
				} else {
					if (wc_avatax()->elr_logging_enabled()) {
						wc_avatax()->log_elr(sprintf('%1$s: Invalid response body received', $response_code ?? 'Error'));
					}
					return false;
				}
			}
			else {
				return $token;
			}
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}
			return false;
		}
	}
	
	/**
	 * Submits invoice to Avalara.
	 *
	 * Centralises the failure-path order note so callers don't each add
	 * their own. The `$operationType` argument selects the wording:
	 *   - {@see OPERATION_INVOICE} (default) — "Order/Refund not sent…",
	 *     used by `process_elr()` and `process_refund_to_elr()`.
	 *   - {@see OPERATION_PAYMENT_REPORTING} — "Payment not reported…",
	 *     used by `reportPaymentToELR()`.
	 *
	 * Callers MUST NOT add a duplicate failure note. Side effects that are
	 * specific to one call site (e.g. the payment-reporting status meta
	 * write) stay in the caller.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order         order object
	 * @param mixed     $data          request payload
	 * @param string    $operationType One of {@see OPERATION_INVOICE},
	 *                                 {@see OPERATION_PAYMENT_REPORTING}.
	 * @return WC_AvaTax_Elr_API_Submit_Invoice_Response|false response
	 *                                                          object or
	 *                                                          false on
	 *                                                          failure
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function submit_invoice($order, $data, $operationType = self::OPERATION_INVOICE) {

		$isRefund = $order instanceof WC_Order_Refund || (class_exists( 'WC_Refund') && $order instanceof WC_Refund);
		$request = $this->get_new_request('submit_invoice');
		$request->prepare_request($data);

		try{
			$api_start = hrtime(true);
			$response =  $this->perform_request($request);
			$api_end = hrtime(true);
			$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
			
			if($response->has_error_code()){
				$note_target = $isRefund ? wc_get_order($order->get_parent_id()) : $order;
				$note_target = $note_target ?: $order;
				$note_target->add_order_note(
					$this->build_submit_invoice_failure_note(
						$order,
						$operationType,
						$response->get_invoice_error_message()
					)
				);

				return false;
			}
			
			return $response;
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr(sprintf('%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage()));
			}

			$noteTarget = $isRefund ? wc_get_order($order->get_parent_id()) : $order;
			$noteTarget = $noteTarget ?: $order;
			$noteTarget->add_order_note(
				$this->build_submit_invoice_failure_note(
					$order,
					$operationType,
					$e->getMessage()
				)
			);

			wc_avatax()->elr_logger()->log_exception("SubmitInvoice", "submit_invoice", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
	}

	/**
	 * Builds the merchant-facing failure note for {@see submit_invoice()}.
	 *
	 * The wording is the only branch that varies between the e-invoice and
	 * payment-reporting callers, so consolidating it here keeps the failure
	 * handling in `submit_invoice()` regardless of which caller invoked it.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $order         order object (a `WC_Order_Refund` swaps the
	 *                              leading noun on the invoice path)
	 * @param string $operationType {@see OPERATION_INVOICE} or
	 *                              {@see OPERATION_PAYMENT_REPORTING}
	 * @param string $errorDetail   API error string or exception message
	 * @return string fully-formatted order note (HTML)
	 */
	protected function build_submit_invoice_failure_note($order, $operationType, $errorDetail) {
		$isRefund = $order instanceof WC_Order_Refund || (class_exists('WC_Refund') && $order instanceof WC_Refund);

		if ($operationType === self::OPERATION_PAYMENT_REPORTING) {
			$headline = $isRefund
				? __('Refund payment not reported to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax')
				: __('Payment not reported to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax');
		} else {
			$noun     = $isRefund ? 'Refund' : 'Order';
			$headline = __($noun . ' not sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax');
		}

		return '<strong>' . $headline . '</strong> </br> ' . $errorDetail;
	}

	/**
	 * Gets invoice Status.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return WC_AvaTax_Elr_API_Invoice_Status_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_invoice_status( $order_id) {
		try{
			$request = $this->get_new_request( 'invoice_status', $order_id );

			$api_start = hrtime(true);
			$response =  $this->perform_request( $request );
			$api_end = hrtime(true);
			$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
			
			if($response->has_error_code()){
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $response->get_invoice_error_message() );
				}
				return false;
			}
			return $response;
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr(sprintf('%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage()));
			}

			wc_avatax()->elr_logger()->log_exception("GetInvoiceStatus", "get_invoice_status", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
	}

	/**
	 * Download invoice.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function download_invoice( $invoice_id, $media_type) {
		try{

			
			$request = $this->get_new_request( 'download_invoice', $invoice_id);
			$this->set_request_headers( [
				'avalara-version' => '1.0.0',
				'accept' => $media_type
			] );
			$api_start = hrtime(true);
			$response =  $this->perform_request( $request );
			$api_end = hrtime(true);
			$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));

			if($response->has_error_code()){
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $response->get_invoice_error_message() );
				}
				return array(
					"success" => false
				);
			}

			return $response->get_download_details();
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr(sprintf('%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage()));
			}

			wc_avatax()->elr_logger()->log_exception("DownloadInvoice", "download_invoice", $e->getMessage(), $e->getTraceAsString());

			return array(
				"success" => false
			);
		}
	}


	/**
	 * Gets the einvoice companies.
	 *
	 * @since 2.9.0
	 *
	 * @return bool | WC_AvaTax_Elr_API_Get_Companies_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_elr_companies(string $paginated_url = '') : ?WC_AvaTax_Elr_API_Get_Companies_Response {

		$response = get_transient('wc_avatax_elr_company_response', '');
		if(!$response || !isset($response) || $response === ''){
			try {
				$request = $this->get_new_request( 'companies',  $paginated_url );
	
				$api_start = hrtime(true);
				$response = $this->perform_request( $request );
				$api_end = hrtime(true);
				$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
				
				if(!$response->has_error_code()){
					set_transient( 'wc_avatax_elr_company_response', $response, 1 * DAY_IN_SECONDS );
				}

				if(get_option('wc_avatax_elr_tenant_id', '') === ''){
					update_option("wc_avatax_elr_tenant_id", $response->get_tenant_id());
				}
	
				return $response;
			} 
			catch ( Exception $e ) {
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $e->getMessage() );
					
					// Display error message immediately
					echo '<div class="error inline" style="margin: 10px 0;">
                	<p><strong>' . esc_html__('Error:', 'woocommerce-avatax') . '</strong> ' 
                	. esc_html__('Unable to retrieve company information. Please try again later.', 'woocommerce-avatax') . '</p>';
					}

					wc_avatax()->elr_logger()->log_exception("GetCompanies", "get_elr_companies", $e->getMessage(), $e->getTraceAsString());

				return null;
			}
		} 
		return $response;
	}
	
	/**
	 * Gets a paginated list of inbound e-invoicing documents.
	 *
	 * Calls `GET /documents` on the Studio Router with `flow=in` and any
	 * additional filters (startDate, endDate, countryCode, status, etc.).
	 *
	 * @since 3.8.4
	 *
	 * @param array $args query parameters; see WC_AvaTax_Elr_API_Get_Documents_Request
	 * @return WC_AvaTax_Elr_API_Get_Documents_Response|null
	 */
	public function get_inbound_documents( array $args = array() ) {

		// always scope to inbound documents
		$args['flow'] = 'in';

		try {
			$request = $this->get_new_request( 'get_inbound_documents', $args );

			$api_start = hrtime( true );
			$response  = $this->perform_request( $request );
			$api_end   = hrtime( true );
			$response->set_response_time( wc_avatax()->wc_avatax_utilities()->microtime_diff( $api_start, $api_end ) );

			if ( $response->has_error_code() ) {
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $response->get_invoice_error_message() );
				}
				return null;
			}

			return $response;
		}
		catch ( Exception $e ) {
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}

			wc_avatax()->elr_logger()->log_exception( 'GetInboundDocuments', 'get_inbound_documents', $e->getMessage(), $e->getTraceAsString() );

			return null;
		}
	}

	/**
	 * Calls the inbound Studio mapper: POST /documents/{documentId}/$download.
	 *
	 * The `metadata` block is built from plugin settings (`appId`, `configId`)
	 * plus the document's own `companyId`, `documentType`, and `countryMandate`.
	 *
	 * @since 3.8.4
	 *
	 * @param object $document inbound document from GET /documents
	 * @return WC_AvaTax_Elr_API_Inbound_Mapper_Download_Response|null null when the
	 *         document has no id; throws on any API failure.
	 * @throws Framework\SV_WC_API_Exception when the request fails or returns an error
	 */
	public function download_inbound_mapper( $document ) {

		$document_id = (string) ( $document->id ?? '' );

		if ( '' === $document_id ) {
			return null;
		}

		$request = $this->get_new_request( 'download_inbound_mapper', array(
			'document_id' => $document_id,
			'body'        => array(
				'metadata' => array(
					'appId'          => wc_avatax()->get_elr_connector_id(),
					'configId'       => get_option( 'wc_avatax_website_id' ) . '_elr',
					'companyId'      => (string) ( $document->companyId ?? '' ),
					'documentType'   => (string) ( $document->documentType ?? '' ),
					'countryMandate' => (string) ( $document->countryMandate ?? '' ),
				),
			),
		) );

		$response = $this->perform_request( $request );

		if ( $response->has_error_code() ) {
			throw new Framework\SV_WC_API_Exception( sprintf( 'download_inbound_mapper failed for document %s: %s', $document_id, $response->get_invoice_error_message() ) );
		}

		return $response;
	}


	/**
	 * Syncs inbound documents and persists buyer-feedback fields on WooCommerce orders.
	 *
	 * For each inbound document: runs the inbound mapper, then stores the mapped
	 * `payload.wp_wc_orders` fields on the order identified by `original_invoice_id`.
	 *
	 * @since 3.8.4
	 *
	 * If any of the three API calls (get documents, run mapper, check status)
	 * fails at any point, the whole run is aborted with `success = false` and
	 * `processed` reflects only the documents fully handled before the failure.
	 * The caller (scheduler) must NOT advance its cursor unless `success` is true,
	 * so a failed run is retried over the same window with no data skipped.
	 *
	 * @param array $args query parameters for the documents endpoint (startDate, endDate, ...)
	 * @return array{total:int, processed:int, pages:int, success:bool, error:string}
	 */
	public function sync_inbound_documents_status( array $args = array() ) : array {

		$summary = array(
			'total'     => 0,
			'processed' => 0,
			'pages'     => 0,
			'success'   => true,
			'error'     => '',
		);

		$page_args = $args;

		try {
			while ( true ) {
				$summary['pages']++;

				$documents_response = $this->get_inbound_documents( $page_args );

				if ( ! $documents_response ) {
					throw new Framework\SV_WC_API_Exception( sprintf( 'get_inbound_documents returned null on page %d', $summary['pages'] ) );
				}

				$documents         = $documents_response->get_documents();
				$summary['total'] += count( $documents );

				foreach ( $documents as $document ) {
					$mapper_response = $this->download_inbound_mapper( $document );

					// null only when the document has no id; nothing to map, skip it.
					if ( ! $mapper_response ) {
						continue;
					}

					if ( $this->persist_inbound_mapper_to_order( $document, $mapper_response->get_wp_wc_orders() ) ) {
						$summary['processed']++;
					}
				}

				$next_page_args = $documents_response->get_next_page_args();

				if ( null === $next_page_args ) {
					break;
				}

				$page_args = array_merge( $args, $next_page_args );
			}
		}
		catch ( Exception $e ) {
			$summary['success'] = false;
			$summary['error']   = $e->getMessage();

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( sprintf(
					'sync_inbound_documents_status aborted on page %d (cursor preserved): %s',
					$summary['pages'],
					$e->getMessage()
				) );
			}

			wc_avatax()->elr_logger()->log_exception( 'SyncInboundDocuments', 'sync_inbound_documents_status', $e->getMessage(), $e->getTraceAsString() );
		}

		return $summary;
	}


	/**
	 * Writes the mapped `wp_wc_orders` fields onto the matching WooCommerce order.
	 *
	 * The order is resolved from `original_invoice_id`. Orders that do not exist
	 * are skipped.
	 *
	 * @since 3.8.4
	 *
	 * @param object              $document inbound document row
	 * @param array<string,mixed> $mapped   mapped `wp_wc_orders` fields
	 * @return bool true when the order was found and updated; false when no
	 *         matching order exists (a legitimate skip, not a failure)
	 * @throws Framework\SV_WC_API_Exception when the invoice status check fails
	 */
	private function persist_inbound_mapper_to_order( $document, array $mapped ) : bool {

		$order_id = (int) ( $mapped['original_invoice_id'] ?? 0 );

		if ( $order_id <= 0 || ! wc_get_order( $order_id ) ) {
			return false;
		}

		$utilities = wc_avatax()->wc_avatax_utilities();

		$meta_map = array(
			'requested_action_code' => '_wc_avatax_elr_requested_action_code',
			'requested_action'      => '_wc_avatax_elr_requested_action',
			'status_reason_code'    => '_wc_avatax_elr_status_reason_code',
			'status_reason'         => '_wc_avatax_elr_status_reason',
		);

		foreach ( $meta_map as $mapped_key => $meta_key ) {
			if ( '' !== (string) ( $mapped[ $mapped_key ] ?? '' ) ) {
				$utilities->update_order_meta( $order_id, $meta_key, $mapped[ $mapped_key ] );
			}
		}

		// Re-check the invoice status to capture the latest businessStatus.
		$invoice_id = $utilities->get_order_meta( $order_id, '_wc_avatax_invoice_id' );

		if ( ! empty( $invoice_id ) ) {
			$status_response = $this->get_invoice_status( $invoice_id );

			if ( ! $status_response ) {
				throw new Framework\SV_WC_API_Exception( sprintf( 'get_invoice_status failed for invoice %s (order %d)', $invoice_id, $order_id ) );
			}

			$utilities->update_order_meta( $order_id, '_wc_avatax_business_status', $status_response->get_business_status() );
		}

		return true;
	}
	/**
	 * Get condition payload
	 *
	 * @since 3.0.0
	 *
	 * @return WC_AvaTax_Elr_API_Condition_Payload_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_condition_payload() {
		// add condition for transient entry
		if (empty(get_transient( 'wc_avatax_elr_condition_payload'))) {
			$request = $this->get_new_request('invoice_condition_payload');

			$response =  $this->perform_request( $request );

			if($response->has_errors()){
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( "Unable to get conditional payload." );
				}
				return "";
			}
			if ($response->get_condition_payload_response()) {
				set_transient( 'wc_avatax_elr_condition_payload', $response->get_condition_payload_response(), DAY_IN_SECONDS);
			}
			return $response->get_condition_payload_response();
		}
	}
}
