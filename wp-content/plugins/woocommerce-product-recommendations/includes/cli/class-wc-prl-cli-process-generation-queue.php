<?php
/**
 * WC_PRL_CLI_Process_Generation_Queue class
 *
 * @package  WooCommerce Product Recommendations
 * @since    3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows processing the generation queue via WP-CLI.
 *
 * @class    WC_PRL_CLI_Process_Generation_Queue
 * @version  4.3.0
 */
class WC_PRL_CLI_Process_Generation_Queue {

	/**
	 * Registers the update command.
	 */
	public static function register_command() {

		$args = array(
			'shortdesc' => 'Runs through the queue and generates product recommendations.',
		);

		WP_CLI::add_command(
			'wc prl process-generation-queue',
			array( 'WC_PRL_CLI_Process_Generation_Queue', 'process' ),
			$args
		);
	}

	/**
	 * Runs through the queue and generates product recommendations.
	 *
	 * @param array $args The arguments.
	 * @param array $assoc_args The associative arguments.
	 * @return void
	 */
	public static function process( $args, $assoc_args ) {

		require_once WC_PRL_ABSPATH . 'includes/class-wc-prl-generator.php';
		require_once WC_PRL_ABSPATH . 'includes/class-wc-prl-generator-queue.php';

		$queue = new WC_PRL_Generator_Queue();
		if ( $queue->is_queue_empty() ) {
			WP_CLI::success( 'Queue is empty. Exiting.' );
			return;
		}

		if ( $queue->is_process_running() ) {
			WP_CLI::success( 'Process already running. Exiting.' );
			return;
		}

		$max_items_limit = isset( $assoc_args['max-iterations'] ) ? absint( $assoc_args['max-iterations'] ) : 0;
		if ( $max_items_limit > 0 ) {
			$batch = $queue->get_batch( $max_items_limit );
			if ( empty( $batch ) ) {
				WP_CLI::success( 'No items to process. Exiting.' );
				return;
			}

			$queue->process_batch( $batch );
			WP_CLI::success( sprintf( 'Processed %d items.', count( $batch ) ) );
			return;
		}

		// Process all items in the queue.
		while ( ! $queue->is_queue_empty() ) {
			$batch = $queue->get_batch( 100 );
			if ( empty( $batch ) ) {
				WP_CLI::success( 'No items to process. Exiting.' );
				break;
			}

			WP_CLI::log( sprintf( 'Processing batch of %d items.', count( $batch ) ) );
			$queue->process_batch( $batch );
		}

		WP_CLI::success( 'Process completed.' );
	}
}
