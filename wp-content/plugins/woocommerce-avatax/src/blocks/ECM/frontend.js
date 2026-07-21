/**
 * External dependencies
 */
import { registerCheckoutBlock } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import $ from 'jquery';
/**
 * Internal dependencies
 */
import metadata from './block.json';

const Block = ({ children, checkoutExtensionData }) => {
	const handleAddCertificateClick = (e) => {
		// Prevent default behavior first
		e.preventDefault();
		e.stopPropagation();
		
		// Try to get user_id from either object
		const userId = wc_avatax_frontend_misc?.user_id || wc_avatax_frontend?.user_id;
		
		// Check if user is guest (user_id === 0 or "0")
		if (userId === 0 || userId === "0") {
			// For guest users, redirect to My Account page with redirect_to parameter
			const myaccounturl = wc_avatax_frontend_misc?.myaccount_url || wc_avatax_frontend?.myaccount_url;
			const checkouturl = wc_avatax_frontend_misc?.checkout_url || wc_avatax_frontend?.checkout_url;
			
			if (myaccounturl && checkouturl) {
				const url = myaccounturl + "?redirect_to=" + checkouturl;
				window.location = url;
				return false;
			}
		}
		
		// For logged-in users, show the certificate popup
		$(".container").css({
			"overflow-y": "",
			"height": ""
		});
		$('#pop').show();
		$('#overlay').show();
		return false;
	};
	const handleManageCertificateClick = (e) => {
		// Prevent default behavior first
		e.preventDefault();
		e.stopPropagation();
		
		// Try to get user_id from either object
		const userId = wc_avatax_frontend_misc?.user_id || wc_avatax_frontend?.user_id;
		
		// Check if user is guest (user_id === 0 or "0")
		if (userId === 0 || userId === "0") {
			// For guest users, redirect to My Account page with redirect_to parameter pointing to certificate management
			const myaccounturl = wc_avatax_frontend_misc?.myaccount_url || wc_avatax_frontend?.myaccount_url;
			
			if (myaccounturl) {
				const certificateurl = myaccounturl + '/tax-certificate';
				const url = myaccounturl + "?redirect_to=" + encodeURIComponent(certificateurl);
				window.location = url;
				return false;
			}
		}
		
		// For logged-in users, use the existing AJAX flow
		var data = {
            action: 'wc_avatax_manage_certificate_link',
        };
        jQuery.post(wc_avatax_frontend.ajax_url, data, function(response) {
			window.location = response.data;
            return;
        })
		 
   };

	// Check if user is guest
	const userId = wc_avatax_frontend_misc?.user_id || wc_avatax_frontend?.user_id;
	const isGuest = userId === 0 || userId === "0";
	
	// Get URLs for guest user links
	const myaccounturl = wc_avatax_frontend_misc?.myaccount_url || wc_avatax_frontend?.myaccount_url;
	const checkouturl = wc_avatax_frontend_misc?.checkout_url || wc_avatax_frontend?.checkout_url;
	
	if (isGuest) {
		// Show guest user UI
		return (
			<div className="wp-block-woocommerce-checkout-order-summary-ecm-links-block wc-block-components-totals-wrapper">
				<div className="wc-block-components-totals-ecm-guest-info">
					<div className="wc-block-components-totals-ecm-guest-card">
						<div className="wc-block-components-totals-ecm-guest-header">
							<span className="wc-block-components-totals-ecm-guest-icon">i</span>
							<span className="wc-block-components-totals-ecm-guest-title">Are you tax exempt?</span>
						</div>
						<div className="wc-block-components-totals-ecm-guest-text">
							<a href={myaccounturl + "?redirect_to=" + checkouturl} className="wc-block-components-totals-ecm-guest-link">Log in</a>
							<span> to manage your certificates or </span>
							<a href={myaccounturl + "?redirect_to=" + checkouturl} className="wc-block-components-totals-ecm-guest-link">create an account</a>
							<span> to add a certificate.</span>
						</div>
					</div>
				</div>
			</div>
		);
	}
	
	// Show logged-in user UI
	return (
		<div className="wp-block-woocommerce-checkout-order-summary-ecm-links-block wc-block-components-totals-wrapper">
			<div className="wc-block-components-totals-ecm-links">
				<a
					role="button"
					id="cert_link"
					href="#"
					className="wc-block-components-totals-ecm-link wc-block-components-totals-ecm-link--upload"
					aria-label={__(
						'Upload new exemption certificate',
						'woo-gutenberg-products-block'
					)}
					onClick={handleAddCertificateClick}
				>
					<span className="wc-block-components-totals-ecm-link__icon">+</span>
					{__('Upload new exemption certificate', 'woo-gutenberg-products-block')}
				</a>
				<div><a
					role="button"
					href="#"
					className="wc-block-components-totals-ecm-link wc-block-components-totals-ecm-link--manage"
					aria-label={__(
						'Manage existing certificates',
						'woo-gutenberg-products-block'
					)}
					onClick={handleManageCertificateClick}
				>
					<span className="wc-block-components-totals-ecm-link__icon">⚙</span>
					{__('Manage existing certificates', 'woo-gutenberg-products-block')}
				</a></div>
			</div>
		</div>
	)
}

registerCheckoutBlock({
	metadata,
	component: Block,
});