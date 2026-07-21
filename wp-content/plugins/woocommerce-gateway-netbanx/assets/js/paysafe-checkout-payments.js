/* global paysafe_checkoutjs_payments_params */
/* global paysafe_layover_params */
/* global wc_checkout_params */
(function ($) {
	$(document).ready(function () {
		wcPaysafeCheckout.onInit();
		if (wcPaysafeCheckout.isCheckoutPage()) {
			checkoutBypass.init();
		}

		// Submit the form directly when on the Pay page.
		// This is for the legacy payment process, but still good to have just in case
		if (wcPaysafeCheckout.isCheckoutPayPage() && !wcPaysafeCheckout.isChangePaymentMethodPage()) {
			setTimeout(function () {
				wcPaysafeCheckout.getFormElement().submit();
			}, 200);
		}
	});

	$(document.body).on('updated_checkout', function () {
		wcPaysafeCheckout.onUpdateCheckout();
	});

	var wcPaysafeCheckout = {
		gatewayId: paysafe_checkoutjs_payments_params.gatewayId || 'paysafe_checkout_payments',

		/**
		 * Loads the form
		 */
		onInit: function () {
			wcPaysafeCheckout.bindFormSubmissions();

			wcPaysafeCheckout.mimicCreateAccountToSaveToAccountCheckbox();
			wcPaysafeCheckout.hideEmptySavedMethodsInput();
			wcPaysafeCheckout.onSavedPaymentMethodInputsChange();
			wcPaysafeCheckout.onPaymentMethodChange();

			$(document.body)
				.on('paysafeErrored', wcPaysafeCheckout.onError)
				.on('checkout_error', wcPaysafeCheckout.resetErrors);

			wcPaysafeCheckout.getFormElement().on('change keyup', wcPaysafeCheckout.resetErrors);
		},

		/**
		 * Actions performed when the checkout is updated
		 */
		onUpdateCheckout: function () {
			wcPaysafeCheckout.mimicCreateAccountToSaveToAccountCheckbox();
			wcPaysafeCheckout.hideEmptySavedMethodsInput();
			wcPaysafeCheckout.onCreateAccountCheckboxChange();
			wcPaysafeCheckout.maybeShowCvvField();
		},

		maybeShowCvvField: function () {
			var cvv = $('.woocommerce-' + wcPaysafeCheckout.gatewayId + '-cvv-wrap');
			if (wcPaysafeCheckout.isPayingWithSavedCard() && wcPaysafeCheckout.isCvvRequired()) {
				cvv.slideDown();
				return;
			}

			cvv.hide();
		},

		/**
		 * Binds the submission of the layover form
		 */
		bindFormSubmissions: function () {
			// Bind the add payment submission
			if (wcPaysafeCheckout.isAddPaymentMethodPage()) {
				// Add payment method form
				var addMethodForm = wcPaysafeCheckout.getFormElement();

				// Remove the submit action, which adds an overlay over the form
				addMethodForm.off('submit');

				addMethodForm.on('submit', function (e) {
					if (!wcPaysafeCheckout.isPaysafeChecked()) {
						return true;
					}

					wcPaysafeCheckout.submitForm(e);

					return false;
				});

				return true;
			}

			// Bind the change payment method submission
			if (wcPaysafeCheckout.isChangePaymentMethodPage() || wcPaysafeCheckout.isPayForOrderPage()) {
				var changeMethodForm = wcPaysafeCheckout.getFormElement();

				// Remove the submit action, which adds an overlay over the form
				changeMethodForm.off('submit');

				changeMethodForm.on('submit', function (e) {
					// Not paying with Paysafe
					if (!wcPaysafeCheckout.isPaysafeChecked()) {
						return true;
					}

					// Paying with a Token
					if (!wcPaysafeCheckout.isPayingWithNewMethod()) {
						// Check the CVV
						if (!wcPaysafeCheckout.isCvvValid()) {
							return false;
						}

						// Block the page when on "Pay for order" because WC does not do it
						if (wcPaysafeCheckout.isPayForOrderPage()) {
							wcPaysafeCheckout.block(wcPaysafeCheckout.getFormElement());
						}

						return true;
					}

					wcPaysafeCheckout.submitForm(e);

					return false;
				});

				return true;
			}


			// Normal checkout submission
			var form = $('#paysafe_checkout_payment_form');
			form.on('submit', wcPaysafeCheckout.submitForm);
		},

		/**
		 * Starts the layover generation
		 * @since 3.7.0
		 * @param e
		 */
		submitForm: function (e) {
			if (e) {
				e.preventDefault();
			}

			wcPaysafeCheckout.loadPaymentForm(paysafe_layover_params);
		},


		/**
		 * Loads the payment layover form
		 * @since 3.7.0
		 * @param {Object} iframeParams
		 * @return {String}
		 */
		loadPaymentForm: function (iframeParams) {
			var options = iframeParams.options;

			// Format the total to an integer
			options.amount = wcPaysafeCheckout.formatAmount(!wcPaysafeCheckout.isUndefined(options.amount) ? options.amount : 0);

			if (!wcPaysafeCheckout.isUndefined(options.accounts)) {
				// The CC needs to be an integer, so let's format it just in case
				options.accounts.CC = wcPaysafeCheckout.formatAmount(!wcPaysafeCheckout.isUndefined(options.accounts.CC) ? options.accounts.CC : 0);
			}

			// Allow for additional parameters or formatting
			$(document.body).triggerHandler('wc_paysafe_chechout_payments_iframe_options', options);

			wcPaysafeCheckout.block(wcPaysafeCheckout.getFormElement());

			var paymentSuccess = false;

			wcPaysafeCheckout.consoleLog('maketoken: options: ', options);
			wcPaysafeCheckout.consoleLog('maketoken: iframeParams: ', iframeParams);

			window.paysafe.checkout.setup(
				paysafe_checkoutjs_payments_params.publicKey,
				options,
				function (instance, error, result) {
					wcPaysafeCheckout.consoleLog('maketoken: result: ', {
						instance: instance,
						error   : error,
						result  : result,
					});

					wcPaysafeCheckout.unblock(wcPaysafeCheckout.getFormElement());

					/**
					 * error.code - Short error code identifying the error type.
					 * error.displayMessage - Provides an error message that can be displayed to users.
					 * error.detailedMessage - Provides a detailed error message that can be logged.
					 * error.correlationId - Unique ID which can be provided to the Paysafe Support team as a reference
					 * for investigation.
					 */

					if (null != result && result.paymentHandleToken) {
						var action = iframeParams.processAction || 'process_payment';

						var saveToAccount = 0;
						if (!wcPaysafeCheckout.isUndefined(result.customerOperation)) {
							saveToAccount = 'ADD' == result.customerOperation ? 1 : 0;
						}

						var data = {
							security        : paysafe_checkoutjs_payments_params.nonce[action],
							token           : result.paymentHandleToken,
							payment_method  : result.paymentMethod,
							amount          : result.amount,
							transaction_type: result.transactionType,
							save_to_account : saveToAccount,
						};

						if ('process_payment' === action) {
							data.order_id = options.orderId;
						} else if ('update_payment_method' === action) {
							data.user_id = options.userId;
							data.update_token_id = options.update_token_id;
						} else if ('add_payment_method' === action) {
							data.user_id = options.userId;
						} else if ('change_payment_method' === action) {
							data.order_id = options.orderId;
						}

						$.ajax({
							type   : 'POST',
							data   : data,
							url    : wcPaysafeCheckout.formatAjaxURL(action),
							success: function (response) {
								// 1. Display the correct screen and message after the payment
								// 2. Set global state of the payment, so we can use it after the iframe is closed

								// Trigger on payment result
								$(document.body).triggerHandler('wc_paysafe_payments_iframe_payment_result', [response, response.success, iframeParams]);

								if (true == response.success) {
									// Show the success screen message
									instance.showSuccessScreen(wcPaysafeCheckout.limitStringLength(
										response.data.message,
										200,
										true),
									);

									// Trigger on successful payment
									$(document.body).triggerHandler('wc_paysafe_payments_iframe_payment_successful', [response, iframeParams]);

									paymentSuccess = true;

									wcPaysafeCheckout.consoleLog('success: iframeParams: ', iframeParams);

									// Close the window
									setTimeout(function () {
										instance.close();
									}, 3000)
								} else {
									// Trigger on failed payment
									$(document.body).triggerHandler('wc_paysafe_payments_iframe_payment_failed', [response, iframeParams]);

									// Show the fail screen message
									instance.showFailureScreen(wcPaysafeCheckout.limitStringLength(
										response.data.message,
										200,
										true),
									);
								}
							},
						});
					} else {

						wcPaysafeCheckout.consoleLog('failure: error: ', error);

						wcPaysafeCheckout.unblock(wcPaysafeCheckout.getFormElement());

						// Show the fail screen message
						if (null != instance) {
							let errorMessage = paysafe_checkoutjs_payments_params.il8n.error + ' ' + error.displayMessage;
							let correlationId = ' ' + paysafe_checkoutjs_payments_params.il8n.correlation + ' ' + error.correlationId;

							instance.showFailureScreen(
								wcPaysafeCheckout.limitStringLength(
									errorMessage + correlationId,
									200,
									true),
							);
						} else {
							$(document.body).trigger('paysafeErrored', new paysafeError(error.displayMessage + ' ' + error.detailedMessage + ' CorrelationId: ' + error.correlationId, '', true));
						}
					}
				},
				function (stage, expired) {
					// Trigger action when the iframe is closed
					$(document.body).triggerHandler('wc_paysafe_payments_iframe_closed', [stage, paymentSuccess, iframeParams]);

					wcPaysafeCheckout.consoleLog('failed: stage: ', stage);
					wcPaysafeCheckout.consoleLog('failed: expired: ', expired);
					wcPaysafeCheckout.consoleLog('failed: iframeParams: ', iframeParams);

					if (stage === "PAYMENT_HANDLE_REDIRECT" || stage === "PAYMENT_HANDLE_PAYABLE") {

						// 1. If the payment is successful, redirect to the  Thank You page
						if (paymentSuccess) {
							var blockForm = wcPaysafeCheckout.getFormElement();

							// Block the area to prevent the customer from clicking the pay button again
							wcPaysafeCheckout.block(blockForm);

							if (wcPaysafeCheckout.isCheckoutPage()
								&& checkoutBypass.form.triggerHandler('checkout_place_order_success') === false) {
								return;
							}

							window.parent.location.href = iframeParams.urls.successRedirectPage;
						} else {
							checkoutBypass.resetCheckoutForm();
						}
					} else {
						if (wcPaysafeCheckout.isCheckoutPage()) {
							wcPaysafeCheckout.consoleLog('failed: stage: resetcheck ', stage);

							// On Checkout page we need to unlock the page and validate the fields again.
							checkoutBypass.resetCheckoutForm();
						} else {
							wcPaysafeCheckout.consoleLog('failed: stage: Unblock ', stage);

							// On Pay page, unblock the form
							wcPaysafeCheckout.unblock(wcPaysafeCheckout.getFormElement());
						}
					}
				},
			);
		},

		/**
		 * Binds the create account checkbox change.
		 * @since 3.3.0
		 */
		onCreateAccountCheckboxChange: function () {
			$('form.woocommerce-checkout').on('change', 'input#createaccount', wcPaysafeCheckout.mimicCreateAccountToSaveToAccountCheckbox);
		},

		/**
		 * Binds the Saved methods selection with the actions needed to be performed on change
		 */
		onSavedPaymentMethodInputsChange: function () {
			// Bind all inputs
			wcPaysafeCheckout.getFormElement().on('change', '.woocommerce-' + wcPaysafeCheckout.gatewayId + '-SavedPaymentMethods-tokenInput', function () {
				wcPaysafeCheckout.maybeShowCvvField();

				if (wcPaysafeCheckout.isPayingWithNewMethod()) {
					wcPaysafeCheckout.maybeShowSaveToAccountCheckbox();
				} else {
					wcPaysafeCheckout.hideSaveToAccountCheckbox();
				}
			});
		},

		onPaymentMethodChange: function () {
			// Bind all inputs
			wcPaysafeCheckout.getFormElement().on('change', '#payment_method_' + wcPaysafeCheckout.gatewayId, function () {
				wcPaysafeCheckout.maybeShowCvvField();
			});
		},

		maybeShowSaveToAccountCheckbox: function () {
			var account_el = $('input#createaccount');

			wcPaysafeCheckout.showSaveToAccountCheckbox();

			if (0 < account_el.length) {
				wcPaysafeCheckout.mimicCreateAccountToSaveToAccountCheckbox();
			}
		},

		/**
		 * Mimics the create account checkbox and if the customer is a guest,
		 * we will not show the save to account option
		 */
		mimicCreateAccountToSaveToAccountCheckbox: function () {
			var account = $('input#createaccount');

			if (0 >= account.length) {
				return;
			}

			if (account.is(':checked')) {
				wcPaysafeCheckout.showSaveToAccountCheckbox();
			} else {
				wcPaysafeCheckout.hideSaveToAccountCheckbox();
			}
		},

		showSaveToAccountCheckbox: function () {
			var el = $('.wc-' + wcPaysafeCheckout.gatewayId + '-save-to-account');

			if (0 === el.length) {
				return;
			}

			// Hide them all first
			wcPaysafeCheckout.hideSaveToAccountCheckbox();

			el.show();
		},

		hideSaveToAccountCheckbox: function () {
			var el = $('.wc-' + wcPaysafeCheckout.gatewayId + '-save-to-account');

			if (0 === el.length) {
				return;
			}

			el.hide();
		},

		/**
		 * Hides the "New" radio button, if there are no saved payment methods
		 */
		hideEmptySavedMethodsInput: function () {
			var savedMethodsWrapper = $('.woocommerce-' + wcPaysafeCheckout.gatewayId + '-SavedPaymentMethods-token-wrapper');

			if (0 === savedMethodsWrapper.data('count')) {
				savedMethodsWrapper.hide();
			}
		},

		/**
		 * Get WC AJAX endpoint URL.
		 *
		 * @param  {String} endpoint Endpoint.
		 * @return {String}
		 */
		formatAjaxURL: function (endpoint) {
			return paysafe_checkoutjs_payments_params.ajaxUrl
				.toString()
				.replace('%%endpoint%%', 'paysafe_payments_' + endpoint);
		},

		block: function (el, message) {
			if (wcPaysafeCheckout.isUndefined(el)) {
				return false;
			}

			if (!wcPaysafeCheckout.isFunction(el, 'block')) {
				return;
			}

			message = !wcPaysafeCheckout.isUndefined(message) ? '<p>' + message + '</p>' : '';

			el.block({
				message   : message,
				overlayCSS: {
					background: '#fff',
					opacity   : 0.6,
				},
			});
		},

		unblock: function (el) {
			if (wcPaysafeCheckout.isUndefined(el)) {
				return false;
			}

			if (!wcPaysafeCheckout.isFunction(el, 'block')) {
				return;
			}

			el.unblock();
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

			if (wcPaysafeCheckout.isScriptDev()) {
				console.log(message, object);
			}
		},

		/**
		 * Is the script in development mode
		 * @returns {boolean}
		 */
		isScriptDev: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.scriptDev) && '1' === paysafe_checkoutjs_payments_params.scriptDev;
		},

		isUndefined: function (e) {
			return 'undefined' === typeof (e);
		},

		/**
		 * Is the Paysafe chosen as the method to pay with
		 * @returns {boolean}
		 */
		isPaysafeChecked: function () {
			return $('#payment_method_' + wcPaysafeCheckout.gatewayId).is(':checked');
		},

		/**
		 * Is the customer paying with a new method
		 * @returns {boolean}
		 */
		isPayingWithNewMethod: function () {
			return $('#wc-' + wcPaysafeCheckout.gatewayId + '-payment-token-new').is(':checked');
		},

		isPayingWithSavedCard: function () {
			var field = $('input[name="wc-' + wcPaysafeCheckout.gatewayId + '-payment-token"]:checked');

			return field.hasClass('card');
		},

		isCvvValid: function () {
			var cvv = $('input#' + wcPaysafeCheckout.gatewayId + '-card-cvv');


			if (!wcPaysafeCheckout.isCvvRequired()) {
				return true;
			}

			if (wcPaysafeCheckout.isUndefined(cvv) || 0 === cvv.length) {
				$(document.body).trigger('paysafeErrored', new paysafeError(paysafe_checkoutjs_payments_params.il8n.missingCVV, '', true));
				return false;
			}

			if ('' === cvv.val()) {
				$(document.body).trigger('paysafeErrored', new paysafeError(paysafe_checkoutjs_payments_params.il8n.emptyCVV, cvv, false));
				return false;
			}

			if (4 < cvv.val().length || 3 > cvv.val().length) {
				$(document.body).trigger('paysafeErrored', new paysafeError(paysafe_checkoutjs_payments_params.il8n.invalidCVV, cvv, false));
				return false;
			}

			return true;
		},

		/**
		 * Is this the add payment method page
		 * @returns {boolean}
		 */
		isAddPaymentMethodPage: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isAddPaymentMethodPage) && '1' === paysafe_checkoutjs_payments_params.isAddPaymentMethodPage;
		},

		/**
		 * Is this the update method page
		 * @returns {boolean}
		 */
		isUpdatePaymentMethodPage: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isUpdatePaymentMethodPage) && '1' === paysafe_checkoutjs_payments_params.isUpdatePaymentMethodPage;
		},

		/**
		 * Is this the pay for order page
		 * @returns {boolean}
		 */
		isPayForOrderPage: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isPayForOrderPage) && '1' === paysafe_checkoutjs_payments_params.isPayForOrderPage;
		},

		/**
		 * Is this the pay for order page
		 * @returns {boolean}
		 */
		isCheckoutPayPage: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isCheckoutPayPage) && '1' === paysafe_checkoutjs_payments_params.isCheckoutPayPage;
		},

		/**
		 * Is this the checkout page
		 * @returns {boolean}
		 */
		isCheckoutPage: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isCheckoutPage) && '1' === paysafe_checkoutjs_payments_params.isCheckoutPage;
		},

		/**
		 * Is this change payment method page
		 * @returns {boolean}
		 */
		isChangePaymentMethodPage: function () {
			return !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isChangePaymentMethodPage) && '1' === paysafe_checkoutjs_payments_params.isChangePaymentMethodPage;
		},

		/**
		 * Resets the displayed errors
		 */
		resetErrors: function () {
			$('.wc-paysafe-error').remove();
		},

		/**
		 * Displays the errors
		 * @param e
		 * @param result
		 */
		onError: function (e, result) {
			var message = result.message;

			wcPaysafeCheckout.resetErrors();

			var appendTo = result.appendTo || '';
			if ('' === appendTo) {
				checkoutBypass.submit_error(wcPaysafeCheckout.wrapErrorMessage(message, false));
				return;
			}

			appendTo.after('<ul class="woocommerce_error woocommerce-error wc-paysafe-error"><li>' + message + '</li></ul>');

			var errorObj = $('.wc-paysafe-error');
			if (errorObj.length) {
				$('html, body').animate({
					scrollTop: (errorObj.offset().top - 200),
				}, 200);
			}
		},

		/**
		 * Wraps the error message in WC notice HTML.
		 * It is handy to have it since we are displaying the payment form on the Checkout page
		 *
		 * @since 3.7.0
		 * @param {String} message
		 * @param {Boolean} needsPaysafeClass Should we add a Paysafe specific class
		 * @returns {string}
		 */
		wrapErrorMessage: function (message, needsPaysafeClass) {
			needsPaysafeClass = needsPaysafeClass || false;

			var psClass = needsPaysafeClass ? 'wc-paysafe-error' : '';

			return '<ul class="woocommerce_error woocommerce-error ' + psClass + '"><li>' + message + '</li></ul>';
		},

		/**
		 * Returns the payment form element
		 *
		 * @returns {*|jQuery|HTMLElement}
		 */
		getFormElement: function () {
			var form = $('#paysafe_checkout_payment_form');
			if (wcPaysafeCheckout.isAddPaymentMethodPage()) {
				form = $('#add_payment_method');
			} else if (wcPaysafeCheckout.isChangePaymentMethodPage()) {
				form = $('#order_review');
			} else if (wcPaysafeCheckout.isPayForOrderPage()) {
				form = $('#order_review');
			} else if (wcPaysafeCheckout.isCheckoutPage()) {
				form = $('form.woocommerce-checkout');
			}

			return form;
		},

		/**
		 * Required if:
		 * 1. It is enabled from the plugin settings
		 * 2. We are not on the update/change/add payment method pages
		 * 3. Pay with new method is not selected
		 *
		 * Trigger "paysafe_require_cvv_with_tokens" allow 3rd party script to manipulate the option
		 *
		 * @returns {boolean}
		 */
		isCvvRequired: function () {
			var required_settings = !wcPaysafeCheckout.isUndefined(paysafe_checkoutjs_payments_params.isCvvRequiredField) && '1' === paysafe_checkoutjs_payments_params.isCvvRequiredField;

			if (!wcPaysafeCheckout.isPaysafeChecked()
				|| !wcPaysafeCheckout.isPayingWithSavedCard()
				|| wcPaysafeCheckout.isAddPaymentMethodPage()
				|| wcPaysafeCheckout.isUpdatePaymentMethodPage()
				|| wcPaysafeCheckout.isChangePaymentMethodPage()
			) {
				return false;
			}

			return required_settings && false !== $(document.body).triggerHandler('paysafe_payments_require_cvv_with_tokens', [wcPaysafeCheckout]);
		},

		/**
		 * Limit a string to the provided length
		 * @since 3.6.0
		 * @param {string} string
		 * @param {number} length
		 * @param {boolean} addDots Should we add "..." at the end of the cut string
		 * @returns {string|*}
		 */
		limitStringLength: function (string, length, addDots) {
			addDots = addDots || false;

			// If equal or smaller, no need to adjustment
			if (string.length <= parseInt(length)) {
				return string;
			}

			var suffix = '';
			if (addDots) {
				length -= 3;
				suffix = '...';
			}

			return string.substring(0, parseInt(length)) + suffix;
		},

		/**
		 * Formats the passed amount to be an integer value
		 * @since 3.6.0
		 * @param amount
		 */
		formatAmount: function (amount) {
			if (!Number(amount)) {
				return 1;
			}

			return parseInt(amount);
		},
	};

	/**
	 * The object replaces the WC checkout JS process with our own.
	 * We wanted to keep the process as close as possible to the WC one, so there are a lot of methods copied over from
	 * the WC process.
	 * @since 3.7.0
	 */
	var checkoutBypass = {
		init: function () {
			checkoutBypass.form = $('form.checkout');

			checkoutBypass.replaceCheckout();
		},

		/**
		 * Replace the WC Checkout when we use the Paysafe method
		 * @since 3.7.0
		 */
		replaceCheckout: function () {
			var form = $('form.checkout');

			form.on('checkout_place_order_' + wcPaysafeCheckout.gatewayId, function () {

				wcPaysafeCheckout.consoleLog('checkout: trigger: ', {});

				// Paying with a Token
				if (wcPaysafeCheckout.isPayingWithSavedCard()) {
					// Check the CVV
					if (!wcPaysafeCheckout.isCvvValid()) {
						return false;
					}
				}

				form.addClass('processing');

				wcPaysafeCheckout.block(form);

				// Attach event to block reloading the page when the form has been submitted
				checkoutBypass.attachUnloadEventsOnSubmit();

				wcPaysafeCheckout.consoleLog('do Ajax: ', {});

				// ajaxSetup is global, but we use it to ensure JSON is valid once returned.
				$.ajaxSetup({
					dataFilter: function (raw_response, dataType) {
						// We only want to work with JSON
						if ('json' !== dataType) {
							return raw_response;
						}

						if (checkoutBypass.is_valid_json(raw_response)) {
							return raw_response;
						} else {
							// Attempt to fix the malformed JSON
							var maybe_valid_json = raw_response.match(/{"result.*}/);

							if (null === maybe_valid_json) {
								wcPaysafeCheckout.consoleLog('Unable to fix malformed JSON');
							} else if (checkoutBypass.is_valid_json(maybe_valid_json[0])) {
								wcPaysafeCheckout.consoleLog('Fixed malformed JSON. Original:');
								wcPaysafeCheckout.consoleLog(raw_response);
								raw_response = maybe_valid_json[0];
							} else {
								wcPaysafeCheckout.consoleLog('Unable to fix malformed JSON');
							}
						}

						return raw_response;
					},
				});

				$.ajax({
					type    : 'POST',
					url     : wc_checkout_params.checkout_url,
					data    : form.serialize(),
					dataType: 'json',
					success : function (result) {
						// Detach the unload handler that prevents a reload / redirect
						checkoutBypass.detachUnloadEventsOnSubmit();

						wcPaysafeCheckout.consoleLog('checkout: result: ', result);

						try {
							if ('success' === result.result) {
								if (!wcPaysafeCheckout.isUndefined(result.paymentData)) {
									if (!wcPaysafeCheckout.isUndefined(result.process_payment_nonce)) {
										paysafe_checkoutjs_payments_params.nonce.process_payment = result.process_payment_nonce;
									}

									wcPaysafeCheckout.loadPaymentForm(result.paymentData);
								} else {
									if (form.triggerHandler('checkout_place_order_success') !== false) {
										if (-1 === result.redirect.indexOf('https://') || -1 === result.redirect.indexOf('http://')) {
											window.location = result.redirect;
										} else {
											window.location = decodeURI(result.redirect);
										}
									}
								}
							} else if ('failure' === result.result) {
								wcPaysafeCheckout.consoleLog('throw: Result failure: ', result);
								throw 'Result failure';
							} else {
								wcPaysafeCheckout.consoleLog('throw: Invalid response: ', result);
								throw 'Invalid response';
							}
						} catch (err) {
							// Reload page
							if (true === result.reload) {
								window.location.reload();
								return;
							}

							wcPaysafeCheckout.consoleLog('checkout: err: ', err);

							// Trigger update in case we need a fresh nonce
							if (true === result.refresh) {
								$(document.body).trigger('update_checkout');
							}

							wcPaysafeCheckout.consoleLog('failed checkout: ',
								{err: err, redMessages: result.messages});

							// Add new errors
							if ('Result failure' !== err && 'Invalid response' !== err) {
								checkoutBypass.submit_error(err);
							} else if (result.messages) {
								checkoutBypass.submit_error(result.messages);
							} else {
								checkoutBypass.submit_error('<div class="woocommerce-error">' + wc_checkout_params.i18n_checkout_error + '111</div>'); // eslint-disable-line max-len
							}
						}
					},

					error: function (jqXHR, textStatus, errorThrown) {
						// Detach the unload handler that prevents a reload / redirect
						checkoutBypass.detachUnloadEventsOnSubmit();

						wcPaysafeCheckout.consoleLog('checkout: errorThrown: ', errorThrown);

						checkoutBypass.submit_error('<div class="woocommerce-error">' + errorThrown + '</div>');
					},
				});

				return false;
			});
		},

		handleUnloadEvent: function (e) {
			// Modern browsers have their own standard generic messages that they will display.
			// Confirm, alert, prompt or custom message are not allowed during the unload event
			// Browsers will display their own standard messages

			// Check if the browser is Internet Explorer
			if ((navigator.userAgent.indexOf('MSIE') !== -1) || (!!document.documentMode)) {
				// IE handles unload events differently than modern browsers
				e.preventDefault();
				return undefined;
			}

			return true;
		},

		attachUnloadEventsOnSubmit: function () {
			$(window).on('beforeunload', checkoutBypass.handleUnloadEvent);
		},

		detachUnloadEventsOnSubmit: function () {
			$(window).unbind('beforeunload', checkoutBypass.handleUnloadEvent);
		},

		submit_error: function (error_message) {
			$('.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message').remove();

			wcPaysafeCheckout.getFormElement().prepend('<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error_message + '</div>'); // eslint-disable-line max-len

			checkoutBypass.resetCheckoutForm();
			checkoutBypass.scroll_to_notices();

			$(document.body).trigger('checkout_error');
		},

		resetCheckoutForm: function () {
			wcPaysafeCheckout.getFormElement().removeClass('processing').unblock();
			wcPaysafeCheckout.getFormElement().find('.input-text, select, input:checkbox').trigger('validate').trigger('blur');
		},

		scroll_to_notices: function () {
			var scrollElement = $('.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout');

			if (!scrollElement.length) {
				scrollElement = checkoutBypass.form;
			}
			$.scroll_to_notices(scrollElement);
		},

		is_valid_json: function (raw_json) {
			try {
				var json = JSON.parse(raw_json);

				return (json && 'object' === typeof json);
			} catch (e) {
				return false;
			}
		},
	};

	var paysafeError = function (message, element, global) {
		this.message = message;
		this.appendTo = element || false;
		this.global = global || true;
	}

	window.wcPaysafe = window.wcPaysafe || {};
	window.wcPaysafe.wcPaysafeCheckout = wcPaysafeCheckout
	window.wcPaysafe.checkoutBypass = checkoutBypass
})(jQuery);