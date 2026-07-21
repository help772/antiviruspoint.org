<?php

namespace LicenseManagerForWooCommerce\Abstracts;

defined('ABSPATH') || exit;

abstract class ResourceModel {

	/**
	 * Returns the class properties as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}

	/**
	 * Returns formatted date
	 *
	 * @param $column
	 * @param $srcFormat
	 * @param string $targetFormat
	 *
	 * @return string|null
	 */
	public function toFormattedDate( $column, $srcFormat, $targetFormat = 'system' ) {
		if ( ! isset( $this->$column ) || empty( $this->$column ) ) {
			return null;
		}

		return $this->$column ;
	}
}
