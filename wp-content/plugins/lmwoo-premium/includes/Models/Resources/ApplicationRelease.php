<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationReleaseMeta as ApplicationReleaseMetaResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationReleaseMeta as ApplicationReleaseMetaResourceRepository;
use stdClass;


/**
 * Class ApplicationRelease
 *
 * @package LicenseManagerForWooCommercePro\Database\Models\Resources
 */
class ApplicationRelease extends AbstractResourceModel implements ModelInterface {


	/**
	 * ID
	 * 
	 * @var int
	**/
	protected $id;

	/**
	 * Application id
	 * 
	 * @var int
	**/
	protected $application_id;

	/**
	 * Version
	 * 
	 * @var string
	**/
	protected $version;

	/**
	 * Download type
	 * 
	 * @var string
	**/
	protected $download_type;

	/**
	 * Download file
	 * 
	 * @var string
	**/
	protected $download_file;

	/**
	 * Changelog
	 * 
	 * @var string
	**/
	protected $changelog;

	/**
	 * Created at
	 * 
	 * @var string
	**/
	protected $created_at;

	/**
	 * Created by
	 * 
	 * @var int
	**/
	protected $created_by;

	/**
	 * Updated at
	 * 
	 * @var string
	**/
	protected $updated_at;

	/**
	 * Updated by
	 * 
	 * @var int
	**/
	protected $updated_by;

	/**
	 * Meta data
	 * 
	 * @var array
	**/
	protected $metadata;

	/**
	 * Download
	 * 
	 * @var array
	**/
	protected $download;

	/** ApplicationRelease constructor **/
	public function __construct( $rel ) {

		if ( ! $rel instanceof stdClass ) {
			return;
		}
		$this->id            = $rel->id;
		$this->application_id   = $rel->application_id;
		$this->version       = wp_unslash( $rel->version );
		$this->download_type = wp_unslash( $rel->download_type );
		$this->download_file = wp_unslash( $rel->download_file );
		$this->changelog     = wp_unslash( $rel->changelog );
		$this->created_at    = $rel->created_at;
		$this->created_by    = $rel->created_by;
		$this->updated_at    = $rel->updated_at;
		$this->updated_by    = $rel->updated_by;

		$this->formatDownload();
	}

	/**
	 * Get id
	 * 
	 * @return int
	**/
	public function getId() {
		return $this->id;
	}

	/**
	 * Set id
	 * 
	 * @param int $id
	**/
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * Get application id
	 * 
	 * @return int
	**/
	public function getApplicationId() {
		return $this->application_id;
	}

	/**
	 * Set application id
	 * 
	 * @param int $id
	**/
	public function setApplicationId( $id ) {
		$this->application_id = $id;
	}

	/**
	 * Get version
	 * 
	 * @return string
	**/
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Set version
	 * 
	 * @param string $version
	**/
	public function setVersion( $version ) {
		$this->version = $version;
	}

	/**
	 * Get download type
	 * 
	 * @return string
	**/
	public function getDownloadType() {
		return $this->download_type;
	}

	/**
	 * Set download type
	 * 
	 * @param string $type
	**/
	public function setDownloadType( $type ) {
		$this->download_type = $type;
	}

	/**
	 * Get download file
	 * 
	 * @return string
	**/
	public function getDownloadFile() {
		return $this->download_file;
	}

	/**
	 * Set download file
	 * 
	 * @param string $file
	**/
	public function setDownloadFile( $file ) {
		$this->download_file = $file;
	}

	/**
	 * Get changelog
	 * 
	 * @return string
	**/
	public function getChangelog() {
		return $this->changelog;
	}

	/**
	 * Set changelog
	 * 
	 * @param string $changelog
	**/
	public function setChangelog( $changelog ) {
		$this->changelog = $changelog;
	}

	/**
	 * Get created at
	 * 
	 * @return string
	**/
	public function getCreatedAt() {
		return $this->created_at;
	}

	/**
	 * Set created at
	 * 
	 * @param string $created_at
	**/
	public function setCreatedAt( $created_at ) {
		$this->created_at = $created_at;
	}

	/**
	 * Get created by
	 * 
	 * @return int
	**/
	public function getCreatedBy() {
		return $this->created_by;
	}

	/**
	 * Set created by
	 * 
	 * @param int $created_by
	**/
	public function setCreatedBy( $created_by ) {
		$this->created_by = $created_by;
	}

	/**
	 * Get updated at
	 * 
	 * @return string
	**/
	public function getUpdatedAt() {
		return $this->updated_at;
	}

	/**
	 * Set updated at
	 * 
	 * @param string $updated_at
	**/
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $updated_at;
	}

	/**
	 * Get updated by
	 * 
	 * @return int
	**/
	public function getUpdatedBy() {
		return $this->updated_by;
	}

	/**
	 * Set updated by
	 * 
	 * @param int $updated_by
	**/
	public function setUpdatedBy( $updated_by ) {
		$this->updated_by = $updated_by;
	}

	/**
	 * Return the formatted date
	 *
	 * @param string $format
	 *
	 * @return string|null
	 */
	public function getCreatedAtFormatted( $format = 'system' ) {
		if ( empty( $this->getCreatedAt() ) ) {
			return null;
		}

		return $this->toFormattedDate( 'created_at', 'Y-m-d H:i:s', $format );
	}

	/**
	 * Return the metadata.
	 *
	 * @param null $key
	 *
	 * @return array|mixed|null
	 */
	public function getMeta( $key = null ) {
		$this->loadMeta();
		if ( ! is_null( $key ) ) {
			return isset( $this->metadata[ $key ] ) ? $this->metadata[ $key ] : null;
		}

		return $this->metadata;
	}

	/**
	 * Query all the metadata.
	 *
	 * @return array
	 */
	private function loadMeta() {
		if ( ! empty( $this->metadata ) ) {
			return $this->metadata;
		} else {
			$this->metadata = array();
		}

		$metadata = ApplicationReleaseMetaResourceRepository::instance()->findAllBy( array(
			'release_id' => $this->getId(),
		) );

		/* @var ApplicationReleaseMetaResourceModel $data */
		foreach ( $metadata as $data ) {
			$this->metadata[ $data->getMetaKey() ] = $data->getMetaValue();
		}

		return $this->metadata;
	}

	/**
	 * Check if download is expired.
	 *
	 * @param LicenseResourceModel $license
	 *
	 * @return bool
	 */
	public function isDownloadAllowed( $license ) {
		if ( empty( $license ) ) {
			return false;
		}
		$expiresAt = $license->getExpiresAt();
		if ( is_null( $expiresAt ) ) {
			return true;
		}
		$releasedAt = $this->getCreatedAt();

		if ( ! empty( $releasedAt ) && ! empty( $expiresAt ) ) {
			$releasedAt = strtotime( $releasedAt );
			$expiresAt  = strtotime( $expiresAt );

			return $releasedAt <= $expiresAt;
		}

		return false;
	}

	/**
	 * Format the download information
	 */
	public function formatDownload() {

		if ( ! empty( $this->download ) ) {
			return;
		}

		$download = array(
			'name'          => '',
			'size_bytes'    => 0,
			'size_readable' => '',
		);

		if ( 'local' === $this->download_type && is_numeric( $this->download_file ) ) {
			$path                      = get_attached_file( $this->download_file );
			$download['name']          = basename( $path );
			$download['size_bytes']    = filesize( $path );
			$download['size_readable'] = $download['size_bytes'] ;
		}
		/**
		 * Filter lmfwc_release_download
		 * 
		 * @since 1.0
		**/
		$this->download = apply_filters( 'lmfwc_release_download', $download, $this );
	}
}
