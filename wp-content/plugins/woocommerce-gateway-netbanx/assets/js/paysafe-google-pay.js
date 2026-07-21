/* global paysafe_google_pay_params */
/* global paysafe_checkoutjs_payments_params */
/* global paysafe_layover_params */

(function ($) {
	const WcPaysafeGooglePay = {
		paymentDetails  : 1,
		shippingOptions : false,
		chosenShippingId: false,
		displayItems    : [],

		gatewayId: paysafe_checkoutjs_payments_params.gatewayId || 'paysafe_checkout_payments',

		/**
		 * Loads the form
		 */
		onInit: function () {
			onGooglePayLoaded();

			$(document.body).on('updated_checkout', WcPaysafeGooglePay.onUpdateCheckout);
		},

		/**
		 * Update the button in case the user adds a subscription.
		 * We don't support subs with Google Pay at the moment
		 *
		 * @param event
		 * @param data
		 */
		onUpdateCheckout: function (event, data) {
			if (data.fragments.hasSubscription) {
				$('#' + WcPaysafeGooglePay.gatewayId + '_wrapper').hide();
				$('#' + WcPaysafeGooglePay.gatewayId + '-request-button-separator').hide();
			} else {
				$('#' + WcPaysafeGooglePay.gatewayId + '_wrapper').show();
				$('#' + WcPaysafeGooglePay.gatewayId + '-request-button-separator').show();
			}
		},

		/**
		 * Gets the cart details so we can quickly load the payment window
		 */
		getCartDetails: function () {
			var data = {
				from_cart: 1,
				security : paysafe_google_pay_params.nonce.cart_details,
			};

			if (window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
				data.from_cart = 0;
				data.order_id = paysafe_layover_params.options.orderId;
			}

			$.ajax({
				type   : 'POST',
				data   : data,
				url    : WcPaysafeGooglePay.getAjaxURL('get_cart_details'),
				success: function (response) {

					WcPaysafeGooglePay.consoleLog('getCartDetails: response: ', response);

					if (response.order_data.displayItems) {
						WcPaysafeGooglePay.displayItems = response.order_data.displayItems;
					}

					if (response.shippingOptions) {
						WcPaysafeGooglePay.shippingOptions = response.shippingOptions;
					}

					WcPaysafeGooglePay.paymentDetails = response;
				},
			});
		},

		/**
		 * Retrieves the shipping options from the DB based on the shipping address
		 * @param shippingAddress
		 * @return {Promise<{defaultSelectedOptionId: *, shippingOptions: *[]}>}
		 */
		getShippingOptions: async function (shippingAddress) {

			WcPaysafeGooglePay.consoleLog('getShippingOptions: shippingAddress: ', shippingAddress);
			WcPaysafeGooglePay.consoleLog('getShippingOptions: paymentDetails: ', WcPaysafeGooglePay.paymentDetails);

			let options = await WcPaysafeGooglePay.updateShippingOptions(WcPaysafeGooglePay.paymentDetails, shippingAddress);

			WcPaysafeGooglePay.consoleLog('getShippingOptions: options: ', options);

			let shippingOptions = WcPaysafeGooglePay.formatShippingOptions(options);
			WcPaysafeGooglePay.chosenShippingId = options.chosen_shipping_option;

			return shippingOptions;
		},

		/**
		 * Update shipping options.
		 *
		 * @param {Object}         details Payment details.
		 * @param {PaymentAddress} address Shipping address.
		 * @return Promise
		 */
		updateShippingOptions: function (details, address) {
			return new Promise(function (resolve, reject) {
				var data = {
					security            : paysafe_google_pay_params.nonce.get_shipping_options,
					country             : address.countryCode,
					state               : address.region,
					postcode            : address.postalCode,
					city                : address.locality,
					address             : typeof address.addressLine === 'undefined' ? '' : address.addressLine[0],
					address_2           : typeof address.addressLine === 'undefined' ? '' : address.addressLine[1],
					payment_request_type: 'googlepay',
					is_product_page     : paysafe_google_pay_params.is_product_page,
				};

				WcPaysafeGooglePay.consoleLog('updateShippingOptions: data: ', data);

				$.ajax({
					type   : 'POST',
					data   : data,
					url    : WcPaysafeGooglePay.getAjaxURL('get_shipping_options'),
					success: function (response) {
						WcPaysafeGooglePay.consoleLog('updateShippingOptions: response: ', response);

						WcPaysafeGooglePay.displayItems = response.displayItems;
						WcPaysafeGooglePay.total = response.total;
						WcPaysafeGooglePay.shippingOptions = response.shipping_options;

						resolve(response);
					},
					error  : function (jqXHR, textStatus, errorThrown) {
						reject({jqXHR, textStatus, errorThrown});
					},
				});
			});
		},

		/**
		 * IMPORTANT:
		 * TODO: We need to update the chosen shipping method when the customer changes the shipping method
		 * 		in the Google Pay window
		 *
		 * Updates the shipping price and the total based on the shipping option.
		 *
		 * @param {String}   shippingOptionId User's preferred shipping option to use for shipping price calculations.
		 */
		updateShippingDetails: function (shippingOptionId) {
			return new Promise(function (resolve, reject) {
				var data = {
					security            : paysafe_google_pay_params.nonce.update_shipping_method,
					shipping_method     : [shippingOptionId],
					payment_request_type: 'googlepay',
					is_product_page     : paysafe_google_pay_params.is_product_page,
				};

				$.ajax({
					type   : 'POST',
					data   : data,
					url    : WcPaysafeGooglePay.getAjaxURL('update_shipping_method'),
					success: function (response) {
						resolve(response);
					},
					error  : function (jqXHR, textStatus, errorThrown) {
						reject({jqXHR, textStatus, errorThrown});
					},
				});
			});

		},

		/**
		 * Creates an order based on the Google Pay data
		 * @param paymentData
		 * @return {Promise<unknown>}
		 */
		createOrder: function (paymentData) {
			return new Promise(async function (resolve, reject) {
				var data = WcPaysafeGooglePay.getOrderData(paymentData);

				WcPaysafeGooglePay.consoleLog('createOrder: request data: ', data);

				if (window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
					WcPaysafeGooglePay.block(WcPaysafeGooglePay.getFormElement());
				}

				await $.ajax({
					type    : 'POST',
					data    : data,
					dataType: 'json',
					url     : WcPaysafeGooglePay.getAjaxURL(window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage() ? 'pay_for_order' : 'create_order'),
					success : function (response) {
						WcPaysafeGooglePay.consoleLog('createOrder: response: ', response);

						if ('success' === response.result) {
							return resolve(response);
						}

						return reject(response);
					},
					error   : function (jqXHR, textStatus, errorThrown) {
						return reject({jqXHR, textStatus, errorThrown});
					},
				});
			});
		},

		/**
		 * Get order data.
		 *
		 * @since 3.1.0
		 * @version 4.0.0
		 * @param {Object} paymentData Payment Response instance.
		 *
		 * @return {Object}
		 */
		getOrderData: function (paymentData) {
			var data = {
				_wpnonce               : paysafe_google_pay_params.nonce.checkout,
				payment_method         : 'paysafe_checkout_payments',
				google_pay_payment_data: paymentData,
				payment_request_type   : 'googlepay',
			};

			if (window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
				data.orderId = paysafe_layover_params.options.orderId;
			} else if (window.wcPaysafe.wcPaysafeCheckout.isCheckoutPage()) {
				var billing = paymentData.paymentMethodData.info.billingAddress;
				var email = paymentData.email;
				var shipping = paymentData.shippingAddress;
				var name = paymentData.shippingAddress.name;

				data.billing_first_name = name?.split(' ')?.slice(0, 1)?.join(' ') ?? '';
				data.billing_last_name = name?.split(' ')?.slice(1)?.join(' ') || '-';
				data.billing_company = '';
				data.billing_email = null !== email ? email : '';
				data.billing_phone = null !== billing && null !== billing.phoneNumber ? billing.phoneNumber : '';
				data.billing_country = null !== billing ? billing.countryCode : '';
				data.billing_address_1 = null !== billing ? billing.address1 : '';
				data.billing_address_2 = null !== billing ? billing.address2 + ' ' + billing.address3 : '';
				data.billing_city = null !== billing ? billing.locality : '';
				data.billing_state = null !== billing && billing.administrativeArea ? billing.administrativeArea : '';
				data.billing_postcode = null !== billing ? billing.postalCode : '';
				data.shipping_first_name = '';
				data.shipping_last_name = '';
				data.shipping_company = '';
				data.shipping_country = '';
				data.shipping_address_1 = '';
				data.shipping_address_2 = '';
				data.shipping_city = '';
				data.shipping_state = '';
				data.shipping_postcode = '';
				data.shipping_method = [paymentData.shippingOptionData.id];
				data.order_comments = '';
				data.ship_to_different_address = 1;
				data.terms = 1;

				if (shipping) {
					data.shipping_first_name = shipping.name.split(' ').slice(0, 1).join(' ');
					data.shipping_last_name = shipping.name.split(' ').slice(1).join(' ');
					// TODO: Don't have this
					data.shipping_company = '';

					data.shipping_country = shipping.countryCode;
					data.shipping_address_1 = null !== shipping.address1 ? shipping.address1 : '';
					data.shipping_address_2 = null !== shipping.address2 ? shipping.address2 + ' ' + shipping.address3 : '';
					data.shipping_city = shipping.locality;
					data.shipping_state = shipping.administrativeArea ? shipping.administrativeArea : '';
					data.shipping_postcode = shipping.postalCode;
				}

				data = WcPaysafeGooglePay.getRequiredFieldDataFromCheckoutForm(data);
			}

			return data;
		},

		/**
		 * Get required field values from the checkout form if they are filled and add to the order data.
		 *
		 * @param {Object} data Order data.
		 *
		 * @return {Object}
		 */
		getRequiredFieldDataFromCheckoutForm: function (data) {
			const requiredfields = $('form.checkout').find('.validate-required');

			if (requiredfields.length) {
				requiredfields.each(function () {
					const field = $(this).find(':input');
					const name = field.attr('name');

					let value = '';
					if (field.attr('type') === 'checkbox') {
						value = field.is(':checked');
					} else {
						value = field.val();
					}

					if (value && name) {
						if (!data[name]) {
							data[name] = value;
						}

						// if shipping same as billing is selected, copy the billing field to shipping field.
						const shipToDiffAddress = $('#ship-to-different-address').find('input').is(':checked');
						if (!shipToDiffAddress) {
							var shippingFieldName = name.replace('billing_', 'shipping_');
							if (!data[shippingFieldName] && data[name]) {
								data[shippingFieldName] = data[name];
							}
						}
					}
				});
			}

			return data;
		},

		getAjaxURL: function (endpoint) {
			return paysafe_checkoutjs_payments_params.ajaxUrl
				.toString()
				.replace('%%endpoint%%', 'paysafe_payments_' + endpoint);
		},

		formatShippingOptions: function (options) {
			WcPaysafeGooglePay.shippingCosts = {};

			let optionsMapped = {
				defaultSelectedOptionId: options.chosen_shipping_option,
				shippingOptions        : [],
			}

			optionsMapped.shippingOptions = options.shipping_options.map(function (value) {
				WcPaysafeGooglePay.shippingCosts[value.id] = value.amount;
				let label = value.label
				if (0 < value.amount) {
					label = paysafe_google_pay_params.currencySymbol + value.amount + ': ' + value.label;
				}

				return {
					"id"         : value.id,
					"label"      : label,
					"description": value.detail,
				};
			});

			WcPaysafeGooglePay.consoleLog('formatShippingOptions: ', optionsMapped);

			return optionsMapped;
		},

		getFormElement: function () {
			var form = $('#paysafe_checkout_payment_form');
			if (window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
				form = $('#order_review');
			} else if (WcPaysafeGooglePay.isCheckoutPage()) {
				form = $('form.woocommerce-checkout');
			}

			return form;
		},

		isJsonString: function (str) {
			try {
				JSON.parse(str);
			} catch (e) {
				return false;
			}
			return true;
		},

		block: function (el, message) {
			if (WcPaysafeGooglePay.isUndefined(el)) {
				return false;
			}

			if (!WcPaysafeGooglePay.isFunction(el, 'block')) {
				return;
			}

			message = !WcPaysafeGooglePay.isUndefined(message) ? '<p>' + message + '</p>' : '';

			el.block({
				message   : message,
				overlayCSS: {
					background: '#fff',
					opacity   : 0.6,
				},
			});
		},

		unblock: function (el) {
			if (WcPaysafeGooglePay.isUndefined(el)) {
				return false;
			}

			if (!WcPaysafeGooglePay.isFunction(el, 'block')) {
				return;
			}

			el.unblock();
		},

		isCheckoutPage: function () {
			return !WcPaysafeGooglePay.isUndefined(paysafe_checkoutjs_payments_params.isCheckoutPage) && '1' === paysafe_checkoutjs_payments_params.isCheckoutPage;
		},

		isTestmode: function () {
			return !WcPaysafeGooglePay.isUndefined(paysafe_google_pay_params.isTestmode) && '1' === paysafe_google_pay_params.isTestmode;
		},

		allowedCountryCodes: function () {
			return !WcPaysafeGooglePay.isUndefined(paysafe_google_pay_params.allowedCountryCodes) && paysafe_google_pay_params.allowedCountryCodes;
		},

		isMobile: function () {
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true;
			}

			return false;
		},

		/**
		 * @param el
		 * @param val
		 * @returns {*|boolean|boolean}
		 */
		isFunction: function (el, val) {
			return typeof el[val] !== "undefined" && typeof el[val] === "function";
		},

		/**
		 * Log to console
		 * @param message
		 * @param object
		 */
		consoleLog: function (message, object) {
			object = object || {};

			if (WcPaysafeGooglePay.isScriptDev()) {
				console.log(message, object);
			}
		},

		/**
		 * Is the script in development mode
		 * @returns {boolean}
		 */
		isScriptDev: function () {
			return !WcPaysafeGooglePay.isUndefined(paysafe_checkoutjs_payments_params.scriptDev) && '1' === paysafe_checkoutjs_payments_params.scriptDev;
		},

		isUndefined: function (e) {
			return 'undefined' === typeof (e);
		},

	}

	$(document).ready(function () {
		WcPaysafeGooglePay.onInit();
	});

	/**
	 * Define the version of the Google Pay API referenced when creating your
	 * configuration
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|apiVersion in PaymentDataRequest}
	 */
	const baseRequest = paysafe_google_pay_params.baseApiVersion;

	/**
	 * Card networks supported by your site and your gateway
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
	 * @todo confirm card networks supported by your site and gateway
	 */
	const allowedCardNetworks = paysafe_google_pay_params.allowedCardNetworks;

	/**
	 * Card authentication methods supported by your site and your gateway
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
	 * @todo confirm your processor supports Android device tokens for your
	 * supported card networks
	 */
	const allowedCardAuthMethods = paysafe_google_pay_params.allowedCardAuthMethods;

	/**
	 * Identify your gateway and your site's gateway merchant identifier
	 *
	 * The Google Pay API response will return an encrypted payment method capable
	 * of being charged by a supported gateway after payer authorization
	 *
	 * @todo check with your gateway on the parameters to pass
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#gateway|PaymentMethodTokenizationSpecification}
	 */
	const tokenizationSpecification = {
		type      : 'PAYMENT_GATEWAY',
		parameters: {
			'gateway'          : 'paysafe',
			'gatewayMerchantId': paysafe_google_pay_params.gatewayMerchantId,
		},
	};

	/**
	 * Describe your site's support for the CARD payment method and its required
	 * fields
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
	 */
	const baseCardPaymentMethod = {
		type      : 'CARD',
		parameters: {
			allowedAuthMethods      : allowedCardAuthMethods,
			allowedCardNetworks     : allowedCardNetworks,
			billingAddressRequired  : true,
			billingAddressParameters: {
				format             : 'FULL',
				phoneNumberRequired: true,
			},
		},
	};

	/**
	 * Describe your site's support for the CARD payment method including optional
	 * fields
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
	 */
	const cardPaymentMethod = Object.assign(
		{},
		baseCardPaymentMethod,
		{
			tokenizationSpecification: tokenizationSpecification,
		},
	);

	/**
	 * An initialized google.payments.api.PaymentsClient object or null if not yet set
	 *
	 * @see {@link getGooglePaymentsClient}
	 */
	let paymentsClient = null;

	/**
	 * Configure your site's support for payment methods supported by the Google Pay
	 * API.
	 *
	 * Each member of allowedPaymentMethods should contain only the required fields,
	 * allowing reuse of this base request when determining a viewer's ability
	 * to pay and later requesting a supported payment method
	 *
	 * @returns {object} Google Pay API version, payment methods supported by the site
	 */
	function getGoogleIsReadyToPayRequest() {
		return Object.assign(
			{},
			baseRequest,
			{
				allowedPaymentMethods: [baseCardPaymentMethod],
			},
		);
	}

	/**
	 * Configure support for the Google Pay API
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|PaymentDataRequest}
	 * @returns {object} PaymentDataRequest fields
	 */
	function getGooglePaymentDataRequest() {
		const paymentDataRequest = Object.assign({}, baseRequest);
		paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
		paymentDataRequest.merchantInfo = {
			merchantName: '',
		};

		if (paysafe_google_pay_params.merchantName) {
			paymentDataRequest.merchantInfo.merchantName = paysafe_google_pay_params.merchantName;
		}
		if (paysafe_google_pay_params.merchantId) {
			paymentDataRequest.merchantInfo.merchantId = paysafe_google_pay_params.merchantId;
		}

		if (!WcPaysafeGooglePay.isTestmode() && !window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
			paymentDataRequest.callbackIntents = ["SHIPPING_ADDRESS", "SHIPPING_OPTION", "PAYMENT_AUTHORIZATION"];
		}

		paymentDataRequest.emailRequired = true;

		if (!window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
			paymentDataRequest.shippingAddressRequired = true;
			paymentDataRequest.shippingAddressParameters = {
				phoneNumberRequired: true,
			};

			if (WcPaysafeGooglePay.allowedCountryCodes()) {
				paymentDataRequest.shippingAddressParameters.allowedCountryCodes = WcPaysafeGooglePay.allowedCountryCodes();
			}

			paymentDataRequest.shippingOptionRequired = true;
		}

		if (WcPaysafeGooglePay.isTestmode() && !window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
			paymentDataRequest.shippingOptionParameters = getGoogleDefaultShippingOptions();
		}

		WcPaysafeGooglePay.consoleLog('getGooglePaymentDataRequest: ', paymentDataRequest)

		return paymentDataRequest;
	}

	function getGoogleDefaultShippingOptions() {
		return {
			defaultSelectedOptionId: "free_shipping:6",
			shippingOptions        : [
				{
					"id"         : "free_shipping:6",
					"label"      : "Free: Standard shipping",
					"description": "Free Shipping delivered in 5 business days.",
				},
			],
		};
	}

	/**
	 * Return an active PaymentsClient or initialize
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/client#PaymentsClient|PaymentsClient constructor}
	 * @returns {google.payments.api.PaymentsClient} Google Pay API client
	 */
	function getGooglePaymentsClient() {
		if (paymentsClient === null) {
			let clientData = {
				environment : WcPaysafeGooglePay.isTestmode() ? "TEST" : "PRODUCTION",
				merchantInfo: {
					merchantName: "Watch Shop",
					merchantId  : "03604146959015963672",
				},
			};

			if (!WcPaysafeGooglePay.isTestmode() && !window.wcPaysafe.wcPaysafeCheckout.isPayForOrderPage()) {
				clientData.paymentDataCallbacks = {};
				clientData.paymentDataCallbacks.onPaymentAuthorized = onPaymentAuthorized;
				clientData.paymentDataCallbacks.onPaymentDataChanged = onPaymentDataChanged;
			}

			if (paysafe_google_pay_params.merchantName) {
				clientData.merchantInfo.merchantName = paysafe_google_pay_params.merchantName;
			}
			if (paysafe_google_pay_params.merchantId) {
				clientData.merchantInfo.merchantId = paysafe_google_pay_params.merchantId;
			}

			paymentsClient = new google.payments.api.PaymentsClient(clientData);
		}

		return paymentsClient;
	}


	function onPaymentAuthorized(paymentData) {
		return new Promise(function (resolve, reject) {

			// handle the response
			processPayment(paymentData)
				.then(function (response) {
					resolve({transactionState: 'SUCCESS'});

					if (response.redirect) {
						WcPaysafeGooglePay.block(WcPaysafeGooglePay.getFormElement());
						window.location.href = response.redirect;
					}
				})
				.catch(function (response) {
					WcPaysafeGooglePay.consoleLog('onPaymentAuthorized: catch: ', response);

					// Unblock the Checkout in case we had it blocked
					WcPaysafeGooglePay.unblock(WcPaysafeGooglePay.getFormElement());

					let messages = '';
					if (response.messages) {
						messages = response.messages;
					} else {
						var responseText = JSON.parse(response.jqXHR.responseText);
						messages = responseText.data ? responseText.data : response.errorThrown;
					}

					messages = messages.replace(/(<([^>]+)>)/gi, "");

					if (WcPaysafeGooglePay.isTestmode()) {
						// Display the error
						$(document.body).trigger('paysafeErrored', new paysafeError(messages, '', true));
					} else {
						resolve({
							transactionState: 'ERROR',
							error           : {
								intent : 'PAYMENT_AUTHORIZATION',
								message: messages,
								reason : 'PAYMENT_DATA_INVALID',
							},
						});
					}
				});
		});
	}

	/**
	 * Handles dynamic buy flow shipping address and shipping options callback intents.
	 *
	 * @param {object} itermediatePaymentData response from Google Pay API a shipping address or shipping option is
	 *     selected in the payment sheet.
	 * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#IntermediatePaymentData|IntermediatePaymentData object reference}
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentDataRequestUpdate|PaymentDataRequestUpdate}
	 * @returns Promise<{object}> Promise of PaymentDataRequestUpdate object to update the payment sheet.
	 */
	function onPaymentDataChanged(intermediatePaymentData) {
		return new Promise(async function (resolve, reject) {

			let shippingAddress = intermediatePaymentData.shippingAddress;
			let shippingOptionData = intermediatePaymentData.shippingOptionData;
			let paymentDataRequestUpdate = {};

			if (intermediatePaymentData.callbackTrigger == "INITIALIZE" || intermediatePaymentData.callbackTrigger == "SHIPPING_ADDRESS") {

				WcPaysafeGooglePay.consoleLog('onPaymentDataChanged: shippingOptions: ', intermediatePaymentData);
				let shippingOptions = await WcPaysafeGooglePay.getShippingOptions(shippingAddress);
				WcPaysafeGooglePay.consoleLog('Shipping options: ', shippingOptions);

				paymentDataRequestUpdate.newShippingOptionParameters = shippingOptions;

				let selectedShippingOptionId = paymentDataRequestUpdate.newShippingOptionParameters.defaultSelectedOptionId;
				paymentDataRequestUpdate.newTransactionInfo = calculateNewTransactionInfo(selectedShippingOptionId);
			} else if (intermediatePaymentData.callbackTrigger == "SHIPPING_OPTION") {
				await WcPaysafeGooglePay.updateShippingDetails(shippingOptionData.id);

				paymentDataRequestUpdate.newTransactionInfo = calculateNewTransactionInfo(shippingOptionData.id);
			}

			resolve(paymentDataRequestUpdate);
		});
	}


	/**
	 * Helper function to create a new TransactionInfo object.

	 * @param string shippingOptionId respresenting the selected shipping option in the payment sheet.
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#TransactionInfo|TransactionInfo}
	 * @returns {object} transaction info, suitable for use as transactionInfo property of PaymentDataRequest
	 */
	function calculateNewTransactionInfo(shippingOptionId) {
		let newTransactionInfo = getGoogleTransactionInfo();

		let shippingCost = getShippingCosts()[shippingOptionId];

		let displayItems = newTransactionInfo.displayItems.map(function (value) {
			if (paysafe_google_pay_params.il8n.shippingLabel === value.label) {
				value.price = shippingCost;
			}

			return value;
		});

		newTransactionInfo.displayItems = displayItems;

		let totalPrice = 0.00;
		newTransactionInfo.displayItems.forEach(displayItem => totalPrice += parseFloat(displayItem.price));
		newTransactionInfo.totalPrice = Number.parseFloat(totalPrice).toFixed(2);

		let asd = newTransactionInfo.displayItems;

		console.log('newTransactionInfo.displayItems: ', asd);
		console.log('newTransactionInfo.totalPrice: ', newTransactionInfo.totalPrice);

		return newTransactionInfo;
	}

	/**
	 * Initialize Google PaymentsClient after Google-hosted JavaScript has loaded
	 *
	 * Display a Google Pay payment button after confirmation of the viewer's
	 * ability to pay.
	 */
	function onGooglePayLoaded() {

		if (!document.getElementById(WcPaysafeGooglePay.gatewayId + '-button')) {
			return;
		}

		const paymentsClient = getGooglePaymentsClient();
		paymentsClient.isReadyToPay(getGoogleIsReadyToPayRequest())
			.then(function (response) {
				if (response.result) {
					addGooglePayButton();

					WcPaysafeGooglePay.getCartDetails();
				}
			})
			.catch(function (err) {
				// show error in developer console for debugging
				console.error(err);
			});
	}

	/**
	 * Add a Google Pay purchase button alongside an existing checkout button
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#ButtonOptions|Button options}
	 * @see {@link https://developers.google.com/pay/api/web/guides/brand-guidelines|Google Pay brand guidelines}
	 */
	function addGooglePayButton() {
		const paymentsClient = getGooglePaymentsClient();
		const button = paymentsClient.createButton({onClick: onGooglePaymentButtonClicked});
		document.getElementById(WcPaysafeGooglePay.gatewayId + '-button').appendChild(button);
		$('#' + WcPaysafeGooglePay.gatewayId + '_wrapper').show();
		$('#' + WcPaysafeGooglePay.gatewayId + '-request-button-separator').show();
	}

	/**
	 * Provide Google Pay API with a payment amount, currency, and amount status
	 *
	 * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#TransactionInfo|TransactionInfo}
	 * @returns {object} transaction info, suitable for use as transactionInfo property of PaymentDataRequest
	 */
	function getGoogleTransactionInfo() {
		let items = {
			displayItems    : WcPaysafeGooglePay.displayItems,
			countryCode     : paysafe_google_pay_params.countryCode,
			currencyCode    : paysafe_google_pay_params.currency,
			totalPriceStatus: "FINAL",
			totalPrice      : "1.00",
			totalPriceLabel : paysafe_google_pay_params.il8n.total,
		};

		let totalPrice = 0.00;
		items.displayItems.forEach(displayItem => totalPrice += parseFloat(displayItem.price));
		items.totalPrice = Number.parseFloat(totalPrice).toFixed(2);

		return items;
	}

	/**
	 * Provide a key value store for shippping options.
	 */
	function getShippingCosts() {
		if (!WcPaysafeGooglePay.shippingCosts) {
			WcPaysafeGooglePay.shippingCosts = {
				"free_shipping:6": "0.00",
			};
		}

		return WcPaysafeGooglePay.shippingCosts;
	}

	/**
	 * Show Google Pay payment sheet when Google Pay payment button is clicked
	 */
	function onGooglePaymentButtonClicked() {
		const paymentDataRequest = getGooglePaymentDataRequest();
		paymentDataRequest.transactionInfo = getGoogleTransactionInfo();

		const paymentsClient = getGooglePaymentsClient();
		if (WcPaysafeGooglePay.isTestmode()) {
			paymentsClient.loadPaymentData(paymentDataRequest)
				.then(function (paymentData) {
					// Block the checkout because we just closed the Google Pay window
					WcPaysafeGooglePay.block(WcPaysafeGooglePay.getFormElement());
					onPaymentAuthorized(paymentData);
				});
		} else {
			paymentsClient.loadPaymentData(paymentDataRequest);
		}
	}

	/**
	 * Process payment data returned by the Google Pay API
	 *
	 * @param {object} paymentData response from Google Pay API after user approves payment
	 * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentData|PaymentData object reference}
	 */
	function processPayment(paymentData) {
		WcPaysafeGooglePay.consoleLog('paymentData: ', paymentData);

		return WcPaysafeGooglePay.createOrder(paymentData);
	}

	var paysafeError = function (message, element, global) {
		this.message = message;
		this.appendTo = element || false;
		this.global = global || true;
	}
})(jQuery);
