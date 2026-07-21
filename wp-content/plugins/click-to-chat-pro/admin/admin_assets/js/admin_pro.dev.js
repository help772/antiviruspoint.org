(function ($) {

    // ready
    $(function () {

        var ht_ctc_admin_var = (window.ht_ctc_admin_var) ? window.ht_ctc_admin_var : {};

        if (localStorage.getItem('ht_ctc_admin')) {
            ht_ctc_admin = localStorage.getItem('ht_ctc_admin');
            ht_ctc_admin = JSON.parse(ht_ctc_admin);
        }

        /**
         * fallback function - if called before loadin main admin.js file where custom event dispatch
         */
        // get items from ht_ctc_admin
        var ctc_getItem = function (item) {
            if (ht_ctc_admin[item]) {
                return ht_ctc_admin[item];
            } else {
                return false;
            }
        };

        // set items to ht_ctc_admin storage
        var ctc_setItem = function (name, value) {
            ht_ctc_admin[name] = value;
            var newValues = JSON.stringify(ht_ctc_admin);
            localStorage.setItem('ht_ctc_admin', newValues);
        }

        var intl_init = function () { return false };
        var intl_onchange = function () { return false };

        try {
            document.addEventListener("ht_ctc_fn_all", function (e) {
                admin_ctc = e.detail.admin_ctc;
                ctc_getItem = e.detail.ctc_getItem;
                ctc_setItem = e.detail.ctc_setItem;
                intl_init = e.detail.intl_init;
                intl_onchange = e.detail.intl_onchange;
            });
        } catch (e) { }




        // click on agent image button
        $(document).on('click', '.greetings_multi_agent_image', function (e) {

            e.preventDefault();

            var agent = $(this).attr('data-agent');
            var pr = $('.image_' + agent);

            var image_frame;
            if (image_frame) {
                image_frame.open();
            }
            // Define image_frame as wp.media object
            image_frame = wp.media({
                title: 'Select Media',
                multiple: false,
                library: {
                    type: 'image',
                }
            });

            image_frame.on('select', function () {


                var attributes = image_frame.state().get('selection').first();

                // if closed with out selecting image
                if (typeof attributes == 'undefined') return true;

                attributes = image_frame.state().get('selection').first().toJSON();
                image_url = attributes.url;
                image_id = attributes.id;

                pr.find('.ht_ctc_pro_agent_field_agent_image_id').val(image_id);
                pr.find('.ht_ctc_pro_agent_field_agent_image_url').val(image_url);
                pr.find('.multi_agent_preview_image').attr('src', image_url);

                // display image
                pr.find('.multi_agent_preview_fallback_image').hide();
                pr.find('.multi_agent_preview_image').show();

                pr.find('.greetings_multi_agent_remove_image').show();


            });

            image_frame.on('open', function () {
                var selection = image_frame.state().get('selection');
                // select image
                if (typeof pr.find('.ht_ctc_pro_agent_field_agent_image_id').val() !== "undefined") {
                    pr.find('.ht_ctc_pro_agent_field_agent_image_id').val().split(',').forEach(function (id) {
                        var attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                }

            });

            image_frame.open();
        });

        // click on remove image button
        $(document).on('click', '.greetings_multi_agent_remove_image', function (e) {

            e.preventDefault();

            var agent = $(this).attr('data-agent');
            var pr = $('.image_' + agent);

            pr.find('.ht_ctc_pro_agent_field_agent_image_id').val('');
            pr.find('.ht_ctc_pro_agent_field_agent_image_url').val('');
            pr.find('.multi_agent_preview_image').hide();
            pr.find('.multi_agent_preview_fallback_image').show();
            pr.find('.greetings_multi_agent_remove_image').hide();
        });



        software_license();
        random_num();
        url_structure();

        try {
            time_picker();
        } catch (e) { }

        try {
            ctc_j_timepicker_start();
            ctc_j_timepicker_end();
        } catch (e) { }


        // ctc_pro_activate_license
        // ctc_pro_activate - error
        function software_license() {

            $(document).on('click', '#ctc_pro_license_button', function (e) {

                e.preventDefault();

                var keyfield = $('#ctc_pro_license_key');
                messagebox = $('.ctc_pro_license_message');
                btn = $('#ctc_pro_license_button');
                btn_name = btn.attr('name');
                btnval = btn.val();
                getlicense = $('.ctc_get_license');
                nonce = $('#ht_ctc_pro_nonce').val();

                messagebox.hide();

                var key = keyfield.val();

                if ('' == key) {
                    messagebox.html('Please Enter the License key!');
                    messagebox.show(100);
                    return;
                }
                key = key.replace(/[^a-z0-9]/gi, '');


                var action = 'ctc_pro_activate_license';
                if ('ctc_pro_activate_btn' == btn_name) {
                    action = 'ctc_pro_activate_license';
                    btn.val('Activating...');
                } else if ('ctc_pro_deactivate_btn' == btn_name) {
                    action = 'ctc_pro_deactivate_license';
                    btn.val('Deactivating...');
                }

                btn.css('pointer-events', 'none');

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: action,
                        key: key,
                        ht_ctc_pro_nonce: nonce,
                    },
                    type: "POST",
                    success: function (response) {
                        output(response);
                    },
                    error: function () {
                        btn.val(btnval);
                        btn.css('pointer-events', 'auto');
                        messagebox.css('color', 'red');
                        messagebox.html('Error: some thing wrong: please try again or please contact Us');
                        messagebox.show(100);
                    }
                });

                function output(response) {

                    data = response.data;
                    message = response.data.message;

                    if ('Activated' == message) {
                        $('#ctc_pro_activated').show();
                        keyfield.hide();
                        // activated - change btn content things to deactivate
                        // btn.html('Deactivate');
                        btn.val('Deactivate License');
                        btn.attr('name', 'ctc_pro_deactivate_btn');
                        btn.removeClass('ctc_pro_activate_btn').addClass('ctc_pro_deactivate_btn');
                        messagebox.css('color', 'green');
                        getlicense.hide();
                    } else if ('Deactivated' == message) {
                        $('#ctc_pro_activated').hide();
                        keyfield.show();
                        // btn.html('Activate');
                        btn.val('Activate License');
                        btn.attr('name', 'ctc_pro_activate_btn');
                        btn.removeClass('ctc_pro_deactivate_btn').addClass('ctc_pro_activate_btn');
                        messagebox.css('color', 'yellowgreen');
                        getlicense.show(500);
                    } else {
                        // something wrong
                        btn.val(btnval);
                        messagebox.css('color', 'red');
                        messagebox.html(message);
                        messagebox.show(100);
                        getlicense.show(500);
                    }

                    btn.css('pointer-events', 'auto');

                }
            });
        }


        // url structure - custom url..
        function url_structure() {
            // default display
            var url_structure_d = $('.url_structure_d').find(":selected").val();
            if (url_structure_d == 'custom_url') {
                $(".custom_url_desktop").show();
            }

            var url_structure_m = $('.url_structure_m').find(":selected").val();
            if (url_structure_m == 'custom_url') {
                $(".custom_url_mobile").show();
            }

            // on change
            $(".url_structure_d").on("change", function (e) {

                var change_url_structure_d = e.target.value;

                if (change_url_structure_d == 'custom_url') {
                    $(".custom_url_desktop").show(500);
                } else {
                    $(".custom_url_desktop").hide(500);
                }
            });
            $(".url_structure_m").on("change", function (e) {

                var change_url_structure_m = e.target.value;

                if (change_url_structure_m == 'custom_url') {
                    $(".custom_url_mobile").show(500);
                } else {
                    $(".custom_url_mobile").hide(500);
                }
            });
        }

        function random_num() {

            // var number_html = $('.add_number').attr('data-html');
            var number_html = $('.ctc_random_number_snippets .additional-number');

            // Add number
            $('.add_number').click(function () {
                var number_html_clone = number_html.clone();
                $(number_html_clone).find('input').attr('name', `ht_ctc_chat_options[r_nums][]`);
                $(number_html_clone).find('input').attr('data-name', `ht_ctc_chat_options[r_nums][]`);
                $(number_html_clone).find('input').addClass('new_intl_number');
                $('.ht_ctc_numbers').append(number_html_clone);
                new_intl();
            });

            // Remove number
            $('.ht_ctc_numbers').on('click', '.remove_number', function (e) {
                e.preventDefault();
                $(this).closest('.additional-number').remove();
            });

        }



        // md time picker
        function time_picker() {

            function timepicker_start(a = '09:00') {
                $('.timepicker_start').timepicker({
                    twelveHour: false,
                    showClearBtn: true,
                    defaultTime: a
                });
            }
            timepicker_start();

            function timepicker_end(a = '18:00') {
                $('.timepicker_end').timepicker({
                    twelveHour: false,
                    showClearBtn: true,
                    defaultTime: a
                });
            }
            timepicker_end();

            $(".timepicker_start").on("change", function (e) {
                var t = $(this).val();
                timepicker_start(t);
            });

            $(".timepicker_end").on("change", function (e) {
                var t = $(this).val();
                timepicker_end(t);
            });

        }

        // jquery timepicker
        function ctc_j_timepicker_start() {
            $('.ctc_j_timepicker_start').timepicker({
                timeFormat: 'H: mm',
                // startTime: '08:00',
                // dynamic: false,
                // dropdown: true,
                scrollbar: true,
                interval: 60
            });
        }
        function ctc_j_timepicker_end() {
            $('.ctc_j_timepicker_end').timepicker({
                timeFormat: 'H: mm',
                // startTime: '16:00',
                scrollbar: true,
                interval: 60
            });
        }


        function business_hours() {

            // business hours
            var bh = $('.select_bh').find(":selected").val();

            if (bh == 'always') {
                $(".bh_time").hide();
            } else if (bh == 'timebase') {
                $(".bh_time").show();
            }

            $(".select_bh").on("change", function (e) {
                var bh = $('.select_bh').find(":selected").val();
                if (bh == 'always') {
                    $(".bh_time").hide(200);
                } else if (bh == 'timebase') {
                    $(".bh_time").show(400);
                }
            });


            // start, end time
            $(".ctc_day").on("change paste keyup", function (e) {
                var day = $(this).attr('data-day');

                if ($(this).is(":checked")) {
                    $('.ctc_time_' + day).css('visibility', 'visible');
                } else {
                    $('.ctc_time_' + day).css('visibility', 'hidden');
                }
            });


            // offline - if hided, number, call to action .. 
            if ($('.off_hide').is(":checked")) {
                $('.offline_hide').hide();
            }

            $(".off_hide").on("change paste keyup", function (e) {

                if ($(this).is(":checked")) {
                    $('.offline_hide').hide(400);
                } else {
                    $('.offline_hide').show(400);
                }
            });

        }
        business_hours();


        // greetings fields.. form .. 
        function greetings_template() {

            var greetings_template = $('.pr_greetings_template select').find(":selected").val();

            // greetings-form
            if (greetings_template == 'greetings-pro-1') {
                display_greetings_pro_1();
            }

            // multi agent
            if (greetings_template == 'greetings-pro-2') {
                display_greetings_pro_2();
            }

            // on change
            $('.pr_greetings_template select').on("change", function (e) {

                var greetings_template = e.target.value;

                // if not no - then first hide all and again display required fields..
                if (greetings_template == 'greetings-pro-1' || greetings_template == 'greetings-pro-2') {
                    $(" .ctc_greetings_settings").hide();
                }

                // greetings from
                if (greetings_template == 'greetings-pro-1') {
                    var e = document.querySelector('.g_content_collapsible');
                    if (e) {
                        collapse(e);
                    }
                    display_greetings_pro_1();
                }

                if (greetings_template == 'greetings-pro-2') {
                    var e = document.querySelector('.g_content_collapsible');
                    if (e) {
                        collapse(e);
                    }
                    display_greetings_pro_2();
                    // animate agent button..
                    $('.ctc_add_agent_button').fadeOut(500).fadeIn(500).fadeOut(500).fadeIn(500);
                }

                // // on change if not greetings multi agent
                // if ('greetings-pro-2' !== greetings_template) {
                //     // remove multi agent field requierd attribute
                //     $('.g_pro_2_required').removeAttr('required');
                // }

            });

            // optin - show/hide
            function optin() {
                if ($('.is_opt_in').is(':checked')) {
                    $(".pr_opt_in ").show(200);
                } else {
                    $(".pr_opt_in ").hide(200);
                }
            }

            function display_greetings_pro_1() {

                // as of now..  greetings-1 all settings are required..
                $('.ctc_greetings_settings.ctc_g_1').show();

                $('.pr_ht_ctc_greetings_pro_1').show();
                $('.pr_ht_ctc_greetings_settings').show();
                optin();
            }

            function display_greetings_pro_2() {

                // as of now..  greetings-1 all settings are required..
                $('.ctc_greetings_settings.ctc_g_1').show();

                $('.pr_ht_ctc_greetings_pro_2').show();
                $('.pr_ht_ctc_greetings_settings').show();
                optin();
            }






        }
        if (document.querySelector('.pr_greetings_template')) {
            greetings_template();
        }


        /**
         * greeting form: 
         *  form fields, ..
         */
        function form() {


            // Add field -  ctc_add_field_button
            var field_snippet = $('.ctc_form_snippets .ht_ctc_pro_field');

            $('.ctc_add_field_button').on('click', function (e) {
                var field = field_snippet.clone();

                var field_count = $('.ht_ctc_pro_form_field_count').val();

                // field_${field_count}

                // filed number for reference
                $(field).find('.ht_ctc_pro_form_field_number').attr('name', `ht_ctc_greetings_pro_1[fields][]`);
                $(field).find('.ht_ctc_pro_form_field_number').val('field_' + field_count);

                // field type, 
                $(field).find('.ht_ctc_pro_form_select_field_type').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][type]`);
                $(field).find('.ht_ctc_pro_form_select_field_name').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][name]`);
                $(field).find('.ht_ctc_pro_form_select_field_placeholder').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][placeholder]`);
                $(field).find('.ht_ctc_pro_form_select_field_required').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][required]`);
                $(field).find('.ht_ctc_pro_form_select_field_add_to_prefilled').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][add_to_prefilled]`);
                $(field).find('.ht_ctc_pro_form_select_field_selectvalues').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][selectvalues]`);
                $(field).find('.ht_ctc_pro_form_select_field_hiddenvalue').attr('name', `ht_ctc_greetings_pro_1[field_${field_count}][hiddenvalue]`);

                $('.ctc_new_fields').append(field);
                $('select').formSelect();

                field_count++;
                $('.ht_ctc_pro_form_field_count').val(field_count);
            });


            // Remove field
            $('.ht_ctc_pro_form').on('click', '.ht_ctc_pro_form_remove_field_link', function (e) {
                console.log('remove field');
                e.preventDefault();
                $(this).closest('.ht_ctc_pro_field').hide(400, function (e) {
                    $(this).remove();
                    is_intltelinput_type();
                });
            });

            // form fields
            $('.ht_ctc_pro_field').each(function () {
                var field_type = $(this).find('.ht_ctc_pro_form_select_field_type').val();
                // if ('checkbox' == field_type) {
                //     $(this).find('.g_form_field_type_placeholder').hide();
                //     $(this).find('.g_form_element_checbox').show();
                // }
                // if ( 'number' == field_type ) {
                //     $(this).find('.g_form_element_number').show();
                // }
                display_form_elements(field_type, this);

            });

            // append .intltelinput_settings details.. to the end of the form settings
            $('.pr_g_p_1_message_box_bg_color').after($('.intltelinput_settings'));

            // which filed to display for each field type
            $(".ht_ctc_pro_field, .ctc_new_fields").on("change", '.ht_ctc_pro_form_select_field_type', function (e) {

                var field_type = $(this).closest('.ht_ctc_pro_field').find('.ht_ctc_pro_form_select_field_type').val();
                var p = $(this).closest('.ht_ctc_pro_field');

                // only on change.. (not on load) thats why added here..
                if ('hidden' == field_type) {
                    $(p).find('.g_form_field_type_add_to_prefilled input').prop('checked', false);
                }

                display_form_elements(field_type, p);

                is_intltelinput_type();

            });

            function display_form_elements(field_type, e) {

                // content specific elements.. inital hide. and display based on field type using respective class
                $(e).find('.g_form_element').hide(100);

                // select values. text area. show only for select field type
                $(e).find('.g_form_field_type_select').hide(100);

                // hidden field. show only for hidden field type
                $(e).find('.g_form_field_type_hidden').hide(100);

                // placeholder: show all, expect checkbox, hidden field type
                $(e).find('.g_form_field_type_placeholder').show(100);

                // required: show all, expect hidden field type
                $(e).find('.g_form_field_type_required').show(100);


                if ('checkbox' == field_type) {
                    $(e).find('.g_form_element_checbox').show(100);
                    // placeholder not required for checkbox
                    $(e).find('.g_form_field_type_placeholder').hide(100);
                }

                if ('select' == field_type) {
                    // select values. text area
                    $(e).find('.g_form_field_type_select').show(100);
                }

                // if number. 
                if ('number' == field_type) {
                    $(e).find('.g_form_element_number').show(100);
                }

                // if hidden.
                if ('hidden' == field_type) {
                    $(e).find('.g_form_field_type_hidden').show(100);
                    $(e).find('.g_form_element_hidden').show(100);
                    $(e).find('.g_form_field_type_placeholder').hide(100);
                    $(e).find('.g_form_field_type_required').hide(100);
                }

            }

            /**
             * on change - input filed type
             * check if any input field type is number. if number. then set hidden filed .ctc_is_load_intltelinput to 'y' or 'n'
             */
            function is_intltelinput_type() {
                // if intl_number
                var intltelinput_load = 'n';

                // check if any input field type is number
                $('.ht_ctc_pro_form_select_field_type').each(function () {
                    if ('number' == $(this).val()) {
                        console.log($(this).val());
                        console.log($(this)[0]);
                        intltelinput_load = 'y';
                    }
                });

                $('.ctc_is_load_intltelinput').val(intltelinput_load);

                // if intltelinput_load is 'y' then display .intltelinput_settings else hide
                if ('y' == intltelinput_load) {
                    $('.intltelinput_settings').show(400);
                } else {
                    $('.intltelinput_settings').hide(400);
                }
            }
            is_intltelinput_type();


        }
        form();


        /**
         * multi agent
         */
        function multi_agent() {


            // Add field -  ctc_add_field_button
            var agent_snippet = $('.ctc_agent_snippets .ht_ctc_pro_agent');

            $('.ctc_add_agent_button').on('click', function (e) {
                var agent = agent_snippet.clone();
                var agent_count = $('.ht_ctc_pro_agent_count').val();

                // field_${agent_count}

                // filed number for reference
                $(agent).find('.ht_ctc_pro_agent_field_ref_number').attr('name', `ht_ctc_greetings_pro_2[agents][]`);
                $(agent).find('.ht_ctc_pro_agent_field_ref_number').val('agent_' + agent_count);

                // field type, 
                $(agent).find('.ht_ctc_pro_agent_field_enable').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][enable]`);
                $(agent).find('.ht_ctc_pro_agent_field_ref_name').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][ref_name]`);

                $(agent).find('.ht_ctc_pro_agent_field_number').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][number]`);
                $(agent).find('.ht_ctc_pro_agent_field_number').attr('data-name', `ht_ctc_greetings_pro_2[agent_${agent_count}][number]`);
                $(agent).find('.ht_ctc_pro_agent_field_number').addClass('new_intl_number');
                // $(agent).find('.ht_ctc_pro_agent_field_number').attr('required', true).addClass('g_pro_2_required');

                $(agent).find('.ht_ctc_pro_agent_field_title').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][title]`);
                $(agent).find('.ht_ctc_pro_agent_field_description').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][description]`);
                $(agent).find('.ht_ctc_pro_agent_field_pre_filled').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][pre_filled]`);
                $(agent).find('.unique_id span').html(`agent_${agent_count}`);

                $(agent).find('.ht_ctc_pro_agent_field_agent_image_id').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][agent_image_id]`);
                $(agent).find('.ht_ctc_pro_agent_field_agent_image_url').attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][agent_image_url]`);


                var a = $(agent).find('.ht_ctc_pro_agent_schedule');

                a.each(function (i, o) {
                    var data_key = $(o).attr('data-key');
                    $(o).attr('name', `ht_ctc_greetings_pro_2[agent_${agent_count}][${data_key}]`);
                });


                // add attributes
                $(agent).find('.multi_schedule').attr('data-agent', `agent_${agent_count}`);

                var b = $(agent).find('.multi_schedule');
                b.each(function (i, o) {
                    var data_key = $(o).find('.day_checkbox').attr('data-key');
                    $(o).attr('data-day', data_key);
                });

                $(agent).find('.pr_agent_image').addClass(`image_agent_${agent_count}`)
                $(agent).find('.greetings_multi_agent_image').attr('data-agent', `agent_${agent_count}`);
                $(agent).find('.greetings_multi_agent_remove_image').attr('data-agent', `agent_${agent_count}`);

                $('.ctc_new_agents').append(agent);

                // $('select').formSelect();
                $('.collapsible').collapsible();
                ctc_j_timepicker_start();
                ctc_j_timepicker_end();

                agent_count++;
                $('.ht_ctc_pro_agent_count').val(agent_count);
                on_change();
                new_intl();
            });


            // Remove field
            $('.ht_ctc_pro_agent').on('click', '.ht_ctc_pro_agent_remove_agent_link', function (e) {
                e.preventDefault();
                $(this).closest('.ht_ctc_pro_agent').hide(400, function (e) {
                    $(this).remove();
                });
            });

            // display: color settings - init
            if (document.querySelector(".pr_g_p_2_inital_display")) {
                var p2_agent_init = $(".pr_g_p_2_inital_display select").val();

                if ('content' == p2_agent_init) {
                    $('.pr_g_p_2_main_bg_color').removeClass('ctc_init_display_none');
                    $('.pr_g_p_2_message_box_bg_color').removeClass('ctc_init_display_none');
                }
            }


            /**
             * on change - dont auto loop it at anywhere..
             * 
             * call by itself
             * and have to assign again based on settings changes.. so on change call again
             * 
             */
            function on_change() {
                // agent header
                // agent name
                $(".ht_ctc_pro_agent_field_title").on("input change paste keyup", function (e) {
                    var v = $(this).val();
                    if ('' == v) {
                        // blank
                        $(this).closest('.ht_ctc_pro_agent').find('.header_agent_name').html('Agent');
                    } else {
                        $(this).closest('.ht_ctc_pro_agent').find('.header_agent_name').html(v);
                    }
                });
                // agent ref name
                $(".ref_name").on("input change paste keyup", function (e) {
                    var v = $(this).val();
                    if ('' == v) {
                        $(this).closest('.ht_ctc_pro_agent').find('.header_ref_name').html('');
                    } else {
                        $(this).closest('.ht_ctc_pro_agent').find('.header_ref_name').html(': ' + v + '');
                    }
                });

                // enable
                $('.ht_ctc_pro_agent_field_enable').on("change", function (e) {
                    if ($(this).is(":checked")) {
                        $(this).closest('.ht_ctc_pro_agent').find('.collapsible-header').css({
                            'opacity': '1'
                        });
                    } else {
                        $(this).closest('.ht_ctc_pro_agent').find('.collapsible-header').css({
                            'opacity': '0.5'
                        });
                    }

                });

                // multi timings
                // var multi_timings_val = $('.multi_timings input:checked').val();
                $('.multi_timings input').on("change", function (e) {
                    var v = $(this).val();
                    if ('set' == v) {
                        $(this).closest('.ht_ctc_pro_agent').find('.multi_schedule').show();
                        $(this).closest('.ht_ctc_pro_agent').find('.current_site_time').show();
                    } else {
                        $(this).closest('.ht_ctc_pro_agent').find('.multi_schedule').hide();
                        $(this).closest('.ht_ctc_pro_agent').find('.current_site_time').hide();
                    }
                });

                // close add time 
                $('.ht_ctc_pro_agent').on('click', '.ht_ctc_pro_agent_remove_time_set_link', function (e) {
                    e.preventDefault();
                    $(this).closest('.add_time').hide(400, function (e) {
                        $(this).remove();
                    });

                    // if no time set, show online_24_content
                    // 0 is perfect. but take time to update. so better to use length <=1
                    if ($(this).closest('.add_time_here').find('.add_time').length <= 1) {
                        $(this).closest('.add_time_here').find('.online_24_content').show();
                    }

                });

                // day_checkbox
                $(".day_checkbox").on("change", function (e) {
                    if ($(this).is(":checked")) {
                        $(this).closest('.multi_schedule').find('.add_time_here').show(250);
                    } else {
                        $(this).closest('.multi_schedule').find('.add_time_here').hide(250);
                    }
                });

                /**
                 * Time sets..
                 */
                var add_time_snippet = $('.ctc_agent_snippets .add_time');

                // add time
                $('.click_to_add_time').on('click', function (e) {
                    var add_time = add_time_snippet.clone();

                    var agent = $(this).closest('.multi_schedule').attr('data-agent');
                    var day = $(this).closest('.multi_schedule').attr('data-day');
                    var set_count = $(this).closest('.multi_schedule').find('.add_time').length + 1;

                    $(add_time).find('.add_time_st').attr('name', `ht_ctc_greetings_pro_2[${agent}][${day}_times][set_${set_count}][st]`);
                    $(add_time).find('.add_time_et').attr('name', `ht_ctc_greetings_pro_2[${agent}][${day}_times][set_${set_count}][et]`);

                    // hide online_24_content
                    $(this).closest('.today_schedule').find('.online_24_content').hide();
                    // add_time_here
                    $(this).closest('.add_time_here').find('.add_time_set').append(add_time);

                    ctc_j_timepicker_start();
                    ctc_j_timepicker_end();
                });


                // on change - initial display - show/hide color pickers. -  
                $(".pr_g_p_2_inital_display select").on("change", function (e) {
                    var val = $(this).val();
                    var p2_agent_init = $(this).closest('.pr_ht_ctc_greetings_pro_2');
                    if ('agents' == val) {
                        $(p2_agent_init).find('.pr_g_p_2_main_bg_color').hide(100);
                        $(p2_agent_init).find('.pr_g_p_2_message_box_bg_color').hide(100);
                    } else {
                        $(p2_agent_init).find('.pr_g_p_2_main_bg_color').show(100);
                        $(p2_agent_init).find('.pr_g_p_2_message_box_bg_color').show(100);
                    }
                });

            }
            on_change();

        }
        multi_agent();


        // // expand collapsible if required filed is not filled
        // function if_required_field() {
        //     $(".click-to-chat_page_click-to-chat-greetings input[type='submit']").on("click", function (event) {
        //         try {
        //             $("input[required='required']").each(function () {
        //                 // if the value is empty, that means that is invalid
        //                 if ($(this).val() == "") {
        //                     var item = $(this).closest(".collapsible");
        //                     console.log('required field is empty');
        //                     try {
        //                         // $(item).find('li').addClass('active');
        //                         $(item).collapsible('open');
        //                     } catch (e) { }
        //                 }
        //             });
        //         } catch (e) {}
        //     });
        // }
        // if_required_field();



        /**
         * for random numbers..
         * calling intl_init, intl_onchange
         */
        function new_intl() {
            if (document.querySelector(".new_intl_number") && typeof intlTelInput !== 'undefined') {
                $('.new_intl_number').each(function () {
                    intl_init(this);

                    $(this).removeClass('new_intl_number');
                    $(this).addClass('intl_number');
                });
                intl_onchange();
            }
        }

        function collapse(e) {
            try {
                var instance = M.Collapsible.getInstance(e);
                instance.close();
            } catch (e) { }
        }


        // analytics
        function analytics() {

            // if #ga_ads is checked then display .ctc_g_ads_values
            if ($('#ga_ads').is(':checked')) {
                console.log('ga_ads is checked');
                $(".ctc_g_ads_values").show();
            }

            // event name, params - display only if ga is enabled.
            $("#ga_ads").on("change", function (e) {
                if ($('#ga_ads').is(':checked')) {
                    console.log('ga_ads: is checked');
                    $(".ctc_g_ads_values").show(400);
                } else {
                    console.log('ga_ads: is not checked');
                    $(".ctc_g_ads_values").hide(200);
                }
            });

            var g_ads_param_snippet = $('.ctc_g_ads_param_snippets .ht_ctc_g_ads_add_param');
            console.log(g_ads_param_snippet);

            // add value
            $(document).on('click', '.ctc_add_g_ads_param_button', function () {

                console.log('on click: add g an param button');
                console.log(g_ads_param_snippet);

                var g_ads_param_order = $('.g_ads_param_order').val();
                g_ads_param_order = parseInt(g_ads_param_order);


                var g_ads_param_clone = g_ads_param_snippet.clone();
                console.log(g_ads_param_clone);

                // filed number for reference
                $(g_ads_param_clone).find('.g_ads_param_order_ref_number').attr('name', `ht_ctc_othersettings[g_ads_params][]`);
                $(g_ads_param_clone).find('.g_ads_param_order_ref_number').val('g_ads_param_' + g_ads_param_order);

                $(g_ads_param_clone).find('.ht_ctc_g_ads_add_param_key').attr('name', `ht_ctc_othersettings[g_ads_param_${g_ads_param_order}][key]`);
                $(g_ads_param_clone).find('.ht_ctc_g_ads_add_param_value').attr('name', `ht_ctc_othersettings[g_ads_param_${g_ads_param_order}][value]`);


                console.log($('.ctc_new_g_ads_param'));

                $('.ctc_new_g_ads_param').append(g_ads_param_clone);


                g_ads_param_order++;
                $('.g_ads_param_order').val(g_ads_param_order);
            });
        }
        analytics();


        // // updates
        // function plugin_updates() {

        //     console.log('updates..');

        //     $.ajax({
        //         url: ajaxurl,
        //         data: {
        //             action: 'ctc_pro_updates',
        //             // key: key,
        //             // ht_ctc_pro_nonce: nonce,
        //         },
        //         type: "POST",
        //         success: function (response) {
        //             console.log(response);
        //             // console.log(response.data);
        //             // console.log(response['data']);
        //             // console.log(response['data']['new_version']);
        //         },
        //         error: function (response) {
        //             console.log(response);
        //         }
        //     });


        // }
        // plugin_updates();


        /**
         * display based on country
         */
        function ctc_display_country() {


            var display_countries = $('.select_display_countries').find(":selected").val();
            if (display_countries == 'all') {
                $(".ctc_display_countries_base").hide();
            } else if (display_countries == 'select') {
                $(".ctc_display_countries_base").show();
            }

            // on change
            $(".select_display_countries").on("change", function (e) {

                var display_countries = $('.select_display_countries').find(":selected").val();
                console.log('select_display_countries change: ' + display_countries);

                if (display_countries == 'all') {
                    $(".ctc_display_countries_base").hide(200);
                } else if (display_countries == 'selected') {
                    $(".ctc_display_countries_base").show(400);
                }
            });



            try {
                // if SumoSelect is a function..
                if (typeof $.fn.SumoSelect == 'function') {

                    $('.display_countries_list').SumoSelect({
                        selectAll: true,
                        search: true,
                        // searchText: 'Search',
                        // placeholder: 'Select Country',
                        // csvDispCount: 2,
                        // captionFormat: 'Country',
                        // captionFormatAllSelected: 'Country',
                        // floatWidth: 400,
                        // forceCustomRendering: false,
                        // nativeOnDevice: ['Android', 'BlackBerry', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile', 'Silk'],
                    });

                }
            } catch (e) { }


        }
        ctc_display_country();


        // save form data in google sheet
        



    });


})(jQuery);