<?php

namespace LicenseManagerForWooCommerce;

use LicenseManagerForWooCommerce\Abstracts\ResourceModel;
use LicenseManagerForWooCommerce\Abstracts\AbstractStorageAdapter;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;
use WP_Error;

/**
 * Class Local
 *
 * @package LicenseManagerForWooCommerce\Core\Storage\Adapters
 */
class LocalAdapter extends AbstractStorageAdapter {

	/**
	 * The base path
	 *
	 * @var null
	 */
	private $base_path = null;

	/**
	 * Local constructor.
	 */
	public function __construct() {
		$ds              = DIRECTORY_SEPARATOR;
		$upload_d        = wp_upload_dir();
		$this->id        = 'local';
		$this->name      = __( 'Local file system', 'license-manager-for-woocommerce' );
		/**
		 * Filter lmfwc_local_storage_base_path
		 * 
		 * @since 1.0
		**/
		$this->base_path = apply_filters( 'lmfwc_local_storage_base_path', sprintf( '%s%s', trailingslashit( $upload_d['basedir'] ), 'lmfwc-private/' . $ds ), $upload_d );
		$this->setup();
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

		if ( is_null( $release ) || empty( $release->getDownloadFile() ) ) {
			return $this->error(
				'data_error',
				__( '404 Release Not Found.' ),
				array( 'status' => 404 )
			);
		}

		$file = get_attached_file( $release->getDownloadFile() );
		if ( file_exists( $file ) ) {
			return $file;
		}

		return null;
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

		if ( empty( $release ) ) {
			return $this->error(
				'data_error',
				__( '404 Release Not Found.' ),
				array( 'status' => 404 )
			);
		}

		if ( ! file_exists( $file ) ) {
			return $this->error( 'server_error', __( 'Source location does not exist.', 'license-manager-for-woocommerce' ) );
		}
		$valid = $this->validate();
		if ( is_wp_error( $valid ) ) {
			return $valid;
		}

		$ext  = pathinfo( $file, PATHINFO_EXTENSION );
		$hash = substr( md5( $file . time() . wp_rand( 100000, 1000000 ) ), 0, 12 );
		$name = sprintf( '%s-%s', pathinfo( $file, PATHINFO_FILENAME ), $hash );
		if ( ! empty( $ext ) ) {
			$path = sprintf( '%s%s.%s', $this->base_path, $name, strtolower( $ext ) );
		} else {
			$path = sprintf( '%s%s', $this->base_path, $name );
		}

		if ( rename( $file, $path ) ) {
			$info = wp_check_filetype( $file );

			if ( ! empty( $info['type'] ) ) {
				$attachment   = array(
					'post_mime_type' => $info['type'],
					'post_title'     => basename( $file ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				);
				$attachmentId = wp_insert_attachment( $attachment, $path );
				
				if ( is_wp_error( $attachmentId ) ) {
					unlink( $file );

					return $attachmentId;
				}
				$metadata = wp_generate_attachment_metadata( $attachmentId, $path );
				wp_update_attachment_metadata( $attachmentId, $metadata );
				update_post_meta( $attachmentId, 'lmfwc_file', '1' );

				return ApplicationReleaseResourceRepository::instance()->update( $release->getId(), array(
					'download_type' => $this->getId(),
					'download_file' => $attachmentId,
				) );
			} else {
				unlink( $file );

				return $this->error( 'server_error', __( 'Extension not allowed.', 'license-manager-for-woocommerce' ) );
			}
		} else {
			return $this->error( 'server_error', __( 'Unable to save file to the storage dir' ) );
		}
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

		if ( is_null( $release ) || empty( $release->getDownloadFile() ) ) {
			return $this->error(
				'data_error',
				__( '404 Release Not Found.' ),
				array( 'status' => 404 )
			);
		}

		$file_id = $release->getDownloadFile();

		$attachment = get_post( $file_id );
		if ( $attachment ) {
			if ( wp_delete_attachment( $attachment->ID, true ) ) {
				ApplicationReleaseResourceRepository::instance()->update( $release->getId(), array(
					'download_file' => null,
				) );

				return true;
			}
		}

		return $this->error( 'server_error', __( 'Unable to delete local file' ), array( 'status' => 404 ) );
	}

	/**
	 * Outputs the contents of the storage
	 *
	 * @param int|ApplicationReleaseResourceModel $release - The id or the release object
	 *
	 * @return WP_Error|bool
	 */
	public function stream( $release ) {

		if ( is_null( $release ) || empty( $release->getDownloadFile() ) ) {
			return $this->error(
				'data_error',
				'No application releases found for this product.',
				array( 'status' => 404 )
			);
		}

		$releasePath = get_attached_file( $release->getDownloadFile() );

		if ( empty( $releasePath ) || ! file_exists( $releasePath ) ) {
			return $this->error(
				'data_error',
				'Requested file not found.',
				array( 'status' => 404 )
			);
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . basename( $releasePath ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $releasePath ) );
		ob_clean();
		flush();
		readfile( $releasePath );

		return true;
	}

	/**
	 * Is the filesystem valid
	 *
	 * @return bool|WP_Error
	 */
	public function validate() {
		if ( ! file_exists( $this->base_path ) ) {
			/* translators: %s: Base path. */
			return $this->error( 'server_error', sprintf( __( 'Main storage path %s does not exist', 'license-manager-for-woocommerce' ), $this->base_path ) );
		}
		if ( ! is_writable( $this->base_path ) ) {
			/* translators: %s: Base path. */
			return $this->error( 'server_error', sprintf( __( 'Main storage path %s is not writable', 'license-manager-for-woocommerce' ), $this->base_path ) );
		}

		return true;
	}

	/**
	 * Setup the file system
	 */
	private function setup() {
		$ds = DIRECTORY_SEPARATOR;
		if ( ! file_exists( $this->base_path ) ) {
			wp_mkdir_p( $this->base_path );
		} else if ( is_file( $this->base_path ) ) {
			unlink( $this->base_path );
			wp_mkdir_p( $this->base_path );
		}
		if ( true === $this->validate() ) {
			$index_path    = sprintf( '%s%s%s', $this->base_path, $ds, 'index.php' );
			$htaccess_path = sprintf( '%s%s%s', $this->base_path, $ds, '.htaccess' );
			if ( ! file_exists( $index_path ) ) {
				file_put_contents( $index_path, 'Permission denied' . PHP_EOL );
			}
			if ( ! file_exists( $htaccess_path ) ) {
				file_put_contents( $htaccess_path, 'deny from all' . PHP_EOL );
			}
		}
	}
}
