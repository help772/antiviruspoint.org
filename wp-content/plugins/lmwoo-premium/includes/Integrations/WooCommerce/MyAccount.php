<?php
// nosemgrep: all
namespace LicenseManagerForWooCommerce\Integrations\WooCommerce;

use Dompdf\Dompdf;
use Exception;
use LicenseManagerForWooCommerce\Settings;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\LicenseActivations as ActivationResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\Application as ApplicationResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;

use LicenseManagerForWooCommerce\Enums\ActivationSource;

defined('ABSPATH') || exit;

class MyAccount {

	/**
	 * MyAccount constructor.
	 */
	public function __construct() {
		add_rewrite_endpoint('view-license-keys', EP_ROOT | EP_PAGES);
		add_rewrite_endpoint('view-applications', EP_ROOT | EP_PAGES);

		add_filter( 'woocommerce_account_menu_items', array( $this, 'accountMenuItems' ), 10, 1);
		add_action( 'woocommerce_account_view-license-keys_endpoint', array( $this, 'viewLicenseKeys' ));
		add_action( 'woocommerce_account_view-applications_endpoint', array( $this, 'viewApplications' ));
		add_action( 'lmfwc_myaccount_licenses_single_page_end', array( $this, 'addSingleApplicationTable' ) , 10, 5); 
		add_action( 'lmfwc_myaccount_licenses_single_page_end', array( $this, 'addSingleLicenseActivationsTable' ), 10, 5 );
		add_action( 'wp_loaded', array( $this, 'handleCustomActions' ));
	}

	/**
	 * Prints out the licenses activation table
	 *
	 * @param License $license
	 * @param $order
	 * @param $product
	 * @param $dateFormat
	 * @param $licenseKey
	 *
	 * @return string
	 */
	public static function addSingleApplicationTable( $license, $order = null, $product = null, $dateFormat = null, $licenseKey = null ) {
		
		$application = ApplicationResourceRepository::instance()->findByLicense( $license );
		$releases = ApplicationResourceRepository::instance()->findApplicationReleases( $license );
		wc_get_template(
			'/myaccount/single-table-applications.php',
			array(
				'license'    => $license,
				'application'=> $application,
				'releases'   => $releases,
				'nonce'      => wp_create_nonce( 'lmfwc_nonce' ),
			),
			'',
			LMFWC_TEMPLATES_DIR
		);
	}

	public function viewApplications() {
		  global $wp_query;
		   wp_enqueue_style('lmfwc_admin_css', LMFWC_CSS_URL . 'main.css');
		$user             = wp_get_current_user();

		if ( ! $user ) {
			return;
		}

		$applicationId = null;
	   
		// Parse query parameters.
		if ( $wp_query->query['view-applications'] ) {
			$page = $wp_query->query['view-applications'];
			if ( ! empty( $page ) ) {
				$parts      = explode( '/', $page );
				$applicationId = (int) $parts[0];
			}
		}

		if ( ! $applicationId ) {

			$downloads = ApplicationResourceRepository::instance()->queryApplicationDownloads( array(
				'customer_id' => $user->ID,
			) );


			wc_get_template(
				'/myaccount/lmfwc-view-applications.php',
				array(
					'downloads'               => $downloads,
					'date_format'             => get_option( 'date_format' ),
					'nonce'                   => wp_create_nonce( 'lmfwc_account' ),
				),
				'',
			  LMFWC_TEMPLATES_DIR
			);

		} else {
	   
			$license = LicenseResourceRepository::instance()->findBy( array( 'id' => $applicationId ) );

			if ( is_wp_error( $license ) || $license->getUserId() != $user->ID ) {
				printf( '<h3>%s</h3>', esc_html__( 'Not found', 'license-manager-for-woocommerce' ) );
				printf( '<p>%s</p>', esc_html__( 'The license you are looking for is not found.', 'license-manager-for-woocommerce' ) );

				return;

			}

			$decrypted = $license->getDecryptedLicenseKey();
			if ( is_wp_error( $decrypted ) ) {
				printf( '<p>%s</p>', esc_attr( $decrypted->get_error_message() ) );

				return;
			}

			
		$application = ApplicationResourceRepository::instance()->findByLicense( $license );
		$releases = ApplicationResourceRepository::instance()->findApplicationReleases( $license );

		wc_get_template(
			'myaccount/single-application.php',
			array(
				'license'  => $license,
				'application' => $application,
				'releases' => $releases,
				'nonce'    => wp_create_nonce( 'lmfwc_nonce' ),
			),
			'',
			LMFWC_TEMPLATES_DIR
		);
		}
	}

	public function handleCustomActions() {
		$lmfwc_data = $_REQUEST;
		$user = wp_get_current_user();
		if (!$user) {
			return;
		}

		$action    = isset( $lmfwc_data['lmfwc_action'] ) ? sanitize_text_field( $lmfwc_data['lmfwc_action'] ) : '';
		if (array_key_exists('action', $lmfwc_data)) {
			$licenseKey =  isset( $lmfwc_data['license'] )  ? sanitize_text_field( $lmfwc_data['license']) : '';

			if ( 'activate' === $lmfwc_data['action']   && Settings::get('lmfwc_allow_users_to_activate' , Settings::SECTION_WOOCOMMERCE)) {
				$nonce = wp_verify_nonce(@$lmfwc_data['_wpnonce'], 'lmfwc_myaccount_activate_license');
				if ($nonce) {
					$args = array();
					$args['source'] = ActivationSource::WEB;
					  $activate = lmfwc_activate_license($licenseKey, $args);
					if ( is_wp_error ( $activate ) ) {

						wc_add_notice(__('License Key is Expired .' , 'license-manager-for-woocommerce'), 'error');
					}
					 
				}
			}
		   
		 

			if ( 'deactivate' === $lmfwc_data['action']   && Settings::get('lmfwc_allow_users_to_deactivate' , Settings::SECTION_WOOCOMMERCE)) {
				$token      = $lmfwc_data['token'];
				$optional = '';
				$args = array( 
					 'token' => $token, 
				);
				$nonce = wp_verify_nonce($lmfwc_data['_wpnonce'], 'lmfwc_myaccount_deactivate_license');
				if ($nonce) {
					try {
						lmfwc_deactivate_license( $optional, $args);
					} catch (Exception $e) {
						return;
					}
				}
			}

			if ( 'reactivate' === $lmfwc_data['action']  ) {

				$token      = $lmfwc_data['token'];
				$nonce = wp_verify_nonce($lmfwc_data['_wpnonce'], 'lmfwc_myaccount_reactivate_license');

				if ($nonce) {
					try {
						 $reactivation = lmfwc_reactivate_license($token);
						if ( is_wp_error ( $reactivation ) ) {

							wc_add_notice(__('License Key is Expired Cannot be Reactivate. ' , 'license-manager-for-woocommerce'), 'error');
						}
					} catch (Exception $e) {
						return;
					}
				}
			}
			
			if ( 'delete'  === $lmfwc_data['action'] ) {

				$activation_id = isset($lmfwc_data['activation_id']) ? $lmfwc_data['activation_id'] : '';
				$license_id = isset($lmfwc_data['license_id']) ? $lmfwc_data['license_id'] : '';
				$nonce = wp_verify_nonce( @$lmfwc_data['_wpnonce'] , 'lmfwc_myaccount_delete_license');

				if ($nonce) {
					try {
						lmfwc_delete_activation($activation_id, $license_id);
					} catch (Exception $e) {
						return;
					}
				}
			}

			if ( 'lmfwc_download_license_pdf' === $lmfwc_data['action']   && Settings::get('lmfwc_download_certificates' , Settings::SECTION_WOOCOMMERCE)) {

				$nonce = wp_verify_nonce($lmfwc_data['_wpnonce'], 'lmfwc_myaccount_download_certificates');

				if ($nonce) {
					$this->lmfwcGeneratePDFCertificate($licenseKey);
				}
			}
			if ( 'application_download' === $lmfwc_data['action'] ) {
				
			/**
			 * Validate order
			 */
			$errors    = array();
			$order     = null;
			$licenseId = isset( $lmfwc_data['license_id'] ) ? sanitize_text_field( $lmfwc_data['license_id'] ) : null;
			/* @var LicenseResourceModel $license */
			$license = LicenseResourceRepository::instance()->find( $licenseId );
				if ( false === $license ) {
					array_push( $errors, __( 'License not found.', 'license-manager-for-woocommerce' ) );
				} else {
					$order = wc_get_order( $license->getOrderId() );
					if ( empty( $order ) ) {
						array_push( $errors, __( 'Permission denied.', 'license-manager-for-woocommerce' ) );
					}
				}

			/**
			 *  Validate customer
			 */
				if ( ! $order || get_current_user_id() !== $order->get_customer_id() ) {
					array_push( $errors, __( 'Permission denied.', 'license-manager-for-woocommerce' ) );
				}

			/**
			 * Validate release
			 */
			$releaseId = isset( $lmfwc_data['application_release'] ) ? intval( $lmfwc_data['application_release'] ) : null;
			/* @var ApplicationReleaseResourceRepository $release */
			$release = ApplicationReleaseResourceRepository::instance()->find( $releaseId );
				if ( empty( $release ) || empty( $release->getDownloadFile() ) ) {
					array_push( $errors, __( 'Application release not found.', 'license-manager-for-woocommerce' ) );
				}

			/**
			 * Check file expiration
			 */
				if ( ! $release->isDownloadAllowed( $license ) ) {
					array_push( $errors, __( 'License is expired. The release could not be download.', 'license-manager-for-woocommerce' ) );
				}

			/**
			 * Validate application
			 */
			$application = ! empty( $release ) ? ApplicationResourceRepository::instance()->find( $release->getApplicationId() ) : false;
				if ( ! $application ) {
					array_push( $errors, __( 'Application not found.', 'license-manager-for-woocommerce' ) );
				}

			/**
			 * Validate the release file
			 */
			$releaseFile = (int) $release->getDownloadFile();
			$releasePath = get_attached_file( $releaseFile );
			$releaseExt  = pathinfo( $releasePath, PATHINFO_EXTENSION );
			// nosemgrep: audit.php.lang.security.file.phar-deserialization
			$releaseName = sprintf( '%s-%s.%s', pathinfo( get_the_title( $releaseFile ), PATHINFO_FILENAME ), $release->getVersion(), $releaseExt );
				if ( ! file_exists( $releasePath ) ) {  // nosemgrep
					array_push( $errors, __( 'Application not found.', 'license-manager-for-woocommerce' ) );
				}

				if ( count( $errors ) > 0 ) {
					wc_add_notice( $errors[0], 'error' );
					wp_safe_redirect( wc_get_account_endpoint_url( sprintf( 'view-license-keys/%s', $releaseId ) ) );
					exit;
				} else {

					header( 'Content-Description: File Transfer' );
					header( 'Content-Type: application/octet-stream' );
					header( 'Content-Disposition: attachment; filename=' . $releaseName );
					header( 'Content-Transfer-Encoding: binary' );
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
					header( 'Pragma: public' );
					header( 'Content-Length: ' . filesize( $releasePath ) );
					ob_clean();
					flush();
					readfile( $releasePath ); // nosemgrep
					exit;
				}

			}
		}
	}

	/**
	 * Prints out the licenses activation table
	 *
	 * @param License $license
	 * @param $order
	 * @param $product
	 * @param $dateFormat
	 * @param $licenseKey
	 *
	 * @return string
	 */
	public static function addSingleLicenseActivationsTable( $license, $order = null, $product = null, $dateFormat = null, $licenseKey = null ) {


		if ( is_null( $order ) ) {
			$order = wc_get_order( $license->getOrderId() );
		}

		if ( is_null( $product ) ) {
			$product = wc_get_order( $license->getProductId() );
		}

		if ( is_null( $dateFormat ) ) {
			$dateFormat = get_option( 'date_format' );
		}

		if ( is_null( $licenseKey ) ) {
			$licenseKey = $license->getDecryptedLicenseKey();
		}
		/**
		* Filter lmfwc_get_license_activations
		* 
		* @since 1.0
		**/
		$activations = apply_filters('lmfwc_get_license_activations', $license->getId());


	 
		wc_get_template(
			'myaccount/single-table-activations.php',
			array(
				'license'                    => $license,
				'license_key'                => $licenseKey,
				'product'                    => $product,
				'order'                      => $order,
				'date_format'                => $dateFormat,
				 'activations'                => $activations,
				'nonce'                      => wp_create_nonce( 'lmfwc_nonce' ),
			),
			'',
			LMFWC_TEMPLATES_DIR
		);
	}

	/**
	 * Adds the plugin pages to the "My account" section.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function accountMenuItems( $items ) {
		$customItems = array();
		$customItems['view-license-keys'] = __('License keys', 'license-manager-for-woocommerce');
		$customItems['view-applications'] = __('Applications', 'license-manager-for-woocommerce');
		$customItems = array_slice( $items, 0, 2, true ) + $customItems + array_slice( $items, 2, count( $items ), true );
		return $customItems;
	}

	/**
	 * Creates an overview of all purchased license keys.
	//  */
	public function viewLicenseKeys() {
		global $wp_query;
		wp_enqueue_style('lmfwc_admin_css', LMFWC_CSS_URL . 'main.css');
		$user = wp_get_current_user();
		$licenseID = null;
		$page = 1;
		
		if ($wp_query->query['view-license-keys']) {

			$page = intval($wp_query->query['view-license-keys']);
			if ( ! empty( $page ) ) {
				$parts = explode( '/', $page );
				if ( count( $parts ) === 2 && 'page' === $parts[0] ) {
					$paged = (int) $parts[1];
				} else {
					$licenseID = sanitize_text_field( $parts[0] );
				}
			}

		}

		

		if (  !$licenseID ) {
			/**
			* Filter lmfwc_get_all_customer_license_keys
			* 
			* @since 1.0
			**/
			$licenseKeys = apply_filters('lmfwc_get_all_customer_license_keys', $user->ID);
		  
			wc_get_template(
				'myaccount/lmfwc-view-license-keys.php',
				array(
					'dateFormat'  => get_option('date_format'),
					'licenseKeys' => $licenseKeys,
					'page'        => $page,
				),
				'',
				LMFWC_TEMPLATES_DIR
			);
		} else {

			 $license = LicenseResourceRepository::instance()->findBy(
					array(
						'id' => $licenseID,
					)
				);
			 
			if (  ! $license || is_wp_error( $license ) || $license->getUserId() != $user->ID ) {
				printf( '<h3>%s</h3>', esc_html__( 'Not found', 'license-manager-for-woocommerce' ) );
				printf( '<p>%s</p>', esc_html__( 'The license you are looking for is not found.', 'license-manager-for-woocommerce' ) );

				return;

			}

			$decrypted = $license->getDecryptedLicenseKey();
			if ( is_wp_error( $decrypted ) ) {
				printf( '<p>%s</p>', esc_attr( $decrypted->get_error_message() ) );

				return;
			}
				wc_get_template(
					'myaccount/single.php',
					array(
						'license'     => $license,
						'license_key' => $license->getDecryptedLicenseKey(),
						'product'     => ! empty( $license->getProductId() ) ? wc_get_product( $license->getProductId() ) : null,
						'order'       => ! empty( $license->getOrderId() ) ? wc_get_order( $license->getOrderId() ) : null,
						'date_format' => get_option( 'date_format' ),
					),
					'',
					LMFWC_TEMPLATES_DIR
				);

		}
	}

	/**
	 * Generate license certificate in PDF
	 *
	 * @param $license
	 *
	 * @return void
	 */
	public function lmfwcGeneratePDFCertificate( $licenseKey ) {

	   $license = LicenseResourceRepository::instance()->findBy(
			array(
				/**
				* Filter lmfwc_hash
				* 
				* @since 1.0
				**/
				'hash' => apply_filters('lmfwc_hash', $licenseKey),
			)
		);

		$errors = array();
		$order  = null;

		if ( is_wp_error( $license ) ) {
			array_push( $errors, $license->get_error_message() );
		} else {
			$order = wc_get_order( $license->getOrderId() );
			if ( empty( $order ) ) {
				array_push( $errors, __( 'Permission denied.', 'license-manager-for-woocommerce' ) );
			}
		}

		/**
		 *  Validate customer
		 */
		if ( ! $order || get_current_user_id() !== $order->get_customer_id() ) {
			array_push( $errors, __( 'Permission denied.', 'license-manager-for-woocommerce' ) );
		}
		if ( ! empty( $errors ) ) {
			wp_die( esc_attr( $errors[0] ) );
		}

		/**
		 * Render the template
		 */
		$html = wc_get_template_html(
			'myaccount/single-certificate.php',
			$this->lmfwcGetCertificateData( $license ),
			'',
			LMFWC_TEMPLATES_DIR
		);
		/**
		 * Output the template
		 */

		$pdf = new DOMPDF();
		$pdf->set_option('enable_html5_parser', true);
		$pdf->set_option('isRemoteEnabled', true);
		$pdf->loadHtml($html, 'UTF-8');
		$pdf->setPaper('A4', 'landscape');
		$pdf->render();
		$pdf->stream(gmdate('Y-m-d') . '_license_certificate.pdf', array( 'attachment'=>true ));
		update_option('license_certificate_counter', (int) get_option('license_certificate_counter', 0) + 1);
	}


	/**
	 * Return the license certification data
	 *
	 * @param License $license
	 *
	 * @return mixed|void
	 */
	private function lmfwcGetCertificateData( $license ) {

		/**
		 * The data template
		 */
		$data = array(
			'title'                => '',
			'logo'                 => '',
			'license_product_name' => '',
			'license_details'      => array(), // eg. array('title' => 'Product Name', 'value' => 'Counter Strike')
		);

		/**
		 * Add option to developers to add their own data and skip our data generation process
		 */
		/**
		* Filter lmfwc_license_certification_prefilter_data
		* 
		* @since 1.0
		**/
		$data = apply_filters( 'lmfwc_license_certification_prefilter_data', $data, $license );
		if ( ! empty( $data['is_final'] ) ) {
			/**
			* Filter lmfwc_license_certification_data
			* 
			* @since 1.0
			**/
			return apply_filters( $data, 'lmfwc_license_certification_data', $data, $license );
		}


		/**
		 * Get the logo
		 */
		$logo = Settings::get( 'lmfwc_company_logo', Settings::SECTION_WOOCOMMERCE );
		if ( ! is_numeric( $logo ) ) {
			$logo = get_theme_mod( 'lmfwc_company_logo' );
		}

		/**
		 * Get basic details
		 */
		$product  = $license->getProductId() ? wc_get_product( $license->getProductId() ) : null;
		$order    = $license->getOrderId() ? wc_get_order( $license->getOrderId() ) : null;
		$customer = $order ? $order->get_customer_id() : null;


		/**
		 * Setup the license details
		 */
		$expiry_date = $license->getExpiresAt();
		if ( empty( $expiry_date ) && empty( $license->getValidFor() ) ) {
			$expiry_date = __( 'Never Expires', 'license-manager-for-woocommerce' );
		} else {
			$expiry_date = wp_date( lmfwc_expiration_format(), strtotime( $expiry_date ) );
		}


		$license_details = array(
			array(
				'title' => __( 'License ID', 'license-manager-for-woocommerce' ),
				'value' => sprintf( '#%d', $license->getId() ),
			),
			array(
				'title' => __( 'License Key', 'license-manager-for-woocommerce' ),
				'value' => $license->getDecryptedLicenseKey(),
			),
			array(
				'title' => __( 'Expiry Date', 'license-manager-for-woocommerce' ),
				'value' => $expiry_date,
			),
		);
		if ( $customer ) {
			$customer          = get_user_by( 'id', $customer );
			$license_details[] = array(
				'title' => __( 'Licensee', 'license-manager-for-woocommerce' ),
				'value' => sprintf(
					'%s (#%d - %s)',
					$customer->display_name,
					$customer->ID,
					$customer->user_email
				),
			);
			if ( $order ) {
				$license_details[] = array(
					'title' => __( 'Order ID', 'license-manager-for-woocommerce' ),
					'value' => sprintf( '#%d', $order->get_id() ),
				);
				$license_details[] = array(
					'title' => __( 'Order Date', 'license-manager-for-woocommerce' ),
					'value' => date_i18n( wc_date_format(), strtotime( $order->get_date_paid() ) ),
				);
			}
		}
		if ( $product ) {
			$license_details[] = array(
				'title' => __( 'Product Name', 'license-manager-for-woocommerce' ),
				'value' => $product->get_formatted_name(),
			);
			$license_details[] = array(
				'title' => __( 'Product URL', 'license-manager-for-woocommerce' ),
				'value' => $product->get_permalink(),
			);
		}

		/**
		 * Setup the data
		 */
		$data['title']                = get_bloginfo( 'name' );
		$data['logo']                 = is_numeric( $logo ) ? wp_get_attachment_image_url( $logo, 'full' ) : null;
		$data['license_product_name'] = $product ? $product->get_formatted_name() : null;
		$data['license_details']      = $license_details;
		/**
		* Filter lmfwc_license_certification_data
		* 
		* @since 1.0
		**/
		return apply_filters( 'lmfwc_license_certification_data', $data, $license );
	}
}
