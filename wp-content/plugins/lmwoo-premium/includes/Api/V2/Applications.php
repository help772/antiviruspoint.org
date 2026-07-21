<?php

namespace LicenseManagerForWooCommerce\Api\V2;

use DateTime;
use DateTimeZone;
use Exception;
use LicenseManagerForWooCommerce\Abstracts\RestController as LMFWC_REST_Controller;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\LicenseActivation as LicenseActivationResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\LicenseActivations as ActivationResourceRepository;
use LicenseManagerForWooCommerce\Enums\LicenseSource;
use LicenseManagerForWooCommerce\Models\Resources\Application as ApplicationResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\Application as ApplicationResourceRepository;
use LicenseManagerForWooCommerce\ApplicationManager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

/**
 * Class Applications
 *
 * @package LicenseManagerForWooCommerce\\Controllers
 */
class Applications extends LMFWC_REST_Controller {
	/**
	 * Namespace
	 * 
	 * @var string
	 */
	protected $namespace = 'lmfwc/v2';

	/**
	 * Rest base
	 * 
	 * @var string
	 */
	protected $rest_base = '/application';

	/**
	 * Settings
	 * 
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Licenses constructor.
	 */
	public function __construct() {
		$this->settings = (array) get_option( 'lmfwc_settings_general' );
	}

	/**
	 * Register all the needed routes for this resource.
	 */
	public function register_routes() {
		/**
		 * GET application/{application_id}
		 *
		 * Retrieves update information's about a WooCommerce product e.g. a WordPress plugin
		 */
		register_rest_route(
			$this->namespace, $this->rest_base . '/(?P<application_id>[\w-]+)', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'applicationInfo' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'application_id' => array(
							'description' => 'The application id',
							'type'        => 'string',
						),
					),
				),
			)
		);

		/**
		 * GET application/download/{identifier}
		 *
		 * Deliver update file for a WooCommerce product e.g. a WordPress plugin
		 */
		register_rest_route(
			$this->namespace, $this->rest_base . '/download/(?P<identifier>[\w-]+)', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'applicationDownload' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'identifier' => array(
							'description' => 'The activation token. If activation token is provided and if license is not expired then the package download url will be available.',
							'type'        => 'string',
						),
					),
				),
			)
		);
	}

	/**
	 * Callback for the GET application/{application_id} route.
	 *
	 * Checks if a product update is available.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function applicationInfo( WP_REST_Request $request ) { 
		if (!$this->isRouteEnabled($this->settings, '027')) {
			return $this->routeDisabledError();
		}

		if (!$this->permissionCheck('application', 'read')) {
			return new WP_Error(
				'lmfwc_rest_cannot_view',
				__('Sorry, you cannot list resources.', 'license-manager-for-woocommerce'),
				array(
					'status' => $this->authorizationRequiredCode(),
				)
			);
		}

		$activationToken = sanitize_text_field( $request->get_param( 'activation_token' ) );
		$license         = null;
		$activation      = null;
		$product         = null;

		if ( $activationToken ) {

			/* @var LicenseActivationResourceModel $activation */
			$activation = ActivationResourceRepository::instance()->findBy( array(
				'token' => $activationToken,
			) );

			if ( $activation ) {
				/* @var LicenseResourceModel $license */
				$license   = LicenseResourceRepository::instance()->find( $activation->getLicenseId() );
				$productId = $license->getProductId();
				if ( ! empty( $productId ) ) {
					$product = wc_get_product( $productId );
				}
			}
		}

		$applicationId = $request->get_param( 'application_id' );
		$context    = $request->get_param( 'context' );

		if ( empty( $applicationId ) ) {
			return new WP_Error(
				'data_error',
				'No application entry assigned to this product.',
				array( 'status' => 404 )
			);
		}

		/* @var ApplicationResourceModel $application */
		$application = ApplicationResourceRepository::instance()->find( $applicationId );
		if ( empty( $application ) ) {
			return new WP_Error(
				'data_error',
				'No application entry found for this product.',
				array( 'status' => 404 )
			);
		}

		
		$release = $application->getStableRelease();
	
		$applicationData = array(
		'name'           => isset($application) ? $application->getName() : null,
		'type'           => isset($application) ? $application->getType() : null,
		'stable_release' => isset($release) ? $release->getVersion() : null,
		'documentation'  => isset($application) ? preg_replace( "/\r\n|\r|\n/", '', strip_tags(wpautop($application->getDocumentation() ) ) ) : null,
		'support'        => isset($application) ? preg_replace( "/\r\n|\r|\n/", '', strip_tags(wpautop($application->getSupport() ) ) ) : null,
		'description'    => isset($application) ?  preg_replace( "/\r\n|\r|\n/", '', strip_tags(wpautop($application->getDescription() ) ) ) : null,
		'gallery'        => isset($application) ? $application->getGallery() : null,
	);


		if ( $activation instanceof LicenseActivationResourceModel && is_null( $activation->getDeactivatedAt() ) && ( ! empty( $license ) ) ) {
			$decrypted = $license->getDecryptedLicenseKey();
			if ( is_wp_error( $decrypted ) ) {
				$decrypted = '';
			}
			$metadata                     = $request->get_param( 'meta' );
			$applicationData['license']      = $decrypted;
			$applicationData['download_url'] = $this->getPackageUrl( $activation->getToken(), $metadata );
			$applicationData['url']          = $product ? get_permalink( $product->get_id() ) : '';
		}

		$applicationType = $request->get_param( 'type' ) ? $request->get_param( 'type' ) : '';
		$applicationData = $this->addReleaseDetails( $applicationData, $application, $applicationType, $release, $license, $activation );

		if ( in_array( $context, array( 'puc:theme', 'puc:plugin', 'puc' ) ) ) {
			return $applicationData;
		}

		return $this->response( true, $applicationData, 200, 'v2/application/{application_id}' );
	}

	/**
	 * Callback for the GET application/download/{activation_token} route. Performs a product download.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error
	 */
	public function applicationDownload( WP_REST_Request $request ) {

		if (!$this->isRouteEnabled($this->settings, '028')) {
			return $this->routeDisabledError();
		}

		if ( ! $this->permissionCheck( 'application', 'download' ) ) {
			return new WP_Error( 'lmfwc_rest_cannot_view', esc_html__( 'Sorry, you cannot view this resource.', 'license-manager-for-woocommerce' ), array( 'status' => $this->authorizationRequiredCode() ) );
		}
		

		$activationToken = sanitize_text_field( $request->get_param( 'identifier' ) );

		if ( ! $activationToken ) {
			return new WP_Error(
				'data_error',
				'License activation is invalid. (1)',
				array( 'status' => 404 )
			);
		}

		/* @var LicenseActivationResourceModel $activation */
		$activation = ActivationResourceRepository::instance()->findBy( 
			array(
			'token'          => $activationToken,
			'deactivated_at' => null,
			) 
		);

		if ( ! $activation ) {
			return new WP_Error(
				'data_error',
				'License activation is invalid. (2)',
				array( 'status' => 404 )
			);
		}

		/* @var LicenseResourceModel $license */
		$license = LicenseResourceRepository::instance()->find( $activation->getLicenseId() );

		if ( ! $license ) {
			return new WP_Error(
				'data_error',
				'License could not be found.',
				array( 'status' => 404 )
			);
		}

		$licenseExpired = $this->hasLicenseExpired( $license );
		if ( false !== $licenseExpired ) {
			return $licenseExpired;
		}
		

		$productId = $license->getProductId();

		if ( ! $productId ) {
			return new WP_Error(
				'data_error',
				'No product assigned to license.',
				array( 'status' => 404 )
			);
		}

		$product = wc_get_product( $license->getProductId() );

		if ( ! $product ) {
			return new WP_Error(
				'data_error',
				'The assigned product could not be found.',
				array( 'status' => 404 )
			);
		}

		$application = ApplicationResourceRepository::instance()->findByProduct( $product );
		if ( ! $application ) {
			return new WP_Error(
				'data_error',
				'No application entry assigned to this product.',
				array( 'status' => 404 )
			);
		}

		$release = $application->getStableRelease();
		$result  = ApplicationManager::instance()->stream( $release );

		if ( is_wp_error( $result ) ) {
			return new WP_Error( $result->get_error_data(), $result->get_error_message(), $result->get_error_data() );
		}

		// // Save download with meta.
		// $source         = ! empty( $request->get_param( 'source' ) ) ? $request->get_param( 'source' ) : LicenseSource::API;
		// $downloadParams = array(
		//  'source'        => $source,
		//  'license_id'    => $license->getId(),
		//  'activation_id' => $activation->getId(),
		//  'ip_address'    => clientIp(),
		//  'user_agent'    => userAgent(),
		//  'meta_data'     => array(),
		// );
		// $activationMeta = $request->get_param( 'meta' );
		// if ( is_array( $activationMeta ) ) {
		//  $downloadParams['meta_data'] = $activationMeta;
		// }

		// ProductDownload::instance()->insert( $downloadParams );
		exit;
	}

	/**
	 * Return the package url
	 *
	 * @param $ActivationToken
	 * @param array $metadata
	 *
	 * @return string
	 */
	protected function getPackageUrl( $ActivationToken, $metadata = array() ) {
		$consumerKey    = '';
		$consumerSecret = '';
		$data = $_REQUEST;
		$server = $_SERVER;
		// If the $_GET parameters are present, use those first.
		if ( ! empty( $data['consumer_key'] ) && ! empty( $_GET['consumer_secret'] ) ) {
			$consumerKey    = $data['consumer_key'];
			$consumerSecret = $data['consumer_secret'];
		}

		// If the above is not present, we will do full basic auth.
		if ( ! $consumerKey && ! empty( $server['PHP_AUTH_USER'] ) && ! empty( $server['PHP_AUTH_PW'] ) ) {
			$consumerKey    = $server['PHP_AUTH_USER'];
			$consumerSecret = $server['PHP_AUTH_PW'];
		}

		// Add key and secret to update url
		$package_url = add_query_arg( array(
			'consumer_key'    => $consumerKey,
			'consumer_secret' => $consumerSecret,
		), get_rest_url() . $this->namespace . $this->rest_base . '/download/' . $ActivationToken );

		if ( ! empty( $metadata ) && is_array( $metadata ) ) {
			$package_url = add_query_arg( $metadata, $package_url );
		}

		return $package_url;
	}

	/**
	 * The release data
	 *
	 * @param array $applicationData
	 * @param ApplicationResourceModel $application
	 * @param string $applicationType
	 * @param ApplicationReleaseResourceModel $release
	 * @param LicenseResourceModel $license
	 * @param LicenseActivationResourceModel $activation
	 *
	 * @return array
	 */
	protected function addReleaseDetails( $applicationData, $application, $applicationType, $release, $license, $activation ) {

		if ( empty( $release ) || empty( $application ) ) {
			return $applicationData;
		}
		switch ( $applicationType ) {
			case 'wp':
			$applicationData['details'] = array(
				'name'         => $application->getName(),
				'slug'         => $this->sanitize_slug( $application->getName() ),
				'tested'       => $release->getMeta( 'tested_wp' ),
				'requires'     => $release->getMeta( 'requires_wp' ),
				'requires_php' => $release->getMeta( 'requires_php' ),
				'last_updated' => $release->getCreatedAt(),
				'stable_tag'   => $release->getVersion(),
				'sections'     => array(
					'changelog' => preg_replace( "/\r\n|\r|\n/", '', wpautop( $release->getChangelog() ) ),
					'description'    => isset($application) ?  preg_replace( "/\r\n|\r|\n/", '', strip_tags(wpautop($application->getDescription() ) ) ) : null,
				),
			);
				break;
		}
		/**
		 * Filter lmfwc_rest_api_release_details
		 * 
		 * @since 1.0
		**/
		return apply_filters( 'lmfwc_rest_api_release_details', $applicationData, $license, $activation, $release );
	}

	/**
	 * Checks if the license has an expiry date and if it has expired already.
	 *
	 * @param LicenseResourceModel $license
	 * @return false|WP_Error
	 */
	private function hasLicenseExpired( $license ) {
		$expiresAt = $license->getExpiresAt();
		if ( $expiresAt ) {
			try {
				$dateExpiresAt = new DateTime($expiresAt);
				$dateNow = new DateTime('now', new DateTimeZone('UTC'));
			} catch (Exception $e) {
				return new WP_Error('lmfwc_rest_license_expired', $e->getMessage());
			}

			if ($dateNow > $dateExpiresAt) {
				return new WP_Error(
					'lmfwc_rest_license_expired',
					sprintf('The license Key expired at %s.', wp_date( lmfwc_expiration_format(), strtotime( $license->getExpiresAt() ) )),
					array( 'status' => 405 )
				);
			}
		}

		return false;
	}
}
