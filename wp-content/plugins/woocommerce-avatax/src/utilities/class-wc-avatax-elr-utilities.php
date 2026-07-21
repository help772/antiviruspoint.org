<?php
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

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_HS_API;
use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * WooCommerce AvaTax main plugin class.
 *
 * @since 2.7.0
 */
class WC_AvaTax_Elr_Utilities
{

    /* ECM Subscription names */
    const TYPE_AVATAX_ECMESSENTIALS = 'ECMEssentials';
    const TYPE_AVATAX_ECMPRO = 'ECMPro';
    const TYPE_AVATAX_ECMPREMIUM = 'ECMPremium';
    const TABLE_TYPE_FLAT = 'flat';
    const TABLE_TYPE_EAV = 'eav';
    const TABLE_TYPE_VERTICAL = 'vertical';
    const ARR_ELR_DOCUMENT_TYPE = [
        'order' => 'ubl-invoice',
        'refund' => 'ubl-creditnote',
        'b2bpayment-ereporting' => 'xml-b2bpayment-ereporting',
        'b2cpayment-ereporting' => 'xml-b2cpayment-ereporting',
        'application_response' => 'ubl-applicationresponse',
        'application_response_outbound' => 'ubl-applicationresponse',
    ];
    const AR_OUTBOUND_ENTITY_TYPE = 'application_response_outbound';
    const AR_OUTBOUND_DEFAULTS_VERSION_OPTION = 'wc_avatax_ar_outbound_defaults_version';
    const AR_OUTBOUND_SELECTED_FIELDS_OPTION = 'wc_avatax_elr_selected_fields_application_response_outbound';
    protected $MAIN_MAPPER_TABLE = "";
    protected $query = "";

    const PAYMENT_ENTITY_TYPES = array(
        'b2bpayment-ereporting',
        'b2cpayment-ereporting',
    );

    public function __construct()
    {
        global $wpdb;
        $this->MAIN_MAPPER_TABLE = $wpdb->prefix . "wc_orders";
    }

    /**
     * ELR document type options for admin UI dropdowns.
     *
     * @since 3.10.0
     *
     * @return array<string, string> Entity key => translated label.
     */
    public function get_elr_entity_type_options()
    {
        return array(
            'order' => __('Order', 'woocommerce-avatax'),
            'refund' => __('Refund', 'woocommerce-avatax'),
            'b2bpayment-ereporting' => __('B2B Payment e-Reporting', 'woocommerce-avatax'),
            'b2cpayment-ereporting' => __('B2C Payment e-Reporting', 'woocommerce-avatax'),
            'application_response' => __('AR-Inbound', 'woocommerce-avatax'),
            'application_response_outbound' => __('AR-Outbound', 'woocommerce-avatax'),
        );
    }

    /**
     * Whether the entity uses order (non-refund) WooCommerce records.
     *
     * @since 3.10.0
     *
     * @param string $entity_type Entity type key.
     * @return bool
     */
    public function is_order_like_elr_entity($entity_type)
    {
        return in_array($entity_type, array('order', 'refund', 'b2bpayment-ereporting', 'b2cpayment-ereporting', 'application_response_outbound'), true);
    }

    /**
     * Resolve Avalara document type for an entity.
     *
     * @since 3.10.0
     *
     * @param string $entity_type Entity type key.
     * @return string
     */
    public function get_elr_document_type($entity_type)
    {
        return self::ARR_ELR_DOCUMENT_TYPE[$entity_type] ?? '';
    }

    /**
     * Validates a SQL identifier (table or column name) against a strict allowlist.
     *
     * MySQL identifiers in this plugin's domain are limited to alphanumerics and underscore.
     * Any value that does not match is rejected so it can never reach a SQL string. This is
     * the defense in depth required even though identifiers come from a database-stored
     * mapper table — those values originate from admin user input and must be re-validated
     * at every use site (CWE-89).
     *
     * @since 3.10.0
     *
     * @param mixed $identifier Identifier candidate.
     * @return string Backtick-quoted identifier safe to embed in raw SQL, or empty string when invalid.
     */
    protected function escape_sql_identifier($identifier)
    {
        if (!is_string($identifier) || '' === $identifier) {
            return '';
        }
        if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            return '';
        }
        return '`' . $identifier . '`';
    }

    /**
     * Validates a comma-separated list of SQL identifiers.
     *
     * Used for fields like `selected_fields` which store column names as a comma list.
     * Returns an associative array keyed by the original (validated) identifier string and
     * valued by its backtick-quoted form. Invalid entries are silently dropped so a single
     * bad token cannot poison the whole query.
     *
     * @since 3.10.0
     *
     * @param mixed $list Either an array of identifiers or a comma-separated string.
     * @return array<string, string> [ raw identifier => backticked identifier ].
     */
    protected function escape_sql_identifier_list($list)
    {
        $items = is_array($list) ? $list : explode(',', (string) $list);
        $escaped = array();
        foreach ($items as $item) {
            $item = trim((string) $item);
            $safe = $this->escape_sql_identifier($item);
            if ('' !== $safe) {
                $escaped[$item] = $safe;
            }
        }
        return $escaped;
    }

    /**
     * Returns true when a candidate string is a syntactically valid SQL identifier.
     *
     * Wrapper around {@see escape_sql_identifier()} for boolean checks at intake.
     *
     * @since 3.10.0
     *
     * @param mixed $identifier Identifier candidate.
     * @return bool
     */
    protected function is_valid_sql_identifier($identifier)
    {
        return '' !== $this->escape_sql_identifier($identifier);
    }

    /**
     * Checks if ELR credentials are set.
     *
     * @internal
     *
     * @since 3.0.0
     *
     * @return bool
     */
    public function is_elr_enabled()
    {
        return (wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api());
    }

    /**
     * Gets the integration API for ELR
     *
     * @internal
     *
     * @since 3.0.0
     */
    public function get_integration_api($generateElrToken = false)
    {
        $api_environment = get_option('wc_avatax_elr_environment');


        return wc_avatax()->get_integration_api("", "", $api_environment, $generateElrToken);
    }

    /**
     * Disconnects the connection to ELR.
     *
     * @since 3.0.0
     *
     */
    public function disconnect_elr($is_update = false)
    {
        $integration_api = wc_avatax()->wc_avatax_utilities()->get_integration_api(true);
        $integration_api->delete_elr_configuration();

        global $wpdb;
        if (!$is_update) {
            update_option("wc_avatax_elr_environment", "");
            update_option("wc_avatax_elr_client_id", "");
            update_option("wc_avatax_elr_client_secret", "");
            delete_option('wc_avatax_elr_company');
            delete_option('wc_avatax_elr_selected_status');


            delete_transient('wc_avatax_elr_connection_status');
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_elr_company','wc_avatax_elr_tenant_id','wc_avatax_elr_custom_fields','wc_avatax_elr_environment','wc_avatax_elr_client_id','wc_avatax_elr_client_secret','wc_avatax_Seller_company_ID','wc_avatax_Seller_VAT_Id','wc_avatax_Seller_Peppol_ID','wc_avatax_elr_selected_custom_fields','wc_avatax_elr_company','wc_avatax_Seller_Registration_Name','wc_avatax_Seller_Tax_Identification_Number','wc_avatax_Seller_Passport_Number')");

            wc_avatax()->refresh_elr_api();
        } else {
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_elr_client_id','wc_avatax_elr_client_secret')");
        }

        // We will be using this common clear_transient() function for both avatax and elr
        //TODO: need to verify if this is correct with both avatax and elr when we will test both
        // Clear transient data
        $this->clear_transient();
    }

    /**
     * Clears transient data.
     * 
     * @since 3.0.0
     *
     */
    public function clear_transient()
    {

        delete_transient('wc_avatax_elr_connection_status');
        delete_transient('wc_avatax_elr_token');
        delete_transient('wc_avatax_elr_company_response');
    }

    /** Functions for Fields mapper functionality */

    /**
     * Get Table Refference/Structure Fields
     */
    public function getTableRefferenceFields($tablename, $withType = false, $withAllFields = true, $entityType = '')
    {
        return $this->getTableStructure($tablename, $withType, $withAllFields, $entityType);
    }

    public function getTableStructure($tablename, $withType = false, $withAllFields = true, $entityType = '')
    {
        global $wpdb;
        $fieldResults = [];
        $results = [];
        $selected_fields = [];
        //$tablename = $this->getTableName($tablename);
        $existCheckResults = $wpdb->query(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM information_schema.TABLES"
                . " WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME = %s",
                $tablename
            )
        );
        // $existCheckResultValue = array_values($existCheckResults[0]);
        if ($existCheckResults > 0) {
            //$query = "DESCRIBE `" . $tablename . "`";
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS"
                    . " WHERE TABLE_NAME = %s",
                    $tablename
                )
            );
            if ($results && count($results) > 0) {
                $fieldResults = [];
                if (!$withAllFields) {
                    if ($entityType && !empty($entityType)) {
                        $selectedFieldResults = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT selected_fields FROM avatax_einvoice_mapper WHERE main_table = %s AND entity_type = %s",
                                $tablename,
                                $entityType
                            )
                        );
                    } else {
                        $selectedFieldResults = $wpdb->get_results(
                            $wpdb->prepare(
                                "SELECT selected_fields FROM avatax_einvoice_mapper WHERE main_table = %s",
                                $tablename
                            )
                        );
                    }
                    if (empty($selectedFieldResults)) {
                        return $fieldResults;
                    }
                    $selected_fields = explode(",", $selectedFieldResults[0]->selected_fields);
                }
                foreach ($results as $result) {
                    if ($withType) {
                        if (!$withAllFields && in_array($result->COLUMN_NAME, $selected_fields)) {
                            $fieldResults[$result->COLUMN_NAME] = $result->DATA_TYPE;
                        } else if ($withAllFields) {
                            $fieldResults[$result->COLUMN_NAME] = $result->DATA_TYPE;
                        }
                    } else {
                        if (!$withAllFields && in_array($result->COLUMN_NAME, $selected_fields)) {
                            $fieldResults[] = $result->COLUMN_NAME;
                        } else if ($withAllFields) {
                            $fieldResults[] = $result->COLUMN_NAME;
                        }

                    }
                }
            }
        }
        return $fieldResults;

    }

    /**
     * Prepare Einvoice Mapper Collection for specific Invoice
     */
    public function getEinvoiceCollectionByInvoiceId($invoiceId = '', $entity_type = '')
    {
        $elrFormattedSchema = array();
        if (!empty($invoiceId)) {

            $entityObj = wc_get_order($invoiceId);
            $isKnownEntityType = $this->is_order_like_elr_entity($entity_type) || 'refund' === $entity_type;
            $isPaymentEntity = in_array($entity_type, self::PAYMENT_ENTITY_TYPES, true);
            $effectiveInvoiceId = $invoiceId;

            if (
                !$entityObj
                || !$isKnownEntityType
                || ('order' === $entity_type && $entityObj->get_parent_id())
                || ('refund' === $entity_type && !$entityObj->get_parent_id())
            ) {
                wc_avatax()->log_elr('Order or Refund not found');
                return $elrFormattedSchema;
            }

            if ($isPaymentEntity && 'shop_order_refund' === $entityObj->get_type() && $entityObj->get_parent_id()) {
                $effectiveInvoiceId = $invoiceId;
            }

            $allMapperRecords = $this->getEinvoiceMapperRecords($entity_type);
            $uniqueInvoiceRecords = $this->getUniqueInvoiceRecords($effectiveInvoiceId, $entity_type);
            $uniqueInvoiceRecords = $this->getApplicableFieldsInArray($allMapperRecords, $uniqueInvoiceRecords, $entity_type);

            // Added to handle the non-unique mapper overlap between B2B and B2C payment-reporting entities.
            $uniqueInvoiceRecords = $this->mergeParentRefundFallbackRecords(
                $uniqueInvoiceRecords,
                $entityObj,
                $entity_type
            );

            $elrFormattedSchema['metadata'] = $this->addMetadataField($entity_type);
            $elrFormattedSchema['conditionPayload'] = $this->addConditionalField($uniqueInvoiceRecords);
             $payload = [
                'customerEInvoicingData' => $this->get_entity_custom_fields_schema(
                            $entity_type,
                            'customer',
                            true,
                            true,
                            $effectiveInvoiceId
                        ),
                'CompanyEInvoicingData' => $this->get_entity_custom_fields_schema(
                            $entity_type,
                            'company',
                            true,
                            true,
                            $effectiveInvoiceId
                        ),
            ];

            if (self::AR_OUTBOUND_ENTITY_TYPE === $entity_type) {
                $payload['ar_outbound'] = $this->get_ar_outbound_fields(true, true);
            }

            $elrFormattedSchema['payload'] = array_merge(
                $payload,
                $uniqueInvoiceRecords
            );

            if ($entity_type == 'refund') {
                $order = wc_get_order($invoiceId);
                if ($order->get_parent_id()) {
                    $parent_order = wc_get_order($order->get_parent_id());
                    $elrFormattedSchema['payload'] = array_merge(
                        $elrFormattedSchema['payload'],
                        [
                            "additionalData" => [
                                "parentOrderDate" => $parent_order->get_date_created()->date('Y-m-d H:i:s'),
                                "parentPaymentMethod" => $parent_order->get_payment_method(),
                                "parentPaymentMethodTitle" => $parent_order->get_payment_method_title(),
                                "transaction_id" => $parent_order->get_transaction_id(),
                            ]
                        ]
                    );
                }
            }
        }
        return $elrFormattedSchema;
    }

    /**
     * Prepare Einvoice Mapper Collection for specific Invoice
     */
    public function getUniqueInvoiceRecords($invoiceId = '', $entity_type = '')
    {
        global $wpdb;

        $strMapperFlatTableJoinQuery = '';
        $flatTableInvoiceRecords = [];
        $restructuredInvoiceRecords = [];
        $uniqueInvoiceRecords = [];
        $schemaSkeletonWithData = [];
        if (!empty($invoiceId)) {
            $flatTableRecords = $this->getEinvoiceMapperRecords($entity_type, self::TABLE_TYPE_FLAT);
            $allMapperRecords = $this->getEinvoiceMapperRecords($entity_type);

            if ($flatTableRecords && count($flatTableRecords) > 0) {
                $mapperRecords = $this->prepareMainAndSecondaryTables($flatTableRecords);
                $strColumnsToSelect = $this->prepareColumnsToSelect($flatTableRecords);

                $strMapperFlatTableJoinQuery = $this->prepareMapperJoinQuery($mapperRecords, $strColumnsToSelect, $invoiceId);
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safely escaped
                $flatTableInvoiceRecords = $wpdb->get_results($strMapperFlatTableJoinQuery);

                if (count($flatTableInvoiceRecords) > 0) {
                    $restructuredInvoiceRecords = $this->restructureKeys($flatTableInvoiceRecords);
                    $uniqueInvoiceRecords = $this->removeDuplicateEntries($restructuredInvoiceRecords);
                    //$keyValuePairs = $this->buildKeyValuePairWithMapperData($allMapperRecords);
                    //wc_avatax()->log_elr("keyValuePairs". json_encode($keyValuePairs));
                    $eavAttributesRecords = $this->prepareEavAttributesRecords($uniqueInvoiceRecords, $invoiceId, $entity_type);
                    if ($eavAttributesRecords && !empty($eavAttributesRecords)) {
                        $uniqueInvoiceRecords = array_merge($uniqueInvoiceRecords, $eavAttributesRecords);
                    }

                    if ($entity_type == 'refund') {
                        $refund = wc_get_order($invoiceId);

                        if ($refund && $refund->get_type() === 'shop_order_refund') {
                            // Get the original order ID
                            $parent_order_id = $refund->get_parent_id();

                            $uniqueParentOrderRecords = $this->getUniqueInvoiceRecords($parent_order_id, 'order');
                        }
                    }

                    //$schemaSkeletonWithData = $this->prepareSchemaSkeleton($keyValuePairs, false, $uniqueInvoiceRecords);
                }
            }

            /**
             * Update null value from parent order if any
             */
            if (!empty($uniqueParentOrderRecords) && !empty($uniqueInvoiceRecords)) {

                foreach ($uniqueInvoiceRecords as $key => $record) {
                    foreach ($record as $field => $value) {
                        if (
                            is_null($value) &&
                            isset($uniqueParentOrderRecords[$key]) &&
                            isset($uniqueParentOrderRecords[$key][$field])
                        ) {

                            $uniqueInvoiceRecords[$key][$field] = $uniqueParentOrderRecords[$key][$field];
                        }
                    }
                }
            }
        }
        return $uniqueInvoiceRecords;
    }

    /** Gets the custom field data
     * 
     * @since 3.0.0
     * 
     * $type string to get either customer or company custom fields
     * $with_data to get the schema with data or with data type
     * returns array 
     */
    protected function getCustomFieldsSchema($type, $with_data = false, $selected_only = false, $invoice_id = null)
    {
        $custom_fields = array();
        $field_list = get_option('wc_avatax_elr_custom_fields', array());

        if (isset($field_list)) {
            // Convert to object if it's an array for consistent access
            if (is_array($field_list)) {
                $field_list = (object) $field_list;
            }

            switch ($type) {
                case 'customer':
                    if ($with_data && !empty($invoice_id)) {
                        $order = wc_get_order($invoice_id);
                        if (!empty($order->get_parent_id())) {
                            $order = wc_get_order($order->get_parent_id());
                        }

                        // Get the WP_User Object instance
                        $user = $order->get_user();
                        $customer_fields = isset($field_list->customer) ? (array) $field_list->customer : array();
                        foreach ($customer_fields as $field) {
                            if (!$selected_only || ($selected_only && $field->selected)) {
                                $custom_fields[$field->field_id] = $user->{$field->field_id};
                            }
                        }
                    } else {
                        $customer_fields = isset($field_list->customer) ? (array) $field_list->customer : array();
                        foreach ($customer_fields as $field) {
                            if (!$selected_only || ($selected_only && $field->selected)) {
                                $custom_fields[$field->field_id] = $field->data_type;
                            }
                        }
                    }
                    break;
                case 'company':
                    $company_fieds = isset($field_list->company) ? (array) $field_list->company : array();
                    foreach ($company_fieds as $field) {
                        if (!$selected_only || ($selected_only && $field->selected)) {
                            $custom_fields[$field->field_id] = ($with_data ? get_option($field->field_id, '') : $field->data_type);
                        }
                    }
                    break;
            }
        }

        return $custom_fields;
    }

    /** Sets the selected custom field
     * 
     * @since 3.0.0
     * 
     */
    protected function setSelectedCustomFieldSchema()
    {
        $selected_field_list = explode(',', get_option("wc_avatax_elr_selected_custom_fields", ''));
        $field_list = get_option('wc_avatax_elr_custom_fields', array());


        if (isset($selected_field_list)) {
            foreach ($field_list->customer as $field) {
                if (in_array(("JSON.customerEInvoicingData." . $field->field_id), $selected_field_list)) {
                    $field->selected = true;
                } else {
                    $field->selected = false;
                }
            }
            foreach ($field_list->company as $field) {
                if (in_array(("JSON.CompanyEInvoicingData." . $field->field_id), $selected_field_list)) {
                    $field->selected = true;
                } else {
                    $field->selected = false;
                }
            }
        }
        update_option("wc_avatax_elr_custom_fields", $field_list);
    }

    /**
     * Gets entity-specific custom-field schema.
     *
     * AR-Outbound exposes only required buyer/seller fields regardless of global
     * "selected custom fields" state to keep its mapper focused.
     *
     * @param string $entity_type ELR mapper entity.
     * @param string $type customer|company.
     * @param bool $with_data True to return values, false for datatypes.
     * @param bool $selected_only Keep parity with legacy caller contract.
     * @param int|null $invoice_id Optional order/refund id for customer data.
     * @return array
     * @since 0.0.0
     *
     */
    protected function get_entity_custom_fields_schema($entity_type, $type, $with_data = false, $selected_only = false, $invoice_id = null)
    {
        if (self::AR_OUTBOUND_ENTITY_TYPE !== $entity_type) {
            return $this->getCustomFieldsSchema($type, $with_data, $selected_only, $invoice_id);
        }

        if (!$selected_only) {
            return $this->getCustomFieldsSchema($type, $with_data, false, $invoice_id);
        }

        $custom_fields = $this->getCustomFieldsSchema($type, $with_data, false, $invoice_id);
        $selected_paths = $this->get_ar_outbound_selected_field_paths();
        $field_prefix = 'customer' === $type ? 'JSON.customerEInvoicingData.' : 'JSON.CompanyEInvoicingData.';
        $filtered_fields = [];

        foreach ($custom_fields as $field_id => $field_value) {
            if (!in_array($field_prefix . $field_id, $selected_paths, true)) {
                continue;
            }
            $filtered_fields[$field_id] = $field_value;
        }

        return $filtered_fields;
    }

    /**
     * Returns required custom field IDs for AR-Outbound mapping.
     *
     * @return array<string,array<int,string>>
     * @since 3.8.4
     *
     */
    protected function get_ar_outbound_required_custom_field_ids()
    {
        return [
            'company' => [
                'wc_avatax_Seller_SIREN_ID',
                'wc_avatax_Seller_SIRET_ID',
                'wc_avatax_Seller_Registration_Name',
            ],
            'customer' => [
                'wc_avatax_Buyer_SIREN_ID',
                'wc_avatax_Buyer_SIRET_ID',
                'wc_avatax_Buyer_Registration_Name',
                'wc_avatax_Buyer_Addressing_Line',
            ],
        ];
    }

    /**
     * Gets default-selected custom-field selectors for a mapper entity.
     *
     * @param string $entity_type Entity selected in mapper.
     * @return array<int,string>
     * @since 0.0.0
     *
     */
    protected function get_selected_custom_fields_for_entity($entity_type)
    {
        if (self::AR_OUTBOUND_ENTITY_TYPE === $entity_type) {
            return array_values(array_filter(
                $this->get_ar_outbound_selected_field_paths(),
                static function ($path) {
                    return 0 === strpos($path, 'JSON.customerEInvoicingData.')
                        || 0 === strpos($path, 'JSON.CompanyEInvoicingData.');
                }
            ));
        }

        $selected = get_option("wc_avatax_elr_selected_custom_fields", '');
        if (empty($selected)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $selected)));
    }

    /**
     * Gets persisted selected field paths for AR-Outbound.
     *
     * Falls back to the required default field set until the mapper is saved.
     *
     * @return array<int,string>
     * @since 3.8.4
     */
    protected function get_ar_outbound_selected_field_paths()
    {
        $saved = get_option(self::AR_OUTBOUND_SELECTED_FIELDS_OPTION, '');
        if (!empty($saved)) {
            return array_values(array_filter(array_map('trim', explode(',', $saved))));
        }

        $required = $this->get_ar_outbound_required_custom_field_ids();
        $selectors = [];

        foreach ($required['customer'] as $field_id) {
            $selectors[] = 'JSON.customerEInvoicingData.' . $field_id;
        }

        foreach ($required['company'] as $field_id) {
            $selectors[] = 'JSON.CompanyEInvoicingData.' . $field_id;
        }

        return array_merge(
            $selectors,
            [
                'JSON.ar_outbound.issue_date',
                'JSON.ar_outbound.issue_time',
            ]
        );
    }

    /**
     * Returns AR-Outbound mapper fields.
     *
     * These fields are exposed in the mapper/schema so AR-specific values can
     * be selected and mapped independently of order-table fields.
     *
     * @param bool $with_data True to return placeholder values, false for data types.
     * @param bool $selected_only True to return only currently selected fields.
     * @return array<string,string>
     * @since 3.8.4
     */
    protected function get_ar_outbound_fields($with_data = false, $selected_only = false)
    {
        $fields = [
            'issue_date' => $with_data ? current_time('Y-m-d') : 'date',
            'issue_time' => $with_data ? current_time('H:i:s') : 'string',
        ];

        if (!$selected_only) {
            return $fields;
        }

        $selected_paths = $this->get_ar_outbound_selected_field_paths();
        $selected_fields = [];

        foreach ($fields as $field_key => $field_value) {
            if (in_array('JSON.ar_outbound.' . $field_key, $selected_paths, true)) {
                $selected_fields[$field_key] = $field_value;
            }
        }

        return $selected_fields;
    }

    /**
     * Gets default-selected AR mapper fields for a mapper entity.
     *
     * @param string $entity_type Entity selected in mapper.
     * @return array<int,string>
     * @since 3.8.4
     */
    protected function get_ar_outbound_selected_fields($entity_type)
    {
        if (self::AR_OUTBOUND_ENTITY_TYPE !== $entity_type) {
            return [];
        }

        return array_values(array_filter(
            $this->get_ar_outbound_selected_field_paths(),
            static function ($path) {
                return 0 === strpos($path, 'JSON.ar_outbound.');
            }
        ));
    }

    public function addMetadataField($entity_type)
    {
        $doctype = '';
        if (isset($entity_type) && !empty($entity_type)) {
            $doctype = $this->get_elr_document_type($entity_type);
        }
        return array(
            "configId" => get_option('wc_avatax_website_id') . "_elr",  // Store ID
            "appId" => wc_avatax()->get_elr_connector_id(), // ELR Connector ID
            "companyId" => get_option("wc_avatax_elr_company"), // ELR Company ID
            "documentType" => $doctype // Avalara document type for the selected entity
        );
    }

    public function getApplicableFieldsInArray($allMapperRecords, $uniqueInvoiceRecords, $entity_type)
    {

        foreach ($allMapperRecords as $mapperRecord) {
            //$uniqueInvoiceConvertedRecord = [];
            if ($mapperRecord->table_type == 'vertical') {
                $uniqueInvoiceRecords[$mapperRecord->main_table] = $this->addVerticalTableData($mapperRecord, $entity_type);
                continue;
            }
            if ($mapperRecord->isarray && !empty($uniqueInvoiceRecords[$mapperRecord->main_table]) && !array_is_list($uniqueInvoiceRecords[$mapperRecord->main_table])) {
                $uniqueInvoiceRecords[$mapperRecord->main_table] = array($uniqueInvoiceRecords[$mapperRecord->main_table]);
            }
            // if (($mapperRecord->isarray) && gettype($uniqueInvoiceRecords[$mapperRecord->main_table]) != "array")  {
            //     $uniqueInvoiceConvertedRecord += $uniqueInvoiceRecords[$mapperRecord->main_table];
            //     $uniqueInvoiceRecords[$mapperRecord->main_table] = $uniqueInvoiceConvertedRecord;
            // }
        }
        return $uniqueInvoiceRecords;
    }

    public function addVerticalTableData($mapperRecord, $entity_type)
    {
        global $wpdb;
        $vertical_table_data = [];
        $key_array = explode(',', $mapperRecord->selected_fields);
        for ($i = 0; $i < count($key_array); $i++) {
            $res = $wpdb->get_results(
                $wpdb->prepare(
                    "select * from %i where %i = %s",
                    $mapperRecord->main_table,
                    $mapperRecord->eav_key_field,
                    trim($key_array[$i])
                )
            );
            if (!empty($res)) {
                $column_name = $mapperRecord->eav_value_field;
                $vertical_table_data[$key_array[$i]] = $res[0]->$column_name;
            }
        }
        return $vertical_table_data;

    }

    public function addConditionalField($payloadData, $withdata = true)
    {
        $conditionalPayload = [];
        $conditionalPayloadRecords = $this->getConditionalPayloadRecords();
        foreach ($conditionalPayloadRecords as $conditionalPayloadRecord) {
            $conditionalPayload[$conditionalPayloadRecord->conditional_param] = "string";
            if ($withdata) {
                if (!$conditionalPayloadRecord->filter_data && !empty($payloadData[$conditionalPayloadRecord->mapper_table])) {
                    $filter_inp_data = $payloadData[$conditionalPayloadRecord->mapper_table];
                    $conditionalPayload[$conditionalPayloadRecord->conditional_param] = $filter_inp_data[$conditionalPayloadRecord->mapper_field];
                    continue;
                }
                $filter_data_array = explode(',', $conditionalPayloadRecord->filter_data);
                $filter_field_array = explode(',', $conditionalPayloadRecord->filter_field);
                if (!empty($payloadData[$conditionalPayloadRecord->mapper_table])) {
                    $filter_inp_data = $payloadData[$conditionalPayloadRecord->mapper_table];
                    for ($i = 0; $i < count($filter_data_array); $i++) {
                        $filter_inp_data = array_filter($filter_inp_data, function ($value) use ($filter_data_array, $filter_field_array, $i) {
                            // Apply your condition here
                            return $value[trim($filter_field_array[$i])] == trim($filter_data_array[$i]); // This will filter out non-positive values
                        });
                    }
                    $conditionalPayload[$conditionalPayloadRecord->conditional_param] = array_values($filter_inp_data)[0][$conditionalPayloadRecord->mapper_field];
                }
            }
        }
        return $conditionalPayload;
    }

    public function getFilteredRecord($filteredConditionalPayloadRecords, $payloadData)
    {
        foreach ($filteredConditionalPayloadRecords as $filteredConditionalPayloadRecord) {
            $filter_field = $filteredConditionalPayloadRecord->filter_field;
            $filter_data = $filteredConditionalPayloadRecord->filter_data;
            if (gettype($payloadData) == "array") {
                $payloadData = array_filter(
                    $payloadData[$filteredConditionalPayloadRecord->mapper_table],
                    function ($value) use ($filter_field, $filter_data) {
                        return $value->$filter_field == $filter_data;
                    }
                );

            } else {
                if ($payloadData && $payloadData[$filter_field] == $filter_data) {
                    return $payloadData;
                } else {
                    return array();
                }

            }

        }
        return $payloadData;
    }
    /**
     * Save E-Invoice Mapping to DB
     */
    public function saveEinvoiceMapping($post)
    {

        global $wpdb;
        $isarray = $post['main_table_isarray'] == "on" ? 1 : 0;
        if ($this->validateEinvoiceMapperFields($post)) {
            if ($this->validateUniqueEinvoiceMapperRecord($post)) {

                if (isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_FLAT) {
                    $wpdb->query(
                        $wpdb->prepare(
                            "INSERT INTO avatax_einvoice_mapper "
                            . "(main_table, main_table_ref_field, secondary_table, secondary_table_ref_field, "
                            . "table_type, entity_type, is_default_table, isarray) "
                            . "VALUES (%s, %s, %s, %s, %s, %s, 0, %d)",
                            $post['main_table'],
                            $post['main_table_ref_field'],
                            $post['secondary_table'],
                            $post['secondary_table_ref_field'],
                            $post['table_type'],
                            $post['entity_type'],
                            $isarray
                        )
                    );


                } else if (isset($post['table_type']) && $post['table_type'] == self::TABLE_TYPE_EAV) {

                    $wpdb->query(
                        $wpdb->prepare(
                            "INSERT INTO avatax_einvoice_mapper "
                            . "(main_table, main_table_ref_field, secondary_table, secondary_table_ref_field, "
                            . "eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) "
                            . "VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 0, %d)",
                            $post['main_table'],
                            $post['main_table_ref_field'],
                            $post['secondary_table'],
                            $post['secondary_table_ref_field'],
                            $post['eav_key_field'],
                            $post['eav_value_field'],
                            $post['table_type'],
                            $post['entity_type'],
                            $isarray
                        )
                    );
                } else {
                    $wpdb->query(
                        $wpdb->prepare(
                            "INSERT INTO avatax_einvoice_mapper "
                            . "(main_table, main_table_ref_field, secondary_table, secondary_table_ref_field, "
                            . "eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) "
                            . "VALUES (%s, '', '', '', %s, %s, %s, %s, 0, %d)",
                            $post['main_table'],
                            $post['eav_key_field'],
                            $post['eav_value_field'],
                            $post['table_type'],
                            $post['entity_type'],
                            $isarray
                        )
                    );
                }

                // $this->messageManager->addSuccessMessage(__('Mapping Saved.'));
                return 'Mapping Saved.';
            } else {
                // $this->messageManager->addErrorMessage(__('Duplicate Mapping Entry. Main Table / EAV Entity should be unique.'));
                return 'Duplicate Mapping Entry. Main Table / EAV Entity should be unique.';
            }
        } else {
            // $this->messageManager->addErrorMessage(__('All inputs are required.'));
            return 'All inputs are required.';
        }
    }

    /**
     * Saves the selected schema fields and sends them to the CCS (Compliance Cloud Service).
     * 
     * This function processes the selected data fields, separates custom fields from standard fields,
     * updates the database with the selected fields, and sends the updated schema to CCS.
     * 
     * @param array  $selectedData Array of selected fields in dot notation (e.g., "JSON.sales_invoice.entity_id")
     * @param string $entityType   Optional. Entity type key (order, refund, b2bpayment-ereporting, etc.).
     * 
     * @return boolean Returns true on successful save and send, false on failure
     * 
     * @throws Exception Catches and logs any exceptions that occur during execution
     * 
     * @global wpdb $wpdb WordPress database access object
     */
    public function save_and_send_schema($selectedData, $entityType = '')
    {
        global $wpdb;
        try {
            //Performance log variables
            $execution_start = hrtime(true);
            $api_time = $execution_end = 0.0;

            $main_table = '';
            $selected_fields = [];
            $selected_custom_fields = [];
            foreach ($selectedData as $key => $data) {
                $columns = explode('.', $data); // e.g => JSON.sales_invoice.entity_id
                $totalColumns = count($columns);
                if ($columns && $totalColumns > 1) {
                    if ($columns[1] == "customerEInvoicingData" || $columns[1] == "CompanyEInvoicingData" || (self::AR_OUTBOUND_ENTITY_TYPE === $entityType && $columns[1] == "ar_outbound")) {
                        array_push($selected_custom_fields, $data);
                        unset($selectedData[$key]);
                    } else {
                        $main_table = $columns[($totalColumns - 2)];
                        $column = $columns[($totalColumns - 1)];
                        $selected_fields[$main_table][] = $column;
                    }
                }
            }
            // Persist selected mapper fields.
            if (self::AR_OUTBOUND_ENTITY_TYPE === $entityType) {
                update_option(self::AR_OUTBOUND_SELECTED_FIELDS_OPTION, implode(',', $selected_custom_fields));
            } else {
                update_option('wc_avatax_elr_selected_custom_fields', implode(',', $selected_custom_fields));
                $this->setSelectedCustomFieldSchema();
            }

            if (count($selected_fields) > 0) {
                if ($entityType && !empty($entityType)) {
                    $mapperRecordsCollection = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT * FROM avatax_einvoice_mapper WHERE entity_type = %s ORDER BY mapper_id ASC",
                            $entityType
                        )
                    );
                } else {
                    $mapperRecordsCollection = $wpdb->get_results("SELECT * FROM avatax_einvoice_mapper ORDER BY mapper_id ASC");
                }

                if ($mapperRecordsCollection) {
                    foreach ($mapperRecordsCollection as $row) {
                        $current_main_table = $row->main_table;
                        $current_selected_fields = '';

                        if (isset($selected_fields[$current_main_table])) {
                            $current_selected_fields = implode(',', $selected_fields[$current_main_table]);
                        }

                        if ($entityType) {
                            $wpdb->query(
                                $wpdb->prepare(
                                    "UPDATE avatax_einvoice_mapper SET selected_fields = %s WHERE main_table = %s AND entity_type = %s",
                                    $current_selected_fields,
                                    $current_main_table,
                                    $entityType
                                )
                            );
                        } else {
                            $wpdb->query(
                                $wpdb->prepare(
                                    "UPDATE avatax_einvoice_mapper SET selected_fields = %s WHERE main_table = %s",
                                    $current_selected_fields,
                                    $current_main_table
                                )
                            );
                        }
                    }
                }
            }
            $response = $this->send_einvoice_schema_to_ccs('POST', $entityType);
            $api_time = $response->get_response_time();

            $execution_end = hrtime(true);
            $execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
            $connector_time = $execution_time - $api_time;
            wc_avatax()->elr_logger()->log_performance_elr("SaveAndSendToElr", "save_and_send_schema", "Save mapping and send schema to CCS.", "", "", $connector_time, $api_time, 0, "", "");

            return true;
        } catch (Exception $e) {
            wc_avatax()->elr_logger()->log_exception("SaveAndSendToElr", "save_and_send_schema", $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Sends ELR Schema to CCS
     *
     * @internal
     *
     * @since 2.9.0
     *
     * @return void
     */
    public function send_einvoice_schema_to_ccs($type, $entityType)
    {
        if ($this->is_elr_enabled()) {
            $doctype = '';
            if (isset($entityType) && !empty($entityType)) {
                $doctype = $this->get_elr_document_type($entityType);
            }

            $data = $this->getCCSSchema($entityType);
            $generateElrToken = true;
            $integrationapi = wc_avatax()->wc_avatax_elr_utilities()->get_integration_api($generateElrToken);
            $parsedData = $this->updateArrayStructureForElr($data);
			return $integrationapi->send_elr_schema_to_ccs($type, $parsedData, $doctype);
		}
	}

    /**
     * Field metadata for the Application Response (CDAR) target schema.
     *
     * Maps the option-array keys (PascalCase, e.g. `RequestedActionCode` — the
     * canonical UBL element names that we persist in
     * `wc_avatax_elr_application_response_mapping`) to the snake_case keys,
     * JSON types and display names that ELR Studio expects on the ERP
     * (right-hand) side of the inbound mapping UI.
     *
     * @return array<string, array{key:string,type:string,displayName:string}>
     * @since 3.8.4
     *
     */
    public function get_application_response_field_definitions()
    {
        return array(
            'RequestedActionCode' => array('key' => 'requested_action_code', 'type' => 'string', 'displayName' => 'Requested Action Code'),
            'RequestedAction' => array('key' => 'requested_action', 'type' => 'string', 'displayName' => 'Requested Action'),
            'StatusReasonCode' => array('key' => 'status_reason_code', 'type' => 'string', 'displayName' => 'Status Reason Code'),
            'StatusReason' => array('key' => 'status_reason', 'type' => 'string', 'displayName' => 'Status Reason'),
        );
    }

    /**
     * Builds the ERP-side target schema for the inbound Application Response
     * mapping in ELR Studio.
     *
     * Studio already owns the UBL (left-hand) side of the inbound mapper for
     * the AR document, so we only need to publish the WooCommerce target
     * fields the seller ticked on the Data Selector tab. Studio renders these
     * as draggable nodes under `OutputSchema > wp_wc_orders` and lets the
     * seller wire them up to the matching UBL paths.
     *
     * `original_invoice_id` is always included regardless of the seller's
     * checkbox selections — it is the only reliable way to match an inbound
     * AR back to the originating WooCommerce order, so it is required for
     * downstream order-meta persistence.
     *
     * @param array<string,bool> $mapping Mapping option as stored in
     *                                    `wc_avatax_elr_application_response_mapping`,
     *                                    keyed by PascalCase UBL element name.
     * @return array{wp_wc_orders: array<string, array{type:string,displayName:string}>}
     * @since 3.8.4
     *
     */
    public function build_application_response_target_schema(array $mapping)
    {
        $definitions = $this->get_application_response_field_definitions();
        $wp_wc_orders = array(
            'original_invoice_id' => array(
                'type' => 'string',
                'displayName' => 'Original Invoice Id',
            ),
        );

        foreach ($definitions as $option_key => $field_def) {
            if (empty($mapping[$option_key])) {
                continue;
            }
            $wp_wc_orders[$field_def['key']] = array(
                'type' => $field_def['type'],
                'displayName' => $field_def['displayName'],
            );
        }

        return array('wp_wc_orders' => $wp_wc_orders);
    }

    /**
     * Posts the seller's chosen Application Response target schema to CCS so
     * the inbound studio mapper exposes the ticked WooCommerce fields as
     * mapping targets.
     *
     * Reuses the existing `post_payload_schema` request as the outbound
     * order/refund flows but sets `flowType=inbound` and uses doctype
     * `ubl-applicationresponse`. Studio supplies the UBL source schema
     * itself; we only publish the ERP target shape.
     *
     * @param array<string,bool> $mapping Mapping option as stored in
     *                                    `wc_avatax_elr_application_response_mapping`.
     *                                    Must contain at least one truthy value.
     * @param string $type HTTP verb. Defaults to POST. The
     *                                    underlying integration API will
     *                                    auto-fall-back to PUT on a 409 conflict.
     * @return mixed The API response object, or `new stdClass()` on failure /
     *               when ELR is disabled.
     * @since 3.8.4
     *
     */
    public function send_application_response_schema_to_ccs(array $mapping, $type = 'POST')
    {
        if (!$this->is_elr_enabled()) {
            return new stdClass();
        }

        $execution_start = hrtime(true);
        $api_time = 0.0;

        try {
            $doctype = self::ARR_ELR_DOCUMENT_TYPE['application_response'];
            $schema = $this->build_application_response_target_schema($mapping);

            $integration_api = wc_avatax()->wc_avatax_elr_utilities()->get_integration_api(true);
            $response = $integration_api->send_elr_schema_to_ccs($type, $schema, $doctype, 'inbound');

            if (is_object($response) && method_exists($response, 'get_response_time')) {
                $api_time = (float)$response->get_response_time();
            }

            $execution_end = hrtime(true);
            $execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
            $connector_time = max(0.0, $execution_time - $api_time);

            wc_avatax()->elr_logger()->log_performance_elr(
                'SaveAndSendApplicationResponseToElr',
                'send_application_response_schema_to_ccs',
                'Save AR mapping selection and send target schema to CCS.',
                '',
                '',
                $connector_time,
                $api_time,
                0,
                '',
                ''
            );

            return $response;
        } catch (Exception $e) {
            wc_avatax()->elr_logger()->log_exception(
                'SaveAndSendApplicationResponseToElr',
                'send_application_response_schema_to_ccs',
                $e->getMessage(),
                $e->getTraceAsString()
            );
            return new stdClass();
        }
    }

    /**
     * Formate schema for CCS API.
     *
     * @since 2.9.0
     *
     * @param array $array Data to covert in CCS accepted formate
     */
    public function updateArrayStructureForElr(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->updateArrayStructureForElr($value); // Recursive call for nested arrays
            } else {
                // Assuming the current value is a string that should be replaced with a structured array
                $name = ucwords(str_replace('_', ' ', $key)); // Capitalize the key to create a display name

                $dataType = $this->getSupportedDataType($value);

                $value = [
                    'type' => $dataType,
                    'displayName' => $name
                ];
            }
        }

        return $array;
    }

    /**
     * Convert data type into CCS JSON supported data type
     *
     * @param string $datatype
     * @return string
     */
    public function getSupportedDataType($datatype)
    {
        $dataTypeArray = [
            "number" => ["number", "int", "float", "smallint", "decimal", "double", "bigint", "tinyint"],
            "string" => ["text", "string", "varchar", "select", "hidden"],
            "date" => ["date", "datetime", "timestamp"],
            "boolean" => ["boolean"],
        ];

        if (!empty($datatype)) {
            foreach ($dataTypeArray as $key => $value) {
                if (in_array($datatype, $value)) {
                    return $key;
                }
            }
        }

        return "string";
    }

    /**
     * Prepare Einvoice Mapper Schema for CCS
     */
    public function getCCSSchema($entityType)
    {
        $records = $this->getEinvoiceMapperRecords($entityType);
        $schema = [];
        $keyValuePairs = [];
        if ($records && count($records) > 0) {
            $keyValuePairs = $this->buildKeyValuePair($records);

            $schema = $this->prepareSchemaForCCS($keyValuePairs, $entityType);
        }
        if (self::AR_OUTBOUND_ENTITY_TYPE === $entityType) {
            $schema['ar_outbound'] = $this->get_ar_outbound_fields(false, true);
        }
        $schema['customerEInvoicingData'] = $this->get_entity_custom_fields_schema($entityType, 'customer', false, true);
        $schema['CompanyEInvoicingData'] = $this->get_entity_custom_fields_schema($entityType, 'company', false, true);
        if ($entityType == 'refund') {
            $schema['additionalData']['parentOrderDate'] = "string";
            $schema['additionalData']['parentPaymentMethod'] = "string";
            $schema['additionalData']['parentPaymentMethodTitle'] = "string";
            $schema['additionalData']['transaction_id'] = "string";
        }
        
        return $schema;
    }

    /**
     * Prepare mapper schema sckeleton
     */
    public function prepareSchemaForCCS($tree, $entityType, $withFields = true, $data = []) {
        $schemaArray = [];
        foreach ($tree as $item) {
            $parentKey = 'parent-flat';
            $isRecordFlat = true;
            if (isset($item['parent-eav'])) {
                $parentKey = 'parent-eav';
                $isRecordFlat = false;
            }
            
            if (!isset($schemaArray[$item[$parentKey]])) {
                $schemaArray[$item[$parentKey]] = [];
                if ($withFields) {
                    if ($isRecordFlat) {
                        $parentArray = $this->getTableRefferenceFields($item[$parentKey], true, false, $entityType);
                    } else {
                        $parentArray = $this->getAllEntityAttributes($item[$parentKey], '', false, $entityType);
                    }


                    $schemaArray[$item[$parentKey]] = $parentArray;
                } else if (count($data) > 0) {
                    $schemaArray[$item[$parentKey]] = isset($data[$item[$parentKey]]) ? $data[$item[$parentKey]] : [];
                }
            }
            
        }
        return $schemaArray;
    }


	/**
     * Validate all Fields for Mapper
     *
     * Every field that ends up referenced as a SQL table or column name is also matched
     * against the strict identifier allowlist so it can never reach a query as anything
     * other than a backtick-safe identifier (CWE-89 defense at intake).
     */
    public function validateEinvoiceMapperFields($post) {
        $required_by_type = array(
            self::TABLE_TYPE_FLAT => array(
                'main_table',
                'main_table_ref_field',
                'secondary_table',
                'secondary_table_ref_field'
            ),
            self::TABLE_TYPE_EAV => array(
                'main_table',
                'main_table_ref_field',
                'secondary_table',
                'secondary_table_ref_field',
                'eav_key_field',
                'eav_value_field'
            ),
            self::TABLE_TYPE_VERTICAL => array('main_table', 'eav_key_field', 'eav_value_field'),
        );

        if (!isset($post['table_type']) || !isset($required_by_type[$post['table_type']])) {
            return false;
        }

        $identifier_fields = array(
            'main_table',
            'main_table_ref_field',
            'secondary_table',
            'secondary_table_ref_field',
            'eav_key_field',
            'eav_value_field',
        );

        foreach ($required_by_type[$post['table_type']] as $field) {
            if (!isset($post[$field]) || '' === $post[$field]) {
                return false;
            }
        }

        foreach ($identifier_fields as $field) {
            if (isset($post[$field]) && '' !== $post[$field] && !$this->is_valid_sql_identifier($post[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate if Mapper entry is unique or not
     */
    public function validateUniqueEinvoiceMapperRecord($data)
    {
        global $wpdb;

        $existCheckResults = 0; // Initialize variable
        if (isset($data['entity_type'])) {
            $existCheckResults = $wpdb->query(
                $wpdb->prepare(
                    "SELECT 1 FROM avatax_einvoice_mapper WHERE main_table = %s AND entity_type = %s",
                    trim($data['main_table']),
                    trim($data['entity_type'])
                )
            );
        } else {
            $existCheckResults = $wpdb->query(
                $wpdb->prepare(
                    "SELECT 1 FROM avatax_einvoice_mapper WHERE main_table = %s",
                    trim($data['main_table'])
                )
            );
        }
        if ($existCheckResults == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function flattenJson($json, $prefix = '', $flattenTopLevelOnly = true)
    {
        $flattenedArray = [];

        foreach ($json as $key => $value) {
            $newPrefix = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value)) {
                $flattenedArray = array_merge($flattenedArray, $this->flattenJson($value, $newPrefix));
            } else {
                $flattenedArray[$newPrefix] = $value;
            }
        }

        return $flattenedArray;
    }

    public function getEinvoiceMapperRecords($entityType = '', $tableType = '')
    {
        global $wpdb;

        if (self::AR_OUTBOUND_ENTITY_TYPE === $entityType) {
            $this->ensure_application_response_outbound_mapper_defaults();
        }

        if ($tableType && !empty($tableType)) {
            if ($entityType && !empty($entityType)) {
                return $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM avatax_einvoice_mapper WHERE mapper_id IN "
                        . "(SELECT MIN(mapper_id) FROM avatax_einvoice_mapper "
                        . "WHERE table_type = %s AND entity_type = %s GROUP BY main_table) "
                        . "ORDER BY mapper_id ASC",
                        $tableType,
                        $entityType
                    )
                );
            } else {
                return $wpdb->get_results($wpdb->prepare("SELECT * FROM avatax_einvoice_mapper WHERE mapper_id IN (SELECT MIN(mapper_id) FROM avatax_einvoice_mapper WHERE table_type = %s GROUP BY main_table) ORDER BY mapper_id ASC", $tableType));
            }
        } else if ($entityType && !empty($entityType)) {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM avatax_einvoice_mapper WHERE mapper_id IN (SELECT MIN(mapper_id) FROM avatax_einvoice_mapper WHERE entity_type = %s GROUP BY main_table) ORDER BY mapper_id ASC", $entityType));
        } else {
            return $wpdb->get_results("SELECT * FROM avatax_einvoice_mapper WHERE mapper_id IN (SELECT MIN(mapper_id) FROM avatax_einvoice_mapper GROUP BY main_table) ORDER BY mapper_id ASC");
        }
    }

    /**
     * Ensures mapper defaults exist for AR-Outbound.
     *
     * Keeps the AR-Outbound payload minimal: only the `{$prefix}wc_orders`
     * fields plus the `{$prefix}wc_order_addresses` table. The addresses row
     * is required so the existing (unchanged) {@see addConditionalField()}
     * can resolve `conditionPayload.destinationCountry` from the shipping
     * address; only the fields needed for that condition are selected.
     *
     * @since 3.8.4
     *
     * @return void
     */
    protected function ensure_application_response_outbound_mapper_defaults()
    {
        global $wpdb;

        $defaults_version = (int) get_option(self::AR_OUTBOUND_DEFAULTS_VERSION_OPTION, 0);
        if (9 === $defaults_version) {
            return;
        }

        $outbound_entity_type = self::AR_OUTBOUND_ENTITY_TYPE;

        // Reset AR-outbound mapper rows so the default seed can expose the
        // same tax source tables used by the tested invoice mappings.
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM avatax_einvoice_mapper WHERE entity_type = %s",
                $outbound_entity_type
            )
        );

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table prefix is safe; entity_type is a class constant.
        $wpdb->query("INSERT INTO avatax_einvoice_mapper (main_table, main_table_ref_field, secondary_table, secondary_table_ref_field, selected_fields, eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) VALUES 
            ('" . $wpdb->prefix . "wc_orders', 'id', NULL, NULL, 'id,total_amount,tax_amount,date_created_gmt', '', '', 'flat', '" . esc_sql($outbound_entity_type) . "', 1, 0),
            ('" . $wpdb->prefix . "wc_order_addresses', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,address_type,country', NULL, NULL, 'flat', '" . esc_sql($outbound_entity_type) . "', 1, 1),
            ('" . $wpdb->prefix . "woocommerce_order_items', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL, 'flat', '" . esc_sql($outbound_entity_type) . "', 1, 1),
            ('" . $wpdb->prefix . "woocommerce_order_itemmeta', 'order_item_id', '" . $wpdb->prefix . "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', '" . esc_sql($outbound_entity_type) . "', 1, 0),
            ('" . $wpdb->prefix . "usermeta', 'user_id', '" . $wpdb->prefix . "wc_orders', 'customer_id', 'wc_avatax_Buyer_Is_Business', 'meta_key', 'meta_value', 'eav', '" . esc_sql($outbound_entity_type) . "', 1, 0)");

        update_option(self::AR_OUTBOUND_DEFAULTS_VERSION_OPTION, 9);
    }

    public function getEinvoiceConditionalMapperRecords()
    {
        global $wpdb;

        return $wpdb->get_results("SELECT main_table, CASE WHEN MAX(isarray) = 1 THEN 1 ELSE 0 END AS isarray, GROUP_CONCAT(DISTINCT selected_fields ORDER BY mapper_id ASC SEPARATOR ',') AS selected_fields FROM avatax_einvoice_mapper GROUP BY main_table ORDER BY main_table");
    }

    public function getConditionalPayloadRecords($inMultipleRow = false)
    {
        global $wpdb;

        if ($inMultipleRow) {
            return $wpdb->get_results("SELECT mp.*, fl.filter_id, fl.filter_field, fl.filter_data FROM avatax_conditional_payload_mapper mp LEFT JOIN avatax_conditional_payload_filter fl ON mp.conditional_mapper_id = fl.conditional_mapper_id");
        } else {
            return $wpdb->get_results("SELECT conditional_param, MAX(mapper_table) as mapper_table, MAX(mapper_field) as mapper_field, GROUP_CONCAT(filter_data ORDER BY filter_data SEPARATOR ', ') as filter_data, GROUP_CONCAT(filter_field ORDER BY filter_data SEPARATOR ', ') as filter_field FROM avatax_conditional_payload_mapper mp LEFT JOIN avatax_conditional_payload_filter fl ON mp.conditional_mapper_id = fl.conditional_mapper_id GROUP BY conditional_param");
        }
    }

    /**
     * Separate Main and Secondary Tables
     */
    public function prepareMainAndSecondaryTables($records = [])
    {
        $mapperRecords = [];
        if ($records && count($records) > 0) {
            $i = 0;
            foreach ($records as $record) {
                if ($record->main_table == $this->MAIN_MAPPER_TABLE) {
                    $mapperRecords['main'] = $record;
                } else {
                    $mapperRecords['secondary'][$i] = $record;
                }
                $i++;
            }
        }
        return $mapperRecords;
    }

    /**
     * Prepare All columns with Alias which needs to select
     *
     * Both the table name and each selected column come from the mapper table, where they
     * originate as admin-supplied free-text. Each is re-validated against the SQL identifier
     * allowlist before being concatenated into the SELECT list (CWE-89).
     */
    public function prepareColumnsToSelect($records = [])
    {
        $strColumnWithAlias = '';
        if ($records && count($records) > 0) {
            foreach ($records as $record) {
                $main_table_safe = $this->escape_sql_identifier($record->main_table);
                if ('' === $main_table_safe) {
                    continue;
                }

                $strFields = $record->selected_fields;
                if (empty($strFields)) {
                    continue;
                }

                $field_map = $this->escape_sql_identifier_list($strFields);
                foreach ($field_map as $field_raw => $field_safe) {
                    // Alias remains in the legacy "<table>|<column>" form so restructureKeys() keeps working.
                    // Both halves have already been allow-listed to [A-Za-z0-9_], so the single-quoted alias
                    // cannot contain a quote or other SQL metacharacter.
                    $strColumnWithAlias .= $main_table_safe
                        . '.' . $field_safe
                        . " AS '" . $record->main_table
                        . '|' . $field_raw . "',";
                }
            }
            $strColumnWithAlias = trim($strColumnWithAlias, ',');
        }
        return $strColumnWithAlias;
    }

    /**
     * Prepare Mapper Join Query to select all records
     *
     * All identifiers come from the mapper table (admin-supplied) and are validated against
     * the strict identifier allowlist before being embedded. The invoice id is bound through
     * $wpdb->prepare() with a %s placeholder so it cannot break out of its quoted context
     * (CWE-89). $strColumnsToSelect is built by prepareColumnsToSelect(), which performs the
     * same validation on every identifier it emits.
     */
    public function prepareMapperJoinQuery($mapperRecords = [], $strColumnsToSelect = '*', $invoiceId = '')
    {
        global $wpdb;
        $strMapperJoinQuery = '';
        if (!($mapperRecords && count($mapperRecords) > 0) || empty($invoiceId)) {
            return $strMapperJoinQuery;
        }

        if (!isset($mapperRecords['main'])) {
            return 'MainTableNotFound';
        }

        $main_table_safe = $this->escape_sql_identifier($mapperRecords['main']->main_table);
        $main_ref_field_safe = $this->escape_sql_identifier($mapperRecords['main']->main_table_ref_field);
        if ('' === $main_table_safe || '' === $main_ref_field_safe) {
            return 'MainTableNotFound';
        }

        if ('' === (string) $strColumnsToSelect) {
            $strColumnsToSelect = '*';
        }

        $strMapperJoinQuery .= 'SELECT ' . $strColumnsToSelect . ' FROM ' . $main_table_safe . ' ';

        if (isset($mapperRecords['secondary']) && count($mapperRecords['secondary']) > 0) {
            foreach ($mapperRecords['secondary'] as $secondaryMapper) {
                $sec_main_safe = $this->escape_sql_identifier($secondaryMapper->main_table);
                $sec_main_ref_safe = $this->escape_sql_identifier($secondaryMapper->main_table_ref_field);
                $sec_secondary_safe = $this->escape_sql_identifier($secondaryMapper->secondary_table);
                $sec_secondary_ref_safe = $this->escape_sql_identifier($secondaryMapper->secondary_table_ref_field);
                if (
                    '' === $sec_main_safe ||
                    '' === $sec_main_ref_safe ||
                    '' === $sec_secondary_safe ||
                    '' === $sec_secondary_ref_safe
                ) {
                    continue;
                }
                $strMapperJoinQuery .= 'LEFT JOIN ' . $sec_main_safe
                    . ' ON ' . $sec_main_safe . '.'
                    . $sec_main_ref_safe . ' = '
                    . $sec_secondary_safe . '.' . $sec_secondary_ref_safe . ' ';
            }
        }

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $main_table_safe and $main_ref_field_safe come from escape_sql_identifier() which restricts to [A-Za-z0-9_]; $invoiceId is bound via the %s placeholder.
        $strMapperJoinQuery .= $wpdb->prepare('WHERE ' . $main_table_safe . '.' . $main_ref_field_safe . ' = %s', $invoiceId);

        return $strMapperJoinQuery;
    }

    /**
     * Get mapper table rows with html
     */
    public function getMapperTableRows()
    {
        global $wpdb;
        $records = "";
        $entityType = sanitize_text_field(Framework\SV_WC_Helper::get_requested_value('entity'));
        $results = $this->getEinvoiceMapperRecords($entityType);
        if (!empty($results)) {

            // The Delete anchor uses href='#' rather than 'javascript:void(0);' because
            // the consumer of this string echoes it through wp_kses(), which strips the
            // javascript: URI scheme as an XSS defence and would leave the anchor with
            // a broken href. The click is wired up in wc-avatax-admin-elr.js by class
            // (.tbl_mapper_delete) and that handler calls e.preventDefault() to stop
            // the '#' fragment navigation.
            foreach ($results as $result) {
                $records = $records . "<tr><td style='display:none;'>" . $result->mapper_id . "</td><td>" . ($result->table_type == 'flat' ? "Flat" : ($result->table_type == 'eav' ? 'EAV' : 'Vertical')) . "</td><td>" . $result->main_table . "</td><td> " . $result->main_table_ref_field . "</td><td>" . $result->secondary_table . "</td><td>" . $result->secondary_table_ref_field . "</td><td>" . $result->eav_key_field . "</td><td>" . $result->eav_value_field . "</td><td>" . ($result->isarray ? "Yes" : "No") . "</td><td>" . ($result->is_default_table ? "" : "<a href='#' class='tbl_mapper_delete' id='tbl_mapper_delete'>Delete</a>") . "</td></tr>";
            }
        }
        return $records;
    }

    /**
     * Get Conditional mapper table rows with html
     */
    public function getConditionalMapperTableRows()
    {
        global $wpdb;
        $records = "";
        $results = $this->getConditionalPayloadRecords(true);
        if (!empty($results)) {

            // See note in getMapperTableRows(): href='#' is required for the wp_kses()
            // pass that the caller applies; the click handler in
            // wc-avatax-admin-elr.js (.tbl_condition_delete) calls e.preventDefault().
            foreach ($results as $result) {
                $records = $records . "<tr><td style='display:none;'>" . $result->conditional_mapper_id . "</td><td style='display:none;'>" . $result->filter_id . "</td><td>" . $result->conditional_param . "</td><td>" . $result->mapper_table . "</td><td>" . $result->mapper_field . "</td><td> " . $result->filter_field . "</td><td>" . $result->filter_data . "</td><td><a href='#' class='tbl_condition_delete' id='tbl_condition_delete'>Delete</a></td></tr>";
            }
        }
        return $records;
    }

    /**
     * Prepare Einvoice Mapper Schema for Preview
     */
    public function getMapperSchema($entityType = '')
    {
        $records = $this->getEinvoiceMapperRecords($entityType);
        $schemaSkeleton = [];
        $keyValuePairs = [];
        if ($records && count($records) > 0) {
            $keyValuePairs = $this->buildKeyValuePair($records);
            $schemaSkeleton = $this->prepareSchemaSkeleton($keyValuePairs, true, array(), $entityType);
            if (self::AR_OUTBOUND_ENTITY_TYPE === $entityType) {
                $schemaSkeleton['ar_outbound'] = $this->get_ar_outbound_fields(false);
            }
            $schemaSkeleton['customerEInvoicingData'] = $this->get_entity_custom_fields_schema($entityType, 'customer', false, false);
            $schemaSkeleton['CompanyEInvoicingData'] = $this->get_entity_custom_fields_schema($entityType, 'company', false, false);
            //Add this line when sending the schema to CCS for adding Conditional Payload Attribute.
            //$schemaSkeleton['conditionPayload'] = $this->addConditionalField($uniqueInvoiceRecords, false);
        }
        // $this->getEinvoiceCollectionByInvoiceId("145", 'order');
        return $schemaSkeleton;
    }

    /**
     * Get Einvoice Selected Fields Schema
     */
    public function getEinvoiceSelectedFieldsSchema($entityType = '')
    {
        $records = $this->getEinvoiceMapperRecords($entityType);
        $resultSelectedFields = [];
        if ($records && count($records) > 0) {
            foreach ($records as $record) {
                if (!empty($record->selected_fields)) {
                    $arrDbSelectedFields = explode(',', $record->selected_fields); // Convert comma-separated string to array.
                    if (count($arrDbSelectedFields) > 0) {
                        foreach ($arrDbSelectedFields as $dbSelectedField) {
                            $resultSelectedFields[] = 'JSON.' . $record->main_table . '.' . $dbSelectedField;
                        }
                    }
                }
            }
        }
        // Add selected custom fields for the current entity.
        $resultSelectedFields = array_merge(
            $resultSelectedFields,
            $this->get_selected_custom_fields_for_entity($entityType),
            $this->get_ar_outbound_selected_fields($entityType)
        );
        return json_encode($resultSelectedFields);
    }

    /**
     * Prepare key pair value for mapper schema
     */
    public function buildKeyValuePair($records)
    {
        $keyValuePairs = [];
        foreach ($records as $record) {
            // if ($record->secondary_table)) {
            if ($record->table_type == self::TABLE_TYPE_FLAT) {
                $keyValuePairs[] = ['parent-flat' => $record->main_table];
            } else {
                $keyValuePairs[] = ['parent-eav' => $record->main_table];
            }

            // }
        }
        return $keyValuePairs;
    }

    /**
     * Prepare key pair value for Invoice Data
     * This method will be used to separate out parent,child and flat,eav records for Invoice Data method
     */
    public function buildKeyValuePairWithMapperData($records)
    {
        $keyValuePairs = [];
        foreach ($records as $record) {
            if (!empty($record->secondary_table) && !empty($record->selected_fields)) { // Consider record, only if it is atleast one field selected.
                if ($record->table_type == self::TABLE_TYPE_FLAT) {
                    $keyValuePairs[] = ['parent-flat' => $record->secondary_table, 'main-flat' => $record->main_table];
                } else {
                    $keyValuePairs[] = ['parent-eav' => $record->secondary_table, 'main-eav' => $record->main_table];
                }

            }
        }
        return $keyValuePairs;
    }

    /**
     * Prepare mapper schema sckeleton
     */
    public function prepareSchemaSkeleton($tree, $withFields = true, $data = [], $entityType = '')
    {
        $arrChild = [];
        $arrParent = [];
        $schema = [];
        foreach ($tree as $item) {
            $parentKey = 'parent-flat';
            $mainKey = 'main-flat';
            $isRecordFlat = true;
            if (isset($item['parent-eav'])) {
                $parentKey = 'parent-eav';
                // $mainKey = 'main-eav';
                $isRecordFlat = false;
            }
            // if (!isset($arrChild[$item[$mainKey]])) {
            //     // add child to array of all elements
            //     $arrChild[$item[$mainKey]] = [];
            // }
            if (!isset($arrChild[$item[$parentKey]])) {
                // add parent to array of all elements
                $arrChild[$item[$parentKey]] = [];
                if ($withFields) {
                    if ($isRecordFlat) {
                        $parentArray = $this->getTableRefferenceFields($item[$parentKey], true, true, $entityType);
                    } else {
                        $parentArray = $this->getAllEntityAttributes($item[$parentKey], '', true, $entityType);
                    }
                    $arrChild[$item[$parentKey]] = $parentArray;
                } else if (count($data) > 0) {
                    $arrChild[$item[$parentKey]] = isset($data[$item[$parentKey]]) ? $data[$item[$parentKey]] : [];
                }
                $arrParent[$item[$parentKey]] = &$arrChild[$item[$parentKey]];
            }
            // if (!isset($arrChild[$item[$parentKey]][$item[$mainKey]])) {
            //     // add reference to child for this parent
            //     if ($isRecordFlat) {
            //         if ($withFields) {
            //             $nameArray = $this->getTableRefferenceFields($item[$mainKey], true);
            //             $arrChild[$item[$mainKey]] = $nameArray;
            //         } else if (count($data) > 0) {
            //             $arrChild[$item[$mainKey]] = isset($data[$item[$mainKey]]) ? $data[$item[$mainKey]] : [];
            //         }
            //     } else {
            //         if ($withFields) {
            //             $nameArray = $this->getAllEntityAttributes($item[$mainKey]);
            //             $arrChild[$item[$mainKey]] = $nameArray;
            //         } else if (count($data) > 0) {
            //             $arrChild[$item[$mainKey]] = isset($data[$item[$mainKey]]) ? $data[$item[$mainKey]] : [];
            //         }
            //     }
            //     $arrChild[$item[$parentKey]][$item[$mainKey]] = &$arrChild[$item[$mainKey]];
            // }
        }
        return $arrParent;
    }

    /**
     * Get all Attributes for entity_code
     *
     * $entity_code (table name) and $entity_key (column name) come from the mapper table,
     * where they originate as admin-supplied free-text. Both are re-validated against the
     * SQL identifier allowlist; the IN-list of selected_fields is bound as %s values via
     * $wpdb->prepare() rather than being concatenated raw — the previous use of
     * $wpdb->esc_like() on identifiers was incorrect (esc_like only escapes LIKE wildcards
     * and does not protect against SQL injection in identifier or value positions).
     */
    public function getAllEntityAttributes($entity_code, $entity_key = "", $withAllFields = true, $entityType = '')
    {
        global $wpdb;
        $selected_fields = "";
        if ($entity_key == "") {
            if ($entityType && !empty($entityType)) {
                $results = $wpdb->get_results($wpdb->prepare("SELECT eav_key_field, selected_fields FROM avatax_einvoice_mapper WHERE main_table = %s AND entity_type = %s", $entity_code, $entityType));
            } else {
                $results = $wpdb->get_results($wpdb->prepare("SELECT eav_key_field, selected_fields FROM avatax_einvoice_mapper WHERE main_table = %s", $entity_code));
            }
            if (empty($results)) {
                return array();
            }
            $entity_key = $results[0]->eav_key_field;
            $selected_fields = $results[0]->selected_fields;
        }

        $entity_key_safe = $this->escape_sql_identifier($entity_key);
        $entity_code_safe = $this->escape_sql_identifier($entity_code);
        if ('' === $entity_key_safe || '' === $entity_code_safe) {
            return array();
        }

        if ($withAllFields) {
            if ($entity_code != $wpdb->prefix . "options") {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- identifiers validated via escape_sql_identifier()
                $results = $wpdb->get_results("SELECT DISTINCT {$entity_key_safe} FROM {$entity_code_safe}");
            } else {
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- identifiers validated via escape_sql_identifier()
                        "SELECT DISTINCT {$entity_key_safe} FROM {$entity_code_safe} WHERE {$entity_key_safe} NOT LIKE %s",
                        '%transient%'
                    )
                );
            }
        } else {
            $values = array_values(array_filter(array_map('trim', explode(',', (string) $selected_fields)), 'strlen'));
            if (empty($values)) {
                return array();
            }
            // The IN-list placeholder string is built dynamically (one '%s' per
            // value) so that every selected_fields entry is bound through prepare()
            // rather than concatenated. phpcs's UnfinishedPrepare sniff cannot see
            // the placeholders inside the interpolated $placeholders var, hence the
            // extended ignore below.
            $placeholders = implode(', ', array_fill(0, count($values), '%s'));
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- identifiers validated via escape_sql_identifier(); values bound via the dynamically built %s placeholders.
                    "SELECT DISTINCT {$entity_key_safe} FROM {$entity_code_safe} WHERE {$entity_key_safe} IN ({$placeholders})",
                    $values
                )
            );
        }
        $attributeCodes = [];
        if (is_array($results)) {
            foreach ($results as $result) {
                $attributeCodes[$result->$entity_key] = "string";
            }
        }

        return $attributeCodes;

    }


    /**
     * Replace Array Values with Null
     */
    public function replaceArrayValueswithNull($array)
    {
        return array_map(function ($value) {
            return null;
        }, $array); // array_map should walk through $array
    }

    /**
     * Restructure Keys to arrange records as per Original Schema
     */
    public function restructureKeys($invoiceRecords = [])
    {
        $restructuredInvoices = [];
        if (count($invoiceRecords) > 0) {
            $i = 0;
            foreach ($invoiceRecords as $invoiceRecord) {
                foreach ($invoiceRecord as $key => $value) {
                    $arrayKey = explode('|', $key);
                    $restructuredInvoices[$i][$arrayKey[0]][$arrayKey[1]] = $value;
                }
                $i++;
            }
        }
        return $restructuredInvoices;
    }

    /**
     * Remove Duplicate Entries from Collection
     */
    public function removeDuplicateEntries($restructuredInvoiceRecords = [])
    {
        $uniqueInvoiceRecords = [];
        $swapTableKeys = [];
        if (count($restructuredInvoiceRecords) > 0) {
            foreach ($restructuredInvoiceRecords as $restructuredInvoiceRecord) {
                foreach ($restructuredInvoiceRecord as $key => $record) {
                    $swapTableKeys[$key][] = $record;
                }
            }
            foreach ($swapTableKeys as $key => $value) {
                $uniqueValue = array_unique($value, SORT_REGULAR);
                if (sizeof($uniqueValue) > 1) {
                    $filteredKeyArray = array_values(array_filter($uniqueValue));
                    $uniqueInvoiceRecords[$key] = $this->sanitizeArray($filteredKeyArray);
                } else {
                    $uniqueInvoiceRecords[$key] = $this->sanitizeArray($uniqueValue[0]);
                }
            }
        }
        return $uniqueInvoiceRecords;
    }

    public function sanitizeArray($array)
    {
        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            $array[$key][$k] = $v;
                        } else {
                            $array[$key][$k] = $this->sanitizeData($v);
                        }
                    }
                } else {
                    $array[$key] = $this->sanitizeData($value);
                }
            }
        }
        return $array;
    }

    public function sanitizeData($data)
    {
        if (!empty($data)) {
            if ($this->isJson($data)) {
                $data = (array) json_decode($data);
            }
        }
        return $data;
    }

    public function sanitizeString($string = '')
    {
        if (!empty($string)) {
            // Using str_ireplace() function
            // to replace the word
            $string = str_ireplace(array(
                '\'',
                '"',
                ',',
                ';',
                '<',
                '>'
            ), ' ', $string);
            // Change thoses 2 variables if needed
            $allowableTags = null;
            $allowHtmlEntities = false;
            // Params are optionnal. If you use default values you can remove it.
            $params = ['allowableTags' => $allowableTags, 'escape' => $allowHtmlEntities];
            return $this->filterManager->stripTags($string, $params);
        }
        return $string;
    }

    public function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    /**
     * Prepare EAV Attribute Mapping Record
     */
    public function prepareEavAttributesRecords($uniqueFlatTableInvoiceRecords, $invoiceId, $entity_type)
    {
        $eavAttributesRecords = [];
        $eavTableRecords = $this->getEinvoiceMapperRecords($entity_type, self::TABLE_TYPE_EAV);
        if ($eavTableRecords && count($eavTableRecords) > 0) {
            foreach ($eavTableRecords as $eavTableRecord) {
                $eavAttributesRecords[$eavTableRecord->main_table] = $this->getModuleEAVAttributesRecords($eavTableRecord, $uniqueFlatTableInvoiceRecords, $invoiceId, $entity_type);
            }
        }
        return $eavAttributesRecords;
    }

    /**
     * GET EAV Attribute Record for specific Module
     *
     * Builds a JOIN query whose every table and column comes from the mapper. Each
     * identifier is allow-listed via escape_sql_identifier() before being concatenated;
     * the WHERE value (invoiceId) is bound via $wpdb->prepare() with %s. If any
     * identifier fails validation the entire query is abandoned so a tampered mapper
     * row cannot reach the database.
     */
    public function getModuleEAVAttributesRecords($eavTableRecord, $uniqueFlatTableInvoiceRecords, $invoiceId, $entity_type)
    {
        $moduleEavAttributesRecords = [];
        // Convert comma-separated fields string into array
        $arrSelectedFields = explode(',', $eavTableRecord->selected_fields);
        // Fetch EAV attributed for given entity
        global $wpdb;

        $main_table_safe = $this->escape_sql_identifier($eavTableRecord->main_table);
        if ('' === $main_table_safe) {
            return $moduleEavAttributesRecords;
        }

        $this->query = 'SELECT * FROM ' . $main_table_safe;
        $key_field = $eavTableRecord->eav_key_field;
        $value_field = $eavTableRecord->eav_value_field;
        $results = $this->getEinvoiceMapperRecords($entity_type);

        $built = $this->prepareEAVQuery(
            $results,
            $eavTableRecord->secondary_table,
            $eavTableRecord->main_table,
            $eavTableRecord->secondary_table_ref_field,
            $eavTableRecord->main_table_ref_field
        );

        if (!$built || false === strpos($this->query, '%s')) {
            // Either an identifier failed validation or no terminal WHERE clause was added.
            // Refuse to execute a partially-built query.
            return $moduleEavAttributesRecords;
        }

        // prepareEAVQuery() leaves a "%s" placeholder for the WHERE value; bind it via prepare.
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $this->query is built from allow-listed identifiers and bound via prepare()
        $prepared = $wpdb->prepare($this->query, $invoiceId);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared above
        $results = $wpdb->get_results($prepared);

        if (is_array($results)) {
            foreach ($results as $result) {
                if (in_array($result->$key_field, $arrSelectedFields)) // check if this field is selected in schema or not
                {
                    $moduleEavAttributesRecords[$result->$key_field] = (!array_key_exists($result->$key_field, $moduleEavAttributesRecords)) ? $this->sanitizeData($result->$value_field) : ($moduleEavAttributesRecords[$result->$key_field] . "," . $this->sanitizeData($result->$value_field));
                }
            }
        }

        return $moduleEavAttributesRecords;
    }


    /**
     * Recursively appends JOIN/WHERE fragments to $this->query.
     *
     * Every identifier is validated against the SQL identifier allowlist; if any value
     * fails, the recursion bails out and the caller is told to abandon the query.
     * The WHERE clause ends with a "%s" placeholder so the caller can bind the invoice
     * id through $wpdb->prepare() rather than concatenating raw input.
     *
     * @return bool true when at least one valid join+where pair was appended, false otherwise.
     */
    public function prepareEAVQuery($results, $secondary_table, $main_table, $secondary_table_ref_field, $main_table_ref_field)
    {

        $main_safe = $this->escape_sql_identifier($main_table);
        $main_ref_safe = $this->escape_sql_identifier($main_table_ref_field);
        $secondary_ref_safe = $this->escape_sql_identifier($secondary_table_ref_field);
        if ('' === $main_safe || '' === $main_ref_safe || '' === $secondary_ref_safe) {
            return false;
        }

        $appended = false;
        foreach ((array) $results as $result) {
            if ($result->main_table != $secondary_table || !$result->main_table) {
                continue;
            }

            $join_table_safe = $this->escape_sql_identifier($result->main_table);
            if ('' === $join_table_safe) {
                return false;
            }

            $this->query .= ' JOIN ' . $join_table_safe . ' ON ' . $main_safe . '.' . $main_ref_safe . '=' . $join_table_safe . '.' . $secondary_ref_safe;
            $appended = true;

            if (!$result->secondary_table) {
                $where_field_safe = $this->escape_sql_identifier($result->main_table_ref_field);
                if ('' === $where_field_safe) {
                    return false;
                }
                $this->query .= ' WHERE ' . $join_table_safe . '.' . $where_field_safe . ' = %s';
                return $appended;
            }

            $sub_appended = $this->prepareEAVQuery(
                $results,
                $result->secondary_table,
                $result->main_table,
                $result->secondary_table_ref_field,
                $result->main_table_ref_field
            );
            if (!$sub_appended) {
                return false;
            }
           return true;
        }

        return $appended;
    }

    public function InsertFilterData($filterInfo)
    {
        global $wpdb;
        try {
            $existCheckResults = $wpdb->query($wpdb->prepare("SELECT 1 FROM avatax_conditional_payload_mapper WHERE conditional_param = %s", $filterInfo['cond_param']));
            if ($existCheckResults == 0) {
                $response = $wpdb->get_results($wpdb->prepare("INSERT INTO avatax_conditional_payload_mapper (mapper_table, mapper_field, conditional_param) VALUES (%s, %s, %s)", $filterInfo['mapped_table'], $filterInfo['mapper_table_field'], $filterInfo['cond_param']));
                $conditional_response = $wpdb->get_results($wpdb->prepare("SELECT * FROM avatax_conditional_payload_mapper WHERE conditional_param = %s", $filterInfo['cond_param']));
                if (!empty($filterInfo['filter_obj'])) {
                    foreach ($filterInfo['filter_obj'] as $key => $value) {
                        $response = $wpdb->get_results($wpdb->prepare("INSERT INTO avatax_conditional_payload_filter (conditional_mapper_id, filter_field, filter_data) VALUES (%s, %s, %s)", $conditional_response[0]->conditional_mapper_id, (($key) ? $key : null), $value));
                    }
                }
                return 'Record Created SuccessFully';
            } else {
                return 'Conditional Param Already Mapped. Delete to continue.';
            }
        } catch (Exception $e) {
            if (wc_avatax()->elr_logging_enabled()) {
                wc_avatax()->log_elr("Exception " . $e->getMessage());
            }
            return false;
        }
    }

    public function saveElrSchema($columns = [])
    {
        update_option("wc_avatax_elr_schema", $columns);
        return $columns;
    }

    public function getElrSchema()
    {
        return get_option("wc_avatax_elr_schema", []);
    }

    /**
     * Get get Main Table List
     */
    public function getMainTableList($entityType = '')
    {
        $records = $this->getEinvoiceMapperRecords($entityType);

        return $records;
    }

    /**
     * Delete Mapper Record
     */
    public function deleteMapperRecord($mapperId)
    {
        global $wpdb;
        return $wpdb->query($wpdb->prepare("DELETE FROM avatax_einvoice_mapper WHERE mapper_id = %s", $mapperId));
    }

    /**
     * Delete conditional Record
     */
    public function deleteConditionalRecord($conditionalId, $filterId)
    {
        global $wpdb;
        wc_avatax()->log_elr("Delete from avatax_conditional_payload_filter where filter_id='" . $filterId . "'");
        $wpdb->query($wpdb->prepare("DELETE FROM avatax_conditional_payload_filter WHERE filter_id = %s", $filterId));
        return $wpdb->query($wpdb->prepare("DELETE FROM avatax_conditional_payload_mapper WHERE conditional_mapper_id = %s", $conditionalId));
    }

    /**
     * create table and default data for "avatax_conditional_payload_filter"
     *
     * @internal
     *
     * @since 3.0.0
     *
     * @return void
     */
    public function elrDataCreationForConditionalPayloadFilter()
    {
        global $wpdb;
        // Create table "avatax_conditional_payload_filter" for payload filter mapping
        $table_name_conditional_payload_filter = 'avatax_conditional_payload_filter';

        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_conditional_payload_filter)) === $table_name_conditional_payload_filter;

        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name_conditional_payload_filter (
					filter_id mediumint(9) NOT NULL AUTO_INCREMENT,
					conditional_mapper_id mediumint(9) NOT NULL,
					filter_field varchar(255)  NOT NULL,
                    filter_data text  NOT NULL,
					created_at timestamp DEFAULT current_timestamp() NOT NULL,
					PRIMARY KEY  (filter_id)
			) $charset_collate;";

            if (defined('WC_ABSPATH') && defined('ABSPATH') && !empty(ABSPATH) && is_string(ABSPATH) && file_exists(ABSPATH . '/wp-admin/includes/upgrade.php')) {
                require_once ABSPATH . '/wp-admin/includes/upgrade.php';
            }
            dbDelta($sql);

            $conditional_mappers = $wpdb->get_results("SELECT conditional_mapper_id FROM avatax_conditional_payload_mapper");

            if ($conditional_mappers) {
                // Prepare the insert data
                $values = array(
                    array(
                        'conditional_mapper_id' => $conditional_mappers[0]->conditional_mapper_id,
                        'filter_field' => 'address_type',
                        'filter_data' => 'shipping'
                    )
                );

                // Use WordPress's prepare statement for safer SQL
                $placeholders = array();
                $data = array();

                foreach ($values as $row) {
                    $placeholders[] = "(%s, %s, %s)";
                    $data[] = $row['conditional_mapper_id'];
                    $data[] = $row['filter_field'];
                    $data[] = $row['filter_data'];
                }

                // Build the query safely
                $table_name_escaped = "`" . esc_sql($table_name_conditional_payload_filter) . "`";
                $placeholders_string = implode(', ', $placeholders);
                $query_template = "INSERT INTO {$table_name_escaped} (conditional_mapper_id, filter_field, filter_data) VALUES {$placeholders_string}";

                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query template is built safely with escaped values
                $wpdb->query($wpdb->prepare($query_template, $data));
            }
        }
    }

    /**
     * create table and default data for "avatax_einvoice_mapper"
     *
     * @internal
     * @codeCoverageIgnore
     * @since 3.0.0
     *
     * @return void
     */
    public function elrDataCreationForEinvoiceMapper()
    {
        global $wpdb;
        $table_name = 'avatax_einvoice_mapper';

        // Check if the table exists
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
					mapper_id mediumint(9) NOT NULL AUTO_INCREMENT,
					main_table varchar(255)  NULL,
					main_table_ref_field varchar(255)  NULL,
					secondary_table varchar(255)  NULL,
					secondary_table_ref_field varchar(255)  NULL,
					selected_fields text NULL,
					eav_key_field varchar(255) NULL,
					eav_value_field varchar(255) NULL,
					table_type varchar(255)  NULL,
                    entity_type varchar(255)  NULL,
                    is_default_table int(11) DEFAULT 0,
                    isarray bit(1) NOT NULL,
					created_at timestamp DEFAULT current_timestamp() NOT NULL,
					PRIMARY KEY  (mapper_id)
			) $charset_collate;";


            if (defined('WC_ABSPATH') && defined('ABSPATH') && !empty(ABSPATH) && is_string(ABSPATH) && file_exists(ABSPATH . '/wp-admin/includes/upgrade.php')) {
                require_once ABSPATH . '/wp-admin/includes/upgrade.php';
            }
            dbDelta($sql);

            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safely escaped
            $wpdb->query("INSERT INTO `" . esc_sql($table_name) . "` (main_table,main_table_ref_field,  secondary_table, secondary_table_ref_field, selected_fields, eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) VALUES 
            		('" . $wpdb->prefix . "wc_orders',	'id', NULL, NULL, 'id,status,currency,type,tax_amount,total_amount,customer_id,billing_email,date_created_gmt,date_updated_gmt,parent_order_id,payment_method,payment_method_title,transaction_id,ip_address,user_agent,customer_note', '',	'',	'flat', 'order', 1, 0),
                    ('" . $wpdb->prefix . "wc_orders_meta',	'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'last4,_card_brand,_wc_avatax_tax_rate_21,_wc_avatax_taxable_amount_rate_21,_wc_avatax_elr_response_keys', 'meta_key',	'meta_value',	'eav', 'order', 1, 0),
					('" . $wpdb->prefix . "wc_order_stats',	'order_id',	'" . $wpdb->prefix . "wc_orders',	'id', 'order_id,parent_id,date_created,date_created_gmt,date_paid,date_completed,num_items_sold,total_sales,tax_total,shipping_total,net_total,returning_customer,status,customer_id', NULL, NULL, 'flat', 'order', 1, 0),
					('" . $wpdb->prefix . "wc_order_addresses', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'id,order_id,address_type,first_name,last_name,company,address_1,address_2,city,state,postcode,country,email,phone', NULL, NULL, 'flat', 'order', 1, 1),
                    ('" . $wpdb->prefix . "users', 'ID', '" . $wpdb->prefix . "wc_orders', 'customer_id', 'display_name,ID,user_activation_key,user_email,user_login,user_nicename,user_pass,user_registered,user_status,user_url',  NULL, NULL, 'flat', 'order', 1, 0),
                    ('" . $wpdb->prefix . "options', '', '', '', 'woocommerce_default_country,woocommerce_store_address,woocommerce_store_address_2,woocommerce_store_city,woocommerce_store_postcode,woocommerce_tax_based_on,wc_avatax_origin_country', 'option_name', 'option_value', 'vertical', 'order', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_tax_lookup', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_tax,shipping_tax,tax_rate_id,total_tax', NULL, NULL, 'flat', 'order', 0, 0),
					('" . $wpdb->prefix . "woocommerce_order_items', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL,	'flat', 'order',	1, 1),
					('" . $wpdb->prefix . "woocommerce_order_itemmeta', 'order_item_id', '" . $wpdb->prefix . "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'order', 1, 0),
                    ('". $wpdb->prefix. "usermeta',	'user_id', '". $wpdb->prefix. "users', 'ID', 'wc_avatax_Buyer_Is_Business', 'meta_key',	'meta_value',	'eav', 'order', 1, 0),
                    ('" . $wpdb->prefix . "wc_orders',	'id', NULL, NULL, 'id,status,currency,type,tax_amount,total_amount,customer_id,billing_email,date_created_gmt,date_updated_gmt,parent_order_id,payment_method,payment_method_title,transaction_id,ip_address,user_agent,customer_note', '',	'',	'flat', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_stats',	'order_id',	'" . $wpdb->prefix . "wc_orders',	'parent_order_id', 'order_id,parent_id,date_created,date_created_gmt,date_paid,date_completed,num_items_sold,total_sales,tax_total,shipping_total,net_total,returning_customer,status,customer_id', NULL, NULL, 'flat', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "wc_customer_lookup',	'customer_id',	'" . $wpdb->prefix . "wc_order_stats',	'customer_id', 'customer_id,user_id', NULL, NULL, 'flat', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_addresses', 'order_id', '" . $wpdb->prefix . "wc_orders', 'parent_order_id', 'id,order_id,address_type,first_name,last_name,company,address_1,address_2,city,state,postcode,country,email,phone', NULL, NULL, 'flat', 'refund', 1, 1),
                    ('" . $wpdb->prefix . "users', 'ID', '" . $wpdb->prefix . "wc_customer_lookup', 'user_id', 'display_name,ID,user_activation_key,user_email,user_login,user_nicename,user_pass,user_registered,user_status,user_url',  NULL, NULL, 'flat', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "options', '', '', '', 'woocommerce_default_country,woocommerce_store_address,woocommerce_store_address_2,woocommerce_store_city,woocommerce_store_postcode,woocommerce_tax_based_on,wc_avatax_origin_country', 'option_name', 'option_value', 'vertical', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_tax_lookup', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_tax,shipping_tax,tax_rate_id,total_tax', NULL, NULL, 'flat', 'refund', 0, 0),
                    ('" . $wpdb->prefix . "woocommerce_order_items', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL,	'flat', 'refund', 1, 1),
                    ('" . $wpdb->prefix . "woocommerce_order_itemmeta', 'order_item_id', '" . $wpdb->prefix . "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "usermeta',	'user_id', '" . $wpdb->prefix . "users', 'ID', 'wc_avatax_Buyer_Is_Business', 'meta_key',	'meta_value',	'eav', 'refund', 1, 0),
                    ('" . $wpdb->prefix . "wc_orders_meta',	'order_id', '" . $wpdb->prefix . "wc_orders', 'parent_order_id', 'last4,_card_brand,_wc_avatax_tax_rate_21,_wc_avatax_taxable_amount_rate_21,_wc_avatax_elr_response_keys', 'meta_key',	'meta_value',	'eav', 'refund', 1, 0),

                    ('" . $wpdb->prefix . "wc_orders',	'id', NULL, NULL, 'id,status,currency,type,tax_amount,total_amount,customer_id,billing_email,date_created_gmt,date_updated_gmt,parent_order_id,payment_method,payment_method_title,transaction_id,ip_address,user_agent,customer_note', '',	'',	'flat', 'b2bpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_addresses', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'id,order_id,address_type,first_name,last_name,company,address_1,address_2,city,state,postcode,country,email,phone', NULL, NULL, 'flat', 'b2bpayment-ereporting', 1, 1),
                    ('" . $wpdb->prefix . "options', '', '', '', 'woocommerce_default_country,woocommerce_store_address,woocommerce_store_address_2,woocommerce_store_city,woocommerce_store_postcode,woocommerce_tax_based_on,wc_avatax_origin_country', 'option_name', 'option_value', 'vertical', 'b2bpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "users', 'ID', '" . $wpdb->prefix . "wc_orders', 'customer_id', 'display_name,ID,user_activation_key,user_email,user_login,user_nicename,user_pass,user_registered,user_status,user_url',  NULL, NULL, 'flat', 'b2bpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "usermeta',	'user_id', '" . $wpdb->prefix . "users', 'ID', 'wc_avatax_Buyer_Is_Business', 'meta_key',	'meta_value',	'eav', 'b2bpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "woocommerce_order_items', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL,	'flat', 'b2bpayment-ereporting',	1, 1),
					('" . $wpdb->prefix . "woocommerce_order_itemmeta', 'order_item_id', '" . $wpdb->prefix . "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'b2bpayment-ereporting', 1, 0),

                    ('" . $wpdb->prefix . "wc_orders',	'id', NULL, NULL, 'id,status,currency,type,tax_amount,total_amount,customer_id,billing_email,date_created_gmt,date_updated_gmt,parent_order_id,payment_method,payment_method_title,transaction_id,ip_address,user_agent,customer_note', '',	'',	'flat', 'b2cpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_addresses', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'id,order_id,address_type,first_name,last_name,company,address_1,address_2,city,state,postcode,country,email,phone', NULL, NULL, 'flat', 'b2cpayment-ereporting', 1, 1),
                    ('" . $wpdb->prefix . "options', '', '', '', 'woocommerce_default_country,woocommerce_store_address,woocommerce_store_address_2,woocommerce_store_city,woocommerce_store_postcode,woocommerce_tax_based_on,wc_avatax_origin_country', 'option_name', 'option_value', 'vertical', 'b2cpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "users', 'ID', '" . $wpdb->prefix . "wc_orders', 'customer_id', 'display_name,ID,user_activation_key,user_email,user_login,user_nicename,user_pass,user_registered,user_status,user_url',  NULL, NULL, 'flat', 'b2cpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "usermeta',	'user_id', '" . $wpdb->prefix . "users', 'ID', 'wc_avatax_Buyer_Is_Business', 'meta_key',	'meta_value',	'eav', 'b2cpayment-ereporting', 1, 0),
                    ('" . $wpdb->prefix . "woocommerce_order_items', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL,	'flat', 'b2cpayment-ereporting',	1, 1),
					('" . $wpdb->prefix . "woocommerce_order_itemmeta', 'order_item_id', '" . $wpdb->prefix . "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'b2cpayment-ereporting', 1, 0),

                    ('" . $wpdb->prefix . "wc_orders', 'id', NULL, NULL, 'id,total_amount,tax_amount,date_created_gmt', '', '', 'flat', 'application_response_outbound', 1, 0),
                    ('" . $wpdb->prefix . "wc_order_addresses', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,address_type,country', NULL, NULL, 'flat', 'application_response_outbound', 1, 1),
                    ('" . $wpdb->prefix . "woocommerce_order_items', 'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'order_id,order_item_id,order_item_name,order_item_type', NULL, NULL, 'flat', 'application_response_outbound', 1, 1),
                    ('" . $wpdb->prefix . "woocommerce_order_itemmeta', 'order_item_id', '" . $wpdb->prefix . "woocommerce_order_items', 'order_item_id', 'meta_id,meta_key,meta_value,order_item_id', NULL, NULL, 'flat', 'application_response_outbound', 1, 0),
                    ('" . $wpdb->prefix . "usermeta', 'user_id', '" . $wpdb->prefix . "wc_orders', 'customer_id', 'wc_avatax_Buyer_Is_Business', 'meta_key', 'meta_value', 'eav', 'application_response_outbound', 1, 0)");

        } else {
            $this->insert_if_not_exists( $table_name );
            $this->ensure_application_response_outbound_mapper_defaults();
        }
    }

    /**
     * Check if a table exists in the mapper and insert if not
     *
     * @param string $table_name The name of the mapper table
     * @return void
     */
    public function insert_if_not_exists($table_name)
    {
        global $wpdb;

        $main_table_value = $wpdb->prefix . "wc_orders_meta";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is validated
        $check_query = $wpdb->prepare("SELECT COUNT(*) FROM `" . esc_sql($table_name) . "` WHERE main_table = %s", $main_table_value);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is validated
        $exists = $wpdb->get_var($check_query);

        if ($exists == 0) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safely escaped
            $wpdb->query("INSERT INTO `" . esc_sql($table_name) . "` (main_table,main_table_ref_field,  secondary_table, secondary_table_ref_field, selected_fields, eav_key_field, eav_value_field, table_type, entity_type, is_default_table, isarray) VALUES 
                ('" . $wpdb->prefix . "wc_orders_meta',	'order_id', '" . $wpdb->prefix . "wc_orders', 'id', 'last4,_card_brand,_wc_avatax_tax_rate_21,_wc_avatax_taxable_amount_rate_21', 'meta_key',	'meta_value',	'eav', 'order', 1, 0),
                ('" . $wpdb->prefix . "wc_orders_meta',	'order_id', '" . $wpdb->prefix . "wc_orders', 'parent_order_id', 'last4,_card_brand,_wc_avatax_tax_rate_21,_wc_avatax_taxable_amount_rate_21', 'meta_key',	'meta_value',	'eav', 'refund', 1, 0)");
        } else {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safely escaped
            $wpdb->query("UPDATE `" . esc_sql($table_name) . "` SET selected_fields = 'last4,_card_brand,_wc_avatax_tax_rate_21,_wc_avatax_taxable_amount_rate_21' WHERE main_table = '" . $wpdb->prefix . "wc_orders_meta' AND entity_type = 'order'");

            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safely escaped
            $wpdb->query("UPDATE `" . esc_sql($table_name) . "` SET selected_fields = 'last4,_card_brand,_wc_avatax_tax_rate_21,_wc_avatax_taxable_amount_rate_21' WHERE main_table = '" . $wpdb->prefix . "wc_orders_meta' AND entity_type = 'refund'");
        }
    }

    /**
     * create table and default data for "avatax_conditional_payload_mapper"
     *
     * @internal
     * @codeCoverageIgnore
     * @since 3.0.0
     *
     * @return void
     */
    public function elrDataCreationForConditionalPayloadMapper()
    {
        global $wpdb;
        // Code to create and insert data for table "avatax_conditional_payload_mapper"
        $table_name = 'avatax_conditional_payload_mapper';

        // Check if the table exists
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
					conditional_mapper_id mediumint(9) NOT NULL AUTO_INCREMENT,
					mapper_table varchar(255) NOT NULL,
					mapper_field varchar(255) NOT NULL,
					conditional_param varchar(255) NOT NULL,
					created_at timestamp DEFAULT current_timestamp() NOT NULL,
					PRIMARY KEY  (conditional_mapper_id)
			) $charset_collate;";

            if (defined('WC_ABSPATH') && defined('ABSPATH') && !empty(ABSPATH) && is_string(ABSPATH) && file_exists(ABSPATH . '/wp-admin/includes/upgrade.php')) {
                require_once ABSPATH . '/wp-admin/includes/upgrade.php';
            }
            dbDelta($sql);

            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name is safely escaped
            $wpdb->query("INSERT INTO `" . esc_sql($table_name) . "` (mapper_table,  mapper_field, conditional_param) VALUES 
						('{$wpdb->prefix}wc_order_addresses', 'country', 'destinationCountry'),
                        ('{$wpdb->prefix}options', 'woocommerce_default_country', 'originCountry'),
                        ('{$wpdb->prefix}options', 'wc_avatax_origin_country', 'custom2'),
                        ('{$wpdb->prefix}usermeta', 'wc_avatax_Buyer_Is_Business', 'custom1')");
        }
    }

    /**
     * Builds a single ELR custom field definition (for use in add_default_custom_fields).
     *
     * @since 3.8.0
     * @param string $field_id   Option/meta key for the field.
     * @param string $field_type 'company' or 'customer'.
     * @param string $field_name Display name for the field.
     * @return array Field definition with data_type, is_default, selected.
     */
    private function build_elr_field_definition($field_id, $field_type, $field_name)
    {
        return [
            'field_id' => $field_id,
            'field_type' => $field_type,
            'data_type' => 'string',
            'field_name' => $field_name,
            'is_default' => true,
            'selected' => true,
        ];
    }

    /**
     * Adds default custom fields in the database
     *
     * @since 3.0.0
     *
     * @return void
     */
    public function add_default_custom_fields()
    {

        $existing_fields = get_option('wc_avatax_elr_custom_fields', []);
        $existing_fields = json_decode(json_encode($existing_fields), true);

        $new_company_fields = [
            [
                "field_id" => "wc_avatax_Seller_Telephone_Number",
                "field_type" => "company",
                "data_type" => "string",
                "field_name" => "Seller Telephone Number",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Seller_ElectronicMail",
                "field_type" => "company",
                "data_type" => "string",
                "field_name" => "Seller ElectronicMail",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Seller_DBNA_ID",
                "field_type" => "company",
                "data_type" => "string",
                "field_name" => "Seller DBNA ID",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Seller_ZUGFERD_ID",
                "field_type" => "company",
                "data_type" => "string",
                "field_name" => "Seller ZUGFERD ID",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Seller_SIREN_ID",
                "field_type" => "company",
                "data_type" => "string",
                "field_name" => "Seller SIREN ID",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Seller_Spain_NIFId",
                "field_type" => "company",
                "data_type" => "string",
                "field_name" => "Seller Spain NIF ID",
                "is_default" => true,
                "selected" => true
            ],
            $this->build_elr_field_definition('wc_avatax_Seller_Poland_KSeF_ID', 'company', 'Seller Poland KSeF ID'),
            $this->build_elr_field_definition('wc_avatax_Seller_SIRET_ID', 'company', 'Seller SIRET ID'),
            $this->build_elr_field_definition('wc_avatax_Seller_Italy_VAT_Id', 'company', 'Seller Italy VAT ID (Partita IVA)'),
            $this->build_elr_field_definition('wc_avatax_Seller_Tax_Identification_Number', 'company', 'Seller Tax Identification Number (TIN)'),
            $this->build_elr_field_definition('wc_avatax_Seller_Passport_Number', 'company', 'Seller Passport Number')
        ];

        $new_customer_fields = [
            [
                "field_id" => "wc_avatax_Buyer_Is_Business",
                "field_type" => "customer",
                "data_type" => "boolean",
                "field_name" => "Buyer Is Business",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Buyer_ZUGFERD_ID",
                "field_type" => "customer",
                "data_type" => "string",
                "field_name" => "Buyer ZUGFERD ID",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Buyer_DBNA_ID",
                "field_type" => "customer",
                "data_type" => "string",
                "field_name" => "Buyer DBNA ID",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Buyer_SIREN_ID",
                "field_type" => "customer",
                "data_type" => "string",
                "field_name" => "Buyer SIREN ID",
                "is_default" => true,
                "selected" => true
            ],
            [
                "field_id" => "wc_avatax_Buyer_Spain_NIFId",
                "field_type" => "customer",
                "data_type" => "string",
                "field_name" => "Buyer Spain NIF ID",
                "is_default" => true,
                "selected" => true
            ],
            $this->build_elr_field_definition('wc_avatax_Buyer_Poland_KSeF_ID', 'customer', 'Buyer Poland KSeF ID'),
            $this->build_elr_field_definition('wc_avatax_Buyer_SIRET_ID', 'customer', 'Buyer SIRET ID'),
            $this->build_elr_field_definition('wc_avatax_Buyer_Italy_VAT_Id', 'customer', 'Buyer Italy VAT ID (Partita IVA)'),
            $this->build_elr_field_definition('wc_avatax_Buyer_Registration_Name', 'customer', 'Buyer registration name'),
            $this->build_elr_field_definition('wc_avatax_Buyer_Addressing_Line', 'customer', 'Buyer Addressing Line'),
            $this->build_elr_field_definition('wc_avatax_Buyer_Tax_Identification_Number', 'customer', 'Buyer Tax Identification Number (TIN)'),
            $this->build_elr_field_definition('wc_avatax_Buyer_Passport_Number', 'customer', 'Buyer Passport Number')

        ];

        if (empty($existing_fields)) {
            $company_fields = [
                ["field_id" => "wc_avatax_Seller_company_ID", "field_type" => "company", "data_type" => "string", "field_name" => "Seller company ID", "is_default" => true, "selected" => true],
                ["field_id" => "wc_avatax_Seller_VAT_ID", "field_type" => "company", "data_type" => "string", "field_name" => "Seller VAT ID", "is_default" => true, "selected" => true],
                ["field_id" => "wc_avatax_Seller_Peppol_ID", "field_type" => "company", "data_type" => "string", "field_name" => "Seller PEPPOL ID", "is_default" => true, "selected" => true],
                ["field_id" => "wc_avatax_Seller_Registration_Name", "field_type" => "company", "data_type" => "string", "field_name" => "Seller registration name", "is_default" => true, "selected" => true],
                ...$new_company_fields
            ];
            $customer_fields = [
                ["field_id" => "wc_avatax_Buyer_company_ID", "field_type" => "customer", "data_type" => "string", "field_name" => "Buyer company ID", "is_default" => true, "selected" => true],
                ["field_id" => "wc_avatax_Buyer_VAT_ID", "field_type" => "customer", "data_type" => "string", "field_name" => "Buyer VAT ID", "is_default" => true, "selected" => true],
                ["field_id" => "wc_avatax_Buyer_Peppol_ID", "field_type" => "customer", "data_type" => "string", "field_name" => "Buyer Peppol ID", "is_default" => true, "selected" => true],
                ...$new_customer_fields
            ];

            $fields = ["company" => $company_fields, "customer" => $customer_fields];
            update_option("wc_avatax_elr_custom_fields", json_decode(json_encode($fields)));
        } else {
            $this->update_existing_custom_fields($existing_fields, $new_company_fields, $new_customer_fields);
        }

        // Generate selected custom fields
        $final_fields = get_option('wc_avatax_elr_custom_fields', []);
        $final_fields = json_decode(json_encode($final_fields), true);

        $formatted_fields = [];

        if (!empty($final_fields['customer'])) {
            foreach ($final_fields['customer'] as $field) {
                $formatted_fields[] = "JSON.customerEInvoicingData." . $field['field_id'];
            }
        }

        if (!empty($final_fields['company'])) {
            foreach ($final_fields['company'] as $field) {
                $formatted_fields[] = "JSON.CompanyEInvoicingData." . $field['field_id'];
            }
        }

        $formatted_result = implode(',', $formatted_fields);
        update_option('wc_avatax_elr_selected_custom_fields', $formatted_result);
    }
    public function update_existing_custom_fields($existing_fields, $new_company_fields, $new_customer_fields = [])
    {

        $existing_company_fields = isset($existing_fields['company']) ? $existing_fields['company'] : [];
        $existing_company_fields = json_decode(json_encode($existing_company_fields), true);

        $existing_field_ids = array_column($existing_company_fields, 'field_id');

        foreach ($new_company_fields as $new_field) {
            if (!in_array($new_field['field_id'], $existing_field_ids, true)) {
                $existing_company_fields[] = $new_field;
            }
        }

        $existing_fields['company'] = $existing_company_fields;

        if (!empty($new_customer_fields)) {
            $existing_customer_fields = isset($existing_fields['customer']) ? $existing_fields['customer'] : [];
            $existing_customer_fields = json_decode(json_encode($existing_customer_fields), true);

            $existing_customer_field_ids = array_column($existing_customer_fields, 'field_id');

            foreach ($new_customer_fields as $new_field) {
                if (!in_array($new_field['field_id'], $existing_customer_field_ids, true)) {
                    $existing_customer_fields[] = $new_field;
                }
            }

            $existing_fields['customer'] = $existing_customer_fields;
        }

        update_option('wc_avatax_elr_custom_fields', json_decode(json_encode($existing_fields)));
    }
    /**
     * Create elr table with default data and custom fields in the database
     *
     * @since 3.0.0
     *
     * @return void
     */
    public function set_elr_default_schema()
    {
        //avatax_einvoice_mapper
        $this->elrDataCreationForEinvoiceMapper();
        //avatax_conditional_payload_mapper
        $this->elrDataCreationForConditionalPayloadMapper();
        // utility function call for pre-requisites of ELR data creation
        $this->elrDataCreationForConditionalPayloadFilter();
        //add default custom fields
        $this->add_default_custom_fields();
    }

    /**
     * Determine if an order has a specific AvaTax status.
     *
     * @since 3.0.0
     * @param \WC_Order|\WC_Order_Refund|int $order The order object or ID.
     * @param string $status Optional. The AvaTax status to check. If none set, it checks if any
     *                       status is set.
     * @return bool Whether the order has the specific status.
     */
    public function order_has_elr_status($order, $status = '')
    {

        if (is_numeric($order)) {
            $order = wc_get_order($order);
        }

        $current_status = $this->get_order_elr_statuses($order);
        return $current_status == $status;
    }


    /**
     * Get the statuses of an order when last posted to AvaTax.
     *
     * Orders can have multiple statuses, like `posted` and 'refunded'.
     *
     * @since 3.0.0
     * @param \WC_Order|int $order The order object or ID.
     * @return array The order's AvaTax statuses.
     */
    public function get_order_elr_statuses($order)
    {

        if (is_numeric($order)) {
            $order = wc_get_order($order);
        }

        $current_status = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_elr_status', true);

        return $current_status;
    }

    /**
     * Generates HTML markup for displaying ELR (Electronic Logging Record) status information.
     *
     * Creates a formatted HTML structure containing the ELR status, message, and a download
     * invoice button within a metabox-style container.
     *
     * @since 3.0.0
     *
     * @param string $invoice_status        The current status of the invoice.
     * @param string $invoice_status_messages The messages related to the invoice status.
     * 
     * @return string Formatted HTML string containing the ELR status information.
     */
    /**
     * Builds the HTML rows for the buyer-feedback fields stored on an order/refund.
     *
     * Renders the businessStatus and inbound Application Response fields, skipping
     * any that have no stored value.
     *
     * @since 3.8.4
     *
     * @param int $id order or refund ID
     * @return string HTML rows, or empty string when nothing is stored
     */
    public function get_elr_buyer_feedback_html($id) {
        $utilities = wc_avatax()->wc_avatax_utilities();

        $fields = array(
            '_wc_avatax_business_status'           => __('Business Status', 'woocommerce-avatax'),
            '_wc_avatax_elr_requested_action_code' => __('Requested Action Code', 'woocommerce-avatax'),
            '_wc_avatax_elr_requested_action'      => __('Requested Action', 'woocommerce-avatax'),
            '_wc_avatax_elr_status_reason_code'    => __('Status Reason Code', 'woocommerce-avatax'),
            '_wc_avatax_elr_status_reason'         => __('Status Reason', 'woocommerce-avatax'),
        );

        $html = '';

        foreach ($fields as $meta_key => $label) {
            $value = $utilities->get_order_meta($id, $meta_key, true);

            if ('' === (string) $value) {
                continue;
            }

            $html .= sprintf(
                '<h4><label for="custom-value">%1$s</label></h4><span>%2$s</span>',
                esc_html($label),
                esc_html($value)
            );
        }

        return $html;
    }

    /**
     * Get the payment-reporting action button HTML for an order/refund.
     *
     * @since 3.8.4
     *
     * @param int    $id order or refund ID
     * @param string $button_class button CSS class
     * @param string $title button tooltip text
     * @return string
     */
    protected function get_payment_reporting_button_html($id, $button_class, $title) {
        $elr_handler = wc_avatax()->get_elr_handler();

        if (!$elr_handler || !method_exists($elr_handler, 'canReportPaymentToELR') || !$elr_handler->canReportPaymentToELR($id)) {
            return '';
        }

        return sprintf(
            '<button type="button" id="report-payment" title="%1$s" class="button actionButton %2$s" data-order_id="%3$s"></button>',
            esc_attr($title),
            esc_attr($button_class),
            esc_attr($id)
        );
    }

    public function get_elr_status_html($order_id, $invoice_status, $processing_id, $invoice_status_messages) {
        $paymentReportingButton = $this->get_payment_reporting_button_html(
            $order_id,
            'report_payment',
            __('Report payment to Avalara E-invoicing and Live Reporting', 'woocommerce-avatax')
        );

        if ($invoice_status) {
            $mediaTypes = wc_avatax()
                                ->wc_avatax_utilities()
                                ->get_order_meta($order_id, '_wc_avatax_elr_media_type', true);
                                
            return sprintf(
                '<div id="order-%1$s" class="customer-history order-attribution-metabox elr-status-box">
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <h4><label for="custom-value">%4$s</label></h4>
                    <span>%5$s</span>
                    %8$s
                    <h4><label for="custom-value">%6$s</label></h4>
                    <span>%7$s</span>
                    <div style="display:flex; justify-content:flex-end; align-items:center; gap:6px; flex-wrap:wrap;">'
                        .'<button type="button" id="refresh-status" class="button actionButton refresh_status" title="Refresh invoice status" data-order_id="%1$s"></button>'
                        .($invoice_status === 'Complete' && !empty($mediaTypes) ?
                        '<select class="selectDocType" id="selectDocType">'
                        .$this->getDownlodableDocTypeOptions($order_id)
                        .'</select>'
                        .'<button type="button" id="download-invoice" class="button actionButton download_document" '
                        .'title="Download invoice" data-order_id="%1$s"></button>'
                        : '')
                        .($invoice_status === 'Error' ? '<button type="button" id="send-refund" title="Send to Avalara E-invoicing and Live Reporting" class="button actionButton send_order" data-order_id="%1$s"></button>' : '')
                        .$paymentReportingButton
                    .'</div>
                </div>',
                $order_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent",
                __('Document ID', 'woocommerce-avatax'),
                $processing_id,
                __('Messages', 'woocommerce-avatax'),
                $invoice_status_messages,
                $this->get_elr_buyer_feedback_html($order_id)
            );
        } else {
            return sprintf(
                '<div  id="order-%1$s" data-order_id="%1$s" class="customer-history order-attribution-metabox elr-status-box">
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <div style="text-align:right;">%4$s</div>
                </div>',
                $order_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent",
                $paymentReportingButton
            );
        }
    }

    /**
     * Generates HTML markup for displaying ELR (Electronic Logging Record) status information.
     *
     * Creates a formatted HTML structure containing the ELR status, message, and a download
     * invoice button within a metabox-style container.
     *
     * @since 3.0.0
     *
     * @param string $invoice_status        The current status of the invoice.
     * @param string $invoice_status_messages The messages related to the invoice status.
     * 
     * @return string Formatted HTML string containing the ELR status information.
     */
    public function get_elr_refund_status_html($refund_id, $invoice_status, $processing_id, $invoice_status_messages) {
        $paymentReportingButton = $this->get_payment_reporting_button_html(
            $refund_id,
            'report_refund_payment',
            __('Report refund payment to Avalara E-invoicing and Live Reporting', 'woocommerce-avatax')
        );

        if ($invoice_status) {
            $mediaTypes = wc_avatax()
                            ->wc_avatax_utilities()
                            ->get_order_meta($refund_id, '_wc_avatax_elr_media_type', true);

            return sprintf(
                '<div id="order-%1$s" class="customer-history order-attribution-metabox elr-status-box refund">
                    <h4><label for="custom-value">Refund #%1$s</label></h4> 
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <h4><label for="custom-value">%4$s</label></h4>
                    <span>%5$s</span>
                    %8$s
                    <h4><label for="custom-value">%6$s</label></h4>
                    <span>%7$s</span>
                    <div style="display:flex; justify-content:flex-end; align-items:center; gap:6px; flex-wrap:wrap;">'
                        .'<button type="button" id="refund-refresh-status" class="button actionButton refresh_status" title="Refresh invoice status" data-order_id="%1$s"></button>'
                        .($invoice_status === 'Complete' && !empty($mediaTypes) ?
                        '<select class="selectDocType">'
                        .$this->getDownlodableDocTypeOptions($refund_id)
                        .'</select>'
                        .'<button type="button" id="download-invoice" class="button actionButton download_document" '
                        .'title="Download credit note" data-order_id="%1$s"></button>'
                        : '')
                        .($invoice_status === 'Error' ? '<button type="button" id="send-refund" title="Send to Avalara E-invoicing and Live Reporting" class="button actionButton send_refund" data-order_id="%1$s"></button>' : '')
                        .$paymentReportingButton.
                    '</div>
                </div>',
                $refund_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent",
                __('Document ID', 'woocommerce-avatax'),
                $processing_id,
                __('Messages', 'woocommerce-avatax'),
                $invoice_status_messages,
                $this->get_elr_buyer_feedback_html($refund_id)
            );
         } else {
            return sprintf(
                '<div id="order-%1$s" class="customer-history order-attribution-metabox elr-status-box refund">
                    <h4><label for="custom-value"><u>Refund #%1$s</u></label></h4> 
                    <h4><label for="custom-value">%2$s</label></h4> 
                    <span>%3$s</span>
                    <div style="text-align:right;">
                        <button type="button" id="send-refund" title="Send to Avalara E-invoicing and Live Reporting" class="button actionButton send_refund" data-order_id="%1$s"></button>
                        %4$s
                    </div>
                </div>',
                $refund_id,
                __('Status', 'woocommerce-avatax'),
                $invoice_status ? $invoice_status : "Not sent",
                $paymentReportingButton
            );
        }
    }

    /**
     * Generates HTML markup for the AR-Outbound (Application Response) status meta box.
     *
     * Mirrors {@see get_elr_status_html()} but is scoped to the AR-Outbound
     * document: it renders the AR status, the document ID received from
     * Avalara, the status messages, and a refresh action button. Sending the
     * Application Response happens via the order-action dropdown and the
     * AR-Outbound status setting, so no Send button is rendered here.
     *
     * @since 3.10.0
     *
     * @param int    $order_id                The order ID.
     * @param string $ar_status               The current AR-Outbound status.
     * @param string $document_id             The AR-Outbound document ID returned by Avalara.
     * @param string $ar_status_messages      The rendered AR-Outbound status messages.
     * @return string Formatted HTML string.
     */
    public function get_ar_outbound_status_html($order_id, $ar_status, $document_id, $ar_status_messages) {
        $refresh_button = '<button type="button" id="ar-refresh-status" class="button actionButton refresh_status ar_refresh_status" title="Refresh AR-Outbound status" data-order_id="' . esc_attr($order_id) . '"></button>';

        if ($ar_status) {
            $download_ui = '';

            $ar_media_types = wc_avatax()
                ->wc_avatax_utilities()
                ->get_order_meta($order_id, \WC_AvaTax_Elr::WC_AVATAX_AR_OUTBOUND_MEDIA_TYPE, true);

            if ('Complete' === $ar_status && !empty($ar_media_types)) {
                $download_ui = '<select class="selectDocType" id="selectArOutboundDocType">'
                    . $this->getDownlodableDocTypeOptions($order_id, \WC_AvaTax_Elr::WC_AVATAX_AR_OUTBOUND_MEDIA_TYPE)
                    . '</select>'
                    . '<button type="button" id="ar-download-invoice" class="button actionButton download_document" '
                    . 'title="Download AR-Outbound document" data-document_context="ar_outbound" data-order_id="' . esc_attr($order_id) . '"></button>';
            }

            return sprintf(
                '<div id="ar-outbound-%1$s" class="customer-history order-attribution-metabox elr-status-box ar-outbound">
                    <h4><label for="custom-value">%2$s</label></h4>
                    <span>%3$s</span>
                    <h4><label for="custom-value">%4$s</label></h4>
                    <span>%5$s</span>
                    <h4><label for="custom-value">%6$s</label></h4>
                    <span>%7$s</span>
                    <div style="text-align:right;">%8$s%9$s</div>
                </div>',
                $order_id,
                __('Status', 'woocommerce-avatax'),
                $ar_status ? $ar_status : "Not sent",
                __('Document ID', 'woocommerce-avatax'),
                $document_id,
                __('Messages', 'woocommerce-avatax'),
                $ar_status_messages,
                $refresh_button,
                $download_ui
            );
        }

        return sprintf(
            '<div id="ar-outbound-%1$s" data-order_id="%1$s" class="customer-history order-attribution-metabox elr-status-box ar-outbound">
                <h4><label for="custom-value">%2$s</label></h4>
                <span>%3$s</span>
                <div style="text-align:right;">%4$s</div>
            </div>',
            $order_id,
            __('Status', 'woocommerce-avatax'),
            "Not sent",
            $refresh_button
        );
    }

    /**
     * Registers or updates the ELR tenant application with the CCS API.
     * 
     * This function handles both registration (POST) and update (PUT) operations for the ELR application.
     * A successful operation is indicated by response codes:
     * - 200: Success
     * - 409: Application already exists
     * 
     * @since 1.0.0
     * 
     * @param string $type The HTTP method type ('POST' for registration or 'PUT' for update)
     * 
     * @return void
     */
    public function register_or_update_elr_tenant($type)
    {
        $integration_api = $this->get_integration_api(true);
        $response_code = $integration_api->register_elr_app_on_ccs($type);

        if (($response_code == "200" || $response_code == "409")) {
            wc_avatax()->log_elr("Successfully " . ($type == "POST" ? "registered" : "updated") . " ELR app for sending schema to CCS API");
        }
    }

    /**
     * Generates HTML option elements for document type selection based on media types.
     *
     * @since 0.0.0
     *
     * @param int|string $invoiceId The ID of the invoice/order to retrieve media types for
     * @return string HTML string containing option elements for document types
     *                Empty string if no media types are found
     */
    public function getDocTypeOptions($invoiceId)
    {
        $docTypeOptions = '';
        $mediaTypes = (array) wc_avatax()
            ->wc_avatax_utilities()
            ->get_order_meta($invoiceId, '_wc_avatax_elr_media_type', true);
        foreach ($mediaTypes as $mediaType) {
            $parts = explode("/", $mediaType);
            if (isset($parts[1])) {
                $docTypeOptions .= '<option value="' . $mediaType . '">' . $parts[1] . '</option>';
            }
        }
        return $docTypeOptions;
    }
    public function getDownlodableDocTypeOptions($invoiceId, $meta_key = '_wc_avatax_elr_media_type')
    {
	$docTypeOptions = '';
	$mediaTypes = (array) wc_avatax()
		->wc_avatax_utilities()
		->get_order_meta($invoiceId, $meta_key, true);
	    foreach ($mediaTypes as $item) {
            $context = '';
            $mediaType = '';
            if (is_object($item)) {
                $context = isset($item->context) ? (string) $item->context : '';
                $mediaType = isset($item->mediaType) ? (string) $item->mediaType : '';
            } elseif (is_array($item)) {
                $context = isset($item['context']) ? (string) $item['context'] : '';
                $mediaType = isset($item['mediaType']) ? (string) $item['mediaType'] : '';
            } elseif (is_string($item)) {
                $mediaType = $item;
            }
            if ('' === $mediaType) {
                continue;
            }
            if ('' !== $context && 'OUTPUT' !== strtoupper($context)) {
                continue;
            }
            $parts = explode('/', $mediaType);
            if (isset($parts[1])) {
                $docTypeOptions .= '<option value="' . esc_attr($mediaType) . '">' . esc_html($parts[1]) . '</option>';
            }
	    }
	    return $docTypeOptions;
    }

    /**
     * Backfills refund/payment mapper data from the parent order when refund values are missing.
     *
     * For direct refunds, parent records are loaded from the order entity; for payment
     * e-reporting refund entities, parent records are loaded using the current entity type.
     *
     * @param array  $uniqueInvoiceRecords Invoice/refund mapper output collected so far.
     * @param object $entityObj            Order-like entity returned by wc_get_order().
     * @param string $entity_type          ELR entity type currently being prepared.
     * @return array
     */
    protected function mergeParentRefundFallbackRecords(array $uniqueInvoiceRecords, $entityObj, $entity_type)
        {
            $shouldMergeParentRefundValues =
                $entityObj
                && 'shop_order_refund' === $entityObj->get_type()
                && $entityObj->get_parent_id()
                && (
                    'refund' === $entity_type
                    || in_array($entity_type, self::PAYMENT_ENTITY_TYPES, true)
                );
            if (!$shouldMergeParentRefundValues) {
                return $uniqueInvoiceRecords;
            }
            $parent_order_id = $entityObj->get_parent_id();
            if ('refund' === $entity_type) {
                $uniqueParentOrderRecords = $this->getUniqueInvoiceRecords($parent_order_id, 'order');
            } else {
                $uniqueParentOrderRecords = $this->getUniqueInvoiceRecords($parent_order_id, $entity_type);
            }
            if (empty($uniqueParentOrderRecords)) {
                return $uniqueInvoiceRecords;
            }
            foreach ($uniqueParentOrderRecords as $key => $parentRecord) {
                if (!isset($uniqueInvoiceRecords[$key]) || empty($uniqueInvoiceRecords[$key])) {
                    $uniqueInvoiceRecords[$key] = $parentRecord;
                    continue;
                }
                if (
                    is_array($uniqueInvoiceRecords[$key])
                    && $this->is_all_null_nested_array($uniqueInvoiceRecords[$key])
                ) {
                    $uniqueInvoiceRecords[$key] = $parentRecord;
                    continue;
                }
                if (
                    is_array($uniqueInvoiceRecords[$key])
                    && is_array($parentRecord)
                    && !array_is_list($uniqueInvoiceRecords[$key])
                ) {
                    foreach ($uniqueInvoiceRecords[$key] as $field => $value) {
                        if (is_null($value) && array_key_exists($field, $parentRecord)) {
                            $uniqueInvoiceRecords[$key][$field] = $parentRecord[$field];
                        }
                    }
                }
            }
            return $uniqueInvoiceRecords;
        }

        /**
         * Determines whether a nested associative array contains only null/empty scalar values.
         *
         * The helper returns false for non-arrays, empty arrays, flat arrays, or arrays that
         * contain deeper nesting. It is used to detect refund mapper sections that should be
         * fully replaced by parent-order fallback data.
         *
         * @param mixed $value Candidate nested array value.
         * @return bool
         */
        protected function is_all_null_nested_array($value)
        {
            if (!is_array($value) || empty($value)) {
                return false;
            }
            foreach ($value as $row) {
                if (!is_array($row)) {
                    return false;
                }
                foreach ($row as $fieldValue) {
                    if (is_array($fieldValue)) {
                        return false;
                    }
                    if ($fieldValue !== null && $fieldValue !== '') {
                        return false;
                    }
                }
            }
            return true;
        }
}
