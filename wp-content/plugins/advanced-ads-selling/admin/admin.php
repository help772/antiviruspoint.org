<?php // phpcs:ignoreFile

use AdvancedAds\Framework\Utilities\Params;

class Advanced_Ads_Selling_Admin {

	/**
	 * stores the settings page hook
	 *
	 * @since   1.0.0
	 * @var     string
	 */
	protected $settings_page_hook = '';

	/**
	 * holds base class
	 *
	 * @var Advanced_Ads_Selling_Plugin
	 * @since 1.0.0
	 */
	protected $plugin;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		$this->plugin = Advanced_Ads_Selling_Plugin::get_instance();

		add_action('plugins_loaded', array($this, 'wp_admin_plugins_loaded'));
	}

	/**
	 * load actions and filters
	 */
	public function wp_admin_plugins_loaded() {
		if( ! class_exists( 'WooCommerce', false ) ){
			add_action('advanced-ads-admin-notices', array($this, 'missing_woocommerce_plugin_notice'));
		}

		if ( ! class_exists( 'WooCommerce', false ) ) {
			return ;
		}

		// add add-on settings to plugin settings page
		add_action('advanced-ads-settings-init', array($this, 'settings_init'), 9 );
		add_filter('advanced-ads-setting-tabs', array($this, 'setting_tabs'));

		// save ads post type
		add_action( 'save_post', array($this, 'set_auto_expiry_date'), 15 );

		// hide injection box with placements for ordered ads
		add_action('edit_form_top', array($this, 'hide_injection_box'));

		add_action( 'advanced-ads-dashbaord-widget_next-steps', [ $this, 'render_ad_store_widget' ], 10 );

		add_action( 'advanced-ads-export-options', array( $this, 'export_options' ) );

	}

	public function set_auto_expiry_date( $post_id ) {
		$aas_pro_days			= '';
		$aas_auto_updt_exp_date = get_post_meta( $post_id, 'advanced_ads_selling_auto_updt_exp_date', true );

		// Only use for ads, no other post type.
		$post_type = Params::post( 'post_type' );
		if ( $aas_auto_updt_exp_date && ( 'advanced_ads' == $post_type || !empty( $_POST['advanced_ad']['type'] ) ) ) {
			return;
		}

		$ad_pricing_option = '';
		$order_id 	= get_post_meta( $post_id, 'advanced_ads_selling_order', true );
		$orderdata 	=  new WC_Order( $order_id );

		$items = $orderdata->get_items();
		if( !empty( $items ) ) {
			foreach ( $items as $key => $orderitem ) {
				if( isset( $orderitem['ad_sales_type'] ) && 'days' === $orderitem['ad_sales_type']
					&& !empty( $orderitem['ad_pricing_option'] ) ){
					$expiry_days = absint( $orderitem['ad_pricing_option'] );

				}
			}
		}

		if ( isset( $expiry_days ) && $expiry_days ) {
			//Get expiry date
			$exp_date = strtotime( "+".$expiry_days." days" );

			if( current_time( 'timestamp' ) < $exp_date ) {
				$ad_options 	= get_post_meta( $post_id, 'advanced_ads_ad_options', true );
				$ad_options	= !empty( $ad_options ) ? $ad_options : array();

				$ad_options['expiry_date'] = $exp_date;

				update_post_meta( $post_id, 'advanced_ads_ad_options', $ad_options );
				update_post_meta( $post_id, 'advanced_ads_selling_auto_updt_exp_date', 1 );
			}
		}
	}

	/**
	 * show warning if WooCommerce is not activated
	 */
	public function missing_woocommerce_plugin_notice() {

		$plugins = get_plugins();

		if( isset( $plugins['woocommerce/woocommerce.php'] ) ){ // is installed, but not active
		$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=woocommerce/woocommerce.php&amp', 'activate-plugin_woocommerce/woocommerce.php' ) . '">'. __( 'Activate Now', 'advanced-ads-selling' ) .'</a>';
		} else {
		$link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'woocommerce'), 'install-plugin_' . 'woocommerce') . '">'. __( 'Install Now', 'advanced-ads-selling' ) .'</a>';
		}
		echo '
		<div class="notice notice-error advads-admin-notice">
		  <p>'. __( '<em>Advanced Ads – Selling Ads</em> requires the free <strong>WooCommerce</strong> to be installed and activated on your site.', 'advanced-ads-selling' ) .
		 '&nbsp;' . $link . '</p></div>';

	}

	/**
	 * add settings to settings page
	 */
	public function settings_init() {
		register_setting( Advanced_Ads_Selling_Plugin::OPTION_KEY, Advanced_Ads_Selling_Plugin::OPTION_KEY );

		/**
		 * Allow Ad Admin to save Selling Ads options.
		 *
		 * @param array $settings Array with allowed options.
		 *
		 * @return array
		 */
		add_filter( 'advanced-ads-ad-admin-options', function( $options ) {
			$options[] = Advanced_Ads_Selling_Plugin::OPTION_KEY;

			return $options;
		} );

		// add new section
		add_settings_section(
			'advanced_ads_selling_setting_section', __('Ad Store', 'advanced-ads-selling'), array($this, 'render_settings_section_callback'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings'
		);

		// setting for store admin
		add_settings_field(
			'advanced-ads-selling-admin-email', __( 'Store admin email', 'advanced-ads-selling' ), array($this, 'render_settings_admin_email'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings', 'advanced_ads_selling_setting_section'
		);
		// setting for store sender admin
		add_settings_field(
			'advanced-ads-selling-sender-email', __( 'Store sender email', 'advanced-ads-selling' ), array($this, 'render_settings_sender_email'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings', 'advanced_ads_selling_setting_section'
		);
		// setting for public setup page
		add_settings_field(
			'advanced-ads-selling-public-setup-page', __( 'Setup page', 'advanced-ads-selling' ), array($this, 'render_settings_setup_page'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings', 'advanced_ads_selling_setting_section'
		);
		// setting to hide public ad setup
		add_settings_field(
			'advanced-ads-selling-hide-ad-setup', __( 'Hide ad setup', 'advanced-ads-selling' ), array($this, 'render_settings_hide_ad_setup'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings', 'advanced_ads_selling_setting_section'
		);
		// apply WooCommerce fixes
		add_settings_field(
			'advanced-ads-selling-wc-fixes', __( 'WooCommerce adjustments', 'advanced-ads-selling' ), array($this, 'render_settings_wc_fixes'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings', 'advanced_ads_selling_setting_section'
		);

		if ( defined ('AAT_VERSION' )  &&  version_compare( AAT_VERSION, '1.8.18', '>=' ) ) {
			// Enable "Public Stats" Endpoint.
			add_settings_field(
					'advanced-ads-selling-ads-page', __( 'List ads in the customer account', 'advanced-ads-selling' ), array($this, 'render_settings_public_ads_page'), Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings', 'advanced_ads_selling_setting_section'
			);
		}
	}


	/**
	 * add selling settings tab
	 *
	 * @param arr $tabs existing setting tabs
	 * @return arr $tabs setting tabs with Selling tab attached
	 */
	public function setting_tabs(array $tabs) {

		$tabs['selling'] = array(
			// TODO abstract string
			'page' => Advanced_Ads_Selling_Plugin::OPTION_KEY . '-settings',
			'group' => Advanced_Ads_Selling_Plugin::OPTION_KEY,
			'tabid' => 'selling',
			'title' => 'Selling'
		);

		return $tabs;
	}

	/**
	 * Render settings section
	 */
	public function render_settings_section_callback() {

	}

	/**
	 * check if license is valid
	 *
	 * @since 1.0.0
	 * @return bool true if license is valid
	 */
	public function license_valid(){
		$status = Advanced_Ads_Admin_Licenses::get_instance()->get_license_status( 'advanced-ads-selling' );
		if( 'valid' === $status ){
			return true;
		}
		return false;
	}

	/**
	 * Render admin email settings field
	 *
	 */
	public function render_settings_admin_email() {
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$admin_email = ( isset( $options['admin-email'] ) && $options['admin-email'] ) ? sanitize_email( $options['admin-email'] ) : get_bloginfo( 'admin_email' );
		require AA_SELLING_ABSPATH . 'admin/views/setting-admin-email.php';
	}

	/**
	 * Render sender email settings field
	 *
	 */
	public function render_settings_sender_email() {

		$sender_email = Advanced_Ads_Selling_Notifications::get_sender_email();
		require AA_SELLING_ABSPATH . 'admin/views/setting-sender-email.php';
	}

	/**
	 * Render public page settings field
	 *
	 */
	public function render_settings_setup_page() {
		// Early bail!!
		$pages             = get_pages();
		if ( ! $pages ) {
			?>
			<p><?php _e( 'No static pages found.', 'advanced-ads-selling' ); ?></p>
			<?php
			return;
		}

		$options           = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$public_page_id    = isset( $options['setup-page-id'] ) && $options['setup-page-id'] ? absint( $options['setup-page-id'] ) : false;
		$permalink_warning = false === $public_page_id && empty( get_option( 'permalink_structure', '' ) );

		require AA_SELLING_ABSPATH . 'admin/views/setting-setup-page.php';
	}

	/**
	 * Render hide-ad-setup setting
	 *
	 */
	public function render_settings_hide_ad_setup() {
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$hide_ad_setup = ( isset( $options['hide-ad-setup'] ) && $options['hide-ad-setup'] ) ? 1 : 0;
		require AA_SELLING_ABSPATH . 'admin/views/setting-hide-ad-setup.php';
	}

	/**
	 * Render WooCommerce fixes setting
	 *
	 */
	public function render_settings_wc_fixes() {
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$wc_fixes = ( isset( $options['wc-fixes'] ) && $options['wc-fixes'] ) ? 1 : 0;
		require AA_SELLING_ABSPATH . 'admin/views/setting-wc-fixes.php';
	}

	/**
	 * Render Public Stats enabling setting
	 */
	public function render_settings_public_ads_page() {
		$options = Advanced_Ads_Selling_Plugin::get_instance()->options();
		$ads_page_enabled = ( isset( $options['ads-page'] ) && $options['ads-page'] ) ? 1 : 0;
		require AA_SELLING_ABSPATH . 'admin/views/setting-ads-page.php';
	}

	/**
	 * hide the placement box for new published ads for ordered ads
	 */
	public function hide_injection_box( $post ){

		if( Advanced_Ads_Selling_Plugin::is_ordered_ad( $post->ID ) ) :
			?><style>#advads-ad-injection-box { display: none !important; }</style><?php
		endif;
	}

	/**
	 * Show Ad Store widget on Advanced Ads dashboard
	 *
	 * @return void
	 */
	public function render_ad_store_widget(){
		// get number of drafted sold ads
		$args = array( // only needs to set unusual arguments
			'post_status' => 'draft', // is draft = new
			'meta_key' => 'advanced_ads_selling_order_item', // is sold
			'meta_compare' => 'EXISTS' // with existing meta value
		);
		$draft_ads = wp_advads_ad_query($args)->posts;

		// get number of pending sold ads
		$args = array( // only needs to set unusual arguments
			'post_status' => 'pending', // is draft = new
			'meta_key' => 'advanced_ads_selling_order_item', // is sold
			'meta_compare' => 'EXISTS' // with existing meta value
		);
		$pending_ads = wp_advads_ad_query( $args )->posts;

		require AA_SELLING_ABSPATH . 'views/admin/widgets/aa-dashboard.php';
	}

	/**
	 * Add Selling options to the list of options to be exported
	 *
	 * @param $options Array of option data keyed by option keys.
	 * @return $options Array of option data keyed by option keys.
	 */
	public function export_options( $options ) {
		$options[ Advanced_Ads_Selling_Plugin::OPTION_KEY ] = get_option( Advanced_Ads_Selling_Plugin::OPTION_KEY );
		return $options;
	}


}
