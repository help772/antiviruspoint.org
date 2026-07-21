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
?>

<div id="hs_code_product_data" class="panel woocommerce_options_panel">
    <div class="hs-code-header">
        <h3><?php esc_html_e('HS Codes', 'woocommerce-avatax'); ?></h3>
        <div class="hs-code-controls">
            <select class="wc-avatax-country-select">
                <option value=""><?php esc_html_e('Select a country', 'woocommerce-avatax'); ?></option>
                <?php
                if (!empty($uniqueCountries)) {
                    foreach (array_keys($uniqueCountries) as $code) {
                        echo '<option value="' . esc_attr($code) . '">' . esc_html($code) . '</option>';
                    }
                }
                ?>
            </select>
            <button type="button" id="wc_avatax_add_hs_code" class="button-primary">
                <?php esc_html_e('Add HS Code', 'woocommerce-avatax'); ?>
            </button>
        </div>
    </div>

    <div class="hs-codes-container">
        <?php
        if ($hsCodeCountries) {
            foreach ($hsCodeCountries as $key => $value) {
                $fieldName = "_wc_avatax_hs_" . $key;
                $hsCode = $product->get_meta($fieldName, true);
                echo '<div class="hs-code-field" data-country="' . esc_attr($key) . '" data-name="' . esc_attr($key) . '">';
                echo '<label for="' . esc_attr($fieldName) . '">' . esc_html($key) . '</label>';
                echo '<input type="text" 
                         class="short required-hs-code" 
                         name="' . esc_attr($fieldName) . '" 
                         id="' . esc_attr($fieldName) . '" 
                         value="'.esc_attr($hsCode).'" 
                         placeholder="Enter HS code"
                         disabled
                         required 
                         style="width: 150px;">';
                echo '<button type="button" class="button edit-hs-code">Edit</button>';
                echo '<button type="button" class="button save-hs-code" style="display:none;">Save</button>';
                echo '<button type="button" class="button remove-hs-code">Remove</button>';
                echo '<span class="validation-message"></span>';
                echo '</div>';
            }
        }
        ?>
    </div>
</div>
<?php
// @codeCoverageIgnoreEnd
?>