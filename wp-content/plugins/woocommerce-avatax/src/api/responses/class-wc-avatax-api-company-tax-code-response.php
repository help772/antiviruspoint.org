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

use WC_AvaTax;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API companies tax code response class.
 *
 * @since 2.6.1
 */
class WC_Avatax_API_Company_Tax_Code_Response extends WC_Avatax_API_Tax_Code_Response_Base {

	/** @var bool */
	public $isSuccess = false;

	/** @var string */
	protected $table_name = 'wp_wc_avatax_tax_codes';

	/** @var string */
	protected $create_ddl = 'CREATE TABLE wp_wc_avatax_tax_codes (taxCode VARCHAR(100) NOT NULL, taxCodeTypeId VARCHAR(10) NOT NULL, description VARCHAR(500) DEFAULT NULL, entityUseCode VARCHAR(100) DEFAULT NULL, isActive TEXT NOT NULL, INDEX(taxCode, description) )';

	/**
	 * Gets the company tax codes from AvaTax.
	 *
	 * @since 2.6.1
	 *
	 * @return bool
	 */
	public function get_company_tax_code_list() {

		$this->save_tax_codes( $this->value );

		$nextLink = $this->get_next_link( $this->response_data );

		while ( ! empty( $nextLink ) ) {
			$token_array   = explode( '/v2', $nextLink );
			$paginated_url = end( $token_array );

			$response = WC_AvaTax::instance()->get_api()->get_company_tax_codes( $paginated_url );

			$this->save_tax_codes( $response->response_data->value );

			$nextLink = $this->get_next_link( $response->response_data );
		}

		return true;
	}
}
