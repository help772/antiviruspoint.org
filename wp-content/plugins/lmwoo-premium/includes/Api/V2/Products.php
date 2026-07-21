<?php

namespace LicenseManagerForWooCommerce\Api\V2;

use Exception;
use LicenseManagerForWooCommerce\Abstracts\RestController as LMFWC_REST_Controller;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\ProductInstalledOn as ProductInstalledOnResourceRepository;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class Products extends LMFWC_REST_Controller {

	protected $namespace = 'lmfwc/v2';

	
	protected $rest_base = '/products';


	protected $settings = array();

	
	public function __construct() {
		$this->settings = get_option( 'lmfwc_settings_general', array() );
	}


	public function register_routes() {
	
		register_rest_route(
			$this->namespace,
			$this->rest_base . '/update/(?P<license_key>[\w-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'checkProductUpdate' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'license_key' => array(
							'description' => 'License Key',
							'type'        => 'string',
						),
					),
				),
			)
		);

	
		register_rest_route(
			$this->namespace,
			$this->rest_base . '/download/latest/(?P<license_key>[\w-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'downloadProductUpdate' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'license_key' => array(
							'description' => 'License Key',
							'type'        => 'string',
						),
					),
				),
			)
		);

		
		register_rest_route(
			$this->namespace,
			$this->rest_base . '/ping',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'processPing' ),
					'permission_callback' => array( $this, 'permissionCallback' ),
					'args'                => array(
						'license_key' => array(
							'description' => 'License Key',
							'type'        => 'string',
						),
					),
				),
			)
		);
	}

	
	public function checkProductUpdate( WP_REST_Request $request ) {
		if ( ! $this->isRouteEnabled( $this->settings, '024' ) ) {
			return $this->routeDisabledError();
		}

		if ( ! $this->permissionCheck( 'product', 'edit' ) ) {
			return new WP_Error( 'lmfwc_rest_cannot_view', esc_html__( 'Sorry, you cannot view this resource.', 'license-manager-for-woocommerce' ), array( 'status' => $this->authorizationRequiredCode() ) );
		}

		$license_key = sanitize_text_field( $request->get_param( 'license_key' ) );

		if ( empty( $license_key ) ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'License Key ID invalid.', array( 'status' => 404 ) );
		}

		try {
			
			$license = LicenseResourceRepository::instance()->findBy( 
				array( 
					/**
					 * Filter lmfwc_hash
					 * 
					 * @since 1.0.0
					 **/
					'hash' => apply_filters( 'lmfwc_hash', $license_key ), 
				) 
			);
		} catch ( Exception $e ) {
			return new WP_Error( 'lmfwc_rest_data_error', $e->getMessage(), array( 'status' => 404 ) );
		}

		if ( null === $license ) {
			return new WP_Error( 'lmfwc_rest_data_error', sprintf( 'License Key: %s could not be found.', $license_key ), array( 'status' => 404 ) );
		}

		$product_id = $license->getProductId();

		if ( null === $product_id ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'No product assigned to license.', array( 'status' => 404 ) );
		}

		$decrypted_license_key = $license->getDecryptedLicenseKey();
		$product               = wc_get_product( $license->getProductId() );
		$productDownloads      = $product->get_downloads();

		if ( ! empty( $productDownloads ) ) {
			$product_download_file = ABSPATH . ltrim( wp_make_link_relative( $product->get_file_download_path( lmfwc_array_key_first( $productDownloads ) ) ), '/' );

			if ( file_exists( $product_download_file ) ) {
				$lastUpdated = gmdate( 'Y-m-d H:i:s', filemtime( $product_download_file ) );
			}
		}

		$consumer_key    = '';
		$consumer_secret = '';
		$get_vars        = $_GET;

		// If the $_GET parameters are present, use those first.
		if ( ! empty( $get_vars['consumer_key'] ) && ! empty( $get_vars['consumer_secret'] ) ) {
			$consumer_key    = $get_vars['consumer_key'];
			$consumer_secret = $get_vars['consumer_secret'];
		}

		// If the above is not present, we will do full basic auth.
		if ( ! $consumer_key && ! empty( sanitize_text_field( $_SERVER['PHP_AUTH_USER'] ) ) && ! empty( sanitize_text_field( $_SERVER['PHP_AUTH_PW'] ) ) ) {
			$consumer_key    = sanitize_text_field( $_SERVER['PHP_AUTH_USER'] );
			$consumer_secret = sanitize_text_field( $_SERVER['PHP_AUTH_PW'] );
		}

		// Add key and secret to update url
		$package_url = add_query_arg(
			array(
				'consumer_key'    => $consumer_key,
				'consumer_secret' => $consumer_secret,
			),
			get_rest_url() . $this->namespace . $this->rest_base . '/download/latest/' . $decrypted_license_key
		);

		// Calculate rating in percent (WordPress 5 star rating system)
		$rating = $product->get_average_rating();

		if ( ! empty( $rating ) ) {
			$rating_percent = $rating / 5 * 100;
		}

		$update_data = array(
			'license_key'  => $license->getDecryptedLicenseKey(),
			'url'          => $product->get_permalink(),
			'new_version'  => $product->get_meta( 'lmfwc_licensed_product_version' ),
			'package'      => $package_url, // Link to download the latest update
			'tested'       => $product->get_meta( 'lmfwc_licensed_product_tested' ), // Testes up to WP version
			'requires'     => $product->get_meta( 'lmfwc_licensed_product_requires' ), // Required WP version
			'requires_php' => $product->get_meta( 'lmfwc_licensed_product_requires_php' ),
			'last_updated' => $lastUpdated,
			'rating'       => $rating_percent,
			'num_ratings'  => $product->get_rating_count(),
			'sections'     => array(
				'changelog' => preg_replace( "/\r\n|\r|\n/", '', wpautop( $product->get_meta( 'lmfwc_licensed_product_changelog' ) ) ),
			),
		);

		return $this->response( true, $update_data, 200, 'v2/products/update/{license_key}' );
	}

	
	public function downloadProductUpdate( WP_REST_Request $request ) {
		if ( ! $this->isRouteEnabled( $this->settings, '025' ) ) {
			return $this->routeDisabledError();
		}

		if ( ! $this->permissionCheck( 'product', 'download' ) ) {
			return new WP_Error( 'lmfwc_rest_cannot_view', esc_html__( 'Sorry, you cannot view this resource.', 'license-manager-for-woocommerce' ), array( 'status' => $this->authorizationRequiredCode() ) );
		}

		$license_key = sanitize_text_field( $request->get_param( 'license_key' ) );

		if ( ! $license_key ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'License Key ID invalid.', array( 'status' => 404 ) );
		}

		try {
			
			$license = LicenseResourceRepository::instance()->findBy( 
				array( 
					/**
					 * Filter lmfwc_hash
					 * 
					 * @since 1.0.0
					 **/
					'hash' => apply_filters( 'lmfwc_hash', $license_key ),
					 ) 
			);
		} catch ( Exception $e ) {
			return new WP_Error( 'lmfwc_rest_data_error', $e->getMessage(), array( 'status' => 404 ) );
		}

		if ( null === $license ) {
			return new WP_Error( 'lmfwc_rest_data_error', sprintf( 'License Key: %s could not be found.', $license_key ), array( 'status' => 404 ) );
		}

		$product_id = $license->getProductId();

		if ( null === $product_id ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'No product assigned to license.', array( 'status' => 404 ) );
		}

		$product = wc_get_product( $license->getProductId() );

		if ( null === $product ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'The assigned product could not be found.', array( 'status' => 404 ) );
		}

		$productDownloads      = $product->get_downloads();
		$product_download_file = ABSPATH . ltrim( wp_make_link_relative( $product->get_file_download_path( lmfwc_array_key_first( $productDownloads ) ) ), '/' );

		if ( empty( $product_download_file ) || ! file_exists( $product_download_file ) ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'Requested file not found.', array( 'status' => 404 ) );
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . basename( $product_download_file ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $product_download_file ) );
		ob_clean();
		flush();
		readfile( $product_download_file );

		$file_details_data = array(
			'filename'       => basename( $product_download_file ),
			'content-length' => filesize( $product_download_file ),
		);

		return $this->response( true, $file_details_data, 200, 'v2/products/download/latest/{license_key}' );
	}

	
	public function processPing( WP_REST_Request $request ) {
		if ( ! $this->isRouteEnabled( $this->settings, '026' ) ) {
			return $this->routeDisabledError();
		}

		if ( ! $this->permissionCheck( 'product', 'read' ) ) {
			return new WP_Error( 'lmfwc_rest_cannot_view', esc_html__( 'Sorry, you cannot view this resource.', 'license-manager-for-woocommerce' ), array( 'status' => $this->authorizationRequiredCode() ) );
		}

		$body = $request->get_params();

		$license_key  = isset( $body['license_key'] ) ? sanitize_text_field( $body['license_key'] ) : null;
		$product_name = isset( $body['product_name'] ) ? sanitize_text_field( $body['product_name'] ) : null;
		$host         = isset( $body['host'] ) ? sanitize_text_field( $body['host'] ) : null;
		$license_id   = null;

		if ( ! empty( $license_key ) ) {
			try {
				
				$license = LicenseResourceRepository::instance()->findBy(
				 array( 
					/**
					 * Filter lmfwc_hash
					 * 
					 * @since 1.0.0
					 **/
					'hash' => apply_filters( 'lmfwc_hash', $license_key ), 
				 ) 
				);
			} catch ( Exception $e ) {
				return new WP_Error( 'lmfwc_rest_data_error', $e->getMessage(), array( 'status' => 404 ) );
			}

			if ( ! $license ) {
				return new WP_Error( 'lmfwc_rest_data_error', sprintf( 'License Key: %s could not be found.', $license_key ), array( 'status' => 404 ) );
			}

			$license_id = $license->getId();
		}

		$product_installed_on = ProductInstalledOnResourceRepository::instance()->insertUpdate(
			array(
				'product_name' => $product_name,
				'license_id'   => $license_id,
				'host'         => $host,
				'last_ping'    => gmdate( 'Y-m-d H:i:s' ),
			),
			array(
				'product_name' => $product_name,
				'license_id'   => $license_id,
				'host'         => $host,
			),
			false
		);

		if ( ! $product_installed_on ) {
			return new WP_Error( 'lmfwc_rest_data_error', 'The product installed on could not be added to the database.', array( 'status' => 404 ) );
		}

		return $this->response( true, null, 200, 'v2/products/ping' );
	}
}
