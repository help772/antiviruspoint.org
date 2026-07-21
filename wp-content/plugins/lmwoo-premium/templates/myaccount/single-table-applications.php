<?php
/**
 * This template is used to display list of releases on the Application single page in "My Account"
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/lmfwc/application/partials/table-releases.php.
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
 * @var LicenseResourceModel $license
 * @var ApplicationResourceModel $application
 * @var ApplicationReleaseResourceModel[] $releases
 * @var int $per_page
 * @var int $page
 * @var int $total
 * @var string $nonce
 * @var bool $pagination
 */

use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\Application as ApplicationResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\ApplicationRelease as ApplicationReleaseResourceModel;
use LicenseManagerForWooCommerce\Integrations\WooCommerce\Controller;

?>

<table class="shop_table shop_table_responsive my_account_orders">

	<thead>
		  <div class="lmfwc-header">
		<h3 class="product-name"><?php esc_html_e( 'Downloads', 'license-manager-for-woocommerce' ); ?></h3>
	</div>
	<tr>
		<th><?php echo esc_html__( 'Version', 'license-manager-for-woocommerce' ); ?></th>
		<th><?php echo esc_html__( 'Release Date', 'license-manager-for-woocommerce' ); ?></th>
		<th></th>
	</tr>
	</thead>

	<tbody>
	<?php if ( count( $releases ) > 0 ) : ?>

		<?php foreach ( $releases as $release ) : ?>

			<tr>
				<td>
					<?php 
					/**
					 * Filter lmfwc_myaccount_release_version
					 * 
					 * @since 1.0
					**/
					$release_version = apply_filters( 'lmfwc_myaccount_release_version', $release->getVersion(), $release );
					echo esc_attr($release_version); 
					/**
					 * Filter lmfwc_myaccount_release_is_latest
					 * 
					 * @since 1.0
					**/
					if ( $application && ( apply_filters( 'lmfwc_myaccount_release_is_latest', false, $release ) || $application->getStableReleaseId() == $release->getId() ) ) : 

						?>
						<span class="lmfwc-small-badge lmfwc-small-badge-success"><?php echo esc_html__( 'Latest', 'license-manager-for-woocommerce' ); ?></span>
					<?php endif; ?>
				</td>
				<td>
				<?php 
				echo esc_attr($release->getCreatedAtFormatted()); 
$text = !empty( $icon ) ? '<span class="lmfwc-icon ' . $icon . '"></span>' : __( 'Download', 'license-manager-for-woocommerce' );
				?>
			</td>
				<td>
						<form method="POST" action="<?php echo esc_url(home_url()); ?>" class="lmfwc-application-download-button-form lmfwc-style-<?php echo ! empty( $style ) ? esc_attr($style) : 'standard'; ?>"> <input type="hidden" name="action" value="application_download"> <input type="hidden" name="lmfwc_nonce" value="<?php echo esc_attr($nonce); ?>"> <input type="hidden" name="license_id" value="<?php echo esc_attr($license->getId()); ?>"> <input type="hidden" name="application_release" value="<?php echo esc_attr($release->getId()); ?>"> <button type="submit" title="<?php echo esc_html__('Download the latest release', 'license-manager-for-woocommerce'); ?>" class="woocommerce-button button lmfwc-button" name="application_download" value="1"> <?php echo wp_kses_post($text); ?> </button> </form> 
				</td>
			</tr>

		<?php endforeach; ?>

	<?php else : ?>

		<tr>
			<td colspan="3"><?php echo esc_html__( 'No downloads found.', 'license-manager-for-woocommerce' ); ?></td>
		</tr>

	<?php endif; ?>

	</tbody>

</table>
