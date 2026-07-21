jQuery( document ).ready( function ( e ) {
	function a() {
		e( '.advanced-ads-selling-setup-ad-type:checked' ).each(
			function ( a, d ) {
				const c = e( d ).val(),
					l = e( d ).parents(
						'.advanced-ads-selling-setup-ad-details'
					);
				'image' === c
					? ( l
							.find(
								'.advanced-ads-selling-setup-ad-details-upload-label, .advanced-ads-selling-setup-ad-details-image-upload, .advanced-ads-selling-setup-ad-details-url, .advanced-ads-selling-setup-ad-details-url-input'
							)
							.show(),
					  l
							.find(
								'.advanced-ads-selling-setup-ad-details-html-label, .advanced-ads-selling-setup-ad-details-html'
							)
							.hide() )
					: ( l
							.find(
								'.advanced-ads-selling-setup-ad-details-html-label, .advanced-ads-selling-setup-ad-details-html'
							)
							.show(),
					  l
							.find(
								'.advanced-ads-selling-setup-ad-details-upload-label, .advanced-ads-selling-setup-ad-details-image-upload, .advanced-ads-selling-setup-ad-details-url, .advanced-ads-selling-setup-ad-details-url-input'
							)
							.hide() );
			}
		);
	}
	a(),
		e( '.advanced-ads-selling-setup-ad-type' ).click( a ),
		e( '.advanced-ads-selling-setup-ad-details-submit' ).on(
			'click',
			function ( e ) {
				document
					.querySelectorAll(
						'.advanced-ads-selling-setup-ad-details-upload-input'
					)
					.forEach( function ( a ) {
						if ( null === a.offsetParent ) {
							return;
						}
						const d = a.files[ 0 ];
						return d && d.size > AdvancedAdSelling.maxFileSize
							? ( e.preventDefault(),
							  alert( 'File size too large' ),
							  ! 1 )
							: void 0;
					} );
			}
		);
	const d = AdvancedAdSelling.product_prices || [];
	jQuery( '#advads-selling-option-ad-price input' ).on(
		'change',
		function () {
			let e = 0;
			if (
				jQuery( '#advads-selling-option-ad-price input:checked' ).length
			) {
				const a =
					jQuery( '#advads-selling-option-ad-price input' ).length -
					jQuery( this ).parents( 'li' ).index() -
					1;
				e = parseFloat( d[ a ].price );
			}
			( e = e.toFixed( 2 ) ),
				( e = e.toString() ),
				( e = e.replace(
					'.',
					AdvancedAdSelling.woocommerce_price_decimal_sep
				) );
			const a = jQuery( '.price .woocommerce-ad-price' ).find(
				'.woocommerce-Price-currencySymbol'
			);
			switch (
				( jQuery( '.price .woocommerce-ad-price' ).html( a ),
				AdvancedAdSelling.woocommerce_currency_position )
			) {
				case 'right':
					jQuery(
						'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
					).before( e );
					break;
				case 'right_space':
					jQuery(
						'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
					).before( e + '&nbsp;' );
					break;
				case 'left_space':
					jQuery(
						'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
					).after( '&nbsp;' + e );
					break;
				default:
					jQuery(
						'.price .woocommerce-ad-price .woocommerce-Price-currencySymbol'
					).after( e );
			}
		}
	);
} );
