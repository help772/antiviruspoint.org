<?php

namespace LicenseManagerForWooCommerce\Repositories\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceRepository as AbstractResourceRepository;
use LicenseManagerForWooCommerce\Interfaces\ResourceRepository as ResourceRepositoryInterface;
use LicenseManagerForWooCommerce\Enums\ColumnType as ColumnTypeEnum;
use LicenseManagerForWooCommerce\Models\Resources\ProductInstalledOn as ProductInstalledOnResourceModel;

defined( 'ABSPATH' ) || exit;

class ProductInstalledOn extends AbstractResourceRepository implements ResourceRepositoryInterface {
	
	const TABLE = 'lmfwc_products_installed_on';

	public function __construct() {
		global $wpdb;

		$this->table      = $wpdb->prefix . self::TABLE;
		$this->primaryKey = 'id';
		$this->model      = ProductInstalledOnResourceModel::class;
		$this->mapping    = array(
			'product_name' => ColumnTypeEnum::LONGTEXT,
			'license_id'   => ColumnTypeEnum::BIGINT,
			'host'         => ColumnTypeEnum::VARCHAR,
			'last_ping'    => ColumnTypeEnum::DATETIME,
		);
	}
}
