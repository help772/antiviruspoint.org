<?php
/**
 * The template for the overview of all application downloads inside "My Account"
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/lmfwc/my-account/application/index.php
 *
 * HOWEVER, on occasion I will need to update template files and you
 * (the developer) will need to copy the new files to your theme to
 * maintain compatibility. I try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 *
 * Default variables
 *
 * @var $data  array
 * @var $page  int
 * @var $per_page int
 * @var $date_format string
 * @var $nonce string
 * @var $licensesService LicenseManagerForWooCommerce\Core\Services\LicensesService
 * @var $applicationReleasesService LicenseManagerForWooCommercePro\Core\Services\ApplicationReleasesService
 */
use LicenseManagerForWooCommerce\Repositories\Resources\ApplicationRelease as ApplicationReleaseResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;
use LicenseManagerForWooCommerce\Integrations\WooCommerce\Controller;

?>

<table class="lmfwc-account-table lmfwc-account-downloads-table shop_table shop_table_responsive my_account_orders">
	<thead>
	<tr>
		<th class="lmfwc-header-column lmfwc-column-license"><?php echo esc_html__( 'License #', 'license-manager-for-woocommerce' ); ?></th>
		<th class="lmfwc-header-column lmfwc-column-name"><?php echo esc_html__( 'Application Name', 'license-manager-for-woocommerce' ); ?></th>
		<th class="lmfwc-header-column lmfwc-column-release"><?php echo esc_html__( 'Latest Release', 'license-manager-for-woocommerce' ); ?></th>
		<th class="lmfwc-header-column lmfwc-column-actions"><?php echo esc_html__( 'Actions', 'license-manager-for-woocommerce' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php if ( ! empty( $downloads['data'] ) ) : ?>
		<?php 
		foreach ( $downloads['data'] as $download ) :
			$license_url = \LicenseManagerForWooCommerce\Integrations\WooCommerce\Controller::getAccountLicenseUrl( $download['license_id'] );
			?>
			<tr>
				<td class="lmfwc-body-column lmfwc-column-license">
					<a href="<?php echo esc_url($license_url); ?>">
						<?php printf( '#%d', esc_html( $download['license_id'] ) ); ?>
					</a>
				</td>
				<td class="lmfwc-body-column lmfwc-column-name">
					<?php echo ! empty( $download['application_name'] ) ? esc_html( $download['application_name'] ) : 'N/a'; ?>
				</td>
				<td class="lmfwc-body-column lmfwc-column-release">
					<?php echo ! empty( $download['application_latest_release'] ) ? esc_html( $download['application_latest_release'] ) : 'N/a'; ?>
				</td>
				<td class="lmfwc-body-column lmfwc-column-actions">
					
					<?php
					$release = ! empty( $download['application_latest_release_id'] ) ? ApplicationReleaseResourceRepository::instance()->find( $download['application_latest_release_id'] ) : new WP_Error();
					$license = LicenseResourceRepository::instance()->findBy(array( 'id' => $download['license_id'] ) );
					if (!is_wp_error($license) && !is_wp_error($release) && is_object($release) ) {

						$text = !empty( $icon ) ? '<span class="lmfwc-icon ' . $icon . '"></span>' : __( 'Download', 'license-manager-for-woocommerce' );
						if ( $release->isDownloadAllowed( $license ) ) : 
							?>
								<form method="POST" action="<?php echo esc_url(home_url()); ?>" class="lmfwc-application-download-button-form lmfwc-style-<?php echo ! empty( $style ) ? esc_attr($style) : 'standard'; ?>">
									<input type="hidden" name="action" value="application_download">
									<?php wp_nonce_field( 'lmfwc_nonce', 'lmfwc_nonce' ); ?>
									<input type="hidden" name="license_id" value="<?php echo esc_attr($license->getId()); ?>">
									<input type="hidden" name="application_release" value="<?php echo esc_attr($release->getId()); ?>">
									<button type="submit" title="<?php echo esc_html__('Download the latest release', 'license-manager-for-woocommerce'); ?>" class="woocommerce-button button lmfwc-button" name="application_download" value="1" action="application_download">
										<?php echo wp_kses_post($text); ?>
										<span class="fa fa-download"></span>
									</button>
									<a title="<?php echo esc_html__( 'View older releases', 'license-manager-for-woocommerce' ); ?>" href="<?php echo esc_url( Controller::getAccountApplicationUrl( $license->getId())); ?>" class="woocommerce-button button">View 
									<span class="fa fa-eye"></span>
									</a>
								</form>
							<?php else : ?>
								<button disabled title="<?php echo esc_html__( 'License expired. Please extend your license in order to be able to download this file.', 'license-manager-for-woocommerce' ); ?>" class="button button-disabled">
									<?php echo wp_kses_post($text); ?>
									<span class="fa fa-download"></span>
								</button>
							<?php 
							endif; 
					
					}
					?>
					
				</td>
			</tr>
		<?php endforeach; ?>
	<?php else : ?>
		<tr>
			<td colspan="4"><?php echo esc_html__( 'No downloads found.', 'license-manager-for-woocommerce' ); ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
