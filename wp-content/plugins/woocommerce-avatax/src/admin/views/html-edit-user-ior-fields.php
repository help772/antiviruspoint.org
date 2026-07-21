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

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) or exit;

/**
 * Display the user ior (importer of record) fields.
 *
 * @type string $selected_ior saved value for the current user
 * @type string $selected_deliveryterms saved value for delivery terms
 */
$selected_deliveryterms = get_user_meta( $user->ID, 'wc_avatax_user_deliveryterms', true );
$selected_buyers_agent = get_user_meta( $user->ID, 'wc_avatax_user_buyers_agent', true );
?>

<!-- <h3><?php esc_html_e( 'Seller importer of record', 'woocommerce-avatax' ); ?></h3> -->

<table class="form-table" aria-describedby="lbl_wc_avatax_user_ior">
	<tr>
		<th>
            <label id="lbl_wc_avatax_user_ior" for="wc_avatax_user_ior">
                <?php esc_html_e('Seller importer of record', 'woocommerce-avatax'); ?>
            </label>
        </th>
		<td>
			<select name="wc_avatax_user_ior" id="wc_avatax_user_ior" style="width: 25em;">
				<!-- <option value=""><?php esc_attr_e( 'Avatax Default', 'woocommerce-avatax' ); ?></option> -->
                <option value="<?php echo esc_attr(""); ?>" <?php selected( $selected_ior, esc_attr(""), true ) ?> > <?php echo esc_attr( "Avatax Default" ); ?></option>
                <option value="<?php echo esc_attr("Yes"); ?>" <?php selected( $selected_ior, esc_attr("Yes"), true ) ?> > <?php echo esc_attr( "Yes" ); ?></option>
                <option value="<?php echo esc_attr("No"); ?>" <?php selected( $selected_ior, esc_attr("No"), true ) ?>  > <?php echo esc_attr( "No" ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
        <th><label for="wc_avatax_user_deliveryterms"><?php esc_html_e( 'Delivery Terms', 'woocommerce-avatax' ); ?></label></th>
        <td>
            <select name="wc_avatax_user_deliveryterms" id="wc_avatax_user_deliveryterms" style="width: 25em;">
				<option value="<?php echo esc_attr(""); ?>" <?php selected( $selected_deliveryterms, esc_attr(""), true ) ?> > <?php echo esc_attr( "No Delivery Terms" ); ?></option>
                <option value="<?php echo esc_attr("DDP"); ?>" <?php selected( $selected_deliveryterms, esc_attr("DDP"), true ) ?> > <?php echo esc_attr( "DDP: Delivered Duty Paid" ); ?></option>
                <option value="<?php echo esc_attr("DAP"); ?>" <?php selected( $selected_deliveryterms, esc_attr("DAP"), true ) ?> > <?php echo esc_attr( "DAP: Delivered At Place" ); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="wc_avatax_user_buyers_agent"><?php esc_html_e( 'Is Buyers Agent', 'woocommerce-avatax' ); ?></label></th>
        <td>
            <select name="wc_avatax_user_buyers_agent" id="wc_avatax_user_buyers_agent" style="width: 25em;">
                <option value="<?php echo esc_attr(""); ?>" <?php selected( $selected_buyers_agent, esc_attr(""), true ) ?> > <?php echo esc_attr( "No buyer agent field" ); ?></option>
                <option value="<?php echo esc_attr("Yes"); ?>" <?php selected( $selected_buyers_agent, esc_attr("Yes"), true ) ?> > <?php echo esc_attr( "Yes" ); ?></option>
                <option value="<?php echo esc_attr("No"); ?>" <?php selected( $selected_buyers_agent, esc_attr("No"), true ) ?> > <?php echo esc_attr( "No" ); ?></option>
            </select>
        </td>
    </tr>
</table>
<?php
// @codeCoverageIgnoreEnd
?>