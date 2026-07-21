/*global wc_avatax_admin_misc*/
(function() {
  "use strict";

  /**
   * WooCommerce AvaTax Admin scripts
   *
   * @since 2.6.0
   */
  jQuery(function($) {
      var accounting, ref, ref1, ref2, ref3, wc_avatax_admin, woocommerce_admin, woocommerce_admin_meta_boxes, wc_avatax_admin_jsontree;
      wc_avatax_admin = (ref = window.wc_avatax_admin_elr) != null ? ref : {};
      woocommerce_admin = (ref1 = window.woocommerce_admin) != null ? ref1 : {};
      woocommerce_admin_meta_boxes = (ref2 = window.woocommerce_admin_meta_boxes) != null ? ref2 : {};
      accounting = (ref3 = window.accounting) != null ? ref3 : {};
        $(document).ready(function() {
            if($("#table_type").val() == 'flat')
            {
              $(".flat-fieldset").show();
              $(".eav-fieldset").hide();
              $(".vertical-fieldset-na").show();
            }else if($("#table_type").val() == 'eav')
            {
              $(".flat-fieldset").hide();
              $(".eav-fieldset").show();
              $(".vertical-fieldset-na").show();
            }
            else if($("#table_type").val() == 'vertical')
            {
              $(".flat-fieldset").hide();
              $(".eav-fieldset").hide();
              $(".vertical-fieldset").show();
              $(".vertical-fieldset-na").hide();
            }
            $(".field-selector-section").show();
            $("#table_type").change(function() {
              if($("#table_type").val() == 'flat')
                {
                  $(".flat-fieldset").show();
                  $(".eav-fieldset").hide();
                  $(".vertical-fieldset-na").show();
                }else if($("#table_type").val() == 'eav')
                {
                  $(".flat-fieldset").hide();
                  $(".eav-fieldset").show();
                  $(".vertical-fieldset-na").show();
                }
                else if($("#table_type").val() == 'vertical')
                {
                  $(".flat-fieldset").hide();
                  $(".eav-fieldset").hide();
                  $(".vertical-fieldset").show();
                  $(".vertical-fieldset-na").hide();
                }
            });
          $(".filter-fields").hide();
          var data = {
              action: 'wc_avatax_submit_map_perform',
              security: wc_avatax_admin.submit_map_nonce,
              param: 'document_ready',
              entity: getParameterByName('entity')  // Get the 'entity' value from URL query parameters using getParameterByName helper function
          };
          
          jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
            if(response['data'].length > 0)
              {
                $.each(response['data'], function (i, main_table) {
                  $("#secondary_table").append($("<option> </option>")
                      .attr("value", main_table.main_table).text(main_table.main_table));

                  /* $("#mapped_table").append($("<option> </option>")
                    .attr("value", main_table.main_table)
                    .attr("isarray",main_table.isarray)
                    .text(main_table.main_table)); */ /* Commented as duplicate options comming due to this */
                });
                showSchemaTree(JSON.parse(response['schema']), JSON.parse(response['savedSchema']));
                refresh_mapped_table(response['mapperTables']);
                bind_delete_mapping();
              }
            
            // Restore sub-tab after AJAX completes
            setTimeout(function() {
              var tabIndex = null;
              var urlParams = new URLSearchParams(window.location.search);
              var urlSubTab = urlParams.get('subtab');
              if (urlSubTab !== null && urlSubTab !== '') {
                tabIndex = parseInt(urlSubTab, 10);
              }
              if (tabIndex === null || isNaN(tabIndex)) {
                var storedSubTabIndex = localStorage.getItem('avatax_elr_subtab_index');
                if (storedSubTabIndex !== null && storedSubTabIndex !== '' && parseInt(storedSubTabIndex, 10) >= 0) {
                  tabIndex = parseInt(storedSubTabIndex, 10);
                }
              }
              if (tabIndex !== null && !isNaN(tabIndex) && $('.elr_container.tabs > nav > a').length > tabIndex) {
                show_content(tabIndex);
                localStorage.removeItem('avatax_elr_subtab_index');
                if (urlSubTab !== null) {
                  var url = new URL(window.location.href);
                  url.searchParams.delete('subtab');
                  window.history.replaceState({}, '', url.toString());
                }
              }
            }, 100);

          });

          // code to delete conditional mapper record
          bind_delete_mapping();
          
          // code to delete conditional mapper record
          bind_delete_conditional_mapping();

          $('.divmapper').trigger('click');

          $.each($("#ulSchema").jsontree("getSelectedItems"), function( k, v ){
            var path = v["path"].replace("JSON." , "");
            $('#mapper_table_field').append($('<option></option>').val(path).html(path));
          });
          
          $("#btnSubmitConditional").click(function(e){
            e.preventDefault();
            var filterobj = {};
            $("#saveConditionalInfo").html("");
        
            // Validate required fields
            if (!$("#cond_params").val() || !$("#mapped_table").val() || !$("#mapper_table_field").val()) {
                $("#saveConditionalInfo").html("All fields are required");
                return false;
            }
        
            // Handle filter fields if table is array type
            if ($("#mapped_table").val() && $("#mapped_table").val() != "" && $("#mapped_table").find(":selected").attr("isarray") == "1") {
                var hasEmptyFields = false;
                
                $.each($(".filter-fields-field"), function(i, dataField){
                    var filterValue = $(dataField).closest('tr').find('#mapper_table_filter_data').val();
                    if (!$(dataField).val() || !filterValue) {
                        hasEmptyFields = true;
                        return false; // break the loop
                    }
                    filterobj[$(dataField).val()] = filterValue;
                });
        
                if (hasEmptyFields) {
                    $("#saveConditionalInfo").html("Filter fields and values are required");
                    return false;
                }
            }
        
            var filter_data = {
                cond_param:           $("#cond_params").val(),
                mapped_table:         $("#mapped_table").val(),
                mapper_table_field:   $("#mapper_table_field").val(),
                filter_obj:          filterobj
            }
        
            var data = {
                action: 'wc_avatax_submit_map_perform',
                security: wc_avatax_admin.submit_map_nonce,
                param: 'save_filter_data',
                filterInfo: filter_data
            };
        
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                $("#saveConditionalInfo").html(response['data']);
                $("#tbl_conditional_mapper tbody").html(JSON.parse(response['schema']));
                if (response['code'] == 200) {
                    // Unselect all options for multi-select
                    $('#mapped_table').find('option').prop('selected', false);
                    $('#mapper_table_field').find('option').prop('selected', false);
                    // Trigger the change event
                    $('#mapped_table').trigger('change');
                    $(".filter-fields").hide();
                }
                bind_delete_conditional_mapping();
            });
        });

          $('.elr_container.tabs > nav > a').on('click', function(e) {
            e.preventDefault();
            show_content($(this).index());
          });
        
          // Restore sub-tab after page reload (e.g., Data selector tab)
          // Get target tab index from URL or localStorage
          var targetTabIndex = null;
          var urlParams = new URLSearchParams(window.location.search);
          var urlSubTab = urlParams.get('subtab');
          if (urlSubTab !== null && urlSubTab !== '') {
            targetTabIndex = parseInt(urlSubTab, 10);
          }
          if (targetTabIndex === null || isNaN(targetTabIndex)) {
            var storedSubTabIndex = localStorage.getItem('avatax_elr_subtab_index');
            if (storedSubTabIndex !== null && storedSubTabIndex !== '' && parseInt(storedSubTabIndex, 10) >= 0) {
              targetTabIndex = parseInt(storedSubTabIndex, 10);
            }
          }
          
          // Function to restore tab
          function restoreSubTab() {
            if (targetTabIndex !== null && !isNaN(targetTabIndex) && $('.elr_container.tabs > nav > a').length > targetTabIndex) {
              var currentTabIndex = $('.elr_container.tabs > nav > a.selected').index();
              // Only restore if not already on the correct tab
              if (currentTabIndex !== targetTabIndex) {
                show_content(targetTabIndex);
                return true; // Successfully restored
              }
              return true; // Already on correct tab
            }
            return false; // Not restored
          }
          
          // If we have a target tab, restore it instead of defaulting to 0
          if (targetTabIndex !== null && !isNaN(targetTabIndex) && $('.elr_container.tabs > nav > a').length > targetTabIndex) {
            // Don't call show_content(0), instead restore the target tab
            var restoreAttempts = 0;
            var restoreInterval = setInterval(function() {
              restoreAttempts++;
              if (restoreSubTab() || restoreAttempts > 20) {
                clearInterval(restoreInterval);
                localStorage.removeItem('avatax_elr_subtab_index');
                // Remove subtab parameter from URL
                if (urlSubTab !== null) {
                  var url = new URL(window.location.href);
                  url.searchParams.delete('subtab');
                  window.history.replaceState({}, '', url.toString());
                }
              }
            }, 100);
            
            // Also try immediately and after delays
            setTimeout(restoreSubTab, 50);
            setTimeout(restoreSubTab, 200);
            setTimeout(restoreSubTab, 500);
            setTimeout(restoreSubTab, 1000);
          } else {
            // No stored tab, default to first tab
            show_content(0);
          }

          // Network Directory tab handlers
          $('.network-directory-nav .network-directory-tab').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            show_network_directory_content($(this).index());
          });

          // Initialize first network directory tab as active
          if ($('.network-directory-tabs').length > 0) {
            show_network_directory_content(0);
          }

          // Search functionality event handlers
          $("#btn_search_directory").on('click', function(e) {
            e.preventDefault();
            perform_directory_search();
          });

          $("#btn_clear_search").on('click', function(e) {
            e.preventDefault();
            clear_search_results();
          });

          // Batch Job Status functionality event handlers
          $("#refresh_batch_list").on('click', function(e) {
            e.preventDefault();
            window.batchSearchesLoaded = false; // Reset flag to allow refresh
            load_batch_searches();
          });

          // Batch list pagination handlers
          $("#batch_prev_page").on('click', function(e) {
            e.preventDefault();
            var currentPage = parseInt($(this).data('current-page')) || window.currentBatchPage || 1;
            var totalPages = parseInt($(this).data('total-pages')) || window.totalBatchPages || 1;
            
            if (currentPage > 1) {
              window.load_batch_searches(currentPage - 1);
            }
          });

          $("#batch_next_page").on('click', function(e) {
            e.preventDefault();
            var currentPage = parseInt($(this).data('current-page')) || window.currentBatchPage || 1;
            var totalPages = parseInt($(this).data('total-pages')) || window.totalBatchPages || 1;
            
            if (currentPage < totalPages) {
              window.load_batch_searches(currentPage + 1);
            }
          });

          // Reset filter tracking when search term changes
          $("#search_term").on('input', function() {
            var currentTerm = $(this).val().trim();
            // If search term changed significantly, reset filter tracking and clear filters
            if (window.lastLoadedFiltersSearchTerm && window.lastLoadedFiltersSearchTerm !== currentTerm) {
              window.lastLoadedFiltersSearchTerm = null;
              // Clear filters when search term changes
              $("#dynamic_filters_content .filters-content").empty();
              $("#dynamic_filters_content .filters-actions").hide();
            }
          });

          // Reset batch results when batch search name changes (indicates new search)
          $("#batch_search_name").on('input', function() {
            var currentName = $(this).val().trim();
            // If batch name changed, clear any existing results
            if (currentName !== window.lastBatchSearchName) {
              window.lastBatchSearchName = currentName;
              // Clear batch results when starting a new search
              $("#batch_results").html('').hide();
              $("#batch_progress").hide();
              
              // Also clear inputs if this is a completely new search
              if (currentName === '') {
                clear_batch_inputs_after_success();
              }
            }
          });

          // Filter functionality event handlers (Load Filters button removed - filters auto-load with search)

          // Apply filters event handler
          $("#btn_apply_filters").on('click', function(e) {
            e.preventDefault();
            apply_filters_search();
          });

          // Clear filters event handler
          $("#btn_clear_filters").on('click', function(e) {
            e.preventDefault();
            clear_all_filters();
          });

          // JSON Copy Button functionality
          $("#copy_json_sample").on('click', function(e) {
            e.preventDefault();
            
            var jsonText = $("#json_sample_content").text().trim();
            var button = $(this);
            var originalText = button.find('.copy-text').text();
            
            // Use modern clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
              navigator.clipboard.writeText(jsonText).then(function() {
                // Success feedback - switch to primary button style
                button.removeClass('button-secondary').addClass('button-primary copied');
                button.find('.copy-text').text('Copied!');
                
                // Reset after 2 seconds
                setTimeout(function() {
                  button.removeClass('button-primary copied').addClass('button-secondary');
                  button.find('.copy-text').text(originalText);
                }, 2000);
              }).catch(function() {
                // Fallback to legacy method
                copyToClipboardLegacy(jsonText, button, originalText);
              });
            } else {
              // Fallback for older browsers or non-secure contexts
              copyToClipboardLegacy(jsonText, button, originalText);
            }
          });
          
          // Legacy clipboard copy function
          function copyToClipboardLegacy(text, button, originalText) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
              document.execCommand('copy');
              // Success feedback - switch to primary button style
              button.removeClass('button-secondary').addClass('button-primary copied');
              button.find('.copy-text').text('Copied!');
              
              // Reset after 2 seconds
              setTimeout(function() {
                button.removeClass('button-primary copied').addClass('button-secondary');
                button.find('.copy-text').text(originalText);
              }, 2000);
            } catch (err) {
              console.error('Could not copy text: ', err);
              // Show error feedback
              button.find('.copy-text').text('Failed');
              setTimeout(function() {
                button.find('.copy-text').text(originalText);
              }, 2000);
            } finally {
              document.body.removeChild(textArea);
            }
          }

          // JSON Preview Accordion Toggle Function
          window.toggleJsonPreview = function() {
            var content = $('.json-preview-content');
            var arrow = $('.json-accordion-arrow');
            
            if (content.is(':visible')) {
              content.slideUp(200);
              arrow.removeClass('expanded');
            } else {
              content.slideDown(200);
              arrow.addClass('expanded');
            }
          };

          // Batch Search Button Validation - make it global so it can be called from other functions
          window.validateBatchSearchButton = function() {
            var method = $('input[name="batch_method"]:checked').val();
            var isValid = false;
            
            if (method === 'upload') {
              // For upload method, check if valid entries have been loaded from file
              // Don't just check if file is selected, but if valid JSON entries exist
              isValid = window.batchSearchEntries && window.batchSearchEntries.length > 0;
            } else if (method === 'builder') {
              // Check if search entries exist
              isValid = window.batchSearchEntries && window.batchSearchEntries.length > 0;
            }
            
            // Enable/disable button based on validation
            if (isValid) {
              $('#btn_batch_search').prop('disabled', false).removeClass('button-secondary').addClass('button-primary');
            } else {
              $('#btn_batch_search').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
            }
          };

          // File upload change handler - removed to prevent premature validation
          // Validation now happens inside handle_batch_file_upload after JSON validation

          // Batch Search Method Selection
          $('input[name="batch_method"]').on('change', function() {
            var method = $(this).val();
            if (method === 'upload') {
              $("#batch_upload_method").show();
              $("#batch_builder_method").hide();
            } else if (method === 'builder') {
              $("#batch_upload_method").hide();
              $("#batch_builder_method").show();
            }
            // Re-validate when method changes
            window.validateBatchSearchButton();
          });

          // Batch Search Builder Functionality
          $("#btn_load_batch_filters").on('click', function(e) {
            e.preventDefault();
            load_batch_filters();
          });

          $("#btn_add_search_entry").on('click', function(e) {
            e.preventDefault();
            add_search_entry();
          });

          $("#btn_batch_search").on('click', function(e) {
            e.preventDefault();
            start_batch_search();
          });

          $("#btn_clear_batch").on('click', function(e) {
            e.preventDefault();
            window.clear_batch_search();
          });

          // Allow Enter key to add search entry
          $("#new_search_term").on('keypress', function(e) {
            if (e.which == 13) {  // Enter key
              e.preventDefault();
              add_search_entry();
            }
          });

          // Allow Enter key to trigger search
          $("#search_term").on('keypress', function(e) {
            if (e.which == 13) {  // Enter key
              e.preventDefault();
              perform_directory_search();
            }
          });



          // Query Builder functionality for batch search
          function update_batch_search_query_preview() {
            var term1 = $("#batch_search_term_1").val().trim();
            var term2 = $("#batch_search_term_2").val().trim();
            var operator = $("#batch_search_operator").val();
            var combinedQuery = '';
            
            // Process individual terms before combining them
            var processedTerm1 = term1 ? process_search_term(term1) : '';
            var processedTerm2 = term2 ? process_search_term(term2) : '';
            
            if (processedTerm1 && processedTerm2) {
              // Add parentheses around terms that contain AND to ensure proper OR precedence
              var wrappedTerm1 = processedTerm1.includes(' AND ') ? '(' + processedTerm1 + ')' : processedTerm1;
              var wrappedTerm2 = processedTerm2.includes(' AND ') ? '(' + processedTerm2 + ')' : processedTerm2;
              combinedQuery = wrappedTerm1 + ' ' + operator + ' ' + wrappedTerm2;
            } else if (processedTerm1) {
              combinedQuery = processedTerm1;
            } else if (processedTerm2) {
              combinedQuery = processedTerm2;
            }
            
            $("#new_search_term").val(combinedQuery);
            
            return combinedQuery;
          }

          // Bind query builder events for batch search
          $("#batch_search_term_1, #batch_search_term_2, #batch_search_operator").on('input change', function() {
            var previousQuery = $("#new_search_term").val();
            var newQuery = update_batch_search_query_preview();
            
            // Clear filters if query changed (same logic as original search term change)
            if (window.batchFiltersLoaded && newQuery !== window.lastFilterSearchTerm) {
              $("#batch_dynamic_filters").empty();
              $("#batch_filters_section").hide();
              window.batchFiltersLoaded = false;
              window.lastFilterSearchTerm = '';
              
              if (newQuery.length > 0) {
                show_info_message('Search query changed. Please load filters again for the new search query.');
              }
            }
          });

          // Allow Enter key on batch query builder fields
          $("#batch_search_term_1, #batch_search_term_2").on('keypress', function(e) {
            if (e.which == 13) {  // Enter key
              e.preventDefault();
              update_batch_search_query_preview();
            }
          });

          // File upload handler for JSON files
          $("#batch_file").on('change', function() {
            // If no file selected, clear entries and validate button
            if (!this.files || this.files.length === 0) {
              window.batchSearchEntries = [];
              $("#generated_json").val('');
              update_search_entries_display(); // Update visual display
              window.validateBatchSearchButton();
              return;
            }
            
            handle_batch_file_upload(this);
          });
          
          // Initialize batch search button state after all functions are defined
          if ($('.network-directory-tabs').length > 0) {
            window.validateBatchSearchButton();
          }
          
        });

        function getParameterByName(name, url) {
          if (!url) url = window.location.href; // Use the current URL if no URL is provided
          name = name.replace(/[\[\]]/g, '\\$&'); // Escape the parameter name for regex
          var regex = new RegExp('[?&]' + name + '=([^&]*)');
          var results = regex.exec(url);
          var entityType = $("#entity_type").val();
      
          // If results are null or empty, return entityType value (if available) or empty string
          if (results === null || results[1] === '') {
              return entityType || '';
          }
      
          // Otherwise return the decoded URL parameter value
          return decodeURIComponent(results[1].replace(/\+/g, ' ').split('#')[0]);
      }
      

        $("#mapped_table").change(function(e){
          var $mapper_field = $("#mapper_table_field");
          var $mapper_filter_field = $("#mapper_table_filter_field");
          var $mapper_table_filter_data = $("#mapper_table_filter_data");
          var $selectedTableValue = this.value;
          $mapper_field.empty();
          $mapper_filter_field.empty();
          $mapper_table_filter_data.text = "";

          $mapper_field.append($("<option> </option>")
              .attr("value", "").text("Select data field"));
          if($selectedTableValue && $selectedTableValue != "")
          {
            var data = {
            action: 'wc_avatax_submit_map_perform',
            security: wc_avatax_admin.submit_map_nonce,
            param: 'InvoiceMapper'
          };
          jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
            var $filterTableRecord  = response["data"].find(ele => ele.main_table == $selectedTableValue);
              if ($filterTableRecord) {
                // Using Set to remove duplicates
                let uniqueValues = [...new Set($filterTableRecord["selected_fields"].split(","))];
                uniqueValues.forEach(c => {
                    $mapper_field.append(
                        $("<option/>")
                        .attr("value", c.trim())
                        .text(c.trim())
                    );
                });
                if ($filterTableRecord["isarray"] == true) {
                  $(".filter-fields").show();
                  $("#mapper_table_filter_field").append($("<option> </option>")
                    .attr("value", "").text("Select data field"));
                  
                  // Using Set to remove duplicates
                  let uniqueValues = [...new Set($filterTableRecord["selected_fields"].split(","))];
                  uniqueValues.forEach(c => {
                    $("#mapper_table_filter_field").append(
                      $("<option/>")
                      .attr("value", c.trim())
                      .text(c.trim())
                    );
                  });
                } else {
                  $("#mapper_table_filter_field").empty()
                  $(".filter-fields").hide();
                }
              }
            });
          }
        });

        if($('#tmpl-wc-avatax-alert-modal').length > 0){
        // Define a Backbone View for the alert
        var AlertView = Backbone.View.extend({
          tagName: 'div',
          className: 'alert-message',
          
          template: _.template(jQuery('#tmpl-wc-avatax-alert-modal').html()),
          
          events: {
              'click .modal-close': 'removeAlert'
          },
          
          initialize: function(options) {
              this.message = options.message || 'Table not found';
              this.render();
          },
          
          render: function() {
              this.$el.html(this.template({ message: this.message }));
              $('body').append(this.$el);

              document.body.classList.add('open');

              return this;
          },
          
          removeAlert: function() {
              // When closing the modal
              document.body.classList.remove('open');
              this.remove();
          }
        });

        $("#main_table").change(function() {
            $("#saveInfo").html("");
            // Get the selected value
            var selectedValue = $("#main_table").val();

            // Check if the field is empty
            if (!selectedValue || selectedValue.trim() === '') {
              var $el = $("#main_table_ref_field");
              var $eav_key = $("#eav_key_field");
              var $eav_value = $("#eav_value_field");

              $el.empty().append($("<option></option>")
                  .attr("value", "Select source table column")
                  .text("Select source table column"));

              $eav_key.empty().append($("<option></option>")
                  .attr("value", "Select column data key")
                  .text("Select column data key"));

              $eav_value.empty().append($("<option></option>")
                  .attr("value", "Select column data value")
                  .text("Select column data value"));

              return;
            }
            
            var data = {
                action: 'wc_avatax_submit_map_perform',
                security: wc_avatax_admin.submit_map_nonce,
                tablename: $("#main_table").val(),
                param: 'table_dependency'
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                if (response === 0) {
                    return;
                }
                
                if(response['data'])
                {
                    var $el = $("#main_table_ref_field");
                    var $eav_key = $("#eav_key_field");
                    var $eav_value = $("#eav_value_field");
                    $el.empty(); // remove old options
                    $eav_key.empty();
                    $eav_value.empty()
                    if(response['data'].length > 0)
                    {
                        $.each(response['data'], function (i, main_table_ref_field) {
                        $el.append($("<option> </option>")
                            .attr("value", main_table_ref_field).text(main_table_ref_field));
                        $eav_key.append($("<option> </option>")
                            .attr("value", main_table_ref_field).text(main_table_ref_field));
                        $eav_value.append($("<option> </option>")
                            .attr("value", main_table_ref_field).text(main_table_ref_field));
                        
                        });
                    }else{
                        // Usage of backbone alertView:
                        var alertView = new AlertView({
                          message: "Table not found"
                        });
                    }
                }
            });
          });

          $("#secondary_table").change(function() {
            // Check if default option is selected
              if ($("#secondary_table").val() === "") {
                var $el = $("#secondary_table_ref_field");
                $el.empty().append($("<option></option>")
                    .attr("value", "")
                    .text("Select reference table field"));
                return;
            }

            var data = {
                action: 'wc_avatax_submit_map_perform',
                security: wc_avatax_admin.submit_map_nonce,
                tablename: $("#secondary_table").val(),
                param: 'table_dependency'
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                if (response === 0) {
                    return;
                }
                
                if(response['data'])
                {
                    var $el = $("#secondary_table_ref_field");
                    $el.empty(); // remove old options
                    if(response['data'].length > 0)
                    {
                        $.each(response['data'], function (i, secondary_table_ref_field) {
                        $el.append($("<option> </option>")
                            .attr("value", secondary_table_ref_field).text(secondary_table_ref_field));
                        });
                    }else{
                        // Usage of backbone alertView:
                        var alertView = new AlertView({
                          message: "Table not found"
                        });
                    }
                }
            });
          });
        }
          function avatax_Block_UI() {
            $("#wc-avatax-block-UI").addClass("wc-avatax-blockUI");
            $("#wc-avatax-block-UI").addClass("blockOverlay");
            $("body").attr("style", "overflow: hidden;");
          }

          function avatax_UnBlock_UI() {
            $("#wc-avatax-block-UI").removeClass("wc-avatax-blockUI");
            $("#wc-avatax-block-UI").removeClass("wc-avatax-blockOverlay");
            $("body").attr("style", "overflow: auto;");
          }

      $(document).on("change", ".application-response-field-checkbox", function () {
          var $checkbox = $(this);
          var $card = $checkbox.closest(".application-response-card");
          if (!$card.length) {
              return;
          }
          var isChecked = $checkbox.is(":checked");
          var $status = $card.find(".application-response-card__status");

          if (isChecked) {
              $card
                  .removeClass("is-inactive").addClass("is-active")
                  .css({
                      "border-color": "#2271b1",
                      "background": "#f6f8fb",
                      "box-shadow": "0 0 0 1px #2271b1 inset"
                  });
              $status.css("color", "#2271b1").html("&#10003; Active");
              if (!$card.find(".application-response-card__dot").length) {
                  $card.prepend(
                      '<span class="application-response-card__dot" aria-hidden="true" ' +
                      'style="position:absolute;top:10px;right:12px;width:6px;height:6px;border-radius:50%;background:#2271b1;"></span>'
                  );
              }
          } else {
              $card
                  .removeClass("is-active").addClass("is-inactive")
                  .css({
                      "border-color": "#dcdcde",
                      "background": "#ffffff",
                      "box-shadow": "none"
                  });
              $status.css("color", "#787c82").text("Inactive");
              $card.find(".application-response-card__dot").remove();
          }
      });

          $("#btnSubmitMapper").click(function(e){
            e.preventDefault();
            
            // Function to show error message with timeout
            function showMessage(message, isError = true) {
              // Clear any existing timeout
              if (window.messageTimeout) {
                  clearTimeout(window.messageTimeout);
              }
            
              // Check if the message indicates success
              const isSuccess = message.toLowerCase().includes('mapping saved successfully');
              $("#mapper_message")
                  .css('color', isSuccess ? 'green' : 'red')
                  .text(message)
                  .show();
            
              // Set timeout only for success messages
              if (!isError) {
                  window.messageTimeout = setTimeout(function() {
                      $("#mapper_message").fadeOut('slow', function() {
                          $(this).hide().text('');
                      });
                  }, 10000);
              }
          }
        
            // Function to hide message
            function hideMessage() {
                if (window.messageTimeout) {
                    clearTimeout(window.messageTimeout);
                }
                $("#mapper_message").hide().text('');
            }

              // Application Response mode uses a different payload (just the four
              // checkbox flags) and skips the source-table validation entirely.
              if ($("#entity_type").val() === 'application_response') {
                  hideMessage();
                  avatax_Block_UI();
                  $("#btnSubmitMapper").addClass("spin").attr("disabled", "disabled");

                  function renderArMessage(text, isSuccess) {
                      if (window.arMessageTimeout) {
                          clearTimeout(window.arMessageTimeout);
                      }
                      var color = isSuccess ? '#4CAF50' : '#dc3545';
                      var icon = isSuccess ? '✓' : '✕';
                      var $msg = $("#mapper_message");
                      $msg.empty()
                          .css({color: color, display: 'inline-flex', 'align-items': 'center'})
                          .append($('<span>').css('margin-right', '5px').text(icon))
                          .append($('<span>').text(text))
                          .show();
                      window.arMessageTimeout = setTimeout(function () {
                          $msg.fadeOut('slow', function () {
                              $(this).hide().empty().css('display', 'none');
                          });
                      }, 10000);
                  }

                  var arMapping = {};
                  $(".application-response-field-checkbox").each(function () {
                      var fieldName = $(this).data('field');
                      if (fieldName) {
                          arMapping[fieldName] = $(this).is(':checked') ? 1 : 0;
                      }
                  });

                  jQuery.post(wc_avatax_admin.ajax_url, {
                      action: 'wc_avatax_submit_map_perform',
                      security: wc_avatax_admin.submit_map_nonce,
                      param: 'save_application_response_mapping',
                      arMapping: arMapping,
                      entity: 'application_response'
                  }, function (response) {
                      if (response && response.code !== 200) {
                          var hardErr = (response && response.error) ? response.error : 'Failed to save Application Response mapping.';
                          renderArMessage(hardErr, false);
                          return;
                      }
                      var humanMessage = (response && response.error_save) ? response.error_save : '';
                      var savedOk = !!(response && response.data && response.data.ccs_ok);

                      if (savedOk) {
                          renderArMessage(humanMessage || 'The selected data fields from WooCommerce are sent successfully to Avalara for mapping.', true);
                      } else {
                          renderArMessage(humanMessage || 'Failed to save Application Response mapping.', false);
                      }
                  }).fail(function (jqXHR, textStatus, errorThrown) {
                      console.error("AJAX Error:", textStatus, errorThrown);
                      renderArMessage('Network error occurred. Please check your connection and try again.', false);
                  }).always(function () {
                      $("#btnSubmitMapper").removeClass("spin").removeAttr("disabled");
                      avatax_UnBlock_UI();
                  });

                  return false;
              }
            
            // Check if main_table is empty
            if (!$("#main_table").val() || $("#main_table").val().trim() === '') {
                showMessage('Please select a Source table');
                resetFieldsToDefault();
                return false;
            } else {
                hideMessage();
            }
        
            avatax_Block_UI();
            $("#mapper_message").html("").hide();
            $("#btnSubmitMapper").addClass("spin").attr("disabled", "disabled");
        
            var filter_data = {
                table_type:             $("#table_type").val(),
                main_table:             $("#main_table").val(),
                main_table_ref_field:   $("#main_table_ref_field").val(),
                eav_key_field:          $("#eav_key_field").val(),
                eav_value_field:        $("#eav_value_field").val(),
                secondary_table:        $("#secondary_table").val(),
                secondary_table_ref_field:   $("#secondary_table_ref_field").val(),
                main_table_isarray:     $(".main_table_isarray:checked").val(),
                entity_type:            $("#entity_type").val()
            }
        
            var data = {
                action: 'wc_avatax_submit_map_perform',
                security: wc_avatax_admin.submit_map_nonce,
                filterInfo: filter_data,
                param: 'save_mapping',
                entity: getParameterByName('entity')
            };
        
            jQuery('body').trigger('processStart');
            
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
              if (response === 0) {
                  showMessage('Failed to process the request. Please try again.', true);
                  resetFieldsToDefault();
                  return;
              }
              const message = response['error_save']
              const isSuccess = message.toLowerCase().includes('mapping saved');
              
              if (isSuccess) {
                showMessage('Mapping saved successfully', false);  // Show success with timeout
                
              } else {
                showMessage(response['error_save'], true);  // Show error without timeout
                
              }
              
              $("#saveInfo").html(response['data']);
              refresh_tbl_mapper(response['records']);
              showSchemaTree(JSON.parse(response['schema']), JSON.parse(response['savedSchema']))
              refresh_mapped_table(response['mapperTables']);
              bind_delete_mapping();
              resetFieldsToDefault();
          })
          .fail(function(jqXHR, textStatus, errorThrown) {
              console.error("AJAX Error:", textStatus, errorThrown);
              showMessage('Error occurred while saving', true);
              resetFieldsToDefault();
          })
          .always(function() {
              $("#btnSubmitMapper").removeClass("spin").removeAttr("disabled");
              avatax_UnBlock_UI();
          });
      
          return false;
      });
      
      // Function to reset all fields to their default state
      function resetFieldsToDefault() {
          // Reset main table
          $("#main_table").val('').trigger('change');
          
          // Reset main table reference field
          $("#main_table_ref_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select source table column"));
          
          // Reset EAV key field
          $("#eav_key_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select column data key"));
          
          // Reset EAV value field
          $("#eav_value_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select column data value"));
          
          // Reset secondary table
          $("#secondary_table").val("");
          
          // Reset secondary table reference field
          $("#secondary_table_ref_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select reference table field"));
          
          // Reset array checkbox
          $(".main_table_isarray").prop('checked', false);
      }
        
      
          function convertFormToJSON(form) {
            return $(form)
              .serializeArray()
              .reduce(function (json, { name, value }) {
                json[name] = value;
                return json;
              }, {});
          };

          
          if(wc_avatax_admin_jsontree){
            showSchemaTree(JSON.parse(wc_avatax_admin_jsontree.schema), JSON.parse(wc_avatax_admin_jsontree.savedSchema));
          }
          
          function showSchemaTree(json_obj, selected_nodes){
            if($('#ulSchema').length > 0){
            $("#ulSchema").jsontree({
              json: json_obj,
              selected_nodes: selected_nodes,
              expand_default: true,
            });
            }
          }

          $("#ulSchema").jsontree("getSelectedItemPaths");

          if($('#tmpl-wc-avatax-confirmation-modal').length > 0){
          const ConfirmationView = Backbone.View.extend({
            tagName: 'div',
            className: 'confirmation-dialog',
            
            template: _.template(jQuery('#tmpl-wc-avatax-confirmation-modal').html()),
        
            events: {
                'click .btn-confirm': 'onConfirm',
                'click .modal-close': 'onCancel'
            },
        
            initialize: function() {
                this.render();
            },
        
            render: function() {
                this.$el.html(this.template());
                $('body').append(this.$el);
                return this;
            },
        
            onConfirm: function() {
              // Get the save button
              const saveButton = $("#btnSchemaSave");
              
              // Enable loading state
              saveButton.addClass("spin").prop("disabled", true);
              
              var nodes = $("#ulSchema").jsontree("getSelectedItemPaths");
              var data = {
                  action: 'wc_avatax_submit_map_perform',
                  security: wc_avatax_admin.submit_map_nonce,
                  columns: nodes,
                  param: 'save_schema',
                  entity: getParameterByName('entity')
              };
          
              jQuery.post(wc_avatax_admin.ajax_url, data)
                  .done(function(response) {
                      if (response === 0 || response.error) {
                          $("#btnSchemaSave").after(
                              '<div id="schema_message" style="margin-top:10px;color:#dc3545;display:flex;align-items:center;">' +
                              '<span style="margin-right:5px">✕</span>' +
                              '<span>Failed to send data fields to Avalara. Please try again.</span>' +
                              '</div>'
                          );
                      } else {
                          $("#btnSchemaSave").after(
                              '<div id="schema_message" style="margin-top:10px;color:#4CAF50;display:flex;align-items:center;">' +
                              '<span style="margin-right:5px">✓</span>' +
                              '<span>The selected data fields from WooCommerce are sent successfully to Avalara for mapping.</span>' +
                              '</div>'
                          );
                      }
                  })
                  .fail(function() {
                      $("#btnSchemaSave").after(
                          '<div id="schema_message" style="margin-top:10px;color:#dc3545;display:flex;align-items:center;">' +
                          '<span style="margin-right:5px">✕</span>' +
                          '<span>Network error occurred. Please check your connection and try again.</span>' +
                          '</div>'
                      );
                  })
                  .always(function() {
                      // Disable loading state
                      saveButton.removeClass("spin").prop("disabled", false);
                      
                      // Remove message after delay
                      setTimeout(function() {
                          $("#schema_message").fadeOut('slow', function() {
                              $(this).remove();
                          });
                      }, 10000);
                  });
          
              this.remove();
          },
        
            onCancel: function() {
                this.remove();
            }
          });

          $("#btnSchemaSave").click(function(e){
            e.preventDefault();
            
            // Remove any existing messages
            $("#schema_message").remove();

            // Show confirmation dialog
            new ConfirmationView();

            return false;
        });
      }

          $("#btnSchemaSelectAll").click(function(){
            $("#ulSchema").jsontree("selectAll");
          })

          $("#btnSchemaUnSelectAll").click(function(){
            $("#ulSchema").jsontree("unSelectAll");
          })

          $("#treeNodeSearch").on("keyup change", function(){
            $("#ulSchema").jsontree("expandAll");
            var key = $(this).val();
            if(key !== ""){
              $("#ulSchema .tree-item").hide();
              
              $("#ulSchema .tree-item[data-value*='"+key+"']").each(function(){ $(this).show();});
            }
            else{
              $("#ulSchema .tree-item").show();
              $("#ulSchema").jsontree("expandAll");
            }
          })
          $(document).on('click', '.rowfy-addrow', function(){
            let rowfyable = $(this).closest('table');
            let currentLast = $('tbody tr:last', rowfyable).prev();
            let lastRow = $('tbody tr:last', rowfyable).prev().clone();

            $('input', lastRow).val('');
            $(lastRow).insertAfter(currentLast);
            $(this).removeClass('rowfy-addrow btn-success').addClass('rowfy-deleterow btn-danger').text('-');
          });

          $(document).on('click', '.rowfy-deleterow', function(){
            $(this).closest('tr').remove();
          });          

          function show_content(index) {
            // Make the content visible
            $('.elr_container.tabs > .content.visible').removeClass('visible');
            $('.elr_container.tabs > .content:nth-of-type(' + (index + 1) + ')').addClass('visible');
          
            // Set the tab to selected
            $('.elr_container.tabs > nav > a.selected').removeClass('selected');
            $('.elr_container.tabs > nav > a:nth-of-type(' + (index + 1) + ')').addClass('selected');
          }

          // Network Directory tab functionality
          function show_network_directory_content(index) {
            // Make the content visible for network directory tabs
            $('.network-directory-tabs .network-directory-tab-content.visible').removeClass('visible');
            $('.network-directory-tabs .network-directory-tab-content:nth-of-type(' + (index + 1) + ')').addClass('visible');
            // Set the tab to selected
            $('.network-directory-nav .network-directory-tab.selected').removeClass('selected');
            $('.network-directory-nav .network-directory-tab:nth-of-type(' + (index + 1) + ')').addClass('selected');
            
            // If this is the Batch Job Status tab (index 2), load batch searches only if not already loaded
            if (index === 2) {
              // Check if batch searches are already loaded
              if (window.batchSearchesLoaded) {
                // Data is already loaded, don't refresh
                return;
              }
              
              // Not loaded yet, load batch searches
              load_batch_searches();
            }
          }

          // Dynamic color generation for consistent badge colors
          window.badgeColorCache = {}; // Cache for consistent colors
          
          function getBadgeColor(value, type) {
            if (!value) return { background: '#f0f6fc', color: '#1d2327' }; // Default
            
            var cacheKey = type + '_' + value.toLowerCase();
            
            // Return cached color if exists
            if (window.badgeColorCache[cacheKey]) {
              return window.badgeColorCache[cacheKey];
            }
            
            // Generate new color for this value
            var color = generateConsistentColor(value, type);
            
            // Cache the color for consistency
            window.badgeColorCache[cacheKey] = color;
            
            return color;
          }
          
          function generateConsistentColor(value, type) {
            // Create a hash from the value to ensure consistent colors
            var hash = 0;
            for (var i = 0; i < value.length; i++) {
              var char = value.charCodeAt(i);
              hash = ((hash << 5) - hash) + char;
              hash = hash & hash; // Convert to 32-bit integer
            }
            
            // Use absolute value to ensure positive numbers
            hash = Math.abs(hash);
            
                         // Predefined color palettes for better visual appeal
             var colorPalettes = {
               network: [
                 { background: '#e7f5ff', color: '#0066cc', border: '2px solid #bae3ff' },      // Blue
                 { background: '#f0f9ff', color: '#0369a1', border: '2px solid #7dd3fc' },      // Dark Blue
                 { background: '#fef3c7', color: '#92400e', border: '2px solid #f9d33f' },      // Yellow
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #34d399' },      // Green
                 { background: '#fdf2f8', color: '#be185d', border: '2px solid #f472b6' },      // Pink
                 { background: '#fefce8', color: '#a16207', border: '2px solid #fbbf24' },      // Light Yellow
                 { background: '#f0f9ff', color: '#0c4a6e', border: '2px solid #60a5fa' },      // Navy Blue
                 { background: '#fef3c7', color: '#92400e', border: '2px solid #fb923c' },      // Orange
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #10b981' },      // Mint Green
                 { background: '#fdf2f8', color: '#be185d', border: '2px solid #ec4899' },      // Rose
                 { background: '#fefce8', color: '#a16207', border: '2px solid #f59e0b' },      // Amber
                 { background: '#f0f9ff', color: '#0c4a6e', border: '2px solid #bae3ff' },      // Slate Blue
                 { background: '#fef3c7', color: '#92400e', border: '2px solid #fbbf24' },      // Warm Yellow
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #6ee7b7' },      // Emerald
                 { background: '#fdf2f8', color: '#be185d', border: '2px solid #f9a8d4' },      // Magenta
                 { background: '#fefce8', color: '#a16207', border: '2px solid #fcd34d' }       // Golden
               ],
               country: [
                 { background: '#fef3c7', color: '#92400e', border: '2px solid #fbbf24' },      // Yellow
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #34d399' },      // Green
                 { background: '#f0f9ff', color: '#0c4a6e', border: '2px solid #60a5fa' },      // Navy Blue
                 { background: '#fdf2f8', color: '#be185d', border: '2px solid #ec4899' },      // Pink
                 { background: '#fefce8', color: '#a16207', border: '2px solid #f59e0b' },      // Light Yellow
                 { background: '#f0f9ff', color: '#0369a1', border: '2px solid #7dd3fc' },      // Dark Blue
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #10b981' },      // Mint Green
                 { background: '#fdf2f8', color: '#be185d', border: '2px solid #f9a8d4' },      // Rose
                 { background: '#fefce8', color: '#a16207', border: '2px solid #fcd34d' },      // Amber
                 { background: '#f0f9ff', color: '#0c4a6e', border: '2px solid #bae3ff' },      // Slate Blue
                 { background: '#fef3c7', color: '#92400e', border: '2px solid #fbbf24' },      // Warm Yellow
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #6ee7b7' },      // Emerald
                 { background: '#fdf2f8', color: '#be185d', border: '2px solid #f9a8d4' },      // Magenta
                 { background: '#fefce8', color: '#a16207', border: '2px solid #fcd34d' },      // Golden
                 { background: '#f0f9ff', color: '#0369a1', border: '2px solid #7dd3fc' },      // Dark Blue
                 { background: '#ecfdf5', color: '#047857', border: '2px solid #10b981' }       // Mint Green
               ]
             };
            
            var palette = colorPalettes[type] || colorPalettes.network;
            var colorIndex = hash % palette.length;
            
            return palette[colorIndex];
          }

          // Helper function to process search terms for API calls
          function process_search_term(searchTerm) {
            if (!searchTerm) return searchTerm;
            // Replace multiple consecutive spaces with single space first
            var cleanedTerm = searchTerm.replace(/\s+/g, ' ');
            // Replace spaces with " AND "
            return cleanedTerm.replace(/ /g, ' AND ');
          }

          // Network Directory Search functionality
          function perform_directory_search(page = 1) {
            var search_term = $("#search_term").val().trim();
            
            if (search_term == "") {
              alert("Please enter a search term");
              return false;
            }
            
            // Check if this is a new search term (different from last loaded filters)
            var isNewSearchTerm = !window.lastLoadedFiltersSearchTerm || window.lastLoadedFiltersSearchTerm !== search_term;
              
            $("#btn_search_directory").addClass("spin").attr("disabled", "disabled");
            
            // Show the search results layout immediately with loading message
            $("#search_results_layout").show();
            $("#filters_sidebar").show();
            $("#search_results").html('<div class="loading" style="padding: 30px; text-align: center; border: 1px solid #0073aa; background: #f0f6fc; margin: 10px 0; color: #0073aa;"><div style="font-size: 16px; margin-bottom: 10px;"><strong class="actionButton spin" style="display:inline-block;">Loading...</strong></div></div>');
            
            // Show loading state in filters section for new search terms
            if (isNewSearchTerm) {
              $("#dynamic_filters_content").show();
              $("#dynamic_filters_content .filters-content").html('<div class="filters-loading" style="padding: 20px; text-align: center; color: #666;"><div><span class="spin actionButton" style="display: inline-block;"></span></div><div style="font-size: 14px;"><strong>Loading Filters...</strong></div></div>');
              // Hide filter action buttons while loading
              $("#dynamic_filters_content .filters-actions").hide();
            }
            
            var data = {
              nonce: wc_avatax_admin.disconnect_nonce,
              action: 'wc_avatax_network_directory_search',
              search_term: process_search_term(search_term),
              page: page,
              per_page: 10
            };

            // Collect all dynamic filter values from the filters content
            $("#dynamic_filters_content .filters-content select").each(function() {
              var filterId = $(this).attr('id');
              var filterValue = $(this).val();
              if (filterId && filterValue) {
                data[filterId] = filterValue;
              }
            });
            
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
              $("#btn_search_directory").removeClass("spin").removeAttr("disabled");
              

              
              if (response.code == 200) {
                // Layout is already shown at the start of search
                
                // Clear only the loading state, preserve existing filters for pagination
                $("#dynamic_filters_content .filters-loading").remove();
                
                // Only render filters if this is a new search term (preserve filter selections for pagination/filtering)
                if (isNewSearchTerm && response.facets && Array.isArray(response.facets) && response.facets.length > 0) {
                  render_dynamic_filters(response.facets);
                  // Remember this search term for which we loaded filters
                  window.lastLoadedFiltersSearchTerm = search_term;
                  // Show the dynamic filters content and action buttons
                  $("#dynamic_filters_content").show();
                  $("#dynamic_filters_content .filters-actions").show();
                } else if (!isNewSearchTerm) {
                  // For pagination, ensure filters and action buttons are visible
                  $("#dynamic_filters_content").show();
                  $("#dynamic_filters_content .filters-actions").show();
                  
                  // Restore filter selections that were preserved during pagination
                  restore_filter_selections();
                }
                
                var html = '<div class="search-results-header">';
                var total_results = response.pagination ? response.pagination.total_results : (response.recordSetCount || 0);
                html += '<h5>Search Results (' + total_results + ' found)</h5>';
                html += '</div>';
                
                if (response.data && response.data.length > 0) {
                  html += '<table class="wp-list-table widefat ndi-identifiers-table avatax striped">';
                  html += '<thead><tr><th>Company Name</th><th>Network</th><th>Country</th><th>Identifiers</th></tr></thead>';
                  html += '<tbody>';
                  
                  response.data.forEach(function(item) {
                    // Extract data from the API response structure
                    var company_name = item.name || '';
                    var network = item.network || '';
                    var country = (item.addresses && item.addresses.length > 0) ? item.addresses[0].country : '';
                    
                    // Generate colors for consistent badge styling
                    var networkColors = getBadgeColor(network, 'network');
                    var countryColors = getBadgeColor(country, 'country');
                    
                    // Get identifiers array
                    var identifiers = item.identifiers || [];
                    
                    if (identifiers.length === 0) {
                      // No identifiers - single row
                      html += '<tr>';
                      html += '<td><div class="company-name">' + company_name + '</div><div class="company-id">ID: ' + item.id + '</div></td>';
                      html += '<td><span class="network-badge" style="background-color: ' + networkColors.background + '; color: ' + networkColors.color + '; border: ' + (networkColors.border || 'none') + ';">' + network + '</span></td>';
                      html += '<td><span class="country-badge" style="background-color: ' + countryColors.background + '; color: ' + countryColors.color + '; border: ' + (countryColors.border || 'none') + ';">' + country + '</span></td>';
                      html += '<td><span class="no-identifiers">No identifiers available</span></td>';
                      html += '</tr>';
                    } else if (identifiers.length === 1) {
                      // Single identifier - simple display without accordion
                      html += '<tr class="company-row" data-company-id="' + item.id + '">';
                      html += '<td><div class="company-name">' + company_name + '</div><div class="company-id">ID: ' + item.id + '</div></td>';
                      html += '<td><span class="network-badge" style="background-color: ' + networkColors.background + '; color: ' + networkColors.color + '; border: ' + (networkColors.border || 'none') + ';">' + network + '</span></td>';
                      html += '<td><span class="country-badge" style="background-color: ' + countryColors.background + '; color: ' + countryColors.color + '; border: ' + (countryColors.border || 'none') + ';">' + country + '</span></td>';
                      
                      // Identifiers column - single identifier display
                      html += '<td>';
                      
                      var identifier = identifiers[0];
                      var identifierName = identifier.name || '';
                      var identifierValue = identifier.value || '';
                      
                      // Clean up identifier name for display
                      var cleanIdentifierName = identifierName;
                      if (identifierName.includes('urn:avalara:')) {
                        cleanIdentifierName = identifierName.split(':').pop().toUpperCase();
                      }
                      
                      html += '<div class="identifier-row"><span class="identifier-name">' + cleanIdentifierName + '</span><span class="identifier-value">' + identifierValue + '</span></div>';
                      
                      html += '</td>'; // Close td
                      html += '</tr>';
                                         } else {
                       // Multiple identifiers - use accordion UI
                       html += '<tr class="company-row" data-company-id="' + item.id + '">';
                       html += '<td><div class="company-name">' + company_name + '</div><div class="company-id">ID: ' + item.id + '</div></td>';
                       html += '<td><span class="network-badge" style="background-color: ' + networkColors.background + '; color: ' + networkColors.color + '; border: ' + (networkColors.border || 'none') + ';">' + network + '</span></td>';
                       html += '<td><span class="country-badge" style="background-color: ' + countryColors.background + '; color: ' + countryColors.color + '; border: ' + (countryColors.border || 'none') + ';">' + country + '</span></td>';
                       
                       // Identifiers column with expand/collapse functionality
                       html += '<td>';
                       
                       // Hidden accordion content - all identifiers will be shown here
                       html += '<div class="identifiers-accordion" id="accordion-' + item.id + '" style="display: none;">';
                       
                       // Show all identifiers in accordion (starting from index 0)
                       for (var i = 0; i < identifiers.length; i++) {
                         var identifier = identifiers[i];
                         var idName = identifier.name || '';
                         var idValue = identifier.value || '';
                         
                         // Clean up identifier name for display
                         var cleanIdName = idName;
                         if (idName.includes('urn:avalara:')) {
                           cleanIdName = idName.split(':').pop().toUpperCase();
                         }
                         
                         html += '<div class="identifier-row">';
                         html += '<span class="identifier-name">' + cleanIdName + '</span>';
                         html += '<span class="identifier-value">' + idValue + '</span>';
                         html += '</div>';
                       }
                       
                       html += '</div>'; // Close identifiers-accordion
                       
                       // Multiple identifiers - show only toggle button initially
                       html += '<div class="identifiers-summary">';
                       html += '<span class="toggle-identifiers" data-company-id="' + item.id + '">';
                       html += '<span class="identifier-count">' + identifiers.length + ' identifier' + (identifiers.length > 1 ? 's' : '') + '</span>';
                       html += '<span class="dashicons dashicons-arrow-down-alt2"></span>';
                       html += '</span>';
                       html += '</div>';
                       
                       html += '</td>'; // Close td
                       html += '</tr>';
                    }
                  });
                  
                  html += '</tbody></table>';
                  
                  // Add pagination
                  if (response.pagination.total_pages > 1) {
                    html += '<div class="search-pagination" style="margin: 20px 0 0; padding: 15px; background: #FFFFFF; border: 1px solid #c3c4c7; border-radius: 4px; box-shadow: 0 1px 1px rgba(0, 0, 0, .04);">';
                    html += '<div class="pagination-wrapper" style="display: flex;justify-content: space-between;align-items: center;flex-wrap: wrap;gap: 10px;width: 100%;">';
                    
                    // Left side - pagination info
                    var startItem = ((response.pagination.current_page - 1) * response.pagination.per_page) + 1;
                    var endItem = Math.min(response.pagination.current_page * response.pagination.per_page, response.pagination.total_results);
                    html += '<div class="pagination-info" style="float left;">';
                    html += '<span>Showing ' + startItem + '-' + endItem + ' of ' + response.pagination.total_results + ' results</span>';
                    html += '</div>';
                    
                    // Right side - pagination controls
                    html += '<div class="pagination-controls" style="float: right; display: flex; align-items: center; gap: 5px;">';
                    
                    // Previous button
                    if (response.pagination.has_prev) {
                      html += '<button type="button" class="button search-page" data-page="' + (response.pagination.current_page - 1) + '">Previous</button>';
                    } else {
                      html += '<button type="button" class="button" disabled>Previous</button>';
                    }
                    
                    // Page numbers
                    var currentPage = response.pagination.current_page;
                    var totalPages = response.pagination.total_pages;
                    var startPage = Math.max(1, currentPage - 2);
                    var endPage = Math.min(totalPages, currentPage + 2);
                    
                    for (var i = startPage; i <= endPage; i++) {
                      if (i === currentPage) {
                        html += '<strong class="button button-primary current-page-number">' + i + '</strong>';
                      } else {
                        html += '<a href="#" class="search-page-number button" data-page="' + i + '">' + i + '</a>';
                      }
                    }
                    
                    // Next button
                    if (response.pagination.has_next) {
                      html += '<button type="button" class="button search-page" data-page="' + (response.pagination.current_page + 1) + '">Next</button>';
                    } else {
                      html += '<button type="button" class="button" disabled>Next</button>';
                    }
                    
                    html += '</div></div></div>';
                  }
                } else {
                  html += '<div class="no-results" style="padding: 20px; text-align: center; border: 1px solid #ddd; background: #f9f9f9; margin: 10px 0;">';
                  html += '<p style="font-size: 16px; margin: 0 0 10px 0;"><strong>No results found</strong></p>';
                  html += '<p class="description" style="margin: 0; color: #666;">Please try a different search term.</p>';
                  html += '</div>';
                }
                
                $("#search_results").html(html);
                
                // Bind pagination click events
                $(document).off('click', '.search-page').on('click', '.search-page', function() {
                  var page = $(this).data('page');
                  // Show loading message immediately for pagination
                  var search_term = $("#search_term").val().trim();
                  $("#search_results").html('<div class="loading" style="padding: 30px; text-align: center; border: 1px solid #0073aa; background: #f0f6fc; margin: 10px 0; color: #0073aa;"><div style="font-size: 16px; margin-bottom: 10px;"><strong>🔍 Loading page ' + page + '...</strong></div><small>Please wait while we load more results for "' + search_term + '"</small></div>');
                  
                  // Preserve filter selections during pagination
                  preserve_filter_selections();
                  
                  perform_directory_search(page);
                });
                
                // Bind page number click events
                $(document).off('click', '.search-page-number').on('click', '.search-page-number', function(e) {
                  e.preventDefault();
                  var page = $(this).data('page');
                  // Show loading message immediately for pagination
                  var search_term = $("#search_term").val().trim();
                  $("#search_results").html('<div class="loading" style="padding: 30px; text-align: center; border: 1px solid #0073aa; background: #f0f6fc; margin: 10px 0; color: #0073aa;"><div style="font-size: 16px; margin-bottom: 10px;"><strong>🔍 Loading page ' + page + '...</strong></div><small>Please wait while we load more results for "' + search_term + '"</small></div>');
                  
                  // Preserve filter selections during pagination
                  preserve_filter_selections();
                  
                  perform_directory_search(page);
                });

                // Bind accordion toggle events for identifiers
                $(document).off('click', '.toggle-identifiers').on('click', '.toggle-identifiers', function(e) {
                  e.preventDefault();
                  var companyId = $(this).data('company-id');
                  var accordion = $('#accordion-' + companyId);
                  var icon = $(this).find('.dashicons');
                  
                  if (accordion.is(':visible')) {
                    // Collapse accordion
                    accordion.slideUp(300);
                    icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
                  } else {
                    // Expand accordion
                    accordion.slideDown(300);
                    icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
                  }
                });
                
              } else {
                // Layout is already shown at the start of search
                // Clear only loading state, preserve existing filters
                $("#dynamic_filters_content .filters-loading").remove();
                
                $("#search_results").html('<div class="notice notice-error" style="padding: 20px; text-align: center; background: #fff2f2; margin: 10px 0;"><strong>Search failed: ' + (response.message || 'Unknown error') + '</strong></div>');
              }
            }).fail(function(xhr, status, error) {
              $("#btn_search_directory").removeClass("spin").removeAttr("disabled");
              
              var errorMessage = 'Network error';
              if (xhr.status) {
                errorMessage += ' (HTTP ' + xhr.status + ')';
              }
              if (xhr.responseText) {
                try {
                  var errorResponse = JSON.parse(xhr.responseText);
                  if (errorResponse.message) {
                    errorMessage = errorResponse.message;
                  }
                } catch (e) {
                  // Response is not JSON, use status text or response text
                  if (xhr.statusText) {
                    errorMessage += ': ' + xhr.statusText;
                  }
                }
              }
              
              // Layout is already shown at the start of search
              // Clear only loading state, preserve existing filters
              $("#dynamic_filters_content .filters-loading").remove();
              
              $("#search_results").html('<div class="notice notice-error" style="padding: 20px; text-align: center; background: #fff2f2; margin: 10px 0;"><strong>Search failed: ' + errorMessage + '</strong><br><small>Please check your internet connection and try again.</small></div>');
            });
          }

          // Clear search results and hide sidebar
          function clear_search_results() {
            $("#search_term").val("");
            $("#search_results").html("");
            $("#search_results_layout").hide();
            
            // Clear filter selections but preserve filter structure
            $("#dynamic_filters_content .filters-content select").val("");
            
            // Clear the filters content completely
            $("#dynamic_filters_content .filters-content").empty();
            
            // Hide filter action buttons
            $("#dynamic_filters_content .filters-actions").hide();
            
            // Hide the filters sidebar since no search results are shown
            $("#filters_sidebar").hide();
            
            // Reset filter tracking variables
            window.lastLoadedFiltersSearchTerm = null;
            
            // Clear all messages
            clear_all_messages();
            
            // Clear badge color cache for fresh color assignments
            window.badgeColorCache = {};
          }

          // Batch Search functionality - placeholder for future implementation
          function perform_batch_search() {
            alert('Batch search functionality will be implemented in a future update.');
          }

          function update_progress_bar(percentage) {
            $("#batch_progress .progress-fill").css('width', percentage + '%');
            $("#progress_count").text(Math.floor(percentage));
          }

          // Load filter options from external API
          // load_filter_options function removed - filters now auto-load with search results

          // Render filters dynamically based on API response
          function render_dynamic_filters(filterData) {
            var filtersContainer = $("#dynamic_filters_content .filters-content");
            filtersContainer.empty(); // Clear existing filters
            
            // Handle new taxonomy structure (array of taxonomies)
            if (filterData && Array.isArray(filterData) && filterData.length > 0) {
              filterData.forEach(function(taxonomy) {
                var filterHtml = create_filter_field(taxonomy);
                filtersContainer.append(filterHtml);
              });
              
              // Show dynamic filters content and action buttons
              $("#dynamic_filters_content").show();
              $("#dynamic_filters_content .filters-actions").show();
            } else if (filterData.filters && filterData.filters.length > 0) {
              // Fallback for old structure
              filterData.filters.forEach(function(filter) {
                var filterHtml = create_filter_field(filter);
                filtersContainer.append(filterHtml);
              });
              
              // Show dynamic filters content and action buttons
              $("#dynamic_filters_content").show();
              $("#dynamic_filters_content .filters-actions").show();
            } else {
              // Show "no filters available" message in the filters content area
              filtersContainer.html('<div class="no-filters" style="padding: 15px; text-align: center; color: #666; font-style: italic;"><div style="margin-bottom: 5px;">📋</div><div>No filters available</div><small>Filters will appear here when available</small></div>');
              $("#dynamic_filters_content").show();
              // Hide action buttons when no filters are available
              $("#dynamic_filters_content .filters-actions").hide();
            }
          }

          // Create individual filter field HTML
          function create_filter_field(taxonomy) {
            var fieldHtml = '<div class="filter-field">';
            
            // Handle new taxonomy structure
            if (taxonomy.taxonomyId && taxonomy.taxonomyName && taxonomy.values) {
              fieldHtml += '<label>' + taxonomy.taxonomyName + '</label>';
              fieldHtml += '<select id="filter_' + taxonomy.taxonomyId + '">';
              fieldHtml += '<option value="">All ' + taxonomy.taxonomyName + '</option>';
              
              if (taxonomy.values && taxonomy.values.length > 0) {
                taxonomy.values.forEach(function(valueObj) {
                  var valueArray = valueObj.value.split(':');
                  var displayValue = valueArray[valueArray.length - 1];
                  // Skip empty values
                  if (displayValue && displayValue.trim() !== '') {
                    // Truncate very long text to prevent overflow
                    var truncatedValue = displayValue.length > 35 ? displayValue.substring(0, 32) + '...' : displayValue;
                    var displayText = truncatedValue;
                    fieldHtml += '<option value="' + displayValue + '" title="' + displayValue + '">' + displayText + '</option>';
                  }
                });
              }
              
              fieldHtml += '</select>';
            } 
            // Fallback for old structure
            else if (taxonomy.key && taxonomy.label) {
              fieldHtml += '<label>' + taxonomy.taxonomyName + '</label>';
              fieldHtml += '<select id="filter_' + taxonomy.key + '">';
              fieldHtml += '<option value="">' + (taxonomy.placeholder || 'Select ' + taxonomy.label) + '</option>';
              
              if (taxonomy.options && taxonomy.options.length > 0) {
                taxonomy.options.forEach(function(option) {
                  fieldHtml += '<option value="' + option.value + '">' + option.label + '</option>';
                });
              }
              
              fieldHtml += '</select>';
            }
            
            fieldHtml += '</div>';
            return fieldHtml;
          }



          // Apply filters and trigger search
          function apply_filters_search() {
            var search_term = $("#search_term").val().trim();
            
            if (search_term == "") {
              alert("Please enter a search term before applying filters");
              $("#search_term").focus();
              return false;
            }
            
            // Trigger search with current search term and applied filters
            perform_directory_search(1); // Start from page 1 when applying filters
          }

          // Clear all filters
          function clear_all_filters() {
            $("#dynamic_filters_content .filters-content select").val("");
            show_success_message('All filters cleared');
            
            // Optionally trigger search again with cleared filters if search term exists
            var search_term = $("#search_term").val().trim();
            if (search_term != "") {
              setTimeout(function() {
                perform_directory_search(1);
              }, 500); // Small delay to show the success message first
            }
          }

          // Preserve filter selections during pagination
          function preserve_filter_selections() {
            // Store current filter selections in a temporary variable
            window.tempFilterSelections = {};
            $("#dynamic_filters_content .filters-content select").each(function() {
              var filterId = $(this).attr('id');
              var filterValue = $(this).val();
              if (filterId && filterValue) {
                window.tempFilterSelections[filterId] = filterValue;
              }
            });
          }

          // Restore filter selections after pagination
          function restore_filter_selections() {
            if (window.tempFilterSelections) {
              Object.keys(window.tempFilterSelections).forEach(function(filterId) {
                var filterValue = window.tempFilterSelections[filterId];
                $("#" + filterId).val(filterValue);
              });
              // Clear temporary storage
              delete window.tempFilterSelections;
            }
          }

          // Show success message
          window.show_success_message = function(message) {
            var messageDiv = $('<div class="notice notice-success is-dismissible" style="margin: 10px 0;"><p>' + message + '</p></div>');
            // Insert after the active tab content
            var activeTab = $('.network-directory-tab-content:visible');
            if (activeTab.length) {
              activeTab.find('h4').first().after(messageDiv);
            } else {
              $('.search-section h4, .batch-search-section h4, .batch-status-section h4').first().after(messageDiv);
            }
            setTimeout(function() {
              messageDiv.fadeOut(function() {
                $(this).remove();
              });
            }, 3000);
          };

          // Show error message
          window.show_error_message = function(message) {
            var messageDiv = $('<div class="notice notice-error is-dismissible" style="margin: 10px 0;" tabindex="-1"><p>' + message + '</p></div>');
            // Insert after the active tab content
            var activeTab = $('.network-directory-tab-content:visible');
            if (activeTab.length) {
              activeTab.find('h4').first().after(messageDiv);
            } else {
              $('.search-section h4, .batch-search-section h4, .batch-status-section h4').first().after(messageDiv);
            }
            
            // Focus on the error message and scroll to it
            setTimeout(function() {
              messageDiv.focus();
              messageDiv[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
            
            setTimeout(function() {
              messageDiv.fadeOut(function() {
                $(this).remove();
              });
            }, 5000);
          };

          // Show info message
          window.show_info_message = function(message) {
            var messageDiv = $('<div class="notice notice-info is-dismissible" style="margin: 10px 0;"><p>' + message + '</p></div>');
            // Insert after the active tab content
            var activeTab = $('.network-directory-tab-content:visible');
            if (activeTab.length) {
              activeTab.find('h4').first().after(messageDiv);
            } else {
              $('.search-section h4, .batch-search-section h4, .batch-status-section h4').first().after(messageDiv);
            }
            setTimeout(function() {
              messageDiv.fadeOut(function() {
                $(this).remove();
              });
            }, 4000);
          };

          // Clear all messages
          window.clear_all_messages = function() {
            // Remove all notice messages
            $('.notice.notice-success, .notice.notice-error, .notice.notice-info').remove();
          };

          // Clear batch results and messages
          window.clear_batch_results_and_messages = function() {
            // Clear batch results
            $("#batch_results").html('').hide();
            $("#batch_progress").hide();
            // Clear all messages
            clear_all_messages();
          };




          $('#entity_type').on('change', function() {
            // Get current URL
            var currentUrl = window.location.href;
            
            // Get selected value
            var selectedValue = $(this).val();
            
            // Store the current active sub-tab index (Data selector, Custom fields, etc.)
            var activeTabIndex = $('.elr_container.tabs > nav > a.selected').index();
            // Also check visible content if no selected tab
            if (activeTabIndex < 0) {
              var visibleIndex = $('.elr_container.tabs > .content.visible').index();
              if (visibleIndex >= 0) {
                activeTabIndex = visibleIndex;
              }
            }
            // Default to Data Selector (index 1) if on that page
            if (activeTabIndex < 0) {
              activeTabIndex = 1; // Data Selector tab
            }
            
            // Store in localStorage
            localStorage.setItem('avatax_elr_subtab_index', activeTabIndex);
            
            // Create new URL object
            var url = new URL(currentUrl);
            
            // Update the entity parameter
            url.searchParams.set('entity', selectedValue);
            // Add subtab parameter to URL
            url.searchParams.set('subtab', activeTabIndex);
            
            // Store the selected value before reload (after URL is prepared)
            localStorage.setItem('avatax_entity_type', selectedValue);
            
            // Ensure beforeunload handler is set up (WooCommerce settings.js should do this,
            // but we ensure it's active by triggering a change event on a form field)
            // This allows the browser's unsaved changes warning to appear
            if (typeof window.onbeforeunload === 'undefined' || window.onbeforeunload === null) {
              // If no beforeunload handler exists, trigger a change on a form field to activate it
              // This ensures WooCommerce's settings.js sets up the warning
              var $formInput = $('#mainform input, #mainform select, #mainform textarea').first();
              if ($formInput.length > 0) {
                $formInput.trigger('change');
                // Small delay to ensure handler is registered
                setTimeout(function() {
                  window.location.href = url.toString();
                }, 10);
                return;
              }
            }
            
            // Navigate to new URL (beforeunload warning should appear if form has unsaved changes)
            window.location.href = url.toString();
        });
        
        // Add this to handle the value after page reload
        $(document).ready(function() {
            var storedValue = localStorage.getItem('avatax_entity_type');
            if (storedValue) {
                $('#entity_type').val(storedValue);
                localStorage.removeItem('avatax_entity_type');
            }
        })

        function refresh_mapped_table(records){
          var $select = $("#mapped_table");
          $select.empty(); // Clear existing options first
          
          $select.append($("<option></option>")
          .attr("value", "")
          .text("Select source table"));
          
          $.each(records, function(index, value) {
              $select.append($("<option></option>")
                  .attr("value", value.main_table)
                  .attr("isarray", value.isarray)
                  .text(value.main_table));
          });

          $select = $("#secondary_table");
          $select.empty(); // Clear existing options first
          
          $select.append($("<option></option>")
          .attr("value", "")
          .text("Select source table"));
          
          $.each(records, function(index, value) {
              $select.append($("<option></option>")
                  .attr("value", value.main_table)
                  .attr("isarray", value.isarray)
                  .text(value.main_table));
          });
        }
      
        function bind_delete_mapping(){
          $(".tbl_mapper_delete").unbind().bind('click', function(e){
                   // The anchor's href is '#' (server-side wp_kses() strips
                   // the previous javascript:void(0); URL) — stop the browser
                   // from jumping to the top of the page on click.
                   e.preventDefault();
                   var $ele = $(this).parent().parent();
                   var mapperid = $ele.children('td:first').text();
       
                   var data = {
                     action: 'wc_avatax_submit_map_perform',
                     security: wc_avatax_admin.submit_map_nonce,
                     mapperid: mapperid,
                     param: 'delete_mapper_record',
                     entity: getParameterByName('entity')
                 };
                 
                 jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                   if(response['data'] == 1){$ele.remove();}
                   refresh_tbl_mapper(response['records']);
                   showSchemaTree(JSON.parse(response['schema']), JSON.parse(response['savedSchema']))
                   refresh_mapped_table(response['mapperTables']);
                   bind_delete_mapping();
                 });
                 });
          }

          function bind_delete_conditional_mapping(){
            // code to delete conditional mapper record
          $(".tbl_condition_delete").unbind().bind('click', function(e){
            // See bind_delete_mapping(): href='#' would otherwise scroll the
            // page to the top before the AJAX call fires.
            e.preventDefault();
            var $ele = $(this).parent().parent();
            var conditionalId = $ele.children('td:first').text();
            var filterId = $ele.children('td').eq(1).text();
            var data = {
              action: 'wc_avatax_submit_map_perform',
              security: wc_avatax_admin.submit_map_nonce,
              conditionalId: conditionalId,
              filterId: filterId,
              param: 'delete_conditional_record'
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
              if(response['data'] == 1){$ele.remove();}
            });
          });
          }

          function refresh_tbl_mapper(records){
            $("#tbl_mapper tbody").empty();
            $("#tbl_mapper").append("<tbody>" + records + "</tbody>");
          }

          $( '.wc-avatax-help-tip' ).tipTip( {
            attribute: 'data-tip',
            fadeIn: 250,
            fadeOut: 2000,
            delay: 100,
            keepAlive: true,
          } );

          // Global variables for batch search
          window.batchSearchEntries = [];
          window.batchFiltersLoaded = false;
          window.batchSearchesLoaded = false; // Track if batch searches are already loaded
          window.lastBatchSearchName = ''; // Track batch search name changes

          // Load filters for batch search
          window.load_batch_filters = function() {
            var searchTerm = $("#new_search_term").val().trim();
            if (!searchTerm) {
              show_error_message('Please enter a search term first');
              return;
            }
            
            $("#btn_load_batch_filters").addClass("spin").attr("disabled", "disabled");
            
            var data = {
              nonce: wc_avatax_admin.disconnect_nonce,
              action: 'wc_avatax_network_directory_search',
              search_term: searchTerm, // Already processed by query builder
              page: 1,
              per_page: 1 // We only need facets, minimal results
            };
            
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
              $("#btn_load_batch_filters").removeClass("spin").removeAttr("disabled");
              
              if (response.code == 200) {
                // Extract facets from search API response (same format as single search)
                var facetsData = response.facets || [];
                
                render_batch_filters(facetsData);
                window.batchFiltersLoaded = true;
                window.lastFilterSearchTerm = searchTerm; // Store the search term used for these filters
                $("#batch_filters_section").show();
                show_success_message('Filters loaded for batch search');
              } else {
                show_error_message('Failed to load filters: ' + (response.message || 'Unknown error'));
              }
            }).fail(function() {
              $("#btn_load_batch_filters").removeClass("spin").removeAttr("disabled");
              show_error_message('Failed to load filters: Network error');
            });
          };

          // Render batch filters
          window.render_batch_filters = function(filterData) {
            var filtersContainer = $("#batch_dynamic_filters");
            filtersContainer.empty();
            
            if (filterData && Array.isArray(filterData) && filterData.length > 0) {
              filterData.forEach(function(taxonomy) {
                var filterHtml = create_batch_filter_field(taxonomy);
                filtersContainer.append(filterHtml);
              });
            } else {
              filtersContainer.html('<p style="color: #666;">No filters available.</p>');
            }
          };

          // Create batch filter field
          window.create_batch_filter_field = function(taxonomy) {
            var fieldHtml = '<div class="batch-filter-field" style="margin-right: 10px;">';
            
            if (taxonomy.taxonomyId && taxonomy.taxonomyName && taxonomy.values) {
              fieldHtml += '<label style="font-size: 11px; display: block; margin-bottom: 3px; width: 100%;">' + taxonomy.taxonomyName + '</label>';
              fieldHtml += '<select id="batch_filter_' + taxonomy.taxonomyId + '" style="font-size: 12px; width: 100%;">';
              fieldHtml += '<option value="">All ' + taxonomy.taxonomyName + '</option>';
              
              if (taxonomy.values && taxonomy.values.length > 0) {
                taxonomy.values.forEach(function(valueObj) {
                  var valueArray = valueObj.value.split(':');
                  var displayValue = valueArray[valueArray.length - 1];
                  if (displayValue && displayValue.trim() !== '') {
                    var truncatedValue = displayValue.length > 50 ? displayValue.substring(0, 47) + '...' : displayValue;
                    var displayText = truncatedValue;
                    fieldHtml += '<option value="' + displayValue + '" title="' + displayValue + '">' + displayText + '</option>';
                  }
                });
              }
              
              fieldHtml += '</select>';
            }
            
            fieldHtml += '</div>';
            return fieldHtml;
          };

          // Add search entry
          window.add_search_entry = function() {
            var searchTerm = $("#new_search_term").val().trim();
            
            if (!searchTerm) {
              show_error_message('Please enter a search term');
              return;
            }
            
            var filters = {};
            if (window.batchFiltersLoaded) {
              $("#batch_dynamic_filters select").each(function() {
                var filterId = $(this).attr('id');
                var filterValue = $(this).val();
                if (filterId && filterValue) {
                  var filterKey = filterId.replace('batch_filter_', '');
                  filters[filterKey] = filterValue;
                }
              });
            }
            
            var searchEntry = {
              id: 'entry_' + Date.now(),
              search_term: searchTerm, // Already processed by query builder
              filters: filters
            };
            
            window.batchSearchEntries.push(searchEntry);
            
            // Only clear the filter selections, keep the search term and loaded filters
            $("#batch_dynamic_filters select").val('');
            
            update_search_entries_display();
            update_generated_json();
            
            // Validate batch search button
            window.validateBatchSearchButton();
            
            show_success_message('Search entry added (' + window.batchSearchEntries.length + ' total). You can add another entry with different filters.');
          };

          // Update search entries display
          window.update_search_entries_display = function() {
            var container = $("#search_entries_container");
            var count = $("#entries_count");
            
            count.text(window.batchSearchEntries.length);
            
            if (window.batchSearchEntries.length === 0) {
              container.html('<p class="no-entries" style="color: #666; font-style: italic;">No search entries added yet. Add your first search entry above.</p>');
              return;
            }
            
            var html = '';
            window.batchSearchEntries.forEach(function(entry, index) {
              html += '<div class="search-entry-item" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 10px; margin: 5px 0; display: flex; justify-content: space-between; align-items: center;">';
              html += '<div class="entry-details">';
              html += '<strong>' + entry.search_term + '</strong>';
              
              if (Object.keys(entry.filters).length > 0) {
                html += '<div style="font-size: 11px; color: #666; margin-top: 3px;">Filters: ';
                var filterTexts = [];
                for (var key in entry.filters) {
                  filterTexts.push(key + '=' + entry.filters[key]);
                }
                html += filterTexts.join(', ');
                html += '</div>';
              }
              
              html += '</div>';
              html += '<button type="button" class="remove-entry" data-entry-id="' + entry.id + '" title="Remove entry"><span class="dashicons dashicons-trash"></span></button>';
              html += '</div>';
            });
            
            container.html(html);
            
            $(".remove-entry").on('click', function() {
              var entryId = $(this).data('entry-id');
              remove_search_entry(entryId);
            });
          };

          // Remove search entry
          window.remove_search_entry = function(entryId) {
            window.batchSearchEntries = window.batchSearchEntries.filter(function(entry) {
              return entry.id !== entryId;
            });
            
            update_search_entries_display();
            update_generated_json();
            
            // Validate batch search button
            window.validateBatchSearchButton();
            
            show_success_message('Search entry removed (' + window.batchSearchEntries.length + ' remaining)');
          };

          // Update generated JSON
          window.update_generated_json = function() {
            // Generate the correct API format with "value" array and "$search"/"$filters" keys
            var jsonData = {
              value: window.batchSearchEntries.map(function(entry) {
                var searchItem = {
                  "$search": entry.search_term || ""
                };
                
                // Convert filter object to OData filter string
                var filterString = "";
                if (entry.filters && typeof entry.filters === 'object' && Object.keys(entry.filters).length > 0) {
                  var filterConditions = [];
                  
                  // Map filter keys to OData expressions
                  Object.keys(entry.filters).forEach(function(filterKey) {
                    var filterValue = entry.filters[filterKey];
                    if (filterValue) {
                      // Create OData filter expression
                      filterConditions.push(filterKey + " eq '" + filterValue + "'");
                    }
                  });
                  
                  filterString = filterConditions.join(' and ');
                }
                
                searchItem["$filters"] = filterString;
                
                return searchItem;
              })
            };
            
            var jsonString = window.batchSearchEntries.length > 0 ? JSON.stringify(jsonData, null, 2) : '';
            $("#generated_json").val(jsonString);
          };

          // Handle batch file upload
          window.handle_batch_file_upload = function(input) {
            var file = input.files[0];
            if (!file) return;
            
            // Validate file type - allow .json and .txt files
            var fileName = file.name.toLowerCase();
            var fileExtension = fileName.split('.').pop();
            var allowedExtensions = ['json', 'txt'];
            var allowedMimeTypes = ['application/json', 'text/plain', 'text/json'];
            
            if (!allowedExtensions.includes(fileExtension) && !allowedMimeTypes.includes(file.type)) {
              show_error_message('Please upload a JSON (.json) or text (.txt) file containing valid JSON data.');
              input.value = ''; // Clear the file input
              window.validateBatchSearchButton(); // Re-validate button after clearing file
              return;
            }
            
            var reader = new FileReader();
            reader.onload = function(e) {
              try {
                var fileContent = e.target.result.trim();
                
                // Validate that content is not empty
                if (!fileContent) {
                  show_error_message('The uploaded file is empty. Please upload a file with valid JSON content.');
                  input.value = '';
                  window.validateBatchSearchButton(); // Re-validate button after clearing file
                  return;
                }
                
                // Parse JSON content
                var jsonData = JSON.parse(fileContent);
                
                // Validate JSON structure - check for the correct API format
                if (!jsonData.value || !Array.isArray(jsonData.value)) {
                  show_error_message('Invalid JSON format. The file must contain a "value" array with search entries in the format: {"value": [{"$search": "term", "$filters": "filter"}]}');
                  input.value = '';
                  window.validateBatchSearchButton(); // Re-validate button after clearing file
                  return;
                }
                
                // Validate each search entry
                var validEntries = [];
                var invalidEntries = [];
                
                jsonData.value.forEach(function(entry, index) {
                  if (typeof entry === 'object' && entry !== null) {
                    // Check for required $search field
                    if (!entry.hasOwnProperty('$search')) {
                      invalidEntries.push('Entry ' + (index + 1) + ': Missing "$search" field');
                      return;
                    }
                    
                    // Validate $search is not empty
                    if (!entry['$search'] || typeof entry['$search'] !== 'string' || entry['$search'].trim() === '') {
                      invalidEntries.push('Entry ' + (index + 1) + ': "$search" field cannot be empty');
                      return;
                    }
                    
                    // Handle $filters - can be string or object
                    var filterString = "";
                    var filterObject = {};
                    
                    if (entry.hasOwnProperty('$filters')) {
                      if (typeof entry['$filters'] === 'string') {
                        // If it's already a string, use it as is
                        filterString = entry['$filters'];
                        // Try to parse it back to object format for internal storage
                        // This is a simplified parser for basic OData expressions
                        if (filterString) {
                          var filterParts = filterString.split(' and ');
                          filterParts.forEach(function(part) {
                            var match = part.trim().match(/^(\w+)\s+eq\s+'([^']+)'$/);
                            if (match) {
                              filterObject[match[1]] = match[2];
                            }
                          });
                        }
                      } else if (typeof entry['$filters'] === 'object' && entry['$filters'] !== null) {
                        // If it's an object, convert to string and store object
                        filterObject = entry['$filters'];
                        var filterConditions = [];
                        Object.keys(filterObject).forEach(function(filterKey) {
                          var filterValue = filterObject[filterKey];
                          if (filterValue) {
                            filterConditions.push(filterKey + " eq '" + filterValue + "'");
                          }
                        });
                        filterString = filterConditions.join(' and ');
                      } else {
                        invalidEntries.push('Entry ' + (index + 1) + ': "$filters" field must be a string or object');
                        return;
                      }
                    }
                    
                    validEntries.push({
                      id: 'uploaded_' + index,
                      search_term: process_search_term(entry['$search'].trim()),
                      filters: filterObject // Store as object for internal consistency
                    });
                  } else {
                    invalidEntries.push('Entry ' + (index + 1) + ': Invalid entry format (must be an object)');
                  }
                });
                
                // Show validation results
                if (invalidEntries.length > 0) {
                  var errorMessage = 'Found ' + invalidEntries.length + ' invalid entries:\n' + invalidEntries.slice(0, 5).join('\n');
                  if (invalidEntries.length > 5) {
                    errorMessage += '\n... and ' + (invalidEntries.length - 5) + ' more errors.';
                  }
                  errorMessage += '\n\nPlease fix these issues and upload the file again.';
                  show_error_message(errorMessage);
                  input.value = '';
                  window.validateBatchSearchButton(); // Re-validate button after clearing file
                  return;
                }
                
                if (validEntries.length === 0) {
                  show_error_message('No valid search entries found in the uploaded file. Please check the JSON format.');
                  input.value = '';
                  window.validateBatchSearchButton(); // Re-validate button after clearing file
                  return;
                }
                
                // Load valid entries
                window.batchSearchEntries = validEntries;
                update_search_entries_display();
                update_generated_json();
                
                // Validate batch search button after loading entries
                window.validateBatchSearchButton();
                
                var successMessage = 'Successfully loaded ' + validEntries.length + ' search entries from file.';
                if (invalidEntries.length > 0) {
                  successMessage += ' (' + invalidEntries.length + ' invalid entries were skipped)';
                }
                show_success_message(successMessage);
                
              } catch (error) {
                var errorMessage = 'Error parsing file content: ';
                if (error.name === 'SyntaxError') {
                  errorMessage += 'Invalid JSON format. Please check that your file contains valid JSON syntax.';
                } else {
                  errorMessage += error.message;
                }
                show_error_message(errorMessage);
                input.value = ''; // Clear the file input on error
                window.validateBatchSearchButton(); // Re-validate button after clearing file
              }
            };
            
            reader.readAsText(file);
          };

          // Start batch search
          window.start_batch_search = function() {
            var method = $('input[name="batch_method"]:checked').val();
            var batchName = $("#batch_search_name").val().trim();
            var notificationEmail = $("#batch_notification_email").val().trim();
            var batchJson = '';

            if (!batchName) {
              show_error_message('Please enter a name for this batch search');
              return;
            }

            if (batchName.length < 2) {
              show_error_message('Batch search name must be at least 2 characters long');
              return;
            }

            if (!notificationEmail) {
              show_error_message('Please enter a notification email address');
              return;
            }

            // Basic email validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(notificationEmail)) {
              show_error_message('Please enter a valid email address');
              return;
            }

            // Get JSON data based on method
            if (method === 'upload') {
              // For upload method, get JSON from uploaded file
              batchJson = $("#generated_json").val().trim();
              if (!batchJson) {
                show_error_message('Please upload a JSON file first');
                return;
              }
            } else {
              // For builder method, get JSON from generated entries
              if (window.batchSearchEntries.length === 0) {
                show_error_message('Please add search entries first');
                return;
              }
              
              // Generate the correct API format with "value" array and "$search"/"$filters" keys
              var searchData = {
                value: window.batchSearchEntries.map(function(entry) {
                  var searchItem = {
                    "$search": entry.search_term || ""
                  };
                  
                  // Convert filter object to OData filter string
                  var filterString = "";
                  if (entry.filters && typeof entry.filters === 'object' && Object.keys(entry.filters).length > 0) {
                    var filterConditions = [];
                    
                    // Map filter keys to OData expressions
                    Object.keys(entry.filters).forEach(function(filterKey) {
                      var filterValue = entry.filters[filterKey];
                      if (filterValue) {
                        // Create OData filter expression
                        filterConditions.push(filterKey + " eq '" + filterValue + "'");
                      }
                    });
                    
                    filterString = filterConditions.join(' and ');
                  }
                  
                  searchItem["$filters"] = filterString;
                  
                  return searchItem;
                })
              };
              
              batchJson = JSON.stringify(searchData, null, 2);
            }

            // Submit batch search to NDI API
            submit_batch_search(batchName, notificationEmail, batchJson);
          };

          // Submit batch search to NDI API
          window.submit_batch_search = function(batchName, notificationEmail, batchJson) {
            $("#btn_batch_search").addClass("spin").attr("disabled", "disabled");
            $("#batch_progress").show();
            
            // Update progress to show submission in progress
            $("#batch_progress").html('<p>Submitting batch search to NDI API...</p>');
            
            $.ajax({
              url: wc_avatax_admin_elr.ajax_url,
              type: 'POST',
              data: {
                action: 'wc_avatax_ndi_batch_search_submit',
                nonce: wc_avatax_admin.disconnect_nonce,
                batch_name: batchName,
                notification_email: notificationEmail,
                batch_json: batchJson
              },
              success: function(response) {
                $("#btn_batch_search").removeClass("spin").removeAttr("disabled");
                
                if (response.success) {
                  // Additional validation: check if we actually have valid data
                  if (!response.data.batch_id || response.data.batch_id === 'null' || response.data.batch_id === null) {
                    show_error_message('Batch search failed: No valid batch ID received from API');
                    $("#batch_progress").hide();
                    // Reset batch results on invalid data
                    $("#batch_results").html('').hide();
                    return;
                  }
                  
                  // Show batch details without duplicate success message
                  var html = '<div class="batch-submission-success" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; padding: 15px; margin: 15px 0;">';
                  html += '<h4 style="color: #155724; margin: 0 0 10px 0;">📋 Batch Search Details</h4>';
                  
                  // Show API message if available
                  if (response.data.message) {
                    html += '<p style="font-style: italic; color: #155724; margin-bottom: 15px;">' + response.data.message + '</p>';
                  }
                  
                  html += '<p><strong>Batch Name:</strong> ' + response.data.batch_name + '</p>';
                  html += '<p><strong>Batch ID:</strong> ' + response.data.batch_id + '</p>';
                  html += '<p><strong>Status:</strong> ' + response.data.status + '</p>';
                  html += '<p><strong>Notification Email:</strong> ' + response.data.notification_email + '</p>';
                  if (response.data.query_count) {
                    html += '<p><strong>Search Queries:</strong> ' + response.data.query_count + '</p>';
                  }
                  html += '<p style="margin-bottom: 0;"><em>You will receive an email notification at the provided address when the batch search processing is complete.</em></p>';
                  html += '</div>';
                  
                  $("#batch_results").html(html).show();
                  $("#batch_progress").hide();
                  
                  // Show success message at the top
                  show_success_message('✅ Batch search submitted successfully! Batch ID: ' + response.data.batch_id);
                  
                  // Clear JSON and file inputs after successful submission
                  clear_batch_inputs_after_success();
                } else {
                  $("#batch_progress").hide();
                  // Reset batch results on failure
                  $("#batch_results").html('').hide();
                  // Show user-friendly error message (already processed by backend)
                  show_error_message(response.data || 'Unable to submit batch search. Please try again.');
                }
              },
              error: function(xhr, status, error) {
                $("#btn_batch_search").removeClass("spin").removeAttr("disabled");
                $("#batch_progress").hide();
                
                // Reset batch results on network error
                $("#batch_results").html('').hide();
                
                // Provide user-friendly network error messages
                var errorMessage = 'Unable to submit batch search. ';
                if (status === 'timeout') {
                  errorMessage += 'The request timed out. Please check your internet connection and try again.';
                } else if (status === 'error') {
                  errorMessage += 'Network error occurred. Please check your internet connection and try again.';
                } else if (xhr.status === 0) {
                  errorMessage += 'No internet connection. Please check your network and try again.';
                } else {
                  errorMessage += 'Please try again or contact support if the problem persists.';
                }
                
                show_error_message(errorMessage);
              }
            });
          };

          // Legacy function - kept for backward compatibility but now unused
          window.perform_batch_search = function(searchData) {
            // This function is no longer used - batch searches are now submitted to NDI API
            console.warn('perform_batch_search is deprecated - use submit_batch_search instead');
            
            var totalSearches = searchData.searches.length;
            var completedSearches = 0;
            var allResults = [];
            
            $("#total_count").text(totalSearches);
            update_batch_progress(0, totalSearches);
            
            function processNextSearch(index) {
              if (index >= searchData.searches.length) {
                display_batch_results(allResults, searchData);
                $("#btn_batch_search").removeClass("spin").removeAttr("disabled");
                return;
              }
              
              var search = searchData.searches[index];
              
              var data = {
                nonce: wc_avatax_admin.disconnect_nonce,
                action: 'wc_avatax_network_directory_search',
                search_term: search.search_term,
                page: 1,
                per_page: 50
              };
              
              for (var filterKey in search.filters) {
                if (search.filters[filterKey]) {
                  data['filter_' + filterKey] = search.filters[filterKey];
                }
              }
              
              jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                completedSearches++;
                
                var searchResult = {
                  search_term: search.search_term,
                  filters: search.filters,
                  results: response.data || [],
                  total_results: response.pagination ? response.pagination.total_results : 0,
                  success: response.code === 200
                };
                
                allResults.push(searchResult);
                update_batch_progress(completedSearches, totalSearches);
                
                setTimeout(function() {
                  processNextSearch(index + 1);
                }, 500);
                
              }).fail(function() {
                completedSearches++;
                
                var searchResult = {
                  search_term: search.search_term,
                  filters: search.filters,
                  results: [],
                  total_results: 0,
                  success: false,
                  error: 'Network error'
                };
                
                allResults.push(searchResult);
                update_batch_progress(completedSearches, totalSearches);
                
                setTimeout(function() {
                  processNextSearch(index + 1);
                }, 500);
              });
            }
            
            processNextSearch(0);
          };

          // Update batch progress
          window.update_batch_progress = function(completed, total) {
            var percentage = total > 0 ? (completed / total) * 100 : 0;
            $("#batch_progress .progress-fill").css('width', percentage + '%');
            $("#progress_count").text(completed);
          };

          // Display batch results
          window.display_batch_results = function(results, searchData) {
            var batchName = searchData && searchData.batch_name ? searchData.batch_name : $("#batch_search_name").val().trim() || 'Untitled Batch Search';
            var totalResults = results.reduce(function(sum, r) { return sum + r.total_results; }, 0);
            
            var html = '<div class="batch-results-summary" style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin: 15px 0;">';
            html += '<h5 style="margin-top: 0; color: #0073aa;">📊 Batch Search Results: "' + batchName + '"</h5>';
            html += '<div style="display: flex; gap: 20px; margin-bottom: 10px;">';
            html += '<div><strong>Searches Processed:</strong> ' + results.length + '</div>';
            html += '<div><strong>Total Results Found:</strong> ' + totalResults + '</div>';
            html += '<div><strong>Success Rate:</strong> ' + Math.round((results.filter(function(r) { return r.success; }).length / results.length) * 100) + '%</div>';
            html += '</div>';
            html += '<p style="font-size: 12px; color: #666; margin: 0;">Completed at: ' + new Date().toLocaleString() + '</p>';
            html += '</div>';
            
            results.forEach(function(result, index) {
              html += '<div class="batch-result-item" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin: 10px 0;">';
              html += '<h6>Search #' + (index + 1) + ': "' + result.search_term + '"</h6>';
              
              if (Object.keys(result.filters).length > 0) {
                html += '<p style="font-size: 12px; color: #666;">Filters: ';
                var filterTexts = [];
                for (var key in result.filters) {
                  filterTexts.push(key + '=' + result.filters[key]);
                }
                html += filterTexts.join(', ') + '</p>';
              }
              
              if (result.success) {
                html += '<p style="color: #0073aa;"><strong>' + result.total_results + ' results found</strong></p>';
                
                if (result.results.length > 0) {
                  html += '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px;">';
                  result.results.slice(0, 5).forEach(function(item) {
                    html += '<div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0;">';
                    html += '<strong>' + (item.name || 'Unknown') + '</strong><br>';
                    html += '<small>Network: ' + (item.network || 'N/A') + ' | Country: ' + (item.addresses && item.addresses[0] ? item.addresses[0].country : 'N/A') + '</small>';
                    html += '</div>';
                  });
                  
                  if (result.results.length > 5) {
                    html += '<p style="font-style: italic; color: #666;">... and ' + (result.results.length - 5) + ' more results</p>';
                  }
                  
                  html += '</div>';
                }
              } else {
                html += '<p style="color: #d63638;"><strong>Search failed</strong>' + (result.error ? ': ' + result.error : '') + '</p>';
              }
              
              html += '</div>';
            });
            
            $("#batch_results").html(html);
          };

          // Clear batch search
          window.clear_batch_search = function() {
            // Reset all batch data and global variables
            window.batchSearchEntries = [];
            window.batchFiltersLoaded = false;
            window.lastFilterSearchTerm = '';
            
            // Clear ALL form fields completely
            $("#batch_search_name").val('');
            $("#batch_notification_email").val('');
            // Clear batch query builder fields
            $("#batch_search_term_1").val('');
            $("#batch_search_term_2").val('');
            $("#batch_search_operator").val('AND');
            $("#new_search_term").val('');
            $("#batch_dynamic_filters").empty();
            $("#batch_filters_section").hide();
            $("#generated_json").val('');
            
            // Clear file upload inputs (reset file selection)
            $("#batch_file").val('');
            $("#batch_file")[0].value = ''; // Force clear file input
            
            // Clear search entries display and reset to initial state
            $("#search_entries_container").html('<p class="no-entries" style="color: #666; font-style: italic;">No search entries added yet. Add your first search entry above.</p>');
            $("#entries_count").text('0');
            update_search_entries_display();
            
            // Clear all results, progress, and download elements
            $("#batch_results").html('').hide();
            $("#batch_progress").hide();
            
            // Clear all messages
            clear_all_messages();
            
            // Reset method selection to default (upload method)
            $('input[name="batch_method"][value="upload"]').prop('checked', true);
            $('input[name="batch_method"][value="builder"]').prop('checked', false);
            $("#batch_upload_method").show();
            $("#batch_builder_method").hide();
            
            // Clear any filter selections that might still be hanging around
            $("#batch_dynamic_filters select").each(function() {
                $(this).val('').prop('selectedIndex', 0);
            });
            
            // Reset button states to default
            $("#btn_load_batch_filters").removeClass("spin").removeAttr("disabled");
            $("#btn_add_search_entry").removeAttr("disabled");
            
            // Validate batch search button (will disable it since entries are cleared)
            window.validateBatchSearchButton();
            
            // Ensure all sections are in default state
            $("#batch_upload_method").show();
            $("#batch_builder_method").hide();
            
            // Reset batch search name tracking
            window.lastBatchSearchName = '';
            
            show_success_message('All data cleared - page reset to initial load state');
          };

          // Reset batch results (helper function)
          window.reset_batch_results = function() {
            $("#batch_results").html('').hide();
            $("#batch_progress").hide();
          };

          // Clear batch inputs after successful submission
          window.clear_batch_inputs_after_success = function() {
            // Clear generated JSON
            $("#generated_json").val('');
            
            // Clear file upload input
            $("#batch_file").val('');
            $("#batch_file")[0].value = ''; // Force clear file input
            
            // Clear search entries
            window.batchSearchEntries = [];
            update_search_entries_display();
            
            // Validate batch search button
            window.validateBatchSearchButton();
            
            // Clear batch query builder fields
            $("#batch_search_term_1").val('');
            $("#batch_search_term_2").val('');
            $("#batch_search_operator").val('AND');
            $("#new_search_term").val('');
            
            // Clear dynamic filters
            $("#batch_dynamic_filters").empty();
            $("#batch_filters_section").hide();
            
            // Reset filter tracking
            window.batchFiltersLoaded = false;
            window.lastFilterSearchTerm = '';
            
            // Show info message about clearing
            show_info_message('Batch inputs cleared. You can now start a new batch search.');
          };

          // Update JSON when batch name changes
          $("#batch_search_name").on('input', function() {
            update_generated_json();
          });

          // Clear filters when search term changes (since filters are based on search term)
          $("#new_search_term").on('input', function() {
            var currentSearchTerm = $(this).val().trim();
            
            // If filters are loaded and search term changed, clear them
            if (window.batchFiltersLoaded && currentSearchTerm !== window.lastFilterSearchTerm) {
              $("#batch_dynamic_filters").empty();
              $("#batch_filters_section").hide();
              window.batchFiltersLoaded = false;
              window.lastFilterSearchTerm = '';
              
              if (currentSearchTerm.length > 0) {
                show_info_message('Search term changed. Please load filters again for the new search term.');
              }
            }
          });

          // Batch Job Status functionality
          window.load_batch_searches = function(page = 1) {
            var $refreshButton = $("#refresh_batch_list");
            var $loadingDiv = $("#batch_list_loading");
            var $errorDiv = $("#batch_list_error");
            var $emptyDiv = $("#batch_list_empty");
            var $tableDiv = $("#batch_list_table");
            var $paginationDiv = $("#batch_list_pagination");

            // Show loading state
            $refreshButton.addClass("spin").attr("disabled", "disabled");
            $loadingDiv.show();
            $errorDiv.hide();
            $emptyDiv.hide();
            $tableDiv.hide();
            $paginationDiv.hide();

            var data = {
              action: 'wc_avatax_ndi_batch_search_list',
              nonce: wc_avatax_admin_elr.disconnect_nonce,
              page: page,
              per_page: 10,
              order_by: 'created DESC'
            };

            jQuery.post(wc_avatax_admin_elr.ajax_url, data, function(response) {
              $refreshButton.removeClass("spin").removeAttr("disabled");
              $loadingDiv.hide();

              if (response.success) {
                var batchSearches = response.data.data || [];
                var pagination = response.data.pagination || {};

                if (batchSearches.length === 0) {
                  $emptyDiv.show();
                  window.batchSearchesLoaded = true; // Mark as loaded even if empty
                } else {
                  render_batch_list_table(batchSearches);
                  render_batch_pagination(pagination);
                  $tableDiv.show();
                  $paginationDiv.show();
                  window.batchSearchesLoaded = true; // Mark as loaded
                }
              } else {
                show_batch_list_error(response.data.message || 'Failed to load batch searches');
              }
            }).fail(function(xhr, status, error) {
              $refreshButton.removeClass("spin").removeAttr("disabled");
              $loadingDiv.hide();
              
              var errorMessage = 'Network error occurred while loading batch searches';
              if (xhr.status) {
                errorMessage += ' (HTTP ' + xhr.status + ')';
              }
              show_batch_list_error(errorMessage);
            });
          }

          function render_batch_list_table(batchSearches) {
            var $tbody = $("#batch_list_tbody");
            $tbody.empty();

            batchSearches.forEach(function(batch) {
              var statusClass = get_status_class(batch.status);
              var statusText = batch.status || 'Unknown';
              
              var row = '<tr>' +
                '<td><strong>' + (batch.name || 'Unnamed') + '</strong></td>' +
                '<td><span class="batch-status ' + statusClass + '">' + statusText + '</span></td>' +
                '<td>' + (batch.created_by || 'Unknown') + '</td>' +
                '<td>' + (batch.created_formatted || batch.created || 'Unknown') + '</td>' +
                '<td>' + (batch.last_modified_formatted || batch.last_modified || 'Unknown') + '</td>' +
                '<td>' +
                  '<button type="button" class="button button-small" onclick="view_batch_details(\'' + batch.id + '\')" title="View Details">' +
                    'View' +
                  '</button>' +
                '</td>' +
              '</tr>';
              
              $tbody.append(row);
            });
          }

          function render_batch_pagination(pagination) {
            var currentPage = parseInt(pagination.current_page) || 1;
            var totalPages = parseInt(pagination.total_pages) || 1;
            var totalResults = parseInt(pagination.total_results) || 0;
            var perPage = parseInt(pagination.per_page) || 10;

            // Store current page in a global variable for fallback
            window.currentBatchPage = currentPage;
            window.totalBatchPages = totalPages;

            // Update pagination info
            var startItem = ((currentPage - 1) * perPage) + 1;
            var endItem = Math.min(currentPage * perPage, totalResults);
            var paginationText = 'Showing ' + startItem + '-' + endItem + ' of ' + totalResults + ' batch searches';
            $("#batch_pagination_info").text(paginationText);

            // Update pagination buttons with proper data attributes
            var $prevButton = $("#batch_prev_page");
            var $nextButton = $("#batch_next_page");
            
            // Clear existing data and set new data
            $prevButton.removeData('current-page').removeData('total-pages');
            $nextButton.removeData('current-page').removeData('total-pages');
            
            $prevButton.data('current-page', currentPage).data('total-pages', totalPages);
            $nextButton.data('current-page', currentPage).data('total-pages', totalPages);

            if (currentPage <= 1) {
              $prevButton.attr('disabled', 'disabled');
            } else {
              $prevButton.removeAttr('disabled');
            }

            if (currentPage >= totalPages) {
              $nextButton.attr('disabled', 'disabled');
            } else {
              $nextButton.removeAttr('disabled');
            }

            // Update page numbers display
            var pageNumbers = '';
            if (totalPages > 1) {
              for (var i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                if (i === currentPage) {
                  pageNumbers += '<strong class="button button-primary current-page-number">' + i + '</strong> ';
                } else {
                  pageNumbers += '<a href="#" class="button page-number-link" data-page="' + i + '">' + i + '</a> ';
                }
              }
            }
            $("#batch_page_numbers").html(pageNumbers);
            
            // Bind click events for page number links
            $("#batch_page_numbers .page-number-link").on('click', function(e) {
              e.preventDefault();
              var page = parseInt($(this).data('page'));
              window.load_batch_searches(page);
            });
          }

          function get_status_class(status) {
            if (!status) return 'status-unknown';
            
            var statusLower = status.toLowerCase();
            if (statusLower.includes('ready') || statusLower.includes('completed')) {
              return 'status-ready';
            } else if (statusLower.includes('processing') || statusLower.includes('running')) {
              return 'status-processing';
            } else if (statusLower.includes('error') || statusLower.includes('failed')) {
              return 'status-error';
            } else if (statusLower.includes('pending') || statusLower.includes('queued')) {
              return 'status-pending';
            }
            return 'status-unknown';
          }

          function show_batch_list_error(message) {
            $("#batch_list_error p").text(message);
            $("#batch_list_error").show();
          }

          // Global function for viewing batch details - redirects to NDI portal
          window.view_batch_details = function(batchId) {
            // Use the NDI portal URL passed as script variable
            if (wc_avatax_admin_elr && wc_avatax_admin_elr.ndi_portal_url) {
              // Open NDI portal with batch results tab in new window
              var portalUrl = wc_avatax_admin_elr.ndi_portal_url + '?tab=batch-results';
              window.open(portalUrl, '_blank', 'noopener,noreferrer');
            } else {
              alert('Unable to open NDI portal. Portal URL not available.');
            }
          };

          // Function is already defined globally above

  });
}).call(this);



function openCity(evt, fieldName) {
  evt.preventDefault();
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(fieldName).style.display = "block";
  //document.getElementsByClassName(fieldName).target.className  += " active";
  }
