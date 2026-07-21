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

namespace SkyVerge\WooCommerce\AvaTax;

defined( 'ABSPATH' ) or exit;

use ActionScheduler_Store;
use Exception;
use SkyVerge\WooCommerce\AvaTax\API\Models\HS_Classification_Model;
use SkyVerge\WooCommerce\AvaTax\API\Models\HS_Item_Model;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Abstract_HS_Classification_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\WC_AvaTax_API_Item_Sync_Response;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;
use stdClass;
use WC_Action_Queue;
use WC_Product;
use WC_Product_Query;
use WC_Product_Variation;
use WP_Post;

/**
 * Handles the synchronization process and notifies admins on important updates.
 *
 * @since 1.13.0
 */
class Landed_Cost_Sync_Handler {

	/** @var string the WooCommerce Action Queue hook name for syncing an individual product */
	const PRODUCT_SYNC_ACTION_QUEUE_HOOK = 'wc_avatax_process_enqueued_product';

	/** @var string the WooCommerce Action Queue hook name for syncing the full product catalog */
	const FULL_SYNC_ACTION_QUEUE_HOOK = 'wc_avatax_process_full_sync';

	/** @var string the WooCommerce Action Queue group name for syncing products */
	const PRODUCT_SYNC_ACTION_QUEUE_GROUP = 'wc_avatax_landed_cost_sync';

	/** @var string sync action name to create a product */
	const PRODUCT_SYNC_ACTION_CREATE = 'create';
	const PRODUCT_SYNC_ACTION_UPDATE = 'update';


	/** @var WC_Action_Queue instance */
	protected $action_queue;

	/** @var string option key for storing the syncing state */
	private $landed_cost_syncing_state_option_key = 'wc_avatax_landed_cost_syncing_state';

	/** @var string option key for storing the full sync status */
	private $landed_cost_full_sync_option_key = 'wc_avatax_landed_cost_full_sync';

	/** @var string option key for storing products data pending sync */
	private $landed_cost_products_pending_sync_option_key = 'wc_avatax_landed_cost_products_pending_sync';

	/** @var string option key for storing products that encountered sync errors */
	private $landed_cost_products_with_sync_errors_option_key = 'wc_avatax_landed_cost_products_with_sync_errors';

	/** @var string option key for storing products that had sync resolutions */
	private $landed_cost_products_with_sync_resolutions_option_key = 'wc_avatax_landed_cost_products_with_sync_resolutions';

	/** @var array list of products flagged to have classifications updated */
	protected $products_to_update_classification = [];

	/**
	 * Initializes the action queue and hooks.
	 *
	 * @since 1.13.0
	 */
	public function __construct() {

		$this->action_queue = new WC_Action_Queue();

		$this->add_hooks();
	}


	/**
	 * Gets the current instance of the action queue.
	 *
	 * @since 1.13.0
	 *
	 * @return WC_Action_Queue
	 */
	public function get_action_queue() : WC_Action_Queue {

		return $this->action_queue;
	}


	/**
	 * Adds handler actions and filters.
	 *
	 * @since 1.13.0
	 */
	protected function add_hooks() {

		add_action( self::FULL_SYNC_ACTION_QUEUE_HOOK, [ $this, 'handle_full_sync' ] );
		add_action( self::PRODUCT_SYNC_ACTION_QUEUE_HOOK, [ $this, 'handle_enqueued_product' ] );
		add_action( 'admin_notices', [ $this, 'maybe_finish_full_sync' ] );
		add_action( 'pre_post_update', [ $this, 'flag_updated_products_to_enqueue' ], 10, 2 );
		add_action( 'woocommerce_before_product_object_save', [ $this, 'maybe_flag_updated_products_to_enqueue' ] );
		add_action( 'save_post', [ $this, 'maybe_enqueue_saved_product' ], 10, 2 );
	}


	/**
	 * Handles full sync action by enqueuing batches of products to be synced.
	 *
	 * Enqueues a batch of products to be synced and re-enqueues itself until all products have been enqueued, avoiding
	 * timeout and memory limit issues on sites with large catalogs.
	 *
	 * @since 1.16.0
	 *
	 * @internal
	 *
	 * @param int $batch the batch number
	 * @return Landed_Cost_Sync_Enqueued_Product[] a list of enqueued products
	 */
	public function handle_full_sync( int $batch ) : array {

		/**
		 * Filters the full product sync batch size.
		 *
		 * @param int $limit batch size limit, 1000 by default
		 */
		$limit    = apply_filters( 'wc_avatax_product_sync_batch_size_limit', 1000 );
		$products = $this->get_products_to_be_synced( $this->query_all_products( $limit, $batch ) );

		// enqueue the next page to be synced, unless there were fewer products found than the limit
		if ( count( $products ) >= $limit ) {
			$this->enqueue_full_sync( $batch + 1 );
		}

		return $this->enqueue_products( $products );
	}


	/**
	 * Checks for changes in product main fields before updating product post, and flags it for classification update.
	 *
	 * Because woocommerce_before_product_object_save hook does not always have access to all the changes made
	 * to a product, we have this lower-level check for post changes.
	 *
	 * @since 1.13.0
	 * @internal
	 *
	 * @see Landed_Cost_Sync_Handler::should_resync_product()
	 * @see Landed_Cost_Sync_Handler::maybe_flag_updated_products_to_enqueue()
	 *
	 * @param int $post_id
	 * @param array $data
	 */
	public function flag_updated_products_to_enqueue( $post_id, $data ) {

		// bail early if not a product post
		if ( empty( $data['post_type'] ) || empty( $data['post_name'] ) || ! $this->is_syncable_product_post_type( $data['post_type'] )) {
			return;
		}

		$product_post = get_post( $post_id );
		$newTaxCode = Framework\SV_WC_Helper::get_posted_value( '_wc_avatax_code' );
		$oldTaxCode = $product_post->_wc_avatax_code;
		
		$newCom = Framework\SV_WC_Helper::get_posted_value( '_wc_avatax_county_of_manufacture' );
		$oldCom = $product_post->_wc_avatax_county_of_manufacture;

		// check for changed fields
		if ( $data['post_excerpt'] !== $product_post->post_excerpt ||
		     $data['post_content'] !== $product_post->post_content ||
		     $data['post_parent'] !== $product_post->post_parent ||
		     $data['post_name'] !== $product_post->post_name ||
		     $data['post_title'] !== $product_post->post_title ||
			 $newTaxCode !== $oldTaxCode ||
		     $newCom !== $oldCom ||
		     $this->has_updated_categories( $post_id ) ||
			 $this->has_updated_hs_codes($post_id)) {

			$this->products_to_update_classification[ $post_id ] = true;
		}
	}


	/**
	 * Checks for changes in product params before saving, and flags it for classification update.
	 *
	 * This method is hooked into woocommerce_before_product_object_save, which is triggered before a product
	 * or variation is saved. Normally, $product->get_changes() would return all changes here, including changes to
	 * product name or description. However, if the product was updated in admin UI (product edit screen), any changes
	 * stored on the product post itself would already have been saved, and as such, not available here. For this
	 * reason, we also hook Landed_Cost_Sync_Handler::flag_updated_products_to_enqueue() into pre_post_update.
	 *
	 * @since 1.16.0
	 * @internal
	 *
	 * @see Landed_Cost_Sync_Handler::should_resync_product()
	 * @see Landed_Cost_Sync_Handler::flag_updated_products_to_enqueue()
	 *
	 * @param WC_Product $product
	 */
	public function maybe_flag_updated_products_to_enqueue( WC_Product $product ) {

		if ( $this->product_has_changes_affecting_classification( $product ) ) {

			$this->products_to_update_classification[ $product->get_id() ] = true;
		}
	}


	/**
	 * Checks if the given product has updated/changed categories.
	 *
	 * @since 1.13.0
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 */
	protected function has_updated_categories( int $product_id ) : bool {

		$posted_terms = Framework\SV_WC_Helper::get_posted_value( 'tax_input' );

		if ( is_array( $posted_terms ) && isset( $posted_terms['product_cat'] ) ) {

			$new_categories_ids = array_filter( array_map( 'absint', $posted_terms['product_cat'] ) );
			sort( $new_categories_ids, SORT_ASC );

			$old_categories_ids = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );
			sort( $old_categories_ids, SORT_ASC );

			return $old_categories_ids !== $new_categories_ids;
		}

		return false;
	}

			/**
	 * Checks if a product's HS codes have changed.
	 *
	 * @since 1.16.0
	 *
	 * @param int $post_id The product ID
	 * @return bool True if HS codes have changed, false otherwise
	 */
	protected function has_updated_hs_codes(int $post_id): bool {
	    // Get and decode the JSON data containing all HS codes
	    $hs_countries_json = isset($_POST['_wc_avatax_hs_countries']) ? 
	        stripslashes($_POST['_wc_avatax_hs_countries']) : '{}';
	    $new_hs_countries = json_decode($hs_countries_json, true) ?: array();
	
	    // Get existing HS countries to check for changes
	    $existing_hs_countries = wc_avatax()->wc_avatax_utilities()->get_wc_meta($post_id, '_wc_avatax_hs_countries', true, 'product') ?: array();
	
	    // Check if countries were added or removed
	    if (count($new_hs_countries) !== count($existing_hs_countries)) {
	        return true;
	    }
	
	    // Check if any HS code values changed
	    foreach ($new_hs_countries as $country => $hsCode) {
	        $meta_key = '_wc_avatax_hs_' . $country;
	        $existing_hs_code = wc_avatax()->wc_avatax_utilities()->get_wc_meta($post_id, $meta_key, true, 'product');
		
	        if ($existing_hs_code !== $hsCode) {
	            return true;
	        }
	    }
	
	    return false;
	}


	/**
	 * Maybe enqueues a saved product to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public function maybe_enqueue_saved_product( $post_id, $post ) {

		if ( ! isset( $post->post_type ) || empty( $post->post_name ) || ! $this->is_syncable_product_post_type( $post->post_type ) ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return;
		}

		$products = [];
		$product_id = $product->get_id();

		$sync_action = $this->get_product_sync_action( $product );

		$products[] = ( new Landed_Cost_Sync_Enqueued_Product() )
			->set_product_id( $product_id )
			->set_action( $sync_action );


		if ( $product->is_type( 'variable' ) ) {
					foreach( $product->get_children() as $variation_id ) {

						$products[] = (new Landed_Cost_Sync_Enqueued_Product())
							->set_product_id( $variation_id )
							->set_action( $sync_action );
					}
				}
		
		$this->enqueue_products( $products );
	}

	/**
	 * Determines the sync action to be executed for the product.
	 *
	 * @since 1.13.0
	 *
	 * @param WC_Product $product
	 * @param string $destination_country
	 * @return string
	 */
	protected function get_product_sync_action( WC_Product $product ) : string {

		//TODO - Implement Item Sync Status
		$productSyncStatus = wc_avatax()->get_landed_cost_handler()->getProductSyncStatus($product);
		if (!$productSyncStatus) {
			return self::PRODUCT_SYNC_ACTION_CREATE;
		}
		if ($productSyncStatus ||  $this->should_resync_product($product)) {
			return self::PRODUCT_SYNC_ACTION_CREATE;
		}

		return '';
	}


	/**
	 * Determines whether the product should be synced again.
	 *
	 * @since 1.13.0
	 *
	 * @param WC_Product $product
	 * @return bool
	 */
	public function should_resync_product( WC_Product $product ) : bool {

		// checks in the flagged products first
		if ( ! empty( $this->products_to_update_classification[ $product->get_id() ] ) ) {
			return true;
		}

		return $this->product_has_changes_affecting_classification( $product );
	}


	/**
	 * Checks whether the product has any changes that might affect classification.
	 *
	 * @since 1.16.0
	 *
	 * @param WC_Product $product
	 * @return bool
	 */
	protected function product_has_changes_affecting_classification( WC_Product $product ) : bool {

		$changes = $product->get_changes();

		if ( empty( $changes ) ) {
			return false;
		}

		foreach (['description', 'short_description', 'name', 'parent_id', 'category_ids', 'sku', 'weight', 'length', 'width', 'height'] as $property_name) {
			if ( array_key_exists( $property_name, $changes ) ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Processes the list of products in the sync queue.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 */
	public function handle_enqueued_product( array $product_data ) {

		if ( ! $this->is_syncing_active() ) {

			$pending_products_data = get_option( $this->landed_cost_products_pending_sync_option_key );

			if ( is_array( $pending_products_data ) ) {
				$pending_product_data = array_merge( $pending_products_data, [ $product_data ] );
			} else {
				$pending_product_data = [ $product_data ];
			}

			update_option( $this->landed_cost_products_pending_sync_option_key, $pending_product_data );

		} else {

			$this->process_product( new Landed_Cost_Sync_Enqueued_Product( $product_data ) );
		}

		// gives a one-second break between each product sync action to prevent multiple API requests
		sleep( 1 );
	}


	/**
	 * Adds a list of products to the sync queue.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product[] $products
	 * @return Landed_Cost_Sync_Enqueued_Product[] enqueued products
	 */
	public function enqueue_products( array $products ) : array {

		$enqueued_products = [];

		foreach ( $products as $product ) {

			if ( ! $product instanceof Landed_Cost_Sync_Enqueued_Product || $this->is_product_scheduled( $product ) ) {
				continue;
			}

			try {

				$this->get_action_queue()->schedule_single(
					$product->get_timestamp() ?: time(),
					self::PRODUCT_SYNC_ACTION_QUEUE_HOOK,
					[ 'product' => $product->to_array() ],
					self::PRODUCT_SYNC_ACTION_QUEUE_GROUP
				);

				$enqueued_products[] = $product;

			} catch ( Exception $e ) {

				if ( wc_avatax()->logging_enabled() ) {
					wc_avatax()->log( $e->getMessage() );
				}

				//Logging error
				wc_avatax()->logger()->log_exception("ItemSync", "enqueue_products", $e->getMessage(), $e->getTraceAsString());
			}
		}

		return $enqueued_products;
	}

	/**
	 * Enqueues a full sync to be performed.
	 *
	 * @since 1.16.0
	 *
	 * @param int $batch batch (or page) to process
	 */
	public function enqueue_full_sync( int $batch = 1 ) {
		try {

			$this->get_action_queue()->schedule_single(
				time(),
				self::FULL_SYNC_ACTION_QUEUE_HOOK,
				[ 'batch' => $batch ],
				self::PRODUCT_SYNC_ACTION_QUEUE_GROUP
			);

		} catch ( Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("ItemSync", "enqueue_full_sync", $e->getMessage(), $e->getTraceAsString());
		}
	}


	/**
	 * Maybe enqueues products that are pending sync.
	 *
	 * @since 1.13.0
	 */
	public function maybe_enqueue_pending_products() {

		$products_to_sync = [];

		foreach ( (array) get_option( $this->landed_cost_products_pending_sync_option_key, [] ) as $product_data ) {
			if ( is_array( $product_data ) ) {
				$products_to_sync[] = new Landed_Cost_Sync_Enqueued_Product( $product_data );
			}
		}

		$this->enqueue_products( $products_to_sync );

		update_option( $this->landed_cost_products_pending_sync_option_key, [] );
	}


	/**
	 * Maybe starts a new full database sync.
	 *
	 * @since 1.13.0
	 */
	public function maybe_start_full_sync() {

		if ( ! $this->is_full_syncing_active() ) {

			update_option( $this->landed_cost_full_sync_option_key, 'yes' );
		}
	}


	/**
	 * Determines whether a full database sync is active or not.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function is_full_syncing_active() : bool {

		return wc_string_to_bool( get_option( $this->landed_cost_full_sync_option_key, 'no' ) );
	}


	/**
	 * Determines whether the landed cost syncing is active or not.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function is_syncing_active() : bool {

		return 'on' === get_option( $this->landed_cost_syncing_state_option_key, 'off' );
	}


	/**
	 * Processes a product enqueued for sync or classification.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 */
	protected function process_product( Landed_Cost_Sync_Enqueued_Product $product ) {

		$wc_product = wc_get_product( $product->get_product_id() );

		if ( ! $wc_product ) {
			// bail early as the product is not found (maybe deleted)
			return;
		}

		if ( 'sync' === $product->get_action() ) {
			$this->process_product_sync_action( $wc_product );
		} else {
			$this->process_product_classification_action( $product, $wc_product );
		}
	}


	/**
	 * Processes a product to run the sync workflow.
	 *
	 * @since 1.16.0
	 *
	 * @param WC_Product $wc_product
	 */
	protected function process_product_sync_action( WC_Product $wc_product ) {
		$product_id = $wc_product->get_id();

		$enqueued_product = ( new Landed_Cost_Sync_Enqueued_Product() )
			->set_product_id( $product_id )
			->set_action( $this->get_product_sync_action( $wc_product) );

		$this->enqueue_products( [ $enqueued_product ] );
	}


	/**
	 * Processes a product to run the classification workflow.
	 *
	 * @since 1.16.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @param WC_Product $wc_product
	 */
	protected function process_product_classification_action( Landed_Cost_Sync_Enqueued_Product $product, WC_Product $wc_product ) {
		$response = null;

		try {
			switch ( $product->get_action() ) {
				case 'create':
				case 'update':
					$response = wc_avatax()->get_api()->sync_item( $wc_product, ($product->get_action() == 'create' ? "POST" : "PUT") );
					break;
			}
			$this->handle_response( $product, $wc_product, $this->convert_to_avatax_response($response) );
		} catch ( Framework\SV_WC_API_Exception $exception ) {
			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $exception->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("ItemSync", "process_product_classification_action", $exception->getMessage(), $exception->getTraceAsString());
		}
	}
	/**
	 * Handles an IC API response.
	 *
	 * @since 1.16.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product the enqueued product
	 * @param WC_Product $wc_product the WooCommerce product
	 * @param \WC_AvaTax_API_Item_Sync_Response|null $response the response, if available
	 */
	protected function handle_response( Landed_Cost_Sync_Enqueued_Product $product, WC_Product $wc_product, \WC_AvaTax_API_Item_Sync_Response $response = null ) {
		if ( null === $response ) {
			// bail as no appropriate API request/response found
			return;
		}

		if ( $response->has_errors() ) {
			$this->handle_error( $product, $response );
		} 

		if($response->get_avatax_item_sync_status() === 'completed'){
			$avataxProductIds = $wc_product->get_meta("_wc_avatax_product_ids");
			$avataxProductIds = is_array($avataxProductIds) ? $avataxProductIds : [];
			$companyId = wc_avatax()->get_company_id();
			if (!isset($avataxProductIds[$companyId]) &&  $response->get_avatax_item_id() != 0) {
				$avataxProductIds[$companyId] = $response->get_avatax_item_id();
				$wc_product->update_meta_data('_wc_avatax_product_ids', $avataxProductIds);
				$wc_product->save();
			}
		}
	}

	/**
	 * Convert response data into WC_AvaTax_API_Item_Sync_Response
	 *
	 * @param mixed $response
	 * @return \WC_AvaTax_API_Item_Sync_Response|null
	 */
	protected function convert_to_avatax_response( $response ) {
		if ( $response instanceof \WC_AvaTax_API_Item_Sync_Response ) {
			return $response; // Already correct instance
		}

		if ( is_array($response) || is_object($response) ) {
			return new \WC_AvaTax_API_Item_Sync_Response(  json_encode($response) );
			
		}
		return null; // Unable to convert
	}


	/**
	 * Handles an IC API error response.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @param Abstract_HS_Classification_Response $response
	 */
	protected function handle_error( Landed_Cost_Sync_Enqueued_Product $product, \WC_AvaTax_API_Item_Sync_Response $response ) {
		$errors = $response->item_sync_has_errors();

		// in case we have an auth error, immediately stop syncing to prevent endless re-queuing of
		// sync jobs that will keep on failing and inflating the database
		if ( $has_auth_error = $response->has_auth_error() ) {
			$this->stop_syncing();
		}

		if ( wc_avatax()->logging_enabled() ) {
			foreach ( $errors as $error ) {
				wc_avatax()->log( "Logged Errors" . $error );
			}

			if ( $has_auth_error ) {
				wc_avatax()->log( __( 'Cross-border product sync stopped. Please ensure you have valid credentials and an active subscription for cross-border item classification.', 'wc-avatax' ) );
			}
		}
		if($response->item_sync_event() != "Error")
		{
			$this->store_error_product( $product );
		}
	}



	/**
	 * Determines whether a product is already scheduled to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 * @return bool
	 */
	public function is_product_scheduled( Landed_Cost_Sync_Enqueued_Product $product ) : bool {

		return ! empty( $this->get_action_queue()->search( [
			'hook'   => self::PRODUCT_SYNC_ACTION_QUEUE_HOOK,
			'args'   => [ 'product' => $product->to_array() ],
			'group'  => self::PRODUCT_SYNC_ACTION_QUEUE_GROUP,
			'status' => ActionScheduler_Store::STATUS_PENDING,
		], 'ids' ) );
	}


	/**
	 * Maybe notifies the admin that the full sync is finished.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 */
	public function maybe_finish_full_sync() {

		if ( false === $this->is_full_syncing_active() ) {
			// bail early as full sync is not active
			return;
		}

		if ( $this->count_pending_sync_actions() > 0 ) {
			// bail early as there are some scheduled actions in the works
			return;
		}

		wc_avatax()->get_admin_notice_handler()->add_admin_notice(
			sprintf(
				/* translators: Placeholders: %1$s - <strong> tag, %2$s - </strong> tag */
				__( '%1$sYour catalog is synced to AvaTax!%2$s Cross-border tax calculations can now take place at checkout. Catalog updates will be synced to AvaTax as you add, update, or delete products in WooCommerce.', 'woocommerce-avatax' ),
				'<strong>', '</strong>'
			),
			'wc-avatax-full-sync-started-notice',
			[
				'dismissible' => true,
			]
		);

		// disable the notice flag
		update_option( $this->landed_cost_full_sync_option_key, 'no' );
	}


	/**
	 * Adds products with errors and products that cannot be classified back to the sync queue.
	 *
	 * @since 1.13.0
	 *
	 * @return array the list of enqueued products
	 */
	public function resync_products_with_errors() {

		$products_with_sync_errors      = (array) get_option( $this->landed_cost_products_with_sync_errors_option_key, [] );
		$products_with_sync_resolutions = (array) get_option( $this->landed_cost_products_with_sync_resolutions_option_key, [] );
		$products = [];

		foreach ( array_merge( $products_with_sync_errors, $products_with_sync_resolutions ) as $product_data ) {
			$product = new Landed_Cost_Sync_Enqueued_Product( $product_data );
			$product->set_timestamp( time() + $this->get_wait_time_to_get_classifications( $product ) );
			$products[] = $product;
		}

		$enqueued_products = $this->enqueue_products( $products );

		update_option( $this->landed_cost_products_with_sync_errors_option_key, [] );
		update_option( $this->landed_cost_products_with_sync_resolutions_option_key, [] );

		return $enqueued_products;
	}


	/**
	 * Toggles the landed cost syncing state.
	 *
	 * If the background sync is running, it will be turned off and vice-versa.
	 *
	 * @since 1.13.0
	 */
	public function toggle_syncing() {

		$current_sync_status = 'on' === get_option( $this->landed_cost_syncing_state_option_key );

		$new_sync_status = $current_sync_status ? 'off' : 'on';

		update_option( $this->landed_cost_syncing_state_option_key, $new_sync_status );

		if ( 'on' === $new_sync_status ) {
			$this->enqueue_full_sync();
			$this->maybe_start_full_sync();
			$this->maybe_enqueue_pending_products();
		}
	}

	/**
	 * Stops the landed cost syncing state.
	 *
	 * @since 1.16.0
	 */
	public function stop_syncing() {

		if ( $this->is_syncing_active() ) {

			update_option( $this->landed_cost_syncing_state_option_key, 'off' );
		}
	}


	/**
	 * Stores a product in an error list.
	 *
	 * The list may be used later for re-syncing matters or even to let merchants know which products must be fixed.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product
	 */
	public function store_error_product( Landed_Cost_Sync_Enqueued_Product $product ) {
		$this->store_product_for_later_resync( $product, $this->landed_cost_products_with_sync_errors_option_key );
	}

	/**
	 * Stores the product in a list that may be used later for re-syncing.
	 *
	 * @since 1.13.0
	 *
	 * @param Landed_Cost_Sync_Enqueued_Product $product the product to be re-synced later
	 * @param string $option_name the WP option name used to store the list
	 */
	protected function store_product_for_later_resync( Landed_Cost_Sync_Enqueued_Product $product, string $option_name ) {
		$product_list = get_option( $option_name, [] );
		if ( is_array( $product_list ) ) {
			$product_list[ $product->get_product_id() ] = $product->to_array();
		} else {
			$product_list = [ $product->get_product_id() => $product->to_array() ];
		}

		update_option( $option_name, $product_list );
	}


	/**
	 * Gets all products in the database that are candidates to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @return int[] an array of products IDs
	 */
	protected function query_all_products( int $limit = 10, int $page = 1 ) : array {

		// TODO: consider making the product type list here filterable (this would require a filter in product save handling as well {IT 2022-01-05}
		return ( new WC_Product_Query([
			'return'  => 'ids',
			'limit'   => $limit,
			'page'    => $page,
			'type'    => array_merge( ['variation'], array_keys( wc_get_product_types() ) ),
			// ordering by ID to keep results consistent
			'order'   => 'ASC',
			'orderby' => 'ID',
		]) )->get_products();
	}


	/**
	 * Gets a list of products to be synced.
	 *
	 * @since 1.13.0
	 *
	 * @param int[] $products_ids an array of products IDs
	 * @return Landed_Cost_Sync_Enqueued_Product[] a list of products to be synced
	 */
	protected function get_products_to_be_synced( array $products_ids ) : array {

		return array_map( static function( $product_id ) {
			return new Landed_Cost_Sync_Enqueued_Product( [
				'product_id' => $product_id,
				'action'     => 'sync',
			] );
		}, $products_ids );
	}


	/**
	 * Gets the number of pending sync actions.
	 *
	 * It basically counts how many actions are scheduled for the handler callback that are pending.
	 *
	 * @since 1.13.0
	 *
	 * @return int
	 */
	protected function count_pending_sync_actions() : int {

		return count( $this->action_queue->search( [
			'group' => self::PRODUCT_SYNC_ACTION_QUEUE_GROUP,
			'status' => ActionScheduler_Store::STATUS_PENDING
		], 'ids' ) );
	}


    /**
     * Gets the time in seconds to wait before attempting to get a classification.
     *
     * It defaults to 24 hours (86400), but can be filtered.
     *
     * @since 1.13.0
     *
     * @param Landed_Cost_Sync_Enqueued_Product $product the product to be synced
     * @return int the time in seconds to wait before attempting to get a classification
     */
	private function get_wait_time_to_get_classifications( Landed_Cost_Sync_Enqueued_Product $product ) : int {

        /**
         * Filters the number of seconds a product sync is delayed while waiting to get a classification.
         *
         * @since 1.13.0
         *
         * @param int $delay The number of seconds between create and get calls.
         * @param Landed_Cost_Sync_Enqueued_Product $product The product to be synced
         * @param Landed_Cost_Sync_Handler $this The current instance of the sync handler
         */
        return apply_filters( 'wc_avatax_wait_time_to_get_classifications', DAY_IN_SECONDS, $product, $this );
    }

	/**
	 * Checks whether the given post type is a syncable product post type.
	 *
	 * @since 1.16.0
	 *
	 * @param string $post_type
	 * @return bool
	 */
	protected function is_syncable_product_post_type( string $post_type ) : bool {

		return in_array( $post_type, ['product', 'product_variation'], true );
	}

}
