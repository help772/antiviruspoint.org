<?php
/**
 * HTML markup for GAM connection modal frame.
 *
 * @package AdvancedAds\Gam
 */

use AdvancedAds\Utilities\Conditional;
use AdvancedAds\Framework\Utilities\Params;

$creds = Advanced_Ads_Gam_Oauth2::get_credentials();
$nonce = wp_create_nonce( 'gam-connect' );

$state = [
	'api'        => 'gam',
	'nonce'      => $nonce,
	'return_url' => admin_url( 'admin.php?page=advanced-ads-settings&oauth=1#top#gam' ),
];

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' .
			rawurlencode( 'https://www.googleapis.com/auth/dfp' ) .
			'&client_id=' . $creds['id'] .
			'&redirect_uri=' . rawurlencode( Advanced_Ads_Gam_Admin::API_REDIRECT_URI ) .
			'&state=' . rawurlencode( base64_encode( wp_json_encode( $state ) ) ) . // phpcs:ignore
			'&access_type=offline&include_granted_scopes=true&prompt=consent&response_type=code';

$_get = wp_unslash( $_GET );

if ( '1' === Params::get( 'oauth' ) && 'gam' === Params::get( 'api' ) ) : ?>
	<?php if ( isset( $_get['nonce'] ) && false !== wp_verify_nonce( $_get['nonce'], 'gam-connect' ) ) : ?>
		<?php if ( isset( $_get['code'] ) && Conditional::user_can( 'advanced_ads_manage_options' ) ) : ?>
		<input type="hidden" id="advads-gam-oauth-code" value="<?php echo esc_attr( urldecode( $_get['code'] ) ); ?>" />
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>
<div id="advads-gam-modal" data-gamsettings="<?php echo esc_url( admin_url( 'admin.php?page=advanced-ads-settings#top#gam' ) ); ?>" data-url="<?php echo esc_url( $auth_url ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>">
	<div id="advads-gam-modal-outer">
		<div>
			<div id="advads-modal-content">
				<div class="advads-gam-modal-content-inner" data-content="confirm-code">
					<i class="dashicons dashicons-dismiss"></i>
					<h2><?php esc_html_e( 'Processing authorization', 'advanced-ads-gam' ); ?></h2>
					<div class="advads-gam-overlay">
						<img alt="..." src="<?php echo esc_url( ADVADS_BASE_URL ); ?>admin/assets/img/loader.gif" style="margin-top:3em" />
					</div>
				</div>
				<div class="advads-gam-modal-content-inner" style="display:none;" data-content="soap_fault">
					<i class="dashicons dashicons-dismiss"></i>
					<h2><?php esc_html_e( 'Switching transport method', 'advanced-ads-gam' ); ?></h2>
					<div class="advads-gam-overlay">
						<img alt="..." src="<?php echo esc_url( ADVADS_BASE_URL ); ?>admin/assets/img/loader.gif" style="margin-top:3em" />
					</div>
				</div>
				<div class="advads-gam-modal-content-inner" style="display:none;" data-content="empty_account">
					<i class="dashicons dashicons-dismiss"></i>
					<p class="advads-error-message"><?php esc_html_e( 'No Ad Manager network found in this Google account', 'advanced-ads-gam' ); ?></p>
				</div>
				<div class="advads-gam-modal-content-inner" style="display:none;" data-content="select_account">
					<i class="dashicons dashicons-dismiss"></i>
					<label style="font-size:1.1em;font-weight:600;margin-bottom:.3em;display:block;"><?php esc_html_e( 'Please select the network account', 'advanced-ads-gam' ); ?></label>
					<select id="gam-account-list"></select>
					<input type="hidden" id="gam-account-list-data" value="" />
					<p class="submit">
						<button id="gam-selected-network" class="button-secondary preventDefault"><?php esc_html_e( 'Select account', 'advanced-ads-gam' ); ?></button>
					</p>
					<div class="advads-gam-overlay">
						<img alt="..." src="<?php echo esc_url( ADVADS_BASE_URL ); ?>admin/assets/img/loader.gif" style="margin-top:3em" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="advads-gam-page-overlay"></div>
