<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Payment Token CC.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WC_Payment_Token_Paysafe_CC extends \WC_Payment_Token_CC {
	
	/** @protected string Token Type String. */
	protected $type = 'Paysafe_CC';
	protected $token_type = 'card';
	
	public function __construct( $token = '' ) {
		$this->extra_data['source_id']          = '';
		$this->extra_data['profile_id']         = '';
		$this->extra_data['billing_address_id'] = '';
		parent::__construct( $token );
	}
	
	public function get_token_type() {
	        return $this->token_type;
	}
	
	/**
	 * Returns the card type label
	 *
	 * @since 2.1.1
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public function get_card_type_label( $type ) {
		$label = wc_get_credit_card_type_label( $type );
		$label = '' == $label ? _x( 'Card', 'label-unknown-card-type', 'wc_paysafe' ) : $label;
		
		return apply_filters( 'wc_paysafe_card_type_label', $label, $type );
	}
	
	/**
	 * Get type to display to user.
	 *
	 * @since  3.3.0
	 *
	 * @param  string $deprecated Deprecated since WooCommerce 3.0
	 *
	 * @return string
	 */
	public function get_display_name( $deprecated = '' ) {
		/* translators: 1: credit card type 2: last 4 digits 3: expiry month 4: expiry year */
		$display = sprintf(
			__( '%1$s ending in %2$s (expires %3$s/%4$s)', 'wc_paysafe' ),
			$this->get_card_type_label( $this->get_card_type() ),
			$this->get_last4(),
			$this->get_expiry_month(),
			substr( $this->get_expiry_year(), 2 )
		);
		
		return apply_filters( 'wc_paysafe_card_display_name', $display, $this );
	}
	
	/**
	 * Hook prefix
	 *
	 * @since 3.3.0
	 */
	protected function get_hook_prefix() {
		return 'woocommerce_payment_token_paysafe_cc_get_';
	}
	
	/**
	 * Validate credit card payment tokens.
	 *
	 * These fields are required by all credit card payment tokens:
	 * expiry_month  - string Expiration date (MM) for the card
	 * expiry_year   - string Expiration date (YYYY) for the card
	 * last4         - string Last 4 digits of the card
	 *
	 * @since 3.3.0
	 *
	 * @return boolean True if the passed data is valid
	 */
	public function validate() {
		$token = $this->get_prop( 'token', 'edit' );
		if ( empty( $token ) ) {
			return false;
		}
		
		if ( ! $this->get_last4( 'edit' ) ) {
			return false;
		}
		
		if ( ! $this->get_expiry_year( 'edit' ) ) {
			return false;
		}
		
		if ( ! $this->get_expiry_month( 'edit' ) ) {
			return false;
		}
		
		if ( 4 !== strlen( $this->get_expiry_year( 'edit' ) ) ) {
			return false;
		}
		
		if ( 2 !== strlen( $this->get_expiry_month( 'edit' ) ) ) {
			return false;
		}
		
		// We need the source ID
		if ( ! $this->get_source_id( 'edit' ) ) {
			return false;
		}
		
		if ( ! $this->get_profile_id( 'edit' ) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the Profile id of the token
	 *
	 * @since 3.3.0
	 *
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function get_profile_id( $context = 'view' ) {
		return $this->get_meta( 'profile_id', true, $context );
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
	 * @since 3.3.0
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
	 * @param  string $context
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
	
	/**
	 * Don't save new token, if the token already exists
	 *
	 * @since 3.3.0
	 *
	 * @return int
	 */
	public function save() {
		$tokens = WC_Payment_Tokens::get_tokens(
			array(
				'user_id' => $this->get_user_id(),
				'type'    => $this->get_type()
			)
		);
		
		if ( ! empty( $tokens ) ) {
			/**
			 * @var WC_Payment_Token_Paysafe_CC $prev_token
			 */
			foreach ( $tokens as $prev_token ) {
				// If we already have the same token saved, but the IDs do not match,
				// update the existing token only
				if ( $this->get_token() == $prev_token->get_token()
				     && $this->get_id() !== $prev_token->get_id() ) {
					// We will just update the token if only the token value is the same
					$this->set_id( $prev_token->get_id() );
					break;
				}
			}
		}
		
		return parent::save();
	}
	
	/**
	 * Delete an object, set the ID to 0, and return result.
	 *
	 * @since  3.3.0
	 *
	 * @param  bool $force_delete
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
}