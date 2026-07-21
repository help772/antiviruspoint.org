/**
 * AvaTax Blocks Checkout Guest User Handler
 * Handles certificate link clicks for guest users in blocks checkout
 */
jQuery(document).ready(function($) {
	// Override the cert_link click handler for guest users
	$("#cert_link").off("click").on("click", function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		// Check if user is guest
		const userId = wc_avatax_frontend_misc?.user_id || wc_avatax_frontend?.user_id;
		if (userId === 0 || userId === "0") {
			// For guest users, redirect to My Account page
			const myaccounturl = wc_avatax_frontend_misc?.myaccount_url || wc_avatax_frontend?.myaccount_url;
			const checkouturl = wc_avatax_frontend_misc?.checkout_url || wc_avatax_frontend?.checkout_url;
			
			if (myaccounturl && checkouturl) {
				const url = myaccounturl + "?redirect_to=" + checkouturl;
				window.location = url;
				return false;
			}
		} else {
			// For logged-in users, show the popup
			$(".container").css({
				"overflow-y": "",
				"height": ""
			});
			$("#pop").show();
			$("#overlay").show();
			$("#exemption-zone-state").select2();
		}
		return false;
	});
});

