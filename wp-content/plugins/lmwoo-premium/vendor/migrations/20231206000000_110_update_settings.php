<?php

defined('ABSPATH') || exit;

/**
 * Migration for New Database Table
 *
 * @var string $migrationMode
 */

use LicenseManagerForWooCommerce\Setup;
use LicenseManagerForWooCommerce\Migration;

$table1 = $wpdb->prefix . Setup::ACTIVATIONS_TABLE_NAME;
$table2 = $wpdb->prefix . Setup::APPLICATION_TABLE_NAME;
$table3 = $wpdb->prefix . Setup::APPLICATION_META_TABLE_NAME;
$table4 = $wpdb->prefix . Setup::APPLICATION_RELEASE_TABLE_NAME;
$table5 = $wpdb->prefix . Setup::APPLICATION_RELEASE_META_TABLE_NAME;


/**
 * Upgrade
 */
if ( Migration::MODE_UP === $migrationMode  ) {

	if (!function_exists('dbDelta')) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	}
	dbDelta( "
        CREATE TABLE IF NOT EXISTS $table1 (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `token` LONGTEXT NOT NULL COMMENT 'Public identifier',
            `license_id` BIGINT(20) UNSIGNED NOT NULL,
            `label` VARCHAR(255) NULL DEFAULT NULL,
            `source` VARCHAR(255) NOT NULL,
            `ip_address` VARCHAR(255) NULL DEFAULT NULL,
            `user_agent` TEXT NULL DEFAULT NULL,
            `meta_data` LONGTEXT NULL DEFAULT NULL,
            `created_at` DATETIME NULL DEFAULT NULL,
            `updated_at` DATETIME NULL DEFAULT NULL,
            `deactivated_at` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    " );
	 dbDelta( "
            CREATE TABLE IF NOT EXISTS $table2 (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` TEXT NOT NULL,
                `type` VARCHAR(255) NULL DEFAULT NULL,
                `stable_release_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `description` LONGTEXT NULL DEFAULT NULL,
                `documentation` LONGTEXT NULL DEFAULT NULL,
                `support` LONGTEXT NULL DEFAULT NULL,
                `gallery` LONGTEXT NULL DEFAULT NULL,
                `created_at` DATETIME NULL COMMENT 'Creation Date',
                `created_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
                `updated_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        " );

		dbDelta( "
            CREATE TABLE IF NOT EXISTS $table3 (
                `meta_id` BIGINT(20) UNSIGNED AUTO_INCREMENT,
                `application_id` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL,
                `meta_key` VARCHAR(255) NULL,
                `meta_value` LONGTEXT NULL,
                `created_at` DATETIME NULL,
                `created_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                `updated_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                PRIMARY KEY (`meta_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        " );

		dbDelta( "
            CREATE TABLE IF NOT EXISTS $table4 (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `application_id` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL,
                `version` VARCHAR(255) NOT NULL,
                `download_type` VARCHAR(255) NOT NULL,
                `download_file` TEXT NOT NULL,
                `changelog` LONGTEXT NULL,
                `created_at` DATETIME NULL COMMENT 'Creation Date',
                `created_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
                `updated_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        " );

		dbDelta( "
            CREATE TABLE IF NOT EXISTS $table5 (
                `meta_id` BIGINT(20) UNSIGNED AUTO_INCREMENT,
                `release_id` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL,
                `meta_key` VARCHAR(255) NULL,
                `meta_value` LONGTEXT NULL,
                `created_at` DATETIME NULL,
                `created_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                `updated_by` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                PRIMARY KEY (`meta_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        " );

		$lmfwc_settings_general = get_option('lmfwc_settings_general', array());
		$general_array = array(
		'lmfwc_enable_my_account_endpoint' => !empty($lmfwc_settings_general['lmfwc_enable_my_account_endpoint']) ? $lmfwc_settings_general['lmfwc_enable_my_account_endpoint'] : 0,
		'lmfwc_allow_users_to_activate' => !empty($lmfwc_settings_general['lmfwc_allow_users_to_activate']) ? $lmfwc_settings_general['lmfwc_allow_users_to_activate'] : 0,
		'lmfwc_allow_users_to_deactivate' => !empty($lmfwc_settings_general['lmfwc_allow_users_to_deactivate']) ? $lmfwc_settings_general['lmfwc_allow_users_to_deactivate'] : 0,
		'lmfwc_auto_delivery' => !empty($lmfwc_settings_general['lmfwc_auto_delivery']) ? $lmfwc_settings_general['lmfwc_auto_delivery'] : 0,
		'lmfwc_enable_stock_manager' => !empty($lmfwc_settings_general['lmfwc_enable_stock_manager']) ? $lmfwc_settings_general['lmfwc_enable_stock_manager'] : 0,
		);
		$lmfwc_settings_orderStatus = get_option('lmfwc_settings_order_status', array());
		$lmfwc_settings_woocommerce = get_option('lmfwc_settings_woocommerce', array());
		$lmfwc_settings_woocommerce = array_merge($general_array, $lmfwc_settings_orderStatus, $lmfwc_settings_woocommerce);
		update_option('lmfwc_settings_woocommerce', $lmfwc_settings_woocommerce);

}

/**
 * Downgrade
 */
if (  Migration::MODE_DOWN === $migrationMode  ) {
	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table1));
	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table2));
	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table3));
	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table4));
	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %s', $table5));
	delete_option('lmfwc_settings_woocommerce');
}
