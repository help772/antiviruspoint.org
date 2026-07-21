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
     $.fn.vh_coloris = function() { 	
	
		rang_Coloris({el: '.vh-coloris',swatches: [
			'rgba(255,255,255,0.0)','#000000','#ffffff','#444444','#888888','#bbbbbb','#FF1493','#FF00FF','#9400D3','#FA8072','#FF0000','#8B0000','#FF8C00','#FF4500','#ffd800','#FFFF00','#F0E68C','#ADFF2F','#32CD32','#00FA9A','#20B2AA','#00FFFF','#00CED1','#00BFFF','#6495ED','#0000FF','#191970','#D2691E','#A52A2A','#800000','#708090','rgba(0,0,0,0.5)','rgba(255,255,255,0.5)'
    ]
			});
			
  				 
				
	 }; 
 
    /*************************************************************************************************************************************************************************
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
     																	Fold Hide
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    **************************************************************************************************************************************************************************/
    function vh_id_image(classes, image_id) {
        classes.after('<div class="vh_loading"></div>');
         $.ajax({
            type: 'POST',
            url: vh_builder_js.ajaxurl,
            data: {
                action: 'vh_id_image',
                id: image_id,
				_wpnonce: vh_settings.nonce,
            },
            success: function(data) {
                if (data.length) {
                    classes.after(data);
                    var image_rand = Math.floor(Math.random() * 9999999999);
                    classes.parents('.vh_options_item').addClass(image_rand).find('.vh_loading').remove();
                    setTimeout(function() {
                        $('.' + image_rand).find('.vh_loading').remove();
                    }, 200);
                }
            }
        });
    }

    function vh_fold_hide(items) {

        items.find('.vh_options_fold').each(function() {
            var show;
            $(this).find('.vh_options_fold_item').each(function() {
                var data_name = $(this).attr('data-name');
                var data_value = $(this).attr('data-value');
                var data_active = $('.vh_id_' + data_name).attr('data-active');
                if (data_active === 'show') {
                    var type = $('.vh_options  [name="' + data_name + '"]').attr('type');
                    if (type == 'radio') {
                        var checked = $('.vh_options [name="' + data_name + '"][value="' + data_value + '"]').attr('checked');
                        if (checked == 'checked') {
                            show = 'checked';
                        }
                    } else {
                        var val = $('.vh_options [name="' + data_name + '"]').val();
                        if (data_value == val) {

                            show = 'checked';
                        }

                    }

                }
            });
            if (show == 'checked') {
                $(this).parent().attr('data-active', 'show');
            } else {
                $(this).parent().attr('data-active', 'hide');
            }
        });


        items.find('.vh_options_fold_item').each(function() {
            var data_name = $(this).attr('data-name');
            var actives = $('.vh_id_+' + data_name).attr('data-active');
            if (actives === 'hide') {
                $(this).parent().parent().attr('data-active', 'hide');
            }
        });

        $(document).on("click", '.vh_options_select .vh_options_setting, .vh_options_checkbox .vh_options_setting,.vh_options_radio .vh_options_setting,.vh_options_radio_image .vh_options_setting', function() {
            $(this).parents('.vh_options_warp').find('.vh_options_fold').each(function() {
                var show;
                $(this).find('.vh_options_fold_item').each(function() {
                    var data_name = $(this).attr('data-name');
                    var data_value = $(this).attr('data-value');
                    var data_active = $('.vh_id_' + data_name).attr('data-active');
                    if (data_active === 'show') {
                        var type = $('.vh_options  [name="' + data_name + '"]').attr('type');
                        if (type == 'radio') {
                            var checked = $('.vh_options [name="' + data_name + '"][value="' + data_value + '"]').attr('checked');
                            if (checked == 'checked') {
                                show = 'checked';
                            }
                        } else {
                            var val = $('.vh_options [name="' + data_name + '"]').val();
                            if (data_value == val) {
                                show = 'checked';
                            }

                        }

                    }
                });
                if (show == 'checked') {
                    $(this).parent().attr('data-active', 'show');
                } else {
                    $(this).parent().attr('data-active', 'hide');
                }
            });


            items.find('.vh_options_fold_item').each(function() {
                var data_name = $(this).attr('data-name');
                var actives = $('.vh_id_+' + data_name).attr('data-active');
                if (actives === 'hide') {
                    $(this).parent().parent().attr('data-active', 'hide');
                }
            });
        });


        $(document).on("click", '.vh_title_tabs a', function() {
            var id = $(this).attr('data-id');
            $('.vh_options_warp[data-tab="' + id + '"]').find('.vh_options_fold').each(function() {
                var show;
                $(this).find('.vh_options_fold_item').each(function() {
                    var data_name = $(this).attr('data-name');
                    var data_value = $(this).attr('data-value');
                    var data_active = $('.vh_id_' + data_name).attr('data-active');
                    if (data_active === 'show') {
                        var type = $('.vh_options  [name="' + data_name + '"]').attr('type');

                        if (type == 'radio') {
                            var checked = $('.vh_options [name="' + data_name + '"][value="' + data_value + '"]').attr('checked');
                            if (checked == 'checked') {
                                show = 'checked';
                            }
                        } else {
                            var val = $('.vh_options [name="' + data_name + '"]').val();
                            if (data_value == val) {
                                show = 'checked';
                            }

                        }
                    }
                });
                if (show == 'checked') {
                    $(this).parent().attr('data-active', 'show');
                } else {
                    $(this).parent().attr('data-active', 'hide');
                }
            });


            $('.vh_options_warp[data-tab="' + id + '"]').find('.vh_options_fold_item').each(function() {
                var data_name = $(this).attr('data-name');
                var actives = $('.vh_id_+' + data_name).attr('data-active');
                if (actives === 'hide') {
                    $(this).parent().parent().attr('data-active', 'hide');
                }
            });
        });
        setTimeout(function() {

            $('.vh_title_tabs a:first-child').trigger("click");
        }, 10);

    }
    /*************************************************************************************************************************************************************************
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    Tabs
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    **************************************************************************************************************************************************************************/
    function vh_has_tabs(vh_options_header) {
        var tab = {};

        $.each(vh_options_header, function(index, header) {
            $.each(header, function(i, option) {
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
        });

        return tab;
    }
    /*************************************************************************************************************************************************************************
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
																					 Open Options Tabs
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		**************************************************************************************************************************************************************************/
    function vh_options_tabs(vh_options_header) {
        var html = '';

        $.each(vh_options_header, function(index, header_option) {
            var array_tab = vh_has_tabs(vh_options_header);
            var count_tab = 0;

            html = '<div class="vh_title_tabs">';

            $.each(array_tab, function(key, tabs) {
                count_tab++;
                var tab_active = count_tab == 1 ? 'vh_layout_active' : '';
                html += '<a class="vh_tab_' + key + ' ' + tab_active + '" data-id="' + key + '">' + tabs + '</a>';
            });

            html += '</div>';
        });
        return html;
    }


    /*************************************************************************************************************************************************************************
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
																					   Options
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		**************************************************************************************************************************************************************************/
    var defaults = {


    };
    $.fn.vh_options = function(options) {
        var settings = $.extend(true, {}, defaults, options);
        /*************************************************************************************************************************************************************************
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
																					 Open Options
		--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		**************************************************************************************************************************************************************************/


        var html = '';



        var vh_options_header = settings.option;
        var rand = Math.floor(Math.random() * 9999999999);

        html += '<form id="vh_options_' + rand + '" class="vh_options   vh_deactive" data-key="' + isset(settings.key) + '"  data-id="' + isset(settings.id) + '"   data-row="' + isset(settings.row) + '"  >';
        html += '<div class="vh_options_middle" >';

        //Title 
        html += '<div class="vh_options_title">';
        html += '<h3>' + settings.name + '</h3>';
        html += '<i class="vh_options_close"></i>';

        if (settings.tabs) {
            html += settings.tabs.call(this, classes, settings);
        }


        html += '</div>';

        html += '<ul class="vh_options_scroll">';
        html += '<div class="vh_options_content">';

        if (settings.content) {
            html += settings.content.call(this, classes, settings);
        }
        html += '</div>';
        html += '</ul>';

        html += '<div class="vh_options_bottom">';

        if (settings.button) {
            html += settings.button.call(this, classes, settings);
        }


        html += '</div>';



        html += '</div>';
        html += '</form>';
        $('.vh_builder').append(html);


        var classes = $(document).find('#vh_options_' + rand);
        classes.addClass('vh_active ');
        classes.removeClass('vh_deactive');

	 $('body').vh_coloris();

 
        vh_fold_hide(classes);


        if (settings.callback) {
            settings.callback.call(this, classes, settings);
        }



        classes.find('.vh_options_image').find('input').each(function(index, element) {
            var image_val = $(this).val();

            if (image_val) {
                vh_id_image($(this), image_val);
            }
        });




    };



})(jQuery);