<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use stdClass;

defined( 'ABSPATH' ) || exit;

class ProductInstalledOn extends AbstractResourceModel implements ModelInterface {

	protected $id;


	protected $license_id;


	protected $host;


	protected $lastPing;


	public function __construct( $product_installed_on ) {
		if ( ! $product_installed_on instanceof stdClass ) {
			return;
		}

		$this->id        = null === $product_installed_on->id ? null : (int) $product_installed_on->id;
		$this->licenseId = null === $product_installed_on->license_id ? null : (int) $product_installed_on->license_id;
		$this->host      = $product_installed_on->host;
		$this->lastPing  = $product_installed_on->last_ping;
	}

	
	public function getId() {
		return $this->id;
	}

	
	public function setId( $id ) {
		$this->id = $id;
	}

	
	public function getLicenseId() {
		return $this->licenseId;
	}


	public function setLicenseId( $license_id ) {
		$this->licenseId = $license_id;
	}


	public function getHost() {
		return $this->host;
	}

	
	public function setHost( $host ) {
		$this->host = $host;
	}


	public function getLastPing() {
		return $this->lastPing;
	}

	
	public function setLastPing( $lastPing ) {
		$this->lastPing = $lastPing;
	}
}
