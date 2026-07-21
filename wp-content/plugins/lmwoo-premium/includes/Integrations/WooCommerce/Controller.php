<?php

namespace LicenseManagerForWooCommerce\Integrations\WooCommerce;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use LicenseManagerForWooCommerce\Abstracts\IntegrationController as AbstractIntegrationController;
use LicenseManagerForWooCommerce\Enums\LicenseSource;
use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Interfaces\IntegrationController as IntegrationControllerInterface;
use LicenseManagerForWooCommerce\Models\Resources\Generator as GeneratorResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Integrations\WooCommerce\Stock;
use LicenseManagerForWooCommerce\Settings;
use LicenseManagerForWooCommerce\Setup;
use LicenseManagerForWooCommerce\Repositories\Resources\LicenseActivations as ActivationResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\Application as ApplicationResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;
use stdClass;
use WC_Order;
use WC_Order_Item_Product;
use WC_Product;
use WC_Product_Simple;
use WC_Product_Variation;
use WP_User;
use WP_User_Query;

defined('ABSPATH') || exit;

class Controller extends AbstractIntegrationController implements IntegrationControllerInterface {

	/**
	 * Controller constructor.
	 */
	public function __construct() {
		$this->bootstrap();

		add_filter('lmfwc_get_customer_license_keys', array( $this, 'getCustomerLicenseKeys' ), 10, 1);
		add_filter('lmfwc_get_all_customer_license_keys', array( $this, 'getAllCustomerLicenseKeys' ), 10, 1);
		add_filter('lmfwc_get_license_activations', array( $this, 'getLicenseActivations' ), 10, 1);
		add_filter('lmfwc_get_license_applications', array( $this, 'getLicenseApplications' ), 10, 1);
		add_filter('lmfwc_insert_generated_license_keys', array( $this, 'insertGeneratedLicenseKeys' ), 10, 5);
		add_filter('lmfwc_insert_imported_license_keys', array( $this, 'insertImportedLicenseKeys' ), 10, 7);
		add_action('lmfwc_sell_imported_license_keys', array( $this, 'sellImportedLicenseKeys' ), 10, 3);
		add_action('wp_ajax_lmfwc_dropdown_search', array( $this, 'dropdownDataSearch' ), 10);
	}

	/**
	 * Initializes the integration component
	 */
	private function bootstrap() {
		new Stock();
		new Order();
		new Email();
		new ProductData();

		if ( Settings::get('lmfwc_enable_my_account_endpoint' , Settings::SECTION_WOOCOMMERCE)) {
			new MyAccount();
		}
	}




	/**
	 * Retrieves ordered license keys.
	 *
	 * @param WC_Order $order WooCommerce Order
	 *
	 * @return array
	 */
	public function getCustomerLicenseKeys( $order ) {
		$data = array();

		foreach ($order->get_items() as $item_data) {

			$product = $item_data->get_product();

			// Skip if product doesn't exist or is not a licensed product
			if (!$product || !$product->get_meta( 'lmfwc_licensed_product', true)) {
				continue;
			}
			
			$licenses = LicenseResourceRepository::instance()->findAllBy(
				array(
					'order_id' => $order->get_id(),
					'product_id' => $product->get_id(),
				)
			);

			$data[$product->get_id()]['name'] = $product->get_name();
			$data[$product->get_id()]['keys'] = $licenses;
		}

		return $data;
	}

	public function getLicenseActivations( $license_id ) {
		
		$activations = ActivationResourceRepository::instance()->findAllBy(
			array(
				'license_id' => $license_id,
			)
		);
		return $activations;
	}

	public function getLicenseApplications( $license_id ) {
		
		$applications = ApplicationResourceRepository::instance()->findAllBy(
			array(
				'license_id' => $license_id,
			)
		);
		return $applications;
	}

	/**
	 * Retrieves all license keys for a user.
	 *
	 * @param int $userId
	 *
	 * @return stdClass|WC_Order[]
	 */
	public function getAllCustomerLicenseKeys( $userId ) {
		$result = array();
		$args = array(
			'limit' => -1,
			'lmfwc_order_complete' => 1,
			'customer_id' => $userId, 
		);
		$orders = wc_get_orders( $args );
		foreach (  $orders as $order ) {
			 $orderIds = $order->get_id();
				 
			if (empty($orderIds)) {
				return array();
			}
	   

		$licenses = LicenseResourceRepository::instance()->findAllBy(
			array(
				'order_id' => $orderIds,
			)
		);


		
			foreach ($licenses as $license) {
				$product = wc_get_product($license->getProductId());

				if (!$product) {
					$result[$license->getProductId()]['name'] = '#' . $license->getProductId();
				} else {
					$result[$license->getProductId()]['name'] = $product->get_formatted_name();
				}

				$result[$license->getProductId()]['licenses'][] = $license;
			}
		}

		return $result;
	}

	/**
	 * Save the license keys for a given product to the database.
	 *
	 * @param int                    $orderId     WooCommerce Order ID
	 * @param int                    $productId   WooCommerce Product ID
	 * @param string[]               $licenseKeys License keys to be stored
	 * @param int                    $status      License key status
	 * @param GeneratorResourceModel $generator   Generator used
	 *
	 * @throws Exception
	 */
	public function insertGeneratedLicenseKeys( $orderId, $productId, $licenseKeys, $status, $generator ) {
		$cleanLicenseKeys = array();
		$cleanOrderId     = $orderId   ? absint($orderId)   : null;
		$cleanProductId   = $productId ? absint($productId) : null;
		$cleanStatus      = $status    ? absint($status)    : null;
		$userId           = null;

		if (!$cleanStatus || !in_array($cleanStatus, LicenseStatus::$status)) {
			throw new Exception('License Status is invalid.');
		}

		if (!is_array($licenseKeys)) {
			throw new Exception('License Keys must be provided as array');
		}

		foreach ($licenseKeys as $licenseKey) {
			array_push($cleanLicenseKeys, sanitize_text_field($licenseKey));
		}

		if (count($cleanLicenseKeys) === 0) {
			throw new Exception('No License Keys were provided');
		}

		$order = wc_get_order($cleanOrderId);
		if ( $order ) {
			$userId = $order->get_user_id();
		}

		$gmtDate           = new DateTime('now', new DateTimeZone('GMT'));
		$invalidKeysAmount = 0;
		$expiresAt         = null;

		if ($generator->getExpiresIn() && LicenseStatus::SOLD  == $status ) {
			$dateInterval  = 'P' . $generator->getExpiresIn() . 'D';
			$dateExpiresAt = new DateInterval($dateInterval);
			$expiresAt     = $gmtDate->add($dateExpiresAt)->format('Y-m-d H:i:s');
		}

		lmfwc_update_order_downloads_expiration( $expiresAt, $cleanOrderId );

		// Add the keys to the database table.
		foreach ($cleanLicenseKeys as $licenseKey) {
			// Key exists, up the invalid keys count.
			/**
			* Filter lmfwc_duplicate
			* 
			* @since 1.0
			**/
			if (apply_filters('lmfwc_duplicate', $licenseKey)) {
				$invalidKeysAmount++;
				continue;
			}

			// Key doesn't exist, add it to the database table.
			/**
			* Filter lmfwc_encrypt
			* 
			* @since 1.0
			**/
			$encryptedLicenseKey = apply_filters('lmfwc_encrypt', $licenseKey);
			/**
			* Filter lmfwc_hash
			* 
			* @since 1.0
			**/
			$hashedLicenseKey    = apply_filters('lmfwc_hash', $licenseKey);

			// Save to database.
			LicenseResourceRepository::instance()->insert(
				array(
					'order_id'            => $cleanOrderId,
					'product_id'          => $cleanProductId,
					'user_id'             => $userId,
					'license_key'         => $encryptedLicenseKey,
					'hash'                => $hashedLicenseKey,
					'expires_at'          => $expiresAt,
					'valid_for'           => $generator->getExpiresIn(),
					'source'              => LicenseSource::GENERATOR,
					'status'              => $cleanStatus,
					'times_activated_max' => $generator->getTimesActivatedMax(),
				)
			);
		}

		// There have been duplicate keys, regenerate and add them.
		if ($invalidKeysAmount > 0) {
			/**
			* Filter lmfwc_generate_license_keys
			* 
			* @since 1.0
			**/
			$newKeys = apply_filters('lmfwc_generate_license_keys', $invalidKeysAmount, $generator);

			$this->insertGeneratedLicenseKeys(
				$cleanOrderId,
				$cleanProductId,
				$newKeys,
				$cleanStatus,
				$generator
			);
		} elseif ( $cleanOrderId ) {
				$order = wc_get_order($cleanOrderId);
				$order->update_meta_data('lmfwc_order_complete', 1);
				$order->save();
		}
	}

	/**
	 * Imports an array of un-encrypted license keys.
	 *
	 * @param array $licenseKeys       License keys to be stored
	 * @param int   $status            License key status
	 * @param int   $orderId           WooCommerce Order ID
	 * @param int   $productId         WooCommerce Product ID
	 * @param int   $userId            WordPress User ID
	 * @param int   $validFor          Validity period (in days)
	 * @param int   $timesActivatedMax Maximum activation count
	 *
	 * @return array
	 * @throws Exception
	 */
	public function insertImportedLicenseKeys(
		$licenseKeys,
		$status,
		$orderId,
		$productId,
		$userId,
		$validFor,
		$timesActivatedMax
	) {
		$result                 = array();
		$cleanLicenseKeys       = array();
		$cleanStatus            = $status            ? absint($status)            : null;
		$cleanOrderId           = $orderId           ? absint($orderId)           : null;
		$cleanProductId         = $productId         ? absint($productId)         : null;
		$cleanUserId            = $userId            ? absint($userId)            : null;
		$cleanValidFor          = $validFor          ? absint($validFor)          : null;
		$cleanTimesActivatedMax = $timesActivatedMax ? absint($timesActivatedMax) : null;

		if (!is_array($licenseKeys)) {
			throw new Exception('License Keys must be an array');
		}

		if (!$cleanStatus) {
			throw new Exception('Status enumerator is missing');
		}

		if (!in_array($cleanStatus, LicenseStatus::$status)) {
			throw new Exception('Status enumerator is invalid');
		}

		foreach ($licenseKeys as $licenseKey) {
			array_push($cleanLicenseKeys, sanitize_text_field($licenseKey));
		}

		$result['added']  = 0;
		$result['failed'] = 0;

		// Add the keys to the database table.
		foreach ($cleanLicenseKeys as $licenseKey) {
			$license = LicenseResourceRepository::instance()->insert(
				array(
					'order_id'            => $cleanOrderId,
					'product_id'          => $cleanProductId,
					'user_id'             => $cleanUserId,
					/**
					* Filter lmfwc_encrypt
					* 
					* @since 1.0
					**/
					'license_key'         => apply_filters('lmfwc_encrypt', $licenseKey),
					/**
					* Filter lmfwc_hash
					* 
					* @since 1.0
					**/
					'hash'                => apply_filters('lmfwc_hash', $licenseKey),
					'valid_for'           => $cleanValidFor,
					'source'              => LicenseSource::IMPORT,
					'status'              => $cleanStatus,
					'times_activated_max' => $cleanTimesActivatedMax,
				)
			);

			if ($license) {
				
				if ( $validFor ) {
					$date          = new DateTime();
					$date_interval = new DateInterval( 'P' . $validFor . 'D' );
					$expiresAt    = $date->add( $date_interval )->format( 'Y-m-d H:i:s' );

					lmfwc_update_order_downloads_expiration( $expiresAt, $orderId );
				}
				$result['added']++;
			} else {
				$result['failed']++;
			}
		}

		return $result;
	}

	/**
	 * Mark the imported license keys as sold.
	 *
	 * @param LicenseResourceModel[] $licenses License key resource models
	 * @param int                    $orderId  WooCommerce Order ID
	 * @param int                    $amount   Amount to be marked as sold
	 *
	 * @throws Exception
	 * @throws Exception
	 */
	public function sellImportedLicenseKeys( $licenses, $orderId, $amount ) {
		$cleanLicenseKeys = $licenses;
		$cleanOrderId     = $orderId ? absint($orderId) : null;
		$cleanAmount      = $amount  ? absint($amount)  : null;
		$userId           = null;

		if (!is_array($licenses) || count($licenses) <= 0) {
			throw new Exception('License Keys are invalid.');
		}

		if (!$cleanOrderId) {
			throw new Exception('Order ID is invalid.');
		}

		if (!$cleanOrderId) {
			throw new Exception('Amount is invalid.');
		}

		$order = wc_get_order($cleanOrderId);
		if ( $order ) {
			$userId = $order->get_user_id();
		}

		for ($i = 0; $i < $cleanAmount; $i++) {
			
			$license   = $cleanLicenseKeys[$i];
			$validFor  = intval($license->getValidFor());
			$expiresAt = $license->getExpiresAt();

			if ($validFor) {
				$date         = new DateTime();
				$dateInterval = new DateInterval('P' . $validFor . 'D');
				$expiresAt    = $date->add($dateInterval)->format('Y-m-d H:i:s');
			}

			LicenseResourceRepository::instance()->update(
				$license->getId(),
				array(
					'order_id'   => $cleanOrderId,
					'user_id'    => $userId,
					'expires_at' => $expiresAt,
					'status'     => LicenseStatus::SOLD,
				)
			);
		}
	}

	/**
 * Performs a paginated data search for orders, products, or users to be used inside a select2 dropdown
 */
	public function dropdownDataSearch() {
		$lmfwc_data = $_REQUEST;
		check_ajax_referer('lmfwc_dropdown_search', 'security');

		$type    = (string) wc_clean(wp_unslash($lmfwc_data['type']));
		$page    = isset($lmfwc_data['page']) ? intval($lmfwc_data['page']) : 1;
		$limit   = 10;
		$results = array();
		$term    = isset($lmfwc_data['term']) ? (string) wc_clean(wp_unslash($lmfwc_data['term'])) : '';
		$more    = true;
		$offset  = ( $page > 1 ) ? ( $page - 1 ) * $limit : 0;

		if (!$term) {
			wp_die();
		}

		if (is_numeric($term)) {
			// Search for a specific order, product, or user
			if ( 'license' === $type ) {

				$license = LicenseResourceRepository::instance()->find( (int) $term );

				// Product exists.
				if ( $license ) {
					$text      = sprintf(
						'#%s',
						$license->getId()
					);
					$results[] = array(
						'id'   => $license->getId(),
						'text' => $text,
					);
				}
			} elseif ('shop_order' === $type) {
				$order = wc_get_order(intval($term));

				if ($order && $order instanceof WC_Order) {
					$text = sprintf(
					'#%1$s %2$s <%3$s>',
					$order->get_order_number(),
					$order->get_formatted_billing_full_name(),
					$order->get_billing_email()
					);

					$results[] = array(
						'id'   => $order->get_id(),
						'text' => $text,
					);
				}
			} elseif ('product' === $type) {
				$product = wc_get_product(intval($term));

				if ($product) {
					$text = sprintf(
					'(%1$s) %2$s',
					$product->get_id(),
					$product->get_formatted_name()
					);

					$results[] = array(
						'id'   => $product->get_id(),
						'text' => $text,
					);
				}
			} elseif ('user' === $type) {
				$users = new WP_User_Query(array(
				'search'         => '*' . esc_attr($term) . '*',
				'search_columns' => array( 'user_id' ),
				));

				foreach ($users->get_results() as $user) {
					$results[] = array(
					'id'   => $user->ID,
					'text' => sprintf('%1$s (#%2$d - %3$s)', $user->user_nicename, $user->ID, $user->user_email),
					);
				}
			}
		} else {
			// Search for orders, generators, products, or users
			$args = array(
			'type'     => $type,
			'limit'    => $limit,
			'offset'   => $offset,
			'customer' => $term,
			);
			if ( 'license' === $type ) {
				$licenses = $this->searchLicenses( $term, $limit, $offset );

				if ( count( $licenses ) < $limit ) {
					$more = false;
				}

				foreach ( $licenses as $licenseId ) {
					
					$text      = sprintf(
						'#%s',
						$licenseId
					);
					$results[] = array(
						'id'   => $licenseId,
						'text' => $text,
					);
				}
			} elseif ('shop_order' === $type) {
				$orders = wc_get_orders($args);

				if (count($orders) < $limit) {
					$more = false;
				}

				foreach ($orders as $order) {
					$text = sprintf(
					'#%1$s %2$s <%3$s>',
					$order->get_order_number(),
					$order->get_formatted_billing_full_name(),
					$order->get_billing_email()
					);

					$results[] = array(
						'id'   => $order->get_id(),
						'text' => $text,
					);
				}
			} elseif ('generator' === $type) {
				$generators = $this->searchGenerators($term, $limit, $offset);

				if (count($generators) < $limit) {
					$more = false;
				}

				foreach ($generators as $generator) {
					$text      = sprintf('#%d - %s', $generator['id'], $generator['name']);
					$results[] = array( 'id' => $generator['id'], 'text' => $text );
				}
			} elseif ('product' === $type) {
				$products = $this->searchProducts($term, $limit, $offset);

				if (count($products) < $limit) {
					$more = false;
				}

				foreach ($products as $productId) {
					$product = wc_get_product($productId);

					if ($product) {
						$text = sprintf('(%1$s) %2$s', $product->get_id(), $product->get_name());

						$results[] = array(
						'id'   => $product->get_id(),
						'text' => $text,
						);
					}
				}
			} elseif ('user' === $type) {
				$users = new WP_User_Query(array(
				'search'         => '*' . esc_attr($term) . '*',
				'search_columns' => array( 'user_login', 'user_nicename', 'user_email', 'user_url' ),
				));

				foreach ($users->get_results() as $user) {
					$results[] = array(
					'id'   => $user->ID,
					'text' => sprintf('%s (#%d - %s)', $user->user_nicename, $user->ID, $user->user_email),
					);
				}
			}
		}

		wp_send_json(array(
		'page'       => $page,
		'results'    => $results,
		'pagination' => array( 'more' => $more ),
		));
	}

	/**
	 * Searches the database for posts that match the given term.
	 *
	 * @param string $term The search term
	 * @param int $limit Maximum number of search results
	 * @param int $offset Search offset
	 *
	 * @return array
	 */
	private function searchLicenses( $term, $limit, $offset ) {
		global $wpdb;
		$wpdb->tblLicenses = $wpdb->prefix . Setup::LICENSES_TABLE_NAME;
		/**
		* Filter lmfwc_hash
		* 
		* @since 1.0
		**/
		$termHash = apply_filters('lmfwc_hash', license($term)) . '%'; // Assuming "license" is a function defined somewhere
		$termId = intval($term);

		return $wpdb->get_col($wpdb->prepare(
			"SELECT DISTINCT licenses.id
	        FROM $wpdb->tblLicenses AS licenses
	        WHERE 1=1 AND (licenses.hash LIKE %s OR licenses.id = %d)
	        ORDER BY licenses.ID DESC
	        LIMIT %d OFFSET %d",
			$termHash,
			$termId,
			$limit,
			$offset
		));
	}

	/**
	 * Searches the database for posts that match the given term.
	 *
	 * @param string $term   The search term
	 * @param int    $limit  Maximum number of search results
	 * @param int    $offset Search offset
	 *
	 * @return array
	 */
	private function searchProducts( $term, $limit, $offset ) {
		 global $wpdb;

		return $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT(posts.ID)
	        FROM {$wpdb->posts} AS posts
	        INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
	        WHERE 1=1
	        AND (posts.post_title LIKE %s OR meta.meta_value LIKE %s)
	        AND (posts.post_type = 'product' OR posts.post_type = 'product_variation')
	        ORDER BY posts.ID DESC LIMIT %d OFFSET %d",
			'%' . $wpdb->esc_like( $term ) . '%',
			'%' . $wpdb->esc_like( $term ) . '%',
			$limit,
			$offset
		) );
	}

	private function searchGenerators( $term, $limit, $offset ) {
	global $wpdb;
	$tableGenerators = $wpdb->prefix . Setup::GENERATORS_TABLE_NAME;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT generators.id, generators.name
        FROM $wpdb->tableGenerators AS generators
        WHERE 1=1 AND (generators.name LIKE %s OR generators.id=%d)
        ORDER BY generators.id DESC
        LIMIT %d OFFSET %d",
		'%' . $wpdb->esc_like( $term ) . '%',
		intval($term),
		$limit,
		$offset
	), ARRAY_A );
	}

		/**
	 * Return license url
	 *
	 * @param $license
	 *
	 * @return string|null
	 */
	public static function getAccountLicenseUrl( $license_id ) {
		return esc_url( wc_get_account_endpoint_url( 'view-license-keys/' . $license_id ) );
	}
	/**
	 * Return license url
	 *
	 * @param $license
	 *
	 * @return string|null
	 */
	public static function getAccountApplicationUrl( $license_id ) {
		return esc_url( wc_get_account_endpoint_url( 'view-applications/' . $license_id ) );
	}
}
