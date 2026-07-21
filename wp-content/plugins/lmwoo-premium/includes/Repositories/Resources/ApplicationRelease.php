<?php

namespace LicenseManagerForWooCommerce\Repositories\Resources;

use LicenseManagerForWooCommerce\Abstracts\ResourceRepository as AbstractResourceRepository;
use LicenseManagerForWooCommerce\Interfaces\ResourceRepository as ResourceRepositoryInterface;
use LicenseManagerForWooCommerce\Enums\ColumnType as ColumnTypeEnum;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;

defined( 'ABSPATH' ) || exit;

/**
 * Class ApplicationRelease
 *
 * @package LicenseManagerForWooCommerce\Repositories\Resources
 */
class ApplicationRelease extends AbstractResourceRepository implements ResourceRepositoryInterface {

	/**
	 * Table application releases
	 * 
	 * @var string
	**/
	const TABLE = 'lmfwc_application_release';

	/**
	 * Country constructor.
	 */
	public function __construct() {

		global $wpdb;

		$this->table      = $wpdb->prefix . self::TABLE;
		$this->primaryKey = 'id';
		$this->model      = ApplicationReleaseResourceModel::class;
		$this->mapping    = array(
			'application_id'   => ColumnTypeEnum::BIGINT,
			'version'       => ColumnTypeEnum::VARCHAR,
			'download_type' => ColumnTypeEnum::VARCHAR,
			'download_file' => ColumnTypeEnum::TEXT,
			'changelog'     => ColumnTypeEnum::HTML_TEXT,
		);
	}
}
