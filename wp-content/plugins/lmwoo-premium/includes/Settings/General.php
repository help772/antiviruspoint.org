<?php

namespace LicenseManagerForWooCommerce\Settings;

defined('ABSPATH') || exit;

class General {

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
		$this->settings = get_option('lmfwc_settings_general', array());

		/**
		 * Register general section
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_setting/#parameters
		 */
		$args = array(
			'sanitize_callback' => array( $this, 'sanitize' ),
		);


		// Register the initial settings group.
		register_setting('lmfwc_settings_group_general', 'lmfwc_settings_general', $args) ;

		// Initialize the individual sections
		$this->initSectionLicenseKeys();
		$this->initSectionGracePeriod();
		$this->initSectionAPI();
		$this->initSectionLogs(); 
	}

	/**
	 * Sanitizes the settings input.
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function sanitize( $settings ) {

		return $settings;
	}

	/**
	 * Initializes the "lmfwc_license_keys" section.
	 *
	 * @return void
	 */
	private function initSectionLicenseKeys() {
		// Add the settings sections.
		add_settings_section(
			'license_keys_section',
			__('License keys', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_license_keys'
		);

		// lmfwc_security section fields.
		add_settings_field(
			'lmfwc_hide_license_keys',
			__('Obscure licenses', 'license-manager-for-woocommerce'),
			array( $this, 'fieldHideLicenseKeys' ),
			'lmfwc_license_keys',
			'license_keys_section'
		);
		add_settings_field(
			'lmfwc_allow_duplicates',
		 __('Allow duplicates', 'license-manager-for-woocommerce'),
			array( $this, 'fieldAllowDuplicates' ),
			'lmfwc_license_keys',
			'license_keys_section'
		);

		add_settings_field(
			'lmfwc_product_downloads',
			esc_html__( 'Product downloads', 'license-manager-for-woocommerce' ),
			array( $this, 'fieldProductDownloads' ),
			'lmfwc_license_keys',
			'license_keys_section'
		);

		add_settings_field(
			'lmfwc_download_expires',
			esc_html__( 'Download expires', 'license-manager-for-woocommerce' ),
			array( $this, 'fieldDownloadExpires' ),
			'lmfwc_license_keys',
			'license_keys_section'
		);
		add_settings_field(
			'lmfwc_expire_format',
			__('License expiration format', 'license-manager-for-woocommerce'),
			array( $this, 'fieldExpireFormat' ), 
			'lmfwc_license_keys',
			'license_keys_section'
		);
	}
	/**
	 * Initializes the "lmfwc_grace_period" section.
	 *
	 * @return void
	 */
	private function initSectionGracePeriod() {
		// Add the settings sections.
		add_settings_section(
			'grace_period_section',
			__('Grace Period', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_grace_period'
		);

		// lmfwc_grace_period section fields.
		add_settings_field(
			'lmfwc_grace_period_duration',
			__('Grace Period', 'license-manager-for-woocommerce'),
			array( $this, 'fieldGracePeriod' ),
			'lmfwc_grace_period',
			'grace_period_section'
		);
	}

	/**
	 * Initializes the "lmfwc_grace_period" section.
	 *
	 * @return void
	 */
	private function initSectionLogs() { 
		// Add the settings sections.
		add_settings_section(
			'api_logs_section',
			__('Traceback', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_api_logs'
		);

		// lmfwc_api_logs section fields.
		add_settings_field(
			'lmfwc_api_activity_log',
			__('API Activity Log', 'license-manager-for-woocommerce'),
			array( $this, 'fieldActivityLog' ),
			'lmfwc_api_logs',
			'api_logs_section'
		);
		add_settings_field(
			'lmfwc_api_exception_log',
			__('API Exception Log', 'license-manager-for-woocommerce'),
			array( $this, 'fieldExceptionLog' ),
			'lmfwc_api_logs',
			'api_logs_section'
		);
		add_settings_field(
			'lmfwc_api_output_logs',
			__('API Output Log', 'license-manager-for-woocommerce'),
			array( $this, 'fieldOutputLog' ),
			'lmfwc_api_logs',
			'api_logs_section'
		);
	}

	public function fieldActivityLog() {
		$field = 'lmfwc_api_activity_log';
		$value = isset($this->settings[$field]) ? $this->settings[$field] : '0';
		$activity_logs_url = add_query_arg(
			array(
				'page' => 'wc-status',
				'tab'  => 'logs',
				'view' => 'single_file',
				'file_id' => 'lmfwc-api-activity-' . gmdate('Y-m-d')
			),
			admin_url( 'admin.php' )
		);
		?>
		<label for="<?php echo esc_attr( $field ); ?>">
			<input name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" id="<?php echo esc_attr( $field ); ?>" type="checkbox" class="lmfwc-input-toggle" value="1" <?php checked(1, $value); ?>>
			<p class="description"><?php echo esc_html__( 'Enable to log api activitites inside', 'license-manager-for-woocommerce' ); ?> <code>lmfwc-api-activity-<?php echo esc_attr(gmdate('Y-m-d')); ?>.log</code></p>
			<p class="description"><a href="<?php echo esc_url( $activity_logs_url ); ?>"><?php echo esc_html__( 'View Log', 'license-manager-for-woocommerce' ); ?></a></p>
		</label>
		<?php
	}
	public function fieldExceptionLog() {
		$field = 'lmfwc_api_exception_log';
		$value = isset($this->settings[$field]) ? $this->settings[$field] : '0';
		$exceptions_logs_url = add_query_arg(
			array(
				'page' => 'wc-status',
				'tab'  => 'logs',
				'view' => 'single_file',
				'file_id' => 'lmfwc-api-exceptions-' . gmdate('Y-m-d')
			),
			admin_url( 'admin.php' )
		);
		?>
		<label for="<?php echo esc_attr( $field ); ?>">
			<input name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" id="<?php echo esc_attr( $field ); ?>" type="checkbox" class="lmfwc-input-toggle" value="1" <?php checked(1, $value); ?>>
			<p class="description"><?php echo esc_html__('Enable to log api exceptions inside', 'license-manager-for-woocommerce'); ?> <code>lmfwc-api-exceptions-<?php echo esc_attr(gmdate('Y-m-d')); ?>.log</code></p>
			<p class="description"><a href="<?php echo esc_url( $exceptions_logs_url ); ?>"><?php echo esc_html__( 'View Log', 'license-manager-for-woocommerce' ); ?></a></p>
		</label>
		<?php
	}
	public function fieldOutputLog() {
		$field = 'lmfwc_api_output_log';
		$value = isset($this->settings[$field]) ? $this->settings[$field] : '0';
		$output_logs_url = add_query_arg(
			array(
				'page' => 'wc-status',
				'tab'  => 'logs',
				'view' => 'single_file',
				'file_id' => 'lmfwc-api-output-' . gmdate('Y-m-d')
			),
			admin_url( 'admin.php' )
		);
		?>
		<label for="<?php echo esc_attr( $field ); ?>">
			<input name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" id="<?php echo esc_attr( $field ); ?>" type="checkbox" class="lmfwc-input-toggle" value="1" <?php checked(1, $value); ?>>
			<p class="description"><?php echo esc_html__('Enable to log api output inside', 'license-manager-for-woocommerce'); ?> <code>lmfwc-api-output-<?php echo esc_attr(gmdate('Y-m-d')); ?>.log</code></p>
			<p class="description"><a href="<?php echo esc_url( $output_logs_url ); ?>"><?php echo esc_html__( 'View Log', 'license-manager-for-woocommerce' ); ?></a></p>
		</label>
		<?php
	}

	public function fieldGracePeriod() {
		$field = 'lmfwc_grace_period_duration';
		$value = isset($this->settings[$field]) ? $this->settings[$field] : array();
		$interval = isset($value['interval']) ? $value['interval'] : '';
		$period = isset($value['period']) ? $value['period'] : '';
		?>
		<fieldset>
		<label for="<?php echo esc_attr( $field ); ?>">
			<input id="<?php echo esc_attr( $field ); ?>" type="number" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>][interval]" value="<?php echo esc_attr( $interval ); ?>" />
			<select name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>][period]"><option value="day" <?php selected('day', $period); ?>><?php esc_html_e( 'Day(s)', 'license-manager-for-woocommerce' ); ?></option><option value="week" <?php selected('week', $period); ?>><?php esc_html_e( 'Week(s)', 'license-manager-for-woocommerce' ); ?></option><option value="month" <?php selected('month', $period); ?>><?php esc_html_e( 'Month(s)', 'license-manager-for-woocommerce' ); ?></option><option value="year" <?php selected('year', $period); ?>><?php esc_html_e( 'Year(s)', 'license-manager-for-woocommerce' ); ?></option></select>
		</label>
		<p class="description">
			<?php echo esc_html__('Time interval before renewable License Key(s) expire', 'license-manager-for-woocommerce'); ?>
		</p>
		</fieldset>
		<?php
	}
	public function fieldExpireFormat() {
		$field = 'lmfwc_expire_format';
		$value = isset($this->settings[$field]) ? $this->settings[$field] : '';
		$html = '<fieldset>';
		$html  .= sprintf( '<input type="text" id="%s" name="lmfwc_settings_general[%s]" value="%s" >', $field,
			$field, $value );
		$html .= '<br><br>'; 
		$html  .= sprintf(
			/* translators: %1$s: date format merge code, %2$s: time format merge code, %3$s: general settings URL, %4$s: link to date and time formatting documentation */
			__( '<code>%1$s</code> and <code>%2$s</code> will be replaced by formats from <a href="%3$s">Administration > Settings > General</a>. %4$s', 'license-manager-for-woocommerce' ),
			'{{DATE_FORMAT}}',
			'{{TIME_FORMAT}}',
			esc_url( admin_url( 'options-general.php' ) ),
			__( '<a href="https://wordpress.org/support/article/formatting-date-and-time/">Documentation on date and time formatting</a>.' )
		);
		$html .= '</fieldset>';


		echo wp_kses( $html, lmfwc_allowed_html() );
	}

	/**
	 * Initializes the "lmfwc_rest_api" section.
	 *
	 * @return void
	 */
	private function initSectionAPI() {
		// Add the settings sections.
		add_settings_section(
			'lmfwc_rest_api_section',
			__('REST API', 'license-manager-for-woocommerce'),
			null,
			'lmfwc_rest_api'
		);

		add_settings_field(
			'lmfwc_disable_api_ssl',
			__('API & SSL', 'license-manager-for-woocommerce'),
			array( $this, 'fieldEnableApiOnNonSsl' ),
			'lmfwc_rest_api',
			'lmfwc_rest_api_section'
		);

		add_settings_field(
			'lmfwc_enabled_api_routes',
			__('Enable/disable API routes', 'license-manager-for-woocommerce'),
			array( $this, 'fieldEnabledApiRoutes' ),
			'lmfwc_rest_api',
			'lmfwc_rest_api_section'
		);
	}

	/**
	 * Callback for the "hide_license_keys" field.
	 *
	 * @return void
	 */
	public function fieldHideLicenseKeys() {
		$field = 'lmfwc_hide_license_keys';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;
		?>
		<fieldset>
			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php esc_html_e('Hide license keys in the admin dashboard.', 'license-manager-for-woocommerce'); ?></span>
			</label>
			<p class="description"><?php esc_html_e('All license keys will be hidden and only displayed when the \'Show\' action is clicked.', 'license-manager-for-woocommerce'); ?></p>
		</fieldset>
		<?php
	}

	public function fieldProductDownloads() {
		$field = 'lmfwc_product_downloads';
		( array_key_exists( $field, $this->settings ) ) ? $value = true : $value = false;
		?>
		<fieldset>

	<label for="<?php echo esc_attr( $field ); ?>">
		<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
		<span><?php echo esc_html__('Enable product download management for digital / virtual products e.g. WordPress themes, plugins & more.', 'license-manager-for-woocommerce'); ?></span>
	</label>

	<p class="description">
		<?php echo esc_html__('If this setting is off, the download management for digital / virtual products is not available e.g. current version or changelog field in products.', 'license-manager-for-woocommerce'); ?>
	</p>

</fieldset>
<?php
	}

	
	public function fieldDownloadExpires() {
		$field = 'lmfwc_download_expires';
		( array_key_exists( $field, $this->settings ) ) ? $value = true : $value = false;
		?>
		<fieldset>

			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php echo esc_html__('Automatically set download expiration date in orders to the license expiration date.', 'license-manager-for-woocommerce'); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__('If this setting is off, digital / virtual products can still be downloaded when the license has expired.', 'license-manager-for-woocommerce'); ?>
			</p>

		</fieldset>
		<?php
	}



	/**
	 * Callback for the "lmfwc_allow_duplicates" field.
	 *
	 * @return void
	 */
	public function fieldAllowDuplicates() {
		$field = 'lmfwc_allow_duplicates';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;
		?>
		<fieldset>

	<label for="<?php echo esc_attr( $field ); ?>">
		<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
		<span><?php echo esc_html__('Allow duplicate license keys inside the licenses database table.', 'license-manager-for-woocommerce'); ?></span>
	</label>

</fieldset>

<?php
	}


	/**
	 * Callback for the "lmfwc_disable_api_ssl" field.
	 *
	 * @return void
	 */
	public function fieldEnableApiOnNonSsl() {
		$field = 'lmfwc_disable_api_ssl';
		( array_key_exists($field, $this->settings) ) ? $value = true : $value = false;
		?>
		<fieldset>

			<label for="<?php echo esc_attr( $field ); ?>">
				<input id="<?php echo esc_attr( $field ); ?>" type="checkbox" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>]" value="1" <?php echo checked(true, $value, false); ?>/>
				<span><?php echo esc_html__('Enable the plugin API routes over insecure HTTP connections.', 'license-manager-for-woocommerce'); ?></span>
			</label>

			<p class="description">
				<?php echo esc_html__('This should only be activated for development purposes.', 'license-manager-for-woocommerce'); ?>
			</p>

		</fieldset>
		<?php
	}

	/**
	 * Callback for the "lmfwc_enabled_api_routes" field.
	 *
	 * @return void
	 */
	public function fieldEnabledApiRoutes() {
		$field = 'lmfwc_enabled_api_routes';
		$value = array();
		$routes = array(
			array(
				'id'         => '010',
				'name'       => 'v2/licenses',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '011',
				'name'       => 'v2/licenses/{license_key}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '012',
				'name'       => 'v2/licenses',
				'method'     => 'POST',
				'deprecated' => false,
			),
			array(
				'id'         => '013',
				'name'       => 'v2/licenses/{license_key}',
				'method'     => 'PUT',
				'deprecated' => false,
			),
			array(
				'id'         => '014',
				'name'       => 'v2/licenses/{license_key}',
				'method'     => 'DELETE',
				'deprecated' => false,
			),
			array(
				'id'         => '015',
				'name'       => 'v2/licenses/activate/{license_key}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '016',
				'name'       => 'v2/licenses/deactivate/{license_key}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '017',
				'name'       => 'v2/licenses/validate/{license_key}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '018',
				'name'       => 'v2/generators',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '019',
				'name'       => 'v2/generators/{id}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '020',
				'name'       => 'v2/generators',
				'method'     => 'POST',
				'deprecated' => false,
			),
			array(
				'id'         => '021',
				'name'       => 'v2/generators/{id}',
				'method'     => 'PUT',
				'deprecated' => false,
			),
			array(
				'id'         => '022',
				'name'       => 'v2/generators/{id}',
				'method'     => 'DELETE',
				'deprecated' => false,
			),
			array(
				'id'         => '029',
				'name'       => 'v2/generators/{id}/generate',
				'method'     => 'POST',
				'deprecated' => false,
			),
			array(
				'id'         => '023',
				'name'       => 'v2/customers/{customer_id}/licenses',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '024',
				'name'       => 'v2/products/update/{license_key}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '025',
				'name'       => 'v2/products/download/latest/{license_key}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '026',
				'name'       => 'v2/products/ping',
				'method'     => 'POST',
				'deprecated' => false,
			),
			array(
				'id'         => '027',
				'name'       => 'v2/application/{application_id}',
				'method'     => 'GET',
				'deprecated' => false,
			),
			array(
				'id'         => '028',
				'name'       => 'v2/application/download/{activation_token}',
				'method'     => 'GET',
				'deprecated' => false,
			),


		);
		$classList = array(
			'GET'    => 'text-success',
			'PUT'    => 'text-primary',
			'POST'   => 'text-primary',
			'DELETE' => 'text-danger ',
		);

		if (array_key_exists($field, $this->settings)) {
			$value = $this->settings[$field];
		}
		?>
		<fieldset>

			<?php foreach ($routes as $route) : ?>
				<?php
				$checked = false;

				if (array_key_exists($route['id'], $value) && '1' === $value[$route['id']]) {
					$checked = true;
				}
				?>
				<label for="<?php echo esc_attr( $field ) . '-' . esc_attr( $route['id'] ); ?>">
					<input id="<?php echo esc_attr( $field ) . '-' . esc_attr( $route['id'] ); ?>" type="checkbox" name="lmfwc_settings_general[<?php echo esc_attr( $field ); ?>][<?php echo esc_attr( $route['id'] ); ?>]" value="1" <?php echo checked(true, $checked, false); ?>>
					<code><b class="<?php echo esc_attr( $classList[$route['method']] ); ?>"><?php echo esc_attr( $route['method'] ); ?></b> - <?php echo esc_attr( $route['name'] ); ?></code>

					<?php if (true === $route['deprecated']) : ?>
						<code class="text-info"><b><?php echo esc_html( strtoupper(__('Deprecated', 'license-manager-for-woocommerce')) ); ?></b></code>
					<?php endif; ?>

				</label>
				<br>

			<?php endforeach; ?>

			<p class="description" style="margin-top: 1em;">
				<?php 
				printf(
					/* translators: %s is the link of Api Docs */
					wp_kses(
						'The complete <b>API documentation</b> can be found <a href="%s" target="_blank" rel="noopener">here</a>.',
						'license-manager-for-woocommerce'
					, lmfwc_allowed_html()),
					esc_url('https://woo.com/document/license-manager-woo-store-owner-guide/#section-7')
				);
				?>
			</p>

		</fieldset>
		<?php
	}
}
