<?php

namespace LicenseManagerForWooCommerce\Controllers;

use LicenseManagerForWooCommerce\AdminMenus;
use LicenseManagerForWooCommerce\AdminNotice;
use LicenseManagerForWooCommerce\Models\Resources\Generator as GeneratorResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;

defined('ABSPATH') || exit;

class Generator {

	/**
	 * Generator constructor.
	 */
	public function __construct() {
		// Admin POST requests
		add_action('admin_post_lmfwc_save_generator', array( $this, 'saveGenerator' ), 10);
		add_action('admin_post_lmfwc_update_generator', array( $this, 'updateGenerator' ), 10);
		add_action('admin_post_lmfwc_generate_license_keys', array( $this, 'generateLicenseKeys' ), 10);
	}

	/**
	 * Save the generator to the database.
	 */
	public function saveGenerator() {
		$lmfwc_data = $_REQUEST;

		// Verify the nonce.
		check_admin_referer('lmfwc_save_generator');

		// Validate request.
		if ( '' ==  $lmfwc_data['name']  || !is_string($lmfwc_data['name'])) {
			AdminNotice::error(__('Generator name is missing.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(admin_url(sprintf('admin.php?page=%s&action=add', AdminMenus::GENERATORS_PAGE)));
			exit();
		}

		if ( '' == $lmfwc_data['charset']   || !is_string($lmfwc_data['charset'])) {
			AdminNotice::error(__('The charset is invalid.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(admin_url(sprintf('admin.php?page=%s&action=add', AdminMenus::GENERATORS_PAGE)));
			exit();
		}

		if ( '' ==  $lmfwc_data['chunks']  || !is_numeric($lmfwc_data['chunks'])) {
			AdminNotice::error(__('Only integer values allowed for chunks.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(admin_url(sprintf('admin.php?page=%s&action=add', AdminMenus::GENERATORS_PAGE)));
			exit();
		}

		if ( '' ==  $lmfwc_data['chunk_length']  || !is_numeric($lmfwc_data['chunk_length'])) {
			AdminNotice::error(__('Only integer values allowed for chunk length.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(admin_url(sprintf('admin.php?page=%s&action=add', AdminMenus::GENERATORS_PAGE)));
			exit();
		}

		// Save the generator.
		$generator = GeneratorResourceRepository::instance()->insert(
			array(
				'name'                => $lmfwc_data['name'],
				'charset'             => $lmfwc_data['charset'],
				'chunks'              => $lmfwc_data['chunks'],
				'chunk_length'        => $lmfwc_data['chunk_length'],
				'times_activated_max' => $lmfwc_data['times_activated_max'],
				'separator'           => $lmfwc_data['separator'],
				'prefix'              => $lmfwc_data['prefix'],
				'suffix'              => $lmfwc_data['suffix'],
				'expires_in'          => $lmfwc_data['expires_in'],
			)
		);

		if ($generator) {
			AdminNotice::success(__('The generator was added successfully.', 'license-manager-for-woocommerce'));
		} else {
			AdminNotice::error(__('There was a problem adding the generator.', 'license-manager-for-woocommerce'));
		}

		wp_safe_redirect(admin_url(sprintf('admin.php?page=%s', AdminMenus::GENERATORS_PAGE)));
		exit();
	}

	/**
	 * Update an existing generator.
	 */
	public function updateGenerator() {
		$lmfwc_data = $_REQUEST;
		
		// Verify the nonce.
		check_admin_referer('lmfwc_update_generator');
	
		$generatorId = absint($lmfwc_data['id']);

		// Validate request.
		if (  ''  ==  $lmfwc_data['name']  || !is_string($lmfwc_data['name'])) {
			AdminNotice::error(__('The Generator name is invalid.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}

		if ( '' == $lmfwc_data['charset']   || !is_string($lmfwc_data['charset'])) {
			AdminNotice::error(__('The Generator charset is invalid.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}

		if ( '' == $lmfwc_data['chunks']   || !is_numeric($lmfwc_data['chunks'])) {
			AdminNotice::error(__('The Generator chunks are invalid.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}

		if ( '' == $lmfwc_data['chunk_length']   || !is_numeric($lmfwc_data['chunk_length'])) {
			AdminNotice::error(__('The Generator chunk length is invalid.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}

		// Update the generator.
		$generator = GeneratorResourceRepository::instance()->update(
			$lmfwc_data['id'],
			array(
				'name'                => $lmfwc_data['name'],
				'charset'             => $lmfwc_data['charset'],
				'chunks'              => $lmfwc_data['chunks'],
				'chunk_length'        => $lmfwc_data['chunk_length'],
				'times_activated_max' => ! empty ($lmfwc_data['times_activated_max'] ) ? $lmfwc_data['times_activated_max'] : null,
				'separator'           => $lmfwc_data['separator'],
				'prefix'              => $lmfwc_data['prefix'],
				'suffix'              => $lmfwc_data['suffix'],
				'expires_in'          => ! empty ($lmfwc_data['expires_in'] ) ? $lmfwc_data['expires_in'] : null,
			)
		);

		// Redirect according to $result.
		if (!$generator) {
			AdminNotice::error(__('There was a problem updating the generator.', 'license-manager-for-woocommerce'));
		} else {
			AdminNotice::success(__('The Generator was updated successfully.', 'license-manager-for-woocommerce'));
		}

		wp_safe_redirect(admin_url(sprintf('admin.php?page=%s', AdminMenus::GENERATORS_PAGE)));
		exit();
	}

	/**
	 * Generates a chosen amount of license keys using the selected generator.
	 */
	public function generateLicenseKeys() {
		$lmfwc_data = $_REQUEST;
		// Verify the nonce.
		check_admin_referer('lmfwc_generate_license_keys');

		$generatorId = absint($lmfwc_data['generator_id']);
		$amount      = absint($lmfwc_data['amount']);
		$status      = absint($lmfwc_data['status']);
		$orderId     = null;
		$productId   = null;

		/**
		 *  GeneratorResourceRepository find generator
		 * 
		 * @var GeneratorResourceRepository $generator 
		**/
		$generator = GeneratorResourceRepository::instance()->find($generatorId);

		if (array_key_exists('order_id', $lmfwc_data) && $lmfwc_data['order_id']) {
			$orderId = absint($lmfwc_data['order_id']);
		}

		if (array_key_exists('product_id', $lmfwc_data) && $lmfwc_data['product_id']) {
			$productId = absint($lmfwc_data['product_id']);
		}

		// Validate request.
		if (!$generator) {
			AdminNotice::error(__('The chosen generator does not exist.', 'license-manager-for-woocommerce'));

			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}

		if ($orderId && !wc_get_order($orderId)) {
			AdminNotice::error(__('The chosen order does not exist.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}

		if ($productId && !wc_get_product($productId)) {
			AdminNotice::error(__('The chosen product does not exist.', 'license-manager-for-woocommerce'));
			wp_safe_redirect(
				admin_url(
					sprintf(
						'admin.php?page=%s&action=edit&id=%d',
						AdminMenus::GENERATORS_PAGE,
						$generatorId
					)
				)
			);
			exit();
		}
		/**
		* Filter lmfwc_generate_license_keys
		* 
		* @since 1.0
		**/
		$licenses = apply_filters('lmfwc_generate_license_keys', $amount, $generator);

		/**
		* Filter lmfwc_insert_generated_license_keys
		* 
		* @since 1.0
		**/ 
		apply_filters(
			'lmfwc_insert_generated_license_keys',
			$orderId,
			$productId,
			$licenses,
			$status,
			$generator
		);

		// Show message and redirect.
		AdminNotice::success(sprintf(
			/* translators: %d is the number of license generated*/
			__('Successfully generated %d license key(s).', 'license-manager-for-woocommerce'), $amount));
		wp_safe_redirect(admin_url(sprintf('admin.php?page=%s&action=generate', AdminMenus::GENERATORS_PAGE)));
		exit();
	}
}
