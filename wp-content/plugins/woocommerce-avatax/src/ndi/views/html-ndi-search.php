<?php
// @codeCoverageIgnoreStart

/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined('ABSPATH') or exit;
?>

<style>
.ndi-search-input {
    height: 32px !important;
    box-sizing: border-box;
    padding: 4px 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
    line-height: 1.4;
}

.ndi-search-input:focus {
    border-color: #0073aa;
    box-shadow: 0 0 0 1px #0073aa;
    outline: none;
}

        /* NDI Search Input Styling */
        .ndi-search-input {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        }

        .ndi-search-input h3 {
            margin-top: 0;
            color: #1d2327;
        }

        .ndi-search-input .search-row {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 15px;
        }

        .ndi-search-input .search-field {
            flex: 1;
        }

        .ndi-search-input .search-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #1d2327;
        }

        .ndi-search-input .search-field input,
        .ndi-search-input .search-field select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px;
        }

        .ndi-search-input .search-field input:focus,
        .ndi-search-input .search-field select:focus {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
            outline: 2px solid transparent;
        }

        .ndi-search-input .search-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
        }

        .ndi-search-input .search-actions .button {
            padding: 8px 16px;
            height: auto;
            line-height: 1.4;
        }

        /* Enhanced Identifiers Table Styling */
        .ndi-identifiers-table {
            border-collapse: collapse;
            width: 100%;
            background: #ffffff;
        }

        .ndi-identifiers-table .company-name {
            font-weight: 600;
            color: #1d2327;
        }

        .ndi-identifiers-table .company-id {
            color: #646970;
            font-family: monospace;
        }

        .ndi-identifiers-table .identifier-row {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
        }

        .ndi-identifiers-table .identifier-name {
            font-weight: 600;
            color: #2271b1;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .ndi-identifiers-table .identifier-value {
            font-family: monospace;
            font-size: 12px;
            color: #1d2327;
            display: inline-block;
            flex-shrink: 0;
        }

        .ndi-identifiers-table .no-identifiers {
            font-style: italic;
            color: #646970;
            font-size: 12px;
        }

        .ndi-identifiers-table .network-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ndi-identifiers-table .country-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        
        /* Dynamic badge styling - colors will be applied via JavaScript */
        .ndi-identifiers-table .network-badge,
        .ndi-identifiers-table .country-badge {
            /* Base styling remains the same */
            box-sizing: border-box;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        /* Pagination Styling */
        .current-page-number {
            cursor: default !important;
            pointer-events: none;
        }

        .current-page-number:hover {
            cursor: default !important;
        }

        /* Responsive table handling */
        @media (max-width: 768px) {
            .ndi-identifiers-table {
                font-size: 12px;
            }
            
            .ndi-identifiers-table .identifier-value {
                font-size: 11px;
                padding: 3px 6px;
            }
        }

        /* Ensure filter visibility and proper styling */
        .filter-field {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }
        
        .filter-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .filter-field select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            background: #fff;
        }
        
        .filter-field select:focus {
            border-color: #0073aa;
            outline: none;
            box-shadow: 0 0 0 1px #0073aa;
        }
        
        .filters-actions {
            margin-top: 15px;
            padding: 15px;
            background: #f0f6fc;
            border: 1px solid #c3c4c7;
            border-radius: 4px;
            text-align: center;
        }
        
        .filters-actions button {
            margin: 0 5px;
        }

        /* Accordion UI for Multiple Identifiers */
        .identifiers-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0px 0px;
            margin-top: 10px;
        }

        .identifier-count {
            font-weight: normal;
            font-size: 13px;
        }

        .toggle-identifiers {
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            width: 100% !important;
            padding: 0px 7px;
            background-color: var(--wc-secondary);
        }

        .toggle-identifiers:hover {
            background-color: var(--wc-avatax-color);
            color: var(--wc-primary-text);
        }

        .toggle-identifiers:hover > span {
            color: var(--wc-primary-text);
        }

        .toggle-identifiers .dashicons {
            font-size: 16px;
            color: var(--wc-avatax-color);
            margin-top: 5px;
        }

        .identifier-preview {
            margin-bottom: 10px;
        }

        .more-identifiers {
            font-size: 11px;
            color: #6c757d;
            font-style: italic;
            margin-top: 4px;
            text-align: center;
        }

        .identifiers-accordion .identifier-row {
            margin-bottom: 6px;
            padding: 4px 0;
        }

        .identifiers-accordion .identifier-row:last-child {
            margin-bottom: 0;
        }

        .network-directory-tab-content .required::after {
            content: " *";
            color: #e65c00;
            font-weight: bold;
        }
        
        /* JSON Copy Button Icon Styling */
        #copy_json_sample .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
            margin-right: 5px;
            vertical-align: text-bottom;
        }
        
        #copy_json_sample.copied .dashicons-clipboard:before {
            content: "\f147"; /* checkmark icon */
        }
        
        /* JSON Accordion Styling */
        .json-accordion-arrow.expanded {
            transform: rotate(90deg);
        }

        /* Remove Entry Button Styling */
        .remove-entry {
            padding: 6px 8px !important;
            min-height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: inherit;
        }
        
        .remove-entry:hover {
            color: #dc3232 !important;
            cursor: pointer;
        }

        .batch-method-selection label {
            display: inline-block !important;
        }
        
        div#batch_results p {
            word-break: break-all;
        }

</style>

<div class="avalara-network-directory-container">
    <h3><?php echo esc_html__('Avalara Network Directory Interface', 'woocommerce-avatax'); ?></h3>
    
    <div class="network-directory-wrapper">
        <p><?php echo esc_html__('Search and manage trade partners in the Avalara Network Directory to enrich your customer and vendor records with validated network-specific IDs for seamless invoicing.', 'woocommerce-avatax'); ?></p>
        
        <!-- Advanced Portal Access Tile -->
        <div class="advanced-portal-tile">
            <h4><?php echo esc_html__('Advanced portal access', 'woocommerce-avatax'); ?></h4>
            <?php
            // Get the NDI Portal URL based on the current environment
            $ndi_portal_url = wc_avatax()->get_ndi_handler()->get_ndi_portal_url();
            ?>
            <p>
                <?php echo esc_html__('Access NDI portal for complex searches, bulk result management', 'woocommerce-avatax'); ?>
                <a href="<?php echo esc_url($ndi_portal_url); ?>" class="button button-primary" id="btn_open_ndi_portal" target="_blank" rel="noopener noreferrer">
                    <?php echo esc_html__('Open NDI Portal', 'woocommerce-avatax'); ?>
                    <span class="dashicons dashicons-external"></span>
                </a>
            </p>
           
            
        </div>
        
        <div class="network-directory-tabs">
            <nav class="network-directory-nav">
                <a class="network-directory-tab"><?php echo esc_html__('Search', 'woocommerce-avatax'); ?></a>
                <a class="network-directory-tab"><?php echo esc_html__('Batch Search', 'woocommerce-avatax'); ?></a>
                <a class="network-directory-tab"><?php echo esc_html__('Batch Job Status', 'woocommerce-avatax'); ?></a>
            </nav>
            <div class="network-directory-tab-content">
                <!-- Search Tab Content -->
                <div class="search-section">
                    <h4><?php echo esc_html__('Directory Search', 'woocommerce-avatax'); ?></h4>
                    <p class="description"><?php echo esc_html__('Search for entities in the Avalara Network Directory.', 'woocommerce-avatax'); ?></p>
                    
                    <!-- Search Form -->
                    <div class="search-form">
                        <div class="search-field">
                            <label class="required" for="search_term"><?php echo esc_html__('Search Term', 'woocommerce-avatax'); ?></label>
                            <input type="text" id="search_term" placeholder="<?php echo esc_attr__('Enter search term', 'woocommerce-avatax'); ?>"/>
                        </div>
                        <div class="search-field">
                            <label>&nbsp;</label>
                            <div class="search-buttons">
                                <button type="button" id="btn_search_directory" class="button-primary actionButton">
                                    <?php echo esc_html__('Search', 'woocommerce-avatax'); ?>
                                </button>
                                <button type="button" id="btn_clear_search" class="button-secondary">
                                    <?php echo esc_html__('Clear', 'woocommerce-avatax'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results Layout with Sidebar -->
                    <div class="search-results-layout" id="search_results_layout" style="display: none;">
                        <!-- Left Sidebar for Filters -->
                        <div class="search-filters-sidebar" id="filters_sidebar">
                            <!-- Dynamic Filters Section (auto-loaded with search results) -->
                            <div class="dynamic-filters-content" id="dynamic_filters_content">
                                <div class="filters-header">
                                    <h5><?php echo esc_html__('Filters', 'woocommerce-avatax'); ?></h5>
                                </div>
                                <div class="filters-content">
                                    <div class="filters-placeholder" style="padding: 20px; text-align: center; color: #999; font-style: italic;">
                                        <div style="margin-bottom: 10px; font-size: 18px;">🔍</div>
                                        <div style="margin-bottom: 5px;">Search to load filters</div>
                                        <small>Filters will appear here after you perform a search</small>
                                    </div>
                                </div>
                                <div class="filters-actions" style="display: none;">
                                    <button type="button" id="btn_apply_filters" class="button-primary">
                                        <?php echo esc_html__('Apply Filters', 'woocommerce-avatax'); ?>
                                    </button>
                                    <button type="button" id="btn_clear_filters" class="button-secondary">
                                        <?php echo esc_html__('Clear', 'woocommerce-avatax'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Main Content Area for Search Results -->
                        <div class="search-results-main">
                            <div id="search_results" class="search-results">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="network-directory-tab-content">
                <!-- Batch Search Tab Content -->
                <div class="batch-search-section">
                    <h4 id="batch_directory_search_heading"><?php echo esc_html__('Batch Directory Search', 'woocommerce-avatax'); ?></h4>
                    <p class="description"><?php echo esc_html__('Create batch search requests by uploading a JSON file or building search queries using our interface.', 'woocommerce-avatax'); ?></p>
                    
                    <!-- Batch Search Name -->
                    <div class="batch-name-section" style="margin: 20px 0;">
                        <div class="batch-name-field" style="margin-bottom: 15px;">
                            <label for="batch_search_name" class="required" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                <?php echo esc_html__('Batch Search Name', 'woocommerce-avatax'); ?>
                            </label>
                            <input type="text" id="batch_search_name" placeholder="<?php echo esc_attr__('Enter a name for this batch search...', 'woocommerce-avatax'); ?>" style="width: 300px; padding: 5px;" />
                            <p class="description"><?php echo esc_html__('Give your batch search a descriptive name for easy identification.', 'woocommerce-avatax'); ?></p>
                        </div>
                        
                        <div class="batch-email-field" style="margin-bottom: 15px;">
                            <label for="batch_notification_email" class="required" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                <?php echo esc_html__('Notification Email', 'woocommerce-avatax'); ?>
                            </label>
                            <input type="email" id="batch_notification_email" placeholder="<?php echo esc_attr__('Enter email address for notifications...', 'woocommerce-avatax'); ?>" style="width: 300px; padding: 5px;" />
                            <p class="description"><?php echo esc_html__('Email address where batch search completion notifications will be sent.', 'woocommerce-avatax'); ?></p>
                        </div>
                    </div>

                    <!-- Batch Search Method Selection -->
                    <div class="batch-method-selection" style="background: #f9f9f9;
                        padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin: 15px 0;">
                        <label style="margin-right: 20px;">
                            <input type="radio" name="batch_method" value="upload" checked> 
                            <?php echo esc_html__('Upload JSON File', 'woocommerce-avatax'); ?>
                        </label>
                        <label>
                            <input type="radio" name="batch_method" value="builder"> 
                            <?php echo esc_html__('Build Search Queries', 'woocommerce-avatax'); ?>
                        </label>
                    
                        <table class="form-table batch-search-table" width="100%" aria-describedby="batch_directory_search_heading" >
                        <tr>
                            <th style="padding: 0px;">
                                <!-- Upload JSON File Method -->
                                <div id="batch_upload_method" class="batch-method">
                                    <div class="batch-input-section">
                                        <div class="file-upload" style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin: 15px 0;">
                                            <input type="file" id="batch_file" accept=".json,.txt" style="margin-bottom: 10px;"/>
                                            <p class="description"><?php echo esc_html__('Upload a JSON (.json) or text (.txt) file containing search queries and filters in JSON format', 'woocommerce-avatax'); ?></p>

                                            <div class="json-preview-accordion" style="margin-top: 15px;">
                                                <div class="json-preview-header" style="display: flex; align-items: center; cursor: pointer; padding: 10px 0;" onclick="toggleJsonPreview()">
                                                    <span class="dashicons dashicons-arrow-right-alt2 json-accordion-arrow" style="margin-right: 8px; transition: transform 0.2s ease;"></span>
                                                    <h4 style="margin: 0; flex: 1;"><?php echo esc_html__('Expected JSON Format:', 'woocommerce-avatax'); ?></h4>
                                                    <button type="button" id="copy_json_sample" class="button button-secondary" title="<?php echo esc_attr__('Copy JSON to clipboard', 'woocommerce-avatax'); ?>" onclick="event.stopPropagation();">
                                                        <span class="dashicons dashicons-clipboard"></span>
                                                        <span class="copy-text"><?php echo esc_html__('Copy', 'woocommerce-avatax'); ?></span>
                                                    </button>
                                                </div>
                                                <div class="json-preview-content" style="display: none; padding-left: 32px;">
                                                    <p class="description" style="margin-bottom: 10px; font-size: 11px;">
                                                        <?php echo esc_html__('Note: Use "$search" for search terms and "$filters" for OData filter expressions. The "$filters" field is optional and can be omitted for simple searches.', 'woocommerce-avatax'); ?>
                                                    </p>
                                                    <pre id="json_sample_content" style="background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px; overflow-x: auto;">
{
  "value": [
    {
      "$search": "test company",
      "$filters": "country eq 'Malaysia' and network eq 'Peppol'"
    },
    {
      "$search": "another company", 
      "$filters": "country eq 'Singapore' and documentType eq 'Invoice'"
    },
    {
      "$search": "simple search",
      "$filters": ""
    },
    {
      "$search": "BMW"
    }
  ]
}
                                                    </pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Build Search Queries Method -->
                                <div id="batch_builder_method" class="batch-method" style="display: none;
                                    background: #f9f9f9;  padding: 15px; border: 1px solid #ddd;
                                    border-radius: 4px; margin: 15px 0;">
                                    <div class="batch-builder-section">
                                        <h4 class="description" style="margin: 0;">
                                            <?php echo esc_html__('Add search terms and apply filters to build your batch search.',
                                            'woocommerce-avatax'); ?>
                                        </h4>
                                        
                                        <!-- Add New Search Entry Form -->
                                        <div class="add-search-entry" style="padding: 15px;">
                                            <div class="search-entry-form">
                                                <!-- Search Query Builder -->
                                                <div class="batch-query-builder" style="margin-bottom: 15px;">
                                                    <label><?php echo esc_html__('Search Query Builder', 'woocommerce-avatax'); ?></label>
                                                    <div class="batch-query-builder-fields" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-top: 5px;">
                                                        <input type="text" id="batch_search_term_1" class="ndi-search-input" placeholder="<?php echo esc_attr__('First search term *', 'woocommerce-avatax'); ?>" style="width: 200px; height: 32px;"/>
                                                        <select id="batch_search_operator" style="width: 80px;">
                                                            <option value="AND"><?php echo esc_html__('AND', 'woocommerce-avatax'); ?></option>
                                                            <option value="OR"><?php echo esc_html__('OR', 'woocommerce-avatax'); ?></option>
                                                        </select>
                                                        <input type="text" id="batch_search_term_2" class="ndi-search-input" placeholder="<?php echo esc_attr__('Second search term', 'woocommerce-avatax'); ?>" style="width: 200px; height: 32px;"/>
                                                    </div>
                                                    <!-- Hidden field to store the combined search term -->
                                                    <input type="hidden" id="new_search_term" />
                                                </div>
                                                
                                                <!-- Filters Section (below search terms) -->
                                                <div class="filters-section" id="batch_filters_section" style="display: none; margin-bottom: 15px;">
                                                    <label><?php echo esc_html__('Filters (Optional)', 'woocommerce-avatax'); ?></label>
                                                    <div id="batch_dynamic_filters" class="batch-filters-content" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 5px;">
                                                        <!-- Dynamic filters will be loaded here -->
                                                    </div>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div class="entry-actions">
                                                    <button type="button" id="btn_load_batch_filters" class="button-secondary actionButton" style="margin-right: 10px;">
                                                        <?php echo esc_html__('Load Filters', 'woocommerce-avatax'); ?>
                                                    </button>
                                                    <button type="button" id="btn_add_search_entry" class="button-primary">
                                                        <?php echo esc_html__('Add Entry', 'woocommerce-avatax'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Search Entries List -->
                                <div class="search-entries-list">
                                    <h4><?php echo esc_html__('Search Entries', 'woocommerce-avatax'); ?> (<span id="entries_count">0</span>)</h4>
                                    <div id="search_entries_container" style="margin: 15px 0 0 0;">
                                        <p class="no-entries" style="color: #666; font-style: italic;"><?php echo esc_html__('No search entries added yet. Add your first search entry above.', 'woocommerce-avatax'); ?></p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        </table>
                    </div>
                    
                    <!-- Common elements for both batch search methods -->
                    <!-- Generated JSON Preview -->
                    <div class="json-output" style="margin-top: 20px;">
                        <h4><?php echo esc_html__('Generated JSON', 'woocommerce-avatax'); ?></h4>
                        <textarea id="generated_json" rows="8" style="width: 100%; font-family: monospace; background: #f5f5f5;" readonly placeholder="<?php echo esc_attr__('JSON will be generated as you add search entries...', 'woocommerce-avatax'); ?>"></textarea>
                    </div>
                    
                    <div class="batch-controls" style="margin-top: 20px;">
                        <button type="button" id="btn_batch_search" class="button-primary actionButton" disabled>
                            <?php echo esc_html__('Start Batch Search', 'woocommerce-avatax'); ?>
                        </button>
                        <button type="button" id="btn_clear_batch" class="button-secondary">
                            <?php echo esc_html__('Clear', 'woocommerce-avatax'); ?>
                        </button>
                    </div>
                    
                    <div id="batch_progress" class="batch-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>
                        <p class="progress-text">Processing... <span id="progress_count">0</span> of <span id="total_count">0</span></p>
                    </div>
                    
                    <div id="batch_results" class="batch-results">
                        <!-- Batch search results will be populated here -->
                    </div>
                </div>
            </div>
            <div class="network-directory-tab-content">
                <!-- Batch Job Status Tab Content -->
                <div class="batch-status-section">
                    <h4><?php echo esc_html__('Batch Job Status', 'woocommerce-avatax'); ?></h4>
                    <p class="description"><?php echo esc_html__('View the status and results of your submitted batch searches.', 'woocommerce-avatax'); ?></p>
                    
                    <!-- Refresh Button -->
                    <div class="batch-status-controls" style="margin: 20px 0;">
                        <button type="button" id="refresh_batch_list" class="button button-primary actionButton">
                            <?php echo esc_html__('Refresh List', 'woocommerce-avatax'); ?>
                        </button>
                    </div>
                    
                    <!-- Batch List Table -->
                    <div id="batch_list_container" class="batch-list-container">
                        <div id="batch_list_loading" class="loading-message" style="display: none; text-align: center; padding: 20px;">
                            <div style="display: inline-flex; align-items: center; gap: 10px;">
                                <span class="spin actionButton" style="display: inline-block;"></span>
                                <span><?php echo esc_html__('Loading batch searches...', 'woocommerce-avatax'); ?></span>
                            </div>
                        </div>
                        
                        <div id="batch_list_error" class="error-message" style="display: none; padding: 15px; background: #ffeaea; border: 1px solid #dc3232; border-radius: 4px; margin: 10px 0;">
                            <p style="margin: 0; color: #dc3232;"></p>
                        </div>
                        
                        <div id="batch_list_empty" class="empty-message" style="display: none; text-align: center; padding: 40px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
                            <p style="margin: 0; color: #666;"><?php echo esc_html__('No batch searches found. Submit a batch search to see results here.', 'woocommerce-avatax'); ?></p>
                        </div>
                        
                        <table id="batch_list_table" class="wp-list-table widefat fixed striped" aria-describedby="batch_list_container" style="display: none;">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 200px;"><?php echo esc_html__('Batch Name', 'woocommerce-avatax'); ?></th>
                                    <th scope="col" style="width: 150px;"><?php echo esc_html__('Status', 'woocommerce-avatax'); ?></th>
                                    <th scope="col" style="width: 180px;"><?php echo esc_html__('Created By', 'woocommerce-avatax'); ?></th>
                                    <th scope="col" style="width: 150px;"><?php echo esc_html__('Created Date', 'woocommerce-avatax'); ?></th>
                                    <th scope="col" style="width: 150px;"><?php echo esc_html__('Last Modified', 'woocommerce-avatax'); ?></th>
                                    <th scope="col" style="width: 100px;"><?php echo esc_html__('Actions', 'woocommerce-avatax'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="batch_list_tbody">
                                <!-- Batch search rows will be populated here -->
                            </tbody>
                        </table>
                        
                        <!-- Batch Status Styles -->
                        <style>
                            .batch-status {
                                padding: 4px 8px;
                                border-radius: 3px;
                                font-size: 11px;
                                font-weight: bold;
                                text-transform: uppercase;
                                display: inline-block;
                            }
                            .batch-status.status-ready {
                                background: #d4edda;
                                color: #155724;
                                border: 1px solid #c3e6cb;
                            }
                            .batch-status.status-processing {
                                background: #fff3cd;
                                color: #856404;
                                border: 1px solid #ffeaa7;
                            }
                            .batch-status.status-error {
                                background: #f8d7da;
                                color: #721c24;
                                border: 1px solid #f5c6cb;
                            }
                            .batch-status.status-pending {
                                background: #d1ecf1;
                                color: #0c5460;
                                border: 1px solid #bee5eb;
                            }
                            .batch-status.status-unknown {
                                background: #e2e3e5;
                                color: #383d41;
                                border: 1px solid #d6d8db;
                            }
                            .batch-pagination a {
                                text-decoration: none;
                                padding: 6px 12px;
                                margin: 0 2px;
                                border: 1px solid #c3c4c7;
                                background: #f6f7f7;
                                color: #2c3338;
                                border-radius: 3px;
                                display: inline-block;
                                font-size: 13px;
                                line-height: 1.15384615;
                                min-height: 30px;
                                box-sizing: border-box;
                                vertical-align: middle;
                            }
                            .batch-pagination a:hover {
                                background: #f0f0f1;
                                border-color: #0a4b78;
                                color: #0a4b78;
                            }
                            .batch-pagination strong {
                                padding: 6px 12px;
                                margin: 0 2px;
                                background: #2271b1;
                                color: white;
                                border: 1px solid #2271b1;
                                border-radius: 3px;
                                display: inline-block;
                                font-size: 13px;
                                line-height: 1.15384615;
                                min-height: 30px;
                                box-sizing: border-box;
                                vertical-align: middle;
                                font-weight: 600;
                            }

                            .pagination-wrapper {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                flex-wrap: wrap;
                                gap: 10px;
                            }
                            .pagination-info {
                                text-align: left;
                                flex: 1;
                                min-width: 200px;
                            }
                            .pagination-controls {
                                text-align: right;
                                flex-shrink: 0;
                                display: flex;
                                align-items: center;
                                gap: 5px;
                            }
                            div#batch_list_pagination {
                                background-color: #FFFFFF;
                                padding: 15px;
                                box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                                border: 1px solid #c3c4c7;
                                border-radius: 4px;
                                margin-top: 20px;
                            }
                            @media (max-width: 768px) {
                                .pagination-wrapper {
                                    flex-direction: column;
                                    align-items: stretch;
                                }
                                .pagination-info,
                                .pagination-controls {
                                    text-align: center;
                                }
                                div#batch_list_pagination {
                                    padding: 12px;
                                }
                            }
                        </style>
                        
                        <!-- Pagination -->
                        <div id="batch_list_pagination" class="batch-pagination" style="display: none;">
                            <div class="pagination-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div class="pagination-info" style="text-align: left;">
                                    <span id="batch_pagination_info"></span>
                                </div>
                                <div class="pagination-controls" style="text-align: right;">
                                    <button type="button" id="batch_prev_page" class="button" disabled><?php echo esc_html__('Previous', 'woocommerce-avatax'); ?></button>
                                    <span id="batch_page_numbers"></span>
                                    <button type="button" id="batch_next_page" class="button" disabled><?php echo esc_html__('Next', 'woocommerce-avatax'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// @codeCoverageIgnoreEnd
?>