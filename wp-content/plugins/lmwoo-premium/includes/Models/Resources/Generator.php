<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use stdClass;

defined('ABSPATH') || exit;

class Generator extends AbstractResourceModel implements ModelInterface {

	/**
	 * ID
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Charset
	 *
	 * @var string
	 */
	protected $charset;

	/**
	 * Chunks
	 *
	 * @var int
	 */
	protected $chunks;

	/**
	 * ChunkLength
	 *
	 * @var int
	 */
	protected $chunkLength;

	/**
	 * TimesActivatedMax
	 *
	 * @var int
	 */
	protected $timesActivatedMax;

	/**
	 * Separator
	 *
	 * @var string
	 */
	protected $separator;

	/**
	 * Prefix
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Suffix
	 *
	 * @var string
	 */
	protected $suffix;

	/**
	 * ExpiresIn
	 *
	 * @var int
	 */
	protected $expiresIn;

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
	 * Generator constructor.
	 *
	 * @param stdClass $generator
	 */
	public function __construct( $generator ) {
		if (!$generator instanceof stdClass) {
			return;
		}

		$this->id                = intval($generator->id);
		$this->name              = $generator->name;
		$this->charset           = $generator->charset;
		$this->chunks            = intval($generator->chunks);
		$this->chunkLength       = intval($generator->chunk_length);
		$this->timesActivatedMax = $generator->times_activated_max;
		$this->separator         = $generator->separator;
		$this->prefix            = $generator->prefix;
		$this->suffix            = $generator->suffix;
		$this->expiresIn         = $generator->expires_in;
		$this->createdAt         = $generator->created_at;
		$this->createdBy         = $generator->created_by;
		$this->updatedAt         = $generator->updated_at;
		$this->updatedBy         = $generator->updated_by;
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
	 * GetName
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * SetName
	 *
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * GetCharset
	 *
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * SetCharset
	 *
	 * @param string $charset
	 */
	public function setCharset( $charset ) {
		$this->charset = $charset;
	}

	/**
	 * GetChunks
	 *
	 * @return int
	 */
	public function getChunks() {
		return $this->chunks;
	}

	/**
	 * SetChunks
	 *
	 * @param int $chunks
	 */
	public function setChunks( $chunks ) {
		$this->chunks = $chunks;
	}

	/**
	 * GetChunkLength
	 *
	 * @return int
	 */
	public function getChunkLength() {
		return $this->chunkLength;
	}

	/**
	 * SetChunkLength
	 *
	 * @param int $chunkLength
	 */
	public function setChunkLength( $chunkLength ) {
		$this->chunkLength = $chunkLength;
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
	 * GetSeparator
	 *
	 * @return string
	 */
	public function getSeparator() {
		return $this->separator;
	}

	/**
	 * SetSeparator
	 *
	 * @param string $separator
	 */
	public function setSeparator( $separator ) {
		$this->separator = $separator;
	}

	/**
	 * GetPrefix
	 *
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * SetPrefix
	 *
	 * @param string $prefix
	 */
	public function setPrefix( $prefix ) {
		$this->prefix = $prefix;
	}

	/** 
	 * GetSuffix
	 *
	 * @return string
	 */
	public function getSuffix() {
		return $this->suffix;
	}

	/**
	 * SetSuffix
	 *
	 * @param string $suffix
	 */
	public function setSuffix( $suffix ) {
		$this->suffix = $suffix;
	}

	/**
	 * GetExpiresIn
	 *
	 * @return int
	 */
	public function getExpiresIn() {
		return $this->expiresIn;
	}

	/**
	 * SetExpiresIn
	 *
	 * @param int $expiresIn
	 */
	public function setExpiresIn( $expiresIn ) {
		$this->expiresIn = $expiresIn;
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
