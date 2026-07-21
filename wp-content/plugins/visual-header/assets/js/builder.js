jQuery(function($) {
    jQuery(document).ready(function($) {
        "use strict";

        /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																			 Isset
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
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

        /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																			settingEmptyAndZero
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
        function settingEmptyAndZero(obj) {
            for (var key in obj) {
                if (obj[key] === null || obj[key] === undefined || obj[key] === 'undefined' || obj[key] === '') {
                    delete obj[key];
                } else if ($.type(obj[key]) === 'object') {
                    settingEmptyAndZero(obj[key]);
                    var sum = Object.values(obj[key]).reduce(function(acc, val) {
                        return acc + val;
                    }, 0);
                    if (sum === 0) {
                        delete obj[key];
                    }
                }
            }
            return obj;
        }

        /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																			Message
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
        function remove_add_error_loading() {
            var output = '';
            output = '<div class="vh_message vh-errored">';
            output += vh_text.error;
            output += '</div>';
            $('.vh-mouse-wait').append(output);
            setTimeout(function() {
                $('.vh-mouse-wait').remove()
            }, 2000);
        }

        function mouse_wait_start() {
            $('.vh_panel').append('<div class="vh-mouse-wait"></div>');
            setTimeout(function() {
                $('.vh-mouse-wait').addClass('vh-active-wait');
            }, 1);
        }



        function mouse_wait_end() {
            $('.vh-mouse-wait').removeClass('vh-active-wait');
            setTimeout(function() {
                $('.vh-mouse-wait').remove();
            }, 300);
        }


        function header_default() {
            var output = '';
            output = '<div class="vh_message vh-header-defaulted">';
            output += vh_text.defaulted;
            output += '</div>';
            $('body').append(output);
            setTimeout(function() {
                $('.vh_message').remove()
            }, 2000);
        }

        function header_created() {
            var output = '';
            output = '<div class="vh_message vh-header-imported">';
            output += vh_text.imported;
            output += '</div>';
            $('body').append(output);
            setTimeout(function() {
                $('.vh_message').remove()
            }, 2000);
        }

        function header_saveed() {
            var output = '';
            output = '<div class="vh_message vh-header-imported">';
            output += vh_text.saved;
            output += '</div>';
            $('body').append(output);
            setTimeout(function() {
                $('.vh_message').remove()
            }, 2000);
        }



        function vh_deactive_remove() {
            $('.vh_options').removeClass('vh_active');
            setTimeout(function() {
                $('.vh_options').remove();
            }, 210);
        }



        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            remove_add_error_loading();
        });


        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																			Confirm
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        function myConfirm(message, callback) {
            var confirmBox = $('<div class="vh_confirmbox"><div class="vh_confirmbox_middle"><span>' + message + '</span><a class="vh_yes vh_btn">Yes</button><a class="vh_no vh_btn">No</button></div></div>');
            confirmBox.on('click', '.vh_yes ', function(e) {
                callback(true);
                confirmBox.remove();
            });
            confirmBox.on('click', '.vh_no ', function(e) {
                callback(false);
                confirmBox.remove();
            });
            confirmBox.appendTo('body');
        }


        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	OutLine
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        function vh_output_online() {
            var outline = {};

            var navbar = [];
            var element = [];


            $('.vh_global_options').each(function() {
                var id = $(this).attr('data-id');
                var option = $(this).find('.vh_data_json').html();
                outline[id] = isset(encodeURIComponent(option));

            });


            $('.vh_navbar_item').each(function() {
                var key = $(this).attr('data-key');
                var id = $(this).attr('data-id');
                var option = $(this).children('.vh_data_json[data-row=navbar]').html();
                var navbar_key = {};
                navbar_key[key] = {
                    'id': id,
                    'option': encodeURIComponent(option)
                };
                navbar.push(navbar_key);
            });

            $('.vh_element_item').each(function() {
                var key = $(this).attr('data-key');
                var childern = $(this).attr('data-childern');
                var option = $(this).children('.vh_data_json[data-row=element]').html();
                var id = $(this).attr('data-id');

                var element_key = {};
                element_key[key] = {
                    'id': id,
                    'childern': childern,
                    'option': encodeURIComponent(option)
                };
                element.push(element_key);

            });
            outline['navbar'] = isset(encodeURIComponent(JSON.stringify(settingEmptyAndZero(navbar))));
            outline['element'] = isset(encodeURIComponent(JSON.stringify(settingEmptyAndZero(element))));
             return outline;


        }

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	Customize
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        function update_customize(new_val) {
            if (typeof wp !== 'undefined' && typeof wp.customize !== 'undefined' && typeof wp.customize('vh_builder_json') !== 'undefined') {
                var my_customize_obj = wp.customize('vh_builder_json');
                my_customize_obj.set(new_val);
            }
        }

        function vh_customize_height() {
            var item2Height = $('.vh_customize').outerHeight();
            $(':root').css('--item2-height', (item2Height + 30) + 'px');
        }
        vh_customize_height();
        $(window).on('resize load', vh_customize_height);
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	Builder_json
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
		var timer_vh_builder_json;

		function vh_builder_json() {
			if (timer_vh_builder_json) clearTimeout(timer_vh_builder_json);
		
			timer_vh_builder_json = setTimeout(function() {
				var $json = JSON.stringify(settingEmptyAndZero(vh_output_online()));
				$('#vh_builder_json').val($json);
		
				var $header_wrapper = $('.vh_preview').contents().find('#hw-header-wrapper,#rd-header-wrapper');
				$header_wrapper.addClass('vh_preview_loading');
		
				$.ajax({
					type: "POST",
					url: vh_builder_js.ajaxurl,
					data: {
						action: "vh_header_perview",
						header_preview: $json,
						_wpnonce: vh_settings.nonce,
					},
					success: function(response) {
						var $response = $(response);
						$header_wrapper.html('');
						$header_wrapper.removeClass('vh_preview_loading');
						$header_wrapper.append($response);
					    					 mouse_wait_end();
	
					}
				});
			}, 300);
		}
      
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        Desktop & Mobile Layout
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        $(document).on('click', '.vh_desktop_layout', function(e) {
            $('.vh_builder').removeClass('vh_mobile_active');
            $('.vh_builder').addClass('vh_desktop_active');
            $('.wp-full-overlay').addClass('preview-desktop ');
            $('.wp-full-overlay').removeClass('preview-mobile');
			        vh_customize_height();


        });
        $(document).on('click', '.vh_mobile_layout', function(e) {
            $('.vh_builder').addClass('vh_mobile_active');
            $('.vh_builder').removeClass('vh_desktop_active');
            $('.wp-full-overlay').addClass('preview-mobile');
            $('.wp-full-overlay').removeClass('preview-desktop');
			        vh_customize_height();

        });
  		 $(document).on('click', '.vh_full_screen', function(e) {
             $('.vh_builder').addClass('vh_builder_full_screen');
 

        });
		 $(document).on('click', '.vh_close_full_screen', function(e) {
             $('.vh_builder').removeClass('vh_builder_full_screen');
 

        });		
       
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        Sotable
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        function vh_sortable() {
            $('.vh_element_list').sortable({
                opacity: 0.6,
                connectWith: ".vh_element_list",
                update: function() {
                    var key = $(this).parents('.vh_column_item').data('key');
                    $(this).find('.vh_element_item').attr('data-childern', key);
                    vh_builder_json();
                 }
            });

        };
        vh_sortable();
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        	frontend
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
     
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        	Builder Custoize
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
          
    
      
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																			Has Tabs
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        function vh_has_tabs(vh_options_header) {
            var tab = {};
            $.each(vh_options_header, function(i, option) {
                if (option['group'] != undefined && option['group'] != '') {
                    var dass = option['group'];
                    if (tab[dass] == undefined) {
                        tab[dass] = option['group'];
                    }
                } else {
                    var general = vh_text.general;
                    var dass = vh_text.general;
                    if (tab[dass] == undefined) {
                        tab[dass] = general;
                    }
                }
            });
            return tab;
        }
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																		Options Tabs
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        var options_tabs = function(classes, settings) {
            var vh_options_header = settings.option;
            var html = '';

            var array_tab = vh_has_tabs(vh_options_header);
            var count_tab = 0;
            html = '<div class="vh_title_tabs">';

            $.each(array_tab, function(key, tabs) {
                count_tab++;
                var tab_active = count_tab === 1 ? 'vh_layout_active' : '';
                html += '<a class="vh_tab_' + key + ' ' + tab_active + '" data-id="' + key + '">' + tabs + '</a>';
            });

            html += '</div>';


            return html;
        };
		 function isValidJSON(jsonString) {
            try {
                JSON.parse(jsonString);
                return true;
            } catch (e) {
                return false;
            }
        }
        /*************************************************************************************************************************************************************************
        	--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																Options Content
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        var options_content = function(classes, settings) {
            var data_value = '';
            var value = '';
            if (isset(settings.json)) {
                data_value = settings.json.html();
                if (isset(data_value)) {
					if(isValidJSON(data_value)){
                    value = JSON.parse(data_value);
					}
				}
            }

            var vh_options_header = settings.option;
            var html = '';

            var array_tab = vh_has_tabs(vh_options_header);
            var count_container = 0;

            $.each(array_tab, function(key, tabs) {

                count_container++;
                var group_active = count_container === 1 ? 'vh_layout_group_active' : '';
                html += '<header class="vh_options_warp ' + group_active + '" data-tab="' + key + '">';


                $.each(vh_options_header, function(index, option) {
                    var general = option.group ? option.group : vh_text.general;
                    if (key === general && option.name && option.id && option.type) {
                        html += $('body').vh_options_functions(value, option);
                    }
                });

                html += '</header>';

            });


            return html;

        };
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																			Global Call Back
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        $(document).on('click', '.vh_options[data-row=global] .vh_options_update', function(e) {
            var data_option = JSON.stringify(settingEmptyAndZero($(this).parents('.vh_options').serializeJSON()));
            var data_id = $(this).parents('.vh_options').attr('data-id');
            $('.vh_global_options[data-id=' + data_id + '] .vh_data_json[data-row=global]').html('');
            $('.vh_global_options[data-id=' + data_id + '] .vh_data_json[data-row=global]').append(data_option);
            vh_builder_json();
            vh_deactive_remove();
         });



        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																	Option Button
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        var options_button = function(classes, settings) {
            var html = '<a class="vh_btn vh_options_cancel">' + vh_text.cancel + '</a>';
            html += '<a class="vh_btn vh_options_update">' + vh_text.update + '</a>';
            return html;
        };
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        Header Options
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        $(document).on('click', ".vh_global_options", function(e) {
            var data_id = $(this).attr('data-id');
            var data_json = $(this).find('.vh_data_json[data-row=global]');
            var data_name = $(this).find('span').text();
            $('body').vh_options({
                name: data_name,
                id: data_id,
                row: 'global',
                json: data_json,
                option: isset(vh_settings['global'][data_id]['options']),
                tabs: options_tabs,
                content: options_content,
                button: options_button,
            });
        });


        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																				Library 
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/


        /*--------------------------------------------------------------------------- Library Button ---------------------------------------------------------------------------*/

        var library_button = function(classes, settings) {
            var html = '<a class="vh_btn vh_options_cancel">' + vh_text.cancel + '</a>';
            html += '<a class="vh_btn vh_model_add">' + vh_text.vh_import + '</a>';
            return html;

        };

        /*--------------------------------------------------------------------------- Library Callback ---------------------------------------------------------------------------*/

        var library_callback = function(classes, settings) {
            classes.addClass('vh_active_loading');
            $.ajax({
                url: vh_builder_js.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vh_library',
					_wpnonce: vh_settings.nonce,

                },
                success: function(data) {

                    if (data.length) {
                        classes.find('.vh_options_content').append(data);
                        classes.removeClass('vh_active_loading');

                    } else {
                        remove_add_error_loading();
                    }

                }
            });

        };
        $(document).on('click', '.vh_options[data-row=library] .vh_model_add', function(e) {
            var data_id = $('.vh_options[data-id=library]').find('.selected').attr('data-id');
            mouse_wait_start();

            $.ajax({
                url: vh_builder_js.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vh_builder',
                    id: data_id,
                    library: true,
                    vh_builder_js,
					_wpnonce: vh_settings.nonce,

                },
                success: function(data) {
                    if (data.length) {
                        $('.vh_panel').html('');
                        $('.vh_panel').append(data);
						$('.vh_panel').off();
 						$('.vh_panel .vh_preview').on('load', function() {
                         vh_sortable();
						vh_builder_json();
 						});
						

                    } else {
                        remove_add_error_loading();
                    }
                }
            });

        });
        /*--------------------------------------------------------------------------- Library  ---------------------------------------------------------------------------*/

        $(document).on('click', ".vh_library", function(e) {
            var data_name = $(this).text();
            $('body').vh_options({
                name: vh_text.import_library,
                id: 'library',
                key: 'library',
                row: 'library',
				post_id:  vh_settings.post_id,

                json: '',
                button: library_button,
                callback: library_callback,
            });
        });

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																				Import
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        /*----------------------------------------------------------------------------Import Content ---------------------------------------------------------------------------*/

        var import_content = function(classes, settings) {
            var html = '<span>' + vh_text.import_json + '</span>';
            html += '<textarea id="vh_header_import" name="header_import"></textarea>';
            return html;

        };

        /*----------------------------------------------------------------------------Import Button ---------------------------------------------------------------------------*/

        var import_button = function(classes, settings) {
            var html = '<a class="vh_btn vh_options_cancel">' + vh_text.cancel + '</a>';
            html += '<a class="vh_btn vh_model_add">' + vh_text.vh_import + '</a>';
            return html;
        };

        /*----------------------------------------------------------------------------Import CallBack ---------------------------------------------------------------------------*/

        $(document).on('click', '.vh_options[data-id=import] .vh_model_add ', function(e) {
            mouse_wait_start();
            var data_option = $(this).parents('.vh_options').find('#vh_header_import').val();

            $.ajax({
                url: vh_builder_js.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vh_builder',
                    option: data_option,
                    json: true,
                    vh_builder_js,
					_wpnonce: vh_settings.nonce,
                    post_id:  vh_settings.post_id,

                },
                success: function(data) {
                    if (data.length) {
                        $('.vh_panel').html('');
						
                        $('.vh_panel').append(data);
                        $('.vh_panel').off();
						$('.vh_panel .vh_preview').on('load', function() {
                          vh_sortable();
						vh_builder_json();
  						});
                    } else {
                        remove_add_error_loading();
                    }

                }
            });

        });


        /*----------------------------------------------------------------------------Import Action ---------------------------------------------------------------------------*/

        $(document).on('click', ".vh_import_header", function(e) {
            var data_id = $(this).attr('data-id');
            var data_name = $(this).text();
            $('body').vh_options({
                id: 'import',
                key: 'import',
                name: vh_text.import_header,
                json: '',
                content: import_content,
                button: import_button,
                    post_id:  vh_settings.post_id,
            });
        });

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																				Export
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        /*----------------------------------------------------------------------------Export Content ---------------------------------------------------------------------------*/

        var export_content = function(classes, settings) {
            var html = '<span>' + vh_text.export_json + '</span>';
            html += '<textarea name="header_export">' + JSON.stringify(settingEmptyAndZero(vh_output_online())) + '</textarea>';

            return html;

        };
        /*----------------------------------------------------------------------------Export Header ---------------------------------------------------------------------------*/
        var export_button = function(classes, settings) {
            var html = '<a class="vh_btn vh_options_cancel">' + vh_text.cancel + '</a>';
            return html;
        };
        /*----------------------------------------------------------------------------Export Header ---------------------------------------------------------------------------*/

        $(document).on('click', ".vh_export_header", function(e) {
            var data_id = $(this).attr('data-id');
            var data_name = $(this).text();
            $('body').vh_options({
                id: 'export',
                key: 'export',
                name: vh_text.export_header,
                json: '',
                content: export_content,
                button: export_button,
            });
        });

        /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 																	 header Options
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/

        $(document).on('click', '.vh_make_it_default', function(e) {

            var header_item = $(this).parents('.vh_builder');
            var data_key = $('input[id=post_name]').val();
            var this_class = $(this);
		
 
            mouse_wait_start();
            $.ajax({
                url: vh_builder_js.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vh_header_default',
                    key: data_key,
                    post_id:  vh_settings.post_id,
					_wpnonce: vh_settings.nonce,

                },
                success: function(data) {
                    if (data.length) {
                        mouse_wait_end();
                         this_class.addClass('vh_is_default');
                        header_default();
                    } else {
                        remove_add_error_loading();
                    }
                }
            });


        });

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																	 Navbar Options
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/


        $(document).on('click', '.vh_options[data-row=navbar] .vh_options_update ', function(e) {
            var data_option = JSON.stringify(settingEmptyAndZero($(this).parents('.vh_options').serializeJSON()));
            var data_key = $(this).parents('.vh_options').attr('data-key');
            $('.vh_navbar_item[data-key=' + data_key + ']').find('.vh_data_json[data-row=navbar]').html('');
            $('.vh_navbar_item[data-key=' + data_key + ']').find('.vh_data_json[data-row=navbar]').append(data_option);
            vh_deactive_remove();
            vh_sortable();
            vh_builder_json();
         });




        $(document).on('click', ".vh_navbar_options", function(e) {
            var data_key = $(this).parents('.vh_navbar_item').attr('data-key');
            var data_json = $('.vh_navbar_item[data-key=' + data_key + ']').find('.vh_data_json[data-row=navbar]');
            var data_name = $(this).parent().attr('data-name');
            $('body').vh_options({
                name: data_name,
                id: 'navbar',
                key: data_key,
                row: 'navbar',
                json: data_json,
                option: vh_settings.navbar,
                tabs: options_tabs,
                content: options_content,
                button: options_button,
            });

        });


        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																	 Option Button
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        var add_element_content = function(classes, settings) {

            var vh_options_header = settings.option;
            var html = '';
            $.each(vh_options_header, function(index, header) {

                html += '<li class="vh_model_item" data-id="' + index + '"  >';
                html += '<div class="vh_model_image"  >';
                html += '<img src="' + header.img + '" />';
                html += '</div>';
                html += '<span>' + header.name + '</span>';
                html += '</li>';
            });
            return html;
        };
        var add_element_button = function(classes, settings) {
            return '<a class="vh_btn vh_model_add">' + vh_text.addelement + '</a>';


        };
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        														 Add Element CallBack
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/


        $(document).on('click', '.vh_options[data-id=element] .vh_model_add', function(e) {

            var data_key = Math.floor(Math.random() * 9999999999);
            var data_childern = $(this).parents('.vh_options[data-id=element]').attr('data-key');
            var data_id = $('.vh_options[data-id=element]').find('.selected').attr('data-id');
            var data_name = $('.vh_options[data-id=element]').find('.selected').find('span').html();

            var vh_options_header = vh_settings.element;


            var value_default = {};
            var data_option = vh_options_header[data_id]['options'];
            jQuery.each(data_option, function(index, option) {
                if (option['default']) {
                    value_default[option['id']] = option['default'];
                }


            });

            var elemenet = '<li class="vh_element_item" data-key="' + data_key + '"  data-id="' + data_id + '"  data-childern="' + data_childern + '" data-row="element">';
            elemenet += '<vh_data_json class="vh_data_json"  data-row="element">' + JSON.stringify(settingEmptyAndZero(value_default)) + '</vh_data_json>';
            elemenet += '<div class=" vh_element_title" data-name="' + data_name + ' ' + vh_text.option + '">';
            elemenet += '<span class="vh_element_name">' + data_name + '</span>';

            elemenet += '<div class="vh_element_title_bottom">';
            elemenet += '<a class="vh_element_options"></a>';
            elemenet += '<a class="vh_element_duplicate"></a>';
            elemenet += '<a class="vh_element_remove"></a>';
            elemenet += '</div>';
            elemenet += '</div>';
            elemenet += '</li>';
            $('.vh_column_item[data-key=' + data_childern + ']  .vh_element_list').append(elemenet);

            vh_deactive_remove();
            vh_sortable();
            vh_builder_json();
 		 });
 


        // Element Select
        $(document).on('click', '.vh_add_element', function(e) {
            $('#vh_model_element').attr('data-childern', $(this).parents('.vh_column_item').attr('data-key'));
            var data_key = $(this).parents('.vh_column_item').attr('data-key');

            $('body').vh_options({
                name: vh_text.addelement,
                id: 'element',
                key: data_key,
                row: 'element',
                option: vh_settings.element,
                type: 'model',
                content: add_element_content,
                button: add_element_button,
            });


        });




        $(document).on("click", ".vh_options .vh_model_item", function() {
            $(this).parents('.vh_options_content').find('.selected').removeClass('selected');
            $(this).addClass('selected');
        });



        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	 Eelement Options
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																	 Navbar Options
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/


        $(document).on('click', '.vh_options[data-row=element] .vh_options_update ', function(e) {
            var data_option = JSON.stringify(settingEmptyAndZero($(this).parents('.vh_options').serializeJSON()));
            var data_key = $(this).parents('.vh_options').attr('data-key');
            $('.vh_element_item[data-key=' + data_key + ']').find('.vh_data_json[data-row=element]').html('');
            $('.vh_element_item[data-key=' + data_key + ']').find('.vh_data_json[data-row=element]').append(data_option);
            vh_deactive_remove();
            vh_sortable();
            vh_builder_json();
         });



        $(document).on('click', '.vh_element_options', function() {
            var data_id = $(this).parents('.vh_element_item').attr('data-id');
            var data_key = $(this).parents('.vh_element_item').attr('data-key');
            var data_json = $('.vh_element_item[data-key=' + data_key + ']').find('.vh_data_json[data-row=element]');
            var data_name = $(this).parent().parent().attr('data-name');
            $('body').vh_options({
                name: data_name,
                id: data_id,
                key: data_key,
                row: 'element',
                json: data_json,
                option: vh_settings.element[data_id]['options'],
                tabs: options_tabs,
                content: options_content,
                button: options_button,
            });
        });



        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	 Duplicate
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        function vh_duplicate(row, key, adress) {
            $(adress).attr('data-key', key).attr('id', "vh_" + row + "_" + key);
        }


        jQuery(document).on("click", ".vh_element_duplicate", function() {

            var duplicate = $(this).parents(".vh_element_item").addClass('vh_duplicate').clone();
            $(this).parents('.vh_duplicate').removeClass('vh_duplicate');
            $(this).parents(".vh_element_item").after(duplicate);
            var element_key = Math.floor(Math.random() * 9999999999);
            vh_duplicate('element', element_key, '.vh_duplicate');
            $('.vh_duplicate').removeClass('vh_duplicate');
            vh_sortable();
            vh_builder_json();

        });
        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	 Remove
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/

        $(document).on('click', '.vh_element_remove', function(e) {
            e.preventDefault();
            var this_element = $(this);
            myConfirm("Are You Sure You Want To Remove This Element?", function(result) {
                if (result) {
                    this_element.parents('.vh_element_item').animate({
                        opacity: 0
                    }, 200, function() {
                        $(this).remove();
                        vh_builder_json();
                    });


                }
            });


        });
        /*************************************************************************************************************************************************************************
        		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        																					 Canel and Close 
        		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        		**************************************************************************************************************************************************************************/
        $(document).on('click', '.vh_options_cancel,.vh_options_close', function() {
            var classes = $(this).parents('.vh_options');
            classes.removeClass('vh_active');
            setTimeout(function() {
                classes.remove();
            }, 200);
        });

        $(document).on("click", '.vh_radio_selected label', function() {
            $(this).parent().parent().find('[checked]').each(function() {
                $(this).removeAttr("checked");
            });
            $(this).find('input').attr("checked", "checked");
        });

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	 Image Rmoeve and Upload
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/


        $(document).on('click', '.vh_image_remove', function(event) {
            var image_rand = Math.floor(Math.random() * 9999999999);
            $(this).parents('.vh_options_setting').find('input').val('').attr('value', '');
            $(this).parent().remove();


        });



        $(document).on('click', '.vh_image_upload', function(event) {
            var data_this = $(this);
            var name = $(this).attr('data-name');
            var imageFrame;
            event.preventDefault();
            var options, attachment;
            var vh_options_upload = data_this.parents('.vh_options_image');
            var $self = $(event.target);
            var $div = $self.closest(vh_options_upload);

            if (imageFrame) {
                imageFrame.open();
                return;
            }
            imageFrame = wp.media({
                title: vh_text.choose,
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: vh_text.uploader_button
                }
            });

            imageFrame.on('select', function() {
                var selection = imageFrame.state().get('selection');
                if (!selection)
                    return;
                selection.each(function(attachment) {
                    console.log(attachment);
                    var id = attachment.attributes.id;

                    if (attachment.attributes.sizes.medium) {
                        var medium_url = attachment.attributes.sizes.medium.url;
                    } else {
                        var medium_url = attachment.attributes.sizes.full.url;
                    }

                    data_this.parent().find('.vh_image_item_medium').remove();

                    data_this.parent().find('.vh_image_item').find('input').val(id);


                    var data = '<div class="vh_image_item_medium"><span class="vh_image_remove"></span><img  src="' + medium_url + '"/></div>';


                    $(data_this).parent().find('.vh_image_item').append(data);
                });
            });

            imageFrame.open();
        });

        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	Checkbox
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        jQuery(document).on("click", '.vh_options [type="checkbox"]', function() {
            if ($(this).is(':checked')) {
                $(this).val(1);

            } else {
                $(this).val('');


            }
        });



        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	Title TABS
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        jQuery(document).on("click", '.vh_title_tabs a', function() {
            $(this).parent().find('.vh_layout_active').removeClass('vh_layout_active');
            $(this).addClass('vh_layout_active');
            var value = $(this).attr('data-id');
            $(this).parents('.vh_icon,.vh_options_middle,.vh_model_middle').find('.vh_layout_group_active').removeClass('vh_layout_group_active');
            $(this).parents('.vh_icon,.vh_options_middle,.vh_model_middle').find('[data-tab="' + value + '"]').addClass('vh_layout_group_active');

        });




        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	Title TABS
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        $(document).on('change keyup mousedown input', '.vh_options_number  input[type="range"]', function(e) {
            var value = $(this).val();
            $(this).parent().find('[type="number"]').val(value).attr('value', value);

        });


        /*************************************************************************************************************************************************************************
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
         																	Title TABS
        --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        **************************************************************************************************************************************************************************/
        $(document).on('change keyup mousedown', '.vh_options_number  input[type="number"]', function(e) {
            var value = $(this).val();
            if (value === 'undefined' || value === null || value === '') {
                value = 0;
            }
            $(this).parent().find('[type="range"]').val(value).attr('value', value);


        });



    });
});