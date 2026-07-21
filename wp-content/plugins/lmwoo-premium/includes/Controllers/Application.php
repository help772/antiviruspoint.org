<?php


namespace LicenseManagerForWooCommerce\Controllers;

use LicenseManagerForWooCommerce\AdminNotice;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationMeta as ApplicationMetaResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\Application as ApplicationResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationReleaseMeta as ApplicationReleaseMetaResourceRepository;
use LicenseManagerForWooCommerce\AdminMenus;
use LicenseManagerForWooCommerce\Enums\ApplicationType;
use LicenseManagerForWooCommerce\ApplicationManager;
use WP_Error;
/**
 * Class Application
 *
 * @package LicenseManagerForWooCommerce\Controllers
 */
class Application {


	/**
	 * Application constructor.
	 */
	public function __construct() {
		
		add_action( 'admin_post_lmfwc_save_application', array( $this, 'saveApplication' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'addAdminScripts' ) );
		add_action( 'wp_ajax_lmfwc_application_update', array( $this, 'updateApplication' ) );
		add_action( 'wp_ajax_lmfwc_release_save', array( $this, 'saveRelease' ) );
		add_action( 'wp_ajax_lmfwc_release_delete', array( $this, 'deleteRelease' ) );
		add_action( 'wp_ajax_lmfwc_release_index', array( $this, 'indexReleases' ) );
	}

	/**
	 * Enqueues admin scripts
	 */
	public function addAdminScripts() {

		if ( ! $this->isEdit() ) {
			return;
		}

		/**
		 * Enqueue media files
		 */
		wp_enqueue_media();
		wp_enqueue_script( 'lmfwc_pro_vue', LMFWC_JS_URL . '/vue.min.js', array(), LMFWC_VERSION, false );
		wp_enqueue_style( 'lmfwc_pro_editor', LMFWC_CSS_URL . '/editor.css', array(), LMFWC_VERSION );

		/**
		 * Prepare application releases
		 */
		$applicationId = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : null;
		$application   = ApplicationResourceRepository::instance()->find( $applicationId );
		$application   = ! empty( $application ) ? $application->toArray() : array();
		$releases   = $this->getReleases( $applicationId );

		/**
		 * Prepare types and release meta fields
		 */
		$application_fields     = ApplicationType::applicationFields();
		$application_types      = array();
		$release_meta_fields = array();
		$release_fields      = ApplicationType::releaseFields();
		foreach ( ApplicationType::releaseTypes() as $key => $type ) {
			$application_types[ $key ]      = isset( $type['name'] ) ? $type['name'] : __( 'Unknown type', 'license-manager-for-woocommerce' );
			$release_meta_fields[ $key ] = array();
			if ( isset( $type['fields'] ) && is_array( $type['fields'] ) ) {
				foreach ( $type['fields'] as $field ) {
					array_push( $release_meta_fields[ $key ], $field );
				}
			}
		}
		
		$ajax_url = admin_url( 'admin-ajax.php' );

		/**
		 * Localize the script and prepare vue.js data
		 */
		wp_localize_script( 'lmfwc_pro_vue', 'LMFWC_PRO_EDITOR', array(
				'application' => array(
					'current' => $application,
					'fields'  => $application_fields,
				),
				'release'  => array(
					'list'        => $releases,
					'fields'      => $release_fields,
					'meta_fields' => $release_meta_fields,
				),
				'strings'  => array(
					'required'          => __( 'Required.', 'license-manager-for-woocommerce' ),
					'save'              => __( 'Save', 'license-manager-for-woocommerce' ),
					'version'           => __( 'Version', 'license-manager-for-woocommerce' ),
					'date'              => __( 'Date', 'license-manager-for-woocommerce' ),
					'title_releases'    => __( 'Application Releases', 'license-manager-for-woocommerce' ),
					'title_application'    => __( 'Application Details', 'license-manager-for-woocommerce' ),
					'no_releases_found' => __( 'No releases found', 'license-manager-for-woocommerce' ),
					'no_application_found' => __( 'Application not found', 'license-manager-for-woocommerce' ),
					'file'              => __( 'File', 'license-manager-for-woocommerce' ),
					//'title'             => __( 'Title', 'license-manager-for-woocommerce' ),
					'description'       => __( 'Description', 'license-manager-for-woocommerce' ),
					'upload'            => __( 'Upload', 'license-manager-for-woocommerce' ),
					'add_release'       => __( 'Add', 'license-manager-for-woocommerce' ),
					'new_release'       => __( 'New Release', 'license-manager-for-woocommerce' ),
					/* translators: %s: Release ID. */
					'edit_release'      => __( 'Edit Release %s', 'license-manager-for-woocommerce' ),
					'update'            => __( 'Update', 'license-manager-for-woocommerce' ),
					'create'            => __( 'Create', 'license-manager-for-woocommerce' ),
					'none'              => __( 'None', 'license-manager-for-woocommerce' ),
					'confirm_deletion'  => __( 'Are you sure you want to delete this record? This action is not reversible.', 'license-manager-for-woocommerce' ),
				),
				'types'    => $application_types,
				'urls'     => array(
					'release_index'       => add_query_arg( array( 'action' => 'lmfwc_release_index' ), $ajax_url ),
					'release_save'        => add_query_arg( array( 'action' => 'lmfwc_release_save', '_wpnonce' => wp_create_nonce('lmfwc_save_release') ), $ajax_url ),
					'release_delete'      => add_query_arg( array( 'action' => 'lmfwc_release_delete' ), $ajax_url ),
					'release_file_delete' => add_query_arg( array( 'action' => 'lmfwc_release_file_delete' ), $ajax_url ),
					'application_update'     => add_query_arg( array( 'action' => 'lmfwc_application_update' ), $ajax_url ),
				),
			)
		);
	}

	/**
	 * Save application item in the database
	 */
	public function saveApplication() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Permission Denied.', 'license-manager-for-woocommerce' ) ),
			) );
		}

		// Verify the nonce.
		check_admin_referer( 'lmfwc_save_application' );
		$data  = $_POST;
		// Validate request.
		if ( empty( $data['name'] ) || ! is_string( $data['name'] ) ) {
			return new WP_Error( 'data_error', __( 'Application name is missing.', 'license-manager-for-woocommerce' ), array(
				'code'  => '422',
				'field' => 'name',
			) );
		}

		if ( empty( $data['type'] ) || ! is_string( $data['type'] ) ) {
			return new WP_Error( 'data_error', __( 'The type is invalid.', 'license-manager-for-woocommerce' ), array(
				'code'  => '422',
				'field' => 'type',
			) );
		}

		// Save the item.
		$args = array(
			'name'              => $data['name'],
			'type'              => $data['type'],
			'stable_release_id' => ! empty( $data['stable_release_id'] ) ? absint( $data['stable_release_id'] ) : null,
			'description'       => ! empty( $data['description'] ) ? (string) $data['description'] : null,
			'documentation'     => ! empty( $data['documentation'] ) ? (string) $data['documentation'] : null,
			'support'           => ! empty ( $data['support'] ) ? (string) $data['support'] : null,
			'gallery'           => ! empty ( $data['gallery'] ) ? $data['gallery'] : null,
		);
				$application = ApplicationResourceRepository::instance()->insert( $args );
	 
		if ( is_wp_error( $application ) ) {
			if ( 'data_error' === $application->get_error_code() ) {
				AdminNotice::error( $application->get_error_message() );
				wp_safe_redirect( admin_url( sprintf( '%s&page=%s&action=add', AdminMenus::PRODUCT_PAGE, AdminMenus::APPLICATIONS_PAGE ) ) );
				exit();
			} else {
				AdminNotice::error( __( 'There was a problem adding the application item.', 'license-manager-for-woocommerce' ) );
				wp_safe_redirect( admin_url( sprintf( '%s&page=%s&action=edit&id=%s', AdminMenus::PRODUCT_PAGE, AdminMenus::APPLICATIONS_PAGE, $application->getId() ) ) );
				exit();
			}
		} else {
			AdminNotice::success( __( 'The application item was added successfully.', 'license-manager-for-woocommerce' ) );
			wp_safe_redirect( admin_url( sprintf( '%s&page=%s&action=%s&id=%s&_wpnonce=%s', AdminMenus::PRODUCT_PAGE, AdminMenus::APPLICATIONS_PAGE, 'edit', absint( $application->getId() ), wp_create_nonce( 'edit' ) ) ) );
			exit();
		}
	}

	/**
	 * Update application item in the database
	 */
	public function updateApplication() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Permission Denied.', 'license-manager-for-woocommerce' ) ),
			) );
		}
		$request = $_REQUEST;
		$id   = isset( $request['id'] ) ? absint( $request['id'] ) : '';
		$keys = array(
			'name'              => null,
			'type'              => null,
			'stable_release_id' => 'stable_release_id',
			'description'       => null,
			'documentation'     => null,
			'support'           => null,
			'gallery'           => null,
		);

		// Collect data.
		$data = array();
		foreach ( $keys as $key => $new_key_name ) {
			if ( isset( $request[ $key ] ) ) {
				$key_name          = ! empty( $new_key_name ) ? $new_key_name : $key;
				$data[ $key_name ] = $request[ $key ];
			}
		}

		if ( empty( $data['name'] ) ) {
			wp_send_json_error( array(
					'errors' => array( $this->formatError( __( 'Application name is required', 'license-manager-for-woocommerce' ) ) ),
				) );
		}
		
		if ( ! empty( $data['gallery'] ) ) {

			$data['gallery'] = $this->sanitizeGallery( $data['gallery'] );

		} else {

			$data['gallery'] = null;
			
		}
		

		$item = ApplicationResourceRepository::instance()->update( $id, $data );

		if ( is_wp_error( $item ) ) {
			if ( 'data_error' === $item->get_error_code() ) {
				wp_send_json_error( array(
					'errors' => array( $this->formatError( $item ) ),
				) );
			} else {
				wp_send_json_error( array(
					'errors' => array( $this->formatError( __( 'There was a problem updating the application item.', 'license-manager-for-woocommerce' ) ) ),
				) );
			}
		} else {
			wp_send_json_success( array(
				'message' => __( 'The application item was updated successfully.', 'license-manager-for-woocommerce' ),
			) );
		}
	}

	/**
	 * Index releases
	 */
	public function indexReleases() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Permission Denied.', 'license-manager-for-woocommerce' ) ),
			) );
		}
		$request = $_REQUEST;
		$applicationId = ! empty( $request['application_id'] ) ? intval( $request['application_id'] ) : 0;
		if ( ! $applicationId ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Invalid application.', 'license-manager-for-woocommerce' ) ),
			) );
		}

		wp_send_json_success( array(
			'records' => $this->getReleases( $applicationId ),
		) );
		exit;
	}

	/**
	 * Create or update application release in the database
	 */
	public function saveRelease() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Permission Denied.', 'license-manager-for-woocommerce' ) ),
			) );
		}
		check_admin_referer( 'lmfwc_save_release' );
		$request = $_REQUEST;
		$release_id = 'new';
		if ( isset( $request['id'] ) ) {
			if ( is_numeric( $request['id'] ) ) {
				$release_id = intval( $request['id'] );
			}
		}
		$release = 'new' === $release_id ? null : ApplicationReleaseResourceRepository::instance()->find( $release_id );

		/**
		 * Validate the release entry
		 */
		if ( empty( $release_id ) || ( 'new' !== $release_id && empty( $release ) ) ) {
			wp_send_json_error( array(
				'errors' => array( $this->formatError( __( 'Invalid release.', 'license-manager-for-woocommerce' ) ) ),
			) );
			exit;
		}

		/**
		 * Validate the application entry
		 */
		$application     = isset( $request['application_id'] ) ? ApplicationResourceRepository::instance()->find( absint( $request['application_id'] ) ) : null;
		$release_type = $application->getType();
		if ( empty( $application ) || empty( $release_type ) ) {
			wp_send_json_error( array(
				'errors' => array( $this->formatError( __( 'Please select application type first in order to add release.', 'license-manager-for-woocommerce' ) ) ),
			) );
			exit;
		}

		/**
		 * Gather info about the files
		 */
		$release_types       = ApplicationType::releaseTypes();
		$release_root_fields = ApplicationType::releaseFields();
		$release_file_fields = array();
		$release_form_fields = array();
		$release_meta_fields = isset( $release_types[ $release_type ]['fields'] ) ? (array) $release_types[ $release_type ]['fields'] : array();
		foreach ( $release_root_fields as $field ) {
			if ( ! isset( $field['type'] ) ) {
				continue;
			}
			if ( 'file' === $field['type'] ) {
				array_push( $release_file_fields, $field );
			} else {
				array_push( $release_form_fields, $field );
			}
		}

		/**
		 * Process post root data from the form fields
		 *
		 * @TODO: Once we implement storage type per release, the download_type
		 *        has to be changed to the selected value and properly validated.
		 */
		$data   = array(
			'application_id'   => $application->getId(),
			'download_type' => 'local',
		);
		$errors = array();
		foreach ( $release_form_fields as $field ) {
			$id = isset( $field['id'] ) ? $field['id'] : null;
			if ( empty( $id ) ) {
				continue;
			} else {
				$valid = $this->validateField( $field, $request );
				if ( is_wp_error( $valid ) ) {
					array_push( $errors, $valid );
				} else {
					$data[ $id ] = $this->processField( $field, $request );
				}
			}
		}

		/**
		 * Find the file data and save for future use
		 */
		$release_file_field = null;
		$release_file_data  = null;
		// phpcs:disable
		$files = $_FILES;
		// Rest of the code processing form data without nonce verification
		// phpcs:enable		
		if ( isset( $release_file_fields[0]['id'] ) ) {
			if ( 'download_file' === $release_file_fields[0]['id'] ) {
				$release_file_field = $release_file_fields[0];
				$release_file_data  = isset( $files[ $release_file_fields[0]['id'] ] ) ? $files[ $release_file_fields[0]['id'] ] : null;
			}
		}

		/**
		 * Process post meta data from the form fields
		 */
		$meta = array();
		foreach ( $release_meta_fields as $field ) {
			$id = isset( $field['id'] ) ? $field['id'] : null;
			if ( empty( $id ) ) {
				continue;
			}
			$valid = $this->validateField( $field, $request );
			if ( is_wp_error( $valid ) ) {
				array_push( $errors, $valid );
			} else {
				$meta[ $id ] = $this->processField( $field, $request );
			}
		}

		/**
		 * Validate the release file
		 */
		$existing = ! is_null( $release ) ? $release->getDownloadFile() : null;
		$filePath = null;
		if ( empty( $existing ) && empty( $release_file_data ) ) {
			array_push( $errors, new WP_Error( 'data_error', __( 'Release file is required', 'license-manager-for-woocommerce' ), array( 'field' => 'download_file' ) ) );
		} elseif ( ! empty( $release_file_data ) ) {
			if ( ! empty( $release_file_data['tmp_name'] ) && is_uploaded_file( $release_file_data['tmp_name'] ) ) {
				$filePath = trailingslashit( get_temp_dir() ) . $release_file_data['name'];
				if ( ! move_uploaded_file( $release_file_data['tmp_name'], $filePath ) ) {
					array_push( $errors, new WP_Error( 'server_error', __( 'Unable to save uploaded file.', 'license-manager-for-woocommerce' ) ) );
					$filePath = null;
				}
			} else {
				array_push( $errors, new WP_Error( 'server_error', __( 'No release file uploaded.', 'license-manager-for-woocommerce' ) ) );
			}
		}

		/**
		 * Handle update or remove or errors and final response.
		 */
		if ( ! empty( $errors ) ) {
			$formatted = array();
			/* @var WP_Error[] $errors */
			foreach ( $errors as $error ) {
				array_push( $formatted, $this->formatError( $error ) );
			}
			wp_send_json_error( array(
				'errors' => $formatted,
			) );
		}

		/**
		 * Insert or update data
		 */
		if ( 'new' === $release_id ) {
			/* @var ApplicationReleaseResourceModel|bool $item */

			$data    = array_merge( $data, array( 'download_type' => 'unknown', 'download_file' => 1 ) );
			$release = ApplicationReleaseResourceRepository::instance()->insert( $data );

			if ( ! is_wp_error( $release ) ) {
				$message = __( 'Release created successfully.', 'license-manager-for-woocommerce' );
				$type    = 'success';
			} else {
				$message = $release->get_error_message();
				$type    = 'error';
			}
		} else {
			/* @var ApplicationReleaseResourceModel|bool $item */
			$release = ApplicationReleaseResourceRepository::instance()->update( $release_id, $data );
			if ( ! is_wp_error( $release ) ) {
				$message = __( 'Release updated successfully.', 'license-manager-for-woocommerce' );
				$type    = 'success';

			} else {
				$message = $release->get_error_message();
				$type    = 'error';
			}

		}

		$item = array();

		/**
		 * A bit more...
		 * - Process the file
		 * - Save the metadata.
		 */
		if ( ! is_wp_error( $release ) ) {

			if ( ! empty( $meta ) ) {
				foreach ( $meta as $key => $value ) {
					ApplicationReleaseMetaResourceRepository::instance()->deleteBy( array(
						'release_id' => $release->getId(),
						'meta_key'   => $key,
					) );
					ApplicationReleaseMetaResourceRepository::instance()->insert( array(
						'release_id' => $release->getId(),
						'meta_key'   => $key,
						'meta_value' => $value,
					) );
				}
			}
			$item = $release->toArray();
			$r_id = $release->getId();
			$meta = $this->formatReleaseMeta( $r_id );
			$item = array_merge( $item, $meta );

			// Process the file.
			if ( ! empty( $filePath ) ) {
				$download = $this->processReleaseFile( $release, $release_file_field, $filePath );
				if ( is_wp_error( $download ) ) {
					$type    = 'error';
					$message = $download->get_error_message();
				}
			}
		}

		if ( 'success' === $type ) {
			wp_send_json_success( array(
				'message' => $message,
				'release' => $item,
			) );
		} else {
			wp_send_json_success( array(
				'errors' => (array) $message,
			) );
		}
		exit;
	}


	/**
	 * Deletes a release from the database.
	 */
	public function deleteRelease() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Permission Denied.', 'license-manager-for-woocommerce' ) ),
			) );
		}
		$request = $_REQUEST;
		$release_id = ! empty( $request['id'] ) ? intval( $request['id'] ) : 0;

		if ( ! $release_id ) {
			wp_send_json_error( array(
				'errors' => array( __( 'Release not found.' ) ),
			) );
		}
		$result = ApplicationReleaseResourceRepository::instance()->delete( $release_id );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'errors' => (array) $result->get_error_message(),
			) );
		} else {
			wp_send_json_success( array(
				'message' => __( 'Release deleted successfully.' ),
			) );
		}
		exit;
	}

	/**
	 * Validate post field
	 *
	 * @param $field
	 * @param array $data
	 *
	 * @return bool|WP_Error
	 */
	private function validateField( $field, $data ) {

		$id       = $field['id'];
		$value    = isset( $data[ $id ] ) ? $data[ $id ] : null;
		$required = isset( $field['required'] ) && $field['required'];

		if ( ! $required ) {
			return true;
		} else if ( ! empty( $value ) ) {
			return true;
		} else {
			/* translators: %s: Field id. */
			return new WP_Error( 'data_error', sprintf( __( 'Field %s is required.', 'license-manager-for-woocommerce' ), $id ), array( 'field' => $id ) );
		}
	}

	/**
	 * Return field value
	 *
	 * @param $field
	 * @param $data
	 *
	 * @return mixed|null
	 */
	private function processField( $field, $data ) {
		$id = $field['id'];
		if ( isset( $data[ $id ] ) ) {
			return $data[ $id ];
		}

		return null;
	}

	/**
	 * Process the release
	 *
	 * @param $release
	 * @param $field
	 * @param $filePath
	 *
	 * @return array|WP_Error
	 */
	private function processReleaseFile( $release, $field, $filePath ) {

		/**
		 * Save file to local.
		 */
		$adapter    = isset( $field['storage'] ) ? $field['storage'] : 'local';
		$validation = ApplicationManager::instance()->is_adapter_valid( $adapter );

		if ( is_bool( $validation ) ) {
			if ( $validation ) {

				$release->setDownloadType( $adapter );
				$result = ApplicationManager::instance()->save( $release, $filePath );
				$f_path = ApplicationManager::instance()->get_path( $result );

				return array( 'download_type' => $adapter, 'download_file' => $f_path );
			} else {
				return new WP_Error( 'invalid_adapter', __( 'Adapter not found.' ), array( 'code' => 404 ) );
			}
		}

		return $validation;
	}

	/**
	 * Return a list of releases formatted.
	 *
	 * @param $applicationId
	 *
	 * @return array
	 */
	private function getReleases( $applicationId ) {
		$releases  = array();
		$_releases = ApplicationReleaseResourceRepository::instance()->findAllBy( array( 'application_id' => $applicationId ) );
		if ( ! empty( $_releases ) ) {
			foreach ( $_releases as $release ) {
				$r_id = $release->getId();
				$item = $release->toArray();
				$meta = $this->formatReleaseMeta( $r_id );
				$item = array_merge( $item, $meta );
				array_push( $releases, $item );
			}
		}

		return $releases;
	}

	/**
	 * Convert error to readable format
	 *
	 * @param WP_Error|string $error
	 * @param null $field
	 *
	 * @return array
	 */
	private function formatError( $error, $field = null ) {
		if ( is_wp_error( $error ) ) {
			$data  = $error->get_error_data();
			$final = array(
				'code'    => $error->get_error_code(),
				'field'   => isset( $data['field'] ) ? $data['field'] : null,
				'message' => $error->get_error_message(),
			);
		} else {
			$final = array(
				'code'    => 'data_error',
				'field'   => $field,
				'message' => $error,
			);
		}

		return $final;
	}

	/**
	 * Format release metadata
	 *
	 * @param $release_id
	 *
	 * @return array
	 */
	private function formatReleaseMeta( $release_id ) {
		/* @var ApplicationMetaResourceModel[] $meta */
		$meta = ApplicationReleaseMetaResourceRepository::instance()->findAllBy( array(
			'release_id' => $release_id,
		) );

		$formatted = array();
		foreach ( $meta as $item ) {
			$formatted[ $item->getMetaKey() ] = $item->getMetaValue();
		}

		return $formatted;
	}

	/**
	 * Is application edit page?
	 *
	 * @return bool
	 */
	private function isEdit() {
		return isset( $_GET['page'] ) && AdminMenus::APPLICATIONS_PAGE === $_GET['page'] && isset( $_GET['action'] ) && 'edit' === $_GET['action'];
	}
	/**
	 * Sanitizes the gallery data.
	 *
	 * @param $gallery
	 *
	 * @return array
	 */
	public function sanitizeGallery( $gallery ) {

		if ( ! is_array( $gallery ) ) {
			return $gallery;
		}

		foreach ( $gallery as $key => $item ) {
			if ( isset( $item['url'] ) ) {
				unset( $gallery[ $key ]['url'] ); // Avoid saving url.
			} else {
				foreach ( $item as $prop => $value ) {
					$gallery[ $key ][ $prop ] = wp_slash( $value );
				}
			}
		}

		return $gallery;
	}
}
