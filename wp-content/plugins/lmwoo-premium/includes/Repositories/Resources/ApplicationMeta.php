<?php


namespace LicenseManagerForWooCommerce\Repositories\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceRepository as AbstractResourceRepository;
use LicenseManagerForWooCommerce\Interfaces\ResourceRepository as ResourceRepositoryInterface;
use LicenseManagerForWooCommerce\Enums\ColumnType as ColumnTypeEnum;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationMeta as ApplicationMetaResourceModel;


defined( 'ABSPATH' ) || exit;

class ApplicationMeta extends AbstractResourceRepository implements ResourceRepositoryInterface {

	/**
	 * Table application meta
	 * 
	 * @var string
	**/
	const TABLE = 'lmfwc_application_meta';

	/**
	 * Country constructor.
	 */
	public function __construct() {

		global $wpdb;

		$this->table      = $wpdb->prefix . self::TABLE;
		$this->primaryKey = 'meta_id';
		$this->model      = ApplicationMetaResourceModel::class;
		$this->mapping    = array(
			'application_id' => ColumnTypeEnum::BIGINT,
			'meta_key'    => ColumnTypeEnum::VARCHAR,
			'meta_value'  => ColumnTypeEnum::LONGTEXT,
		);
	}
}
