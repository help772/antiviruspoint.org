<?php

namespace WcPaysafe\Gateways\Redirect\Checkout\Response_Processors;

use Paysafe\CustomerVault\ACHBankaccounts;
use Paysafe\CustomerVault\EFTBankaccounts;
use Paysafe\CustomerVault\SEPABankaccounts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since
 * @author VanboDevelops
 *
 *        Copyright: (c) 2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Vault_Processor extends Checkoutjs_Processor {
	
	public function is_successful() {
		return $this->response->get_error();
	}
	
	public function get_bank_account_type() {
		$bank_account_type = 'bacs';
		if ( $this->get_response() instanceof EFTBankaccounts ) {
			$bank_account_type = 'eft';
		} elseif ( $this->get_response() instanceof ACHBankaccounts ) {
			$bank_account_type = 'ach';
		} elseif ( $this->get_response() instanceof SEPABankaccounts ) {
			$bank_account_type = 'sepa';
		}
		
		return $bank_account_type;
	}
}