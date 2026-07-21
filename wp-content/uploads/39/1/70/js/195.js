/* global AdvancedAdSelling */
import jQuery from 'jquery';

jQuery( document ).ready( function ( $ ) {
	function advadsSellingToggleDetailsSection() {
		// iterate through all ads.
		$( '.advanced-ads-selling-setup-ad-type:checked' ).each(
			function ( key, el ) {
				// get active sections.
				const adType = $( el ).val();

				// get parent container.
				const parentContainer = $( el ).parents(
					'.advanced-ads-selling-setup-ad-details'
				);

				// highlight correct form fields.
				if ( 'image' === adType ) {
					parentContainer
						.find(
							'.advanced-ads-selling-setup-ad-details-upload-label, .advanced-ads-selling-setup-ad-details-image-upload, .advanced-ads-selling-setup-ad-details-url, .advanced-ads-selling-setup-ad-details-url-input'
						)
						.show();
					parentContainer
						.find(
							'.advanced-ads-selling-setup-ad-details-html-label, .advanced-ads-selling-setup-ad-details-html'
						)
						.hide();
				} else {
					// HTML is fallback.
					parentContainer
						.find(
							'.advanced-ads-selling-setup-ad-details-html-label, .advanced-ads-selling-setup-ad-details-html'
						)
						.show();
					parentContainer
						.find(
							'.advanced-ads-selling-setup-ad-details-upload-label, .advanced-ads-selling-setup-ad-details-image-upload, .advanced-ads-selling-setup-ad-details-url, .advanced-ads-selling-setup-ad-details-url-input'
						)
						.hide();
				}
			}
		);
	}

	advadsSellingToggleDetailsSection();
	// trigger, when selection is changed.
	$( '.advanced-ads-selling-setup-ad-type' ).click(
		advadsSellingToggleDetailsSection
	);
	// submit frontend ad form.
	$( '.advanced-ads-selling-setup-ad-details-submit' ).on(
		'click',
		function ( e ) {
			const uploadforms = document.querySelectorAll(
				'.advanced-ads-selling-setup-ad-details-upload-input'
			);
			uploadforms.forEach( function ( el ) {
				// skip check if the upload form is not visible
				if ( el.offsetParent === null ) {
					return;
				}

				const file = el.files[ 0 ];
				if ( file && file.size > AdvancedAdSelling.maxFileSize ) {
					// default is 1,000,000 bytes = 1 MB
					// Prevent default and display error
					e.preventDefault();
					// eslint-disable-next-line no-alert
					alert( 'File size too large' );
					return false;
				}
			} );
		}
	);

	// update prices dynamically.
	const priceArray = AdvancedAdSelling.product_prices || [];
	jQuery( '#advads-selling-option-ad-price input' ).on(
		'change',
		advadsSellingUpdatePrice
	);

	function advadsSellingUpdatePrice() {
		// when ad expiry is given.
		let totalPrice = 0;
		if (
			jQuery( '#advads-selling-option-ad-price input:checked' ).length
		) {
			const priceIndex =
				jQuery( '#advads-selling-option-ad-price input' ).length -
				jQuery( this ).parents( 'li' ).index() -
				1; // needed to be reversed.
			totalPrice = parseFloat( priceArray[ priceIndex ].price );
		}

		totalPrice = totalPrice.toFixed( 2 );
		totalPrice = totalPrice.toString();
		totalPrice = totalPrice.replace(
			'.',
			AdvancedAdSelling.woocommerce_price_decimal_sep
		);

		// write price into frontend.
		const selector = jQuery( '.price .woocommerce-ad-price' );
		const noRemove = selector.find( '.woocommerce-Price-currencySymbol' );
		jQuery( '.price .woocommerce-ad-price' ).html( noRemove );

		// place new price based on currency symbol position.
		switch ( AdvancedAdSelling.woocommerce_currency_position ) {
			case 'right':
				jQuery(
					'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
				).before( totalPrice );
				break;
			case 'right_space':
				jQuery(
					'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
				).before( totalPrice + '&nbsp;' );
				break;
			case 'left_space':
				jQuery(
					'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
				).after( '&nbsp;' + totalPrice );
				break;
			default:
				jQuery(
					'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
				).after( totalPrice );
		}
	}
} );
