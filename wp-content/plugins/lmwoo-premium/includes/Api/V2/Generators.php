<?php

namespace LicenseManagerForWooCommerce\Api\V2;

use Exception;
use LicenseManagerForWooCommerce\Abstracts\RestController as LMFWC_REST_Controller;
use LicenseManagerForWooCommerce\Enums\LicenseSource;
use LicenseManagerForWooCommerce\Models\Resources\Generator as GeneratorResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') || exit;

class Generators extends LMFWC_REST_Controller {

	/**
	 * Namespace
	 *
	 * @var string
	 */
	protected $namespace = 'lmfwc/v2';

	/**
	 * Rest_base
	 *
	 * @var string
	 */
	protected $rest_base = '/generators';

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Generators constructor.
	 */
	public function __construct() {
		$this->settings = (array) get_option('lmfwc_settings_general');
	}

	/**
	 * Register all the needed routes for this resource.
	 */
	public function register_routes() {
		/**
		 * GET generators
		 *
		 * Retrieves all the available generators from the database.
		 */
		register_rest_route(
			$this->namespace, $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'getGenerators' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
				),
			)
		);

		/**
		 * GET generators/{id}
		 *
		 * Retrieves a single generator from the database.
		 */
		register_rest_route(
			$this->namespace, $this->rest_base . '/(?P<generator_id>[\w-]+)', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'getGenerator' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'generator_id' => array(
							'description' => 'Generator ID',
							'type'        => 'integer',
						),
					),
				),
			)
		);

		/**
		 * POST generators
		 *
		 * Creates a new generator in the database
		 */
		register_rest_route(
			$this->namespace, $this->rest_base, array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'createGenerator' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
				),
			)
		);

		/**
		 * PUT generators/{id}
		 *
		 * Updates an already existing generator in the database
		 */
		register_rest_route(
			$this->namespace, $this->rest_base . '/(?P<generator_id>[\w-]+)', array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'updateGenerator' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'generator_id' => array(
							'description' => 'Generator ID',
							'type'        => 'integer',
						),
					),
				),
			)
		);

		/**
		 * DELETE generators/{id}
		 *
		 * Updates an already existing generator in the database
		 */
		register_rest_route(
			$this->namespace, $this->rest_base . '/(?P<generator_id>[\w-]+)', array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'deleteGenerator' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'generator_id' => array(
							'description' => 'Generator ID',
							'type'        => 'integer',
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			$this->rest_base . '/(?P<generator_id>[\w-]+)/generate',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'generateLicenseKeys' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'generator_id' => array(
							'description' => 'Generator ID',
							'type'        => 'integer',
						),
					),
				),
			)
		);
	}

	/**
	 * Callback for the GET generators route. Retrieves all generators from the database.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function getGenerators() {
		if (!$this->isRouteEnabled($this->settings, '018')) {
			return $this->routeDisabledError();
		}

		if (!$this->permissionCheck('generator', 'read')) {
			return new WP_Error(
				'lmfwc_rest_cannot_view',
				__('Sorry, you cannot list resources.', 'license-manager-for-woocommerce'),
				array(
					'status' => $this->authorizationRequiredCode(),
				)
			);
		}


		try {
			
			/**
			 *  GeneratorResourceRepository find generator
			 * 
			 * @var GeneratorResourceRepository $generator 
			**/
			$generators = GeneratorResourceRepository::instance()->findAll();
		} catch (Exception $e) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				$e->getMessage(),
				array( 'status' => 404 )
			);
		}

		if (!$generators) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'No Generators available',
				array( 'status' => 404 )
			);
		}

		$response = array();

		foreach ($generators as $generator) {
			$response[] = $generator->toArray();
		}

		return $this->response(true, $response, 200, 'v2/generators');
	}

	/**
	 * Callback for the GET generators/{id} route. Retrieves a single generator from the database.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function getGenerator( WP_REST_Request $request ) {
		if (!$this->isRouteEnabled($this->settings, '019')) {
			return $this->routeDisabledError();
		}

		if (!$this->permissionCheck('generator', 'read')) {
			return new WP_Error(
				'lmfwc_rest_cannot_view',
				__('Sorry, you cannot view this resource.', 'license-manager-for-woocommerce'),
				array(
					'status' => $this->authorizationRequiredCode(),
				)
			);
		}

		$generatorId = absint($request->get_param('generator_id'));

		if (!$generatorId) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Generator ID is invalid.',
				array( 'status' => 404 )
			);
		}

		try {
			/**
			 *  GeneratorResourceRepository find generator
			 * 
			 * @var GeneratorResourceRepository $generator 
			**/
			$generator = GeneratorResourceRepository::instance()->find($generatorId);
		} catch (Exception $e) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				$e->getMessage(),
				array( 'status' => 404 )
			);
		}

		if (!$generator) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				sprintf(
					'Generator with ID: %d could not be found.',
					$generatorId
				),
				array( 'status' => 404 )
			);
		}

		return $this->response(true, $generator->toArray(), 200, 'v2/generators/{id}');
	}

	/**
	 * Callback for the POST generators route. Creates a new generator in the database.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function createGenerator( WP_REST_Request $request ) {
		if (!$this->isRouteEnabled($this->settings, '020')) {
			return $this->routeDisabledError();
		}

		if (!$this->permissionCheck('generator', 'create')) {
			return new WP_Error(
				'lmfwc_rest_cannot_create',
				__('Sorry, you are not allowed to create resources.', 'license-manager-for-woocommerce'),
				array(
					'status' => $this->authorizationRequiredCode(),
				)
			);
		}

		$body = $request->get_params();

		$name              = isset($body['name'])                ? sanitize_text_field($body['name'])      : null;
		$charset           = isset($body['charset'])             ? sanitize_text_field($body['charset'])   : null;
		$chunks            = isset($body['chunks'])              ? absint($body['chunks'])                 : null;
		$chunkLength       = isset($body['chunk_length'])        ? absint($body['chunk_length'])           : null;
		$timesActivatedMax = isset($body['times_activated_max']) ? absint($body['times_activated_max'])    : null;
		$separator         = isset($body['separator'])           ? sanitize_text_field($body['separator']) : null;
		$prefix            = isset($body['prefix'])              ? sanitize_text_field($body['prefix'])    : null;
		$suffix            = isset($body['suffix'])              ? sanitize_text_field($body['suffix'])    : null;
		$expiresIn         = isset($body['expires_in'])          ? absint($body['expires_in'])             : null;

		if (!$name) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__('The Generator name is missing from the request.', 'license-manager-for-woocommerce'),
				array( 'status' => 404 )
			);
		}

		if (!$charset) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__('The Generator charset is missing from the request.', 'license-manager-for-woocommerce'),
				array( 'status' => 404 )
			);
		}

		if (!$chunks) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__('The Generator chunks is missing from the request.', 'license-manager-for-woocommerce'),
				array( 'status' => 404 )
			);
		}

		if (!$chunkLength) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__('The Generator chunk length is missing from the request.', 'license-manager-for-woocommerce'),
				array( 'status' => 404 )
			);
		}

		try {
			/**
			 *  GeneratorResourceRepository insert generator
			 * 
			 * @var GeneratorResourceRepository $generator 
			**/
			$generator = GeneratorResourceRepository::instance()->insert(
				array(
					'name'                => $name,
					'charset'             => $charset,
					'chunks'              => $chunks,
					'chunk_length'        => $chunkLength,
					'times_activated_max' => $timesActivatedMax,
					'separator'           => $separator,
					'prefix'              => $prefix,
					'suffix'              => $suffix,
					'expires_in'          => $expiresIn,
				)
			);
		} catch (Exception $e) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				$e->getMessage(),
				array( 'status' => 404 )
			);
		}

		if (!$generator) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__('The Generator could not be added to the database.', 'license-manager-for-woocommerce'),
				array( 'status' => 404 )
			);
		}

		return $this->response(true, $generator->toArray(), 200, 'v2/generators');
	}

	/**
	 * Callback for the PUT generators/{id} route. Updates an existing generator in the database.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function updateGenerator( WP_REST_Request $request ) {
		if (!$this->isRouteEnabled($this->settings, '021')) {
			return $this->routeDisabledError();
		}

		if (!$this->permissionCheck('generator', 'edit')) {
			return new WP_Error(
				'lmfwc_rest_cannot_edit',
				__('Sorry, you are not allowed to edit resources.', 'license-manager-for-woocommerce'),
				array(
					'status' => $this->authorizationRequiredCode(),
				)
			);
		}

		$body        = null;
		$generatorId = null;

		// Set and sanitize the basic parameters to be used.
		if ($request->get_param('generator_id')) {
			$generatorId = absint($request->get_param('generator_id'));
		}

		if ($this->isJson($request->get_body())) {
			$body = json_decode($request->get_body());
		}

		// Validate basic parameters
		if (!$generatorId) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__('The Generator ID is missing from the request.', 'license-manager-for-woocommerce'),
				array( 'status' => 404 )
			);
		}

		if (!$body) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'No parameters were provided.',
				array( 'status' => 404 )
			);
		}

		$updateData = (array) $body;

		if (array_key_exists('name', $updateData) && strlen($updateData['name']) <= 0) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Generator name is invalid.',
				array( 'status' => 404 )
			);
		}

		if (array_key_exists('charset', $updateData) && strlen($updateData['charset']) <= 0) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Generator charset is invalid.',
				array( 'status' => 404 )
			);
		}

		if (array_key_exists('chunks', $updateData) && !is_numeric($updateData['chunks'])) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Generator chunks must be an absolute integer.',
				array( 'status' => 404 )
			);
		}

		if (array_key_exists('chunk_length', $updateData) && !is_numeric($updateData['chunk_length'])) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Generator chunk_length must be an absolute integer.',
				array( 'status' => 404 )
			);
		}

		if (array_key_exists('times_activated_max', $updateData)
			&& !is_numeric($updateData['times_activated_max'])
		) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Generator times_activated_max must be an absolute integer.',
				array( 'status' => 404 )
			);
		}

		/**
		 *  GeneratorResourceRepository update Generator
		 * 
		 * @var GeneratorResourceRepository $updatedGenerator 
		**/
		$updatedGenerator = GeneratorResourceRepository::instance()->update($generatorId, $updateData);

		if (!$updatedGenerator) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'The generator could not be updated.',
				array( 'status' => 404 )
			);
		}

		return $this->response(true, $updatedGenerator->toArray(), 200, 'v2/generators/{id}');
	}

	/**
	 * Callback for the DELETE generators/{id} route. Deletes an existing generator in the database.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function deleteGenerator( WP_REST_Request $request ) {

		if (!$this->isRouteEnabled($this->settings, '022')) {
			return $this->routeDisabledError();
		}

	 

	  $urlParams = $request->get_url_params();
		$generator_id = isset( $urlParams['generator_id'] ) ? sanitize_text_field( $urlParams['generator_id'] ) : '';

		$generator = GeneratorResourceRepository::instance()->delete($generator_id);

		if (!$generator) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'The generator key could not be found or deleted.',
				array( 'status' => 404 )
			  );
		}
		return $this->response( true, array(), 200, 'v2/generators/{id}' ); 
	}

	public function generateLicenseKeys( WP_REST_Request $request ) {

		if ( ! $this->isRouteEnabled( $this->settings, '029' ) ) {
			return $this->routeDisabledError();
		}

		if ( ! $this->permissionCheck( 'generator', 'create' ) ) {
			return new WP_Error(
				'lmfwc_rest_cannot_create',
				__( 'Sorry, you are not allowed to create resources.', 'license-manager-for-woocommerce' ),
				array( 'status' => $this->authorizationRequiredCode() )
			);
		}

		$body         = null;
		$generator_id = null;

		// Set and sanitize the basic parameters to be used.
		if ( $request->get_param( 'generator_id' ) ) {
			$generator_id = absint( $request->get_param( 'generator_id' ) );
		}

		if ( $this->isJson( $request->get_body() ) ) {
			$body = json_decode( $request->get_body(), true );
		}

		// Validate basic parameters
		if ( ! $generator_id ) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				__( 'The Generator ID is missing from the request.', 'license-manager-for-woocommerce' ),
				array( 'status' => 404 )
			);
		}

		if ( ! $body ) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'No parameters were provided.',
				array( 'status' => 404 )
			);
		}

		$save = (bool) $body['save'];

		if ( $save ) {
			$status_enum = sanitize_text_field( $body['status'] );
			$status      = $this->getLicenseStatus( $status_enum );
			$order_id    = null;
			$product_id  = null;
			$user_id     = null;

			if ( isset( $body['order_id'] ) ) {
				$order_id = (int) $body['order_id'];

				if ( ! wc_get_order( $order_id ) ) {
					return new WP_Error(
						'lmfwc_rest_data_error',
						'The order does not exist.',
						array( 'status' => 404 )
					);
				}
			}

			if ( isset( $body['product_id'] ) ) {
				$product_id = (int) $body['product_id'];

				if ( ! wc_get_product( $product_id ) ) {
					return new WP_Error(
						'lmfwc_rest_data_error',
						'The product does not exist.',
						array( 'status' => 404 )
					);
				}
			}

			if ( isset( $body['user_id'] ) ) {
				$user_id = (int) $body['user_id'];

				if ( ! get_user_by( 'ID', $user_id ) ) {
					return new WP_Error(
						'lmfwc_rest_data_error',
						'The user does not exist.',
						array( 'status' => 404 )
					);
				}
			}
		}

		$generator = GeneratorResourceRepository::instance()->find( $generator_id );

		if ( ! $generator ) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				sprintf(
					'Generator with ID: %d could not be found.',
					$generator_id
				),
				array( 'status' => 404 )
			);
		}

		$amount = null;

		if ( isset( $body['amount'] ) ) {
			$amount = (int) $body['amount'];
		}

		if ( ! $amount || ! is_int( $amount ) ) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'Invalid amount',
				array( 'status' => 400 )
			);
		}

		/**
		 * Filter 
		 * 
		 * @since 1.0.0
		 **/
		$licenses = apply_filters( 'lmfwc_generate_license_keys', $amount, $generator );
		
		if ( ! $licenses ) {
			return new WP_Error(
				'lmfwc_rest_data_error',
				'The licenses could not be generated.',
				array( 'status' => 400 )
			);
		}

		if ( $save ) {
			foreach ( $licenses as $license_key ) {
				$data = array(
					/**
				 * Filter 
				 * 
				 * @since 1.0.0
				 **/
					'license_key'         => apply_filters( 'lmfwc_encrypt', $license_key ),
					/**
					 * Filter 
					 * 
					 * @since 1.0.0
					 **/
					'hash'                => apply_filters( 'lmfwc_hash', $license_key ),
					'valid_for'           => $generator->getExpiresIn(),
					'expires_at'          => null,
					'source'              => LicenseSource::API,
					'status'              => $status,
					'times_activated_max' => $generator->getTimesActivatedMax(),
				);

				if (  null !== $order_id ) {
					$data['order_id'] = $order_id;
				}

				if ( null !== $product_id ) {
					$data['product_id'] = $product_id;
				}

				if ( null !== $user_id ) {
					$data['user_id'] = $user_id;
				}
				$data = apply_filters( 'generate_license_key_data', $data, $generator_id);
				LicenseResourceRepository::instance()->insert( $data );
			}
		}

		return $this->response( true, $licenses, 'v2/generators/{id}/generate' );
	}
}
