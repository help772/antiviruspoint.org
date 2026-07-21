<?php

namespace LicenseManagerForWooCommerce\Settings;

defined('ABSPATH') || exit;

class Tools {

	/**
	 * Settings array
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Tools constructor.
	 */
	public function __construct() {
		$this->settings = get_option('lmfwc_settings_tools', array());

		/**
		 * Register Tools Section
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_setting/#parameters
		 */
		$args = array(
			'sanitize_callback' => array( $this, 'sanitize' ),
		);

		// Register the initial settings group.
		register_setting('lmfwc_settings_group_tools', 'lmfwc_settings_tools', $args);

		
		$this->initSectionExport();
	}

	/**
	 * Sanitize settings array
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function sanitize( $settings ) {
		if (  null === $settings  ) {
			return array();
		}

		return $settings;
	}

	/**
	 * Initializes the "lmfwc_license_keys" section.
	 *
	 * @return void
	 */
	private function initSectionExport() {
		// Add the settings sections.
		add_settings_section(
			'export_section',
			__('License key export', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_export'
		);

		// lmfwc_export section fields.
		add_settings_field(
			'lmfwc_csv_export_columns',
			__('CSV Export Columns', 'license-manager-for-woocommerce'),
			array( $this, 'fieldCsvExportColumns' ),
			'lmfwc_export',
			'export_section'
		);
	}
	

	public function fieldCsvExportColumns() {
		$field   = 'lmfwc_csv_export_columns';
		$value   = array();
		$columns = array(
			array(
				'slug' => 'id',
				'name' => __('ID', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'order_id',
				'name' => __('Order ID', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'product_id',
				'name' => __('Product ID', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'user_id',
				'name' => __('User ID', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'license_key',
				'name' => __('License key', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'expires_at',
				'name' => __('Expires at', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'valid_for',
				'name' => __('Valid for', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'status',
				'name' => __('Status', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'times_activated',
				'name' => __('Times activated', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'times_activated_max',
				'name' => __('Times activated (max.)', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'created_at',
				'name' => __('Created at', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'created_by',
				'name' => __('Created by', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'updated_at',
				'name' => __('Updated at', 'license-manager-for-woocommerce'),
			),
			array(
				'slug' => 'updated_by',
				'name' => __('Updated by', 'license-manager-for-woocommerce'),
			),
		);

		if (array_key_exists($field, $this->settings)) {
			$value = $this->settings[$field];
		}
		?>

		<fieldset>

			<?php foreach ($columns as $column) : ?>
				<?php
				$checked = false;

				if (array_key_exists($column['slug'], $value) && '1' === $value[$column['slug']]) {
					$checked = true;
				}
				?>

				<label for="<?php echo esc_attr( $field ) . '-' . esc_attr( $column['slug'] ); ?>">
					<input id="<?php echo esc_attr( $field ) . '-' . esc_attr( $column['slug'] ); ?>" type="checkbox" name="lmfwc_settings_tools[<?php echo esc_attr( $field ); ?>][<?php echo esc_attr( $column['slug'] ); ?>]" value="1" <?php echo checked(true, $checked, false); ?>>
					<span><?php echo esc_attr( $column['name'] ); ?></span>
				</label>
				<br>

			<?php endforeach; ?>

			<p class="description" style="margin-top: 1em;"><?php esc_html_e('The selected columns will appear on the CSV export for license keys.', 'license-manager-for-woocommerce'); ?></p>
		</fieldset>


		<?php
	}
}
