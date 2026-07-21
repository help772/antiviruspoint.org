<?php

namespace LicenseManagerForWooCommerce\Models\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Interfaces\Model as ModelInterface;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;
use stdClass;

/**
 * Class Application
 *
 * @package LicenseManagerForWooCommercePro\Database\Models\Resources
 */
class Application extends AbstractResourceModel implements ModelInterface {


	/**
	 * ID
	 * 
	 * @var int
	**/
	protected $id;

	/**
	 * Name
	 * 
	 * @var string
	**/
	protected $name;

	/**
	 * Type
	 * 
	 * @var string
	**/
	protected $type;

	/**
	 * Stable release id
	 * 
	 * @var int
	**/
	protected $stable_release_id;

	/**
	 * Description
	 * 
	 * @var string
	**/
	protected $description;

	/**
	 * Documentation
	 * 
	 * @var string
	**/
	protected $documentation;

	/**
	 * Support
	 * 
	 * @var string
	**/
	protected $support;

	/**
	 * Gallery
	 * 
	 * @var array
	**/
	protected $gallery;

	/**
	 * Create at
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
	 * Application constructor.
	 *
	 * @param $application
	**/
	public function __construct( $application ) {
		if ( ! $application instanceof stdClass ) {
			return;
		}
		
		$this->id                = $application->id;
		$this->name              = wp_unslash( $application->name );
		$this->type              = $application->type;
		$this->stable_release_id = $application->stable_release_id;
		$this->description       = wp_unslash( $application->description );
		$this->documentation     = wp_unslash( $application->documentation );
		$this->support           = wp_unslash( $application->support );
		$this->gallery           = json_decode($application->gallery, true);
		$this->created_at        = $application->created_at;
		$this->created_by        = $application->created_by;
		$this->updated_at        = $application->updated_at;
		$this->updated_by        = $application->updated_by;

		// Add gallery item url.
		if ( is_array( $this->gallery ) ) {
			foreach ( $this->gallery as $key => $val ) {
				if ( isset( $val['id'] ) ) {
					$this->gallery[ $key ]['url'] = wp_get_attachment_image_url( $val['id'], 'full' );
					foreach ( $this->gallery[ $key ] as $prop => $value ) {
						$this->gallery[ $key ][ $prop ] = wp_unslash( $value );
					}
				}
			}
		}
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
	 * Get name
	 * 
	 * @return string
	**/
	public function getName() {
		return $this->name;
	}

	/**
	 * Set name
	 * 
	 * @param string $name
	**/
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * Get type
	 * 
	 * @return string
	**/
	public function getType() {
		return $this->type;
	}

	/**
	 * Set type
	 * 
	 * @param string $type
	**/
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * Get stable release id
	 * 
	 * @return int
	**/
	public function getStableReleaseId() {

		return $this->stable_release_id;
	}

	/**
	 * Return stable release
	 *
	 * @return bool|AbstractResourceModel|ApplicationReleaseResourceModel
	**/
	public function getStableRelease() {
		
		if ( empty( $this->stable_release_id ) ) {
			return null;
		}

		return ApplicationReleaseResourceRepository::instance()->find( $this->stable_release_id );
	}

	/**
	 * Set stable release id
	 * 
	 * @param int $stable_release_id
	**/
	public function setStableReleaseId( $stable_release_id ) {
		$this->stable_release_id = $stable_release_id;
	}

	/**
	 * Get description
	 * 
	 * @return string
	**/
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set description
	 * 
	 * @param string $description
	**/
	public function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * Get documentation
	 * 
	 * @return string
	**/
	public function getDocumentation() {
		return $this->documentation;
	}

	/**
	 * Set documentation
	 * 
	 * @param string $documentation
	**/
	public function setDocumentation( $documentation ) {
		$this->documentation = $documentation;
	}

	/**
	 * Get support
	 * 
	 * @return string
	**/
	public function getSupport() {
		return $this->support;
	}

	/**
	 * Set support
	 * 
	 * @param string $support
	**/
	public function setSupport( $support ) {
		$this->support = $support;
	}

	/**
	 * Get gallery
	 * 
	 * @return array
	**/
	public function getGallery() {
		return $this->gallery;
	}

	/**
	 * Set gallery
	 * 
	 * @param array $gallery
	**/
	public function setGallery( array $gallery ) {
		$this->gallery = $gallery;
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
}
