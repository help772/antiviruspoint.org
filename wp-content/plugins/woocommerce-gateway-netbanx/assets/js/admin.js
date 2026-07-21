/* global wc_paysafe_params */
(function ($) {
	var wcPaysafeOrderRefund = {
		init: function () {
			this.moveRefundInfoAfterTable();
		},

		moveRefundInfoAfterTable: function () {
			var refundInfo = $('#woocommerce-order-items').find('.wc-paysafe-refund-info');

			var refundTotals = $('.wc-order-refund-items').not('.wc-paysafe-refund-info').find('table.wc-order-totals');

			refundTotals.after(refundInfo)
		},
	};
	var wcPaysafeOrderCapture = {
		init: function () {
			$('#woocommerce-order-items')
				.on('click', 'button.wc-paysafe-capture-payment-init', this.initOrderCapture)
				.on('click', 'button.wc-paysafe-capture-cancel', this.cancelOrderCapture)
				.on('click', 'button.wc-paysafe-capture-payment', this.processCapture);

		},

		initOrderCapture: function () {
			$('.wc-paysafe-capture-payment-wrapper').show();
			$('.wc-paysafe-capture-allowed-amount-wrapper').show();
			$('.wc-paysafe-capture-amount-wrapper').show();
			$('.wc-paysafe-capture-cancel').show();
			$('button.wc-paysafe-capture-payment-init').hide();
		},

		cancelOrderCapture: function () {
			$('.wc-paysafe-capture-payment-wrapper').hide();
			$('.wc-paysafe-capture-allowed-amount-wrapper').hide();
			$('.wc-paysafe-capture-amount-wrapper').hide();
			$('.wc-paysafe-capture-cancel').hide();
			$('button.wc-paysafe-capture-payment-init').show();
		},

		processCapture: function () {
			var orderItems = $('#woocommerce-order-items');
			orderItems.block();

			if (!window.confirm(wc_paysafe_params.i18n_capture_payment)) {
				orderItems.unblock();
				return false;
			}

			var captureAmount = $('.wc-paysafe-capture-amount-wrapper input.wc-paysafe-capture-amount').val();

			if ('' === captureAmount) {
				orderItems.unblock();
				return false;
			}

			var data = {
				action        : 'wc_paysafe_capture_payment',
				order_id      : woocommerce_admin_meta_boxes.post_id,
				capture_amount: captureAmount,
				security      : wc_paysafe_params.capture_payment,
			};

			$.post(wc_paysafe_params.ajax_url, data, function (response) {
				if (true === response.success) {
					// Redirect to same page for show the refunded status
					window.alert(response.data.message);

					window.location.href = window.location.href;
				} else {
					window.alert(response.data.message);
					orderItems.unblock();
				}
			});
		},
	};

	var wcPaysafeSettings = {
		init: function () {
			$('.woocommerce_page_wc-settings').on('change', '#woocommerce_netbanx_integration', this.loadIntegrationSettings);
			this.triggerIntegrationChange();
			this.onSettingsChanges();
			this.onIframeSettingsChange();

			if ('undefined' != typeof $('.repeater-field')) {
				this.initRepeaterFields();
			}

			$("#woocommerce_paysafe_checkout_payments_available_payment_options").on('change', this.paymentsSettingsLogic).trigger('change');
		},

		integrationType: function () {
			return $('#woocommerce_netbanx_integration').val();
		},

		triggerIntegrationChange: function () {
			$('.woocommerce_page_wc-settings #woocommerce_netbanx_integration').change();
		},

		onSettingsChanges: function () {
			$('#woocommerce_netbanx_saved_cards').on('change', this.showHideSaveCustomersSettings).change();
			$('#woocommerce_netbanx_use_layover_3ds2').on('change', this.showHide3DSSettings).change();

			$('#woocommerce_paysafe_checkout_payments_saved_cards').on('change', function (e){
				var el = $(e.target);
				var showHideEl = $('.show_if_paysafe_checkout_payments_saved_cards');
				var showHideBlock = showHideEl.closest('tr');

				if (el.is(':checked')) {
					showHideBlock.show();
					showHideEl.show();
				} else {
					showHideBlock.hide();
					showHideEl.hide();
				}
			}).change();
		},

		onIframeSettingsChange: function () {
			$('#woocommerce_netbanx_use_iframe').on('change', this.showHideIframeSettings).change();
		},

		showHide3DSSettings: function (e) {
			var self = $(e.target);
			var preference = $('#woocommerce_netbanx_threeds2_challenge_preference');

			preference.closest('tr').hide();

			if (self.is(':checked')) {
				preference.closest('tr').show();
			}
		},

		showHideSaveCustomersSettings: function (e) {
			var el = $(e.target);
			var integration = wcPaysafeSettings.integrationType();
			var showHideEl = $('.show_if_' + integration + '_saved_cards');
			var showHideBlock = showHideEl.closest('tr');

			if (el.is(':checked')) {
				showHideBlock.show();
				showHideEl.show();
			} else {
				showHideBlock.hide();
				showHideEl.hide();
			}
		},

		showHideIframeSettings: function (e) {
			var el = $(e.target);
			var integration = wcPaysafeSettings.integrationType();
			var showHideEl = $('.show_if_' + integration + '_use_iframe');
			var showHideBlock = showHideEl.closest('tr');

			if (el.is(':checked')) {
				showHideBlock.show();
				showHideEl.show();
			} else {
				showHideBlock.hide();
				showHideEl.hide();
			}
		},

		loadIntegrationSettings: function (e) {

			var loadedPage = $('#woocommerce_netbanx_general_settings_start');

			if ('undefined' === typeof loadedPage) {
				return;
			}

			var integrationEl = $(e.target);
			var integration = integrationEl.val();
			var initial_type = integrationEl.data('initial-type');
			var hideElements = [];
			var showElements = [];
			var errorMessageEl = $('.paysafe_warning');
			errorMessageEl.html(wc_paysafe_params.il8n_integration_changed);
			errorMessageEl.hide();

			if (integration !== initial_type) {
				hideElements = $('.integration_' + initial_type);
				hideElements.hide();
				hideElements.closest('tr').hide();
				errorMessageEl.show();
			} else {
				showElements = $('.integration_' + initial_type);
				showElements.show();
				showElements.closest('tr').show();
				errorMessageEl.hide();
			}
		},

		initRepeaterFields: function () {
			$('.repeater-field-wrap').on('click', '.paysafe-remove-account-id', function () {
				var fieldKey = $(this).closest('.repeater-field-wrap').data('id');
				var wrap = $('.' + fieldKey + '-repeater-field-wrap');
				var allFieldsetsCount = wrap.find('.repeater-field').not('.template-fieldset').length;
				var fieldset = $(this).closest('.repeater-field');

				if (1 < parseInt(allFieldsetsCount) && window.confirm(wc_paysafe_params.il8n_confirm_pair_removal)) {
					fieldset.remove();
				}
			})
				.on('click', '.paysafe-add-account-id', function () {
					var fieldKey = $(this).closest('.repeater-field-wrap').data('id');
					var wrap = $('.' + fieldKey + '-repeater-field-wrap');
					var allFieldsets = wrap.find('.repeater-field').not('.template-fieldset');
					var template = wrap.find('.repeater-field.template-fieldset');
					var last = allFieldsets.last();

					var keys = [];
					allFieldsets.each(function () {
						keys.push($(this).data('field-key'));
					});

					var max = keys.reduce(function (a, b) {
						return Math.max(a, b);
					});

					var newHtml = template.clone();

					var currency_field_name = fieldKey + '_account_currency';
					var account_id_field_name = fieldKey + '_account_id';

					newHtml.html(function (i, html) {
						var newValue = html.replace(new RegExp("{number}", "g"), parseInt(max) + 1);
						newValue = newValue.replace(new RegExp("{" + currency_field_name + "}", "g"), currency_field_name);
						newValue = newValue.replace(new RegExp("{" + account_id_field_name + "}", "g"), account_id_field_name);

						return newValue;
					});

					newHtml.find('.enhanced').removeClass('enhanced');
					newHtml.removeClass('template-fieldset');
					newHtml.data('field-key', parseInt(max) + 1);

					newHtml.show();
					newHtml.insertAfter(last);

					$('body').trigger('wc-enhanced-select-init');
				});
		},

		paymentsSettingsLogic: function (e) {
			var methods = $(e.target).val();

			$('.apple_pay_element').closest('tr').hide();
			$('.google_pay_element').closest('tr').hide();
			$('.google_pay_block').hide();

			if (methods.includes('applePay')) {
				$('.apple_pay_element').closest('tr').show();
			}

			if (methods.includes('googlePay')) {
				$('.google_pay_element').closest('tr').show();
				$('.google_pay_block').show();
			}
		},
	};

	wcPaysafeOrderCapture.init();
	wcPaysafeOrderRefund.init();
	wcPaysafeSettings.init();
})(jQuery);