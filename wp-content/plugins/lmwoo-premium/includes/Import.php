<?php

namespace LicenseManagerForWooCommerce;

defined('ABSPATH') || exit;

class Import {

	/**
	 * Temporary import file name
	 */
	const TEMP_IMPORT_FILE = 'import.tmp';

	/**
	 * Import constructor.
	 */
	public function __construct() {
		add_filter('lmfwc_import_license_keys_file', array( $this, 'importLicenseKeysFile' ), 10);
		add_filter('lmfwc_import_license_keys_clipboard', array( $this, 'importLicenseKeysClipboard' ), 10, 1);
	}

	/**
	 * Extracts the license keys from the uploaded CSV/TXT file.
	 *
	 * @return array|false|null
	 *
	 * @throws \Exception
	 */
	public function importLicenseKeysFile()
{
    $duplicateLicenseKeys = array();
    $licenseKeys = array();
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $fileName = $_FILES['file']['tmp_name'];
    $filePath = LMFWC_ASSETS_DIR . self::TEMP_IMPORT_FILE;
    // Validate the file extension
    if (!in_array($ext, array('txt', 'csv')) || !in_array($_FILES['file']['type'], $mimes)) {
        AdminNotice::error(__('Invalid file type, only TXT and CSV allowed.', 'license-manager-for-woocommerce'));
        wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
        exit();
    }
    // File upload file, return with error
    if (!move_uploaded_file($fileName, $filePath)) {
        return null;
    }
    // Handle CSV file uploads
    if ('csv' == $ext ) {
        if (($handle = fopen(LMFWC_ASSETS_DIR . self::TEMP_IMPORT_FILE, 'r')) !== false) {
            // Read the header row to identify columns
            $header = fgetcsv($handle, 1000, ',', '"', '\\'); 
            $licenseKeyIndex = array_search('license_key', $header);
            if ($licenseKeyIndex === false) {
                AdminNotice::error(__('Invalid CSV format: Missing "license_key" column.', 'license-manager-for-woocommerce'));
                fclose($handle);
                unlink($filePath);
                wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
                exit();
            }
            // Read and process rows
            while (($data = fgetcsv($handle, 1000, ',', '"', '\\')) !== false) {
                if (isset($data[$licenseKeyIndex]) && !empty($data[$licenseKeyIndex])) {
                    $licenseKeys[] = $data[$licenseKeyIndex];
                }
            }
            fclose($handle);
        }
    }
    // Handle TXT file uploads
    elseif ('txt' == $ext ) {
        $licenseKeys = file(LMFWC_ASSETS_DIR . self::TEMP_IMPORT_FILE, FILE_IGNORE_NEW_LINES);
        if (!is_array($licenseKeys)) {
            AdminNotice::error(__('Invalid file content.', 'license-manager-for-woocommerce'));
            wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
            exit();
        }
    }
    // Check for duplicates
    foreach ($licenseKeys as $i => $licenseKey) {
        if (apply_filters('lmfwc_duplicate', $licenseKey)) {
            unset($licenseKeys[$i]);
            $duplicateLicenseKeys[] = $licenseKey;
        }
    }
    if (count($duplicateLicenseKeys) > 0) {
        AdminNotice::warning(
            sprintf(
                __('%d license key(s) skipped because they already exist.', 'license-manager-for-woocommerce'),
                count($duplicateLicenseKeys)
            )
        );
        if (count($licenseKeys) === 0) {
            wp_safe_redirect(sprintf('admin.php?page=%s&action=import', AdminMenus::LICENSES_PAGE));
            exit();
        }
    }
    // Delete the temporary file
    unlink($filePath);
    return $licenseKeys;
}


	/**
	 * Extracts license keys from a string.
	 *
	 * @param string $clipboard
	 * @param string $delimiter
	 *
	 * @return array
	 */
	public function importLicenseKeysClipboard( $clipboard ) {
		return explode("\n", $clipboard);
	}
}
