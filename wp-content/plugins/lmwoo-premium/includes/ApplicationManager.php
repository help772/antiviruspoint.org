<?php

namespace LicenseManagerForWooCommerce;

use LicenseManagerForWooCommerce\LocalAdapter;
use LicenseManagerForWooCommerce\Abstracts\AbstractResourceModel;
use LicenseManagerForWooCommerce\Abstracts\Singleton;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;
use WP_Error;

use LicenseManagerForWooCommerce\Abstracts\AbstractStorageAdapter;

/**
 * Class Manager
 *
 * @package LicenseManagerForWooCommerce\Core\Storage
 */
class ApplicationManager extends Singleton {

	/**
	 * The storage adapters
	 *
	 * @var AbstractStorageAdapter[]
	 */
	protected $adapters = array();

	/**
	 * Storage constructor.
	 */
	public function __construct() {
		$this->add_adapter( new LocalAdapter() );
		/**
		 * Filter lmfwc_storage_adapters
		 * 
		 * @since 1.0
		**/
		$this->adapters = apply_filters( 'lmfwc_storage_adapters', $this->adapters, $this );
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
	public function get_path( $release ) {

		$release = $this->get_release( $release );
		$adapter = $this->get_adapter( $release );

		if ( is_wp_error( $adapter ) ) {
			return $adapter;
		}

		return $adapter->get_path( $release );
	}

	/**
	 * Save file to the file system and sets the reference to point to the file
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 * @param $file - The path of the file that needs to be stored into the file system
	 *
	 * @return WP_Error|ApplicationReleaseResourceModel|AbstractResourceModel - The storage identifier. Eg. media library id
	 */
	public function save( $release, $file ) {

		$release = $this->get_release( $release );
		$adapter = $this->get_adapter( $release );

		if ( is_wp_error( $adapter ) ) {
			return $adapter;
		}

		return $adapter->save( $release, $file );
	}

	/**
	 * Deletes application release file and sets the reference to NULL.
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 *
	 * @return WP_Error|bool
	 */
	public function delete( $release ) {

		$release = $this->get_release( $release );
		$adapter = $this->get_adapter( $release );

		if ( is_wp_error( $adapter ) ) {
			return $adapter;
		}

		return $adapter->delete( $release );
	}

	/**
	 * Outputs the contents of the storage
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 *
	 * @return WP_Error|bool
	 */
	public function stream( $release ) {
		$release = $this->get_release( $release );
		$adapter = $this->get_adapter( $release );

		if ( is_wp_error( $adapter ) ) {
			return $adapter;
		}

		return $adapter->stream( $release );
	}

	/**
	 * Is the adapter valid?
	 *
	 * @param $adapterId
	 *
	 * @return bool|WP_Error
	 */
	public function is_adapter_valid( $adapterId ) {
		foreach ( $this->adapters as $adapter ) {
			if ( $adapter->getId() == $adapterId ) {
				return $adapter->validate();
			}
		}

		return false;
	}

	/**
	 * Add storage adapter
	 *
	 * @param AbstractStorageAdapter $adapter
	 */
	protected function add_adapter( $adapter ) {
		$this->adapters[] = $adapter;
	}

	/**
	 * Return the storage adapter by release
	 *
	 * @param ApplicationReleaseResourceModel $release
	 *
	 * @return WP_Error|bool
	 */
	protected function get_adapter( $release ) {

		$release = $this->get_release( $release );

		if ( is_wp_error( $release ) ) {
			return $release;
		}

		foreach ( $this->adapters as $adapter ) {
			if ( $adapter->getId() == $release->getDownloadType() ) {
				return $adapter;
			}
		}
		/* translators: %s: Download Type. */
		return $this->error( 'not_found', sprintf( __( 'Adapter %s not found for application release.', 'license-manager-for-woocommerce' ), $release->getDownloadType() ) );
	}

	/**
	 * Returns the application release
	 *
	 * @param int|ApplicationReleaseResourceModel $release
	 *
	 * @return bool|AbstractResourceModel|ApplicationReleaseResourceModel|int|WP_Error
	 */
	protected function get_release( $release ) {
		if ( is_numeric( $release ) ) {
			$release = ApplicationReleaseResourceRepository::instance()->find( $release );
		}

		if ( empty( $release ) ) {
			return $this->error( 'not_found', __( 'Release not found.', 'license-manager-for-woocommerce' ) );
		}

		return $release;
	}

	/**
	 * Returns error
	 *
	 * @param $code
	 * @param $message
	 *
	 * @return WP_Error
	 */
	protected function error( $code, $message ) {
		return new WP_Error( $code, $message, array( 'code' => 404 ) );
	}
}
