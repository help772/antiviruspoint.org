<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined('ABSPATH') or exit;

/**
 * Reconciliation Handler.
 *
 * Handles the business logic for reconciliation between WooCommerce orders
 * and Avalara transactions. Fetches data from both systems, compares them,
 * and identifies discrepancies.
 *
 * @since 3.8.0
 */
class WC_AvaTax_Reconciliation_Handler {

	/** Batch size for WC orders per Avatax API call. */
	const RECONCILIATION_BATCH_SIZE = 200;

	/** Transient key prefix for background job status (suffix: session_id). */
	const JOB_TRANSIENT_PREFIX = 'wc_avatax_reconciliation_job_';

	/** Transient expiry for job status (1 hour). */
	const JOB_TRANSIENT_EXPIRY = 3600;

	/**
	 * Run reconciliation in background: batch loop only, write to DB. Used by cron.
	 * Caller is responsible for updating job status transient (running → completed/failed).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param array $filters from_date, to_date, document_type
	 * @return void
	 * @throws Exception On error
	 */
	public function run_reconciliation_batches($session_id, $filters)
	{
		$fromDate = !empty($filters['from_date']) ? sanitize_text_field($filters['from_date']) : date('Y-m-01');
		$toDate = !empty($filters['to_date']) ? sanitize_text_field($filters['to_date']) : date('Y-m-d');
		$documentType = !empty($filters['document_type']) ? sanitize_text_field($filters['document_type']) : 'SalesInvoice';

		$batch_index = 0;
		$offset = 0;
		$batch_count = 0;
		do {
			$batch = $this->get_woocommerce_orders($fromDate, $toDate, $documentType, self::RECONCILIATION_BATCH_SIZE, $offset);
			$documentCodes = array_values(array_filter(array_unique(wp_list_pluck($batch, 'document_code'))));
			$avalaraBatch = !empty($documentCodes)
				? $this->get_avalara_transactions_by_codes($documentCodes)
				: array();
			$batchComparison = $this->compare_data($batch, $avalaraBatch);
			$batch_count = count($batch);
			$this->save_reconciliation_batch(
				$session_id,
				$batch_index,
				$batchComparison,
				$batch_count,
				count($avalaraBatch),
				$fromDate,
				$toDate,
				$documentType
			);
			// Free memory - critical for preventing memory exhaustion (same pattern as transaction push)
			unset($batchComparison);
			unset($avalaraBatch);
			$this->clear_order_caches_for_batch($batch);
			unset($batch, $documentCodes);
			if (function_exists('gc_collect_cycles')) {
				gc_collect_cycles();
			}
			$batch_index++;
			$offset += self::RECONCILIATION_BATCH_SIZE;
		} while ($batch_count === self::RECONCILIATION_BATCH_SIZE);
	}

	/**
	 * Clear WordPress and WooCommerce caches for order/refund IDs from a batch to free memory.
	 * Same pattern as WC_AvaTax_Transaction_Push_Handler::clear_order_caches_for_batch.
	 *
	 * @since 3.8.0
	 * @param array $batch_order_rows Array of order rows (each with 'order_id' key)
	 */
	protected function clear_order_caches_for_batch($batch_order_rows)
	{
		if (!is_array($batch_order_rows)) {
			return;
		}
		foreach ($batch_order_rows as $row) {
			$order_id = isset($row['order_id']) ? (int) $row['order_id'] : 0;
			if ($order_id <= 0) {
				continue;
			}
			wp_cache_delete('order-' . $order_id, 'orders');
			wp_cache_delete($order_id, 'post_meta');
			wp_cache_delete($order_id, 'posts');
			clean_post_cache($order_id);
		}
		wp_cache_flush();
	}

	/**
	 * Get reconciliation data for all tabs (sync: run batches then return overview).
	 * For async flow, use run_reconciliation_batches via cron and get_reconciliation_overview_from_batches when polling.
	 *
	 * @since 3.8.0
	 *
	 * @param array $filters Filter parameters (from_date, to_date, document_type, search)
	 * @return array Reconciliation data for all tabs
	 */
	public function get_reconciliation_data($filters)
	{
		try {
			$fromDate = !empty($filters['from_date']) ? sanitize_text_field($filters['from_date']) : date('Y-m-01');
			$toDate = !empty($filters['to_date']) ? sanitize_text_field($filters['to_date']) : date('Y-m-d');
			$documentType = !empty($filters['document_type']) ? sanitize_text_field($filters['document_type']) : 'SalesInvoice';

			$session_id = function_exists('wp_generate_uuid') ? wp_generate_uuid() : uniqid('recon_', true);
			$this->run_reconciliation_batches($session_id, $filters);

			$overview = $this->get_reconciliation_overview_from_batches($session_id);
			return array(
				'session_id' => $session_id,
				'overview' => $overview,
				'missing_orders' => array('count' => $overview['missing_in_avalara']),
				'mismatches' => array('count' => $overview['mismatches']),
			);
		} catch (Exception $e) {
			wc_avatax()->log(sprintf('Reconciliation error: %s', $e->getMessage()));
			throw $e;
		}
	}

	/**
	 * Get current job status from transient (pending, running, completed, failed).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array { status, filters?, error? }
	 */
	public function get_reconciliation_job_status($session_id)
	{
		$session_id = trim((string) $session_id);
		if ('' === $session_id) {
			return array('status' => 'failed', 'error' => __('Invalid session.', 'woocommerce-avatax'));
		}
		$key = self::JOB_TRANSIENT_PREFIX . $session_id;
		$job = get_transient($key);
		if (false === $job || !is_array($job)) {
			return array('status' => 'unknown', 'error' => __('Session expired or not found.', 'woocommerce-avatax'));
		}
		return array(
			'status' => isset($job['status']) ? $job['status'] : 'unknown',
			'filters' => isset($job['filters']) ? $job['filters'] : array(),
			'error' => isset($job['error']) ? $job['error'] : '',
		);
	}

	/**
	 * Set job status transient (pending, running, completed, failed).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param string $status pending|running|completed|failed
	 * @param array $filters Optional, for pending/running
	 * @param string $error Optional, for failed
	 */
	public function set_reconciliation_job_status($session_id, $status, $filters = array(), $error = '')
	{
		$key = self::JOB_TRANSIENT_PREFIX . $session_id;
		$job = array(
			'status' => $status,
			'filters' => $filters,
			'error' => $error,
		);
		set_transient($key, $job, self::JOB_TRANSIENT_EXPIRY);
	}

	/**
	 * Get WooCommerce orders for the specified date range (paginated).
	 *
	 * @since 3.8.0
	 *
	 * @param string $fromDate Start date (Y-m-d format)
	 * @param string $toDate End date (Y-m-d format)
	 * @param string $documentType Document type filter (SalesInvoice, ReturnInvoice, or All)
	 * @param int $limit Number of orders per batch (default 200)
	 * @param int $offset Offset for pagination (default 0)
	 * @return array Array of order data
	 */
	protected function get_woocommerce_orders($fromDate, $toDate, $documentType = 'All', $limit = 200, $offset = 0)
	{
		$orders = array();

		$args = array(
			'limit' => $limit,
			'offset' => $offset,
			'date_created' => $fromDate . '...' . $toDate,
			'return' => 'ids',
		);

		// Handle document type filtering
		if ($documentType === 'ReturnInvoice') {
			$args['type'] = 'shop_order_refund';
			$orderIds = wc_get_orders($args);
		} elseif ($documentType === 'SalesInvoice') {
			$args['type'] = 'shop_order';
			$orderIds = wc_get_orders($args);
		} else {
			// All: get all orders in date range (both shop_order and shop_order_refund)
			$args['type'] = array('shop_order', 'shop_order_refund');
			$orderIds = wc_get_orders($args);
		}

		foreach ($orderIds as $orderId) {
			$order = wc_get_order($orderId);
			
			if (!$order) {
				continue;
			}

		// Check if this is a refund (WC_Order_Refund) or regular order (WC_Order)
		$isRefund = is_a($order, 'WC_Order_Refund');
		$parentOrder = null;
		
		// Get AvaTax document code (transaction code)
		// First try to get from meta, fallback to generating it the same way as Avalara API does
		$documentCode = $order->get_meta('_wc_avatax_document_code');

		if (empty($documentCode)) {
			if ($isRefund) {
				// For refunds, document code is: parent_order_key-refund_id
				// See: class-wc-avatax-api-tax-request.php line 816
				$parentOrder = wc_get_order($order->get_parent_id());
				if ($parentOrder) {
					$documentCode = $parentOrder->get_order_key('edit') . '-' . $order->get_id();
				}
			} else {
				// For regular orders, document code is just the order key
				// See: class-wc-avatax-api-tax-request.php line 504
				$documentCode = $order->get_order_key('edit');
			}
		}

		// For refunds, use parent order for customer info and order key
		$orderForData = $isRefund && $parentOrder ? $parentOrder : $order;

			$orders[] = array(
				'order_id' => $order->get_id(),
				'document_code' => $documentCode,
				'order_date' => $order->get_date_created()->format('Y-m-d H:i:s'),
				'customer' => $this->get_customer_name($orderForData),
				'total' => $order->get_total() - $order->get_total_tax(), // Subtotal excluding tax (to match Avalara's totalAmount)
				'total_tax' => $order->get_total_tax(),
				'status' => $order->get_status(),
				'currency' => $order->get_currency(),
				'order_key' => $orderForData->get_order_key('edit'),
				'refund_type' => $isRefund ? $order->get_meta('_refund_type') : '',
			);
		}

		return $orders;
	}

	/**
	 * Get Avalara transactions by document codes (code in (...) filter).
	 * Uses same API URL as date-range fetch but with $filter=code in ("code1","code2",...).
	 *
	 * @since 3.8.0
	 *
	 * @param array $documentCodes List of _wc_avatax_document_code values from WooCommerce orders
	 * @return array Array of Avalara transaction data
	 */
	protected function get_avalara_transactions_by_codes($documentCodes)
	{
		$transactions = array();
		if (empty($documentCodes)) {
			return $transactions;
		}

		try {
			$api = wc_avatax()->get_api();
			$filter = $this->build_avalara_transactions_filter_by_codes($documentCodes);
			$response = $api->get_transactions(get_option('wc_avatax_company_code'), $filter);

			$nextLink = $this->append_avalara_transactions_from_response($response, $transactions);

			while (!empty($nextLink)) {
				$paginated_url = $this->normalize_avalara_pagination_url($nextLink);
				$response = $api->get_transactions('', '', $paginated_url);
				$nextLink = $this->append_avalara_transactions_from_response($response, $transactions);
			}
		} catch (Exception $e) {
			wc_avatax()->log(sprintf('Failed to fetch Avalara transactions: %s', $e->getMessage()));
			return array();
		}

		return $transactions;
	}

	/**
	 * Build OData filter string for Avalara transactions by document codes: code in ('c1','c2',...).
	 *
	 * @since 3.8.0
	 * @param array $documentCodes Non-empty list of document codes
	 * @return string OData filter
	 */
	protected function build_avalara_transactions_filter_by_codes($documentCodes)
	{
		return 'code in (' . implode(',', $documentCodes) . ')';
	}

	/**
	 * Build OData filter string for Avalara transactions (date range and optional type).
	 *
	 * @since 3.8.0
	 * @param string $fromDate Start date (Y-m-d)
	 * @param string $toDate End date (Y-m-d)
	 * @param string $documentType Document type or 'All'
	 * @return string OData filter
	 */
	protected function build_avalara_transactions_filter($fromDate, $toDate, $documentType)
	{
		$filterParts = array(
			sprintf("date ge '%s'", $fromDate),
			sprintf("date le '%s'", $toDate),
		);
		if ($documentType !== 'All') {
			$filterParts[] = sprintf("type eq '%s'", $documentType);
		}
		return implode(' and ', $filterParts);
	}

	/**
	 * Normalize Avalara @nextLink to path relative to API base (avoids .../api/v2/api/v2/...).
	 *
	 * @since 3.8.0
	 * @param string $nextLink Value of response @nextLink
	 * @return string Path to pass to get_transactions for next page
	 */
	protected function normalize_avalara_pagination_url($nextLink)
	{
		if (strpos($nextLink, '/v2') === false) {
			return $nextLink;
		}
		$parts = explode('/v2', $nextLink, 2);
		return isset($parts[1]) ? $parts[1] : $nextLink;
	}

	/**
	 * Append transaction rows from an API response and return next page link.
	 *
	 * @since 3.8.0
	 * @param \SkyVerge\WooCommerce\AvaTax\API\Responses\Transactions_Response|null $response API response
	 * @param array &$transactions Array to append to (by reference)
	 * @return string Next page link or empty string
	 */
	protected function append_avalara_transactions_from_response($response, &$transactions)
	{
		if (!$response || !$response->get_transactions()) {
			return '';
		}
		foreach ($response->get_transactions() as $transaction) {
			$transactions[] = $this->map_avalara_transaction_to_row($transaction);
		}
		return $response->get_next_link();
	}

	/**
	 * Map a single Avalara transaction object to reconciliation row format.
	 *
	 * @since 3.8.0
	 * @param object $transaction Transaction from API
	 * @return array Row for comparison
	 */
	protected function map_avalara_transaction_to_row($transaction)
	{
		return array(
			'code' => $transaction->code ?? '',
			'document_code' => $transaction->code ?? '',
			'date' => $transaction->date ?? '',
			'total_amount' => $transaction->totalAmount ?? 0,
			'total_tax' => $transaction->totalTax ?? 0,
			'status' => $transaction->status ?? '',
			'type' => $transaction->type ?? '',
			'customer_code' => $transaction->customerCode ?? '',
		);
	}

	/**
	 * Save or append reconciliation data: first batch INSERTs a row, subsequent batches UPDATE the same row using JSON_ARRAY_APPEND. Caller should unset batch variables after each call to free memory.
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param int $batch_index 0 = insert new row, >0 = append via JSON_ARRAY_APPEND
	 * @param array $batchComparison Result of compare_data() for this batch
	 * @param int $orders_in_batch WC orders count in this batch
	 * @param int $avalara_in_batch Avalara transactions count in this batch
	 * @param string $from_date Filter from date
	 * @param string $to_date Filter to date
	 * @param string $document_type Filter document type
	 */
	protected function save_reconciliation_batch($session_id, $batch_index, $batchComparison, $orders_in_batch, $avalara_in_batch, $from_date, $to_date, $document_type)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$batch_missing_ids = array_values(wp_list_pluck($batchComparison['missing'], 'order_id'));
		$batch_mismatch_rows = array();
		foreach ($batchComparison['mismatches'] as $m) {
			$avalara = $m['avalara'];
			$batch_mismatch_rows[] = array(
				'order_id' => (int) $m['order']['order_id'],
				'avalara_total' => (float) (isset($avalara['total_amount']) ? $avalara['total_amount'] : 0),
				'avalara_tax' => (float) (isset($avalara['total_tax']) ? $avalara['total_tax'] : 0),
			);
		}

		// Initial row already created by ensure_initial_reconciliation_session_row; always UPDATE (append this batch).
		$missing_expr = 'missing_order_ids';
		foreach ($batch_missing_ids as $id) {
			$id = (int) $id;
			$missing_expr = "JSON_ARRAY_APPEND($missing_expr, '\$', %d)";
		}
		$mismatch_expr = 'mismatch_order_ids';
		foreach ($batch_mismatch_rows as $row) {
			$oid = (int) $row['order_id'];
			$atot = (float) $row['avalara_total'];
			$atax = (float) $row['avalara_tax'];
			$mismatch_expr = "JSON_ARRAY_APPEND($mismatch_expr, '\$', JSON_OBJECT('order_id', $oid, 'avalara_total', $atot, 'avalara_tax', $atax))";
		}
		$sql = "UPDATE {$table} SET "
			. "missing_order_ids = $missing_expr, "
			. "mismatch_order_ids = $mismatch_expr, "
			. "orders_in_batch = orders_in_batch + %d, "
			. "avalara_in_batch = avalara_in_batch + %d "
			. "WHERE session_id = %s";
		$params = array_merge(array_map('intval', array_values($batch_missing_ids)), array($orders_in_batch, $avalara_in_batch, $session_id));
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query template is built with placeholders and passed to prepare().
		$wpdb->query($wpdb->prepare($sql, $params));
	}

	/** Maximum number of previous runs to retain. */
	const MAX_RUNS_TO_KEEP = 10;

	/**
	 * Detect pending sessions whose Action Scheduler action was cancelled or deleted,
	 * and update their status to 'cancelled'. Called automatically before fetching runs.
	 *
	 * @since 3.8.0
	 */
	protected function sync_stale_pending_runs()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$pending_sessions = $wpdb->get_col($wpdb->prepare(
			'SELECT session_id FROM %i WHERE status = %s',
			$table,
			'pending'
		));
		if (empty($pending_sessions)) {
			return;
		}

		$as_table = $wpdb->prefix . 'actionscheduler_actions';
		if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $as_table)) !== $as_table) {
			return;
		}

		$like_clauses = array();
		$params = array($as_table, 'wc_avatax_run_reconciliation');
		foreach ($pending_sessions as $sid) {
			$like_clauses[] = 'args LIKE %s';
			$params[] = '%' . $wpdb->esc_like($sid) . '%';
		}

		$sql = "SELECT action_id, args, status FROM %i "
			. "WHERE hook = %s AND (" . implode(' OR ', $like_clauses) . ") "
			. "ORDER BY action_id DESC";
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is built with dynamic LIKE clauses; all values escaped via esc_like + prepare.
		$actions = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);

		$session_as_status = array();
		foreach ($actions as $row) {
			$decoded = json_decode($row['args'], true);
			if (!is_array($decoded)) {
				$decoded = maybe_unserialize($row['args']);
			}
			$sid = is_array($decoded) && isset($decoded[0]) ? $decoded[0] : '';
			if ($sid !== '' && !isset($session_as_status[$sid])) {
				$session_as_status[$sid] = $row['status'];
			}
		}

		foreach ($pending_sessions as $sid) {
			if (!isset($session_as_status[$sid])) {
				$this->update_reconciliation_session_status($sid, 'cancelled');
				continue;
			}
			$as_status = $session_as_status[$sid];
			if ($as_status === 'pending' || $as_status === 'in-progress') {
				continue;
			}
			if ($as_status === 'complete') {
				$this->update_reconciliation_session_status($sid, 'completed');
			} elseif ($as_status === 'failed') {
				$this->update_reconciliation_session_status($sid, 'failed');
			} else {
				$this->update_reconciliation_session_status($sid, 'cancelled');
			}
		}
	}

	/**
	 * Get list of previous reconciliation runs (most recent first, up to MAX_RUNS_TO_KEEP).
	 *
	 * @since 3.8.0
	 * @return array List of runs with session_id, from_date, to_date, document_type, run_date, counts, and status.
	 */
	public function get_reconciliation_runs()
	{
		$this->sync_stale_pending_runs();

		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';

		$sql = $wpdb->prepare(
			"SELECT session_id, from_date, to_date, document_type, status, "
			. "created_at as run_date, "
			. "orders_in_batch as total_wc_orders, "
			. "avalara_in_batch as total_avalara, "
			. "COALESCE(JSON_LENGTH(missing_order_ids), 0) as missing_count, "
			. "COALESCE(JSON_LENGTH(mismatch_order_ids), 0) as mismatch_count "
			. "FROM %i "
			. "ORDER BY created_at DESC "
			. "LIMIT %d",
			$table,
			self::MAX_RUNS_TO_KEEP
		);
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is the result of $wpdb->prepare() above.
		$rows = $wpdb->get_results($sql, ARRAY_A);
		if (empty($rows)) {
			return array();
		}
		$result = array();
		foreach ($rows as $row) {
			$result[] = array(
				'session_id'    => $row['session_id'],
				'from_date'     => $row['from_date'],
				'to_date'       => $row['to_date'],
				'document_type' => $row['document_type'],
				'status'        => !empty($row['status']) ? $row['status'] : 'unknown',
				'wc_orders'     => (int) $row['total_wc_orders'],
				'avalara_transactions' => (int) $row['total_avalara'],
				'missing_count' => (int) $row['missing_count'],
				'mismatch_count' => (int) $row['mismatch_count'],
			);
		}
		return $result;
	}

	/**
	 * Purge old reconciliation runs beyond the retention limit.
	 * Keeps the most recent MAX_RUNS_TO_KEEP runs, deletes all batches for older ones.
	 *
	 * @since 3.8.0
	 * @return int Total rows deleted
	 */
	public function purge_old_reconciliation_runs()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';

		$sql = $wpdb->prepare(
			"SELECT session_id FROM %i "
			. "GROUP BY session_id "
			. "ORDER BY MIN(created_at) DESC",
			$table
		);
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is the result of $wpdb->prepare() above.
		$all_sessions = $wpdb->get_col($sql);
		$old_sessions = array_slice($all_sessions, self::MAX_RUNS_TO_KEEP);
		if (empty($old_sessions)) {
			return 0;
		}
		$total_deleted = 0;
		foreach ($old_sessions as $sid) {
			$deleted = $wpdb->delete($table, array('session_id' => $sid), array('%s'));
			if (is_numeric($deleted)) {
				$total_deleted += (int) $deleted;
			}
		}
		return $total_deleted;
	}

	/**
	 * Delete all batch rows for a reconciliation session (cleanup after UI has been rendered / user left).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return int|false Number of rows deleted or false on failure
	 */
	public function delete_reconciliation_batches_for_session($session_id)
	{
		$session_id = trim((string) $session_id);
		if ('' === $session_id) {
			return 0;
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		return $wpdb->delete($table, array('session_id' => $session_id), array('%s'));
	}


	/**
	 * Ensure the reconciliation session has an initial row with status 'pending' so the table has an entry as soon as the job starts.
	 * Inserts only if no row exists for this session_id (called at the start of run_reconciliation_batches).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param string $from_date Filter from date
	 * @param string $to_date Filter to date
	 * @param string $document_type Filter document type
	 */
	public function ensure_initial_reconciliation_session_row($session_id, $from_date, $to_date, $document_type)
	{
		$session_id = trim((string) $session_id);
		if ('' === $session_id) {
			return;
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$exists = $wpdb->get_var($wpdb->prepare(
			"SELECT 1 FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		));
		if ($exists) {
			return;
		}
		$wpdb->insert(
			$table,
			array(
				'session_id' => $session_id,
				'missing_order_ids' => '[]',
				'mismatch_order_ids' => '[]',
				'from_date' => $from_date,
				'to_date' => $to_date,
				'document_type' => $document_type,
				'orders_in_batch' => 0,
				'avalara_in_batch' => 0,
				'status' => 'pending',
			),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s')
		);
	}

	/**
	 * Update status for a reconciliation session row. Called by the scheduler to mirror job status (e.g. completed, failed).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param string $status New status (e.g. 'pending', 'running', 'completed', 'failed')
	 */
	public function update_reconciliation_session_status($session_id, $status)
	{
		$session_id = trim((string) $session_id);
		if ('' === $session_id) {
			return;
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$rows = $wpdb->update(
			$table,
			array('status' => $status),
			array('session_id' => $session_id),
			array('%s'),
			array('%s')
		);
	}

	/**
	 * Get reconciliation session filters directly from the DB row.
	 * Unlike transients, DB rows are never evicted by external object caches
	 * making this the reliable source of filter data for
	 * background jobs.
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array|null { from_date, to_date, document_type } or null if not found
	 */
	public function get_reconciliation_session_filters_from_db($session_id)
	{
		$session_id = trim((string) $session_id);
		if ('' === $session_id) {
			return null;
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$row = $wpdb->get_row($wpdb->prepare(
			"SELECT from_date, to_date, document_type FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		), ARRAY_A);
		if (empty($row)) {
			return null;
		}
		return array(
			'from_date'     => $row['from_date'],
			'to_date'       => $row['to_date'],
			'document_type' => $row['document_type'],
		);
	}

	/**
	 * Get reconciliation session status directly from the DB (fast PK lookup, never expires).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return string|null Status string or null if not found
	 */
	public function get_reconciliation_session_status_from_db($session_id)
	{
		$session_id = trim((string) $session_id);
		if ('' === $session_id) {
			return null;
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		return $wpdb->get_var($wpdb->prepare(
			"SELECT status FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		));
	}

	/**
	 * Check if any batch rows exist for a given session.
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return bool
	 */
	public function has_reconciliation_batches($session_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*) FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		));
		return (int) $count > 0;
	}

	/**
	 * Get overview counts from batches table (aggregates only, no order/transaction data loaded).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array Same shape as format_overview_data() output
	 */
	public function get_reconciliation_overview_from_batches($session_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$r = $wpdb->get_row($wpdb->prepare(
			"SELECT orders_in_batch, avalara_in_batch, "
			. "COALESCE(JSON_LENGTH(missing_order_ids), 0) as missing_count, "
			. "COALESCE(JSON_LENGTH(mismatch_order_ids), 0) as mismatch_count "
			. "FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		), ARRAY_A);
		if (empty($r)) {
			return array(
				'wc_orders' => 0,
				'avalara_transactions' => 0,
				'missing_in_avalara' => 0,
				'mismatches' => 0,
				'matched' => 0,
				'extra_in_avalara' => 0,
			);
		}
		$total_wc = (int) $r['orders_in_batch'];
		$total_avalara = (int) $r['avalara_in_batch'];
		$total_missing = (int) $r['missing_count'];
		$total_mismatch = (int) $r['mismatch_count'];
		$total_matched = $total_wc - $total_missing - $total_mismatch;
		$total_extra = $total_avalara - $total_matched - $total_mismatch;
		return array(
			'wc_orders' => $total_wc,
			'avalara_transactions' => $total_avalara,
			'missing_in_avalara' => $total_missing,
			'mismatches' => $total_mismatch,
			'matched' => max(0, $total_matched),
			'extra_in_avalara' => max(0, $total_extra),
		);
	}

	/**
	 * Get paginated missing order IDs for a session (from batches table only).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array List of order IDs (may contain duplicates across batches; caller can unique)
	 */
	protected function get_reconciliation_missing_order_ids($session_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$r = $wpdb->get_row($wpdb->prepare(
			"SELECT missing_order_ids FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		), ARRAY_A);
		if (empty($r)) {
			return array();
		}
		$missing = json_decode($r['missing_order_ids'], true);
		return is_array($missing) ? array_values(array_unique(array_filter($missing))) : array();
	}

	/**
	 * Get paginated mismatch order IDs for a session (from batches table only).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array List of order IDs
	 */
	protected function get_reconciliation_mismatch_order_ids($session_id)
	{
		$rows = $this->get_reconciliation_mismatch_rows($session_id);
		return array_values(array_column($rows, 'order_id'));
	}

	/**
	 * Get all stored mismatch rows from batches for this session (order_id, avalara_total, avalara_tax).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array List of stored mismatch rows (each with order_id, avalara_total, avalara_tax)
	 */
	protected function get_reconciliation_mismatch_rows($session_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$r = $wpdb->get_row($wpdb->prepare(
			"SELECT mismatch_order_ids FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		), ARRAY_A);
		$merged = array();
		if (!empty($r)) {
			$decoded = json_decode($r['mismatch_order_ids'], true);
			if (!is_array($decoded)) {
				return $merged;
			}
			$first = reset($decoded);
			if (!is_array($first) || !isset($first['order_id']) || !array_key_exists('avalara_total', $first)) {
				return array();
			}
			$merged = $decoded;
		}
		return $merged;
	}

	/**
	 * Get one page of missing orders for display (loads only that page from DB/Avatax).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param int $page 1-based page number
	 * @param int $per_page Items per page
	 * @return array { 'orders' => formatted array, 'count' => total missing count }
	 */
	public function get_reconciliation_missing_orders_page($session_id, $page = 1, $per_page = 20)
	{
		$ids = $this->get_reconciliation_missing_order_ids($session_id);
		$total = count($ids);
		$page = max(1, (int) $page);
		$per_page = max(1, min(100, (int) $per_page));
		$offset = ($page - 1) * $per_page;
		$slice = array_slice($ids, $offset, $per_page);
		$orders = array();
		foreach ($slice as $order_id) {
			$order_row = $this->build_order_row_from_order_id($order_id);
			if ($order_row) {
				$orders[] = $order_row;
			}
		}
		$formatted = $this->format_missing_orders_data($orders);
		$formatted['count'] = $total;
		return $formatted;
	}

	/**
	 * Get one page of mismatches for display (loads only that page from DB/Avatax).
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @param int $page 1-based page number
	 * @param int $per_page Items per page
	 * @return array { 'mismatches' => formatted array, 'count' => total mismatch count }
	 */
	public function get_reconciliation_mismatches_page($session_id, $page = 1, $per_page = 20)
	{
		$stored_rows = $this->get_reconciliation_mismatch_rows($session_id);
		$total = count($stored_rows);
		$page = max(1, (int) $page);
		$per_page = max(1, min(100, (int) $per_page));
		$offset = ($page - 1) * $per_page;
		$slice = array_slice($stored_rows, $offset, $per_page);
		$formatted = array();
		foreach ($slice as $row) {
			$formatted[] = $this->format_stored_mismatch_row($row);
		}
		return array(
			'mismatches' => $formatted,
			'count' => $total,
		);
	}

	/**
	 * Format one stored mismatch row (from DB) for display. Row has order_id, avalara_total, avalara_tax; WC values from WooCommerce DB. No API call.
	 *
	 * @since 3.8.0
	 * @param array $row Stored row (order_id, avalara_total, avalara_tax)
	 * @return array Same shape as one element of format_mismatches_data output
	 */
	protected function format_stored_mismatch_row($row)
	{
		$order_id = isset($row['order_id']) ? (int) $row['order_id'] : 0;
		$avalara_total = (float) (isset($row['avalara_total']) ? $row['avalara_total'] : 0);
		$avalara_tax = (float) (isset($row['avalara_tax']) ? $row['avalara_tax'] : 0);
		$order_row = $this->build_order_row_from_order_id($order_id);
		if (!$order_row) {
			return array(
				'order_id' => $order_id,
				'document_code' => '',
				'order_date' => '',
				'wc_total' => wc_price(0),
				'wc_tax' => wc_price(0),
				'avalara_total' => wc_price($avalara_total),
				'avalara_tax' => wc_price($avalara_tax),
				'difference' => '',
				'difference_value' => '',
				'all_differences' => array(),
			);
		}
		$wc_total = (float) (isset($order_row['total']) ? $order_row['total'] : 0);
		$wc_tax = (float) (isset($order_row['total_tax']) ? $order_row['total_tax'] : 0);
		$order_date = isset($order_row['order_date']) ? $order_row['order_date'] : '';
		$order_date = $order_date ? date('Y-m-d H:i', strtotime($order_date)) : '';
		$tax_diff = abs($wc_tax - $avalara_tax);
		$total_diff = abs($wc_total - $avalara_total);
		$tolerance = 0.01;
		if ($tax_diff > $tolerance) {
			$diff_label = __('Tax amount', 'woocommerce-avatax');
			$diff_value = wc_price($tax_diff);
		} elseif ($total_diff > $tolerance) {
			$diff_label = __('Total amount', 'woocommerce-avatax');
			$diff_value = wc_price($total_diff);
		} else {
			$diff_label = __('No differences', 'woocommerce-avatax');
			$diff_value = '';
		}
		return array(
			'order_id' => $order_id,
			'document_code' => isset($order_row['document_code']) ? $order_row['document_code'] : '',
			'order_date' => $order_date,
			'wc_total' => wc_price($wc_total),
			'wc_tax' => wc_price($wc_tax),
			'avalara_total' => wc_price($avalara_total),
			'avalara_tax' => wc_price($avalara_tax),
			'difference' => $diff_label,
			'difference_value' => $diff_value,
			'all_differences' => array(),
		);
	}

	/**
	 * Build comparison structure from batches table for this session (overview + load missing/mismatch by ID).
	 * Used when full data is needed; prefer get_reconciliation_overview_from_batches + get_*_page for batched display.
	 *
	 * @since 3.8.0
	 * @param string $session_id Session UUID
	 * @return array Same structure as compare_data() for format_overview_data / format_missing_orders_data / format_mismatches_data
	 */
	protected function get_reconciliation_data_from_batches($session_id)
	{
		global $wpdb;
		$table = $wpdb->prefix . 'wc_avatax_reconciliation_batches';
		$r = $wpdb->get_row($wpdb->prepare(
			"SELECT missing_order_ids, mismatch_order_ids, orders_in_batch, avalara_in_batch FROM %i WHERE session_id = %s LIMIT 1",
			$table,
			$session_id
		), ARRAY_A);
		if (empty($r)) {
			return array(
				'wc_orders' => array(),
				'avalara_transactions' => array(),
				'missing' => array(),
				'mismatches' => array(),
				'matched' => array(),
				'extra_in_avalara' => array(),
			);
		}
		$total_wc = (int) $r['orders_in_batch'];
		$total_avalara = (int) $r['avalara_in_batch'];
		$all_missing_ids = json_decode($r['missing_order_ids'], true);
		$all_missing_ids = is_array($all_missing_ids) ? array_values(array_unique(array_filter($all_missing_ids))) : array();
		$all_mismatch_ids = json_decode($r['mismatch_order_ids'], true);
		$all_mismatch_ids = is_array($all_mismatch_ids) ? array_values(array_filter($all_mismatch_ids, 'is_array')) : array();
		$total_missing = count($all_missing_ids);
		$total_mismatch = count($all_mismatch_ids);
		$total_matched = $total_wc - $total_missing - $total_mismatch;
		$total_extra = $total_avalara - $total_matched - $total_mismatch;

		$missing_orders = array();
		foreach ($all_missing_ids as $order_id) {
			$order_row = $this->build_order_row_from_order_id($order_id);
			if ($order_row) {
				$missing_orders[] = $order_row;
			}
		}
		$mismatches = array();
		foreach ($all_mismatch_ids as $stored_row) {
			$mismatch_entry = $this->format_stored_mismatch_row($stored_row);
			if ($mismatch_entry) {
				$mismatches[] = $mismatch_entry;
			}
		}

		return array(
			'wc_orders' => array_fill(0, max(0, $total_wc), null),
			'avalara_transactions' => array_fill(0, max(0, $total_avalara), null),
			'missing' => $missing_orders,
			'mismatches' => $mismatches,
			'matched' => array_fill(0, max(0, $total_matched), null),
			'extra_in_avalara' => array_fill(0, max(0, $total_extra), null),
		);
	}

	/**
	 * Build one order row (same shape as get_woocommerce_orders) from order ID. Returns null if order not found.
	 *
	 * @since 3.8.0
	 * @param int $order_id WC order or refund ID
	 * @return array|null Order row or null
	 */
	protected function build_order_row_from_order_id($order_id)
	{
		$order = wc_get_order($order_id);
		return $order ? $this->build_order_row_from_order($order) : null;
	}

	/**
	 * Build one order row from WC_Order / WC_Order_Refund (same shape as get_woocommerce_orders).
	 *
	 * @since 3.8.0
	 * @param \WC_Order|\WC_Order_Refund $order
	 * @return array
	 */
	protected function build_order_row_from_order($order)
	{
		$isRefund = is_a($order, 'WC_Order_Refund');
		$parentOrder = null;
		$documentCode = $order->get_meta('_wc_avatax_document_code');
		if (empty($documentCode)) {
			if ($isRefund) {
				$parentOrder = wc_get_order($order->get_parent_id());
				if ($parentOrder) {
					$documentCode = $parentOrder->get_order_key('edit') . '-' . $order->get_id();
				}
			} else {
				$documentCode = $order->get_order_key('edit');
			}
		}
		$orderForData = $isRefund && $parentOrder ? $parentOrder : $order;
		return array(
			'order_id' => $order->get_id(),
			'document_code' => $documentCode,
			'order_date' => $order->get_date_created() ? $order->get_date_created()->format('Y-m-d H:i:s') : '',
			'customer' => $this->get_customer_name($orderForData),
			'total' => $order->get_total() - $order->get_total_tax(),
			'total_tax' => $order->get_total_tax(),
			'status' => $order->get_status(),
			'currency' => $order->get_currency(),
			'order_key' => $orderForData->get_order_key('edit'),
			'refund_type' => $isRefund ? $order->get_meta('_refund_type') : '',
		);
	}

	/**
	 * Build one mismatch entry (order + avalara + differences) by loading order and fetching Avatax transaction.
	 *
	 * @since 3.8.0
	 * @param int $order_id WC order or refund ID
	 * @return array|null { order, avalara, differences } or null
	 */
	protected function build_mismatch_entry_from_order_id($order_id)
	{
		$order_row = $this->build_order_row_from_order_id($order_id);
		if (!$order_row || empty($order_row['document_code'])) {
			return null;
		}
		try {
			$api = wc_avatax()->get_api();
			$filter = "code eq '" . str_replace("'", "''", $order_row['document_code']) . "'";
			$response = $api->get_transactions(get_option('wc_avatax_company_code'), $filter);
			$txs = $response ? $response->get_transactions() : array();
			if (empty($txs)) {
				return null;
			}
			$avalara_row = $this->map_avalara_transaction_to_row($txs[0]);
			$differences = $this->find_differences($order_row, $avalara_row);
			return array(
				'order' => $order_row,
				'avalara' => $avalara_row,
				'differences' => $differences,
			);
		} catch (Exception $e) {
			wc_avatax()->log(sprintf('Reconciliation: failed to fetch Avatax for mismatch order %s: %s', $order_id, $e->getMessage()));
			return null;
		}
	}

	/**
	 * Compare WooCommerce orders with Avalara transactions.
	 *
	 * @since 3.8.0
	 *
	 * @param array $wcOrders WooCommerce orders
	 * @param array $avalaraTransactions Avalara transactions
	 * @return array Comparison results
	 */
	protected function compare_data($wcOrders, $avalaraTransactions)
	{
		$missing = array();
		$mismatches = array();
		$matched = array();

		// Create lookup map for Avalara transactions by document code
		$avalaraMap = array();
		foreach ($avalaraTransactions as $transaction) {
			$code = $transaction['document_code'];
			$avalaraMap[$code] = $transaction;
		}

		// Compare each WooCommerce order
		foreach ($wcOrders as $order) {
			$documentCode = $order['document_code'];

			// Order has no document code - not sent to AvaTax
			if (empty($documentCode)) {
				$missing[] = $order;
				continue;
			}

			// Order not found in Avalara
			if (!isset($avalaraMap[$documentCode])) {
				$missing[] = $order;
				continue;
			}

			// Order found - compare values
			$avalaraTransaction = $avalaraMap[$documentCode];
			$differences = $this->find_differences($order, $avalaraTransaction);

			if (!empty($differences)) {
				$mismatches[] = array(
					'order' => $order,
					'avalara' => $avalaraTransaction,
					'differences' => $differences,
				);
			} else {
				$matched[] = $order;
			}

			// Remove from map (processed)
			unset($avalaraMap[$documentCode]);
		}

		// Remaining items in avalaraMap are in Avalara but not in WC
		$extraInAvalara = array_values($avalaraMap);

		return array(
			'wc_orders' => $wcOrders,
			'avalara_transactions' => $avalaraTransactions,
			'missing' => $missing,
			'mismatches' => $mismatches,
			'matched' => $matched,
			'extra_in_avalara' => $extraInAvalara,
		);
	}

	/**
	 * Find differences between an order and its Avalara transaction.
	 *
	 * @since 3.8.0
	 *
	 * @param array $order WooCommerce order data
	 * @param array $avalaraTransaction Avalara transaction data
	 * @return array Array of differences found
	 */
	protected function find_differences($order, $avalaraTransaction)
	{
		$differences = array();
		$tolerance = 0.01; // Allow 1 cent difference due to rounding

		$wc_total = (float) (isset($order['total']) ? $order['total'] : 0);
		$wc_tax = (float) (isset($order['total_tax']) ? $order['total_tax'] : 0);
		$avalara_amount = (float) (isset($avalaraTransaction['total_amount']) ? $avalaraTransaction['total_amount'] : 0);
		$avalara_tax = (float) (isset($avalaraTransaction['total_tax']) ? $avalaraTransaction['total_tax'] : 0);

		// Full refund: WC and AvaTax both normalized to total=(amount+tax), total_tax=0 for comparison.
		$refund_type = isset($order['refund_type']) ? $order['refund_type'] : '';
		if ($refund_type === 'full') {
			$avalara_amount = $avalara_amount + $avalara_tax;
			$wc_total = $wc_total + $wc_tax;
			$avalara_tax = 0.0;
			$wc_tax = 0.0;
		}

		// Compare total amount
		$totalDiff = abs($wc_total - $avalara_amount);
		if ($totalDiff > $tolerance) {
			$differences[] = array(
				'field' => 'total',
				'wc_value' => $wc_total,
				'avalara_value' => $avalara_amount,
				'difference' => $totalDiff,
			);
		}

		// Compare tax amount
		$taxDiff = abs($wc_tax - $avalara_tax);
		if ($taxDiff > $tolerance) {
			$differences[] = array(
				'field' => 'tax',
				'wc_value' => $wc_tax,
				'avalara_value' => $avalara_tax,
				'difference' => $taxDiff,
			);
		}

		return $differences;
	}

	/**
	 * Format overview data for the Overview tab.
	 *
	 * @since 3.8.0
	 *
	 * @param array $comparison Comparison results
	 * @return array Formatted overview data
	 */
	protected function format_overview_data($comparison)
	{
		return array(
			'wc_orders' => count($comparison['wc_orders']),
			'avalara_transactions' => count($comparison['avalara_transactions']),
			'missing_in_avalara' => count($comparison['missing']),
			'mismatches' => count($comparison['mismatches']),
			'matched' => count($comparison['matched']),
			'extra_in_avalara' => count($comparison['extra_in_avalara']),
		);
	}

	/**
	 * Format missing orders data for the Missing Orders tab.
	 *
	 * @since 3.8.0
	 *
	 * @param array $missingOrders Array of missing orders
	 * @return array Formatted missing orders data
	 */
	protected function format_missing_orders_data($missingOrders)
	{
		$formatted = array();

		foreach ($missingOrders as $order) {
			$formatted[] = array(
				'order_id' => $order['order_id'],
				'document_code' => $order['document_code'] ?? '',
				'order_date' => date('Y-m-d H:i', strtotime($order['order_date'])),
				'customer' => $order['customer'],
				'total' => wc_price($order['total']),
				'tax' => wc_price($order['total_tax']),
				'status' => ucfirst($order['status']),
			);
		}

		return array(
			'orders' => $formatted,
			'count' => count($formatted),
		);
	}

	/**
	 * Format mismatches data for the Mismatches tab.
	 *
	 * @since 3.8.0
	 *
	 * @param array $mismatches Array of mismatched orders
	 * @return array Formatted mismatches data
	 */
	protected function format_mismatches_data($mismatches)
	{
		$formatted = array();

		foreach ($mismatches as $mismatch) {
			$order = $mismatch['order'];
			$avalara = $mismatch['avalara'];
			$differences = $mismatch['differences'];

			// Determine primary difference for display
			$primaryDiff = $this->get_primary_difference($differences);

			$formatted[] = array(
				'order_id' => $order['order_id'],
				'document_code' => $order['document_code'] ?? '',
				'order_date' => date('Y-m-d H:i', strtotime($order['order_date'])),
				'wc_total' => wc_price($order['total']),
				'wc_tax' => wc_price($order['total_tax']),
				'avalara_total' => wc_price($avalara['total_amount']),
				'avalara_tax' => wc_price($avalara['total_tax']),
				'difference' => $primaryDiff['label'],
				'difference_value' => $primaryDiff['value'],
				'all_differences' => $differences,
			);
		}

		return array(
			'mismatches' => $formatted,
			'count' => count($formatted),
		);
	}

	/**
	 * Search by specific transaction ID or order ID.
	 *
	 * @since 3.8.0
	 *
	 * @param string $searchTerm Order ID or transaction code
	 * @return array Search results
	 */
	protected function search_by_transaction($searchTerm)
	{
		$searchTerm = sanitize_text_field($searchTerm);
		
		// Try to find WooCommerce order
		$order = wc_get_order($searchTerm);
		
		if (!$order) {
			// Try searching by document code
			$orders = wc_get_orders(array(
				'meta_key' => '_wc_avatax_document_code',
				'meta_value' => $searchTerm,
				'limit' => 1,
			));
			
			$order = !empty($orders) ? $orders[0] : null;
		}

		if (!$order) {
			return array(
				'overview' => array(
					'wc_orders' => 0,
					'avalara_transactions' => 0,
					'missing_in_avalara' => 0,
					'mismatches' => 0,
				),
				'missing_orders' => array('orders' => array(), 'count' => 0),
				'mismatches' => array('mismatches' => array(), 'count' => 0),
			);
		}

	// Check if this is a refund (WC_Order_Refund) or regular order (WC_Order)
	$isRefund = is_a($order, 'WC_Order_Refund');
	$parentOrder = null;
	
	// Determine document type
	if ($isRefund) {
		$documentType = 'ReturnInvoice';
	} else {
		$orderStatus = $order->get_status();
		$documentType = ($orderStatus === 'refunded') ? 'ReturnInvoice' : 'SalesInvoice';
	}

	// Get AvaTax document code (transaction code)
	// First try to get from meta, fallback to generating it the same way as Avalara API does
	$documentCode = $order->get_meta('_wc_avatax_document_code');
	
	if (empty($documentCode)) {
		if ($isRefund) {
			// For refunds, document code is: parent_order_key-refund_id
			// See: class-wc-avatax-api-tax-request.php line 816
			$parentOrder = wc_get_order($order->get_parent_id());
			if ($parentOrder) {
				$documentCode = $parentOrder->get_order_key('edit') . '-' . $order->get_id();
			}
		} else {
			// For regular orders, document code is just the order key
			// See: class-wc-avatax-api-tax-request.php line 504
			$documentCode = $order->get_order_key('edit');
		}
	}

	// For refunds, use parent order for customer info
	$orderForData = $isRefund && $parentOrder ? $parentOrder : $order;

		// Format single order for comparison
		$wcOrders = array(
			array(
				'order_id' => $order->get_id(),
				'document_code' => $documentCode,
				'order_date' => $order->get_date_created()->format('Y-m-d H:i:s'),
				'customer' => $this->get_customer_name($orderForData),
				'total' => $order->get_total() - $order->get_total_tax(), // Subtotal excluding tax (to match Avalara's totalAmount)
				'total_tax' => $order->get_total_tax(),
				'status' => $order->get_status(),
				'currency' => $order->get_currency(),
				'document_type' => $documentType,
				'refund_type' => $isRefund ? $order->get_meta('_refund_type') : '',
			)
		);

		// Try to fetch corresponding Avalara transaction
		$documentCode = $wcOrders[0]['document_code'];
		$orderDocumentType = $wcOrders[0]['document_type'];
		$avalaraTransactions = array();

		if (!empty($documentCode)) {
			try {
				$api = wc_avatax()->get_api();
				$companyCode = get_option('wc_avatax_company_code');
				
				// Search for transaction by code and type using filter
				$filter = sprintf("code eq '%s' and type eq '%s'", $documentCode, $orderDocumentType);
				$response = $api->get_transactions($companyCode, $filter);
				
				if ($response && $response->get_transactions()) {
					$transactionsArray = $response->get_transactions();
					if (!empty($transactionsArray)) {
						$transaction = $transactionsArray[0];
						$avalaraTransactions[] = array(
							'code' => $transaction->code ?? '',
							'document_code' => $transaction->code ?? '',
							'date' => $transaction->date ?? '',
							'total_amount' => $transaction->totalAmount ?? 0,
							'total_tax' => $transaction->totalTax ?? 0,
							'status' => $transaction->status ?? '',
							'type' => $transaction->type ?? '',
						);
					}
				}
			} catch (Exception $e) {
				// Transaction not found or API error
				wc_avatax()->log(sprintf(
					'Failed to fetch Avalara transaction for code %s: %s',
					$documentCode,
					$e->getMessage()
				));
			}
		}

		// Compare
		$comparison = $this->compare_data($wcOrders, $avalaraTransactions);

		return array(
			'overview' => $this->format_overview_data($comparison),
			'missing_orders' => $this->format_missing_orders_data($comparison['missing']),
			'mismatches' => $this->format_mismatches_data($comparison['mismatches']),
		);
	}

	/**
	 * Get order statuses based on document type.
	 *
	 * @since 3.8.0
	 *
	 * @param string $documentType Document type filter (SalesInvoice or ReturnInvoice)
	 * @return array Array of order statuses to query
	 */
	protected function get_order_statuses_by_document_type($documentType)
	{
		switch ($documentType) {
			case 'ReturnInvoice':
				// Note: This case is not actually used in queries
				// We query refund objects directly using 'type' => 'shop_order_refund'
				// Kept for backward compatibility
				return array('wc-refunded');
				
			case 'SalesInvoice':
			default:
				// Regular sales orders (not refunded)
				// Include on-hold orders as they may have tax calculated
				return array('wc-completed', 'wc-processing', 'wc-on-hold');
		}
	}

	/**
	 * Get customer name from order.
	 *
	 * @since 3.8.0
	 *
	 * @param WC_Order $order WooCommerce order
	 * @return string Customer name
	 */
	protected function get_customer_name($order)
	{
		$firstName = $order->get_billing_first_name();
		$lastName = $order->get_billing_last_name();
		
		if ($firstName || $lastName) {
			return trim($firstName . ' ' . $lastName);
		}
		
		// Fallback to email or "Guest"
		$email = $order->get_billing_email();
		return $email ? $email : __('Guest', 'woocommerce-avatax');
	}

	/**
	 * Get primary difference to display.
	 *
	 * @since 3.8.0
	 *
	 * @param array $differences Array of differences
	 * @return array Primary difference with label and value
	 */
	protected function get_primary_difference($differences)
	{
		if (empty($differences)) {
			return array(
				'label' => __('No differences', 'woocommerce-avatax'),
				'value' => '',
			);
		}

		// Prioritize tax differences
		foreach ($differences as $diff) {
			if ($diff['field'] === 'tax') {
				return array(
					'label' => __('Tax amount', 'woocommerce-avatax'),
					'value' => wc_price($diff['difference']),
				);
			}
		}

	// Then total differences
	foreach ($differences as $diff) {
		if ($diff['field'] === 'total') {
			return array(
				'label' => __('Total amount', 'woocommerce-avatax'),
				'value' => wc_price($diff['difference']),
			);
		}
	}

	// Return first difference (status check removed)
	$first = $differences[0];
	return array(
		'label' => ucfirst($first['field']),
		'value' => __('Mismatch detected', 'woocommerce-avatax'),
	);
}
}
