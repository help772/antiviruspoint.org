<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

?>

<style>
.scrolldiv {
	overflow-y: auto;
	max-height: 350px;
}
.frmTable { margin-bottom: 10px !important; border:none !important;}
.frmTable td {border:none !important;}
</style>
<table class="form-table frmTable" width="100%">
		<tbody>
            <tr>
			<td width="20%"></h3></td>
            <td width="80%" style="text-align:right;padding-right:0;"><button class="button button-primary wp-element-button" id="btnAddExemption" type="submit" >Add Exemption</button></td>
            </tr>
            <tr>
			<td colspan="2" style="padding-top:12px;">
				<div class="wc-avatax-exemption-notice wc-avatax-certificate-sync-notice" role="alert">
					<span class="wc-avatax-exemption-notice__icon" aria-hidden="true">
						<span class="dashicons dashicons-info"></span>
					</span>
					<div class="wc-avatax-exemption-notice__content">
						<h3 class="wc-avatax-exemption-notice__title"><?php esc_html_e( 'Certificate Pending Validation', 'woocommerce-avatax' ); ?></h3>
						<p class="wc-avatax-exemption-notice__text"><?php esc_html_e( 'If your certificate is in pending status, please allow the administrator to validate it. Once your certificate has been validated by an administrator, it takes a brief processing period before it becomes active for exemption use.', 'woocommerce-avatax' ); ?></p>
						<ul class="wc-avatax-exemption-notice__steps">
							<li>
								<span class="dashicons dashicons-yes" aria-hidden="true"></span>
								<?php esc_html_e( 'Pending Admin Validation', 'woocommerce-avatax' ); ?>
							</li>
							<li>
								<span class="dashicons dashicons-clock" aria-hidden="true"></span>
								<?php esc_html_e( '30 sec - 2 min activation', 'woocommerce-avatax' ); ?>
							</li>
							<li class="wc-avatax-exemption-notice__step--ready">
								<span class="dashicons dashicons-yes wc-avatax-exemption-notice__step--ready" aria-hidden="true"></span>
								<?php esc_html_e( 'Auto-applied once active', 'woocommerce-avatax' ); ?>
							</li>
						</ul>
					</div>
				</div>
			</td>
            </tr>
        </tbody>
</table>  
<?php if ( $has_certificates ) : ?>
	<div class="scrolldiv">
	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
			
		<thead>
		<tr>
			<tr>
				<?php foreach ( wc_get_account_certificates_columns() as $column_id => $column_name ) : ?>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $customer_certificates as $customer_certificate ) {
                $certid = $customer_certificate['id'];
				$customerCode = $customer_certificate['customerCode'];
				?>
				<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-order">
					<?php foreach ( wc_get_account_certificates_columns() as $column_id => $column_name ) : ?>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
								<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>

							<?php elseif ( 'certificate-state' === $column_id ) : ?>
									<?php echo esc_html($customer_certificate['state']); ?>
								</a>
							<?php elseif ( 'certificate-signedDate' === $column_id ) : ?>
								<time datetime="<?php echo esc_attr( $customer_certificate['signedDate']); ?>"><?php echo esc_html($customer_certificate['signedDate']); ?></time>
                            
                            <?php elseif ( 'certificate-expirationDate' === $column_id ) : ?>
							<time datetime="<?php echo esc_attr( $customer_certificate['expirationDate']); ?>"><?php echo esc_html($customer_certificate['expirationDate']); ?></time>

							<?php elseif ( 'certificate-status' === $column_id ) : ?>
								<?php echo esc_html( $customer_certificate['status']); ?>
							<?php elseif ( 'certificate-ecm-validity' === $column_id ) : ?>
								<?php echo esc_html( isset( $customer_certificate['ecmStatus'] ) ? $customer_certificate['ecmStatus'] : '' ); ?>
                            <?php elseif ( 'certificate-view' === $column_id ) : ?>
								<a class="wc-avatax-download-certificate" cert-id="<?php echo esc_attr( $certid ); ?>" href=""><?php echo esc_html__( 'View Certificate', 'woocommerce-avatax' ); ?></a>

                            <?php elseif ( 'certificate-invalidate' === $column_id ) : ?>
								<a class="wc-avatax-unlink-certificate" cert-id="<?php echo esc_attr( $certid ); ?>" customer-code="<?php echo esc_attr( $customerCode ); ?>" href=""><?php echo esc_html__( 'Invalidate Certificate', 'woocommerce-avatax' ); ?></a>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<div>
<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
	<?php esc_html_e( 'No certificates found for this customer.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>
