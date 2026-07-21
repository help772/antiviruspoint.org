<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Payment_Token_Paysafe_DD extends \WC_Payment_Token_eCheck {
	
	/** @protected string Token Type String. */
	protected $type = 'Paysafe_DD';
	protected $token_type = 'directdebit';
	
	public function __construct( $token = '' ) {
		$this->extra_data['source_id']          = '';
		$this->extra_data['profile_id']         = '';
		$this->extra_data['billing_address_id'] = '';
		$this->extra_data['bank_account_type']  = '';
		parent::__construct( $token );
	}
	
	public function get_token_type() {
	        return $this->token_type;
	}
	
	/**
	 * Get type to display to user.
	 *
	 * @since  3.3.0
	 *
	 * @param string $deprecated Deprecated since WooCommerce 3.0
	 *
	 * @return string
	 */
	public function get_display_name( $deprecated = '' ) {
		/* translators: 1: credit card type 2: last 4 digits 3: expiry month 4: expiry year */
		$display = sprintf(
			__( '%2$s bank account ending in %1$s', 'wc_paysafe' ),
			$this->get_last4(),
			strtoupper( $this->get_bank_account_type() )
		);
		
		return apply_filters( 'wc_paysafe_dd_display_name', $display, $this );
	}
	
	/**
	 * Hook prefix
	 *
	 * @since 3.3.0
	 */
	protected function get_hook_prefix() {
		return 'woocommerce_payment_token_paysafe_dd_get_';
	}
	
	/**
	 * Validate credit card payment tokens.
	 *
	 * Requried:
	 * last4 - digits of the bank account
	 * source_id - The ID of the method
	 * bank_account_type - The bank account type: ach, eft, bacs, sepa
	 *
	 * @since 3.3.0
	 * @return boolean True if the passed data is valid
	 */
	public function validate() {
		if ( ! $this->get_last4( 'edit' ) ) {
			return false;
		}
		
		// We need the source ID
		if ( ! $this->get_source_id( 'edit' ) ) {
			return false;
		}
		
		$bank_account = $this->get_bank_account_type( 'edit' );
		if ( ! $bank_account ) {
			return false;
		}
		
		if ( ! in_array( $bank_account, $this->allowed_bank_account_types() ) ) {
			return false;
		}
		
		return true;
	}
	
	public function allowed_bank_account_types() {
		return apply_filters( 'wc_paysafe_token_dd_allowed_bank_account_types', array(
			'ach',
			'bacs',
			'eft',
			'sepa',
		) );
	}
	
	/**
	 * Delete an object, set the ID to 0, and return result.
	 *
	 * @since  3.3.0
	 *
	 * @param bool $force_delete
	 *
	 * @return bool result
	 */
	public function delete( $force_delete = false ) {
		if ( $this->data_store ) {
			
			// Run an action before we delete a Payment Token
			do_action( 'woocommerce_paysafe_payment_token_delete', $this->get_id(), $this );
			
			$this->data_store->delete( $this, array( 'force_delete' => $force_delete ) );
			$this->set_id( 0 );
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the bank account type.
	 * Values: ach, eft, bacs, sepa
	 *
	 * @since 3.3.0
	 *
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function get_bank_account_type( $context = 'view' ) {
		return $this->get_meta( 'bank_account_type', true, $context );
	}
	
	/**
	 * Sets the bank account type.
	 * Values: ach, eft, bacs, sepa
	 *
	 * @since 3.3.0
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set_bank_account_type( $value ) {
		$this->add_meta_data( 'bank_account_type', $value, true );
	}
	
	/**
	 * Returns Profile id of the token
	 *
	 * @since 3.3.0
	 *
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function get_profile_id( $context = 'view' ) {
		return $this->get_meta( 'profile_id' );
	}
	
	/**
	 * Sets the Profile id of the token
	 *
	 * @since 3.3.0
	 *
	 * @param $profile_id
	 */
	public function set_profile_id( $profile_id ) {
		$this->add_meta_data( 'profile_id', $profile_id, true );
	}
	
	/**
	 * This is the Card ID, Bank Account ID or the basic object ID that we can use to retrieve the card or bank account with.
	 *
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function get_source_id( $context = 'view' ) {
		return $this->get_meta( 'source_id', true, $context );
	}
	
	/**
	 * This is the Card ID, Bank Account ID or the basic object ID that we can use to retrieve the card or bank account with.
	 *
	 * @since 2.0
	 *
	 * @param string $value Usually in the format: 6a275b7c-6f11-4ed1-ae77-21071724574a
	 */
	public function set_source_id( $value ) {
		$this->add_meta_data( 'source_id', $value, true );
	}
	
	/**
	 * Returns the billing address ID
	 *
	 * @since  3.3.0
	 *
	 * @param string $context
	 *
	 * @return string Expiration year
	 */
	public function get_billing_address_id( $context = 'view' ) {
		return $this->get_prop( 'billing_address_id', $context );
	}
	
	/**
	 * Set the billing address ID
	 * @since 3.3.0
	 *
	 * @param string $value
	 */
	public function set_billing_address_id( $value ) {
		$this->set_prop( 'billing_address_id', $value );
	}
	
	/**
	 * Return if the method is set to be the default one
	 *
	 * @since 2.2
	 *
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function get_is_default( $context = 'view' ) {
		if ( ! \WcPaysafe\Compatibility\WC_Compatibility::is_wc_3_0() ) {
			return $this->is_default();
		}
		
		return parent::get_is_default( $context );
	}
}