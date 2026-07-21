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

namespace SkyVerge\WooCommerce\AvaTax\API\Responses;

use WC_AvaTax_API_Response;

defined( 'ABSPATH' ) or exit;

/**
 * Shared trait for tax code response classes.
 *
 * Contains common functionality used by both WC_Avatax_API_Tax_Code_Response
 * and WC_Avatax_API_Company_Tax_Code_Response.
 *
 * @since 2.6.1
 */
class WC_Avatax_API_Tax_Code_Response_Base extends WC_AvaTax_API_Response {

	/**
	 * Provides nextLink if available in response, if not then ''
	 *
	 * @since 2.6.1
	 *
	 * @param mixed $response_data
	 * @return string
	 */
	protected function get_next_link( $response_data ) {
		if ( isset( $response_data->{'@nextLink'} ) ) {
			return $response_data->{'@nextLink'};
		}
		return '';
	}

	/**
	 * Saves the tax codes from AvaTax.
	 *
	 * @since 2.6.1
	 *
	 * @param array|null $response The tax codes to save.
	 */
	protected function save_tax_codes( $response ) {
		if ( $this->maybe_create_table( $this->table_name, $this->create_ddl ) ) {
			foreach ( $response ?? [] as $tax_code ) {
				$this->create_update_tax_code( $tax_code );
			}
		} else {
			wc_avatax()->log( 'TaxCode table creation failed.' );
		}
	}

	/**
	 * Inserts or updates the tax code data in database.
	 *
	 * @since 2.6.1
	 *
	 * @param object $tax_code The tax code object to save.
	 */
	protected function create_update_tax_code( $tax_code ) {
		global $wpdb;

		$table = $this->table_name;

		$checkIfExists = $wpdb->get_var(
			$wpdb->prepare(
				/* phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared */
				'SELECT taxCode FROM `' . $table . '` WHERE taxCode = %s',
				$tax_code->taxCode
			)
		);

		if ( null === $checkIfExists ) {
			$wpdb->insert(
				$table,
				[
					'taxCode'       => $tax_code->taxCode,
					'taxCodeTypeId' => (int) $tax_code->taxCodeTypeId,
					'description'   => $tax_code->description,
					'entityUseCode' => $tax_code->entityUseCode,
					'isActive'      => (int) $tax_code->isActive,
				],
				[ '%s', '%d', '%s', '%s', '%d' ]
			);
		} else {
			$wpdb->update(
				$table,
				[
					'taxCodeTypeId' => (int) $tax_code->taxCodeTypeId,
					'description'   => $tax_code->description,
					'entityUseCode' => $tax_code->entityUseCode,
					'isActive'      => (int) $tax_code->isActive,
				],
				[ 'taxCode' => $tax_code->taxCode ],
				[ '%d', '%s', '%s', '%d' ],
				[ '%s' ]
			);
		}
	}

	/**
	 * Checks if the table is present or not, creates it if needed.
	 *
	 * @since 2.6.1
	 *
	 * @param string $table_name The table name to check/create.
	 * @param string $create_ddl The CREATE TABLE statement.
	 * @return bool True if table exists or was created, false otherwise.
	 */
	protected function maybe_create_table( $table_name, $create_ddl ) {
		global $wpdb;

		$exists = $wpdb->get_var(
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$wpdb->esc_like( $table_name )
			)
		);

		if ( $exists === $table_name ) {
			return true;
		}

		if ( defined( 'WC_ABSPATH' ) && defined( 'ABSPATH' ) && ! empty( ABSPATH ) && is_string( ABSPATH ) && file_exists( ABSPATH . '/wp-admin/includes/upgrade.php' ) ) {
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';
		}
		dbDelta( $create_ddl );

		$exists = $wpdb->get_var(
			$wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$wpdb->esc_like( $table_name )
			)
		);

		return ( $exists === $table_name );
	}
}

