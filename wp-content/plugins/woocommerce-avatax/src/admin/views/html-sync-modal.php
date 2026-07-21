<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 * @codeCoverageIgnore
 */

 // @codeCoverageIgnoreStart
?>
<script type="text/template" id="tmpl-wc-avatax-sync-modal">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1>{{{data.title}}}</h1>
					<# if ( ! data.batch_enabled ) { #>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce-avatax' ); ?></span>
					</button>
					<# } #>
				</header>
				<article>{{{data.body}}}</article>
				<footer>
					<div class="inner">
						<# if ( data.cancel ) { #>
						<button id="btn-cancel" class="button button-large button-secondary modal-close">{{{data.cancel}}}</button>
						<# } #>
						<# if ( data.action ) { #>
						<button id="btn-ok" class="button button-large button-primary {{{data.button_class}}}">{{{data.action}}}</button>
						<# } #>
						<# if ( ! data.cancel && ! data.action ) { #>
						<button id="btn-close" class="button button-large button-secondary modal-close"><?php esc_html_e( 'Close', 'woocommerce-avatax' ); ?></button>
						<# } #>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
<?php
// @codeCoverageIgnoreEnd
?>
