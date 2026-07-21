import metadata from './block.json';
import { Button, extensionCartUpdate  } from '@woocommerce/blocks-checkout';
import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { Icon } from '@wordpress/icons';
import { info, check, close } from '@wordpress/icons';

// Global import
const { registerCheckoutBlock } = wc.blocksCheckout;
const { useSelect, useDispatch  } = window.wp.data;
const { VALIDATION_STORE_KEY } = window.wc.wcBlocksData;

let persistedBillingValidatedAddress = null;

const Block = ({ cart }) => { 
	const [validatedAddress, _setValidatedAddress] = React.useState(persistedBillingValidatedAddress);
	const setValidatedAddress = ( addr ) => {
		persistedBillingValidatedAddress = addr;
		_setValidatedAddress( addr );
	};
	const validationError = useSelect( ( select ) => {
		const store = select( VALIDATION_STORE_KEY );
		return store.getValidationError( 'btn-address-validation' );
	}, [] );
	const [validatedMessage, setValidatedMessage] = React.useState('');

	const { clearValidationError, setValidationErrors } =
		useDispatch( VALIDATION_STORE_KEY );

		const isCountryApplicableForValidation = useMemo( () => {
			if (wc_avatax_frontend.cart_contains_only_virtual_zero) {
				clearValidationError( 'btn-address-validation' );
				return 'false';
			}
			const currentBillingCountry = cart.billingAddress.country;
			const isCountryAllowed = wc_avatax_frontend.address_validation_countries.includes(currentBillingCountry) ? 'true' : 'false';
			if(isCountryAllowed === 'false')
			{
				clearValidationError( 'btn-address-validation' );
			}
			else
			{
				setValidationErrors( {
					'btn-address-validation': {
						message: 'Address validation required Please validate your billing address before placing your order.',
						hidden: true,
					},
				} );
			}
			return isCountryAllowed;
		}, [ cart.billingAddress.country ] );
		useMemo( () => {
			if (wc_avatax_frontend.cart_contains_only_virtual_zero) {
				return false;
			}
			setValidatedMessage('');
			const isAddressSame = (validatedAddress!=null && 
									cart.billingAddress.country == validatedAddress["country"] &&
									cart.billingAddress.state == validatedAddress["state"] &&
									cart.billingAddress.city == validatedAddress["city"] &&
									cart.billingAddress.postcode == validatedAddress["postcode"] &&
									cart.billingAddress.address_1 == validatedAddress["address_1"] &&
									cart.billingAddress.address_2 == validatedAddress["address_2"])
			if(!isAddressSame)
			{
				setValidationErrors( {
					'btn-address-validation': {
						message: 'Address validation required Please validate your billing address before placing your order.',
						hidden: true,
					},
				} );
			}
			if (isAddressSame) {
				setValidatedMessage('Address Validated');
				clearValidationError( 'btn-address-validation' );
			}
			return isAddressSame;
		}, [ cart.billingAddress, validatedAddress ] );

		const handleAddressValidation = () => {
		clearValidationError( 'btn-address-validation' );
		setValidatedMessage('');

		extensionCartUpdate( {
			namespace: 'address-validation-block',
			data: {
				action : "billing_validate_address",
				address : cart.billingAddress,
			},
		} )
		.then( (res) => {
            setValidatedMessage('Address Validated');
			clearValidationError( 'btn-address-validation' );
			if(res && res.billing_address)
			{
				setValidatedAddress(res.billing_address);
			}
        })
		.catch( ( { message } ) => {
			setValidationErrors( {
				'btn-address-validation': {
					message: message || 'Address validation failed. Please try again.',
					hidden: false,
				},
			} );
		} );
	};
	return (
		<div className={ 'example-fields' }>
			{ isCountryApplicableForValidation === 'true' && (
				<>
					<Button
						className="wc_avatax_validate_address button"
						id="btn-address-validation"
						onClick={ handleAddressValidation }
					>
						Validate Address
					</Button>
					{ validatedMessage !== 'Address Validated' && (
						<div className="wc-avatax-validate-address-mandatory-message" style={{ display: 'flex', alignItems: 'center', marginTop: '8px', fontSize: '0.875em' }}>
							<Icon icon={info} className="wc-avatax-validate-address-mandatory-icon" style={{ width: '24px', height: '24px', margin: '-4px', flexShrink: 0 }} />
							<span className="wc-avatax-validate-address-mandatory-text" style={{ lineHeight: '1.4', marginLeft: '6px' }}>Clicking this button is mandatory before placing your order.</span>
						</div>
					) }
					{ validatedMessage === 'Address Validated' && (
						<div className="wc-avatax-validate-address-success-message" style={{ display: 'flex', alignItems: 'center', marginTop: '12px', padding: '12px 16px', backgroundColor: '#e8f5e9', borderRadius: '4px', border: '1px solid #c8e6c9' }}>
							<span className="wc-avatax-validate-address-success-icon-wrapper" style={{ width: '16px', height: '16px', marginRight: '10px', flexShrink: 0, borderRadius: '50%', border: '2px solid #155724', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
								<Icon icon={check} className="wc-avatax-validate-address-success-icon" style={{ width: '18px', height: '18px', color: '#155724', fill: '#155724' }} />
							</span>
							<span className="wc-avatax-validate-address-success-text" style={{ color: '#155724', fontSize: '0.875em', fontWeight: '500', lineHeight: '1.4' }}>Address validated successfully</span>
						</div>
					) }
					{ validationError && !validationError.hidden && validatedMessage !== 'Address Validated' && (() => {
						// Extract text content from HTML if message contains HTML tags
						// Using DOMParser instead of innerHTML to prevent XSS vulnerabilities
						const extractTextFromHTML = (html) => {
							if (!html) return [];
							try {
								// Use DOMParser to safely parse HTML without executing scripts
								const parser = new DOMParser();
								const doc = parser.parseFromString(html, 'text/html');
								// Find all error divs and extract their text content
								const errorDivs = doc.querySelectorAll('.wc-avatax-address-validation-error, div');
								const messages = [];
								errorDivs.forEach(div => {
									const text = (div.textContent || div.innerText || '').trim();
									if (text) {
										messages.push(text);
									}
								});
								// If no divs found, try to extract all text
								if (messages.length === 0) {
									const allText = (doc.body.textContent || doc.body.innerText || html).trim();
									if (allText) {
										messages.push(allText);
									}
								}
								return messages;
							} catch (e) {
								// Fallback: if parsing fails, return the original HTML as plain text
								return [String(html).trim()];
							}
						};

						const errorMessages = extractTextFromHTML(validationError.message);
						const allErrorText = errorMessages.join(' ');
						const isValidationRequired = allErrorText && allErrorText.includes('Address validation required');

						return (
							<div className="wc-avatax-validate-address-error-message" style={{ display: 'flex', alignItems: 'flex-start', marginTop: '8px', padding: '12px 16px', backgroundColor: '#fff5f5', borderRadius: '4px', border: '1px solid #fecaca' }}>
								<span className="wc-avatax-validate-address-error-icon-wrapper" style={{ width: '16px', height: '16px', marginRight: '10px', flexShrink: 0, borderRadius: '50%', border: '2px solid #721c24', display: 'flex', alignItems: 'center', justifyContent: 'center', marginTop: '2px' }}>
									<Icon icon={close} className="wc-avatax-validate-address-error-icon" style={{ width: '18px', height: '18px', color: '#721c24', fill: '#721c24' }} />
								</span>
								<span className="wc-avatax-validate-address-error-text" style={{ color: '#721c24', fontSize: '0.875em', lineHeight: '1.4', flex: 1 }}>
									{isValidationRequired ? (
										<>
											<span style={{ display: 'block' }}>Address validation required</span>
											<span style={{ display: 'block' }}>Please validate your billing address before placing your order.</span>
										</>
									) : errorMessages.length > 0 ? (
										errorMessages.map((msg, index) => (
											<span key={index} style={{ display: 'block' }}>{msg}</span>
										))
									) : (
										<span>{validationError.message}</span>
									)}
								</span>
							</div>
						);
					})() }
				</>
			) }
		</div>
	);
}

const options = {
	metadata,
	component: Block
};

registerCheckoutBlock( options );