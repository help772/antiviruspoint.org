<?php
/**
 * WC_Software_API class.
 *
 * @extends WooCommerce_Software
 */
class WC_Software_API {
	public $debug;
	private $available_requests = array();

	public function __construct( $debug = false ) {
		$this->debug = ( WP_DEBUG ) ? true : $debug; // always on if WP_DEBUG is on

		if ( apply_filters( 'woocommerce_software_addon_api_require_user_auth', false ) && ! is_user_logged_in() ) {
			$this->error( '403' );
		}

		$this->load_available_requests();

		if ( isset( $_REQUEST['request'] ) ) {

			$request = $_REQUEST['request'];

			if ( isset( $this->available_requests[ $request ] ) ) {
				$json = $this->available_requests[ $request ]->do_request();
			}

			if ( ! isset( $json ) ) {
				$this->error( '100', __( 'Invalid API Request', 'woocommerce-software-add-on' ) );
			}
		} else {
			$this->error( '100', __( 'No API Request Made', 'woocommerce-software-add-on' ) );
		}

		nocache_headers();
		wp_send_json( $json );
	}

	private function load_available_requests() {
		require_once 'class-wc-software-api-request.php';

		require 'requests/class-wc-generate-key-request.php';
		require 'requests/class-wc-check-request.php';
		require 'requests/class-wc-activation-request.php';
		require 'requests/class-wc-activation-reset-request.php';
		require 'requests/class-wc-deactivation-request.php';

		$this->available_requests['generate_key']     = new WC_Generate_Key_Request( $this );
		$this->available_requests['check']            = new WC_Check_Request( $this );
		$this->available_requests['activation']       = new WC_Activation_Request( $this );
		$this->available_requests['activation_reset'] = new WC_Activation_Reset_Request( $this );
		$this->available_requests['deactivation']     = new WC_Deactivation_Request( $this );
	}

	public function error( $code = 100, $debug_message = null, $secret = null, $addtl_data = array() ) {
		switch ( $code ) {
			case '101':
				$error = array(
					'error' => __( 'Invalid License Key', 'woocommerce-software-add-on' ),
					'code'  => '101',
				);
				break;
			case '102':
				$error = array(
					'error' => __( 'Software has been deactivated', 'woocommerce-software-add-on' ),
					'code'  => '102',
				);
				break;
			case '103':
				$error = array(
					'error' => __( 'Exceeded maximum number of activations', 'woocommerce-software-add-on' ),
					'code'  => '103',
				);
				break;
			case '104':
				$error = array(
					'error' => __( 'Invalid Instance ID', 'woocommerce-software-add-on' ),
					'code'  => '104',
				);
				break;
			case '105':
				$error = array(
					'error' => __( 'Invalid security key', 'woocommerce-software-add-on' ),
					'code'  => '105',
				);
				break;
			case '403':
				$error = array(
					'error' => __( 'Forbidden', 'woocommerce-software-add-on' ),
					'code'  => '403',
				);
				break;
			default:
				$error = array(
					'error' => __( 'Invalid Request', 'woocommerce-software-add-on' ),
					'code'  => '100',
				);
				break;
		}

		if ( isset( $this->debug ) && $this->debug ) {
			if ( ! isset( $debug_message ) || ! $debug_message ) {
				$debug_message = __( 'No debug information available', 'woocommerce-software-add-on' );
			}
			$error['additional info'] = $debug_message;
		}

		if ( isset( $addtl_data['secret'] ) ) {
			$secret = $addtl_data['secret'];
			unset( $addtl_data['secret'] );
		}

		foreach ( $addtl_data as $k => $v ) {
			$error[ $k ] = $v;
		}

		$secret             = ( $secret ) ? $secret : 'null';
		$error['timestamp'] = time();

		foreach ( $error as $k => $v ) {
			if ( $v === false ) {
				$v = 'false';
			}
			if ( $v === true ) {
				$v = 'true';
			}
			$sigjoined[] = "$k=$v";
		}

		$sig = implode( '&', $sigjoined );
		$sig = 'secret=' . $secret . '&' . $sig;

		if ( ! $this->debug ) {
			$sig = md5( $sig );
		}

		$error['sig'] = $sig;
		$json         = $error;

		nocache_headers();
		wp_send_json( $json );
	}
}

$GLOBALS['wc_software_api'] = new WC_Software_API(); // run the API
