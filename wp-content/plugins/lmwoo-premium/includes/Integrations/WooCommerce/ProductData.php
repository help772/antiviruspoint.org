<?php

namespace LicenseManagerForWooCommerce\Integrations\WooCommerce;

use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Settings;
use LicenseManagerForWooCommerce\Models\Resources\Generator as GeneratorResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\Application as ApplicationResourceRepository;
use WP_Error;
use WP_Post;

defined('ABSPATH') || exit;

class ProductData {

	/**
	 * ADMIN_TAB_NAME
	 *
	 * @var string
	 */
	const ADMIN_TAB_NAME = 'license_manager_tab';
	/**
	 * ADMIN_TAB_TARGET
	 *
	 * @var string
	 */
	const ADMIN_TAB_TARGET = 'license_manager_product_data';

	/**
	 * ProductData constructor.
	 */
	public function __construct() {
		/**
		 * Construct
		 *
		 * @see https://www.proy.info/woocommerce-admin-custom-product-data-tab/
		 */
		add_filter( 'woocommerce_product_tabs', array( $this, 'productTabs' ), 100, 1 );
		add_action( 'woocommerce_product_meta_start', array( $this, 'productMeta' ), 5 );
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'simpleProductLicenseManagerTab' ));
		add_action( 'woocommerce_product_data_panels', array( $this, 'simpleProductLicenseManagerPanel' ));
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variableProductLicenseManagerFields' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'variableProductLicenseManagerSaveAction' ), 10, 2 );
		// Change the product_data_tab icon
		add_action( 'admin_head', array( $this, 'styleInventoryManagement' ));
		add_action( 'save_post', array( $this, 'savePost' ), 10);
	}

	/**
	 * Customize product data tabs
	 *
	 * @param $tabs
	 * @param \WC_Product $product
	 *
	 * @return mixed
	 */
	public function productTabs( $tabs ) {

		global $post;
		if ( empty( $post->ID ) ) {
			return $tabs;
		}
		wp_enqueue_style('lmfwc_admin_css', LMFWC_CSS_URL . 'main.css');
		$application = ApplicationResourceRepository::instance()->findByProduct( $post->ID );
		if ( empty( $application ) ) {
			return $tabs;
		}

		// Documentation
		$documentation = $application->getDocumentation();
		if ( ! empty( $documentation ) ) {
			$tabs['documentation'] = array(
				'title'    => __( 'Documentation', 'license-manager-for-woocommerce' ),
				'priority' => 20,
				'callback' => function () use ( $documentation ) {
					echo wp_kses( $documentation, lmfwc_allowed_html() );
				},
			);
		}


		// Screenshots
		$gallery = $application->getGallery();
		if (!empty($gallery)) {
			$tabs['screenshots'] = array(
				'title' => __('Screenshots', 'woocommerce'),
				'priority' => 20,
				'callback' => function () use ( $gallery ) {

					$gifs = 0;
					$columns = 5;
					/**
					* Filter woocommerce_single_product_image_gallery_classes
					* 
					* @since 1.0
					**/
					$wrapper_classes = apply_filters(
						'woocommerce_single_product_image_gallery_classes',
						array(
							'woocommerce-product-gallery',
							'woocommerce-product-gallery--columns-' . absint($columns),
							'images',
						)
					);


					?><div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" data-columns="<?php echo esc_attr($columns); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;width: 543px;">
					<div class="woocommerce-product-gallery__wrapper">
						<?php

						$main_image = true;
						foreach ($gallery as $screenshot) {
							$imageId = isset($screenshot['id']) ? $screenshot['id'] : '';
							$imageDesc = isset($screenshot['description']) ? $screenshot['description'] : '';

							add_filter( 'woocommerce_gallery_image_html_attachment_image_params', function ( $args ) use ( $imageDesc ) {

								$args['data-caption'] = $imageDesc;
								return $args;
							} );
								/**
								* Filter woocommerce_single_product_image_thumbnail_html
								* 
								* @since 1.0
								**/
								echo wp_kses_post( apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $imageId, $main_image ), $imageId ) );
								$main_image = false;
						}
							
						?>
						</div>
					</div>
					<?php
				},
			);
		}



	// Support
		$support = $application->getSupport();
		if ( ! empty( $support ) ) {
			$tabs['support'] = array(
				'title'    => __( 'Support', 'license-manager-for-woocommerce' ),
				'priority' => 25,
				'callback' => function () use ( $support ) {
					echo wp_kses( $support, lmfwc_allowed_html() );
				},
			);
		}
	// Support
		$description = $application->getDescription();
		if ( ! empty( $description ) ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'license-manager-for-woocommerce' ),
				'priority' => 25,
				'callback' => function () use ( $description ) {
					echo wp_kses( $description, lmfwc_allowed_html() );
				},
			);
		}
		$release = $application->getStableRelease();
	// Documentation
		if ( $release  ) {
			$changelog = $release->getChangelog();
			if ( ! empty( $changelog ) ) {
				$tabs['changelog'] = array(
					'title'    => __( 'Changelog', 'license-manager-for-woocommerce' ),
					'priority' => 20,
					'callback' => function () use ( $changelog ) {
						echo wp_kses( $changelog, lmfwc_allowed_html() );
					},
				);
			}
		}

		return $tabs;
	}


/**
* The product meta
*/
	public function productMeta() {
		global $post;
		if ( empty( $post->ID ) ) {
			return;
		}

		$application = ApplicationResourceRepository::instance()->findByProduct( $post->ID );
		if ( empty( $application ) ) {
			return;
		}

		$release = $application->getStableRelease();
		if ( empty( $release ) ) {
			return;
		}

		$meta = array(
		__( 'Version', 'license-manager-for-woocommerce' ) => $release->getVersion(),
		__( 'Updated', 'license-manager-for-woocommerce' ) => $release->getCreatedAtFormatted(),
		__( 'WP Version required', 'license-manager-for-woocommerce' ) => $release->getMeta( 'requires_wp' ),
		__( 'PHP Version required', 'license-manager-for-woocommerce' ) => $release->getMeta( 'requires_php' ),
		);
		/**
		* Filter lmfwc_product_meta
		* 
		* @since 1.0
		**/
		$meta = apply_filters( 'lmfwc_product_meta', $meta, $application->getId(), $post->ID );

		if ( empty( $meta ) ) {
			return;
		}

		echo '<ul class="lmfwc-application-meta">';
		foreach ( $meta as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			printf( '<li><span>%s:</span> %s</li>', esc_attr($key), esc_attr( $value ) );
		}
		echo '</ul>';
	}


/**
* Adds a product data tab for simple WooCommerce products.
*
* @param array $tabs
*
* @return mixed
*/
	public function simpleProductLicenseManagerTab( $tabs ) {
		$tabs[self::ADMIN_TAB_NAME] = array(
		'label'    => __('License Manager', 'license-manager-for-woocommerce'),
		'target'   => self::ADMIN_TAB_TARGET,
		'class'    => array( 'show_if_simple', 'show_if_variable' ),
		'priority' => 21,
		);

		return $tabs;
	}


	/**
	* Displays the new fields inside the new product data tab.
	*/
	public function simpleProductLicenseManagerPanel() {
		global $post;
		$product = wc_get_product($post->ID);
		$generators         = GeneratorResourceRepository::instance()->findAll();
		$applications       = ApplicationResourceRepository::instance()->findAll();
		$licensed           =  $product->get_meta('lmfwc_licensed_product', true);
		$deliveredQuantity  =  $product->get_meta('lmfwc_licensed_product_delivered_quantity', true);
		$generatorId        =  $product->get_meta('lmfwc_licensed_product_assigned_generator', true);
		$useGenerator       =  $product->get_meta('lmfwc_licensed_product_use_generator', true);
		$useApplication     =  $product->get_meta('lmfwc_application_id', true);
		$useStock           =  $product->get_meta('lmfwc_licensed_product_use_stock', true);
		$productVersion     =  $product->get_meta('lmfwc_licensed_product_version', true);
		$productTested      =  $product->get_meta('lmfwc_licensed_product_tested' , true);
		$productRequires    =  $product->get_meta('lmfwc_licensed_product_requires' , true);
		$productRequiresPhp =  $product->get_meta('lmfwc_licensed_product_requires_php', true);
		$productChangelog   =  $product->get_meta('lmfwc_licensed_product_changelog' , true);
		$generatorOptions  = array( '' => __('Please select a generator', 'license-manager-for-woocommerce') );
		$applicationOptions  = array( '' => __('None (disabled)', 'license-manager-for-woocommerce') );
		if ($generators) {
			foreach ($generators as $generator) {
				$generatorOptions[$generator->getId()] = sprintf(
				'(#%d) %s',
				$generator->getId(),
				$generator->getName()
				);
			}
		}
		if ($applications) {
			foreach ($applications as $application) {
				$applicationOptions[$application->getId()] = sprintf(
				'(#%d) %s',
				$application->getId(),
				$application->getName()
				);
			}
		}
		printf(
		'<div id="%s" class="panel woocommerce_options_panel"><div class="options_group">',
		esc_attr( self::ADMIN_TAB_TARGET) 
		);
		echo '<input type="hidden" name="lmfwc_edit_flag" value="true" />';

		woocommerce_wp_select(
		array(
			'id'      => 'lmfwc_application_id',
			'label'   => __('Select Application', 'license-manager-for-woocommerce'),
			'options' => $applicationOptions,
			'value'   => $useApplication,
		)
		);
		if ( $product->is_type( array( 'simple', 'subscription' ) ) ) {

			// Checkbox "lmfwc_licensed_product"
			woocommerce_wp_checkbox(
			array(
				'id'          => 'lmfwc_licensed_product',
				'label'       => __('Sell license keys', 'license-manager-for-woocommerce'),
				'description' => __('Sell license keys for this product', 'license-manager-for-woocommerce'),
				'value'       => $licensed,
				'cbvalue'     => 1,
				'desc_tip'    => false,
			)
			);

				// Number "lmfwc_licensed_product_deliver_amount"
			woocommerce_wp_text_input(
				array(
				'id'                => 'lmfwc_licensed_product_delivered_quantity',
				'label'             => __('Delivered quantity', 'license-manager-for-woocommerce'),
				'value'             => $deliveredQuantity ? $deliveredQuantity : 1,
				'description'       => __('Defines the amount of license keys to be delivered upon purchase.', 'license-manager-for-woocommerce'),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '1',
				),
			)
			);

			echo '</div><div class="options_group">';

				// Checkbox "lmfwc_licensed_product_use_generator"
			woocommerce_wp_checkbox(
				array(
				'id'          => 'lmfwc_licensed_product_use_generator',
				'label'       => __('Generate license keys', 'license-manager-for-woocommerce'),
				'description' => __('Automatically generate license keys with each sold product', 'license-manager-for-woocommerce'),
				'value'       => $useGenerator,
				'cbvalue'     => 1,
				'desc_tip'    => false,
			)
			);

				// Dropdown "lmfwc_licensed_product_assigned_generator"
			woocommerce_wp_select(
				array(
				'id'      => 'lmfwc_licensed_product_assigned_generator',
				'label'   => __('Assign generator', 'license-manager-for-woocommerce'),
				'options' => $generatorOptions,
				'value'   => $generatorId,
			)
			);

			echo '</div><div class="options_group">';

				// Checkbox "lmfwc_licensed_product_use_stock"
			woocommerce_wp_checkbox(
				array(
				'id'          => 'lmfwc_licensed_product_use_stock',
				'label'       => __('Sell from stock', 'license-manager-for-woocommerce'),
				'description' => __('Sell license keys from the available stock.', 'license-manager-for-woocommerce'),
				'value'       => $useStock,
				'cbvalue'     => 1,
				'desc_tip'    => false,
			)
			);

			printf(
				'<p class="form-field"><label>%s</label><span class="description">%d %s</span></p>',
				esc_html__('Available', 'license-manager-for-woocommerce'),
				esc_attr( LicenseResourceRepository::instance()->countBy(
				array(
					'product_id' => esc_attr( $post->ID ),
					'status' => esc_attr( LicenseStatus::ACTIVE ),
				)
			) ),
			esc_html__('License key(s) in stock and available for sale', 'license-manager-for-woocommerce')
			);  


				/**
				* Action 
				* 
				* @since 1.0.0
				**/
				do_action( 'lmfwc_simple_product_data_panel', $post );
			if ( Settings::get( 'lmfwc_product_downloads', Settings::SECTION_GENERAL ) ) {
				echo '</div><div class="options_group">';

				woocommerce_wp_text_input(
					array(
						'id'          => 'lmfwc_licensed_product_version',
						'label'       => esc_html__( 'Product version', 'license-manager-for-woocommerce' ),
						'description' => esc_html__( 'Defines current version of the product.', 'license-manager-for-woocommerce' ),
						'value'       => esc_html__( $productVersion ),
						'desc_tip'    => true,
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => 'lmfwc_licensed_product_tested',
						'label'       => esc_html__( 'Product tested', 'license-manager-for-woocommerce' ),
						'description' => esc_html__( 'The version of WordPress where the product has been tested up to.', 'license-manager-for-woocommerce' ),
						'value'       => $productTested,
						'desc_tip'    => true,
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => 'lmfwc_licensed_product_requires',
						'label'       => esc_html__( 'Product requires', 'license-manager-for-woocommerce' ),
						'description' => esc_html__( 'The version of WordPress that the product requires.', 'license-manager-for-woocommerce' ),
						'value'       => $productRequires,
						'desc_tip'    => true,
					)
				);

				woocommerce_wp_text_input(
					array(
						'id'          => 'lmfwc_licensed_product_requires_php',
						'label'       => esc_html__( 'Product requires PHP', 'license-manager-for-woocommerce' ),
						'description' => esc_html__( 'The version of PHP that the product requires.', 'license-manager-for-woocommerce' ),
						'value'       => $productRequiresPhp,
						'desc_tip'    => true,
					)
				);
				?>
					<div class="form-field lmfwc_licensed_product_changelog">
						<label><?php esc_html_e( 'Product changelog', 'license-manager-for-woocommerce' ); ?></label>
					<?php wp_editor( $productChangelog, 'lmfwc_licensed_product_changelog', array( 'media_buttons' => false ) ); ?>
					</div>
					<?php
			}


		}
			echo '</div></div>';
	}

	/**
	 * Adds an icon to the new data tab.
	 *
	 * @see https://docs.woocommerce.com/document/utilising-the-woocommerce-icon-font-in-your-extensions/
	 * @see https://developer.wordpress.org/resource/dashicons/
	 */
	public function styleInventoryManagement() {
		printf(
			'<style>#woocommerce-product-data ul.wc-tabs li.%s_options a:before { font-family: %s; content: "%s"; }</style>',
			esc_attr( self::ADMIN_TAB_NAME ),
			'dashicons',
			'\f160'
		);
	}

	/**
	 * Hook which triggers when the WooCommerce Product is being saved or updated.
	 *
	 * @param int $postId
	 */
	public function savePost( $postId ) {
		$data = $_REQUEST;
		// This is not a product.
		if (!array_key_exists('post_type', $data) || 'product' != $data['post_type'] || !array_key_exists('lmfwc_edit_flag', $data) ) {
			return;
		}

		$product = wc_get_product( $postId );
		if ( ! is_object($product) ) {
			return;
			// Update licensed product flag, according to checkbox.
		}
		if (array_key_exists('lmfwc_licensed_product', $data)) {
			$product->update_meta_data('lmfwc_licensed_product', 1);
		} else {
			$product->update_meta_data('lmfwc_licensed_product', 0);
		}

		// Update delivered quantity, according to field.
	$deliveredQuantity = absint(@$data['lmfwc_licensed_product_delivered_quantity']);

	$product->update_meta_data('lmfwc_licensed_product_delivered_quantity', $deliveredQuantity ? $deliveredQuantity : 1);

		// Update the use stock flag, according to checkbox.
		if (array_key_exists('lmfwc_licensed_product_use_stock', $data)) {
			$product->update_meta_data('lmfwc_licensed_product_use_stock', 1);
		} else {
			$product->update_meta_data('lmfwc_licensed_product_use_stock', 0);
		}

		// Update the assigned generator id, according to select field.
	$product->update_meta_data('lmfwc_licensed_product_assigned_generator', intval( @$data['lmfwc_licensed_product_assigned_generator'] ));

		// Update the use generator flag, according to checkbox.
		if (array_key_exists('lmfwc_licensed_product_use_generator', $data)) {
			// You must select a generator if you wish to assign it to the product.
			if (!$data['lmfwc_licensed_product_assigned_generator']) {
				$error = new WP_Error(2, __('Assign a generator if you wish to sell automatically generated licenses for this product.', 'license-manager-for-woocommerce'));

				set_transient('lmfwc_error', $error, 45);
				$product->update_meta_data('lmfwc_licensed_product_use_generator', 0);
				$product->update_meta_data('lmfwc_licensed_product_assigned_generator', 0);
			} else {
				$product->update_meta_data('lmfwc_licensed_product_use_generator', 1);
			}
		} else {
			$product->update_meta_data('lmfwc_licensed_product_use_generator', 0);
			$product->update_meta_data('lmfwc_licensed_product_assigned_generator', 0);
		}

		if ( isset( $data['lmfwc_application_id'] ) ) {
			$application_id = is_numeric( $data['lmfwc_application_id'] ) ? (int) $data['lmfwc_application_id'] : '0';
			$product->update_meta_data('lmfwc_application_id', (int) $application_id );
		} else {
			$product->delete_meta_data( 'lmfwc_application_id' );
		}

		 // Update the product version according to the field.
	$productVersion = sanitize_text_field( wp_unslash( @$data['lmfwc_licensed_product_version'] ) );

	$product->update_meta_data('lmfwc_licensed_product_version', $productVersion );


		// Update the product WordPress version tested up to according to the field.
	$productTested = sanitize_text_field( wp_unslash( @$data['lmfwc_licensed_product_tested'] ) );

	$product->update_meta_data('lmfwc_licensed_product_tested', $productTested );

		// Update the product required WordPress version according to the field.
	$productRequires = sanitize_text_field( wp_unslash( @$data['lmfwc_licensed_product_requires'] ) );

	$product->update_meta_data('lmfwc_licensed_product_requires', $productRequires );

		// Update the product required PHP version according to the field.
	$productRequiresPhp = sanitize_text_field( wp_unslash( @$data['lmfwc_licensed_product_requires_php'] ) );

	$product->update_meta_data('lmfwc_licensed_product_requires_php', $productRequiresPhp );

		// Update the product changelog according to the field.
	$productChangelog = wp_unslash( @$data['lmfwc_licensed_product_changelog'] );

	$product->update_meta_data('lmfwc_licensed_product_changelog', $productChangelog );
	$product->save();

		 /**
		 * Action 
		 * 
		 * @since 1.0.0
		 **/
		 do_action( 'lmfwc_simple_product_save', $postId );
	}

	/**
	 * Adds the new product data fields to variable WooCommerce Products.
	 *
	 * @param int     $loop
	 * @param array   $variationData
	 * @param WP_Post $variation
	 */
	public function variableProductLicenseManagerFields( $loop, $variationData, $variation ) {
		/**
		 *  LicenseResourceRepository find license
		 * 
		 * @var LicenseResourceRepository $license 
		**/
		$generators         = GeneratorResourceRepository::instance()->findAll();
		$applications       = ApplicationResourceRepository::instance()->findAll();
		$productId          = $variation->ID;
		$product = wc_get_product($productId);
		$licensed           = $product->get_meta( 'lmfwc_licensed_product', true);
		$deliveredQuantity  = $product->get_meta( 'lmfwc_licensed_product_delivered_quantity', true);
		$generatorId        = $product->get_meta( 'lmfwc_licensed_product_assigned_generator', true);
		$useGenerator       = $product->get_meta( 'lmfwc_licensed_product_use_generator', true);
		$useStock           = $product->get_meta( 'lmfwc_licensed_product_use_stock', true);
		$generatorOptions  = array( '' => __('Please select a generator', 'license-manager-for-woocommerce') );
		$applicationOptions  = array( '' => __('None (disabled)', 'license-manager-for-woocommerce') );
		$generators = is_array($generators) ? $generators : [];
		foreach ($generators as $generator) {
			$generatorOptions[$generator->getId()] = sprintf(
				'(#%d) %s',
				$generator->getId(),
				$generator->getName()
			);
		}

		if ($applications) {
			foreach ($applications as $application) {
				$applicationOptions[$application->getId()] = sprintf(
					'(#%d) %s',
					$application->getId(),
					$application->getName()
				);
			}
		}


		echo '<p class="form-row form-row-full lmfwc-form-row-section">';

		printf('<strong>%s</strong></p>', esc_html__('License Manager for WooCommerce', 'license-manager-for-woocommerce'));

		echo '<input type="hidden" name="lmfwc_edit_flag" value="true" />';

		?>
		<p class="form-row form-row-full options">
			<label class="tips" data-tip="<?php esc_attr_e( 'Sell license keys for this variation', 'license-manager-for-woocommerce' ); ?>">
				<?php esc_html_e( 'Sell license key(s)', 'license-manager-for-woocommerce' ); ?>
				<input type="checkbox" class="checkbox lmfwc_licensed_product" name="lmfwc_licensed_product[<?php echo esc_attr( $loop ); ?>]" <?php checked( 1, $licensed, true ); ?> />
			</label>
		</p>
		<?php

		// Number "lmfwc_licensed_product_deliver_amount"
		woocommerce_wp_text_input(
			array(
				'id'                => 'lmfwc_licensed_product_delivered_quantity',
				'name'              => sprintf('lmfwc_licensed_product_delivered_quantity[%d]', $loop),
				'label'             => __('Delivered quantity', 'license-manager-for-woocommerce'),
				'value'             => $deliveredQuantity ? $deliveredQuantity : 1,
				'description'       => __('Defines the amount of license keys to be delivered upon purchase.', 'license-manager-for-woocommerce'),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min'  => '1',
				),
				'desc_tip' => true,
				'wrapper_class' => 'form-row form-row-full',
			)
		);

		?>
		<p class="form-row form-row-full options">
			<label class="tips" data-tip="<?php esc_attr_e( 'Automatically generate license keys with each sold variation', 'license-manager-for-woocommerce' ); ?>">
				<?php esc_html_e( 'Generate license key(s)', 'license-manager-for-woocommerce' ); ?>
				<input type="checkbox" class="checkbox lmfwc_licensed_product_use_generator" name="lmfwc_licensed_product_use_generator[<?php echo esc_attr( $loop ); ?>]" <?php checked( 1, $useGenerator, true ); ?> />
			</label>
		</p>
		<?php
		// Dropdown "lmfwc_licensed_product_assigned_generator"
		woocommerce_wp_select(
			array(
				'id'      => 'lmfwc_licensed_product_assigned_generator',
				'name'    => sprintf('lmfwc_licensed_product_assigned_generator[%d]', $loop),
				'label'   => __('Assign generator', 'license-manager-for-woocommerce'),
				'options' => $generatorOptions,
				'value'   => $generatorId,
				'wrapper_class' => 'form-row form-row-full',
			)
		);

		?>
		<p class="form-row form-row-full options">
			<label class="tips" data-tip="<?php esc_attr_e( 'Sell license keys from the available stock.', 'license-manager-for-woocommerce' ); ?>">
				<?php esc_html_e( 'Sell from stock', 'license-manager-for-woocommerce' ); ?>
				<input type="checkbox" class="checkbox lmfwc_licensed_product_use_stock" name="lmfwc_licensed_product_use_stock[<?php echo esc_attr( $loop ); ?>]" <?php checked( 1, $useStock, true ); ?> />
			</label>
		</p>
		<?php

		printf(
			'<p class="form-field form-row"><label>%s <span class="description">%d %s</span></label></p>',
			esc_html__('Available', 'license-manager-for-woocommerce'),
			esc_attr( LicenseResourceRepository::instance()->countBy(
				array(
					'product_id' => esc_attr( $productId ),
					'status' => esc_attr( LicenseStatus::ACTIVE ),
				) )
			),
			esc_html__('License key(s) in stock and available for sale.', 'license-manager-for-woocommerce')
		);
		/**
		 * Action 
		 * 
		 * @since 1.0.0
		 **/
		do_action( 'lmfwc_variable_product_data_panel', $loop, $variationData, $variation );
	}

	/**
	 * Saves the data from the product variation fields.
	 *
	 * @param int $variationId
	 * @param int $i
	 */
	public function variableProductLicenseManagerSaveAction( $variationId, $i ) {
		$data = $_REQUEST;
		$variation = wc_get_product( $variationId );
		// Update licensed product flag, according to checkbox.
		if (array_key_exists('lmfwc_licensed_product', $data)
			&& array_key_exists($i, $data['lmfwc_licensed_product'])
		) {
			$variation->update_meta_data('lmfwc_licensed_product', 1);
		} else {
			$variation->update_meta_data( 'lmfwc_licensed_product', 0);
		}

		// Update delivered quantity, according to field.
	$deliveredQuantity = absint($data['lmfwc_licensed_product_delivered_quantity'][$i]);

	$variation->update_meta_data(
		'lmfwc_licensed_product_delivered_quantity',
		$deliveredQuantity ? $deliveredQuantity : 1
	);

		// Update the use stock flag, according to checkbox.
		if (array_key_exists('lmfwc_licensed_product_use_stock', $data)
		&& array_key_exists($i, $data['lmfwc_licensed_product_use_stock'])
	) {
			$variation->update_meta_data( 'lmfwc_licensed_product_use_stock', 1);
		} else {
			$variation->update_meta_data( 'lmfwc_licensed_product_use_stock', 0);
		}

		// Update the assigned generator id, according to select field.
$variation->update_meta_data(
	'lmfwc_licensed_product_assigned_generator',
	intval($data['lmfwc_licensed_product_assigned_generator'][$i])
);

		// Update the use generator flag, according to checkbox.
		if (array_key_exists('lmfwc_licensed_product_use_generator', $data)
	&& array_key_exists($i, $data['lmfwc_licensed_product_use_generator'])
) {
			// You must select a generator if you wish to assign it to the product.
			if (!$data['lmfwc_licensed_product_assigned_generator'][$i]) {
				$error = new WP_Error(2, __('Assign a generator if you wish to sell automatically generated licenses for this product.', 'license-manager-for-woocommerce'));

				set_transient('lmfwc_error', $error, 45);
				$variation->update_meta_data( 'lmfwc_licensed_product_use_generator', 0);
				$variation->update_meta_data( 'lmfwc_licensed_product_assigned_generator', 0);
			} else {
				$variation->update_meta_data( 'lmfwc_licensed_product_use_generator', 1);
			}
		} else {
			$variation->update_meta_data('lmfwc_licensed_product_use_generator', 0);
			$variation->update_meta_data( 'lmfwc_licensed_product_assigned_generator', 0);
		}

		if ( isset( $data['lmfwc_application_id'] ) ) {
			$application_id = is_numeric( $data['lmfwc_application_id'] ) ? (int) $data['lmfwc_application_id'] : '0';
			$variation->update_meta_data( 'lmfwc_application_id', (int) $application_id );
		} else {
			$variation->delete_meta_data('lmfwc_application_id' );
		}
$variation->save();
		/**
		 * Action 
		 * 
		 * @since 1.0.0
		 **/
		do_action( 'lmfwc_variable_product_save', $variationId, $i );
	}
}