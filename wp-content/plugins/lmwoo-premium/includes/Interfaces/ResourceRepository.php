<?php

namespace LicenseManagerForWooCommerce\Interfaces;

use stdClass;
use LicenseManagerForWooCommerce\Abstracts\ResourceModel as AbstractResourceModel;

defined('ABSPATH') || exit;

interface ResourceRepository {

	/**
	 * Sanitize
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function sanitize( &$data );

	/**
	 * Insert
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function insert( $data );

	/**
	 * Find
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function find( $id );

	/**
	 * FindBy
	 *
	 * @param array $query
	 *
	 * @return mixed
	 */
	public function findBy( $query );

	/**
	 * FindAll
	 *
	 * @return mixed
	 */
	public function findAll();

	/**
	 * FindAllBy
	 *
	 * @param array $query
	 *
	 * @return mixed
	 */
	public function findAllBy( $query );

	/**
	 * Update
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function update( $id, $data );

	/**
	 * UpdateBy
	 *
	 * @param array $query
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function updateBy( $query, $data );

	/**
	 * Delete
	 *
	 * @param array $ids
	 *
	 * @return mixed
	 */
	public function delete( $ids );

	/**
	 * DeleteBy
	 *
	 * @param array $query
	 *
	 * @return mixed
	 */
	public function deleteBy( $query );

	/**
	 * Count
	 *
	 * @return mixed
	 */
	public function count();

	/**
	 * CountBy
	 *
	 * @param array $query
	 *
	 * @return mixed
	 */
	public function countBy( $query );

	/**
	 * Query
	 *
	 * @param string $queryString
	 *
	 * @return mixed
	 */
	public function query( $queryString );

	/**
	 * Truncate
	 *
	 * @return mixed
	 */
	public function truncate();

	/**
	 * GetTable
	 *
	 * @return string
	 */
	public function getTable();

	/**
	 * GetPrimaryKey
	 *
	 * @return string
	 */
	public function getPrimaryKey();

	/**
	 * GetModel
	 *
	 * @return string
	 */
	public function getModel();

	/**
	 * GetMapping
	 *
	 * @return array
	 */
	public function getMapping();
}
