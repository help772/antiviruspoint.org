<?php

namespace LicenseManagerForWooCommerce\Abstracts;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;
use WP_Error;

abstract class AbstractStorageAdapter {
	/**
	 * The id of the storage driver
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The name of the storage driver
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Return the id of the storage driver
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Return the id of the storage driver
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the application release path
	 *
	 * Note: For remote stored files, first we should download in localhost, somewhere in a private dir and provide it.
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 *
	 * @return WP_Error|string
	 */
	abstract public function get_path( $release );

	/**
	 * Save file to the file system and sets the reference to point to the file
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 * @param $file - The path of the file that needs to be stored into the file system
	 *
	 * @return WP_Error|ApplicationReleaseResourceModel|AbstractResourceModel - The storage identifier. Eg. media library id
	 */
	abstract public function save( $release, $file );

	/**
	 * Deletes application release file and sets the reference to NULL.
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 *
	 * @return WP_Error|bool
	 */
	abstract public function delete( $release );

	/**
	 * Outputs the contents of the storage
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 *
	 * @return WP_Error|bool
	 */
	abstract public function stream( $release );

	/**
	 * Validates the file system
	 *
	 * @return WP_Error|bool
	 */
	abstract public function validate();

	/**
	 * Returns error
	 *
	 * @param $code
	 * @param $message
	 * @param array $status
	 *
	 * @return WP_Error
	 */
	protected function error( $code, $message, $status = array() ) {
		return new WP_Error( $code, $message, $status );
	}

	/**
	 * Ensure the parameter is always release object.
	 *
	 * @param $release
	 *
	 * @return bool|AbstractResourceModel|ApplicationReleaseResourceModel
	 */
	protected function get_release( $release ) {
		if ( is_numeric( $release ) ) {
			$release = ApplicationReleaseResourceRepository::instance()->find( $release );
		}

		return $release;
	}
}
