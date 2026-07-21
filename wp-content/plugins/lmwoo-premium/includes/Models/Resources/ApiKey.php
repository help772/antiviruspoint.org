<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use stdClass;

defined('ABSPATH') || exit;

class ApiKey extends AbstractResourceModel implements ModelInterface {

	/**
	 * ID
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * UserId
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Permissions
	 *
	 * @var string
	 */
	protected $permissions;

	/**
	 * ConsumerKey
	 *
	 * @var string
	 */
	protected $consumerKey;

	/**
	 * ConsumerSecret
	 *
	 * @var string
	 */
	protected $consumerSecret;

	/**
	 * Nonces
	 *
	 * @var string
	 */
	protected $nonces;

	/**
	 * TruncatedKey
	 *
	 * @var string
	 */
	protected $truncatedKey;

	/**
	 * LastAccess
	 *
	 * @var string
	 */
	protected $lastAccess;

	/**
	 * ApiKey constructor.
	 *
	 * @param stdClass|null $apiKey
	 */
	public function __construct( $apiKey = null ) {
		if (!$apiKey instanceof stdClass) {
			return;
		}

		$this->id             = $apiKey->id;
		$this->userId         = $apiKey->user_id;
		$this->description    = $apiKey->description;
		$this->permissions    = $apiKey->permissions;
		$this->consumerKey    = $apiKey->consumer_key;
		$this->consumerSecret = $apiKey->consumer_secret;
		$this->nonces         = $apiKey->nonces;
		$this->truncatedKey   = $apiKey->truncated_key;
		$this->lastAccess     = $apiKey->last_access;
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
	 * GetDescription
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * SetDescription
	 *
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * GetPermissions
	 *
	 * @return string
	 */
	public function getPermissions() {
		return $this->permissions;
	}

	/**
	 * SetPermissions
	 *
	 * @param string $permissions
	 */
	public function setPermissions( $permissions ) {
		$this->permissions = $permissions;
	}

	/**
	 * GetConsumerKey
	 *
	 * @return string
	 */
	public function getConsumerKey() {
		return $this->consumerKey;
	}

	/**
	 * SetConsumerKey
	 *
	 * @param string $consumerKey
	 */
	public function setConsumerKey( $consumerKey ) {
		$this->consumerKey = $consumerKey;
	}

	/**
	 * GetConsumerSecret
	 *
	 * @return string
	 */
	public function getConsumerSecret() {
		return $this->consumerSecret;
	}

	/**
	 * SetConsumerSecret
	 *
	 * @param string $consumerSecret
	 */
	public function setConsumerSecret( $consumerSecret ) {
		$this->consumerSecret = $consumerSecret;
	}

	/**
	 * GetNonces
	 *
	 * @return string
	 */
	public function getNonces() {
		return $this->nonces;
	}

	/**
	 * SetNonces
	 *
	 * @param string $nonces
	 */
	public function setNonces( $nonces ) {
		$this->nonces = $nonces;
	}

	/**
	 * GetTruncatedKey
	 *
	 * @return string
	 */
	public function getTruncatedKey() {
		return $this->truncatedKey;
	}

	/**
	 * SetTruncatedKey
	 *
	 * @param string $truncatedKey
	 */
	public function setTruncatedKey( $truncatedKey ) {
		$this->truncatedKey = $truncatedKey;
	}

	/**
	 * GetLastAccess
	 *
	 * @return string
	 */
	public function getLastAccess() {
		return $this->lastAccess;
	}

	/**
	 * SetLastAccess
	 *
	 * @param string $lastAccess
	 */
	public function setLastAccess( $lastAccess ) {
		$this->lastAccess = $lastAccess;
	}
}
