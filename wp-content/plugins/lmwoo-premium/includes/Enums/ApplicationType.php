<?php


namespace LicenseManagerForWooCommerce\Enums;

/**
 * Class ApplicationType
 *
 * @package LicenseManagerForWooCommerce\Enums
 */
abstract class ApplicationType {

	const WORDPRESS = 'wordpress';
	const OTHER = 'other';

	/**
	 * List of application fields
	 *
	 * @return array[]
	 */
	public static function applicationFields() {
		return array(
			array(
				'id'          => 'name',
				'label'       => __( 'Name', 'license-manager-for-woocommerce' ),
				'description' => __( 'A short name to describe the application.', 'license-manager-for-woocommerce' ),
				'required'    => true,
				'type'        => 'text',
			),
			array(
				'id'          => 'type',
				'label'       => __( 'Type', 'license-manager-for-woocommerce' ),
				'description' => __( 'The type determines the metadata in the application release editor.', 'license-manager-for-woocommerce' ),
				'required'    => true,
				'type'        => 'select',
				'options'     => 'computed', // computed variable.
			),
			array(
				'id'          => 'stable_release_id',
				'label'       => __( 'Stable Release', 'license-manager-for-woocommerce' ),
				'description' => __( 'The current stable version, this is used in the application info API endpoint', 'license-manager-for-woocommerce' ),
				'required'    => false,
				'type'        => 'select',
				'options'     => 'computed', // computed variable.
			),
			array(
				'id'          => 'description',
				'label'       => __( 'Short description', 'license-manager-for-woocommerce' ),
				'description' => __( 'A short description to describe the application.', 'license-manager-for-woocommerce' ),
				'required'    => false,
				'type'        => 'textarea',
			),
			array(
				'id'          => 'documentation',
				'label'       => __( 'Documentation', 'license-manager-for-woocommerce' ),
				'description' => __( 'A short info about the application documentation. Maybe link your documentation pages here.', 'license-manager-for-woocommerce' ),
				'required'    => false,
				'type'        => 'textarea',
			),
			array(
				'id'          => 'support',
				'label'       => __( 'Support', 'license-manager-for-woocommerce' ),
				'description' => __( 'A short info about the application support. Maybe link your ticketing application pages here.', 'license-manager-for-woocommerce' ),
				'required'    => false,
				'type'        => 'textarea',
			),
			array(
				'id'          => 'gallery',
				'label'       => __( 'Gallery', 'license-manager-for-woocommerce' ),
				'description' => __( 'A list of screenshots that will be displayed in your product page.', 'license-manager-for-woocommerce' ),
				'required'    => false,
				'type'        => 'gallery',
			),
		);
	}

	/**
	 * List of fields
	 *
	 * @return array[]
	 */
	public static function releaseFields() {
		return array(
			array(
				'id'          => 'version',
				'label'       => __( 'Version', 'license-manager-for-woocommerce' ),
				'description' => __( 'A string that represents this release version.', 'license-manager-for-woocommerce' ),
				'required'    => true,
				'type'        => 'text',
			),
			array(
				'id'          => 'download_file',
				'label'       => __( 'Release file', 'license-manager-for-woocommerce' ),
				/* translators: %s: Max upload file size. */
				'description' => sprintf( __( 'A release file that represents this release. <strong>Max upload size: %s</strong> - Upload in Zip format', 'license-manager-for-woocommerce' ), size_format( wp_max_upload_size() ) ),
				'required'    => true,
				'type'        => 'file',
			),
			array(
				'id'          => 'changelog',
				'label'       => __( 'Changelog', 'license-manager-for-woocommerce' ),
				'description' => __( 'A short about what is changed in this release.', 'license-manager-for-woocommerce' ),
				'required'    => true,
				'type'        => 'textarea',
			),
		);
	}

	/**
	 * List of fields
	 *
	 * @return \array[][]
	 */
	public static function releaseTypes() {
		$types = array(
			self::WORDPRESS => array(
				'name'   => __( 'WordPress', 'license-manager-for-woocommerce' ),
				'fields' => array(
					'tested_wp'    => array(
						'id'          => 'tested_wp',
						'label'       => __( 'Test up to', 'license-manager-for-woocommerce' ),
						'description' => __( 'A version number of WordPress that your application is tested with.', 'license-manager-for-woocommerce' ),
						'required'    => true,
						'type'        => 'text',
					),
					'requires_wp'  => array(
						'id'          => 'requires_wp',
						'label'       => __( 'Minimum WordPress version', 'license-manager-for-woocommerce' ),
						'description' => __( 'A minimum WordPress version required to run your application.', 'license-manager-for-woocommerce' ),
						'required'    => true,
						'type'        => 'text',
					),
					'requires_php' => array(
						'id'          => 'requires_php',
						'label'       => __( 'Minimum PHP version', 'license-manager-for-woocommerce' ),
						'description' => __( 'A minimum PHP version required to run your application.', 'license-manager-for-woocommerce' ),
						'required'    => true,
						'type'        => 'text',
					),
				),
			),
			self::OTHER     => array(
				'name'   => __( 'Other', 'license-manager-for-woocommerce' ),
				'fields' => array(),
			),

		);
		/**
		 * Filter lmfwc_release_types
		 * 
		 * @since 1.0
		**/
		$types = apply_filters( 'lmfwc_release_types', $types );

		return $types;
	}
}
