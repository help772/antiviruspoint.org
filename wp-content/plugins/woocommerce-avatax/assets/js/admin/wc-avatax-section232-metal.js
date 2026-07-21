/**
 * Section 232 metal percent rows — validation and add/remove.
 */
( function ( $ ) {
	'use strict';

	function parseNum( raw ) {
		if ( raw === undefined || raw === null ) {
			return NaN;
		}
		const s = String( raw ).trim().replace( ',', '.' );
		if ( s === '' ) {
			return NaN;
		}
		return parseFloat( s );
	}

	function rowTriple( $row ) {
		const v = $row.find( '.wc-avatax-metal-value' ).val();
		const u = $row.find( '.wc-avatax-metal-unit' ).val();
		const c = $row.find( '.wc-avatax-metal-country' ).val();
		return { v, u, c };
	}

	function isRowEmpty( t ) {
		return (
			String( t.v ).trim() === '' &&
			String( t.u ).trim() === '' &&
			String( t.c ).trim() === ''
		);
	}

	function setFieldError( $field, $err, msg ) {
		if ( msg ) {
			$field.addClass( 'wc-avatax-metal-invalid' );
			$err.text( msg );
		} else {
			$field.removeClass( 'wc-avatax-metal-invalid' );
			$err.text( '' );
		}
	}

	function validateRow( $row, msgs ) {
		const t = rowTriple( $row );
		const $val = $row.find( '.wc-avatax-metal-value' );
		const $unit = $row.find( '.wc-avatax-metal-unit' );
		const $country = $row.find( '.wc-avatax-metal-country' );
		const $errV = $row.find( '.wc-avatax-metal-field-value .wc-avatax-metal-field-error' );
		const $errU = $row.find( '.wc-avatax-metal-field-unit .wc-avatax-metal-field-error' );

		setFieldError( $val, $errV, '' );
		setFieldError( $unit, $errU, '' );
		$row.removeClass( 'wc-avatax-metal-row-invalid' );

		if ( isRowEmpty( t ) ) {
			return true;
		}

		let ok = true;
		const n = parseNum( t.v );
		if ( String( t.v ).trim() === '' || isNaN( n ) ) {
			setFieldError( $val, $errV, msgs.valueRequired );
			ok = false;
		} else if ( n <= 0 || n > 100 ) {
			setFieldError( $val, $errV, msgs.valueRange );
			ok = false;
		}

		if ( String( t.u ).trim() === '' ) {
			setFieldError( $unit, $errU, msgs.unitRequired );
			ok = false;
		}

		if ( ! ok ) {
			$row.addClass( 'wc-avatax-metal-row-invalid' );
		}
		return ok;
	}

	function sumValidValues( $wrap ) {
		let sum = 0;
		$wrap.find( '.wc-avatax-metal-row' ).each( function () {
			const t = rowTriple( $( this ) );
			if ( isRowEmpty( t ) ) {
				return;
			}
			const n = parseNum( t.v );
			if ( ! isNaN( n ) && n > 0 && n <= 100 && String( t.u ).trim() !== '' ) {
				sum += n;
			}
		} );
		return sum;
	}

	function validateSum( $wrap, msgs ) {
		const $g = $( '#wc_avatax_metal_global_error' );
		const sum = sumValidValues( $wrap );
		if ( Math.round( sum * 1000000 ) / 1000000 > 100 ) {
			$g.text( msgs.sumAtMost100 ).prop( 'hidden', false );
			return false;
		}
		$g.text( '' ).prop( 'hidden', true );
		return true;
	}

	/** Disable material options already chosen on another row (current row keeps its selection enabled). */
	function refreshMaterialUnitOptions( $wrap ) {
		$wrap.find( '.wc-avatax-metal-unit' ).each( function () {
			const $sel = $( this );
			const myVal = $sel.val() || '';
			$sel.find( 'option' ).each( function () {
				const $opt = $( this );
				const val = $opt.val();
				if ( val === '' ) {
					$opt.prop( 'disabled', false );
					return;
				}
				let takenElsewhere = false;
				$wrap.find( '.wc-avatax-metal-unit' ).not( $sel ).each( function () {
					if ( $( this ).val() === val ) {
						takenElsewhere = true;
						return false;
					}
				} );
				$opt.prop( 'disabled', takenElsewhere && val !== myVal );
			} );
		} );
	}

	function validateDuplicateUnits( $wrap, msgs ) {
		const unitRows = {};
		$wrap.find( '.wc-avatax-metal-row' ).each( function () {
			const $row = $( this );
			const t = rowTriple( $row );
			if ( isRowEmpty( t ) || String( t.u ).trim() === '' ) {
				return;
			}
			if ( ! unitRows[ t.u ] ) {
				unitRows[ t.u ] = [];
			}
			unitRows[ t.u ].push( $row );
		} );

		let ok = true;
		Object.keys( unitRows ).forEach( function ( u ) {
			const rows = unitRows[ u ];
			if ( rows.length < 2 ) {
				return;
			}
			ok = false;
			rows.forEach( function ( $r ) {
				const $unit = $r.find( '.wc-avatax-metal-unit' );
				const $errU = $r.find( '.wc-avatax-metal-field-unit .wc-avatax-metal-field-error' );
				setFieldError( $unit, $errU, msgs.unitDuplicate );
				$r.addClass( 'wc-avatax-metal-row-invalid' );
			} );
		} );
		return ok;
	}

	function validateAll( $wrap, msgs ) {
		let ok = true;
		$wrap.find( '.wc-avatax-metal-row' ).each( function () {
			if ( ! validateRow( $( this ), msgs ) ) {
				ok = false;
			}
		} );
		if ( ! validateDuplicateUnits( $wrap, msgs ) ) {
			ok = false;
		}
		if ( ! validateSum( $wrap, msgs ) ) {
			ok = false;
		}
		return ok;
	}

	/**
	 * Scroll error into view without aggressive centering: smooth + block "center" can chain-scroll
	 * multiple ancestors and break #woocommerce-product-data (overflow:hidden + floats / tab column).
	 */
	function scrollValidationTargetIntoView( el ) {
		if ( ! el || typeof el.scrollIntoView !== 'function' ) {
			return;
		}
		requestAnimationFrame( function () {
			el.scrollIntoView( {
				block: 'nearest',
				inline: 'nearest',
				behavior: 'auto',
			} );
		} );
	}

	function renumberRows( $wrap ) {
		$wrap.find( '.wc-avatax-metal-row' ).each( function ( i ) {
			$( this ).attr( 'data-row-index', i );
		} );
	}

	function cloneRow( $wrap ) {
		const $first = $wrap.find( '.wc-avatax-metal-row' ).first();
		const $clone = $first.clone( true, true );
		$clone.find( '.wc-avatax-metal-value' ).val( '' );
		$clone.find( '.wc-avatax-metal-unit' ).val( '' );
		$clone.find( '.wc-avatax-metal-country' ).val( '' );
		$clone.find( '.wc-avatax-metal-invalid' ).removeClass( 'wc-avatax-metal-invalid' );
		$clone.find( '.wc-avatax-metal-field-error' ).text( '' );
		$clone.removeClass( 'wc-avatax-metal-row-invalid' );
		$wrap.find( '#wc_avatax_metal_rows' ).append( $clone );
		renumberRows( $wrap );
		refreshMaterialUnitOptions( $wrap );
	}

	function section232HiddenByVirtualDownloadable() {
		// Match WooCommerce core: meta-boxes-product.js uses input#_virtual / input#_downloadable (global IDs).
		return (
			$( 'input#_virtual:checked' ).length > 0 ||
			$( 'input#_downloadable:checked' ).length > 0
		);
	}

	function toggleSection232MetalVisibility() {
		const $block = $( 'div.form-field._wc_avatax_section232_metal_field' );
		if ( ! $block.length ) {
			return;
		}
		if ( section232HiddenByVirtualDownloadable() ) {
			$block.hide();
		} else {
			$block.show();
		}
	}

	$( function () {
		// Bind by ID (not only under #woocommerce-product-data): WC moves the type box into .hndle; ensure change still runs.
		$( document.body ).on(
			'change',
			'input#_virtual, input#_downloadable, select#product-type',
			toggleSection232MetalVisibility
		);
		// After WC runs select#product-type .trigger( 'change' ) and show_and_hide_panels().
		$( document.body ).on(
			'woocommerce-product-type-change',
			toggleSection232MetalVisibility
		);
		toggleSection232MetalVisibility();

		const $wrap = $( 'div.form-field._wc_avatax_section232_metal_field' );
		if ( ! $wrap.length || ! window.wcAvaTaxSection232Metal ) {
			return;
		}
		const msgs = window.wcAvaTaxSection232Metal.i18n;

		refreshMaterialUnitOptions( $wrap );
		validateAll( $wrap, msgs );

		$( '#wc_avatax_add_metal_row' ).on( 'click', function () {
			cloneRow( $wrap );
			validateAll( $wrap, msgs );
		} );

		$wrap.on( 'click', '.wc-avatax-metal-remove', function () {
			const $rows = $wrap.find( '.wc-avatax-metal-row' );
			if ( $rows.length <= 1 ) {
				$rows.find( '.wc-avatax-metal-value' ).val( '' );
				$rows.find( '.wc-avatax-metal-unit' ).val( '' );
				$rows.find( '.wc-avatax-metal-country' ).val( '' );
				$rows.find( '.wc-avatax-metal-invalid' ).removeClass( 'wc-avatax-metal-invalid' );
				$rows.find( '.wc-avatax-metal-field-error' ).text( '' );
				$rows.removeClass( 'wc-avatax-metal-row-invalid' );
			} else {
				$( this ).closest( '.wc-avatax-metal-row' ).remove();
				renumberRows( $wrap );
			}
			refreshMaterialUnitOptions( $wrap );
			validateAll( $wrap, msgs );
		} );

		$wrap.on( 'input change', '.wc-avatax-metal-value, .wc-avatax-metal-unit, .wc-avatax-metal-country', function () {
			refreshMaterialUnitOptions( $wrap );
			validateAll( $wrap, msgs );
		} );

		$( 'form#post' ).on( 'submit', function ( e ) {
			if ( ! $wrap.find( '[name="_wc_avatax_section232_save"]' ).length ) {
				return;
			}
			if ( ! $wrap.is( ':visible' ) ) {
				return;
			}
			if ( ! validateAll( $wrap, msgs ) ) {
				e.preventDefault();
				const $top = $wrap.find( '.wc-avatax-metal-row-invalid' ).first();
				if ( $top.length ) {
					scrollValidationTargetIntoView( $top[ 0 ] );
				} else {
					const globalErr = document.getElementById( 'wc_avatax_metal_global_error' );
					if ( globalErr ) {
						scrollValidationTargetIntoView( globalErr );
					}
				}
			}
		} );
	} );
} )( jQuery );
