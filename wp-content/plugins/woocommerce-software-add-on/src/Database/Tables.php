<?php
/**
 * Handles the plugin database tables.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Database;

use Themesquad\WC_Software_Addon\Utilities\String_Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tables.
 */
class Tables {

	/**
	 * Set up the database tables.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		self::migrate_legacy_tables();

		dbDelta( self::get_schema() );
	}

	/**
	 * Migrates legacy tables.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	private static function migrate_legacy_tables() {
		global $wpdb;

		// Legacy naming.
		if ( self::table_exists( 'woocommerce_software_licences' ) && ! self::table_exists( 'woocommerce_software_licenses' ) ) {
			$wpdb->query( "ALTER TABLE {$wpdb->wc_software_licenses_legacy} RENAME TO {$wpdb->wc_software_licenses};" );
			$wpdb->query( "ALTER TABLE {$wpdb->wc_software_licenses} RENAME COLUMN 'licence_key' TO 'license_key';" );
			$wpdb->query( "ALTER TABLE {$wpdb->wc_software_licenses} CHANGE `licence_key` `license_key` varchar(200);" );
			$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_software_license_key_prefix' WHERE meta_key = '_software_licence_key_prefix';" );
		}
	}

	/**
	 * Gets the schema for the database tables.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = ( $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '' );

		return "
CREATE TABLE {$wpdb->prefix}woocommerce_software_licenses (
  key_id bigint(20) NOT NULL auto_increment,
  order_id bigint(20) NOT NULL DEFAULT 0,
  activation_email varchar(200) NOT NULL,
  license_key varchar(200) NOT NULL,
  software_product_id varchar(200) NOT NULL,
  software_version varchar(200) NOT NULL,
  activations_limit varchar(9) NULL,
  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  PRIMARY KEY  (key_id)
) $collate;
CREATE TABLE {$wpdb->prefix}woocommerce_software_activations (
  activation_id bigint(20) NOT NULL auto_increment,
  key_id bigint(20) NOT NULL,
  instance varchar(200) NOT NULL,
  activation_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  activation_active int(1) NOT NULL DEFAULT 1,
  activation_platform varchar(200) NULL,
  PRIMARY KEY  (activation_id)
) $collate;
		";
	}

	/**
	 * Registers the database tables.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function register_tables() {
		global $wpdb;

		// List of custom tables without prefixes.
		$tables = array(
			'wc_software_licenses'        => 'woocommerce_software_licenses',
			'wc_software_licenses_legacy' => 'woocommerce_software_licences',
			'wc_software_activations'     => 'woocommerce_software_activations',
		);

		foreach ( $tables as $name => $table ) {
			$wpdb->$name    = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
	}

	/**
	 * Gets a list of the database tables.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @return array
	 */
	public static function get_tables() {
		global $wpdb;

		return array(
			"{$wpdb->prefix}woocommerce_software_licenses",
			"{$wpdb->prefix}woocommerce_software_licences",
			"{$wpdb->prefix}woocommerce_software_activations",
		);
	}

	/**
	 * Drops the tables from the database.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = self::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}

	/**
	 * Checks if the table exists in the database.
	 *
	 * @since 1.9.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param string $table_name The table name.
	 * @return bool
	 */
	protected static function table_exists( $table_name ) {
		global $wpdb;

		$table_name = String_Utils::maybe_prefix( $table_name, $wpdb->prefix );

		return (bool) $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) )
		);
	}
}
