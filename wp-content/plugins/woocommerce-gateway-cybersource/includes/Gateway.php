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

use Firebase\JWT\JWT;
use SkyVerge\WooCommerce\Cybersource\Blocks\Credit_Card_Checkout_Block_Integration;
use SkyVerge\WooCommerce\Cybersource\Gateway\Base_Payment_Form;
use SkyVerge\WooCommerce\Cybersource\Gateway\Integrations\SubscriptionIntegration;
use SkyVerge\WooCommerce\Cybersource\Gateway\Payment_Form;
use SkyVerge\WooCommerce\PluginFramework\v5_15_11 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CyberSource Base Gateway Class
 *
 * @since 2.0.0
 *
 * @method Plugin get_plugin()
 */
abstract class Gateway extends Framework\SV_WC_Payment_Gateway_Direct {


	/** @var string the flex form feature */
	const FEATURE_FLEX_FORM = 'flex_form';

	/** @var string the decision manager toggle setting name */
	const SETTING_ENABLE_DECISION_MANAGER = 'enable_decision_manager';


	/** @var string production merchant ID */
	protected $merchant_id;

	/** @var string production API key */
	protected $api_key;

	/** @var string production API shared secret */
	protected $api_shared_secret;

	/** @var string Token Management Service profile ID */
	protected $tokenization_profile_id;

	/** @var string test merchant ID */
	protected $test_merchant_id;

	/** @var string test API key */
	protected $test_api_key;

	/** @var string test API shared secret */
	protected $test_api_shared_secret;

	/** @var string test Token Management Service profile ID */
	protected $test_tokenization_profile_id;

	/** @var string whether the decision manager is enabled */
	protected $enable_decision_manager;

	/** @var API instance */
	protected $api;

	/** @var array shared settings names */
	protected $shared_settings_names = [
		'merchant_id',
		'api_key',
		'api_shared_secret',
		'test_merchant_id',
		'test_api_key',
		'test_api_shared_secret',
		'tokenization_profile_id',
		self::SETTING_ENABLE_DECISION_MANAGER,
	];


	/**
	 * Gateway constructor.
	 *
	 * @param string $id gateway ID
	 * @param Plugin $plugin plugin instance
	 * @param array $args gateway args
	 */
	public function __construct( $id, $plugin, array $args ) {

		parent::__construct( $id, $plugin, $args );

		// add the device data iframe to the checkout markup
		add_action( 'wp_footer', [ $this, 'add_device_data_iframe' ] );

		// blocks initialize (and enqueue scripts) at 5, so we need to register the scripts before that
		if (! did_action('init')) {
			add_action( 'init', [ $this, 'register_scripts' ], 1 );
		} else {
			$this->register_scripts();
		}
	}


	/**
	 * Adds the device data iframe to the checkout markup.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 */
	public function add_device_data_iframe(): void {

		if ( ! $this->is_available() || ! $this->is_decision_manager_enabled() ) {
			return;
		}

		Device_Data::render_noscript_iframe( $this->get_organization_id(), $this->get_merchant_id(), Device_Data::get_session_id() );
	}


	/**
	 * Registers scripts for the gateway.
	 *
	 * This method is called early, so that the scripts are available for the checkout block.
	 *
	 * @since 2.8.0
	 *
	 * @internal
	 * @see \Automattic\WooCommerce\Blocks\Payments::init()
	 */
	public function register_scripts(): void {

		/**
		 * Flex Microform will be enqueued by the shortcode payment form and the checkout block.
		 *
		 * @see Credit_Card_Checkout_Block_Integration::__construct()
		 * @see Payment_Form::render_js()
		 */
		wp_register_script( 'wc-cybersource-flex-microform', $this->get_flex_microform_js_url(), [], $this->get_plugin()->get_version() );

		/**
		 * Device Data JS will be enqueued by the gateway if Decision Manager is enabled.
		 *
		 * @see static::enqueue_gateway_assets()
		 * @see Credit_Card_Checkout_Block_Integration::__construct()
		 */
		if ( $this->is_decision_manager_enabled() && ! is_admin() && ! wp_script_is( 'wc-cybersource-device-data', 'registered' ) ) {

			$session_id = Device_Data::start_session();

			wp_register_script( "wc-cybersource-device-data", Device_Data::get_js_url( $this->get_organization_id(), $this->get_merchant_id(), $session_id ), [], Plugin::VERSION, false );
		}

	}


	/**
	 * Enqueues the gateway assets.
	 *
	 * @since 2.1.0
	 */
	protected function enqueue_gateway_assets() {

		// if enabled, enqueue the device data collection JS and generate a new session ID
		if ( $this->is_decision_manager_enabled() ) {

			wp_enqueue_script('wc-cybersource-device-data');
		}

		// bail if on my account page and *not* on add payment method page
		if ( is_account_page() && ! is_add_payment_method_page() ) {
			return;
		}

		parent::enqueue_gateway_assets();
	}


	/**
	 * Initializes the payment form instance.
	 *
	 * @since 2.1.0
	 *
	 * @return Base_Payment_Form
	 */
	protected function init_payment_form_instance() {

		return new Base_Payment_Form( $this );
	}


	/** Admin settings methods ************************************************/


	/**
	 * Returns an array of form fields specific for this method.
	 *
	 * @see SV_WC_Payment_Gateway::get_method_form_fields()
	 *
	 * @since 2.0.0
	 *
	 * @return array of form fields
	 */
	protected function get_method_form_fields() {

		$fields = [

			// production
			'merchant_id' => [
				'title'    => __( 'Merchant ID', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The Merchant ID for your CyberSource account.', 'woocommerce-gateway-cybersource' ),
			],

			'api_key' => [
				'title'    => __( 'API Key Detail', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The API key ID for your CyberSource account.', 'woocommerce-gateway-cybersource' ),
			],

			'api_shared_secret' => [
				'title'    => __( 'API Shared Secret Key', 'woocommerce-gateway-cybersource' ),
				'type'     => 'password',
				'class'    => 'environment-field production-field',
				'desc_tip' => __( 'The API shared secret key for your CyberSource account.', 'woocommerce-gateway-cybersource' ),
			],

			// test
			'test_merchant_id' => [
				'title'    => __( 'Test Merchant ID', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'The Merchant ID for your CyberSource sandbox account.', 'woocommerce-gateway-cybersource' ),
			],

			'test_api_key' => [
				'title'    => __( 'Test API Key Detail', 'woocommerce-gateway-cybersource' ),
				'type'     => 'text',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'The API key ID for your CyberSource sandbox account.', 'woocommerce-gateway-cybersource' ),
			],

			'test_api_shared_secret' => [
				'title'    => __( 'Test API Shared Secret Key', 'woocommerce-gateway-cybersource' ),
				'type'     => 'password',
				'class'    => 'environment-field test-field',
				'desc_tip' => __( 'The API shared secret key for your CyberSource sandbox account.', 'woocommerce-gateway-cybersource' ),
			],
		];

		// if it was migrated from legacy or SOP (if either option exist, regardless of value),
		// add a button to migrate historical orders
		$migrated        = get_option( 'wc_' . $this->get_plugin()->get_id() . '_legacy_active', false ) ||
		                   get_option( 'wc_' . $this->get_plugin()->get_id() . '_migrated_from_sop', false );
		$orders_migrated = get_option( 'wc_' . $this->get_id() . '_legacy_orders_migrated', false );

		if ( $migrated && ! $orders_migrated ) {

			$fields['migrate_legacy_orders'] = [
				'title' => __( 'Migrate historical orders', 'woocommerce-gateway-cybersource' ),
				'type'  => 'migrate_orders_button',
			];

		}

		$fields[ self::SETTING_ENABLE_DECISION_MANAGER ] = [
			'title'       => __( 'Fraud management', 'woocommerce-gateway-cybersource' ),
			'label'       => __( 'Enable fraud management fraud prevention for your orders', 'woocommerce-gateway-cybersource' ),
			'description' => __( 'Your merchant account must have this optional service enabled.', 'woocommerce-gateway-cybersource' ),
			'type'        => 'checkbox',
			'default'     => 'no',
			'class'       => 'shared-settings-field',
		];

		return $fields;
	}


	/**
	 * Adds the tokenization feature form fields for gateways that support it.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_fields form fields
	 * @return array
	 */
	protected function add_tokenization_form_fields( $form_fields ) {

		$form_fields = parent::add_tokenization_form_fields( $form_fields );

		// Tokenization Profile ID is no longer required, but we need to keep the fields for backwards compatibility,
		// in case a merchant tries to downgrade.
		// TODO: remove in 2.9.0 {@itambek 2024-01-18}
		$form_fields['tokenization_profile_id'] = array(
			'title'       => __( 'Tokenization Profile ID', 'woocommerce-gateway-cybersource' ),
			'description' => __( 'Your Token Management Server profile ID, provided by CyberSource.', 'woocommerce-gateway-cybersource' ),
			'type'        => 'hidden',
			'class'       => 'environment-field deprecated-field profile-id-field',
			'placeholder' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
		);

		$form_fields['test_tokenization_profile_id'] = array(
			'title'       => __( 'Tokenization Profile ID', 'woocommerce-gateway-cybersource' ),
			'description' => __( 'Your Token Management Server profile ID, provided by CyberSource.', 'woocommerce-gateway-cybersource' ),
			'type'        => 'hidden',
			'class'       => 'environment-field deprecated-field profile-id-field',
			'placeholder' => 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
		);

		return $form_fields;
	}


	/**
	 * Displays the admin options.
	 *
	 * Overridden to add a little extra JS for toggling dependant settings.
	 *
	 * @since 2.0.0
	 */
	public function admin_options() {

		parent::admin_options();

		if ( isset( $this->form_fields['tokenization'] ) ) :

			ob_start();

			?>
			$( '#woocommerce_<?php echo esc_js( $this->get_id() ); ?>_tokenization' ).change( function() {

				var enabled     = $( this ).is( ':checked' ),
				    environment = $( '#woocommerce_<?php echo esc_js( $this->get_id() ); ?>_environment' ).val()

				if ( enabled ) {
					$( '.profile-id-field.' + environment + '-field' ).closest( 'tr' ).show();
				} else {
					$( '.profile-id-field.' + environment + '-field' ).closest( 'tr' ).hide();
				}

			} ).change();
			<?php

			wc_enqueue_js( ob_get_clean() );

		endif;
	}


	/**
	 * Generates a "Migrate historical orders" button.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key the field key
	 * @param array $data the field params
	 * @return false|string
	 */
	public function generate_migrate_orders_button_html( $key, $data ) {

		$data = wp_parse_args( $data, [
			'title'       => '',
			'class'       => '',
			'description' => '',
		] );

		$migrate_button_text = __( 'Update records', 'woocommerce-gateway-cybersource' );
		$disabled_text       = __( 'Please save your API credentials before migrating your historical orders', 'woocommerce-gateway-cybersource' );

		if ( $this->get_plugin()->is_subscriptions_active() ) {
			$migrate_text      = __( 'Migrate orders and subscriptions to use this gateway instead of the legacy CyberSource plugin', 'woocommerce-gateway-cybersource' );
			$confirmation_text = __( 'This action will update the payment method on historical orders and subscriptions to use the new CyberSource gateway. This allows you to capture existing payments or process refunds for historical transactions. For subscription records with manual renewals, this will not enable automatic renewals, as customers need to save a payment method first.', 'woocommerce-gateway-cybersource' );
		} else {
			$migrate_text      = __( 'Migrate orders to use this gateway instead of the legacy CyberSource plugin', 'woocommerce-gateway-cybersource' );
			$confirmation_text = __( 'This action will update the payment method on historical orders to use the new CyberSource gateway. This allows you to capture existing payments or process refunds for historical transactions.', 'woocommerce-gateway-cybersource' );
		}

		wp_enqueue_script( 'wc-cybersource-admin', $this->get_plugin()->get_plugin_url() . '/assets/js/admin/wc-cybersource-admin.min.js', [ 'jquery' ], $this->get_plugin()->get_version() );

		wp_localize_script( 'wc-cybersource-admin', 'wc_cybersource_admin', [
			'ajax_url'              => admin_url( 'admin-ajax.php' ),
			'migrate_nonce'         => wp_create_nonce( 'wc_cybersource_migrate_orders' ),
			'gateway_id'            => $this->get_id(),
			'confirmation_text'     => $confirmation_text,
			'migrate_disabled'      => ! $this->is_configured(),
			'migrate_disabled_text' => $disabled_text,
			'migrate_error_message' => __( 'Error executing the migration, please check the debug logs.', 'wc_cybersource_admin' ),
		] );

		ob_start();

		?>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
			</th>
			<td class="forminp <?php echo esc_attr( $data['class'] ); ?>">
				<p class="description">
					<?php echo esc_html( $migrate_text ); ?>
				</p>
				<br />
				<a id="js-wc-cybersource-migrate-orders"
				   href="#"
				   class="button <?php echo ( ! $this->is_configured() ? 'disabled' : '' ); ?>">
					<?php echo esc_html( $migrate_button_text ); ?>
				</a>
				<p id="wc-cybersource-migrate-status" class="description">
					<?php if ( ! $this->is_configured() && ! empty( $disabled_text ) ): ?>
						<?php echo esc_html( $disabled_text ); ?>
					<?php endif; ?>
				</p>
			</td>
		</tr>

		<?php

		return ob_get_clean();
	}


	/** Order handling methods ****************************************************************************************/


	/**
	 * Gets the order object with payment information attached.
	 *
	 * @since 2.3.0
	 *
	 * @param int|\WC_Order $order_id WooCommerce order ID or object
	 * @return \WC_Order
	 */
	public function get_order( $order_id ) {

		$order = parent::get_order( $order_id );

		$order->merchant_customer_id = $order->get_user_id() ?: $this->get_guest_customer_id( $order );

		$order->use_decision_manager = $this->is_decision_manager_enabled();

		// if the device data (fingerprinting) session ID is present, add it to the order
		if ( $session_id = Device_Data::get_session_id() ) {
			$order->decision_manager_session_id = $session_id;
		}

		if ( $this->is_flex_form_enabled() && $flex_token = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-flex-token' ) ) {

			$flex_key = Framework\SV_WC_Helper::get_posted_value( 'wc-' . $this->get_id_dasherized() . '-flex-key' );

			// decode the token to get the payment information
			try {

				$payload = Flex_Helper::decode_flex_token( $flex_token, $flex_key );

			} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

				$this->add_debug_message( $exception->getMessage(), 'error' );

				throw new Framework\SV_WC_Plugin_Exception( __( 'An error occurred, please try again or try an alternate form of payment.', 'woocommerce-gateway-cybersource' ) );
			}

			$order->payment->jwt = $flex_token;

			// add Flex Microform tokenization data to the order
			$order = $this->get_flex_form_order( $order, $payload );
		}

		$order->create_token = $this->get_payment_tokens_handler()->should_tokenize();

		return $order;
	}


	/**
	 * Adds transaction data to the order that's specific to this gateway.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Customer_Response $response API response object
	 */
	public function add_payment_gateway_transaction_data( $order, $response ) {

		// ensure we have valid objects
		if ( ! $order instanceof \WC_Order || ! $response instanceof Framework\SV_WC_Payment_Gateway_API_Response ) {
			return;
		}

		if ( ! empty( $order->payment->instrument_identifier ) ) {
			$this->update_order_meta( $order, 'instrument_identifier_id', $order->payment->instrument_identifier->id );
			$this->update_order_meta( $order, 'instrument_identifier_new', $order->payment->instrument_identifier->new ? 'yes' : 'no' );
			$this->update_order_meta( $order, 'instrument_identifier_state', $order->payment->instrument_identifier->state );
		}

		// if the transaction is pending review, it could still be captured if the merchant deems it legitimate
		if ( $response instanceof API\Responses\Payments ) {

			if ( $reconciliation_id = $response->get_reconciliation_id() ) {
				$this->update_order_meta( $order, 'reconciliation_id', $reconciliation_id );
			}

			if ( $transaction_id = $response->get_processor_transaction_id() ) {
				$this->update_order_meta( $order, 'processor_transaction_id', $transaction_id );
			}

			if ( $response->transaction_held() && API\Responses\Payments\Payment::STATUS_AUTHORIZED_PENDING_REVIEW === $response->get_status_code() ) {
				$this->update_order_meta( $order, 'charge_captured', 'no' );
			}
		}

		// add any decision manager data to the given order
		if ( $this->is_decision_manager_enabled() ) {
			$this->add_decision_manager_data( $order, $response );
		}
	}


	/**
	 * Adds any decision manager data to the given order.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param Framework\SV_WC_Payment_Gateway_API_Response $response API response object
	 */
	protected function add_decision_manager_data( \WC_Order $order, Framework\SV_WC_Payment_Gateway_API_Response $response ) {

		// always store the transaction ID so the order can be located by Decision Manager later
		if ( $transaction_id = $response->get_transaction_id() ) {

			$this->update_order_meta( $order, 'trans_id', $transaction_id );

			// @NOTE: Do not update the transaction order meta directly to avoid WC casting a notice
			$order->set_transaction_id( $transaction_id );
			$order->save();
		}

		$message = '';

		switch ( $response->get_status_code() ) {

			case API\Responses\Payments\Payment::STATUS_AUTHORIZED:
				$message = __( 'Order authorized by CyberSource fraud management.', 'woocommerce-gateway-cybersource' );
			break;

			case API\Responses\Payments\Payment::STATUS_AUTHORIZED_PENDING_REVIEW:
				$message = __( 'Order requires manual review in CyberSource Case Management system.', 'woocommerce-gateway-cybersource' );
			break;

			case API\Responses\Payments\Payment::STATUS_AUTHORIZED_RISK_DECLINED:
				$message = __( 'Order rejected by CyberSource fraud management. View the CyberSource Case Management system for more details.', 'woocommerce-gateway-cybersource' );
			break;
		}

		if ( $message ) {
			$order->add_order_note( $message );
		}
	}


	/**
	 * Adds any necessary flex tokenization data to the order's payment property.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param array $payload tokenization data from CyberSource
	 * @return \WC_Order
	 */
	public function get_flex_form_order( \WC_Order $order, $payload = [] ) {

		$order->payment->jti = ! empty( $payload['jti'] ) ? $payload['jti'] : '';

		return $order;
	}


	/**
	 * Marks an order as failed.
	 *
	 * @since 2.3.0
	 *
	 * @param \WC_Order $order WooCommerce order object
	 * @param string $error_message error message
	 * @param Framework\SV_WC_Payment_Gateway_API_Response|null $response response object, if any
	 */
	public function mark_order_as_failed( $order, $error_message, $response = null ) {

		parent::mark_order_as_failed( $order, $error_message, $response );

		// ensure we have valid objects
		if ( ! $order instanceof \WC_Order || ! $response instanceof Framework\SV_WC_Payment_Gateway_API_Response ) {
			return;
		}

		// add any decision manager data to the given order
		if ( $this->is_decision_manager_enabled() ) {
			$this->add_decision_manager_data( $order, $response );
		}
	}


	/** Tokenization methods ******************************************************************************************/


	/**
	 * Tokenize with sale.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function tokenize_with_sale() {

		return true;
	}


	/**
	 * Gets a user's customer ID.
	 *
	 * CyberSource does not support customer IDs.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id WordPress user ID
	 * @param array $args customer ID args
	 * @return false
	 */
	public function get_customer_id( $user_id, $args = [] ) {

		// Cybersource creates customer IDs for us
		$args = array_merge( $args, [
			'autocreate' => false,
		] );

		return parent::get_customer_id( $user_id, $args );
	}


	/** Getters ***************************************************************/


	/**
	 * Gets the order's transaction URL for use in the admin.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return string
	 */
	public function get_transaction_url( $order ) {

		// build the URL to the test/production environment
		if ( self::ENVIRONMENT_TEST === $this->get_order_meta( $order, 'environment' ) ) {
			$base_url = 'https://ubctest.cybersource.com/ebc2/app/TransactionManagement/details';
		} else {
			$base_url = 'https://ubc.cybersource.com/ebc2/app/TransactionManagement/details';
		}

		$this->view_transaction_url = $base_url . '?requestId=%s&merchantId=' . $this->get_merchant_id();

		/**
		 * Filters the transaction URL for admin use.
		 *
		 * @since 2.4.1
		 *
		 * @param string $transaction_url the transaction URL
		 * @param \WC_Order $order the order object
		 * @param \SkyVerge\WooCommerce\Cybersource\Gateway CyberSource gateway object
		 */
		return (string) apply_filters( 'wc_cybersource_gateway_transaction_url', parent::get_transaction_url( $order ), $order, $this );
	}


	/**
	 * Determines if the gateway is properly configured to perform transactions.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		$is_configured = parent::is_configured();

		if ( ! $this->get_merchant_id() || ! $this->get_api_key() || ! $this->get_api_shared_secret() ) {
			$is_configured = false;
		}

		return $is_configured;
	}


	/**
	 * Gets the API object.
	 *
	 * @see SV_WC_Payment_Gateway::get_api()
	 *
	 * @since 2.0.0
	 *
	 * @return API instance
	 */
	public function get_api() {

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		return $this->api = new API( $this );
	}


	/**
	 * Returns the merchant account ID based on the current environment.
	 *
	 * @since 2.0.0
	 *
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string
	 */
	public function get_merchant_id( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->merchant_id : $this->test_merchant_id;
	}


	/**
	 * Returns the API key ID based on the current environment.
	 *
	 * @since 2.0.0
	 *
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string
	 */
	public function get_api_key( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->api_key : $this->test_api_key;
	}


	/**
	 * Returns the API shared secret based on the current environment.
	 *
	 * @since 2.0.0
	 *
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string
	 */
	public function get_api_shared_secret( $environment_id = null ) {

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->api_shared_secret : $this->test_api_shared_secret;
	}


	/**
	 * Gets the Token Management Server Profile ID.
	 *
	 * @since 2.0.0
	 *
	 * @deprecated 2.8.0 Tokenization Profile ID is no longer required
	 *
	 * @param string $environment_id optional one of 'test' or 'production', defaults to current configured environment
	 * @return string
	 */
	public function get_tokenization_profile_id( $environment_id = null ) {

		_deprecated_function( __METHOD__, '2.8.0' );

		if ( null === $environment_id ) {
			$environment_id = $this->get_environment();
		}

		return self::ENVIRONMENT_PRODUCTION === $environment_id ? $this->tokenization_profile_id : $this->test_tokenization_profile_id;
	}


	/**
	 * Determines whether the flex form is enabled.
	 *
	 * @since 2.0.0
	 * @since 2.5.0 always returns true
	 *
	 * @return bool
	 */
	public function is_flex_form_enabled() {

		return true;
	}


	/**
	 * Determines whether the flex form is supported by this gateway.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function supports_flex_form() {

		return $this->supports( self::FEATURE_FLEX_FORM );
	}


	/**
	 * Gets the configured organization ID.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_organization_id(): string {

		/**
		 * Filters the organization ID.
		 *
		 * @since 2.5.3
		 *
		 * @param string|null $organizationI-d
		 * @param Gateway $gateway
		 */
		return (string) apply_filters( 'wc_cybersource_api_organization_id', $this->is_test_environment() ? '1snn5n9w' : 'k8vif92e', $this );
	}


	/**
	 * Determines whether the Decision Manager feature is configured to be enabled.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_decision_manager_enabled(): bool {

		return 'yes' === $this->enable_decision_manager && $this->get_organization_id();
	}


	/**
	 * Gets the FLex Microform asset url.
	 *
	 * This value will be overridden by {@see Flex_Helper::addFlexMicroformScriptHooks()}
	 * We don't add it in here directly to avoid calling {@see CaptureContextRetriever} unnecessarily on every page load.
	 * By filtering the `src` value after the fact we ensure we only make API calls when the script is actually being used.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_flex_microform_js_url() :string {

		// Note: this URL can also be extracted from the capture context JWT, which might be a more resilient option
		// when upgrading the Microform client version
		return $this->is_production_environment()
			? 'https://flex.cybersource.com/microform/bundle/v2/flex-microform.min.js'
			: 'https://testflex.cybersource.com/microform/bundle/v2/flex-microform.min.js';
	}

}
