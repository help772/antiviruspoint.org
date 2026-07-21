<?php

namespace WcPaysafe\Api\Vault;

use WcPaysafe\Api\Request_Fields\Vault_Fields;
use WcPaysafe\Api\Service_Abstract;
use WcPaysafe\Api\Service_Interface;
use WcPaysafe\Api\Vault\Requests\Ach;
use WcPaysafe\Api\Vault\Requests\Bacs;
use WcPaysafe\Api\Vault\Requests\Cards;
use WcPaysafe\Api\Vault\Requests\Eft;
use WcPaysafe\Api\Vault\Requests\Profiles;
use WcPaysafe\Api\Vault\Requests\Sepa;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides setup and routing for the specific service actions card/check payments, refunds, captures etc.
 * Will load the integration credentials and pass the request props to the specific service class.
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Service extends Service_Abstract implements Service_Interface {
	
	/**
	 * @param $source
	 *
	 * @return Vault_Fields
	 */
	public function get_fields( $source ) {
		return new Vault_Fields( $source );
	}
	
	/**
	 * Returns the Transaction API class
	 *
	 * @since 2.0
	 *
	 * @return \WcPaysafe\Api\Vault\Requests\Profiles
	 */
	public function profile() {
		return new Profiles( $this );
	}
	
	public function card() {
		return new Cards( $this );
	}
	
	public function ach() {
		return new Ach( $this );
	}
	
	public function eft() {
		return new Eft( $this );
	}
	
	public function bacs() {
		return new Bacs( $this );
	}
	
	public function sepa() {
		return new Sepa( $this );
	}
}