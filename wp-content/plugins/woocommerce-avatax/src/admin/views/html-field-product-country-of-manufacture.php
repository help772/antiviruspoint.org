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
 * @codeCoverageIgnore
 */

 // @codeCoverageIgnoreStart
defined( 'ABSPATH' ) or exit;

/**
 * Display the Bulk Edit Tax Code field.
 */
?>

<p class="form-field _wc_avatax_county_of_manufacture_field" style="margin-bottom: 0 !important;">
	<label for="_wc_avatax_county_of_manufacture">COM (Country Of Manufacture)</label>
	<select name="_wc_avatax_county_of_manufacture" id="_wc_avatax_county_of_manufacture">
    <option value=""><?php esc_html_e('Select 2 Digit ISO Code', 'wc-avatax'); ?></option>
    <?php
    $countries = WC()->countries->get_countries();
    foreach ($countries as $code => $name) :
        $selected = selected($county_of_manufacture, $code, false);
        echo '<option value="' . esc_attr($code) . '"' . esc_attr($selected) . '>' . esc_html($code) . '</option>';
    endforeach;
    ?>
</select>
</p>
<?php
// @codeCoverageIgnoreEnd
?>