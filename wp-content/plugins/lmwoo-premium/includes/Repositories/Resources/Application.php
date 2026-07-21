<?php

namespace LicenseManagerForWooCommerce\Repositories\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceRepository as AbstractResourceRepository;
use LicenseManagerForWooCommerce\Interfaces\ResourceRepository as ResourceRepositoryInterface;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Enums\ColumnType as ColumnTypeEnum;
use LicenseManagerForWooCommerce\Models\Resources\Application as ApplicationResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;


defined( 'ABSPATH' ) || exit;

class Application extends AbstractResourceRepository implements ResourceRepositoryInterface {

	/**
	 * Application Table name
	 * 
	 * @var string
	**/
	const TABLE = 'lmfwc_application';

	/** Country constructor **/
	public function __construct() {

		global $wpdb;

		$this->table      = $wpdb->prefix . self::TABLE;
		$this->primaryKey = 'id';
		$this->model      = ApplicationResourceModel::class;
		$this->mapping    = array(
			'name'              => ColumnTypeEnum::TEXT,
			'type'              => ColumnTypeEnum::VARCHAR,
			'stable_release_id' => ColumnTypeEnum::BIGINT,
			'description'       => ColumnTypeEnum::HTML_TEXT,
			'documentation'     => ColumnTypeEnum::HTML_TEXT,
			'support'           => ColumnTypeEnum::HTML_TEXT,
			'gallery'           => ColumnTypeEnum::SERIALIZED,
		);
	}

	/**
	 * Find by license
	 *
	 * @param LicenseResourceModel $license
	 */
	public function findByLicense( $license ) {
		$productId = $license->getProductId();

		return $productId ? $this->findByProduct( $productId ) : null;
	}

	/**
	 * Find by product id
	 *
	 * @param $productId
	 *
	 * @return bool|LicenseManagerForWooCommerce\Abstracts\AbstractResourceModel|null|ApplicationResourceModel
	 */
	public function findByProduct( $productId ) {

		$productId = $this->sanitizeProductId( $productId );

		if ( ! $productId ) {
			return null;
		}

		$product = wc_get_product( $productId );
		if ( ! $product ) {
			return null;
		}

		$applicationId = (int) $product->get_meta( 'lmfwc_application_id', true );
		if ( $applicationId ) {
			return self::find( $applicationId );
		}

		return null;
	}

	/**
	 * Find the active releases by license.
	 *
	 * @param LicenseResourceModel $license
	 * @param array $params
	 *
	 * @return bool|LicenseManagerForWooCommerce\Abstracts\AbstractResourceModel[]|ApplicationReleaseResourceModel[]
	 */
	public function findApplicationReleases( $license, $params = array() ) {

		return $this->queryApplicationReleases( $license, $params, 'data' );
	}

	/**
	 * Find the active releases by license.
	 *
	 * @param LicenseResourceModel $license
	 * @param array $params
	 *
	 * @return int
	 */
	public function countApplicationReleases( $license, $params = array() ) {

		return $this->queryApplicationReleases( $license, $params, 'count' );
	}

	/**
	 * Query the application releases by license
	 *
	 * @param $license
	 * @param $params
	 * @param $type
	 *
	 * @return array|bool|LicenseManagerForWooCommerce\Abstracts\AbstractResourceModel[]|int
	 */
	public function queryApplicationReleases( $license, $params = array(), $type = 'data' ) {

		$expiresAt = $license->getExpiresAt();
		$application  = $this->findByLicense( $license );

		if ( empty( $application ) ) {
			return array();
		}

		$per_page = isset( $params['per_page'] ) ? (int) $params['per_page'] : - 1;
		$page     = isset( $params['page'] ) ? (int) $params['page'] : - 1;
		$offset   = ( $page < 0 || $per_page < 0 ) ? 0 : ( ( $page * $per_page ) - $per_page );

		if ( $per_page > 0 && $offset < 0 ) {
			$offset = 0;
		}

		$query = array(
			'application_id' => $application->getId(),
		);

		if ( isset( $params['active'] ) && $params['active'] && ! is_null( $expiresAt ) ) {
			$query['created_at'] = array(
				'compare' => '<=',
				'value'   => $expiresAt,
			);
		}

		if ( 'count' === $type ) {
			return ApplicationReleaseResourceRepository::instance()->countBy( $query );
		} else {
			return ApplicationReleaseResourceRepository::instance()->findAllBy( $query, 'created_at', 'DESC', $offset, $per_page );
		}
	}

	/**
	 * Query the downloads
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function queryApplicationDownloads( $params = array() ) {

		/**
		 * Default parameters
		 */
		$params = wp_parse_args( $params, array(
			'customer_id' => 0,
		) );

		/**
		 * Pagination data
		 */
		
		$customer_id = isset( $params['customer_id'] ) ? (int) $params['customer_id'] : 0;


		$results = LicenseResourceRepository::instance()->findAllBy( array(
			'user_id' => $customer_id,
		), 'expires_at', 'DESC' );

		$total = LicenseResourceRepository::instance()->countBy( array(
			'user_id' => $customer_id,
		) );


		$formatted = array();
		foreach ( $results as $license ) {

			/* @var LicenseResourceModel $license */
			$product    = wc_get_product( $license->getProductId() );
			if ( !is_object($product) ) {
				continue;
			}
			
			$product_id = strpos( $product->get_type(), 'variation' ) ? self::findByProduct( $product->get_parent_id() ) : $product->get_id();
			$application   = self::findByProduct( $product_id );
			
			$release    = null;
			if ( $application ) {
				$release = $application->getStableRelease();
				$data    = array(
					'license_id'                 => $license->getId(),
					'license_hash'               => $license->getHash(),
					'license_expires_at'         => $license->getExpiresAt(),
					'application_id'                => $application->getId(),
					'application_name'              => $application->getName(),
					'application_latest_release'    => empty($release) ? ' ' :  $release->getVersion(),
					'application_latest_release_id' => empty($release) ? ' ' :  $release->getId(),
				);
				$formatted[] = $data;
			}
			
		}
		/**
		 * Query and results
		 */
		return array(
			'total' => $total,
			'data'  => $formatted,
		);
	}

	/**
	 * Returns the product id sanitized
	 *
	 * @param $productId
	 *
	 * @return mixed|void
	 */
	private function sanitizeProductId( $productId ) {
		if ( is_object( $productId ) ) {
			if ( method_exists( $productId, 'get_id' ) ) {
				$productId = $productId->get_id();
			} else if ( isset( $productId->ID ) ) {
				$productId = $productId->ID;
			} else if ( isset( $productId->id ) ) {
				$productId = $productId->id;
			}
		}
		/**
		 * Filter lmfwc_product_id
		 * 
		 * @since 1.0
		**/
		return apply_filters( 'lmfwc_product_id', $productId );
	}
}
