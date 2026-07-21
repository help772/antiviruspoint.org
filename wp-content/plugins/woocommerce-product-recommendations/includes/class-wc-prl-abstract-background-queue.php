<?php
/**
 * WC_PRL_Background_Queue class
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generator Queue class.
 *
 * @class    WC_PRL_Background_Queue
 * @version  4.3.0
 */
abstract class WC_PRL_Background_Queue {

	/**
	 * The queue identifier.
	 *
	 * @var string
	 */
	protected $identifier = '';

	/**
	 * The queue prefix.
	 *
	 * @var string
	 */
	protected $action = 'async_request';

	/**
	 * The queue data.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Start time of current process.
	 *
	 * (default value: 0)
	 *
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * Property to set when dispatching a single queue item.
	 *
	 * @var string
	 */
	protected $run_for_key;

	/**
	 * Max size of the queue. After that number, the system no longer saves the task request.
	 *
	 * @var int
	 */
	protected $max_queue_allowed;

	/**
	 * The queue batch size.
	 *
	 * @var int
	 */
	protected $batch_size = 10;

	/**
	 * Generate a key for the queue items.
	 *
	 * @param array $data Data.
	 * @return string
	 */
	abstract public function generate_key( $data );

	/**
	 * Run step.
	 *
	 * This is a recurring task that will be called by the action scheduler. It will be called on repeat until it returns false.
	 *
	 * @param array $data Data.
	 * @return array|false
	 */
	abstract protected function run_step( $data );

	/**
	 * Get job data.
	 *
	 * Parses the queue's runtime $data property into a format that the background job can accept in the run_step method.
	 *
	 * @param array $data Data.
	 * @return array|false The job data. Return array should contain the 'id' key to used as the source ID of each queue item. It can be a Product ID or a Deployment ID.
	 */
	abstract protected function get_job_data( $data );

	/**
	 * Initiate new background process
	 */
	public function __construct() {

		// Uses unique prefix per blog so each blog has its own queue.
		$blog_id          = get_current_blog_id();
		$this->identifier = sprintf( 'wp_%d_%s', $blog_id, $this->action );
	}

	/**
	 * Handle recurring request via AS.
	 *
	 * @param array $args Arguments.
	 * @return void
	 */
	public function handle_request( $args = array() ) {

		if ( $this->is_process_running() || $this->is_queue_empty() ) {
			return;
		}

		$batch = $this->get_batch( $args['batch_size'] ?? $this->batch_size );
		if ( empty( $batch ) ) {
			return;
		}

		$this->process_batch( $batch );
	}

	/**
	 * Decorate item.
	 *
	 * @param array $item The item.
	 * @return object|false
	 */
	private function decorate_item( array $item ) {

		if ( ! isset( $item['item_key'], $item['deployment_id'], $item['data'], $item['iterations'] ) ) {
			return false;
		}

		return (object) array(
			'key'        => $item['item_key'],
			'source_id'  => absint( $item['deployment_id'] ),
			'data'       => maybe_unserialize( $item['data'] ),
			'iterations' => absint( $item['iterations'] ),
		);
	}

	/**
	 * Get item.
	 *
	 * @param string $key The item key.
	 * @return object|false
	 */
	public function get_item( string $key ) {
		$args = array(
			'key'   => $key,
			'stale' => false,
			'limit' => 1,
		);

		$item = WC_PRL()->db->queue->query( $args );
		if ( empty( $item ) || ! is_array( $item ) ) {
			return false;
		}

		$item = $item[0];
		return $this->decorate_item( $item );
	}

	/**
	 * Get batch.
	 *
	 * @since 4.3.0
	 *
	 * @param int $size The batch size.
	 * @return array
	 */
	public function get_batch( int $size ) {

		if ( ! $size ) {
			$size = $this->batch_size;
		}

		$args = array(
			'key'      => $this->identifier . '_item_%',
			'limit'    => $size,
			'order_by' => array( 'added_time' => 'ASC' ),
			'stale'    => false,
		);

		$batch = (array) WC_PRL()->db->queue->query( $args );
		if ( empty( $batch ) || ! is_array( $batch ) ) {
			return array();
		}

		// Decorate items.
		$batch = array_filter( array_map( array( $this, 'decorate_item' ), $batch ) );
		return $batch;
	}

	/**
	 * Process batch.
	 *
	 * @since 4.3.0
	 *
	 * Hint: Tries to process all items in the batch.
	 * If the execution limit is exceeded, it will stop processing (e.g., queries take too long).
	 *
	 * @param array $batch The batch.
	 * @return void
	 */
	public function process_batch( array $batch ) {

		$this->lock_process();

		foreach ( $batch as $item ) {
			$this->process_item( $item );

			if ( $this->is_execution_limit_exceeded() ) {
				break;
			}
		}

		$this->unlock_process();
	}

	/**
	 * Process item.
	 *
	 * @since 4.3.0
	 *
	 * Hint: Each items needs multiple iterations to complete.
	 * If not completed, it saved in the queue and will be processed again
	 * on the next AS tick.
	 *
	 * @param object $item The item.
	 * @return void
	 */
	public function process_item( $item ) {
		$data           = $item->data;
		$iterations     = (int) ( $item->iterations ?? 0 );
		$max_iterations = self::get_max_iterations_per_item(); // Acts as a guard against infinite loops.
		do {
			++$iterations;
			$data = $this->run_step( $data );
			if ( false === $data ) {
				break;
			}
		} while ( $iterations < $max_iterations );

		// Finally update if more processing is needed.
		if ( is_array( $data ) ) {
			$this->update( $item->key, $data, $iterations );
		}

		if ( false === $data ) {
			$this->delete( $item->key );
		}
	}

	/**
	 * Get max iterations.
	 *
	 * @since 4.3.0
	 *
	 * @return int
	 */
	public static function get_max_iterations_per_item(): int {
		return 10;
	}

	/**
	 * Get number of queue items.
	 *
	 * @param array $args The query arguments.
	 * @return int
	 */
	public function count( $args = array() ) {
		if ( ! is_array( $args ) ) {
			$args = array();
		}

		$args  = array_merge( $args, array( 'count' => true ) );
		$count = $this->query( $args );
		return $count;
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	public function is_queue_empty(): bool {
		return WC_PRL()->db->queue->is_queue_empty();
	}

	/**
	 * Checks if the queue is full.
	 *
	 * @return bool
	 */
	public function is_queue_full(): bool {
		return $this->count() >= $this->get_max_queue_allowed();
	}

	/**
	 * Does the task exist the queue?
	 *
	 * @param array $data Data.
	 * @return bool
	 */
	public function is_task_in_queue( $data ): bool {
		$key = $this->generate_key( $this->get_job_data( $data ) );
		return WC_PRL()->db->queue->key_exists( $key );
	}

	/**
	 * Query queue items.
	 *
	 * @param array $args The query arguments.
	 * @return array
	 */
	public function query( $args = array() ) {
		$args['key'] = $this->identifier . '_item_%';
		return WC_PRL()->db->queue->query( $args );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Saves local data to queue.
	 *
	 * Hint: Runs on shutdown and saves all requests to the queue.
	 *
	 * @return int|bool The number of items saved or false if no data.
	 */
	public function save() {

		if ( empty( $this->data ) || $this->is_queue_full() ) {
			// Drop requests if limits are reached.
			return;
		}

		$args = array();
		foreach ( $this->data as $data ) {

			// Validate.
			if ( false === $this->get_job_data( $data ) ) {
				continue;
			}

			// Generate task key.
			$key = $this->generate_key( $data );
			// Decorate data with item key.
			$job_data = array_merge( $data, array( 'item_key' => $key ) );
			// Prepare data store arguments.
			$args[] = array(
				'key'       => $key,
				'source_id' => absint( $data['id'] ),
				'data'      => $job_data,
			);
		}

		if ( ! empty( $args ) ) {
			return WC_PRL()->db->queue->add( $args );
		}

		return false;
	}

	/**
	 * Add to queue.
	 *
	 * Hint: Runs on runtime when a page view is loaded and recommendations are requested.
	 *
	 * @param mixed $data Data.
	 *
	 * @return string|false The key to be added. False if data is invalid.
	 */
	public function add( $data ) {
		$data = $this->get_job_data( $data );
		if ( false === $data ) {
			return false;
		}

		$this->data[] = $data;
		return $this->generate_key( $data );
	}

	/**
	 * Update queue item.
	 *
	 * @param string $key Key.
	 * @param array  $data Data.
	 * @param int    $iterations Iterations.
	 *
	 * @return int|false
	 */
	public function update( $key, $data, $iterations = 0 ) {
		return WC_PRL()->db->queue->update( $key, $data, $iterations );
	}

	/**
	 * Delete queue item.
	 *
	 * @param string $key Key.
	 *
	 * @return bool
	 */
	public function delete( $key ) {
		return WC_PRL()->db->queue->delete( $key );
	}

	/*
	|--------------------------------------------------------------------------
	| Iterations management.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Force set a number of iterations in a queue item.
	 *
	 * @param string $key Key.
	 * @param int    $number The number of iterations to set.
	 *
	 * @return bool
	 */
	public function set_number_of_iterations( $key, $number ): bool {
		return WC_PRL()->db->queue->set_number_of_iterations( $key, $number );
	}

	/*
	|--------------------------------------------------------------------------
	| Process locking utilities.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 *
	 * @return bool
	 */
	public function is_process_running(): bool {
		if ( get_site_transient( $this->identifier . '_process_lock' ) ) {
			// Process already running.
			return true;
		}

		return false;
	}

	/**
	 * Lock process
	 *
	 * Lock the process so that multiple instances can't run simultaneously.
	 * Override if applicable, but the duration should be greater than that
	 * defined in the time_exceeded() method.
	 */
	protected function lock_process() {
		$this->start_time = time(); // Set start time of current process.
		$lock_duration    = 50; // Seconds.
		set_site_transient( $this->identifier . '_process_lock', microtime(), $lock_duration );
	}

	/**
	 * Unlock process
	 *
	 * Unlock the process so that other instances can spawn.
	 *
	 * @return $this
	 */
	protected function unlock_process() {
		delete_site_transient( $this->identifier . '_process_lock' );
		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Keeping execution limits.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get max queue allowed.
	 *
	 * @since 4.3.0
	 *
	 * @return int
	 */
	public function get_max_queue_allowed(): int {

		if ( ! isset( $this->max_queue_allowed ) ) {
			$is_wp_cli = WC_PRL_AS_Generator::is_handled_via_wp_cli();

			/**
			 * Use this filter to change the maximum number of queue items allowed.
			 *
			 * @since 1.0.0
			 *
			 * @param int  $max_queue_allowed The maximum number of queue items allowed.
			 * @param bool $is_wp_cli Whether the queue is handled via WP-CLI.
			 * @return int
			 */
			$this->max_queue_allowed = (int) apply_filters( $this->identifier . '_queue_max_size', $is_wp_cli ? 2000 : 20, $is_wp_cli );
		}

		return $this->max_queue_allowed;
	}

	/**
	 * Execution limits.
	 */
	protected function is_execution_limit_exceeded(): bool {
		return $this->time_exceeded() || $this->memory_exceeded();
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded(): bool {
		$memory_percentage = 0.9; // 90% of max memory.
		$memory_limit      = $this->get_memory_limit() * $memory_percentage;
		$current_memory    = memory_get_usage( true );
		$exceeded          = false;

		if ( $current_memory >= $memory_limit ) {
			$exceeded = true;
		}

		return $exceeded;
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	protected function get_memory_limit(): int {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || '-1' === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded(): bool {
		$time_limit = 20; // 20 seconds.
		$finish     = $this->start_time + $time_limit;
		$exceeded   = false;

		if ( time() >= $finish ) {
			$exceeded = true;
		}

		return $exceeded;
	}
}
