<?php

namespace WcPaysafe\Admin;

use WcPaysafe\Gateways\Redirect\Gateway;
use WcPaysafe\Helpers\Factories;
use WcPaysafe\Paysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that represents admin notices.
 *
 * @since 3.2.0
 */
class Admin_Notices {
	
	/**
	 * All allowed notices
	 * @var array
	 */
	public $allowed_notices = array(
		'valid_ssl',
		'phpver',
		'wcver',
		'curl',
		'missing_api_key',
		'missing_checkoutjs_api_key',
		'incorrect_api_key',
	);
	/**
	 * Collection of all active notices
	 * @var array
	 */
	public $active_notices = array();
	protected $prefix = 'wc_paysafe';
	protected $id = 'paysafe';
	protected $text_domain = 'wc_paysafe';
	protected $gateway_notice_name;
	
	public function __construct() {
		$this->gateway_notice_name = __( 'Paysafe Gateway', $this->text_domain );
	}
	
	/**
	 * Hooks. Should be loaded only once
	 */
	public function hooks() {
		add_action( 'admin_notices', array( $this, 'maybe_admin_notices' ) );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
	}
	
	/**
	 * Adds an allowed notice slug, so we can retrieve and update it
	 *
	 * @since 3.2.0
	 *
	 * @param $slug
	 */
	public function add_allowed_notice( $slug ) {
		$this->allowed_notices[] = $slug;
	}
	
	/**
	 * Adds a notice to the display list
	 *
	 * @since 3.2.0
	 *
	 * @param      $slug
	 * @param      $type
	 * @param      $message
	 * @param bool $dismissible
	 */
	public function add_notice( $slug, $type, $message, $dismissible = false ) {
		
		$map_class = array(
			'error'   => 'error',
			'notice'  => 'notice notice-warning',
			'warning' => 'notice notice-error',
		);
		
		$this->active_notices[ $slug ] = array(
			'class'       => $map_class[ $type ],
			'message'     => $this->gateway_notice_name . ' - ' . $message,
			'dismissible' => $dismissible,
		);
	}
	
	/**
	 * Updates the display status of a notice
	 *
	 * @since 3.2.0
	 *
	 * @param $slug
	 * @param $value
	 */
	public function update_notice( $slug, $value ) {
		update_option( $this->prefix . '_show_notice_' . $slug, $value );
	}
	
	/**
	 * Loads the notices
	 * @since 3.2.0
	 */
	public function maybe_admin_notices() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		
		$this->perform_checks();
		
		foreach ( (array) $this->active_notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';
			
			if ( $notice['dismissible'] ) {
				?>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-' . $this->id . '-hide-notice', $notice_key ), $this->prefix . '_hide_notice_nonce', $this->prefix . '_notice_nonce' ) ); ?>" class="woocommerce-message-close notice-dismiss" style="position:absolute;right:1px;padding:9px;text-decoration:none;"></a>
				<?php
			}
			
			echo '<p>';
			echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
			echo '</p></div>';
		}
	}
	
	public function get_notices_values() {
		$values = array();
		foreach ( $this->allowed_notices as $name ) {
			$values[ $name ] = 'no' == get_option( $this->prefix . '_show_notice_' . $name ) ? false : true;
		}
		
		return $values;
	}
	
	/**
	 * Performs the plugin checks
	 * @since 3.2.0
	 */
	public function perform_checks() {
		$notices_values = $this->get_notices_values();
		
		/**
		 * @var Gateway $gateway
		 */
		$gateway = Factories::get_gateway( 'netbanx' );
		
		if ( false == $gateway ) {
			return;
		}
		
		// Bail, if the gateway is not enabled
		if ( 'yes' !== $gateway->enabled ) {
			return;
		}
		
		$testmode = 'yes' == $gateway->testmode;
		
		if ( $notices_values['phpver'] ) {
			if ( version_compare( phpversion(), Paysafe::MIN_PHP_VERSION, '<' ) ) {
				$message = __( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', $this->text_domain );
				
				$this->add_notice( 'phpver', 'error', sprintf( $message, Paysafe::MIN_PHP_VERSION, phpversion() ), true );
				
				return;
			}
		}
		
		if ( $notices_values['wcver'] ) {
			$wc_version = defined( 'WC_VERSION' ) ? WC_VERSION : '1.0.0';
			if ( version_compare( $wc_version, Paysafe::MIN_WC_VERSION, '<' ) ) {
				$message = __( 'The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', $this->text_domain );
				
				$this->add_notice( 'wcver', 'notice', sprintf( $message, Paysafe::MIN_WC_VERSION, $wc_version ), true );
				
				return;
			}
		}
		
		if ( $notices_values['curl'] ) {
			if ( ! function_exists( 'curl_init' ) ) {
				$this->add_notice( 'curl', 'notice', __( 'cURL is not installed.', $this->text_domain ), true );
			}
		}
		
		if ( $notices_values['valid_ssl'] ) {
			if ( ! wc_checkout_is_https() && ! $testmode ) {
				$this->add_notice( 'valid_ssl', 'notice', sprintf( __( 'The gateway is enabled for live payments, but an SSL certificate is not detected. Your checkout may not be secure! Please ensure your server has a valid <a href="%1$s" target="_blank">SSL certificate</a>', $this->text_domain ), 'https://en.wikipedia.org/wiki/Transport_Layer_Security' ), true );
			}
		}
		
		if ( 'hosted' == $gateway->integration ) {
			
			if ( $notices_values['missing_api_key'] ) {
				if ( '' == $gateway->get_option( 'api_user_name' )
				     || '' == $gateway->get_option( 'api_password' ) ) {
					$this->add_notice( 'missing_api_key', 'warning',
						sprintf( __( 'Your API credentials are not entered. Please visit the %ssettings page%s to enter your credentials. Your gateway will not be shown on the checkout page.', $this->text_domain
						), '<a href="' . $this->get_setting_link() . '">', '</a>' ), true );
				}
			}
		} else {
			if ( $notices_values['missing_checkoutjs_api_key'] ) {
				if ( '' == $gateway->get_option( 'api_user_name' )
				     || '' == $gateway->get_option( 'api_password' )
				     || '' == $gateway->get_option( 'single_use_token_user_name' )
				     || '' == $gateway->get_option( 'single_use_token_password' )
				     || ( '' == $gateway->get_account_id()
				          && '' == $gateway->get_account_id( null, 'directdebit' ) )
				) {
					$this->add_notice( 'missing_checkoutjs_api_key', 'warning',
						sprintf( __( 'Not all required credentials are filled in. Please visit the %ssettings page%s to enter your credentials. Your gateway will not be shown on the checkout page.', $this->text_domain
						), '<a href="' . $this->get_setting_link() . '">', '</a>' ), true );
				}
			}
		}
		
		// Allow 3rd party to add notices
		do_action( $this->prefix . '_admin_notices_checks', $notices_values, $this );
	}
	
	/**
	 * Hides any admin notices.
	 *
	 * @since 3.2.0
	 */
	public function hide_notices() {
		if ( isset( $_GET[ 'wc-' . $this->id . '-hide-notice' ] ) && isset( $_GET[ $this->prefix . '_notice_nonce' ] ) ) {
			if ( ! wp_verify_nonce( wc_clean( wp_unslash( $_GET[ $this->prefix . '_notice_nonce' ] ) ), $this->prefix . '_hide_notice_nonce' ) ) {
				wp_die( esc_html( __( 'Action failed. Please refresh the page and retry.', $this->text_domain ) ) );
			}
			
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html( __( 'Cheatin&#8217; huh?', $this->text_domain ) ) );
			}
			
			$notice = wc_clean( wp_unslash( $_GET[ 'wc-' . $this->id . '-hide-notice' ] ));
			
			if ( in_array( $notice, $this->allowed_notices ) ) {
				$this->update_notice( $notice, 'no' );
			}
		}
	}
	
	/**
	 * Get setting link.
	 *
	 * @since 3.2.0
	 *
	 * @return string Setting link
	 */
	public function get_setting_link() {
		$use_id_as_section = function_exists( 'WC' ) ? version_compare( WC()->version, '2.6', '>=' ) : false;
		
		// TODO: needs to revisit the integration part as get_gateway_class will return hosted or direct class integration
		$section_slug = $use_id_as_section ? 'netbanx' : wc_paysafe_instance()->get_gateway_class();
		
		return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $section_slug );
	}
}