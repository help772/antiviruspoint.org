<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use stdClass;

defined('ABSPATH') || exit;

class LicenseMeta {

	/**
	 * Meta ID
	 *
	 * @var int
	 */
	protected $metaId;

	/**
	 * License ID
	 *
	 * @var int
	 */
	protected $licenseId;

	/**
	 * Meta Key
	 *
	 * @var string
	 */
	protected $metaKey;

	/**
	 * Meta Value
	 *
	 * @var mixed
	 */
	protected $metaValue;

	/**
	 * License constructor.
	 *
	 * @param stdClass $licenseMeta
	 */
	public function __construct( $licenseMeta ) {
		if (!$licenseMeta instanceof stdClass) {
			return;
		}

		$this->metaId    = intval($licenseMeta->meta_id);
		$this->licenseId = intval($licenseMeta->license_id);
		$this->metaKey   = $licenseMeta->meta_key;
		$this->metaValue = maybe_unserialize($licenseMeta->meta_value);
	}

	/**
	 * Return the meta id 
	 *
	 * @return int
	 */
	public function getMetaId() {
		return $this->metaId;
	}

	/**
	 * Set the meta id 
	 *
	 * @param int $metaId
	 */
	public function setMetaId( $metaId ) {
		$this->metaId = $metaId;
	}

	/**
	 * Return the license id
	 *
	 * @return int
	 */
	public function getLicenseId() {
		return $this->licenseId;
	}

	/**
	 * Set the license id
	 *
	 * @param int $licenseId
	 */
	public function setLicenseId( $licenseId ) {
		$this->licenseId = $licenseId;
	}

	/**
	 * Return the meta key
	 *
	 * @return string
	 */
	public function getMetaKey() {
		return $this->metaKey;
	}

	/**
	 * Set the meta key
	 *
	 * @param string $metaKey
	 */
	public function setMetaKey( $metaKey ) {
		$this->metaKey = $metaKey;
	}

	/**
	 * Return the meta value
	 *
	 * @return mixed
	 */
	public function getMetaValue() {
		return $this->metaValue;
	}

	/**
	 * Set the meta value
	 *
	 * @param mixed $metaValue
	 */
	public function setMetaValue( $metaValue ) {
		$this->metaValue = $metaValue;
	}
}
