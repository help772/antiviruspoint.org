<?php

namespace LicenseManagerForWooCommerce\Enums;

defined('ABSPATH') || exit;

class ColumnType {

	/**
	 * INT
	 *
	 * @var string
	 */
	const INT = 'INT';

	 /**
	  * TEXT
	  *
	 * @var string
	 */
	const TEXT = 'TEXT';
	/**
	 * TINYINT
	 *
	 * @var string
	 */
	const TINYINT = 'TINYINT';

	/**
	 * BIGINT
	 *
	 * @var string
	 */
	const BIGINT = 'BIGINT';

	/**
	 * CHAR
	 *
	 * @var string
	 */
	const CHAR = 'CHAR';

	/**
	 * HTML_TEXT
	 *
	 * @var string
	 */
	const HTML_TEXT = 'HTML_TEXT';
	
	/**
	 * VARCHAR
	 *
	 * @var string
	 */
	const VARCHAR = 'VARCHAR';

	/**
	 * LONGTEXT
	 *
	 * @var string
	 */
	const LONGTEXT = 'LONGTEXT';

	/**
	 * DATETIME
	 *
	 * @var string
	 */
	const DATETIME = 'DATETIME';

	const SERIALIZED = 'SERIALIZED';
}
