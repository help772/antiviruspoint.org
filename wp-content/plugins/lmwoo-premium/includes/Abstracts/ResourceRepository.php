<?php
// phpcs:ignoreFile
namespace LicenseManagerForWooCommerce\Abstracts;

use LicenseManagerForWooCommerce\Enums\ColumnType as ColumnTypeEnum;
use LicenseManagerForWooCommerce\Interfaces\ResourceRepository as RepositoryInterface;
use LicenseManagerForWooCommerce\Setup;

defined('ABSPATH') || exit;

abstract class ResourceRepository extends Singleton implements RepositoryInterface {

	/**
	 * Table
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * PrimaryKey
	 *
	 * @var string
	 */
	protected $primaryKey;

	/**
	 * Model
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Mapping
	 *
	 * @var array
	 */
	protected $mapping;

	
	protected $useCreatedBy = true;

	protected $useCreatedAt = true;

	protected $useUpdatedAt = true;

	protected $useUpdatedBy = true;

	/**
	 * Sanitizes the user data when adding or updating entities.
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function sanitize( &$data ) {
		foreach ( $data as $column => $value ) {
			switch ( $this->mapping[ $column ] ) {
				case ColumnTypeEnum::HTML_TEXT:
					if ( null !== $data[ $column ] ) {
						$data[ $column ] = $this->sanitizeHtml( $value );
					}
					break;
				case ColumnTypeEnum::CHAR:
				case ColumnTypeEnum::VARCHAR:
				case ColumnTypeEnum::LONGTEXT:
				case ColumnTypeEnum::DATETIME:
					if ( null !== $data[ $column ]   ) {
						$data[ $column ] = sanitize_text_field( $value );
					}
					break;
				case ColumnTypeEnum::INT:
				case ColumnTypeEnum::TINYINT:
				case ColumnTypeEnum::BIGINT:
					if (  null !== $data[ $column ] ) {
						$data[ $column ] = (int) $value;
					}
					break;
				case ColumnTypeEnum::SERIALIZED:
					if ( ! is_scalar( $data[ $column ] ) ) {
						$data[ $column ] = json_encode( $data[ $column ] );
					}
			}
		}
	}

   /**
	 * Allowed tags in the HTML supported fields.
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function sanitizeHtml( $value ) {
		return wp_kses( $value, array(
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'p'      => array(),
			'h1'     => array(),
			'h2'     => array(),
			'h3'     => array(),
			'h4'     => array(),
			'h5'     => array(),
			'h6'     => array(),
			'span'   => array(),
			'ul'     => array(),
			'li'     => array(),
			'ol'     => array(),
		) );
	}

	/**
	 * Adds a new entry to the table.
	 *
	 * @param array $data
	 *
	 * @return bool|ResourceModel
	 */
	public function insert( $data ) {
		global $wpdb;

		$meta = array(
			'created_at' => gmdate('Y-m-d H:i:s'),
			'created_by' => get_current_user_id(),
		);
		
		// Pass the data by reference and sanitize its contents
		$this->sanitize($data);
		if ( $wpdb->prefix . Setup::ACTIVATIONS_TABLE_NAME == $this->table ) {
			unset($meta['created_by']);
		}
		
		$insert = $wpdb->insert($this->table, array_merge($data, $meta));

		if (!$insert) {
			return false;
		}
		
		return $this->find($wpdb->insert_id);
	}

	public function insertUpdate( array $data, array $findBy, $addMeta = true ) {
		global $wpdb;

		if ( $addMeta ) {
			$meta = array(
				'created_at' => gmdate( 'Y-m-d H:i:s' ),
				'created_by' => get_current_user_id(),
			);

			$data = array_merge( $data, $meta );
		}

		// Pass the data by reference and sanitize its contents
		$this->sanitize( $data );

		if ( $this->findBy( $findBy ) ) {
			$result = $wpdb->update( $this->table, $data, $findBy );
		} else {
			$result = $wpdb->insert( $this->table, $data );
		}

		if ( ! $result ) {
			return false;
		}

		return true;
	}



	/**
	 * Retrieves a single table row by its ID.
	 *
	 * @param int $id
	 *
	 * @return bool|ResourceModel
	 */
	public function find( $id ) {
		if (!class_exists($this->model)) {
			return false;
		}

		global $wpdb;

		$result = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM %1s WHERE %2s = %d;', $this->table, $this->primaryKey, $id
			)
		);

		if (!$result) {
			return false;
		}

		return new $this->model($result);
	}

	/**
	 * Retrieves a single table row by the query parameter.
	 *
	 * @param array $query
	 *
	 * @return bool|ResourceModel
	 */
	public function findBy( $query ) {
		if (!class_exists($this->model) || !$query || !is_array($query) || count($query) <= 0) {
			return false;
		}

		global $wpdb;

		$sqlQuery = $this->parseQueryConditions($query);
		$sqlQuery .= ';';
		$wpdb->sqlQueryCondition = $sqlQuery;
		$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %1s WHERE 1=1 {$wpdb->sqlQueryCondition}", $this->table));

		if (!$result) {
			return false;
		}

		return new $this->model($result);
	}

	/**
	 * Retrieves all table rows as an array.
	 *
	 * @return bool|ResourceModel[]
	 */
	public function findAll() {
		global $wpdb;

		$returnValue = array();
		$result = $wpdb->get_results($wpdb->prepare('SELECT %1s FROM %2s;', $this->primaryKey , $this->table));

		if (!$result) {
			return false;
		}

		foreach ($result as $row) {
			$returnValue[] = $this->find($row->id);
		}

		return $returnValue;
	}

	/**
	 * Retrieves multiple table rows as an array, filtered by the query.
	 *
	 * @param array       $query
	 * @param null|string $orderBy
	 * @param null|string $sort
	 *
	 * @return bool|ResourceModel[]
	 */
	public function findAllBy( $query, $orderBy = null, $sort = null ) {
		if (!class_exists($this->model) || !$query || !is_array($query) || count($query) <= 0) {
			return false;
		}

		global $wpdb;
		$sqlQuery = '';
		$result   = array();
		$sqlQuery .= $this->parseQueryConditions($query);

		if ($orderBy && is_string($orderBy)) {
			$sqlQuery .= "ORDER BY {$orderBy} ";
		}

		if ($sort && is_string($sort)) {
			$sqlQuery .= "{$sort} ";
		}

		$sqlQuery .= ';';
		$wpdb->sqlQueryCondition = $sqlQuery;
		foreach ($wpdb->get_results($wpdb->prepare("SELECT * FROM %1s WHERE 1=1 {$wpdb->sqlQueryCondition}", $this->table)) as $row) {
			$result[] = new $this->model($row);
		}

		return $result;
	}



	/**
	 * Updates a single table row by its ID.
	 *
	 * @param int $id
	 * @param array $data
	 *
	 * @return bool|AbstractResourceModel
	 */
	public function update( $id, $data ) {
		global $wpdb;

		$meta = array();
		if ( $this->useUpdatedAt ) {
			$meta['updated_at'] = gmdate( 'Y-m-d H:i:s' );
		}
		if ( $this->useUpdatedBy ) {
			$meta['updated_by'] = get_current_user_id();
		}

		// Pass the data by reference and sanitize its contents
		$this->sanitize( $data );
		$updated = $wpdb->update(
			$this->table,
			array_merge( $data, $meta ),
			array( 'id' => $id )
		);

		if ( ! $updated ) {
			return false;
		}

		return $this->find( $id );
	}

	/**
	 * Updates one or multiple table rows by the query.
	 *
	 * @param array $query
	 * @param array $data
	 *
	 * @return bool|int
	 */
	public function updateBy( $query, $data ) {
		if (!$query || !is_array($query) || count($query) <= 0) {
			return false;
		}

		if (!$data || !is_array($data) || count($data) <= 0) {
			return false;
		}

		global $wpdb;
		$sqlQuery = '';
		$sqlQuery .= $wpdb->prepare(' updated_at = %s,', gmdate('Y-m-d H:i:s'));
		$sqlQuery .= $wpdb->prepare(' updated_by = %d,', get_current_user_id());

		foreach ($data as $columnName => $value) {
			if (is_numeric($value)) {
				$sqlQuery .= " {$columnName} = {$value},";
			} elseif (is_string($value)) {
				$sqlQuery .= " {$columnName} = '{$value}',";
			} elseif (null ===$value) {
				$sqlQuery .= " {$columnName} = NULL,";
			}
		}

		$sqlQuery = rtrim($sqlQuery, ',');

		$sqlQuery .= ' WHERE 1=1 ';
		$sqlQuery .= $this->parseQueryConditions($query);

		$sqlQuery .= ';';
		$wpdb->sqlQueryCondition = $sqlQuery;
		return $wpdb->query($wpdb->prepare("UPDATE %1s SET {$wpdb->sqlQueryCondition}", $this->table));
	}

	/**
	 * Removes multiple table rows by their ID's.
	 *
	 * @param array $ids
	 *
	 * @return bool|int
	 */
	public function delete( $ids ) {

		global $wpdb;

		if ( ! is_array( $ids ) ) {
			$ids = (array) $ids;
		}

		$ids = implode(', ', array_map('absint', $ids));
		$wpdb->deleteIds = $ids;
		return $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE %2s IN ({$wpdb->deleteIds});", $this->table, $this->primaryKey));
	}

	/**
	 * Deletes one or more table rows by the query parameter.
	 *
	 * @param array $query
	 *
	 * @return bool|int
	 */
	public function deleteBy( $query ) {

		if (!$query || !is_array($query) || count($query) <= 0) {
			return false;
		}
		 

		global $wpdb;

		$sqlQuery = '';
		$sqlQuery .= $this->parseQueryConditions($query);
		$sqlQuery .= ';';
		$wpdb->sqlQueryCondition = $sqlQuery;
		return $wpdb->query($wpdb->prepare("DELETE FROM %1s WHERE 1=1 {$wpdb->sqlQueryCondition}", $this->table));
	}

	/**
	 * Retrieves the total count of table entries.
	 *
	 * @return int
	 */
	public function count() {
		global $wpdb;

		$count = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM %1s;', $this->table));

		return intval($count);
	}

	/**
	 * Retrieves the total count of table entries, filtered by the query parameter.
	 *
	 * @param array $query
	 *
	 * @return int
	 */
	public function countBy( $query ) {
		if (!$query || !is_array($query) || count($query) <= 0) {
			return false;
		}

		global $wpdb;

		$sqlQuery = '';
		$sqlQuery .= $this->parseQueryConditions($query);
		$sqlQuery .= ';';
		$wpdb->sqlQueryCondition = $sqlQuery;
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %1s WHERE 1=1 {$wpdb->sqlQueryCondition}", $this->table));

		return intval($count);
	}

	/**
	 * Performs a general query on the table.
	 *
	 * @param string $sqlQuery
	 * @param string $output
	 *
	 * @return array|object|null
	 */
	public function query( $sqlQuery, $output = OBJECT ) {
		global $wpdb;
		$wpdb->sqlQueryCondition = $sqlQuery;
		return $wpdb->get_results($wpdb->sqlQueryCondition, $output);
	}

	/**
	 * Truncates the table.
	 *
	 * @return bool|int
	 */
	public function truncate() {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare('TRUNCATE TABLE %s;', $this->table)
		);
	}

	/**
	 * ParseQueryConditions
	 *
	 * @param array $query
	 *
	 * @return string
	 */
	private function parseQueryConditions( $query ) {
		$result = '';

		foreach ($query as $columnName => $value) {

			if (is_array($value)) {
				$valuesIn = implode(', ', array_map('absint', $value));
				$result .= "AND {$columnName} IN ({$valuesIn}) ";
			} elseif (is_string($value)) {
				$result .= "AND {$columnName} = '{$value}' ";
			} elseif (is_numeric($value)) {
				$value = absint($value);
				$result .= "AND {$columnName} = {$value} ";
			} elseif (null === $value) {
				$result .= "AND {$columnName} IS NULL ";
			}
		}
	  
		return $result;
	}

	/**
	 * GetTable
	 *
	 * @return string
	 */
	public function getTable() {
		return $this->table;
	}

	/**
	 * GetPrimaryKey
	 *
	 * @return string
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * GetModel
	 *
	 * @return string
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * GetMapping
	 *
	 * @return array
	 */
	public function getMapping() {
		return $this->mapping;
	}

	/**
	 * Checks whether an array has string keys.
	 *
	 * @param array $array
	 * @return bool
	 */
	private function hasStringKeys( $array ) {
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}

	/**
	 * Determines if the given string is a valid MySQL logical operator.
	 *
	 * @see https://www.scommerce-mage.com/blog/magento2-condition-type-search-filter.html
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	private function isLogicalOperator( $string ) {
		return in_array(strtoupper($string), array( 'AND', 'OR', 'IN', 'NOT IN', 'NOT LIKE' ));
	}
}
