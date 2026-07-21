/**
 * WooCommerce AvaTax Reconciliation JavaScript
 *
 * @package WooCommerce_AvaTax
 */

(function($) {
	'use strict';

	/**
	 * Reconciliation object
	 */
	var WC_AvaTax_Reconciliation = {

		// Maximum date range in months (From to To must be within this)
		MAX_DATE_RANGE_MONTHS: 3,

		// Store the current data
		currentData: null,

		// Track which run's data is currently displayed in the Overview section
		activeSessionId: null,

		// Pagination: server-side (session_id + per_page) or client-side (full data)
		paginationPerPage: 20,
		paginationState: {
			'missing-orders': { sessionId: null, totalCount: 0, page: 1, data: [] },
			'mismatches': { sessionId: null, totalCount: 0, page: 1, data: [] }
		},


	/**
	 * Initialize
	 */
	init: function() {
		this.bindEvents();
		this.applyDateRangeConstraints();
		this.initTooltips();
		this.hideTabsInitially();
		this.fetchPreviousRuns();
	},

	/**
	 * Fetch and render previous reconciliation runs on page load.
	 */
	fetchPreviousRuns: function() {
		var self = this;
		var $tbody = $('#previous-runs-table tbody');
		$tbody.html(
			'<tr class="previous-runs-loading"><td colspan="9">' +
			'<span class="dashicons dashicons-update reconciliation-spinner"></span> ' +
			(wcAvataxReconciliation.strings.loading || 'Loading...') +
			'</td></tr>'
		);
		$('#previous-runs-table').show();
		$('.previous-runs-empty').hide();
		$.post(wcAvataxReconciliation.ajax_url, {
			action: 'wc_avatax_get_reconciliation_runs',
			nonce: wcAvataxReconciliation.nonce
		}).done(function(res) {
			if (res.success && res.data && res.data.runs) {
				self.renderPreviousRunsTable(res.data.runs);
			} else {
				self.renderPreviousRunsTable([]);
			}
		}).fail(function() {
			self.renderPreviousRunsTable([]);
		});
	},

	/**
	 * Render the Previous Runs table with data.
	 * @param {Array} runs List of run objects from the server
	 */
	renderPreviousRunsTable: function(runs) {
		var self = this;
		var $table = $('#previous-runs-table');
		var $tbody = $table.find('tbody');
		var $empty = $('.previous-runs-empty');
		var $infoText = $('.previous-runs-info-text');
		$tbody.empty();
		if (!runs || runs.length === 0) {
			$table.hide();
			$empty.show();
			$infoText.hide();
			if (self.activeSessionId) {
				self.activeSessionId = null;
				self.currentData = null;
				$('.wc-avatax-reconciliation-tabs, .wc-avatax-reconciliation-content').fadeOut();
			}
			return;
		}
		$table.show();
		$empty.hide();
		$infoText.show();

		var activeSessionStillPresent = false;
		$.each(runs, function(idx, run) {
			if (self.activeSessionId && run.session_id === self.activeSessionId) {
				activeSessionStillPresent = true;
			}
			var docType = run.document_type || 'All';
			var runStatus = (run.status || 'unknown').toLowerCase();
			if (docType === 'SalesInvoice') { docType = 'Sales Invoice'; }
			else if (docType === 'ReturnInvoice') { docType = 'Return Invoice'; }
			var statusLabel = runStatus.charAt(0).toUpperCase() + runStatus.slice(1);
			var isCompleted = (runStatus === 'completed');
			var viewBtn = isCompleted
				? '<button type="button" class="button button-small view-previous-run" data-session-id="' + run.session_id + '">View</button>'
				: '<button type="button" class="button button-small" disabled style="opacity:0.5;cursor:not-allowed;">View</button>';
			var statusBadge = '<span class="run-status-badge status-' + runStatus + '">' + statusLabel + '</span>';
			$tbody.append(
				'<tr data-session-id="' + run.session_id + '">' +
				'<td>' + (idx + 1) + '</td>' +
				'<td>' + (run.from_date || '') + ' &rarr; ' + (run.to_date || '') + '</td>' +
				'<td>' + docType + '</td>' +
				'<td>' + (run.wc_orders || 0) + '</td>' +
				'<td>' + (run.avalara_transactions || 0) + '</td>' +
				'<td>' + (run.missing_count || 0) + '</td>' +
				'<td>' + (run.mismatch_count || 0) + '</td>' +
				'<td>' + viewBtn + '</td>' +
				'<td>' + statusBadge + '</td>' +
				'</tr>'
			);
		});

		if (self.activeSessionId) {
			if (activeSessionStillPresent) {
				self.highlightActiveRow();
			} else {
				self.activeSessionId = null;
				self.currentData = null;
				$('.wc-avatax-reconciliation-tabs, .wc-avatax-reconciliation-content').fadeOut();
			}
		}
	},

	/**
	 * Handle clicking "View" on a previous run: load that session's data into the tabs.
	 * @param {string} sessionId Session UUID for the run to view
	 */
	handleViewRun: function(sessionId) {
		var self = this;
		self.activeSessionId = null;
		self.highlightActiveRow();
		self.clearPreviousResults();
		$('.wc-avatax-reconciliation-header').siblings('.notice').remove();
		$.post(wcAvataxReconciliation.ajax_url, {
			action: 'wc_avatax_get_reconciliation_job_status',
			nonce: wcAvataxReconciliation.nonce,
			session_id: sessionId
		}).done(function(res) {
			if (res.success && res.data && res.data.overview) {
				self.currentData = res.data;
				self.activeSessionId = sessionId;
				self.highlightActiveRow();
				self.showTabs();
				self.populateOverviewTab(res.data.overview);
				self.initMissingOrdersTabFromCount(sessionId, res.data.missing_orders ? res.data.missing_orders.count : 0);
				self.initMismatchesTabFromCount(sessionId, res.data.mismatches ? res.data.mismatches.count : 0);
				self.switchTab('overview');
			} else {
				self.showNotice('Could not load run data. The run may have been purged.', 'error');
			}
		}).fail(function() {
			self.showNotice('Failed to load run data.', 'error');
		});
	},

	/**
	 * Hide tabs initially until Run button is clicked
	 */
	hideTabsInitially: function() {
		$('.wc-avatax-reconciliation-tabs').hide();
		$('.wc-avatax-reconciliation-content').hide();
	},

	/**
	 * Highlight the active row in the Previous Runs table matching activeSessionId.
	 */
	highlightActiveRow: function() {
		$('#previous-runs-table tbody tr').removeClass('active-run');
		if (this.activeSessionId) {
			$('#previous-runs-table tbody tr[data-session-id="' + this.activeSessionId + '"]').addClass('active-run');
		}
	},

	/**
	 * Show tabs after data is loaded
	 */
	showTabs: function() {
		$('.wc-avatax-reconciliation-tabs').fadeIn();
		$('.wc-avatax-reconciliation-content').fadeIn();
	},

	/**
	 * Clear previous search results from the UI so only the current search is shown.
	 */
	clearPreviousResults: function() {
		var waiting = (wcAvataxReconciliation.strings && wcAvataxReconciliation.strings.waitingForResults) || 'Waiting for results. Run a search to see data.';

		// Overview: show loading spinners on card values
		var $cards = $('#overview-cards .reconciliation-card');
		if ($cards.length >= 4) {
			var spinnerHtml = '<span class="dashicons dashicons-update reconciliation-spinner"></span>';
			$cards.eq(0).find('.card-value').html(spinnerHtml);
			$cards.eq(1).find('.card-value').html(spinnerHtml);
			$cards.eq(2).find('.card-value').html(spinnerHtml);
			$cards.eq(3).find('.card-value').html(spinnerHtml);
		}
		// Reset pagination state so tabs don't show old data
		this.paginationState['missing-orders'] = { sessionId: null, totalCount: 0, page: 1, data: [] };
		this.paginationState['mismatches'] = { sessionId: null, totalCount: 0, page: 1, data: [] };

		// Missing-orders tab: hide table, show waiting message
		var $missing = $('.reconciliation-tab-content[data-tab-content="missing-orders"]');
		$missing.find('.wc-avatax-reconciliation-table').hide();
		$missing.find('.wc-avatax-reconciliation-tablenav').hide();
		$missing.find('.reconciliation-no-results').hide();
		$missing.find('.reconciliation-waiting-results').remove();
		$missing.find('.results-count').text('—');
		$missing.append('<div class="reconciliation-waiting-results"><p><span class="dashicons dashicons-info"></span> ' + waiting + '</p></div>');

		// Mismatches tab: same
		var $mismatch = $('.reconciliation-tab-content[data-tab-content="mismatches"]');
		$mismatch.find('.wc-avatax-reconciliation-table').hide();
		$mismatch.find('.wc-avatax-reconciliation-tablenav').hide();
		$mismatch.find('.reconciliation-no-results').hide();
		$mismatch.find('.reconciliation-waiting-results').remove();
		$mismatch.find('.results-count').text('—');
		$mismatch.append('<div class="reconciliation-waiting-results"><p><span class="dashicons dashicons-info"></span> ' + waiting + '</p></div>');
	},

		/**
		 * Bind event handlers
		 */
	bindEvents: function() {
		var self = this;

		// Tab switching (client-side, no postback)
		$(document).on('click', '.reconciliation-tab-link', function(e) {
			e.preventDefault();
			self.switchTab($(this).data('tab'));
		});

		// Guide accordion toggle (header button): swap down/up arrow icon
		$(document).on('click', '.reconciliation-guide-toggle', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $panel = $('#reconciliation-guide-accordion');
			var $icon = $btn.find('.reconciliation-guide-toggle-icon');
			var expanded = $btn.attr('aria-expanded') === 'true';
			$btn.attr('aria-expanded', !expanded);
			if (expanded) {
				$panel.attr('hidden', true);
				$panel.removeClass('is-open');
				$icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
			} else {
				$panel.removeAttr('hidden');
				$panel.addClass('is-open');
				$icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
			}
		});

		// Download button click
		$(document).on('click', '.overview-actions .button, .results-actions .button', this.handleDownload);

		// Pagination (server-side: fetch page; client-side: just re-render)
		$(document).on('click', '.wc-avatax-reconciliation-pagination .first-page, .wc-avatax-reconciliation-pagination .prev-page, .wc-avatax-reconciliation-pagination .next-page, .wc-avatax-reconciliation-pagination .last-page', function(e) {
			e.preventDefault();
			var $el = $(this);
			if ($el.hasClass('disabled')) { return; }
			var page = parseInt($el.data('page'), 10);
			var tabKey = $el.closest('[data-pagination-tab]').data('pagination-tab');
			if (page >= 1 && tabKey) {
				var state = WC_AvaTax_Reconciliation.paginationState[tabKey];
				state.page = page;
				if (state.sessionId) {
					if (tabKey === 'missing-orders') {
						WC_AvaTax_Reconciliation.fetchMissingOrdersPage(page);
					} else {
						WC_AvaTax_Reconciliation.fetchMismatchesPage(page);
					}
				} else {
					WC_AvaTax_Reconciliation.renderPaginatedTab(tabKey);
				}
			}
		});

		// View previous run button
		$(document).on('click', '.view-previous-run', function(e) {
			e.preventDefault();
			var sessionId = $(this).data('session-id');
			if (sessionId) {
				self.handleViewRun(sessionId);
			}
		});

		// Refresh button: re-fetch previous runs via AJAX (no page reload)
		$(document).on('click', '.previous-runs-refresh', function(e) {
			e.preventDefault();
			self.fetchPreviousRuns();
		});

		// Clear button: reset all filter fields to empty/default
		$(document).on('click', '.wc-avatax-reconciliation-filters .reconciliation-clear-filters', function(e) {
			e.preventDefault();
			self.clearFilters();
		});

		// Run button click - use broader selector and check context
		$(document).on('click', '.wc-avatax-reconciliation-filters .button-primary', function(e) {
			e.preventDefault(); // Prevent any default behavior
			e.stopPropagation(); // Stop event bubbling
			
			if (self.validateFilterForm()) {
				self.handleRunButton();
			} else {
			}
			return false; // Extra safety
		});

		// Date range: apply 3-month constraints and validate
		$(document).on('change', '#from_date, #to_date', function() {
			self.applyDateRangeConstraints();
			self.validateDateRange.call(this);
			setTimeout(function() { window.onbeforeunload = null; }, 0);
		});

		// Document type: clear WooCommerce's beforeunload (filter fields are not settings)
		$(document).on('change', '#document_type', function() {
			setTimeout(function() { window.onbeforeunload = null; }, 0);
		});

		// Restrict date inputs to picker only: no typing, paste, or arrow-key editing
		$(document).on('keydown paste', '#from_date, #to_date', function(e) {
			if (e.type === 'paste') {
				e.preventDefault();
				return;
			}
			// Allow only Tab and Escape; block all other keys (arrows change value in native date input)
			var key = e.key;
			if (key === 'Tab' || key === 'Escape') {
				return;
			}
			e.preventDefault();
		});

	},

		/**
		 * Switch tabs (client-side only, no page reload). Loads missing/mismatch page when tab is shown (batched display).
		 */
		switchTab: function(tabName) {
			// Update tab links
			$('.reconciliation-tab-link').removeClass('current');
			$('.reconciliation-tab-link[data-tab="' + tabName + '"]').addClass('current');

			// Update tab content
			$('.reconciliation-tab-content').removeClass('active');
			$('.reconciliation-tab-content[data-tab-content="' + tabName + '"]').addClass('active');

			// Lazy-load page when switching to missing-orders or mismatches (server-side batched display)
			if (tabName === 'missing-orders') {
				this.ensureMissingOrdersPageLoaded();
			} else if (tabName === 'mismatches') {
				this.ensureMismatchesPageLoaded();
			}
		},

		/**
		 * Initialize tooltips
		 */
		initTooltips: function() {
			// Add tooltips to cards or other elements if needed
			$('[data-tooltip]').each(function() {
				var $el = $(this);
				$el.attr('title', $el.data('tooltip'));
			});
		},

		/**
		 * Handle download button click
		 */
		handleDownload: function(e) {
			e.preventDefault();

			var $button = $(this);
			var originalText = $button.html();

			// Show loading state
			$button.prop('disabled', true)
				.html('<span class="dashicons dashicons-update"></span> ' + wcAvataxReconciliation.strings.loading);

			// Simulate download (will be replaced with actual AJAX call)
			setTimeout(function() {
				$button.prop('disabled', false).html(originalText);
				
				// Show notice
				WC_AvaTax_Reconciliation.showNotice(
					'Download functionality will be implemented in the next phase.',
					'info'
				);
			}, 1000);
		},

		/**
		 * Validate filter form
		 */
		validateFilterForm: function() {
			var fromDate = $('#from_date').val();
			var toDate = $('#to_date').val();

			// Check if dates are provided
			if (!fromDate || !toDate) {
				WC_AvaTax_Reconciliation.showNotice(
					'Please select both From and To dates.',
					'error'
				);
				return false;
			}

			// Check if from date is before to date
			if (new Date(fromDate) > new Date(toDate)) {
				WC_AvaTax_Reconciliation.showNotice(
					'From date must be before To date.',
					'error'
				);
				return false;
			}

			// Check date range is at most 3 months
			var from = new Date(fromDate);
			var to = new Date(toDate);
			var maxTo = this.addMonths(new Date(from.getTime()), this.MAX_DATE_RANGE_MONTHS);
			if (to > maxTo) {
				WC_AvaTax_Reconciliation.showNotice(
					'Date range cannot exceed ' + this.MAX_DATE_RANGE_MONTHS + ' months. Please adjust From or To date.',
					'error'
				);
				return false;
			}

			return true;
		},

		/**
		 * Format a Date as YYYY-MM-DD for input[type=date]
		 */
		formatDateYMD: function(d) {
			var year = d.getFullYear();
			var month = String(d.getMonth() + 1);
			var day = String(d.getDate());
			if (month.length === 1) { month = '0' + month; }
			if (day.length === 1) { day = '0' + day; }
			return year + '-' + month + '-' + day;
		},

		/**
		 * Add months to a date (returns new Date)
		 */
		addMonths: function(d, months) {
			var result = new Date(d.getTime());
			result.setMonth(result.getMonth() + months);
			return result;
		},

		/**
		 * Apply 3-month constraint to From/To date inputs:
		 * - From: min = To - 3 months, max = To
		 * - To: min = From, max = min(From + 3 months, today)
		 * Clamps values if they fall outside these bounds.
		 */
		applyDateRangeConstraints: function() {
			var self = this;
			var $from = $('#from_date');
			var $to = $('#to_date');
			var fromVal = $from.val();
			var toVal = $to.val();
			var today = this.formatDateYMD(new Date());

			if (toVal) {
				var toDate = new Date(toVal);
				var fromMin = this.formatDateYMD(this.addMonths(toDate, -self.MAX_DATE_RANGE_MONTHS));
				var fromMax = toVal;
				$from.attr('min', fromMin);
				$from.attr('max', fromMax);
				// Clamp From if it is before (To - 3 months)
				if (fromVal && new Date(fromVal) < new Date(fromMin)) {
					$from.val(fromMin);
				}
				if (fromVal && new Date(fromVal) > new Date(fromMax)) {
					$from.val(fromMax);
				}
			} else {
				$from.removeAttr('min').removeAttr('max');
			}

			if (fromVal) {
				var fromDate = new Date(fromVal);
				var toMin = fromVal;
				var toMaxDate = this.addMonths(fromDate, self.MAX_DATE_RANGE_MONTHS);
				var todayDate = new Date(today);
				var toMax = this.formatDateYMD(toMaxDate <= todayDate ? toMaxDate : todayDate);
				$to.attr('min', toMin);
				$to.attr('max', toMax);
				// Clamp To if it is after (From + 3 months) or after today
				if (toVal && new Date(toVal) < new Date(toMin)) {
					$to.val(toMin);
				}
				if (toVal && new Date(toVal) > new Date(toMax)) {
					$to.val(toMax);
				}
			} else if (toVal) {
				// To only: cap To at today
				$to.attr('max', today);
				if (new Date(toVal) > new Date(today)) {
					$to.val(today);
				}
			} else {
				$to.removeAttr('min').removeAttr('max');
			}
		},

		/**
		 * Clear all filter fields (From, To, Document Type) to empty/default.
		 * Shows mm/dd/yyyy-style placeholder on date inputs. Same behavior in all browsers including Safari.
		 */
		clearFilters: function() {
			$('#from_date').val('').removeAttr('min').removeAttr('max').css('border-color', '');
			$('#to_date').val('').removeAttr('min').removeAttr('max').css('border-color', '');
			$('#document_type').val('All');
			// Remove any visible notices in the reconciliation header area
			$('.wc-avatax-reconciliation-header').siblings('.notice').remove();
		},

		/**
		 * Handle Run button - Fetch data via AJAX
		 */
		handleRunButton: function() {
			var self = this;
			var $form = $('.reconciliation-filter-form');
			var $button = $form.find('.button-primary');

			var filters = {
				from_date: $('#from_date').val(),
				to_date: $('#to_date').val(),
				document_type: $('#document_type').val()
			};

			var originalButtonHtml = $button.html();
			$button.prop('disabled', true)
				.html('<span class="dashicons dashicons-update reconciliation-spinner"></span> ' +
					wcAvataxReconciliation.strings.loading);

			$.ajax({
				url: wcAvataxReconciliation.ajax_url,
				type: 'POST',
				data: {
					action: 'wc_avatax_get_reconciliation_data',
					nonce: wcAvataxReconciliation.nonce,
					filters: filters
				},
				success: function(response) {
					if (!response.success) {
						self.showNotice(response.data.message || wcAvataxReconciliation.strings.error, 'error');
						$button.prop('disabled', false).html(originalButtonHtml);
						return;
					}
					self.currentData = response.data;
					if (response.data.status === 'pending') {
						self.fetchPreviousRuns();
						$button.prop('disabled', false).html(originalButtonHtml);
						return;
					}
					self.activeSessionId = response.data.session_id || null;
					self.showTabs();
					self.populateOverviewTab(response.data.overview);
					self.initMissingOrdersTabFromCount(response.data.session_id, response.data.missing_orders ? response.data.missing_orders.count : 0);
					self.initMismatchesTabFromCount(response.data.session_id, response.data.mismatches ? response.data.mismatches.count : 0);
					self.switchTab('overview');
					self.fetchPreviousRuns();
					$button.prop('disabled', false).html(originalButtonHtml);
				},
				error: function(xhr, status, error) {
					self.showNotice(wcAvataxReconciliation.strings.error, 'error');
					$button.prop('disabled', false).html(originalButtonHtml);
				}
			});
		},

		/**
		 * Validate date range on change
		 */
		validateDateRange: function() {
			var fromDate = $('#from_date').val();
			var toDate = $('#to_date').val();

			if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
				$(this).css('border-color', '#d63638');
			} else {
				$(this).css('border-color', '#8c8f94');
			}
		},

		/**
		 * Show admin notice
		 */
		showNotice: function(message, type) {
			type = type || 'info';
			
			var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
			
			// Insert notice after the header
			$('.wc-avatax-reconciliation-header').after($notice);
			
			// Make notice dismissible
			$notice.find('.notice-dismiss').on('click', function() {
				$notice.fadeOut(function() {
					$(this).remove();
				});
			});

			// Auto-dismiss after 5 seconds
			setTimeout(function() {
				$notice.fadeOut(function() {
					$(this).remove();
				});
			}, 5000);

			// Scroll to notice
			$('html, body').animate({
				scrollTop: $notice.offset().top - 32
			}, 300);
		},

	/**
	 * Populate Overview Tab with data
	 */
	populateOverviewTab: function(data) {
		if (!data) {
			return;
		}

		// Update summary cards using the correct selector
		var $cards = $('#overview-cards .reconciliation-card');
		$cards.eq(0).find('.card-value').text(data.wc_orders || 0);
		$cards.eq(1).find('.card-value').text(data.avalara_transactions || 0);
		$cards.eq(2).find('.card-value').text(data.missing_in_avalara || 0);
		$cards.eq(3).find('.card-value').text(data.mismatches || 0);
	},

	/**
	 * Row renderers for each paginated tab (returns HTML string for one <tr>)
	 */
	paginationRowRenderers: {
		'missing-orders': function(item) {
			return '<tr>' +
				'<td><strong>#' + item.order_id + '</strong></td>' +
				'<td>' + (item.document_code || '') + '</td>' +
				'<td>' + item.order_date + '</td>' +
				'<td>' + item.customer + '</td>' +
				'<td>' + item.total + '</td>' +
				'<td>' + item.tax + '</td>' +
				'<td><span class="order-status status-' + item.status + '">' + item.status + '</span></td>' +
				'</tr>';
		},
		'mismatches': function(item) {
			return '<tr>' +
				'<td><strong>#' + item.order_id + '</strong></td>' +
				'<td>' + (item.document_code || '') + '</td>' +
				'<td>' + item.order_date + '</td>' +
				'<td><div class="mismatch-values"><span class="label">Total:</span> ' + item.wc_total + '<br><span class="label">Tax:</span> ' + item.wc_tax + '</div></td>' +
				'<td><div class="mismatch-values"><span class="label">Total:</span> ' + item.avalara_total + '<br><span class="label">Tax:</span> ' + item.avalara_tax + '</div></td>' +
				'<td><span class="mismatch-type">' + item.difference + '</span></td>' +
				'<td><span class="mismatch-details">' + item.difference_value + '</span></td>' +
				'</tr>';
		}
	},

	/**
	 * Generic: render current page and WooCommerce-style pagination for a tab.
	 * Server-side (sessionId): total = totalCount, pageData = state.data (already one page).
	 * Client-side: total = state.data.length, pageData = slice of state.data.
	 */
	renderPaginatedTab: function(tabKey) {
		var state = this.paginationState[tabKey];
		if (!state) { return; }
		var perPage = this.paginationPerPage;
		var total = state.sessionId ? state.totalCount : state.data.length;
		var totalPages = Math.ceil(total / perPage) || 1;
		var page = Math.max(1, Math.min(state.page, totalPages));
		state.page = page;
		var pageData = state.sessionId ? state.data : state.data.slice((page - 1) * perPage, page * perPage);

		var $container = $('.reconciliation-tab-content[data-tab-content="' + tabKey + '"]');
		var $tbody = $container.find('.wc-avatax-reconciliation-table tbody');
		var renderRow = this.paginationRowRenderers[tabKey];
		$tbody.empty();
		if (renderRow) {
			pageData.forEach(function(item) {
				$tbody.append(renderRow(item));
			});
		}

		var $tablenav = $container.find('.wc-avatax-reconciliation-tablenav');
		var $nav = $container.find('.wc-avatax-reconciliation-pagination');
		if (total === 0) {
			$tablenav.hide();
			return;
		}
		$tablenav.show();
		$nav.find('.displaying-num').text((total === 1) ? '1 item' : total + ' items');

		var firstBtn = (page === 1) ?
			'<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>' :
			'<a class="first-page button" href="#" data-page="1"><span class="screen-reader-text">First page</span><span aria-hidden="true">&laquo;</span></a>';
		var prevBtn = (page === 1) ?
			'<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>' :
			'<a class="prev-page button" href="#" data-page="' + (page - 1) + '"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">&lsaquo;</span></a>';
		var ofText = '<span class="paging-input"><span class="tablenav-paging-text">' + page + ' of ' + totalPages + '</span></span>';
		var nextBtn = (page >= totalPages) ?
			'<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>' :
			'<a class="next-page button" href="#" data-page="' + (page + 1) + '"><span class="screen-reader-text">Next page</span><span aria-hidden="true">&rsaquo;</span></a>';
		var lastBtn = (page >= totalPages) ?
			'<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>' :
			'<a class="last-page button" href="#" data-page="' + totalPages + '"><span class="screen-reader-text">Last page</span><span aria-hidden="true">&raquo;</span></a>';

		$nav.find('.pagination-links').html(firstBtn + prevBtn + ofText + nextBtn + lastBtn);
	},

	/**
	 * Initialize missing-orders tab from Run response (count + session_id only; data loaded when tab shown).
	 */
	initMissingOrdersTabFromCount: function(sessionId, count) {
		count = count || 0;
		this.paginationState['missing-orders'] = {
			sessionId: sessionId || null,
			totalCount: count,
			page: 1,
			data: []
		};
		var $container = $('.reconciliation-tab-content[data-tab-content="missing-orders"]');
		$container.find('.reconciliation-waiting-results').remove();
		$container.find('.results-count').html(
			count + ' ' + (count === 1 ? 'order' : 'orders') + ' found'
		);
		if (count === 0) {
			$container.find('.wc-avatax-reconciliation-table').hide();
			$container.find('.wc-avatax-reconciliation-tablenav').hide();
			$container.find('.description').hide();
			$container.find('.results-count').hide();

			if (!$container.find('.reconciliation-no-results').length) {
				$container.append('<div class="reconciliation-no-results"><span class="dashicons dashicons-yes-alt"></span><p>No missing orders found. All WooCommerce orders are present in Avalara.</p></div>');
			}
			$container.find('.reconciliation-no-results').show();
		} else {
			$container.find('.reconciliation-no-results').hide();
			$container.find('.wc-avatax-reconciliation-table').show();
			$container.find('.wc-avatax-reconciliation-tablenav').show();
			$container.find('.wc-avatax-reconciliation-table tbody').empty();
			$container.find('.wc-avatax-reconciliation-pagination .displaying-num').text(count + ' items');
			$container.find('.description').show();
			$container.find('.results-count').show();
		}
	},

	/**
	 * Initialize mismatches tab from Run response (count + session_id only; data loaded when tab shown).
	 */
	initMismatchesTabFromCount: function(sessionId, count) {
		count = count || 0;
		this.paginationState['mismatches'] = {
			sessionId: sessionId || null,
			totalCount: count,
			page: 1,
			data: []
		};
		var $container = $('.reconciliation-tab-content[data-tab-content="mismatches"]');
		$container.find('.reconciliation-waiting-results').remove();
		$container.find('.results-count').html(
			count + ' ' + (count === 1 ? 'mismatch' : 'mismatches') + ' found'
		);
		if (count === 0) {
			$container.find('.wc-avatax-reconciliation-table').hide();
			$container.find('.wc-avatax-reconciliation-tablenav').hide();
			$container.find('.description').hide();
			$container.find('.results-count').hide();
			if (!$container.find('.reconciliation-no-results').length) {
				$container.append('<div class="reconciliation-no-results"><span class="dashicons dashicons-yes-alt"></span><p>No mismatches found. WooCommerce and Avalara data match.</p></div>');
			}
			$container.find('.reconciliation-no-results').show();
		} else {
			$container.find('.reconciliation-no-results').hide();
			$container.find('.wc-avatax-reconciliation-table').show();
			$container.find('.wc-avatax-reconciliation-tablenav').show();
			$container.find('.wc-avatax-reconciliation-table tbody').empty();
			$container.find('.wc-avatax-reconciliation-pagination .displaying-num').text(count + ' items');
			$container.find('.description').show();
			$container.find('.results-count').show();
		}
	},

	/**
	 * Load current page for missing-orders tab if not yet loaded (server-side batched display).
	 */
	ensureMissingOrdersPageLoaded: function() {
		var state = this.paginationState['missing-orders'];
		if (!state || !state.sessionId || state.totalCount === 0) { return; }
		if (state.data.length > 0) {
			this.renderPaginatedTab('missing-orders');
			return;
		}
		this.fetchMissingOrdersPage(state.page);
	},

	/**
	 * Load current page for mismatches tab if not yet loaded (server-side batched display).
	 */
	ensureMismatchesPageLoaded: function() {
		var state = this.paginationState['mismatches'];
		if (!state || !state.sessionId || state.totalCount === 0) { return; }
		if (state.data.length > 0) {
			this.renderPaginatedTab('mismatches');
			return;
		}
		this.fetchMismatchesPage(state.page);
	},

	/**
	 * Fetch one page of missing orders via AJAX and render.
	 */
	fetchMissingOrdersPage: function(page) {
		var self = this;
		var state = this.paginationState['missing-orders'];
		if (!state || !state.sessionId) { return; }
		var $container = $('.reconciliation-tab-content[data-tab-content="missing-orders"]');
		$container.find('.wc-avatax-reconciliation-table tbody').html(
			'<tr><td colspan="7"><span class="dashicons dashicons-update reconciliation-spinner"></span> ' + wcAvataxReconciliation.strings.loading + '</td></tr>'
		);
		$.ajax({
			url: wcAvataxReconciliation.ajax_url,
			type: 'POST',
			data: {
				action: 'wc_avatax_get_reconciliation_missing_orders',
				nonce: wcAvataxReconciliation.nonce,
				session_id: state.sessionId,
				page: page,
				per_page: this.paginationPerPage
			},
			success: function(response) {
				if (response.success && response.data) {
					state.data = response.data.orders || [];
					state.page = page;
					WC_AvaTax_Reconciliation.renderPaginatedTab('missing-orders');
				}
			},
			error: function() {
				$container.find('.wc-avatax-reconciliation-table tbody').html(
					'<tr><td colspan="7">' + wcAvataxReconciliation.strings.error + '</td></tr>'
				);
			}
		});
	},

	/**
	 * Fetch one page of mismatches via AJAX and render.
	 */
	fetchMismatchesPage: function(page) {
		var self = this;
		var state = this.paginationState['mismatches'];
		if (!state || !state.sessionId) { return; }
		var $container = $('.reconciliation-tab-content[data-tab-content="mismatches"]');
		$container.find('.wc-avatax-reconciliation-table tbody').html(
			'<tr><td colspan="7"><span class="dashicons dashicons-update reconciliation-spinner"></span> ' + wcAvataxReconciliation.strings.loading + '</td></tr>'
		);
		$.ajax({
			url: wcAvataxReconciliation.ajax_url,
			type: 'POST',
			data: {
				action: 'wc_avatax_get_reconciliation_mismatches',
				nonce: wcAvataxReconciliation.nonce,
				session_id: state.sessionId,
				page: page,
				per_page: this.paginationPerPage
			},
			success: function(response) {
				if (response.success && response.data) {
					state.data = response.data.mismatches || [];
					state.page = page;
					WC_AvaTax_Reconciliation.renderPaginatedTab('mismatches');
				}
			},
			error: function() {
				$container.find('.wc-avatax-reconciliation-table tbody').html(
					'<tr><td colspan="7">' + wcAvataxReconciliation.strings.error + '</td></tr>'
				);
			}
		});
	},

	/**
	 * Populate Missing Orders Tab with data (client-side pagination)
	 */
	populateMissingOrdersTab: function(data) {
		if (!data) { return; }
		var orders = data.orders || [];
		this.paginationState['missing-orders'] = { data: orders, page: 1 };
		var $container = $('.reconciliation-tab-content[data-tab-content="missing-orders"]');
		$container.find('.results-count').html(
			orders.length + ' ' + (orders.length === 1 ? 'order' : 'orders') + ' found'
		);
		if (orders.length === 0) {
			$container.find('.wc-avatax-reconciliation-table').hide();
			$container.find('.wc-avatax-reconciliation-tablenav').hide();
			$container.find('.reconciliation-no-results').show();
		} else {
			$container.find('.reconciliation-no-results').hide();
			$container.find('.wc-avatax-reconciliation-table').show();
			this.renderPaginatedTab('missing-orders');
		}
	},

	/**
	 * Populate Mismatches Tab with data (client-side pagination)
	 */
	populateMismatchesTab: function(data) {
		if (!data) { return; }
		var mismatches = data.mismatches || [];
		this.paginationState['mismatches'] = { data: mismatches, page: 1 };
		var $container = $('.reconciliation-tab-content[data-tab-content="mismatches"]');
		$container.find('.results-count').html(
			mismatches.length + ' ' + (mismatches.length === 1 ? 'mismatch' : 'mismatches') + ' found'
		);
		if (mismatches.length === 0) {
			$container.find('.wc-avatax-reconciliation-table').hide();
			$container.find('.wc-avatax-reconciliation-tablenav').hide();
			$container.find('.reconciliation-no-results').show();
		} else {
			$container.find('.reconciliation-no-results').hide();
			$container.find('.wc-avatax-reconciliation-table').show();
			this.renderPaginatedTab('mismatches');
		}
	}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		WC_AvaTax_Reconciliation.init();
	});

})(jQuery);
