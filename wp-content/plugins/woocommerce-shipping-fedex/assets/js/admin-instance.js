jQuery( document ).ready(function(){

	jQuery('#woocommerce_fedex_packing_method').change(function(){

		if ( jQuery(this).val() == 'box_packing' )
			jQuery('#packing_options').show();
		else
			jQuery('#packing_options').hide();

	}).change();

	jQuery('#woocommerce_fedex_freight_enabled').change(function(){

		if ( jQuery(this).is(':checked') ) {

			var $table = jQuery('#woocommerce_fedex_freight_enabled').closest('table');

			$table.find('tr:not(:first)').show();

		} else {

			var $table = jQuery('#woocommerce_fedex_freight_enabled').closest('table');

			$table.find('tr:not(:first)').hide();
		}

	}).change();

	// Adjust box max weight fields when FedEx One Rates are enabled.
	jQuery( '#woocommerce_fedex_fedex_one_rate' ).change( function() {
		if ( jQuery( this ).is( ':checked' ) ) {
			jQuery( '.wc-shipping-fedex-boxes-max-weight' ).each( function() {
				var field = jQuery( this );
				if ( field.data( 'one-rate-max-weight' ) ) {
					field.val( field.data( 'one-rate-max-weight' ) );
				}
			} );
		} else {
			jQuery( '.wc-shipping-fedex-boxes-max-weight' ).each( function() {
				var field = jQuery( this );
				if ( field.data( 'max-weight' ) ) {
					field.val( field.data( 'max-weight' ) );
				}
			} );
		}
	} ).change();

	jQuery('.fedex_boxes .insert').click( function() {
		var $tbody = jQuery('.fedex_boxes').find('tbody');
		var size = $tbody.find('tr').length;
		var code = '<tr class="new">\
				<td class="check-column"><input type="checkbox" /></td>\
				<td><input type="text" style="width:100% !important;" size="10" name="boxes_name[' + size + ']" /></td>\
				<td><input type="text" size="5" name="boxes_length[' + size + ']" /></td>\
				<td><input type="text" size="5" name="boxes_width[' + size + ']" /></td>\
				<td><input type="text" size="5" name="boxes_height[' + size + ']" /></td>\
				<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" /></td>\
				<td><input type="text" size="5" name="boxes_max_weight[' + size + ']" /></td>\
				<td>\
					<select name="boxes_type[' + size + ']">\
					<optgroup label="Custom">\
						<option value="YOUR_PACKAGING">YOUR_PACKAGING</option>\
					</optgroup>\
					<optgroup label="FedEx">\
						<option value="FEDEX_BOX:' + size + '">FEDEX_BOX</option>\
						<option value="FEDEX_10KG_BOX:' + size + '">FEDEX_10KG_BOX</option>\
						<option value="FEDEX_25KG_BOX:' + size + '">FEDEX_25KG_BOX</option>\
					</optgroup>\
					<optgroup label="FedEx One Rate">\
						<option value="FEDEX_ENVELOPE:' + size + '">FEDEX_ENVELOPE</option>\
						<option value="FEDEX_EXTRA_SMALL_BOX:' + size + '">FEDEX_EXTRA_SMALL_BOX</option>\
						<option value="FEDEX_SMALL_BOX:' + size + '">FEDEX_SMALL_BOX</option>\
						<option value="FEDEX_MEDIUM_BOX:' + size + '">FEDEX_MEDIUM_BOX</option>\
						<option value="FEDEX_LARGE_BOX:' + size + '">FEDEX_LARGE_BOX</option>\
						<option value="FEDEX_EXTRA_LARGE_BOX:' + size + '">FEDEX_EXTRA_LARGE_BOX</option>\
						<option value="FEDEX_PAK:' + size + '">FEDEX_PAK</option>\
						<option value="FEDEX_TUBE:' + size + '">FEDEX_TUBE</option>\
					</optgroup>\
					</select>\
				</td>\
				<td><input type="checkbox" name="boxes_enabled[' + size + ']" /></td>\
			</tr>';

		$tbody.append( code );

		return false;
	} );

	jQuery('.fedex_boxes .remove').click(function() {
		var $tbody = jQuery('.fedex_boxes').find('tbody');

		$tbody.find('.check-column input:checked').each(function() {
			jQuery(this).closest('tr').hide().find('input').val('');
		});

		return false;
	});

	// Ordering
	jQuery('.fedex_services tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: '.sort',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
			fedex_services_row_indexes();
		}
	});

	function fedex_services_row_indexes() {
		jQuery('.fedex_services tbody tr').each(function(index, el){
			jQuery('input.order', el).val( parseInt( jQuery(el).index('.fedex_services tr') ) );
		});
	};

});