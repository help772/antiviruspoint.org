/**
 * Frontend JS
 *
 * @package WooCommerce_Shipping_UPS
 */

( function () {

	// We need our localized data to be able to run this script.
	if ( ! wc_ups_checkout_params ) {
		return;
	}

	/**
	 * Remove any of our stale address-validation notices from WooCommerce's sticky
	 * submit-error container (`.woocommerce-NoticeGroup-checkout`). That container is
	 * populated on a blocked Place Order attempt and NOT refreshed by WooCommerce on
	 * subsequent update_order_review calls — so without this, the error notice from
	 * a blocked submission lingers even after the customer changes rate or applies
	 * the suggested address. We identify our notices via the `.wc-shipping-ups-notice-marker`
	 * span injected by `get_invalid_destination_address_notice_message()`.
	 */
	const clearStaleUpsSubmitNotice = function () {
		const markers = document.querySelectorAll(
			'.woocommerce-NoticeGroup-checkout .wc-shipping-ups-notice-marker'
		);

		markers.forEach( function ( marker ) {
			// Remove the `<li>` wrapping our notice (or the notice itself if not in an <li>).
			const li = marker.closest( 'li' ) || marker.closest( '.woocommerce-error, .woocommerce-message, .woocommerce-info' );
			if ( li && li.parentNode ) {
				li.parentNode.removeChild( li );
			}
		} );

		// Clean up parent lists and containers that are now empty of actual notice content.
		document.querySelectorAll( '.woocommerce-NoticeGroup-checkout' ).forEach( function ( group ) {
			group.querySelectorAll( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).forEach( function ( list ) {
				if ( ! list.querySelector( 'li' ) ) {
					list.parentNode.removeChild( list );
				}
			} );

			if ( ! group.querySelector( 'li, .woocommerce-error, .woocommerce-message, .woocommerce-info' ) ) {
				group.parentNode.removeChild( group );
			}
		} );
	};

	// Fires after classic checkout's update_order_review AJAX completes — covers rate changes
	// AND the extensionCartUpdate path invoked by the "Apply suggested address" button below.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_checkout', clearStaleUpsSubmitNotice );
	}

	/**
	 * Resolve the WooCommerce Blocks checkout data store.
	 *
	 * The store is registered only on an actual block checkout — including one rendered via a
	 * theme template (FSE), where the server-side `has_block()`-based `is_wc_block_checkout` flag
	 * is wrong. Returns null on classic checkout even when another block on the page (e.g. the
	 * Mini-Cart block in the theme header) has merely loaded the Blocks bundle, since that does
	 * not register the checkout store. Used both to detect the block checkout and to read the
	 * "use shipping as billing" preference without dereferencing a missing API.
	 *
	 * @return {Object|null} The checkout data store, or null when the block checkout is unavailable.
	 */
	const getBlockCheckoutStore = function () {
		if (
			! window.wc ||
			! window.wc.blocksCheckout ||
			'function' !== typeof window.wc.blocksCheckout.extensionCartUpdate ||
			! window.wc.wcBlocksData ||
			! window.wp ||
			! window.wp.data ||
			'function' !== typeof window.wp.data.select
		) {
			return null;
		}

		const store = window.wp.data.select( window.wc.wcBlocksData.CHECKOUT_STORE_KEY );

		return store && 'function' === typeof store.getUseShippingAsBilling ? store : null;
	};

	// Handle clicking the suggested address.
	document.addEventListener(
		'click',
		function ( e ) {

			if ( ! e.target.classList.contains( 'ups_apply_suggested_address' ) ) {
				return;
			}

			e.preventDefault();

			const button               = e.target;
			const suggestedAddressJSON = button.getAttribute( 'data-suggested_address' );

			if ( ! suggestedAddressJSON ) {
				return;
			}

			// Change the button text to indicate that the address is being applied.
			button.innerHTML = wc_ups_checkout_params.strings.button_apply_address;

			// Resolve the block checkout data store once. It is present only on an actual block
			// checkout (including FSE template-rendered ones the server-side flag misses), and
			// absent on classic checkout even when another block has loaded the Blocks bundle.
			const blockCheckoutStore = getBlockCheckoutStore();

			// Handle classic checkout. The server-side `is_wc_block_checkout` flag relies on
			// has_block(), which misses block checkout rendered via a theme template (FSE), so
			// fall back to a runtime check: when the block checkout store is unavailable we are
			// on classic checkout regardless of what the server detected.
			if ( '1' !== wc_ups_checkout_params.is_wc_block_checkout && null === blockCheckoutStore ) {

				const suggestedAddressObject = JSON.parse( suggestedAddressJSON );

				// The "ship to a different address" checkbox only exists on classic checkout; treat it as
				// unchecked when absent. It is invariant across the loop, so resolve it once up front.
				const shipToDifferentAddressCheckbox = document.getElementById( 'ship-to-different-address-checkbox' );
				const isShipToDifferentAddressChecked = shipToDifferentAddressCheckbox ? shipToDifferentAddressCheckbox.checked : false;

				// Loop through suggested address object and apply the values to the corresponding inputs.
				Object.entries( suggestedAddressObject ).forEach(
					function ( [ key, value ] ) {
						const inputSelector = isShipToDifferentAddressChecked ? 'shipping_' + key : key;
						const input = document.querySelector( '[id$="' + inputSelector + '"]' );

						if ( input ) {
							input.value = value;

							// If the target is a select2 field, trigger the change event.
							if ( input.classList.contains( 'select2-hidden-accessible' ) ) {
								const event = new Event( 'change', { bubbles: true } );
								input.dispatchEvent( event );
							}
						}
					}
				);

				return;
			}

			// Handle block checkout. Bail out safely if the block checkout store is unavailable
			// (e.g. the server flag was "1" but the Blocks runtime is not fully ready), so the
			// handler degrades gracefully instead of throwing on a missing API.
			if ( null === blockCheckoutStore ) {
				return;
			}

			window.wc.blocksCheckout.extensionCartUpdate(
				{
					namespace: wc_ups_checkout_params.store_api_namespace,
					data: {
						action: 'apply_suggested_shipping_address',
						suggested_address: suggestedAddressJSON,
						use_shipping_as_billing: blockCheckoutStore.getUseShippingAsBilling()
					},
				}
			);

		}
	);
} )();