<?php

namespace LicenseManagerForWooCommerce\Settings;

defined('ABSPATH') || exit;

class WooSettings {

	/**
	 * Settings array
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * General constructor.
	 */
	public function __construct() {
		$this->settings = get_option('lmfwc_settings_woocommerce', array());

		/**
		 * Register WooCommerce Settings
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_setting/#parameters
		 */
		$args = array(
			'sanitize_callback' => array( $this, 'sanitize' ),
		);

		// Register the initial settings group.

		// Initialize the individual sections
		$this->initSectionsLicenseDelivery();
		$this->initSectionBranding();
		$this->initSectionMyAccount();


		register_setting('lmfwc_settings_group_woocommerce', 'lmfwc_settings_woocommerce', $args) ;
	}

	/**
	 * Sanitize settings array 
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function sanitize( $settings ) {
		$data = $_REQUEST;
		if (isset($data['lmfwc_stock_synchronize'])) {
			// Permission check
			if (!current_user_can('manage_options')) {
				return $settings;
			}
			/**
			* Filter lmfwc_stock_synchronize
			* 
			* @since 1.0
			**/
			$productsSynchronized = apply_filters('lmfwc_stock_synchronize', null);

			if ($productsSynchronized > 0) {
				add_settings_error(
					'lmfwc_settings_group_woocommerce',
					'lmfwc_stock_update',
					sprintf(
						/* translators: %d is the product number */
						esc_html__('Successfully updated the stock of %d WooCommerce products.', 'license-manager-for-woocommerce'), $productsSynchronized),
					'success'
				);
			} else {
				add_settings_error(
					'lmfwc_settings_group_woocommerce',
					'lmfwc_stock_update',
					esc_html__('The stock of all WooCommerce products is already synchronized.', 'license-manager-for-woocommerce'),
					'success'
				);
			}
		}


		if ( null === $settings ) {
			return array();
		}
		
		if ( isset( $settings['lmfwc_enable_my_account_endpoint'] ) ) {
			flush_rewrite_rules( true );
		}

		return $settings;
	}

	/**
	 * Initializes the "lmfwc_branding" section.
	 *
	 * @return void
	 */
	private function initSectionBranding() {

		// Add the Branding sections.
		add_settings_section(
			'branding_section',
			esc_html__('Branding', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_branding'
		);

		// lmfwc_logo_field.
		add_settings_field(
			'lmfwc_company_logo',
			esc_html__('Company Logo', 'license-manager-for-woocommerce'),
			array( $this, 'fieldImageUpload' ),
			'lmfwc_branding',
			'branding_section'
		);
	}
	 /**
	 * Render the image upload field
	 *
	 * @return void
	 */
	public function fieldImageUpload() {

		$field = 'lmfwc_company_logo';
		$placeholder = LMFWC_PLUGIN_URL . 'assets/img/logo.jpg';
		$value = isset($this->settings[$field]) ? $this->settings[$field] : ''; 
		$current_src = !empty($value) ? wp_get_attachment_image_src($value, 'large') : '';
		$current_src = !empty($current_src) && is_array($current_src) ? $current_src[0] : $placeholder;
		?>
		<fieldset>
	<div class="lmfwc-field-upload" data-show-attachment-preview="1">
		<img class="lmfwc-field-placeholder" data-src="<?php echo esc_url($placeholder); ?>" src="<?php echo esc_url($current_src); ?>" alt="File" />
		<div class="lmfwc-field-submit">
			<input id="<?php echo esc_attr($field); ?>" type="hidden" name="lmfwc_settings_woocommerce[<?php echo esc_attr($field); ?>]" value="<?php echo esc_attr($value); ?>" />
			<button type="submit" class="lmfwc-field-upload-button button"><?php echo esc_html__('Upload', 'license-manager-for-woocommerce'); ?></button>
			<button type="submit" class="lmfwc-field-remove-button button">&times;</button>
		</div>
	</div>
</fieldset>

<?php 
	}


	/**
	 * Initializes the "lmfwc_my_account" section.
	 *
	 * @return void
	 */
	private function initSectionMyAccount() {

		// Add the settings sections.
		add_settings_section(
			'my_account_section',
			esc_html__('My account', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_my_account'
		);

		// lmfwc_my_account section fields.
		add_settings_field(
			'lmfwc_enable_my_account_endpoint',
			esc_html__('Enable "License keys"', 'license-manager-for-woocommerce'),
			array( $this, 'fieldEnableMyAccountEndpoint' ),
			'lmfwc_my_account',
			'my_account_section'
		);

		add_settings_field(
			'lmfwc_allow_users_to_activate',
			esc_html__('User activation', 'license-manager-for-woocommerce'),
			array( $this, 'fieldAllowUsersToActivate' ),
			'lmfwc_my_account',
			'my_account_section'
		);

		add_settings_field(
			'lmfwc_allow_users_to_deactivate',
			esc_html__('User deactivation', 'license-manager-for-woocommerce'),
			array( $this, 'fieldAllowUsersToDeactivate' ),
			'lmfwc_my_account',
			'my_account_section'
		);
		add_settings_field(
			'lmfwc_download_certificates',
			esc_html__('Enable Certificates', 'license-manager-for-woocommerce'),
			array( $this, 'fieldEnableLicenseCertificates' ),
			'lmfwc_my_account',
			'my_account_section'
		);
	}


	private function initSectionsLicenseDelivery() {
		add_settings_section(
			'license_key_delivery_section',
			esc_html__('License key delivery', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_license_key_delivery'
		);

		add_settings_field(
			'lmfwc_auto_delivery',
			esc_html__('Automatic delivery', 'license-manager-for-woocommerce'),
			array( $this, 'fieldAutoDelivery' ),
			'lmfwc_license_key_delivery',
			'license_key_delivery_section'
		);

		add_settings_field(
			'lmfwc_terminated_status',
			esc_html__('Terminated Status', 'license-manager-for-woocommerce'),
			array( $this, 'fieldTerminatedStatus' ),
			'lmfwc_license_key_delivery',
			'license_key_delivery_section'
		);

		add_settings_field(
			'lmfwc_license_key_delivery_options',
			esc_html__('Define license key delivery', 'license-manager-for-woocommerce'),
			array( $this, 'fieldLicenseKeyDeliveryOptions' ),
			'lmfwc_license_key_delivery',
			'license_key_delivery_section'
		);

		add_settings_field(
			'lmfwc_enable_stock_manager',
			esc_html__('Stock management', 'license-manager-for-woocommerce'),
			array( $this, 'fieldEnableStockManager' ),
			'lmfwc_license_key_delivery',
			'license_key_delivery_section'
		);
	}


	public function fieldEnableLicenseCertificates() {

		$field = 'lmfwc_download_certificates';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;

		?>
		<fieldset>

			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php echo esc_html__('Allow users to download license certificates', 'license-manager-for-woocommerce'); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__('Use this option if you want to allow customers to download license certificates from the single license page.', 'license-manager-for-woocommerce'); ?>
			</p>

		</fieldset>
		<?php
	}


	public function fieldAutoDelivery() {
		$field = 'lmfwc_auto_delivery';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;
		?>
		<fieldset>

			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php echo esc_html__('Automatically send license keys when an order is set to \'Complete\'.', 'license-manager-for-woocommerce'); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__('If this setting is off, you must manually send out all license keys for completed orders.', 'license-manager-for-woocommerce'); ?>
			</p>

		</fieldset>
		<?php
	}

	public function fieldTerminatedStatus() {
		$field = 'lmfwc_terminated_status';

		$terminated_statuses = array(
			'wc-cancelled' => __('Cancelled', 'license-manager-for-woocommerce'),
			'wc-refunded' => __('Refunded', 'license-manager-for-woocommerce'),
			'wc-failed' => __('Failed', 'license-manager-for-woocommerce')
		);
		$terminated_statuses = apply_filters('lmfwc_terminated_status', $terminated_statuses);
		?>
		<fieldset>
			<?php
			foreach( $terminated_statuses as $key => $status ) : 
				$value = isset($this->settings[$field][$key]) ? true : false;
			?>
				<label for="<?php echo esc_attr( $key ); ?>">
					<input id="<?php echo esc_attr( $key ); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>][<?php echo esc_attr($key); ?>]" value="1" <?php checked(true, $value); ?>/>
					<span><?php echo esc_attr($status); ?></span>
				</label><br>
			<?php endforeach; ?>

		</fieldset>
		<?php
	}
	
	/**
	 * Callback for the "lmfwc_enable_my_account_endpoint" field.
	 *
	 * @return void
	 */
	public function fieldEnableMyAccountEndpoint() {
		$field = 'lmfwc_enable_my_account_endpoint';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;

		?>
		<fieldset>

			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php echo esc_html__('Display the \'License keys\' section inside WooCommerce\'s \'My Account\'.', 'license-manager-for-woocommerce'); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__('You might need to save your permalinks after enabling this option.', 'license-manager-for-woocommerce'); ?>
			</p>

		</fieldset>

		<?php
	}

	/**
	 * Callback for the "lmfwc_allow_users_to_activate" field.
	 */
	public function fieldAllowUsersToActivate() {
		$field = 'lmfwc_allow_users_to_activate';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;
		?>
		<fieldset>

			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php echo esc_html__('Allow users to activate their license keys.', 'license-manager-for-woocommerce'); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__('The option will be visible from the \'License keys\' section inside WooCommerce\'s \'My Account\'.', 'license-manager-for-woocommerce'); ?>
			</p>

		</fieldset>

		<?php
	}

	 /**
	 * Callback for the "lmfwc_allow_users_to_deactivate" field.
	 */
	public function fieldAllowUsersToDeactivate() {
		$field = 'lmfwc_allow_users_to_deactivate';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;

		?>
		 <fieldset>
			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked( true, $value, false ); ?> />
				<span><?php echo esc_html__( 'Allow users to deactivate their license keys.', 'license-manager-for-woocommerce' ); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__( 'The option will be visible from the \'License keys\' section inside WooCommerce\'s \'My Account\'.', 'license-manager-for-woocommerce' ); ?>
			</p>
		</fieldset>
		<?php
	}


	public function fieldLicenseKeyDeliveryOptions( $foo ) {
		$field = 'lmfwc_license_key_delivery_options';
		$html  = '';
		?>
		 <table class="wp-list-table widefat fixed striped posts">

			 <thead>
				 <tr>
					 <td><strong><?php echo esc_html__('Order status', 'license-manager-for-woocommerce'); ?></strong></td>
					 <td><strong><?php echo esc_html__('Send', 'license-manager-for-woocommerce'); ?></strong></td>
				 </tr>
			 </thead>

			 <tbody>

			   <?php foreach (wc_get_order_statuses() as $slug => $name) : ?>
					<?php
					$send = false;

					if (array_key_exists($field, $this->settings) && array_key_exists($slug, $this->settings[$field])) {
						if (array_key_exists('send', $this->settings[$field][$slug]) && $this->settings[$field][$slug]) {
							$send = true;
						}
					}
					?>

					 <tr>
						 <td><?php echo esc_attr( $name ); ?></td>

						 <td>
							 <input type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr( $field ); ?>][<?php echo esc_attr( $slug ); ?>][send]" value="1" <?php echo $send ? 'checked="checked"' : ''; ?>>
						 </td>

					 </tr>

				<?php endforeach; ?>

			 </tbody>

		 </table>
		<?php 
	}

	 /**
	 * Callback for the "lmfwc_enable_stock_manager" field.
	 *
	 * @return void
	 */
	public function fieldEnableStockManager() {
		$field = 'lmfwc_enable_stock_manager';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;
		?>
	<fieldset style="margin-bottom: 0;">
	<label for="<?php echo esc_attr($field); ?>">
		<input id="<?php echo esc_attr($field); ?>" type="checkbox" name="lmfwc_settings_woocommerce[<?php echo esc_attr($field); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
		<span><?php echo esc_html__('Enable automatic stock management for WooCommerce products.', 'license-manager-for-woocommerce'); ?></span>
	</label>

	<p class="description">
		<?php echo esc_html__('To use this feature, you also need to enable the following settings at a product level:', 'license-manager-for-woocommerce'); ?><br/>
		1. <?php echo esc_html__('Inventory &rarr; Manage stock?', 'license-manager-for-woocommerce'); ?><br/>
		2. <?php echo esc_html__('License Manager &rarr; Sell license keys', 'license-manager-for-woocommerce'); ?><br/>
		3. <?php echo esc_html__('License Manager &rarr; Sell from stock', 'license-manager-for-woocommerce'); ?>
	</p>
</fieldset>

<fieldset style="margin-top: 1em;">
	<button class="button button-secondary"
			type="submit"
			name="lmfwc_stock_synchronize"
			value="1"><?php echo esc_html__('Synchronize', 'license-manager-for-woocommerce'); ?></button>

	<p class="description" style="margin-top: 1em;">
		<?php echo esc_html__('The "Synchronize" button can be used to manually synchronize the product stock.', 'license-manager-for-woocommerce'); ?>
	</p>
</fieldset>


		<?php
	}
}
