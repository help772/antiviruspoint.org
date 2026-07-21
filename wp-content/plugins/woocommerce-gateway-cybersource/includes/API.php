<?php
/**
 * WooCommerce CyberSource
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce CyberSource to newer
 * versions in the future. If you wish to customize WooCommerce CyberSource for your
 * needs please refer to http://docs.woocommerce.com/document/cybersource-payment-gateway/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cybersource;

use CyberSource\ApiClient;
use CyberSource\ApiException;
use CyberSource\Authentication\Core\MerchantConfiguration;
use CyberSource\Authentication\Util\GlobalParameter;
use CyberSource\Configuration;
use CyberSource\Logging\LogConfiguration;
use Exception;
use SkyVerge\WooCommerce\Cybersource\API\Requests;
use SkyVerge\WooCommerce\Cybersource\API\Responses;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11\SV_WC_Payment_Gateway_Helper;
use WC_Log_Handler_File;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource API Class
 *
 * This is a pseudo-wrapper around the CyberSource PHP SDK
 *
 * @link https://github.com/CyberSource/cybersource-rest-client-php
 * @link https://github.com/CyberSource/cybersource-rest-samples-php
 *
 * @since 2.0.0
 *
 * @method Responses\Flex\Key_Generation|Responses\Payments\Authorization_Reversal|Responses\Payments\Refund|Responses\Payments\Capture|Responses\Payments\Credit_Card_Payment|Responses\Payments\Electronic_Check_Payment|Responses\Payment_Instruments perform_request( $request )
 */
class API extends Framework\SV_WC_API_Base implements Framework\SV_WC_Payment_Gateway_API {


	/** @var Gateway class instance */
	protected $gateway;

	/** @var \WC_Order order associated with the request, if any */
	protected $order;

	/** @var ApiClient instance of the SDK API client */
	protected $sdk_api_client;

	/** @var MerchantConfiguration|null instance of the SDK merchant configuration */
	protected ?MerchantConfiguration $merchant_configuration = null;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 2.0.0
	 *
	 * @param Gateway $gateway class instance
	 */
	public function __construct( $gateway ) {

		$this->gateway = $gateway;

		$this->set_request_content_type_header( 'application/json' );
		$this->set_request_accept_header( 'application/json' );
	}


	/** API Methods ***********************************************************/


	/**
	 * Creates a new credit card charge transaction.
	 *
	 * @see SV_WC_Payment_Gateway_API::credit_card_charge()
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order
	 * @return Responses\Payments\Credit_Card_Payment
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_charge( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_payment_request( Gateway::PAYMENT_TYPE_CREDIT_CARD );

		$request->create_credit_card_charge( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Creates a new credit card auth transaction.
	 *
	 * @see SV_WC_Payment_Gateway_API::credit_card_authorization()
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order
	 * @return Responses\Payments\Credit_Card_Payment
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_authorization( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_payment_request( Gateway::PAYMENT_TYPE_CREDIT_CARD );

		$request->create_credit_card_auth( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Captures funds for a credit card authorization.
	 *
	 * @see SV_WC_Payment_Gateway_API::credit_card_capture()
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order
	 * @return Responses\Payments\Credit_Card_Payment
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function credit_card_capture( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_capture_request();

		$request->create_credit_card_capture( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Performs a customer check debit transaction.
	 *
	 * An amount will be debited from the customer's account to the merchant's account.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return Responses\Payments\Electronic_Check_Payment
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function check_debit( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_payment_request( Gateway::PAYMENT_TYPE_ECHECK );

		$request->create_payment( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Performs a refund for the given order.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return Responses\Payments\Refund
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function refund( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_refund_request();

		$request->create_refund( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Performs a void for the given order.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return Responses\Payments\Authorization_Reversal
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function void( \WC_Order $order ) {

		$this->order = $order;

		$request = $this->get_new_void_request();

		$request->create_authorization_reversal( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Creates a transaction specific public key used to initiate the Flex Microform.
	 *
	 * @link https://developer.cybersource.com/docs/cybs/en-us/digital-accept-flex/developer/all/rest/digital-accept-flex/microform-integ-v2/microform-integ-getting-started-v2/creating-server-side-context-v2.html
	 *
	 * @since 2.0.0
	 *
	 * @param string $encryption_type type of encryption to use
	 * @return Responses\Flex\Key_Generation
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function generate_public_key( string $encryption_type = 'RsaOaep256' ) {

		$request = $this->get_new_key_generation_request();

		$enabled_card_types = array_map( 'strtoupper', array_map(
			[ SV_WC_Payment_Gateway_Helper::class, 'normalize_card_type' ],
			$this->get_gateway()->get_card_types()
		) );

		$request->set_generate_public_key_data( $encryption_type, $enabled_card_types );

		return $this->perform_request( $request );
	}


	/* Tokenization methods *******************************************************************************************/


	/**
	 * Tokenizes the order's payment method.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order the order
	 * @return Responses\Payments\Credit_Card_Payment|Responses\Payment_Instruments
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function tokenize_payment_method( \WC_Order $order ) {

		if ( 'credit_card' === $order->payment->type ) {

			$order->create_token = true;

			$response = $this->credit_card_authorization( $order );

		} else {

			$request = $this->get_new_payment_instrument_request();

			$request->set_create_payment_instrument( $order );

			$response = $this->perform_request( $request );
		}

		return $response;
	}


	/**
	 * Gets the tokenized payment methods - no-op
	 *
	 * @since 2.0.0
	 *
	 * @param string $customer_id unique
	 */
	public function get_tokenized_payment_methods( $customer_id ) { }


	/**
	 * Updates tokenized payment method - no-op
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order
	 *
	 * @return API\Responses\Payment_Instruments
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function update_tokenized_payment_method( \WC_Order $order ) {

		$request = $this->get_new_payment_instrument_request();

		$request->set_update_payment_instrument( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Determines whether updating tokenized methods is supported.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function supports_update_tokenized_payment_method() {

		return true;
	}


	/**
	 * Removes tokenized payment method - no-op
	 *
	 * @since 2.0.0
	 *
	 * @param string $token the payment method token
	 * @param string $customer_id unique
	 * @return API\Responses\Payment_Instruments
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function remove_tokenized_payment_method( $token, $customer_id ) {

		$request = $this->get_new_payment_instrument_request();

		$request->set_delete_payment_instrument( $token );

		return $this->perform_request( $request );
	}


	/**
	 * Determines whether retrieving tokenized methods is supported.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function supports_get_tokenized_payment_methods() {

		return false;
	}


	/**
	 * Determines whether removing tokenized methods is supported.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function supports_remove_tokenized_payment_method() {

		return false;
	}


	/** 3D Secure methods *********************************************************************************************/


	/**
	 * Sets up a 3D Secure session.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @return Responses\Payer_Authentication\Setup
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function threed_secure_setup( \WC_Order $order ) {

		$this->set_request_accept_header( 'application/hal+json' );

		/** @var Requests\Payer_Authentication\Setup $request */
		$request = $this->get_new_request( [
			'request_class'  => Requests\Payer_Authentication\Setup::class,
			'response_class' => Responses\Payer_Authentication\Setup::class,
		] );

		$request->set_order_data( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Checks 3D Secure enrollment.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order
	 * @return Responses\Payer_Authentication\Check_Enrollment
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function threed_secure_check_enrollment( \WC_Order $order ) {

		$this->set_request_accept_header( 'application/hal+json' );

		/** @var Requests\Payer_Authentication\Check_Enrollment $request */
		$request = $this->get_new_request( [
			'request_class'  => Requests\Payer_Authentication\Check_Enrollment::class,
			'response_class' => Responses\Payer_Authentication\Check_Enrollment::class,
		] );

		$request->set_order_data( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Validates 3D Secure results.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order
	 * @return Responses\Payer_Authentication\Validate
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function threed_secure_validate_results( \WC_Order $order ) {

		$this->set_request_accept_header( 'application/hal+json' );

		/** @var Requests\Payer_Authentication\Validate $request */
		$request = $this->get_new_request( [
			'request_class'  => Requests\Payer_Authentication\Validate::class,
			'response_class' => Responses\Payer_Authentication\Validate::class,
		] );

		$request->set_order_data( $order );

		return $this->perform_request( $request );
	}


	/**
	 * Gets the latest fraud case conversion details for the given org ID & time window.
	 *
	 * @since 2.3.0
	 *
	 * @param string $organization_id organization ID
	 * @param int $start_time unix timestamp for the earliest reports to return
	 * @param int|null $end_time unix timestamp for the latest reports to return (defaults to now)
	 * @return Responses\Reporting\Conversion_Details
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_conversion_details( $organization_id, $start_time, $end_time = null ) {

		$this->set_request_accept_header( 'application/hal+json' );

		$request = $this->get_new_request( [
			'request_class'  => Requests\Reporting\Conversion_Details::class,
			'response_class' => Responses\Reporting\Conversion_Details::class,
		] );

		$request->set_conversion_details( $organization_id, $start_time, $end_time );

		return $this->perform_request( $request );
	}


	/** Request/Response Methods **********************************************/


	/**
	 * Performs a remote request using the CyberSource SDK. Overrides the standard
	 * wp_remote_request() as the SDK already provides a cURL implementation
	 *
	 * @see SV_WC_API_Base::do_remote_request()
	 *
	 * @since 2.0.0
	 *
	 * @param string $callback SDK callback, e.g. `PaymentsApi->createPayment`
	 * @param array $callback_params parameters to pass to the callback
	 * @return \Exception|mixed
	 */
	protected function do_remote_request( $callback, $callback_params ) {

		try {
			return $this->get_sdk_api_client()->callApi(
				$this->get_request()->get_path(),
				$this->get_request()->get_method(),
				$this->get_request()->get_params(),
				$this->get_request()->get_data(),
				$this->get_request_headers()
			);

		} catch ( \Exception $e ) {

			$response = $e;
		}

		return $response;
	}


	/**
	 * Validates the response data before it's been parsed.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function do_pre_parse_response_validation() {

		// 404s will never have additional information
		if ( 404 === $this->get_response_code() ) {
			throw new Framework\SV_WC_API_Exception( $this->get_response_message(), $this->get_response_code() );
		}

		return true;
	}


	/**
	 * Validates the response after it's been parsed.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function do_post_parse_response_validation() {

		$response = $this->get_response();

		// Payments API server errors
		if ( $response instanceof Responses\Payments && Responses\Payments::STATUS_SERVER_ERROR === $response->get_status_code() ) {
			throw new Framework\SV_WC_API_Exception( $response->get_status_message() . ' [' . $response->get_reason_code() . ']' );
		}

		return true;
	}


	/**
	 * Handles and parses the response.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $response directly from CyberSource SDK
	 * @return API\Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function handle_response( $response ) {

		if ( $response instanceof ApiException ) {

			$code    = $response->getCode();
			$message = $response->getMessage();
			$body    = $response->getResponseBody();
			$headers = $response->getResponseHeaders();

			$body = json_encode( $body, true );

		} elseif ( is_array( $response ) ) {

			[ $data, $code, $headers ] = $response;

			$body = json_encode( $data, true );

			$message = '';

		} else {

			throw new Framework\SV_WC_API_Exception( 'Invalid response data' );
		}

		$this->response_code     = $code;
		$this->response_headers  = $headers;
		$this->response_message  = $message;
		$this->raw_response_body = $body;

		// allow child classes to validate response prior to parsing -- this is useful
		// for checking HTTP status codes, etc.
		$this->do_pre_parse_response_validation();

		$handler_class = $this->get_response_handler();

		// parse the response body and tie it to the request
		$this->response = new $handler_class( $this->raw_response_body );

		if ( ! empty( $order = $this->get_order() ) && $order instanceof \WC_Order ) {

			$this->response->set_order( $order );
		}

		$this->do_post_parse_response_validation();

		$this->broadcast_request();

		return $this->response;
	}


	/**
	 * Gets a new payment API request.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type payment type
	 * @return Requests\Payments\Credit_Card_Payment|Requests\Payments\Electronic_Check_Payment
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_payment_request( $type ) {

		$this->set_request_accept_header( 'application/hal+json' );

		switch ( $type ) {

			case Gateway::PAYMENT_TYPE_CREDIT_CARD:
				$request  = Requests\Payments\Credit_Card_Payment::class;
				$response = Responses\Payments\Credit_Card_Payment::class;
			break;

			case Gateway::PAYMENT_TYPE_ECHECK:
				$request  = Requests\Payments\Electronic_Check_Payment::class;
				$response = Responses\Payments\Electronic_Check_Payment::class;
			break;

			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid payment type' );
		}

		return $this->get_new_request( [
			'request_class'  => $request,
			'response_class' => $response,
		] );
	}


	/**
	 * Gets a new capture request.
	 *
	 * @since 2.0.0
	 *
	 * @return Requests\Payments\Capture
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_capture_request() {

		$this->set_request_accept_header( 'application/hal+json' );

		return $this->get_new_request( [
			'request_class'  => Requests\Payments\Capture::class,
			'response_class' => Responses\Payments\Capture::class,
		] );
	}


	/**
	 * Gets a new refund request.
	 *
	 * @since 2.0.0
	 *
	 * @return Requests\Payments\Refund
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_refund_request() {

		$this->set_request_accept_header( 'application/hal+json' );

		return $this->get_new_request( [
			'request_class'  => Requests\Payments\Refund::class,
			'response_class' => Responses\Payments\Refund::class,
		] );
	}


	/**
	 * Gets a new void request.
	 *
	 * @since 2.0.0
	 *
	 * @return Requests\Payments\Authorization_Reversal
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_void_request() {

		$this->set_request_accept_header( 'application/hal+json' );

		return $this->get_new_request( [
			'request_class'  => Requests\Payments\Authorization_Reversal::class,
			'response_class' => Responses\Payments\Authorization_Reversal::class,
		] );
	}


	/**
	 * Gets a new key generation request.
	 *
	 * @since 2.0.0
	 *
	 * @return Requests\Payment_Instruments
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_payment_instrument_request() {

		return $this->get_new_request( [
			'request_class'  => Requests\Payment_Instruments::class,
			'response_class' => Responses\Payment_Instruments::class,
		] );
	}


	/**
	 * Gets a new key generation request.
	 *
	 * @since 2.0.0
	 *
	 * @return Requests\Flex\Key_Generation
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_key_generation_request() {

		$this->set_request_accept_header( 'application/jwt' );
		$this->set_request_content_type_header( 'application/json;charset=utf-8' );

		return $this->get_new_request( [
			'request_class'  => Requests\Flex\Key_Generation::class,
			'response_class' => Responses\Flex\Key_Generation::class,
		] );
	}


	/**
	 * Gets a new request object.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args request args
	 *
	 * @return Requests\Flex\Key_Generation|Requests\Payments\Authorization_Reversal|Requests\Payments\Refund|Requests\Payments\Capture|Requests\Payments\Credit_Card_Payment|Requests\Payments\Electronic_Check_Payment|Requests\Payment_Instruments
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function get_new_request( $args = [] ) {

		$args = wp_parse_args( $args, [
			'request_class'  => '',
			'response_class' => '',
		] );

		if ( ! class_exists( $args['request_class'] ) ) {
			throw new Framework\SV_WC_API_Exception( 'Invalid request class' );
		}

		if ( ! class_exists( $args['response_class'] ) ) {
			throw new Framework\SV_WC_API_Exception( 'Invalid response class' );
		}

		$this->set_response_handler( $args['response_class'] );

		return new $args['request_class']( $this->order );
	}


	/** Helper methods ********************************************************/


	/**
	 * Returns a fresh SDK API client instance.
	 *
	 * Ensures that we re-use the same merchant configuration, but create a new API client and API configuration,
	 * because Cybersource SDK API client is not designed to be re-used across requests - it can mix up request data
	 * from previous requests, resulting in incorrect request data being sent to the API or auth failures.
	 *
	 * @since 2.0.0
	 *
	 * @return ApiClient
	 * @throws Exception
	 */
	public function get_sdk_api_client(): ApiClient {

		$merchant_configuration = $this->get_merchant_configuration();

		return new ApiClient(
			( new Configuration() )->setHost( $merchant_configuration->getHost() ),
			$merchant_configuration
		);
	}


	/**
	 * Gets the SDK merchant configuration object.
	 *
	 * @since 2.3.0
	 *
	 * @return MerchantConfiguration
	 * @throws Exception
	 */
	private function get_merchant_configuration(): MerchantConfiguration {

		if ( null === $this->merchant_configuration ) {

			$merchant_configuration = new MerchantConfiguration();

			if ( $custom_host = $this->get_custom_host() ) {

				$merchant_configuration->setHost( $custom_host );

			} else {

				$run_environment = $this->gateway->is_test_environment()
					? 'apitest.cybersource.com'
					: 'api.cybersource.com';

				$merchant_configuration->setRunEnvironment( $run_environment );
			}

			/**
			 * Filters the API Merchant ID used when initializing the SDK API client.
			 *
			 * @since 2.0.2
			 *
			 * @param string $merchant_id the merchant ID
			 * @param \WC_Order $order order instance
			 */
			$merchant_configuration->setMerchantID( apply_filters( 'wc_cybersource_api_credentials_merchant_id', $this->get_gateway()->get_merchant_id(), $this->get_order() ) );

			/**
			 * Filters the API Key used when initializing the SDK API client.
			 *
			 * @since 2.0.2
			 *
			 * @param string $api_key the API key
			 * @param \WC_Order $order order instance
			 */
			$merchant_configuration->setApiKeyID( apply_filters( 'wc_cybersource_api_credentials_api_key', $this->get_gateway()->get_api_key(), $this->get_order() ) );

			/**
			 * Filters the API Shared Secret used to Initialize the SDK API client.
			 *
			 * @since 2.0.2
			 *
			 * @param string $api_shared_secret the shared secret
			 * @param \WC_Order $order order instance
			 */
			$merchant_configuration->setSecretKey( apply_filters( 'wc_cybersource_api_credentials_api_shared_secret', $this->get_gateway()->get_api_shared_secret(), $this->get_order() ) );

			$merchant_configuration->setAuthenticationType( GlobalParameter::HTTP_SIGNATURE );
			$merchant_configuration->setLogConfiguration( $this->get_log_configuration() );

			$this->merchant_configuration = $merchant_configuration;
		}

		return $this->merchant_configuration;
	}


	/**
	 * Gets the SDK log configuration.
	 *
	 * @since 2.8.0
	 *
	 * @return LogConfiguration
	 */
	private function get_log_configuration(): LogConfiguration {

		$log_config = new LogConfiguration();
		$log_config->enableLogging( ! $this->get_gateway()->debug_off() );
		$log_config->setDebugLogFile( WC_Log_Handler_File::get_log_file_path( $this->get_gateway()->get_id() . '-SDK' ) );
		$log_config->setLogLevel( 'debug' );
		$log_config->enableMasking( true );

		return $log_config;
	}


	/**
	 * Gets the custom host, if any.
	 *
	 * @since 2.3.0
	 *
	 * @return string|null
	 */
	private function get_custom_host() {

		/**
		 * Filters the Cybersource custom host.
		 *
		 * The value should be a host domain without the protocol, e.g. 'apitest.cybersource.com'
		 *
		 * @since 2.3.0
		 *
		 * @param string|null $custom_host custom host value
		 * @param API $api API handler instance
		 */
		return apply_filters( 'wc_cybersource_api_custom_host', null, $this );
	}


	/**
	 * Returns the order associated with the request, if any.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Order
	 */
	public function get_order() {

		return $this->order;
	}


	/**
	 * Gets the ID for the API, used primarily to namespace the action name
	 * for broadcasting requests.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_api_id() {

		return $this->get_gateway()->get_id();
	}


	/**
	 * Returns the gateway plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return Framework\SV_WC_Payment_Gateway_Plugin
	 */
	public function get_plugin() {

		return $this->get_gateway()->get_plugin();
	}


	/**
	 * Returns the gateway class associated with the request.
	 *
	 * @since 2.0.0
	 *
	 * @return Gateway class instance
	 */
	public function get_gateway() {

		return $this->gateway;
	}


}
