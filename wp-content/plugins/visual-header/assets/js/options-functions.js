(function($, undefined) {
    'use strict';

    function isset(variable) {

        if (variable === "undefined") {
            return '';
        } else if (variable === undefined) {
            return '';
        } else if (variable === null) {
            return '';
        } else if (variable === 0) {
            return 0;
        } else if (variable === '0') {
            return '0';
        } else {
            return variable;
        }
    }

	function escapeHTML(input) {
		try {
			const str = String(input);
			if (typeof str.replace !== 'function') {
				console.warn('escapeHTML: .replace not available', str);
				return '';
			}
	
			return str.replace(/[&<>"'`=\/]/g, function(s) {
				return {
					"&": "&amp;",
					"<": "&lt;",
					">": "&gt;",
					"\"": "&quot;",
					"'": "&#39;",
					"`": "&#x60;",
					"=": "&#x3D;",
					"/": "&#x2F;"
				}[s] || s;
			});
		} catch (e) {
			console.warn('escapeHTML: invalid input', input);
			return '';
		}
	}



 
    /*************************************************************************************************************************************************************************
    		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    																					 Mulit Options
    		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    		**************************************************************************************************************************************************************************/
    function vh_options_function_multi_item(value, id, vh_options) {
        var html = '';

        jQuery.each(vh_options, function(index, settings) {

            var vh_value_id = isset(value[settings['id']]);
            var vh_name = isset(settings['name']);
            var vh_id = id + '[' + isset(settings['id']) + ']';
            var vh_type = isset(settings['type']);
            var vh_desc = isset(settings['desc']);
            var vh_placeholder = isset(settings['placeholder']);
            var vh_unit = isset(settings['unit']);
            var vh_options = isset(settings['options']);


            html += '<li class="vh_multi_options_item vh_multi_options_' + vh_type + '">';

            if (vh_name) {
                html += '<label for="vh_label_' + vh_id + '">' + escapeHTML(vh_name) + '</label>';
            }

            switch (vh_type) {
                case 'text':
                    html += '<input type="text" placeholder="' + escapeHTML(vh_placeholder) + '" style="width:100px" name="' + vh_id + '" value="' + escapeHTML(vh_value_id) + '">';
                    break;
                case 'number':
                    html += '<input type="number" placeholder="' + escapeHTML(vh_placeholder) + '" style="width:50px" name="' + vh_id + '" id="vh_label_' + vh_id + '" value="' + escapeHTML(vh_value_id) + '">';
                    break;
                case 'select':
                    html += '<select name="' + vh_id + '" vh_placeholder="' + escapeHTML(vh_placeholder) + '" id="vh_label_' + vh_id + '">';
                    jQuery.each(vh_options, function(select_key, select_text) {
                        html += '<option value="' + select_key + '"' + (vh_value_id === select_key ? ' selected' : '') + '>' + escapeHTML(select_text) + '</option>';
                    });
                    html += '</select>';
                    break;
                case 'color':
                    html += '<input class="vh-coloris vh-color" data-rgba="false" type="text" name="' + vh_id + '" id="vh_label_' + id + '" value="' + escapeHTML(vh_value_id) + '">';
                    break;
                case 'color_rgba':
                    html += '<input class="vh-coloris vh-color" data-rgba="true" type="text" name="' + vh_id + '" id="vh_label_' + id + '" value="' + escapeHTML(vh_value_id) + '">';
                    break;

            }
            html += '</li>';
        });
        return html;
    }

    var defaults = {};
    $.fn.vh_options_functions = function(value, options) {
        var settings = $.extend(true, {}, defaults, options);

        var vh_value_id = isset(value[settings['id']]);
        var vh_name = isset(settings['name']);
        var vh_id = isset(settings['id']);
        var vh_type = isset(settings['type']);
        var vh_desc = isset(settings['desc']);
        var vh_placeholder = isset(settings['placeholder']);
        var vh_unit = isset(settings['unit']);
        var vh_options = isset(settings['options']);
        var vh_step = isset(settings['step']);
        var vh_min = isset(settings['min']);
        var vh_max = isset(settings['max']);
        var vh_fold = isset(settings['fold']);
        var vh_responsive = isset(settings['responsive']);


        var responsive_class = '';
        if (vh_responsive === 'desktop') {
            responsive_class = 'vh_responsive_desktop';
        } else if (vh_responsive === 'mobile') {
            responsive_class = 'vh_responsive_mobile';
        }

        var html = '';
        html += '<li class="vh_options_item  vh_options_' + vh_type + ' vh_id_' + vh_id + ' ' + responsive_class + ' " data-active="show">';

        if (vh_fold) {
            html += '<div class="vh_options_fold">';
            jQuery.each(vh_fold, function(fold_key, fold_value) {
                html += '<div class="vh_options_fold_item" data-name="' + fold_value + '" data-value="' + fold_key + '"></div>';
            });
            html += '</div>';
        }
         html += '<div class="vh_options_name">';
        html += '<label for="vh_label_' + vh_id + '" >' + escapeHTML(vh_name) + '</label>';
        html += '<div class="vh_options_description">' + escapeHTML(vh_desc) + '</div>';
        html += '</div>';
		
		if(vh_type!=='heading'){
        html += '<div class="vh_options_setting">';
		}

        switch (vh_type) {
            // Text


            case 'text':
                html += '<input type="text" name="' + vh_id + '" id="vh_label_' + vh_id + '" vh_placeholder="' + escapeHTML(vh_placeholder) + '" value="' + escapeHTML(vh_value_id) + '">';
                break;


                // Number
            case 'number':
                html += '<input type="number" name="' + vh_id + '" id="vh_label_' + vh_id + '" width="40px" vh_placeholder="' + escapeHTML(vh_placeholder) + '" value="' + escapeHTML(vh_value_id) + '"><span>' + vh_unit + '</span>';
                if (vh_max) {
                    html += ' <input type="range" class="vh_form_range" autocomplete="off" min="' + vh_min + '" max="' + vh_max + '" value="' + escapeHTML(vh_value_id) + '" step="' + vh_step + '">';
                }
                break;

                // Color
            case 'color':
                html += '<input class="vh-coloris vh-color" data-rgba="false" type="text" name="' + vh_id + '" id="vh_label_' + vh_id + '" value="' + escapeHTML(vh_value_id) + '">';
                break;

                // Color RGBA
            case 'color_rgba':
                html += '<input class="vh-coloris vh-color" data-rgba="true" type="text" name="' + vh_id + '" id="vh_label_' + vh_id + '" value="' + escapeHTML(vh_value_id) + '">';
                break;

                // Textarea
            case 'textarea':
                html += '<textarea name="' + vh_id + '" id="vh_label_' + vh_id + '" vh_placeholder="' + escapeHTML(vh_placeholder) + '">' + escapeHTML(vh_value_id) + '</textarea>';
                break;

                // Select
            case 'select':
                html += '<select name="' + vh_id + '" vh_placeholder="' + vh_placeholder + '" id="vh_label_' + vh_id + '">';
                jQuery.each(vh_options, function(select_key, select_text) {
                    html += '<option value="' + select_key + '"' + (vh_value_id === select_key ? ' selected' : '') + '>' + select_text + '</option>';
                });
                html += '</select>';
                break;

                // Checkbox/
            case 'checkbox':
                var checked = vh_value_id === 1 ? 'checked="checked"' : '';
                html += '<div class="vh_checkbox vh_checkbox_primary"><input type="checkbox" id="vh_label_' + vh_id + '" name="' + vh_id + '" value="' + escapeHTML(vh_value_id) + '"' + checked + '><label for="vh_label_' + vh_id + '"></label></div>';
                break;

                // Radio

            case 'radio':
                html += '<ul class="vh_multi_options vh_radio_selected">';
                jQuery.each(vh_options, function(radio_key, radio_text) {
                    var checked = vh_value_id === radio_key ? 'checked="checked"' : '';
                    html += '<li>';
                    html += '<input type="radio" name="' + vh_id + '" id="vh_label_' + vh_id + '_' + radio_key + '" value="' + escapeHTML(radio_key) + '" ' + checked + '>';
                    html += '<label for="vh_label_' + vh_id + '_' + radio_key + '">' + radio_text + '</label>';
                    html += '</li>';
                });
                html += '</ul>';
                break;



            case 'radio_image':
                html += '<ul class="vh_multi_options vh_radio_selected">';
                jQuery.each(vh_options, function(radio_key, radio_link) {
                    var checked = vh_value_id === radio_key ? 'checked="checked"' : '';
                    html += '<li>';
                    html += '<label>';
                    html += '<input type="radio" name="' + vh_id + '" id="vh_label_' + vh_id + '_' + radio_key + '" value="' + escapeHTML(radio_key) + '" ' + checked + '>';
                    html += '<img for="vh_label_' + vh_id + '_' + radio_key + '" src="' + radio_link + '"/>';
                    html += '</label>';
                    html += '</li>';
                });
                html += '</ul>';
                break;

            case 'multi_options':
                html += '<ul class="vh_multi_options">';
                html += vh_options_function_multi_item(vh_value_id, vh_id, vh_options);
                html += '</ul>';
                break;


            case 'image':
                html += '<a class="vh_image_upload button button-small"  data-name="' + vh_id + '"  >'+escapeHTML(vh_text.upload)+'</a>';
                html += '<div class="vh_image_item">';
                html += '<input  type="hidden" class="vh_attachment_' + vh_id + '" name="' + vh_id + '" 	value="' + escapeHTML(vh_value_id) + '">';
                html += '</div>';

                break;



        }
		if(vh_type !=='heading'){
        html += '</div>';
		}
        html += '</li>';

        /*************************************************************************************************************************************************************************
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
																					 Open Options
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		**************************************************************************************************************************************************************************/

        return html;

    };


})(jQuery);