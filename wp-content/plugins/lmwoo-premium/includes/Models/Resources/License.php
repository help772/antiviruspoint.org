<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use LicenseManagerForWooCommerce\Repositories\Resources\LicenseActivations ;
use stdClass;
use DateTime;
use DateTimeZone;

defined('ABSPATH') || exit;

class License extends AbstractResourceModel implements ModelInterface {

	/**
	 * License ID
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Order Id
	 *
	 * @var int
	 */
	protected $orderId;

	/**
	 * ProductId
	 *
	 * @var int
	 */
	protected $productId;

	/**
	 * UserId
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * LicenseKey
	 *
	 * @var string
	 */
	protected $licenseKey;

	/**
	 * Hash
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * ExpiresAt
	 *
	 * @var string
	 */
	protected $expiresAt;

	/**
	 * ValidFor
	 *
	 * @var int
	 */
	protected $validFor;

	/**
	 * Source
	 *
	 * @var int
	 */
	protected $source;

	/**
	 * Status
	 *
	 * @var int
	 */
	protected $status;

	/**
	 * TimesActivated
	 *
	 * @var int
	 */
	protected $timesActivated;

	/**
	 * TimesActivatedMax
	 *
	 * @var int
	 */
	protected $timesActivatedMax;

	/**
	 * CreatedAt
	 *
	 * @var string
	 */
	protected $createdAt;

	/**
	 * CreatedBy
	 *
	 * @var int
	 */
	protected $createdBy;

	/**
	 * UpdatedAt
	 *
	 * @var string
	 */
	protected $updatedAt;

	/**
	 * UpdatedBy
	 *
	 * @var int
	 */
	protected $updatedBy;

	/**
	 * License constructor.
	 *
	 * @param stdClass $license
	 */
	public function __construct( $license ) {
		if (!$license instanceof stdClass) {
			return;
		}

		$this->id                = null === $license->id          ? null : intval($license->id);
		$this->orderId           = null === $license->order_id    ? null : intval($license->order_id);
		$this->productId         = null === $license->product_id  ? null : intval($license->product_id);
		$this->userId            = null === $license->user_id     ? null : intval($license->user_id);
		$this->licenseKey        = $license->license_key;
		$this->hash              = $license->hash;
		$this->expiresAt         = $license->expires_at;
		$this->validFor          = null === $license->valid_for            ? null : intval($license->valid_for);
		$this->source            = null === $license->source              ? null : intval($license->source);
		$this->status            = null === $license->status              ? null : intval($license->status);
		$this->timesActivated    = null === $license->times_activated     ? null : intval($license->times_activated);
		$this->timesActivatedMax = null === $license->times_activated_max ? null : intval($license->times_activated_max);
		$this->createdAt         = $license->created_at;
		$this->createdBy         = null === $license->created_by          ? null : intval($license->created_by);
		$this->updatedAt         = $license->updated_at;
		$this->updatedBy         = null === $license->updated_by          ? null : intval($license->updated_by);
	}

	/**
	 * GetId
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * SetId
	 *
	 * @param int $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * GetOrderId
	 *
	 * @return int
	 */
	public function getOrderId() {
		return $this->orderId;
	}

	/**
	 * SetOrderId
	 *
	 * @param int $orderId
	 */
	public function setOrderId( $orderId ) {
		$this->orderId = $orderId;
	}

	/**
	 * GetProductId
	 *
	 * @return int
	 */
	public function getProductId() {
		return $this->productId;
	}

	/**
	 * SetProductId
	 *
	 * @param int $productId
	 */
	public function setProductId( $productId ) {
		$this->productId = $productId;
	}

	/**
	 * GetUserId
	 *
	 * @return int
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * SetUserId
	 *
	 * @param int $userId
	 */
	public function setUserId( $userId ) {
		$this->userId = $userId;
	}

	/**
	 * GetLicenseKey
	 *
	 * @return string
	 */
	public function getLicenseKey() {
		return $this->licenseKey;
	}

	/**
	 * SetLicenseKey
	 *
	 * @param string $licenseKey
	 */
	public function setLicenseKey( $licenseKey ) {
		$this->licenseKey = $licenseKey;
	}


	/**
	 * GetDecryptedLicenseKey
	 *
	 * @return string
	 */
	public function getDecryptedLicenseKey() {
		/**
		* Filter lmfwc_decrypt
		* 
		* @since 1.0
		**/
		return apply_filters('lmfwc_decrypt', $this->licenseKey);
	}

	/**
	 * GetHash
	 *
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}

	/**
	 * SetHash
	 *
	 * @param string $hash
	 */
	public function setHash( $hash ) {
		$this->hash = $hash;
	}

	/**
	 * GetExpiresAt
	 *
	 * @return string
	 */
	public function getExpiresAt() {
		return $this->expiresAt;
	}




	/**
	 * SetExpiresAt
	 *
	 * @param string $expiresAt
	 */
	public function setExpiresAt( $expiresAt ) {
		$this->expiresAt = $expiresAt;
	}

	/**
	 * GetValidFor
	 *
	 * @return int
	 */
	public function getValidFor() {
		return $this->validFor;
	}

	/**
	 * SetValidFor
	 *
	 * @param int $validFor
	 */
	public function setValidFor( $validFor ) {
		$this->validFor = $validFor;
	}

	/**
	 * GetSource
	 *
	 * @return int
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * SetSource
	 *
	 * @param int $source
	 */
	public function setSource( $source ) {
		$this->source = $source;
	}

	/**
	 * GetStatus
	 *
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * SetStatus
	 *
	 * @param int $status
	 */
	public function setStatus( $status ) {
		$this->status = $status;
	}

	/**
	 * GetTimesActivated
	 *
	 * @return int
	 */
	public function getTimesActivated() {
		return $this->timesActivated;
	}

	/**
	 * SetTimesActivated
	 *
	 * @param int $timesActivated
	 */
	public function setTimesActivated( $timesActivated ) {
		$this->timesActivated = $timesActivated;
	}

   

	/**
	 * GetTimesActivatedMax
	 *
	 * @return int
	 */
	public function getTimesActivatedMax() {
		return $this->timesActivatedMax;
	}

	/**
	 * SetTimesActivatedMax
	 *
	 * @param int $timesActivatedMax
	 */
	public function setTimesActivatedMax( $timesActivatedMax ) {
		$this->timesActivatedMax = $timesActivatedMax;
	}

	/**
	 * GetCreatedAt
	 *
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * SetCreatedAt
	 *
	 * @param string $createdAt
	 */
	public function setCreatedAt( $createdAt ) {
		$this->createdAt = $createdAt;
	}

	/**
	 * GetCreatedBy
	 *
	 * @return int
	 */
	public function getCreatedBy() {
		return $this->createdBy;
	}

	/**
	 * SetCreatedBy
	 *
	 * @param int $createdBy
	 */
	public function setCreatedBy( $createdBy ) {
		$this->createdBy = $createdBy;
	}

	/**
	 * GetUpdatedAt
	 *
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->updatedAt;
	}

	/**
	 * SetUpdatedAt
	 *
	 * @param string $updatedAt
	 */
	public function setUpdatedAt( $updatedAt ) {
		$this->updatedAt = $updatedAt;
	}

	/**
	 * GetUpdatedBy
	 *
	 * @return int
	 */
	public function getUpdatedBy() {
		return $this->updatedBy;
	}

	/**
	 * SetUpdatedBy
	 *
	 * @param int $updatedBy
	 */
	public function setUpdatedBy( $updatedBy ) {
		$this->updatedBy = $updatedBy;
	}
}
