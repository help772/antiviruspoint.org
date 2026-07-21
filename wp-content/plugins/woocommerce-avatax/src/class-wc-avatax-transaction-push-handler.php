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
 * Transaction Push Handler for pushing WooCommerce order data to CCS.
 *
 * Handles background push of WooCommerce orders to the CCS integration API
 * when a customer successfully connects to AvaTax sandbox or production environment.
 *
 * ## Architecture Overview
 *
 * This handler uses a file-based storage approach to efficiently process large datasets
 * (up to 100K orders) without exhausting PHP memory limits.
 *
 * ### Processing Flow:
 * 1. Create temporary file with JSON header: {"transactions":[
 * 2. Orders are queried in batches (default: 1000 orders per batch)
 * 3. Each batch is transformed and streamed to file as comma-separated JSON objects
 * 4. Memory is freed between batches via cache clearing and garbage collection
 * 5. After all batches, finalize file with closing ]}
 * 6. File path passed directly to API (no memory copy)
 * 7. File automatically cleaned up after successful API call
 *
 * ### Key Features:
 * - **File-based streaming**: Writes JSON array format {"transactions":[...]} directly to file
 * - **Zero-copy upload**: File path passed directly to API, no read-back into memory
 * - **Memory optimization**: Clears WordPress caches and triggers garbage collection between batches
 * - **Timeout protection**: Stops processing after 4 minutes to prevent execution timeouts
 * - **Automatic cleanup**: Removes temporary files older than 1 hour from previous failed runs
 * - **Background processing**: Uses wp_schedule_single_event() to run asynchronously
 *
 * ### File Storage Location:
 * Temporary files are stored in: `wp-content/uploads/wc-avatax-temp/`
 * Files are protected with .htaccess rules and index.php to prevent direct access.
 *
 * @since 3.6.4
 */
class WC_AvaTax_Transaction_Push_Handler
{

	/**
	 * Maximum number of transactions to push in a single first-call operation.
	 * Limits are in place to prevent excessive API payload sizes.
	 *
	 * @var int
	 */
	const MAX_TRANSACTIONS = 100000;

	/**
	 * Number of orders to process in each batch.
	 * Balances memory usage against processing efficiency.
	 * Lower values use less memory but require more file I/O.
	 *
	 * @var int
	 */
	const BATCH_SIZE = 1000;

	/**
	 * WordPress option key to track if the first push has been completed.
	 * Once set to true, subsequent connections only send firstCall=false.
	 *
	 * @var string
	 */
	const PUSH_COMPLETED_OPTION = 'wc_avatax_transaction_push_completed';

	/**
	 * Transient key to track if a push is currently in progress.
	 * Prevents concurrent push operations. Expires after 1 hour.
	 *
	 * @var string
	 */
	const PUSH_IN_PROGRESS_TRANSIENT = 'wc_avatax_transaction_push_in_progress';

	/**
	 * Prefix for temporary file names.
	 * Used to identify and clean up orphaned files from failed runs.
	 * Files are named: {prefix}{uniqid}_{timestamp}.json
	 *
	 * @var string
	 */
	const TEMP_FILE_PREFIX = 'wc_avatax_transactions_';

	/**
	 * Path to the current temporary file for storing transactions.
	 * Set during process_orders_in_batches() and cleared after cleanup.
	 *
	 * @var string|null
	 */
	private $temp_file_path = null;

	/**
	 * Flag to track if first transaction has been written to file.
	 * Used to determine whether to add comma separator between transactions.
	 *
	 * @var bool
	 */
	private $first_transaction_written = false;


	/**
	 * Constructor.
	 *
	 * @since 3.6.4
	 */
	public function __construct()
	{
		// Hook registration is handled in main plugin class (class-wc-avatax.php)
		// to avoid loading this class on every request
	}

	/**
	 * Triggers the transaction push to run in the background.
	 *
	 * This should be called when a customer successfully connects to AvaTax.
	 * First call: Sends full transaction data with firstCall=true
	 * Subsequent calls: Sends empty data with firstCall=false
	 * Uses wp_schedule_single_event() to run in the background.
	 *
	 * @since 3.6.4
	 *
	 * @return bool True if push was triggered successfully, false if already in progress
	 */
	public function trigger_push()
	{
		// Check if push is already in progress
		if ($this->is_push_in_progress()) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Transaction push to CCS already in progress. Skipping.');
			}
			return false;
		}

		try {
			// Mark push as in progress (expires in 1 hour)
			set_transient(self::PUSH_IN_PROGRESS_TRANSIENT, true, HOUR_IN_SECONDS);

			if (wc_avatax()->logging_enabled()) {
				$isFirstCall = ! $this->is_push_completed();
				wc_avatax()->log(
					sprintf(
						'Transaction push to CCS triggered (%s call). Running in background...',
						$isFirstCall ? 'first' : 'subsequent'
					)
				);
			}

			// Schedule the push to run in the background (10 seconds from now)
			$scheduledTime = time() + 10;
			wp_schedule_single_event($scheduledTime, 'wc_avatax_process_transaction_push');

			return true;

		} catch (Exception $e) {

			// Clear the in-progress flag on error
			delete_transient(self::PUSH_IN_PROGRESS_TRANSIENT);

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Failed to trigger transaction push: ' . $e->getMessage());
			}

			// Logging error
			wc_avatax()->logger()->log_exception("TransactionPush", "trigger_push", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
	}


	/**
	 * Handles the transaction push background job.
	 *
	 * First call: Queries WooCommerce orders from the past year (max 100K records),
	 * writes them directly to a temporary JSON file in the final format
	 * {"transactions":[...]} and passes the file path to the API for upload.
	 * Subsequent calls: Sends empty data with firstCall=false.
	 *
	 * Uses file-based storage to handle large datasets without loading into memory.
	 * The file is written in streaming fashion and uploaded directly.
	 * Cleans up temporary files automatically after push or on error.
	 *
	 * @since 3.6.4
	 */
	public function handle_transaction_push()
	{
		wc_avatax()->logger()->log_event("TransactionPush", "handle_transaction_push", "Starting transaction push...");

		$this->log_if_enabled('Starting transaction push to CCS...');

		try {
			// Clean up any old temporary files from previous runs
			$this->cleanup_old_temp_files();
			
			$isFirstCall = ! $this->is_push_completed();
			$payload = $isFirstCall ? $this->handle_first_call() : $this->handle_subsequent_call();

			// Early return if no payload to send
			if ($payload === null) {
				delete_transient(self::PUSH_IN_PROGRESS_TRANSIENT);
				return;
			}

			$this->send_to_ccs_api($payload, $isFirstCall);
			$this->finalize_push($isFirstCall, $payload);
			
			wc_avatax()->logger()->log_event("TransactionPush", "handle_transaction_push", "Transaction push completed. First call: " . ($isFirstCall ? 'true' : 'false'));

		} catch (Exception $e) {
			// Ensure temp file is cleaned up on error
			$this->cleanup_temp_file();
			$this->handle_push_error($e);
		}
	}

	/**
	 * Handles the first transaction push call.
	 *
	 * Processes orders in batches, writing transactions directly to a temporary file
	 * in the final JSON format {"transactions":[...]}. Returns the file path for
	 * direct upload without loading data into memory.
	 *
	 * @since 3.6.4
	 * @return string|null Path to the JSON file, or null if no data to push
	 */
	protected function handle_first_call()
	{
		$totalCount = min($this->get_total_order_count(), self::MAX_TRANSACTIONS);

		if ($totalCount === 0) {
			$this->log_if_enabled('No orders found for transaction push.');
			return null;
		}

		$this->log_if_enabled(
			sprintf('Found %d orders to push. Processing in batches of %d...', $totalCount, self::BATCH_SIZE)
		);

		try {
			// Process orders and write to temporary file (returns file path)
			$tempFilePath = $this->process_orders_in_batches($totalCount);
			
			if (!$tempFilePath || !file_exists($tempFilePath)) {
				$this->log_if_enabled('Failed to process orders: temporary file not created');
				return null;
			}

			// Finalize the JSON file (close the array and object)
			if (!$this->finalize_temp_file()) {
				$this->cleanup_temp_file();
				$this->log_if_enabled('Failed to finalize temporary file.');
				return null;
			}

			// Get file size for logging
			$fileSize = filesize($tempFilePath);
			$this->log_if_enabled(
				sprintf('First push: File ready with %d orders. File size: %s', 
					$totalCount, 
					size_format($fileSize)
				)
			);

			// Return the file path - request class will read and send directly
			// Temp file cleanup happens in finalize_push() after successful send
			return $tempFilePath;
			
		} catch (Exception $e) {
			// Clean up temp file on error
			$this->cleanup_temp_file();
			throw $e;
		}
	}

	/**
	 * Handles subsequent transaction push calls.
	 *
	 * @since 3.6.4
	 * @return array Empty payload for subsequent calls
	 */
	protected function handle_subsequent_call()
	{
		$this->log_if_enabled('Subsequent push: Sending API call with firstCall=false...');
		return [];
	}

	/**
	 * Finalizes the push by marking completion, cleaning up, and logging success.
	 *
	 * @since 3.6.4
	 * @since 3.6.4 Added temp file cleanup after successful upload
	 * @param bool         $isFirstCall Whether this was the first call
	 * @param string|array $payload     The file path or payload that was sent
	 */
	protected function finalize_push($isFirstCall, $payload)
	{
		if ($isFirstCall) {
			// Clean up the temp file after successful upload
			$this->cleanup_temp_file();
			
			$this->mark_push_completed();
			wc_avatax()->logger()->log_event(
				"TransactionPush",
				"handle_transaction_push",
				"Successfully pushed transactions to CCS. First push completed."
			);
		} else {
			wc_avatax()->logger()->log_event(
				"TransactionPush",
				"handle_transaction_push",
				"Subsequent push call sent successfully with firstCall=false."
			);
		}
		
		delete_transient(self::PUSH_IN_PROGRESS_TRANSIENT);
	}

	/**
	 * Handles errors during transaction push.
	 *
	 * @since 3.6.4
	 * @param Exception $e The exception that occurred
	 */
	protected function handle_push_error($e)
	{
		delete_transient(self::PUSH_IN_PROGRESS_TRANSIENT);
		$this->log_if_enabled('Transaction push failed: ' . $e->getMessage());
		wc_avatax()->logger()->log_exception(
			"TransactionPush",
			"handle_transaction_push",
			$e->getMessage(),
			$e->getTraceAsString()
		);
	}

	/**
	 * Helper method to log messages if logging is enabled.
	 *
	 * @since 3.6.4
	 * @param string $message The message to log
	 */
	protected function log_if_enabled($message)
	{
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log($message);
		}
	}


	/**
	 * Processes orders in batches and writes them to a temporary file.
	 *
	 * This method is the core of the file-based storage approach. It prevents
	 * memory exhaustion when processing large numbers of orders by:
	 *
	 * 1. Processing orders in configurable batch sizes (BATCH_SIZE constant)
	 * 2. Writing each batch to a temporary file immediately
	 * 3. Clearing WordPress caches and freeing memory after each batch
	 * 4. Triggering garbage collection to reclaim memory
	 * 5. Implementing timeout protection (4 minutes) to prevent execution limits
	 *
	 * @since 3.6.4
	 *
	 * @param int $totalCount Total number of orders to process
	 * @return string Path to the temporary file containing transactions
	 * @throws Exception If temporary file cannot be created
	 */
	protected function process_orders_in_batches($totalCount)
	{
		// Create temporary file for storing transactions
		$this->temp_file_path = $this->create_temp_file();
		
		if (!$this->temp_file_path) {
			throw new Exception('Failed to create temporary file for transaction storage');
		}

		$offset = 0;
		$batchNumber = 0;
		$processedCount = 0;
		$totalTransactions = 0;

		try {
			while ($offset < $totalCount && $processedCount < self::MAX_TRANSACTIONS) {

				$batchNumber++;
				$remaining = min(self::BATCH_SIZE, $totalCount - $offset, self::MAX_TRANSACTIONS - $processedCount);

				if (wc_avatax()->logging_enabled()) {
					wc_avatax()->log(
						sprintf(
							'Processing batch %d: orders %d-%d of %d',
							$batchNumber,
							$offset + 1,
							$offset + $remaining,
							$totalCount
						)
					);
				}

				// Get batch of orders
				$batchOrders = $this->get_orders_batch($offset, $remaining);

				if (empty($batchOrders)) {
					break; // No more orders
				}

				// Transform this batch
				$batchTransactions = $this->transform_orders_to_transactions($batchOrders);
				
				// Write batch to file instead of accumulating in memory
				$written = $this->write_transactions_to_file($batchTransactions);
				$totalTransactions += $written;

				$processedCount += count($batchOrders);
				$offset += $remaining;

				// Log progress
				if (wc_avatax()->logging_enabled()) {
					wc_avatax()->log(
						sprintf(
							'Batch %d completed: %d transactions written to file (Total: %d)',
							$batchNumber,
							$written,
							$totalTransactions
						)
					);
				}

				// Free memory - critical for preventing memory exhaustion
				unset($batchTransactions);
				$this->clear_order_caches_for_batch($batchOrders);
				unset($batchOrders);
				
				// Force garbage collection
				if (function_exists('gc_collect_cycles')) {
					gc_collect_cycles();
				}

				// Prevent timeout - if we've been running for more than 4 minutes, stop
				if (function_exists('hrtime')) {
					static $startTime;
					if (!isset($startTime)) {
						$startTime = hrtime(true);
					}
					$elapsedSeconds = (hrtime(true) - $startTime) / 1e9;
					if ($elapsedSeconds > 240) { // 4 minutes
						if (wc_avatax()->logging_enabled()) {
							wc_avatax()->log(
								sprintf(
									'Push timeout protection: processed %d transactions in %.2f seconds. Stopping batch processing.',
									$totalTransactions,
									$elapsedSeconds
								)
							);
						}
						break;
					}
				}
			}

			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log(
					sprintf(
						'Batch processing completed: %d total transactions written to file. File size: %s',
						$totalTransactions,
						file_exists($this->temp_file_path) ? size_format(filesize($this->temp_file_path)) : 'unknown'
					)
				);
			}

			return $this->temp_file_path;
			
		} catch (Exception $e) {
			// Clean up temp file on error
			$this->cleanup_temp_file();
			throw $e;
		}
	}


	/**
	 * Creates a temporary file for storing transactions.
	 *
	 * @since 3.6.4
	 * @codeCoverageIgnore
	 * @return string|false Path to the temporary file, or false on failure
	 */
	protected function create_temp_file()
	{
		$upload_dir = wp_upload_dir();
		$temp_dir = trailingslashit($upload_dir['basedir']) . 'wc-avatax-temp';
		
		// Create temp directory if it doesn't exist
		if (!file_exists($temp_dir)) {
			wp_mkdir_p($temp_dir);
			
			// Add .htaccess to protect the directory
			file_put_contents(
				$temp_dir . '/.htaccess',
				"Order deny,allow\nDeny from all"
			);
			
			// Add index.php for extra protection
			file_put_contents(
				$temp_dir . '/index.php',
				"<?php\n// Silence is golden."
			);
		}
		
		// Create unique temporary file
		$filename = self::TEMP_FILE_PREFIX . uniqid() . '_' . time() . '.json';
		$filepath = $temp_dir . '/' . $filename;
		
		// Create the file with opening JSON structure
		// Format: {"transactions":[...]} - will be closed by finalize_temp_file()
		if (file_put_contents($filepath, '{"transactions":[') === false) {
			return false;
		}
		
		// Reset the first transaction flag
		$this->first_transaction_written = false;
		
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log('Created temporary file: ' . basename($filepath));
		}
		
		return $filepath;
	}


	/**
	 * Writes transactions to the temporary file as JSON array elements.
	 *
	 * Writes transactions in streaming fashion as part of a JSON array.
	 * File format: {"transactions":[{...},{...},{...}]}
	 * 
	 * Uses comma separation (not newlines) to create valid JSON array.
	 * The file is opened by create_temp_file() with the opening bracket
	 * and closed by finalize_temp_file() with the closing bracket.
	 *
	 * @since 3.6.4
	 *
	 * @param array $transactions Array of transaction data
	 * @return int Number of transactions successfully written
	 * @throws Exception If file cannot be opened for writing
	 */
	protected function write_transactions_to_file($transactions)
	{
		if (empty($transactions) || !$this->temp_file_path) {
			return 0;
		}
		
		$count = 0;
		
		// Open file in append mode
		$handle = fopen($this->temp_file_path, 'a');
		
		if (!$handle) {
			throw new Exception('Failed to open temporary file for writing');
		}
		
		// Set serialize_precision to -1 to prevent floating-point precision issues
		// PHP 7.1+ uses a smarter algorithm that outputs the shortest representation
		$oldPrecision = ini_get('serialize_precision');
		ini_set('serialize_precision', -1);
		
		try {
			foreach ($transactions as $transaction) {
				// Encode transaction as compact JSON
				// JSON_UNESCAPED_SLASHES: Prevents escaping "/" as "\/" (saves bytes)
				// JSON_UNESCAPED_UNICODE: Outputs UTF-8 directly instead of \uXXXX escapes
				$json = json_encode($transaction, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
				
				if ($json === false) {
					if (wc_avatax()->logging_enabled()) {
						wc_avatax()->log(
							'Failed to encode transaction: ' . json_last_error_msg()
						);
					}
					continue;
				}
				
				// Add comma separator between transactions (not before first one)
				$prefix = $this->first_transaction_written ? ',' : '';
				
				if (fwrite($handle, $prefix . $json) !== false) {
					$count++;
					$this->first_transaction_written = true;
				}
			}
			
		} finally {
			// Restore original precision setting
			ini_set('serialize_precision', $oldPrecision);
			fclose($handle);
		}
		
		return $count;
	}

	/**
	 * Finalizes the temporary file by closing the JSON structure.
	 *
	 * Writes the closing ]} to complete the {"transactions":[...]} format.
	 * Must be called after all transactions have been written.
	 *
	 * @since 3.6.4
	 * @return bool True on success, false on failure
	 */
	protected function finalize_temp_file()
	{
		if (!$this->temp_file_path || !file_exists($this->temp_file_path)) {
			return false;
		}
		
		// Append closing brackets to complete the JSON structure
		$result = file_put_contents($this->temp_file_path, ']}', FILE_APPEND);
		
		if ($result !== false && wc_avatax()->logging_enabled()) {
			wc_avatax()->log('Finalized temporary file: ' . basename($this->temp_file_path));
		}
		
		return $result !== false;
	}


	/**
	 * Reads all transactions from the temporary file.
	 *
	 * Reads the NDJSON file line by line, decoding each JSON line into a
	 * transaction array. Includes periodic garbage collection (every 10,000 lines)
	 * to manage memory when reading very large files.
	 *
	 * Invalid JSON lines are logged and skipped rather than causing failure.
	 *
	 * @since 3.6.4
	 *
	 * @return array Array of all transactions read from file
	 * @throws Exception If file cannot be opened for reading
	 */
	protected function read_transactions_from_file()
	{
		if (!$this->temp_file_path || !file_exists($this->temp_file_path)) {
			return [];
		}
		
		$transactions = [];
		$handle = fopen($this->temp_file_path, 'r');
		
		if (!$handle) {
			throw new Exception('Failed to open temporary file for reading');
		}
		
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log(
				sprintf(
					'Reading transactions from file. File size: %s',
					size_format(filesize($this->temp_file_path))
				)
			);
		}
		
		try {
			$lineNumber = 0;
			
			// Read file line by line (newline-delimited JSON)
			while (($line = fgets($handle)) !== false) {
				$lineNumber++;
				$line = trim($line);
				
				if (empty($line)) {
					continue;
				}
				
				$transaction = json_decode($line, true);
				
				if ($transaction === null && json_last_error() !== JSON_ERROR_NONE) {
					if (wc_avatax()->logging_enabled()) {
						wc_avatax()->log(
							sprintf(
								'Failed to decode transaction at line %d: %s',
								$lineNumber,
								json_last_error_msg()
							)
						);
					}
					continue;
				}
				
				$transactions[] = $transaction;
				
				// Optional: Clear memory periodically if reading very large files
				if ($lineNumber % 10000 === 0) {
					if (function_exists('gc_collect_cycles')) {
						gc_collect_cycles();
					}
				}
			}
			
		} finally {
			fclose($handle);
		}
		
		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log(
				sprintf('Successfully read %d transactions from file', count($transactions))
			);
		}
		
		return $transactions;
	}


	/**
	 * Cleans up the temporary file.
	 *
	 * @since 3.6.4
	 */
	protected function cleanup_temp_file()
	{
		if ($this->temp_file_path && file_exists($this->temp_file_path)) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Cleaning up temporary file: ' . basename($this->temp_file_path));
			}
			
			@unlink($this->temp_file_path);
			$this->temp_file_path = null;
		}
	}


	/**
	 * Cleans up old temporary files (older than 1 hour).
	 *
	 * This is a safety measure to clean up orphaned files from failed previous runs.
	 * Called at the start of each transaction push to ensure the temp directory
	 * doesn't accumulate stale files over time.
	 *
	 * Files are identified by the TEMP_FILE_PREFIX pattern and their modification time.
	 *
	 * @since 3.6.4
	 */
	protected function cleanup_old_temp_files()
	{
		$upload_dir = wp_upload_dir();
		$temp_dir = trailingslashit($upload_dir['basedir']) . 'wc-avatax-temp';
		
		if (!file_exists($temp_dir)) {
			return;
		}
		
		// @codeCoverageIgnoreStart
		$files = glob($temp_dir . '/' . self::TEMP_FILE_PREFIX . '*.json');
		
		if (empty($files)) {
			return;
		}
		
		$oneHourAgo = time() - HOUR_IN_SECONDS;
		$deletedCount = 0;
		
		foreach ($files as $file) {
			if (is_file($file) && filemtime($file) < $oneHourAgo) {
				if (@unlink($file)) {
					$deletedCount++;
				}
			}
		}
		
		if ($deletedCount > 0 && wc_avatax()->logging_enabled()) {
			wc_avatax()->log(
				sprintf('Cleaned up %d old temporary transaction files', $deletedCount)
			);
		}
		// @codeCoverageIgnoreEnd
	}


	/**
	 * Clears WordPress caches for processed orders to prevent memory buildup.
	 *
	 * WordPress and WooCommerce cache order data in memory. When processing
	 * thousands of orders, this cache can grow very large. This method
	 * explicitly clears relevant caches after each batch to free memory:
	 *
	 * - Order object cache
	 * - Post meta cache
	 * - Post cache
	 * - Object cache (full flush)
	 *
	 * @since 3.6.4
	 *
	 * @param array $orders Array of WC_Order objects to clear from cache
	 */
	protected function clear_order_caches_for_batch($orders)
	{
		if (!is_array($orders)) {
			return;
		}
		
		foreach ($orders as $order) {
			if ($order instanceof WC_Order) {
				$order_id = $order->get_id();
				
				// Clear various WordPress caches
				wp_cache_delete('order-' . $order_id, 'orders');
				wp_cache_delete($order_id, 'post_meta');
				wp_cache_delete($order_id, 'posts');
				clean_post_cache($order_id);
			}
		}
		
		// Clear object cache
		wp_cache_flush();
	}


	/**
	 * Gets base query arguments for orders.
	 *
	 * @since 3.6.4
	 *
	 * @return array Query arguments
	 */
	protected function get_base_order_query_args()
	{
		$oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));

		$args = [
			'orderby'        => 'date',
			'order'          => 'DESC',
			'date_created'   => '>=' . $oneYearAgo,
			'return'         => 'objects',
			'type'           => 'shop_order',
			'status'         => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ], // Include common order statuses
			'paginate'       => false, // We handle pagination manually
		];

		/**
		 * Filters the query arguments for getting orders to push.
		 *
		 * @since 3.6.4
		 *
		 * @param array $args Query arguments
		 */
		return apply_filters('wc_avatax_transaction_push_order_query_args', $args);
	}


	/**
	 * Gets total count of orders to push.
	 *
	 * @since 3.6.4
	 *
	 * @return int Total order count
	 */
	protected function get_total_order_count()
	{
		$args = $this->get_base_order_query_args();
		$args['limit'] = 1;
		$args['return'] = 'ids';
		$args['paginate'] = true;

		$result = wc_get_orders($args);

		return isset($result->total) ? $result->total : 0;
	}


	/**
	 * Gets a batch of orders for pushing.
	 *
	 * @since 3.6.4
	 *
	 * @param int $offset Offset for the query
	 * @param int $limit  Number of orders to retrieve
	 * @return WC_Order[] Array of order objects
	 */
	protected function get_orders_batch($offset = 0, $limit = self::BATCH_SIZE)
	{
		$args = $this->get_base_order_query_args();
		$args['limit'] = $limit;
		$args['offset'] = $offset;

		return wc_get_orders($args);
	}


	/**
	 * Transforms WooCommerce orders into the transaction format required by CCS.
	 *
	 * @since 3.6.4
	 *
	 * @param WC_Order[] $orders Array of order objects
	 * @return array Array of transaction data
	 */
	protected function transform_orders_to_transactions($orders)
	{
		$transactions = [];

		foreach ($orders as $order) {

			try {
				$transaction = $this->transform_single_order($order);

				if (! empty($transaction)) {
					$transactions[] = $transaction;
				}

			} catch (Exception $e) {
				if (wc_avatax()->logging_enabled()) {
					wc_avatax()->log(sprintf('Failed to transform order #%s: %s', $order->get_id(), $e->getMessage()));
				}
				continue;
			}
		}

		return $transactions;
	}


	/**
	 * Transforms a single WooCommerce order into transaction format.
	 *
	 * Uses WooCommerce's native order data since orders were created before AvaTax was connected.
	 *
	 * @since 3.6.4
	 *
	 * @param WC_Order $order The order object
	 * @return array Transaction data
	 */
	protected function transform_single_order($order)
	{
		// Get origin address from store settings (WooCommerce store address)
		$originAddress = $this->get_origin_address($order);

		// Get destination address from WooCommerce order data (shipping or billing)
		$destinationAddress = $this->get_destination_address($order);

		// Build the transaction array using WooCommerce native data
		// Format amounts to 2 decimal points to reduce JSON file size
		$transaction = [
			'transaction_id'  => (string) $order->get_id(),
			'shipped_from'   => $originAddress,
			'shipped_to'     => $destinationAddress,
			'total_amount'   => round((float) $order->get_total(), 2), // WooCommerce total (2 decimals)
			'tax_amount'     => round((float) $order->get_total_tax(), 2), // WooCommerce tax (2 decimals)
		];

		/**
		 * Filters the transaction data before adding to the push payload.
		 *
		 * @since 3.6.4
		 *
		 * @param array    $transaction Transaction data
		 * @param WC_Order $order       Order object
		 */
		return apply_filters('wc_avatax_transaction_push_transaction_data', $transaction, $order);
	}


	/**
	 * Gets the origin address for an order.
	 *
	 * Uses WooCommerce store address (state only).
	 * This is for historical orders created before AvaTax connection,
	 * so AvaTax-specific metadata won't be available.
	 *
	 * @since 3.6.4
	 *
	 * @param WC_Order $order The order object
	 * @return string Formatted origin address (state only)
	 */
	protected function get_origin_address($order)
	{
		// Extract state from WooCommerce store address (Country:State format)
		$defaultCountry = get_option('woocommerce_default_country', '');
		$countryState = explode(':', $defaultCountry);
		
		// Get the last part (state if present, otherwise country code)
		$state = end($countryState);
		
		$originAddressData = [
			'state' => $state ? $state : '',
		];

		return $this->format_address($originAddressData);
	}


	/**
	 * Gets the destination address for an order.
	 *
	 * Uses WooCommerce's native order data (shipping or billing address).
	 * Orders were created before AvaTax connection, so we use WC data, not AvaTax meta.
	 *
	 * @since 3.6.4
	 *
	 * @param WC_Order $order The order object
	 * @return string Formatted destination address
	 */
	protected function get_destination_address($order)
	{
		$addressData = [];

		// Prefer shipping address if available (WooCommerce native data)
		// Only state is required as per the new requirement
		if ($order->has_shipping_address()) {
			$addressData = [
				'state' => $order->get_shipping_state(),
			];
		} else {
			// Fall back to billing address (WooCommerce native data)
			$addressData = [
				'state' => $order->get_billing_state(),
			];
		}

		return $this->format_address($addressData);
	}


	/**
	 * Formats address data into a string.
	 *
	 * @since 3.6.4
	 *
	 * @param array $addressData Address data array
	 * @return string Formatted address string
	 */
	protected function format_address($addressData)
	{
		if (empty($addressData)) {
			return '';
		}

		$parts = [];

		if (! empty($addressData['address_1'])) {
			$parts[] = $addressData['address_1'];
		}

		if (! empty($addressData['address_2'])) {
			$parts[] = $addressData['address_2'];
		}

		if (! empty($addressData['city'])) {
			$parts[] = $addressData['city'];
		}

		if (! empty($addressData['state'])) {
			$parts[] = $addressData['state'];
		}

		if (! empty($addressData['postcode'])) {
			$parts[] = $addressData['postcode'];
		}

		if (! empty($addressData['country'])) {
			$parts[] = $addressData['country'];
		}

		return implode(', ', array_filter($parts));
	}


	/**
	 * Sends transaction data to the Onboarding API.
	 *
	 * @since 3.6.4
	 *
	 * @param array $payload The transaction data payload
	 * @param bool $firstCall Whether this is the first call (true) or subsequent call (false)
	 * @throws Exception If API call fails
	 */
	protected function send_to_ccs_api($payload, $firstCall = true)
	{
		if (! wc_avatax()->has_api_credentials_set()) {
			throw new Exception('AvaTax API credentials are not configured.');
		}

		try {
			// Get the Onboarding API instance
			$accountNumber = get_option('wc_avatax_api_account_number');
			$licenseKey    = get_option('wc_avatax_api_license_key');
			$companyId     = get_option('wc_avatax_company_id');
			$environment   = get_option('wc_avatax_api_environment', 'production');

			$api = wc_avatax()->get_onboarding_api($accountNumber, $licenseKey, $companyId, $environment);

			if (! $api) {
				throw new Exception('Onboarding API is not available.');
			}

			// Send the transaction data with the appropriate firstCall flag
            $response = $api->send_transaction_data($payload, $firstCall);

            // Double-check response for errors (safety check in case exception wasn't thrown)
            if ($response && method_exists($response, 'has_errors') && $response->has_errors()) {
                $errors = $response->get_errors();
                $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Unknown error';
                throw new Exception('Onboarding API returned errors: ' . $errorMessage);
            }

            if (wc_avatax()->logging_enabled()) {
                wc_avatax()->log(sprintf(
                    'Transaction data sent to Onboarding API successfully (firstCall=%s).',
                    $firstCall ? 'true' : 'false'
                ));
            }

		} catch (Exception $e) {
			if (wc_avatax()->logging_enabled()) {
				wc_avatax()->log('Onboarding API call failed: ' . $e->getMessage());
			}
			throw $e;
		}
	}


	/**
	 * Checks if a push is currently in progress.
	 *
	 * @since 3.6.4
	 *
	 * @return bool True if push is in progress
	 */
	public function is_push_in_progress()
	{
		return (bool) get_transient(self::PUSH_IN_PROGRESS_TRANSIENT);
	}


	/**
	 * Checks if the push has been completed.
	 *
	 * This is a one-time operation, so once completed, it should not run again.
	 *
	 * @since 3.6.4
	 *
	 * @return bool True if push has been completed
	 */
	public function is_push_completed()
	{
		return (bool) get_option(self::PUSH_COMPLETED_OPTION, false);
	}


	/**
	 * Marks the push as completed.
	 *
	 * This prevents the push from running again.
	 *
	 * @since 3.6.4
	 */
	protected function mark_push_completed()
	{
		update_option(self::PUSH_COMPLETED_OPTION, true);

		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log('Transaction push marked as completed. This will not run again.');
		}
	}


	/**
	 * Resets the push completion flag.
	 *
	 * This allows the push to run again. Use with caution.
	 * This method is primarily for testing or manual intervention.
	 *
	 * @since 3.6.4
	 */
	public function reset_push_flag()
	{
		delete_option(self::PUSH_COMPLETED_OPTION);
		delete_transient(self::PUSH_IN_PROGRESS_TRANSIENT);

		if (wc_avatax()->logging_enabled()) {
			wc_avatax()->log('Transaction push flag has been reset. Push can now run again.');
		}
	}
}

