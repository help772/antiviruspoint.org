<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use stdClass;

/**
 * Class applicationMeta
 *
 * @package LicenseManagerForWooCommercePro\Database\Models\Resources
 */
class ApplicationMeta extends AbstractResourceModel implements ModelInterface {


	/**
	 * Meta id
	 * 
	 * @var int
	**/
	protected $meta_id;

	/**
	 * Application id
	 * 
	 * @var int
	**/
	protected $application_id;

	/**
	 * Meta key
	 * 
	 * @var string
	**/
	protected $meta_key;

	/**
	 * Meta value
	 * 
	 * @var mixed
	**/
	protected $meta_value;

	/**
	 * License constructor.
	 *
	 * @param stdClass $meta
	**/
	public function __construct( $meta ) {
		if ( ! $meta instanceof stdClass ) {
			return;
		}
		$this->meta_id     = (int) $meta->meta_id;
		$this->application_id = (int) $meta->application_id;
		$this->meta_key    = $meta->meta_key;
		$this->meta_value  = json_decode($meta->meta_value, true);
		if ( is_string( $this->meta_value ) ) {
			$this->meta_value = wp_unslash( $this->meta_value );
		}
	}

	/**
	 * Get meta id
	 * 
	 * @return int
	**/
	public function getMetaId() {
		return $this->meta_id;
	}

	/**
	 * Set meta id
	 * 
	 * @param int $meta_id
	**/
	public function setMetaId( $meta_id ) {
		$this->meta_id = $meta_id;
	}

	/**
	 * Get application id
	 * 
	 * @return int
	**/
	public function getapplicationId() {
		return $this->application_id;
	}

	/**
	 * Set application id
	 * 
	 * @param int $id
	**/
	public function setapplicationId( $id ) {
		$this->application_id = $id;
	}

	/**
	 * Get meta key
	 * 
	 * @return string
	**/
	public function getMetaKey() {
		return $this->meta_key;
	}

	/**
	 * Set meta key
	 * 
	 * @param string $meta_key
	**/
	public function setMetaKey( $meta_key ) {
		$this->meta_key = $meta_key;
	}

	/**
	 * Get meta value
	 * 
	 *  @return mixed
	**/
	public function getMetaValue() {
		return $this->meta_value;
	}

	/**
	 * Set meta value
	 * 
	 * @param mixed $meta_value
	**/
	public function setMetaValue( $meta_value ) {
		$this->meta_value = $meta_value;
	}
}
