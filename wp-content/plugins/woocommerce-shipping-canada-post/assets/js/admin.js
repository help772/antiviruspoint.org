/**
 * Box packing JavaScript for Canada Post shipping method.
 *
 * @package woocommerce-shipping-canada-post
 */

jQuery(document).ready(function($) {
    $('#woocommerce_canada_post_packing_method').change(function() {
        const $el1 = $('#canada_post_box_sizes_table_wrapper');
        const $el2 = $('#woocommerce_canada_post_max_weight');

        $(this).val() === 'box_packing'
            ? $el1.show()
            : $el1.hide();
        $(this).val() === 'weight'
            ? $el2.closest('tr').show()
            : $el2.closest('tr').hide();

    }).change();

    $('#woocommerce_canada_post_show_delivery_time').change(function() {
        const $el = $('#woocommerce_canada_post_delivery_time_delay');
        $(this).is(':checked')
            ? $el.closest('tr').show()
            : $el.closest('tr').hide();
    }).change();

    $('#woocommerce_canada_post_enable_flat_rates').change(function() {
        const $el = $('#canada_post_flat_rate_box_sizes_table_wrapper');
        $(this).val() === 'yes'
            ? $el.show()
            : $el.hide();
    }).change();

    $('#canada_post_box_sizes_table .insert').click(function() {
        var $tbody = $('#canada_post_box_sizes_table').find('tbody');
        var size = $tbody.find('tr').length;
        var code = '<tr class="new">\
                <td class="check-column"><input type="checkbox" /></td>\
                <td><input type="text" size="10" name="boxes_name[' + size + ']" required /></td>\
                <td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="boxes_outer_length[' + size + ']" />cm</td>\
                <td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="boxes_outer_width[' + size + ']" />cm</td>\
                <td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="boxes_outer_height[' + size + ']" />cm</td>\
                <td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="boxes_inner_length[' + size + ']" />cm</td>\
                <td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="boxes_inner_width[' + size + ']" />cm</td>\
                <td><input type="number" min="0.01" step="0.01" max="9999" size="5" name="boxes_inner_height[' + size + ']" />cm</td>\
                <td><input type="number" min="0" step="0.001" max="9999" size="5" name="boxes_box_weight[' + size + ']" />kg</td>\
                <td><input type="number" min="0.001" step="0.001" max="9999" size="5" name="boxes_max_weight[' + size + ']" />kg</td>\
                <td><input type="checkbox" name="boxes_enabled[' + size + ']" checked="checked" value="yes" /></td>\
            </tr>';

        $tbody.append(code);

        return false;
    });

    $('#canada_post_box_sizes_table .remove').click(function() {
        var $tbody = $('#canada_post_box_sizes_table').find('tbody');

        $tbody.find('.check-column input:checked').each(function() {
            $(this).closest('tr').hide().find('input').val('');
        });

        return false;
    });

    // Ordering
    $('.canada-post-settings-table tbody').sortable({
        items: 'tr',
        cursor: 'move',
        axis: 'y',
        handle: '.sort',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'wc-metabox-sortable-placeholder',
        start: function(event, ui) {
            ui.item.css('background-color', '#f6f6f6');
        },
        stop: function(event, ui) {
            ui.item.removeAttr('style');
            canada_post_services_row_indexes();
        }
    });

    function canada_post_services_row_indexes() {
        $('.canada-post-settings-table tbody tr').each(function(index, el) {
            $('input.order', el).val(parseInt($(el).index('.canada-post-settings-table tr'), 10));
        });
    }
});
