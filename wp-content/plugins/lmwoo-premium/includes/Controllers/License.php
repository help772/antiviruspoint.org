<?php

namespace LicenseManagerForWooCommerce\Controllers;

use Exception;
use LicenseManagerForWooCommerce\AdminMenus;
use LicenseManagerForWooCommerce\AdminNotice;
use LicenseManagerForWooCommerce\Enums\LicenseSource;
use LicenseManagerForWooCommerce\Enums\LicenseStatus as LicenseStatusEnum;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;

defined('ABSPATH') || exit;

class License {

	/**
	 * License constructor.
	 */
	public function __construct() {
		// Admin POST requests
		add_action('admin_post_lmfwc_import_license_keys', array( $this, 'importLicenseKeys' ), 10);
		add_action('admin_post_lmfwc_add_license_key', array( $this, 'addLicenseKey' ), 10);
		add_action('admin_post_lmfwc_update_license_key', array( $this, 'updateLicenseKey' ), 10);

		// AJAX calls
		add_action('wp_ajax_lmfwc_show_license_key', array( $this, 'showLicenseKey' ), 10);
		add_action('wp_ajax_lmfwc_show_all_license_keys', array( $this, 'showAllLicenseKeys' ), 10);
	}

	/**
	 * Import licenses from a compatible CSV or TXT file into the database.
	 */
	public function importLicenseKeys() {
		$lmfwc_data = $_REQUEST;
		// Check the nonce.
		check_admin_referer('lmfwc_import_license_keys');

		$orderId     = null;
		$productId   = null;
		$userId      = null;
		$status      = LicenseStatusEnum::ACTIVE;
		$source      = $lmfwc_data['source'];
		$licenseKeys = array();

		if (array_key_exists('order_id', $lmfwc_data) && $lmfwc_data['order_id']) {
			$orderId = $lmfwc_data['order_id'];
		}

		if (array_key_exists('product_id', $lmfwc_data) && $lmfwc_data['product_id']) {
			$productId = $lmfwc_data['product_id'];
		}

		if (array_key_exists('user_id', $lmfwc_data) && $lmfwc_data['user_id']) {
			$userId = $lmfwc_data['user_id'];
		}

		if (array_key_exists('status', $lmfwc_data)
			&& $lmfwc_data['status']
			&& in_array($lmfwc_data['status'], LicenseStatusEnum::$status)
		) {
			$status = intval($lmfwc_data['status']);
		}

		if ( 'file' === $source  ) {
			/**
			* Filter lmfwc_import_license_keys_file
			* 
			* @since 1.0
			**/
			$licenseKeys = apply_filters('lmfwc_import_license_keys_file', null);
		} elseif ('clipboard'  === $source) {
			/**
			* Filter lmfwc_import_license_keys_clipboard
			* 
			* @since 1.0
			**/
			$licenseKeys = apply_filters('lmfwc_import_license_keys_clipboard', $lmfwc_data['clipboard']);
		}

		if (!is_array($licenseKeys) || count($licenseKeys) === 0) {
			AdminNotice::error(__('There was a problem importing the license keys.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
			exit();
		}

		
		try {
			/**
			* Filter lmfwc_insert_imported_license_keys
			* 
			* @since 1.0
			**/
			$result = apply_filters(
				'lmfwc_insert_imported_license_keys',
				$licenseKeys,
				$status,
				$orderId,
				$productId,
				$userId,
				$lmfwc_data['valid_for'],
				$lmfwc_data['times_activated_max']
			);
		} catch (Exception $e) {
			AdminNotice::error(__($e->getMessage(), 'license-manager-for-woocommerce'));
			wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
			exit();
		}

		// Redirect according to $result.
		if (  0 == $result['failed']  && 0 == $result['added']  ) {
			AdminNotice::error(__('There was a problem importing the license keys.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
			exit();
		}

		if ( 0  == $result['failed']  && $result['added'] > 0) {
			// Update the stock
			if ( LicenseStatusEnum::ACTIVE ===  $status ) {
				/**
				* Filter lmfwc_stock_increase
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_stock_increase', $productId, $result['added']);
			}

			// Display a success message
			AdminNotice::success(
				sprintf(
					/* translators: %d is the number of keys*/
					__('%d license key(s) added successfully.', 'license-manager-for-woocommerce'),
					intval($result['added'])
				)
			);
			wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
			exit();
		}

		if ($result['failed'] > 0 && 0 ==  $result['added'] ) {
			AdminNotice::error(__('There was a problem importing the license keys.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
			exit();
		}

		if ($result['failed'] > 0 && $result['added'] > 0) {
			// Update the stock
			if ( LicenseStatusEnum::ACTIVE === $status  ) {
				/**
				* Filter lmfwc_stock_increase
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_stock_increase', $productId, $result['added']);
			}

			// Display a warning message
			AdminNotice::warning(
				sprintf(
					/* translators: %1$d is the number of added key %2$d were failed */
					__('%1$d key(s) have been imported, while %2$d key(s) were not imported.', 'license-manager-for-woocommerce'),
					intval($result['added']),
					intval($result['failed'])
				)
			);
			wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
			exit();
		}
	}

	/**
	 * Add a single license key to the database.
	 */
	public function addLicenseKey() {
		// Check the nonce
		$lmfwc_data = $_REQUEST;
		check_admin_referer('lmfwc_add_license_key');

		$status            = absint($lmfwc_data['status']);
		$orderId           = null;
		$productId         = null;
		$userId            = null;
		$validFor          = null;
		$expiresAt         = null;
		$timesActivatedMax = null;

		if (array_key_exists('order_id', $lmfwc_data) && $lmfwc_data['order_id']) {
			$orderId = $lmfwc_data['order_id'];
		}

		if (array_key_exists('product_id', $lmfwc_data) && $lmfwc_data['product_id']) {
			$productId = $lmfwc_data['product_id'];
		}

		if (array_key_exists('user_id', $lmfwc_data) && $lmfwc_data['user_id']) {
			$userId = $lmfwc_data['user_id'];
		}

		if (array_key_exists('valid_for', $lmfwc_data) && $lmfwc_data['valid_for']) {
			$validFor  = $lmfwc_data['valid_for'];
			$expiresAt = null;
		}

		if (array_key_exists('expires_at', $lmfwc_data) && $lmfwc_data['expires_at']) {
			$validFor  = null;
			$expiresAt = $lmfwc_data['expires_at'];
		}

		if (array_key_exists('times_activated_max', $lmfwc_data) && $lmfwc_data['times_activated_max']) {
			$timesActivatedMax = absint($lmfwc_data['times_activated_max']);
		}
		/**
		* Filter lmfwc_duplicate
		* 
		* @since 1.0
		**/
		if (apply_filters('lmfwc_duplicate', $lmfwc_data['license_key'])) {
			AdminNotice::error(__('The license key already exists.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(sprintf('admin.php?page=%s&action=add', AdminMenus::LICENSES_PAGE));
			exit;
		}

		// empty license key
		if(empty($_POST['license_key'])){
            AdminNotice::error(__('The license key field is empty.', 'license-manager-for-woocommerce'));
            wp_safe_redirect(sprintf('admin.php?page=%s&action=add', AdminMenus::LICENSES_PAGE));
            exit;
        };

		/**
		 *  LicenseResourceRepository insert license
		 * 
		 * @var LicenseResourceRepository $license 
		**/
		$license = LicenseResourceRepository::instance()->insert(
			array(
				'order_id'            => $orderId,
				'product_id'          => $productId,
				'user_id'             => $userId,
				/**
				* Filter lmfwc_encrypt
				* 
				* @since 1.0
				**/
				'license_key'         => apply_filters('lmfwc_encrypt', $lmfwc_data['license_key']),
				/**
				* Filter lmfwc_hash
				* 
				* @since 1.0
				**/
				'hash'                => apply_filters('lmfwc_hash', $lmfwc_data['license_key']),
				'expires_at'          => $expiresAt,
				'valid_for'           => $validFor,
				'source'              => LicenseSource::IMPORT,
				'status'              => $status,
				'times_activated_max' => $timesActivatedMax,
			)
		);

		// Redirect with message
		if ($license) {

			if ( ! $expiresAt && $validFor ) {
				$expiresAt = lmfwc_convert_valid_for_to_expires_at( $validFor );
			}

			lmfwc_update_order_downloads_expiration( $expiresAt, $orderId );

			AdminNotice::success(__('1 license key(s) added successfully.', 'license-manager-for-woocommerce'));

			// Update the stock
			if ($license->getStatus() == LicenseStatusEnum::ACTIVE) {
				/**
				* Filter lmfwc_stock_increase
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_stock_increase', $productId);
			}
		} else {
			AdminNotice::error(__('There was a problem adding the license key.', 'license-manager-for-woocommerce'));
		}

		wp_safe_redirect(sprintf('admin.php?page=%s&action=add', AdminMenus::LICENSES_PAGE));
		exit();
	}

	/**
	 * Updates an existing license keys.
	 *
	 * @throws Exception
	 */
	public function updateLicenseKey() {
		// Check the nonce
		$lmfwc_data = $_REQUEST;
		check_admin_referer('lmfwc_update_license_key');

		$licenseId         = absint($lmfwc_data['license_id']);
		$status            = absint($lmfwc_data['status']);
		$orderId           = null;
		$productId         = null;
		$userId            = null;
		$validFor          = null;
		$expiresAt         = null;
		$timesActivatedMax = null;

		/**
		 *  LicenseResourceRepository find license
		 * 
		 * @var LicenseResourceRepository $license 
		**/
		$oldLicense = LicenseResourceRepository::instance()->find($licenseId);

		if (array_key_exists('order_id', $lmfwc_data) && $lmfwc_data['order_id']) {
			$orderId = $lmfwc_data['order_id'];
		}

		if (array_key_exists('product_id', $lmfwc_data) && $lmfwc_data['product_id']) {
			$productId = $lmfwc_data['product_id'];
		}

		if (array_key_exists('user_id', $lmfwc_data) && $lmfwc_data['user_id']) {
			$userId = $lmfwc_data['user_id'];
		}

		if (array_key_exists('valid_for', $lmfwc_data) && $lmfwc_data['valid_for']) {
			$validFor  = $lmfwc_data['valid_for'];
			$expiresAt = null;
		}

		if (array_key_exists('expires_at', $lmfwc_data) && $lmfwc_data['expires_at']) {
			$validFor  = null;
			$expiresAt = $lmfwc_data['expires_at'];
		}

		if (array_key_exists('times_activated_max', $lmfwc_data) && $lmfwc_data['times_activated_max']) {
			$timesActivatedMax = absint($lmfwc_data['times_activated_max']);
		}

		// Check for duplicates
		/**
		* Filter lmfwc_duplicate
		* 
		* @since 1.0
		**/
		if (apply_filters('lmfwc_duplicate', $lmfwc_data['license_key'], $licenseId)) {
			AdminNotice::error(__('The license key already exists.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(sprintf('admin.php?page=%s&action=edit&id=%d', AdminMenus::LICENSES_PAGE, $licenseId));
			exit;
		}
		// empty license key
        if(empty($_POST['license_key'])){
            AdminNotice::error(__('The license key field is empty.', 'license-manager-for-woocommerce'));
            wp_safe_redirect(sprintf('admin.php?page=%s&action=add', AdminMenus::LICENSES_PAGE));
            exit;
        };

		// Update the stock
		if ($oldLicense->getProductId() !== null && $oldLicense->getStatus() === LicenseStatusEnum::ACTIVE) {
			/**
			* Filter lmfwc_stock_decrease
			* 
			* @since 1.0
			**/
			apply_filters('lmfwc_stock_decrease', $oldLicense->getProductId());
		}

		/**
		 *  LicenseResourceRepository find license
		 * 
		 * @var LicenseResourceRepository $license 
		**/
		$license = LicenseResourceRepository::instance()->update(
			$licenseId,
			array(
				'order_id'            => $orderId,
				'product_id'          => $productId,
				'user_id'             => $userId,
				/**
				* Filter lmfwc_encrypt
				* 
				* @since 1.0
				**/
				'license_key'         => apply_filters('lmfwc_encrypt', $lmfwc_data['license_key']),
				/**
				* Filter lmfwc_hash
				* 
				* @since 1.0
				**/
				'hash'                => apply_filters('lmfwc_hash', $lmfwc_data['license_key']),
				'expires_at'          => $expiresAt,
				'valid_for'           => $validFor,
				'source'              => $lmfwc_data['source'],
				'status'              => $status,
				'times_activated_max' => $timesActivatedMax,
			)
		);

		if ($license) {
			// Update the stock
			if ($license->getProductId() !== null && $license->getStatus() === LicenseStatusEnum::ACTIVE) {
				/**
				* Filter lmfwc_stock_increase
				* 
				* @since 1.0
				**/
				apply_filters('lmfwc_stock_increase', $license->getProductId());
			}

			if ( ! $expiresAt && $validFor ) {
				$expiresAt = lmfwc_convert_valid_for_to_expires_at( $validFor );
			}

			lmfwc_update_order_downloads_expiration( $expiresAt, $orderId );

			// Display a success message
			AdminNotice::success(__('Your license key has been updated successfully.', 'license-manager-for-woocommerce'));
		} else {
			//Display an error message
			AdminNotice::error(__('There was a problem updating the license key.', 'license-manager-for-woocommerce'));
		}

		wp_safe_redirect(sprintf('admin.php?page=%s&action=edit&id=%d', AdminMenus::LICENSES_PAGE, $licenseId));
		exit();
	}

	/**
	 * Show a single license key.
	 */
	public function showLicenseKey() {
		$lmfwc_data = $_SERVER;
		$data = $_REQUEST;
		// Validate request.
		check_ajax_referer('lmfwc_show_license_key', 'show');

		if ( 'POST' !== $lmfwc_data['REQUEST_METHOD']  ) {
			wp_die(esc_html__('Invalid request.', 'license-manager-for-woocommerce'));
		}

		/**
		 *  LicenseResourceRepository find license
		 * 
		 * @var LicenseResourceRepository $license 
		**/
		$license = LicenseResourceRepository::instance()->findBy(array( 'id' => $data['id'] ));

		wp_send_json($license->getDecryptedLicenseKey());

		wp_die();
	}

	/**
	 * Shows all visible license keys.
	 */
	public function showAllLicenseKeys() { 
		// Validate request.
		$lmfwc_data = $_REQUEST;
		
		check_ajax_referer('lmfwc_show_all_license_keys', 'show_all');
		
		if ( 'POST' == $lmfwc_data['REQUEST_METHOD'] ) {
			wp_die(esc_html__('Invalid request.', 'license-manager-for-woocommerce'));
		}

		$licenseKeysIds = array();

		foreach (json_decode($lmfwc_data['ids']) as $licenseKeyId) {
			/**
			 *  LicenseResourceRepository find license
			 * 
			 * @var LicenseResourceRepository $license 
			**/
			$license = LicenseResourceRepository::instance()->find($licenseKeyId);

			$licenseKeysIds[$licenseKeyId] = $license->getDecryptedLicenseKey();
		}

		wp_send_json($licenseKeysIds);
	}
}
