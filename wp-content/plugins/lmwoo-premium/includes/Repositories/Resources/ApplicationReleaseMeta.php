<?php


namespace LicenseManagerForWooCommerce\Repositories\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceRepository as AbstractResourceRepository;
use LicenseManagerForWooCommerce\Interfaces\ResourceRepository as ResourceRepositoryInterface;
use LicenseManagerForWooCommerce\Enums\ColumnType as ColumnTypeEnum;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationReleaseMeta as ApplicationReleaseMetaResourceModel;

defined( 'ABSPATH' ) || exit;

class ApplicationReleaseMeta extends AbstractResourceRepository implements ResourceRepositoryInterface {

	/**
	 * Table application release meta
	 * 
	 * @var string
	**/
	const TABLE = 'lmfwc_application_release_meta';

	/**
	 * Country constructor.
	 */
	public function __construct() {

		global $wpdb;

		$this->table      = $wpdb->prefix . self::TABLE;
		$this->primaryKey = 'meta_id';
		$this->model      = ApplicationReleaseMetaResourceModel::class;
		$this->mapping    = array(
			'release_id' => ColumnTypeEnum::BIGINT,
			'meta_key'   => ColumnTypeEnum::VARCHAR,
			'meta_value' => ColumnTypeEnum::LONGTEXT,
		);
	}
}
