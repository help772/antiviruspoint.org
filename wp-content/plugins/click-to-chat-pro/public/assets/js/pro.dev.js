// Click to Chat - PRO
(function ($) {
    // ready
    $(function () {

        var multi = '';
        // yes to check for next step. if no then dont display chat. 
        var is_chat_display = '';

        var url = window.location.href;
        var post_title = (typeof document.title !== "undefined") ? document.title : '';
        var for_get_r_sqx = 0;


        // maybe later change things..  localization.. etc..
        var day_1 = 'Monday';
        var day_2 = 'Tuesday';
        var day_3 = 'Wednesday';
        var day_4 = 'Thursday';
        var day_5 = 'Friday';
        var day_6 = 'Saturday';
        var day_7 = 'Sunday';

        var ht_ctc_storage = {};

        function getStorageData() {
            console.log('pro.js - getStorageData');
            console.log(ht_ctc_storage);
            if (localStorage.getItem('ht_ctc_storage')) {
                ht_ctc_storage = localStorage.getItem('ht_ctc_storage');
                ht_ctc_storage = JSON.parse(ht_ctc_storage);
                console.log(ht_ctc_storage);
            }
        }
        getStorageData();

        // get items from ht_ctc_storage
        function ctc_getItem(item) {
            console.log('ctc_getItem');
            return (ht_ctc_storage[item]) ? ht_ctc_storage[item] : false;
        }

        // set items to ht_ctc_storage storage
        function ctc_setItem(name, value) {
            console.log('ctc_setItem: name: ' + name + ' value: ' + value);
            console.log(ht_ctc_storage);
            // required if multiple sources/plugins are setting values..
            getStorageData();
            console.log(ht_ctc_storage);
            ht_ctc_storage[name] = value;
            console.log(ht_ctc_storage);
            var newValues = JSON.stringify(ht_ctc_storage);
            localStorage.setItem('ht_ctc_storage', newValues);
        }

        var ctc = '';
        variable_ctc();

        var ctc_values = {};
        variable_ctc_values();

        document.dispatchEvent(
            new CustomEvent("ht_ctc_pro_init", { detail: { ht_ctc_storage, ctc_setItem, ctc_getItem } })
        );

        /**
         * get ht_ctc_chat_var values
         */
        function variable_ctc() {
            if (typeof ht_ctc_chat_var !== "undefined") {
                ctc = ht_ctc_chat_var;
            } else {
                console.log('ht_ctc_chat_var not defined');
                try {
                    if (document.querySelector('.ht_ctc_chat_data')) {
                        var settings = $('.ht_ctc_chat_data').attr('data-settings');
                        ctc = JSON.parse(settings);
                    }
                } catch (e) {
                    ctc = {};
                }
                // window.ht_ctc_chat_var = ctc;        
            }
        }


        /**
         * get ht_ctc_variables values
         */
        function variable_ctc_values() {
            console.log('variable_ctc_values');

            if (typeof ht_ctc_variables !== "undefined") {
                ctc_values = ht_ctc_variables;
            } else {
                console.log('ht_ctc_variables not defined');
                ctc_values = {};
                // main plugin have fallback values.. so might be better to call after.. instead of adding fallback values again here.. one more attempt after 2 seconds..
                setTimeout(() => {
                    ctc_values = (typeof ht_ctc_variables !== "undefined") ? ht_ctc_variables : {};
                }, 2000);
            }
            console.log(ctc_values);
        }



        /**
         * multi agents.. 
         * based on time settings
         * next working hour
         * 
         */
        function multi_agent() {
            console.log('multi_agent()');
            var agents = $('.ht_ctc_multi_agent');
            console.log(agents);
            console.log(typeof agents);

            var multi_main = get_var('ht_ctc_multi_agent_main');
            console.log(multi_main);

            var is_time_set = 'yes';

            agents.each(function (i, element) {
                console.log(`i: ${i}`);
                console.log(element);

                var key = $(element).attr('data-key');
                console.log(key);

                var multi = get_var(key);
                console.log(multi);

                var t = time_on_wordpress();
                // console.log(t);
                var today = t.getDay()
                if ('0' == today) {
                    today = 7;
                }
                console.log(today);

                // week loop
                var week = [];
                for (var i = today; i <= 7; i++) {
                    week.push(i);
                }
                // console.log(week);
                if (7 !== week.length) {
                    var add_next_week_days = 7 - week.length;
                    for (var i = 1; i <= add_next_week_days; i++) {
                        week.push(i);
                    }
                }
                // console.log(week);

                var is_online = 'yes';

                if (multi.timings && 'always' == multi.timings) {
                    is_online = 'yes';
                    console.log('always');
                } else if (multi.timings && 'set' == multi.timings) {
                    console.log('set');

                    if (multi.time_sets) {
                        is_online = 'no';
                        var time_sets = multi.time_sets;
                        console.log(time_sets);

                        var current_minute = get_current_minute();
                        var stop_loop = 1;

                        time_sets.forEach(set => {
                            if ((set['stm'] <= current_minute) && (current_minute <= set['etm']) && 1 == stop_loop) {
                                // once in the loop is set to online - no need to check other sets
                                is_online = 'yes';

                                console.log(set['stm']);
                                console.log(current_minute);
                                console.log(set['etm']);
                                console.log(is_online);

                                stop_loop++;
                            }
                        });

                    } else {
                        // offline
                        console.log('time set not added');
                        is_online = 'no';
                        is_time_set = 'no';
                    }

                }


                // agent online / offline..
                if ('no' == is_online) {
                    console.log('is_online: no (offline)');
                    if (multi_main.agent_offline) {
                        if ('chat' == multi_main.agent_offline) {
                            console.log('offline - chat');
                            // add as offline and add next available time
                            offline('chat');
                        } else if ('nochat' == multi_main.agent_offline) {
                            console.log('offline - nochat');
                            // disable agent. add offline (display agent, next available time)
                            offline('nochat');
                        } else {
                            // hide
                            console.log('offline - hide');
                            element.remove();
                        }
                    }
                } else {
                    console.log('is_online: yes');
                }

                /**
                 * add 'next available time' to ctc_g_tags and display flex
                 * change opacity img 0.7 text 0.8
                 * 
                 * @param {chat or no chat} is_chat 
                 */
                function offline(is_chat) {
                    console.log(element);
                    console.log(is_chat);
                    console.log('function offline');
                    var e = $(element);
                    console.log(e);

                    $(element).css({
                        'order': '1'
                    });

                    // e.find('.ctc_g_agent_image img').css({
                    //     'opacity': '0.5',
                    //     // 'border': '1px solid #dddddd'
                    // });
                    // e.find('.ctc_g_agent_content').css({
                    //     'opacity': '0.5'
                    // });

                    e.find('.g_multi_box').css({
                        'opacity': '0.5'
                    });

                    e.find('.ctc_g_agent_tags').css({
                        // 'color': '#c7c7c7'
                        'color': 'unset'
                    });

                    if ('nochat' == is_chat) {
                        $(element).css({
                            'pointer-events': 'none'
                        });
                    }

                    var next_hour = nextWorkingHour();
                    console.log('--------------');
                    console.log(next_hour);
                    console.log('--------------');

                    if ('no' !== is_time_set) {
                        console.log('append next time');
                        e.find('.ctc_g_agent_tags .ctc_agent_next_time').append(next_hour);

                        e.find('.ctc_g_agent_tags .ctc_agent_next_time').css({
                            'display': 'flex'
                        });
                    }


                }

                function nextWorkingHour() {

                    var next_online_time = '';

                    if (multi.time_sets) {
                        console.log(multi);
                        var current_minute = get_current_minute();
                        var t_r = multi.time_sets;
                        console.log(current_minute);
                        console.log(t_r);
                        console.log(typeof t_r);

                        var next_online = '';
                        var stop_loop = 1;

                        t_r.forEach(entry => {
                            console.log(entry);
                            console.log(entry['stm']);
                            console.log(entry['etm']);

                            // current_minute = 10070;
                            if (current_minute <= entry['stm'] && 1 == stop_loop) {
                                next_online = entry['stm'];
                                console.log(next_online);
                                stop_loop++;
                            }
                        });

                        if ('' == next_online) {
                            console.log('next online is in next week');
                            var current_week_minutes_left = 10080 - current_minute;
                            console.log(current_week_minutes_left);
                            next_week_start_minute = t_r[0]['stm'];
                            console.log(next_week_start_minute);
                            time_between = current_week_minutes_left + next_week_start_minute;
                            console.log(time_between);
                        } else {
                            console.log(next_online);
                            console.log(current_minute);
                            var time_between = next_online - current_minute;
                            console.log(time_between);
                        }

                        next_online_time = time_between;

                        console.log(next_online_time);

                        // if less than 60 then add in minutes..
                        if (next_online_time < 60) {
                            console.log('minutes: ' + next_online_time);
                            next_online_time = Math.round(next_online_time);
                            console.log('minutes: ' + next_online_time);
                            var suffix = (next_online_time < 2) ? multi_main.ctc_minute : multi_main.ctc_minutes;
                            next_online_time = next_online_time + ' ' + suffix;
                            console.log(next_online_time);
                        } else {
                            // convert in to hours - and add in hours/days
                            next_online_time = next_online_time / 60;
                            console.log(next_online_time);

                            if (24 > next_online_time) {
                                // if less than 1 day then show in hours
                                console.log('hours: ' + next_online_time);
                                next_online_time = Math.round(next_online_time);
                                console.log('hours: ' + next_online_time);
                                var suffix = (next_online_time < 2) ? multi_main.ctc_hour : multi_main.ctc_hours;
                                next_online_time = next_online_time + ' ' + suffix;
                                console.log(next_online_time);
                            } else {
                                // if more than 1 day then show in days
                                console.log('days: ' + next_online_time);
                                next_online_time = next_online_time / 24;
                                console.log('days: ' + next_online_time);
                                next_online_time = Math.round(next_online_time);
                                var suffix = (next_online_time < 2) ? multi_main.ctc_day : multi_main.ctc_days;
                                next_online_time = next_online_time + ' ' + suffix;
                                console.log('days: ' + next_online_time);
                            }

                        }

                    } else {
                        console.log('no time set');
                    }

                    return next_online_time;

                }

            });



        }
        multi_agent();


        /**
         * variables.. 
         * ht_ctc_multi_agent_main, 
         * like.. ht_ctc_multi_agent_1, ht_ctc_multi_agent_2, ht_ctc_multi_agent_3, .. 
         */
        function get_var(key) {

            console.log('fn: get_var: ' + key);

            var v = '';
            var data_key = key + '_data';
            if (typeof window[key] !== "undefined") {
                v = window[key];
                console.log(v);
            } else {
                try {
                    if (document.querySelector('.' + data_key)) {
                        var settings = $('.' + data_key).attr('data-settings');
                        v = JSON.parse(settings);
                        console.log(v);
                    }
                } catch (e) {
                    v = {};
                }
            }

            $('.' + data_key).remove();

            return v;
        }


        // webhook .... global web hook values, global hook url
        g_hook_v = (ctc.hook_v) ? ctc.hook_v : '';
        g_hook_url = (ctc.hook_url) ? ctc.hook_url : '';


        // ht_ctc_settings
        // $(document).on('ht_ctc_event_settings', { 'hello': 'world' }, function (e, ctc) {
        // console.log(e.data);
        // ctc['hi'] = 10;
        // ctc.test = "helloworld";
        // });

        /**
         * Handles the `ht_ctc_event_number` custom event.
         *
         * Selects a contact number (`ctc.number`) from `ctc.r_nums` using either:
         * - **Sequential mode**: Uses `ctc.r_sqx` as an index, updates it with `update_r_sqx()`, resets to 0 if it exceeds the array length.
         * - **Random mode**: Picks a number randomly if `ctc.r_sqx` is empty or not set.
         *
         * Wrapped in a try-catch for safe error handling.
         *
         * @param {CustomEvent} e - Event containing `ctc` in `e.detail`.
         * 
         * @expects `ctc.r_nums` (Array) – List of contact numbers.
         * @optional `ctc.r_sqx` (Number|String) – Sequential index.
         * @modifies `ctc.number`, `ctc.r_sqx`
         */
        document.addEventListener("ht_ctc_event_number", function (e) {
            try {
                console.log('ht_ctc_event_number');
        
                ctc = e.detail.ctc;
        
                if (ctc.r_nums && ctc.r_nums[0]) {
                    console.log('[ht_ctc_event_number] r_nums:', ctc.r_nums);
                    console.log('ctc.r_sqx:', ctc.r_sqx);

                    // if ctc.r_sqx is not set then it is pure random mode. if set then sequential mode.

                    // ctc.r_sqx can be 0. so instead of if ctc.r_sqx check with typeof ctc.r_sqx !== undefined
                    if ( 'undefined' !== typeof ctc.r_sqx && '' !== ctc.r_sqx && !isNaN(ctc.r_sqx) ) {
                        console.log('[ht_ctc_event_number] Sequential logic');
        
                        var r_length = ctc.r_nums.length;
                        console.log('[ht_ctc_event_number] r_length:', r_length);
        
                        var r_sqx = ctc.r_sqx;
                        console.log('[ht_ctc_event_number] r_sqx:', r_sqx);
        
                        if (r_sqx >= r_length) {
                            console.log('[ht_ctc_event_number] r_sqx exceeded r_length, resetting to 0');
                            r_sqx = 0;
                        }
        
                        var selected_num = ctc.r_nums[r_sqx];
                        console.log('[ht_ctc_event_number] r_num picked:', selected_num);
        
                        ctc.number = selected_num;
        
                        if (r_sqx >= r_length) {
                            console.log('r_sqx exceeded r_length, resetting to 0');
                            r_sqx = 0;
                        }
        
                        ctc.r_sqx = r_sqx;
        
                        r_sqx++; // Increment for next time
        
                        update_r_sqx(r_sqx); // Send to DB
                        console.log('[ht_ctc_event_number] r_sqx incremented & updated to DB:', r_sqx);
                    } else {
                        // Pure random logic
                        var r_nums = ctc.r_nums;
                        console.log('[ht_ctc_event_number] Pure random mode');
        
                        var randomIndex = Math.floor(Math.random() * r_nums.length);
                        ctc.number = r_nums[randomIndex];
                        console.log('[ht_ctc_event_number] Randomly picked r_num:', ctc.number);
                    }
                }
            } catch (err) {
                console.error('Error in ht_ctc_event_number handler:', err);
            }
        });

       /**
         * Sends the provided `r_sqx` value to the server via AJAX to update in the database.
         *
         * - Includes a nonce (`ctc.nonce`) for security verification.
         * - Logs the result and handles errors gracefully using try-catch.
         * - Updates the local `ctc.r_sqx` value on successful response.
         *
         * @param {number} r_sqx - The sequential index value to be stored in the database.
         */
        function update_r_sqx(r_sqx) {

            console.log('update_r_sqx()');

            
            try {

                console.log('Sending update_r_sqx:', r_sqx);

                // ctc.r_sqx can be 0. so instead of ctc.r_sqx check ctc.r_sqx !== undefined
                if ('undefined' !== typeof ctc.r_sqx && !isNaN(ctc.r_sqx) ) {

                    $.ajax({
                        url: ctc.ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'ctc_pro_update_r_sqx',
                            r_sqx: r_sqx,
                            nonce: ctc.nonce
                        },
                        success: function (response) {
                            try {
                                if (response.success) {
                                    console.log('update_r_sqx success:', response.data);
                                    ctc.r_sqx = Number(r_sqx);
                                } else {
                                    console.warn('update_r_sqx failed:', response.data);
                                }
                            } catch (innerErr) {
                                console.error('update_r_sqx success handler error:', innerErr);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('update_r_sqx AJAX error:', error);
                        }
                    });
                }
            } catch (err) {
                console.error('update_r_sqx error:', err);
            }
        }

        /**
         * Retrieves the `r_sqx` value from the server via AJAX (useful for initial page load).
         *
         * - Ensures only one fetch request runs using `for_get_r_sqx`.
         * - Handles valid and failed responses, updating the `ctc.r_sqx` value accordingly.
         * - Uses a nonce for secure communication with the backend.
         */
        function get_r_sqx() {
            try {

                if (!ctc.r_sqx) {
                    return;
                }

                if ( 0 !== for_get_r_sqx ) {
                    console.log('get_r_sqx called:', for_get_r_sqx);
                    return;
                }
                for_get_r_sqx++;

                $.ajax({
                    url: ctc.ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'ctc_pro_get_r_sqx',
                        nonce: ctc.nonce
                    },
                    success: function (response) {
                        try {
                            if (response.success) {
                                console.log('Fetched r_sqx value:', response.data);
                                ctc.r_sqx = response.data;
                            } else {
                                console.warn('get_r_sqx failed:', response.data);
                                ctc.r_sqx = '';
                            }
                        } catch (innerErr) {
                            console.error('get_r_sqx success handler error:', innerErr);
                            ctc.r_sqx = '';
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('get_r_sqx AJAX error:', error);
                        ctc.r_sqx = '';
                    }
                });
            } catch (err) {
                console.error('get_r_sqx error:', err);
                ctc.r_sqx = '';
            }
        }

        /**
         * Initial call to fetch `r_sqx` from the server on page load.
         * Should be placed after the `get_r_sqx()` function definition.
         */
        get_r_sqx();




        // form - greetings call to action button (as the button maynot be the form send type button)
        $(document).on('click', '.ht_ctc_chat_greetings_for_forum_link', function (e) {
            console.log('ht_ctc_chat_greetings_for_forum_link');
            $('.ht_ctc_chat_greetings_forum_link').trigger('click');

            // form checkbox validation.. in separate way also..
            var forms = document.getElementById("ctc_pro_greetings_form");
            if (forms && forms.checkValidity() === false) {
                console.log('invalid form..');
                $('#ctc_pro_greetings_form [type=checkbox][required]:not(:checked)').closest('div').fadeOut('1').fadeIn('1');
            }

        });


        // form - opt-in..
        // optin - checkbox on change
        if (document.querySelector('#ctc_opt_g_form')) {
            $("#ctc_opt_g_form").on("change", function (e) {
                if ($('#ctc_opt_g_form').is(':checked')) {
                    $('.ctc_opt_g_form').hide(500);
                    ctc_setItem('g_optin', 'y');
                }
            });
            if (ctc_getItem('g_optin')) {
                console.log('g_optin');
                $('#ctc_opt_g_form').prop('checked', true);
                $('.ctc_opt_g_form').hide();
            } else {
                $('#ctc_opt_g_form').prop('required', true);
            }
        }

        // if exists.. ht_ctc_g_form_field_hidden then for each.. get the value and replace with apply_variables function and replace the value
        if (document.querySelector('.ht_ctc_g_form_field_hidden')) {
            console.log('ht_ctc_g_form_field_hidden');
            $('.ht_ctc_g_form_field_hidden').each(function (i, obj) {
                console.log(i);
                console.log(obj);
                var value = $(obj).val();
                console.log(value);
                value = apply_variables(value);
                console.log(value);
                $(obj).val(value);
            });
        }


        // ht_ctc_chat_greetings_box_link_multi
        // multi agent - greetings link click..
        $(document).on('click', '.ht_ctc_chat_greetings_box_link_multi', function (e) {
            console.log('ht_ctc_chat_greetings_box_link_multi');
            e.preventDefault();

            // optin (optin id: ctc_opt_multi)
            if (document.querySelector('#ctc_opt_multi')) {
                if ($('#ctc_opt_multi').is(':checked') || ctc_getItem('g_optin')) {
                    console.log('checked.........');
                    display_multi_agents();
                } else {
                    $('.ctc_opt_in').show(400).fadeOut('1').fadeIn('1');
                }
            } else {
                display_multi_agents();
            }

            // optin - checkbox on change
            if (document.querySelector('#ctc_opt_multi')) {
                $("#ctc_opt_multi").on("change", function (e) {
                    console.log('ctc_opt_multi changed..');
                    if ($('#ctc_opt_multi').is(':checked')) {
                        $('.ctc_opt_in').hide(100);
                        ctc_setItem('g_optin', 'y');
                        setTimeout(() => {
                            display_multi_agents();
                        }, 500);
                    }
                });
            }

            function display_multi_agents() {
                console.log('display_multi_agents');

                var agent_style = $('.ctc_g_content').attr('data-agentstyle');
                console.log(agent_style);


                if ('card-1' == agent_style) {
                    // ctc_g_content
                    // bg color - ffffff | f5f5f5f5 | fefefe | f6f6f6
                    $('.ctc_g_content').css({
                        'padding': '4px 0px',
                        'background-color': '#f8f8f8'
                    });

                } else if ('7_1' == agent_style) {

                    // ctc_g_content
                    $('.ctc_g_content').css({
                        'padding': '4px 0px 8px 0px',
                    });

                }

                $('.ctc_g_sentbutton').hide();

                if ('card-1' == agent_style) {

                    // $('.ctc_g_message_box').css({
                    //     'background-color': '',
                    //     'text-align': 'center',
                    //     'padding': '4px 0px',
                    // });

                    var ctc_g_message_box = $('.ctc_g_message_box');
                    // var clone = ctc_g_message_box.clone();
                    $(ctc_g_message_box).removeClass('ctc_g_message_box')
                    $(ctc_g_message_box).css({
                        'background-color': '',
                        // 'color': '#ffffff',
                        // 'font-size': '14px',
                        'text-align': 'inherit',
                        'padding': '4px 0px',
                    });
                    $('.ctc_g_heading_for_main_content').append(ctc_g_message_box);

                } else if ('7_1' == agent_style) {

                    $('.ctc_g_message_box').css({
                        'background-color': '',
                        'text-align': 'center',
                        'padding': '4px 0px',
                    });
                }





                // var ctc_g_message_box = $('.ctc_g_message_box');
                // var clone = ctc_g_message_box.clone();
                // $(clone).removeClass('ctc_g_message_box')
                // $(clone).css({
                //     'background-color': '',
                //     'color': '#ffffff',
                //     'text-align': 'center',
                //     'padding': '4px 0px',
                // });
                // $('.ctc_g_heading').append(clone);


                // $('.ctc_g_agents').show(500);
                $('.ctc_g_agents').slideToggle('slow', function () {
                    console.log('slideup done');
                });
            }


        });

        // greeting multi - if initial display is agents - optin
        function multi_optin_inital_agents() {

            if (document.querySelector('.add_ctc_chat')) {
                // before onclick - if already optin then remove add_ctc_chat and add ctc_chat
                if ($('#ctc_opt_multi').is(':checked') || ctc_getItem('g_optin')) {
                    console.log('already option - change class name .........');
                    $('.add_ctc_chat').addClass('ctc_chat').removeClass('add_ctc_chat');
                }

                // on click - display optin
                $(document).on('click', '.add_ctc_chat', function (e) {
                    // $('.ctc_opt_multi')
                    $('.ctc_opt_in').show(400).fadeOut('1').fadeIn('1');
                });

                // optin - checkbox on change
                $("#ctc_opt_multi").on("change", function (e) {
                    console.log('ctc_opt_multi changed..');
                    if ($('#ctc_opt_multi').is(':checked')) {
                        $('.ctc_opt_in').hide(100);
                        ctc_setItem('g_optin', 'y');
                        $('.add_ctc_chat').addClass('ctc_chat').removeClass('add_ctc_chat');
                    }
                });
            }
        }
        multi_optin_inital_agents()




        /**
         * Greetings from. on sent.. 
         */
        function greetings_form() {

            /**
             * form sent..
             * 
             * @var in_one_second: 2500ms - to avoid duplicate form send in one second
             */
            var form_send_count = 1;
            var in_one_second = 'no';

            // form: greetings form submit
            $(document).on('submit', '.ctc_pro_greetings_form', function (e) {

                console.log('form send pro...........');
                console.log('form send count: ' + form_send_count);
                console.log('in_one_second: ' + in_one_second);


                e.preventDefault();


                if (in_one_second == 'yes') {
                    console.log('Duplicate form detected. (in_one_second) retun...');
                    return;
                }

                throttleFormSend();


                // if checkbox is checked, then disabled the sibling hidden field
                handleCheckboxes();


                // form data: at email, webhook
                var formData = $('.ctc_pro_greetings_form').find('.ht_ctc_g_form_field').serializeArray();
                console.log('formData');
                console.log(formData);


                var existing_pre_filled = ctc.pre_filled;
                console.log('existing_pre_filled: ' + existing_pre_filled);

                // Form data attach to ctc.pre_filled
                var form_prefilled = form_data_attach_to_pre_filled();
                console.log('form_prefilled: ' + form_prefilled);


                $('.ht_ctc_chat_greetings_forum_link').addClass('ht_ctc_chat_greetings_box_link');

                // time may need to add class.. in form its not working if delay not added..
                setTimeout(() => {
                    $('.ht_ctc_chat_greetings_box_link').trigger('click');
                }, 20);


                setTimeout(() => {
                    $('.ht_ctc_chat_greetings_forum_link').removeClass('ht_ctc_chat_greetings_box_link');

                    console.log(ctc.pre_filled);

                    // reset the pre-filled value after form send.
                    ctc.pre_filled = existing_pre_filled;
                    console.log(ctc.pre_filled);

                }, 500);



                // Send form data via email or webhook
                sendFormData(formData);

                reenableHiddenfields();

                form_send_count++;

            });



            // throttle form send
            function throttleFormSend() {

                console.log('throttleFormSend()');

                // form started. change to yes. and after 1 second change to no
                in_one_second = 'yes';

                setTimeout(() => {
                    in_one_second = 'no';
                }, 2500);

            }

            // if checkbox is checked, then disabled the sibling hidden field - handle checkboxs in form - part-1
            function handleCheckboxes() {
                try {
                    $('.ctc_g_its_checkbox').each(function () {
                        if ($(this).is(":checked")) {
                            $(this).siblings(['type="hidden"']).prop('disabled', true);
                            console.log($(this).siblings(['type="hidden"']));
                        }
                    });
                } catch (e) { }
            }

            function reenableHiddenfields() {
                // handle checkboxs in form - part-2
                try {
                    $('.ctc_g_hidden_for_checkbox').each(function () {
                        $(this).prop('disabled', false);
                    });
                } catch (e) { }
            }

            // form: send form data
            function sendFormData(formData) {

                console.log('sendFormData() - webhook or email - ready to call');

                // if email address is added..
                if (ctc.ajaxurl && ctc.g1_form_email) {
                    form_send_email(formData);
                }

                // webhook ....
                if (ctc.g1_form_webhook) {
                    form_webhooks(formData);
                }
            }

            // form: data attach to ctc.pre_filled
            function form_data_attach_to_pre_filled() {

                console.log('form prefilled');

                if (!document.querySelector('.ctc_g_field_add_to_prefilled')) {
                    console.log('no form field to add to pre-filled');
                    return '';
                }


                var temp_pre_filled = ctc.pre_filled;
                console.log(temp_pre_filled);

                // ctc_g_field_add_to_prefilled
                var prefilled_form = $('.ctc_pro_greetings_form').find('.ctc_g_field_add_to_prefilled').serializeArray();
                console.log(prefilled_form);

                var pre_fields = '\n';
                for (var value of prefilled_form.values()) {
                    console.log(value);
                    var name = value['name'];
                    var c = ".ctc_pro_greetings_form [name='" + name + "']";
                    var data_name = name;
                    if ($(c)[0].hasAttribute('data-name')) {
                        data_name = $(c).attr('data-name');
                    }
                    var val = value['value'];
                    // if value is blank then dont add to pre-filled. so if filed value is blank then dont add to pre-filled. (if need can set as required in form)
                    if ('' !== val) {
                        pre_fields += data_name + ': ' + val + '\n';
                    }
                }
                console.log(pre_fields);
                temp_pre_filled += pre_fields;
                // temp_pre_filled += encodeURIComponent(pre_fields)

                ctc.pre_filled = temp_pre_filled;
                console.log(ctc.pre_filled);

                return temp_pre_filled;

            }

            // form: send_email
            function form_send_email(formData) {

                var nonce = $('.ctc_g_form_keys #ht_ctc_pro_greetings_nonce').val();
                console.log(nonce);

                $.ajax({
                    url: ctc.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'ctc_pro_greetings_form',
                        form_data: formData,
                        nonce: nonce,
                    },
                    success: function (response) {
                        // ctc_setItem('form_data_sent', new Date().getTime());
                        console.log('form email: success');
                        console.log(response);
                    },
                    error: function (response) {
                        console.log('error');
                        console.log(response);
                    }
                });

            }

            // form: webhooks
            function form_webhooks(formData) {

                console.log('form_webhooks');

                var h_url = ctc.g1_form_webhook;
                console.log(formData);

                var v_json = {};

                // in localization it may change .. so not using data-name  - so name will be like field-1, ....
                for (var value of formData.values()) {
                    console.log(value);
                    var name = value['name'];
                    var val = value['value'];
                    v_json[name] = val;
                }

                console.log(v_json);

                console.log(url);
                decoded_url = ctc_decodeURI(url);
                console.log(decoded_url);


                v_json['ctc_page_url'] = decoded_url;

                var t = time_on_wordpress();
                v_json['ctc_wp_date'] = t.toDateString();
                v_json['ctc_wp_time'] = t.getHours() + ':' + t.getMinutes() + ':' + t.getSeconds();

                // page title
                v_json['ctc_page_title'] = post_title;

                console.log(v_json);
                console.log(typeof v_json);

                // ht_ctc_chat_var.webhook_format = 'json';
                if (ctc.webhook_format && 'json' == ctc.webhook_format) {
                    console.log('form: json');
                    var data = v_json;
                } else {
                    console.log('form: string');
                    // var data = JSON.stringify(form);
                    var data = JSON.stringify(v_json);
                }


                console.log(data);
                console.log(typeof data);

                console.log(h_url);


                $.ajax({
                    url: h_url,
                    type: 'POST',
                    mode: 'no-cors',
                    data: data,
                    success: function (response) {
                        // ctc_setItem('form_data_sent', new Date().getTime());
                        console.log('form webhook: success');
                        console.log(response);
                    },
                    error: function (response) {
                        console.log('error');
                        console.log(response);
                    }
                });

            }




        }
        greetings_form();

        
        
        

        


        


        /**
         * ht_ctc_event_hook
         * 
         * @var t call time_on_wordpress() to get latest value;
         */
        document.addEventListener("ht_ctc_event_hook", function (e) {

            console.log(e);

            ctc = e.detail.ctc;
            number = (ctc.chat_number && '' !== ctc.chat_number) ? ctc.chat_number : e.detail.number;

            console.log(ctc);
            console.log(number);


            var t = time_on_wordpress();
            console.log(t);
            // time = day + ' ' + hour + ' ' + minute;
            var time_var = t.toDateString() + ', ' + t.getHours() + ':' + t.getMinutes() + ':' + t.getSeconds();
            console.log(time_var);

            if (ctc.hook_url) {

                // var h_url = g_hook_url;
                var h_url = (ctc.hook_url) ? ctc.hook_url : '';

                console.log(url);
                decoded_url = ctc_decodeURI(url);
                console.log(decoded_url);
                h_url = h_url.replace(/\{url}/gi, decoded_url);
                h_url = h_url.replace(/\{time}/gi, time_var);

                h_url = apply_variables(h_url);
                // h_url = h_url.replace(/\{number}/gi, number);
                // h_url = h_url.replace(/\{title}/gi, post_title);


                console.log(h_url);

                // hook values
                if (ctc.hook_v) {

                    /**
                     * always have to get initial values. if not with out refresh the page is mulitple clicks happened, foreach may not works
                     */
                    hook_values = g_hook_v;

                    console.log(hook_values);
                    console.log(typeof hook_values);

                    var pair_values = {};
                    var i = 1;

                    hook_values.forEach(e => {
                        console.log(i);
                        console.log(e);

                        e = e.replace(/\{url}/gi, decoded_url);
                        e = e.replace(/\{time}/gi, time_var);

                        e = apply_variables(e);
                        // e = e.replace(/\{number}/gi, number);
                        // e = e.replace(/\{title}/gi, post_title);

                        console.log(e);
                        pair_values['value' + i] = e;
                        i++;
                    });

                    console.log(typeof pair_values);
                    console.log(pair_values);

                    ctc.hook_url = h_url;
                    ctc.hook_v = pair_values;
                }

            }

        });


        // calls only if ctc.display_user_base is logged_in or logged_out
        function display_user_base(e) {

            is_chat_display = 'no';

            ctc = e.detail.ctc;

            var display_user_base = (ctc.display_user_base) ? ctc.display_user_base : '';
            console.log(display_user_base);

            var user_login_status = 'logged_out';
            if (document.querySelector('body').classList.contains('logged-in')) {
                user_login_status = 'logged_in';
            }

            if ('logged_in' == display_user_base) {
                if ('logged_in' == user_login_status) {
                    // business_hour(e);
                    is_chat_display = 'yes';
                } else {
                    // if logged-in class not exists - double check
                    if (ctc.ajaxurl) {
                        $.ajax({
                            url: ctc.ajaxurl,
                            data: {
                                action: 'ctc_pro_is_user_logged_in',
                            },
                            success: function (response) {
                                if ('yes' == response.data) {
                                    // console.log(response);
                                    // business_hour(e);
                                    is_chat_display = 'yes';
                                }
                            }
                        });
                    }
                }
            } else if ('logged_out' == display_user_base && 'logged_out' == user_login_status) {
                // business_hour(e);
                is_chat_display = 'yes';
            }

            return is_chat_display;

        }


        function business_hour(e) {

            console.log('business_hour');

            ctc = e.detail.ctc;
            display_chat = e.detail.display_chat;
            ht_ctc_chat = e.detail.ht_ctc_chat;

            console.log(wptime);


            var b_h = '';
            var bh = ctc.bh;
            var day = wptime.getDay()
            var c_h = wptime.getHours();
            var c_m = wptime.getMinutes();
            console.log(day);
            console.log(c_h);
            console.log(c_m);

            // business hours
            if ('always' == bh) {
                // always online
                b_h = 'yes';
            } else if ('timebase' == bh) {

                var today = wptime.getDay();
                console.log(today);

                if ('0' == today) {
                    // sunday ( count, day starts with 0, sunday. in admin side settings sunday 7 )
                    console.log('sunday');
                    today = 7;
                }

                var wpday = 'd' + today;
                console.log(wpday);

                if (ctc[wpday]) {
                    // today is online
                    console.log('today is online');

                    var st = wpday + '_st';
                    var et = wpday + '_et';
                    console.log(st);
                    console.log(et);

                    // if time set - if not set 24 hours online
                    if (ctc[st] && ctc[et]) {
                        // if start, end time is set
                        console.log('start and end is set');

                        var st = ctc[st];
                        var et = ctc[et];
                        console.log(st);
                        console.log(et);

                        // hour
                        var get_s_h_m = st.split(':');
                        var get_e_h_m = et.split(':');

                        var s_h = get_s_h_m[0];
                        var s_m = get_s_h_m[1];

                        var e_h = get_e_h_m[0];
                        var e_m = get_e_h_m[1];

                        console.log(s_h);
                        console.log(s_m);
                        console.log(e_h);
                        console.log(e_m);

                        console.log(c_h);
                        console.log(c_m);

                        // hour
                        // if ( s_h <= c_h < e_h ) {
                        if (s_h <= c_h && c_h <= e_h) {
                            console.log('scheduled hour time');
                            b_h = 'yes';
                            // if same hour then minute
                            if (s_h == c_h) {
                                console.log('same hour - start time or end time');
                                if (s_m > c_m) {
                                    // yes if s_m <= c_m (reverse play as early b_h yes is added)
                                    b_h = 'no';
                                }
                            } else if (c_h == e_h) {
                                console.log('same hour - end time');
                                if (c_m > e_m) {
                                    b_h = 'no';
                                }
                            }
                        } else {
                            // not in business hour range
                            console.log('not in business hours range');
                            b_h = 'no';
                        }

                    } else {
                        // start time, end time not set so 24 hours online
                        console.log('week day 24 hours online');
                        b_h = 'yes';
                    }

                } else {
                    // offline based on week day
                    console.log('offline based on week day');
                    b_h = 'no';
                }

            }

            // if its business hour
            if ('yes' == b_h) {
                // its online now
                console.log('online');
                time_scroll();
                woo_display_bh();
                online_content_pro();
            } else if ('no' == b_h) {
                // its offline now
                offline();
                offline_content_pro();
            }

            function online_content_pro() {
                console.log('online_content_pro');

                if (typeof online_content !== 'undefined') {
                    online_content();
                }

            }

            function offline_content_pro() {
                console.log('offline_content_pro');

                if ($('.for_greetings_header_image_badge').length) {

                    if (ctc.offline_badge_color) {
                        $('.for_greetings_header_image_badge').addClass('g_header_badge_online');
                        // $('.for_greetings_header_image_badge').css('background-color', ctc.offline_badge_color);
                        $('.g_header_badge_online').css('background-color', ctc.offline_badge_color);
                    }
                    $('.for_greetings_header_image_badge').show();
                }


            }

            function offline() {
                console.log('offline');

                // call to action
                if (ctc.off_cta) {
                    $(".ht-ctc-chat .ctc_cta").text(ctc.off_cta);
                    // woo
                    $(".ctc_chat.ctc_woo_schedule .ctc_cta").text(ctc.off_cta);
                }

                // offline number change
                if (ctc.off_num) {
                    ctc.number = ctc.off_num;
                    // if offline number is added, no random numbers at offline
                    ctc.r_nums = '';
                }

                // if not hided
                // ctc.off_hide = 'y';
                if (!ctc.off_hide) {
                    // time, scroll settings
                    time_scroll();
                    woo_display_bh();
                }

            }


            function time_scroll() {

                // time delay, page scroll
                if ('' !== ctc.timedelay || '' !== ctc.scroll) {

                    if ('' !== ctc.timedelay) {
                        setTimeout(() => {
                            display_chat(ht_ctc_chat);
                        }, ctc.timedelay * 1000);
                    }

                    if ('' !== ctc.scroll) {
                        window.addEventListener("scroll", event_parse_scroll, false);

                        function event_parse_scroll() {
                            var h = document.documentElement;
                            var b = document.body;
                            var st = 'scrollTop';
                            var sh = 'scrollHeight';
                            var percent = (h[st] || b[st]) / ((h[sh] || b[sh]) - h.clientHeight) * 100;

                            if (percent >= ctc.scroll) {
                                console.log('display based on scroll');
                                display_chat(ht_ctc_chat);
                                window.removeEventListener("scroll", event_parse_scroll);
                            }
                        }
                    }

                } else {
                    display_chat(ht_ctc_chat);
                }
            }

            // woo - display
            function woo_display_bh() {

                if (document.querySelector('.ctc_woo_schedule')) {
                    var dt = $('.ctc_woo_schedule').attr('data-dt');
                    console.log(dt);
                    $('.ctc_woo_schedule').css('display', dt);
                }

            }


        }

        /**
         * 
         * Custom event: ht_ctc_event_display. (if ctc.schedule then only this custom event calls)
         * 
         * if ctc.display_user_base - then display chat to logged in/out users
         *      and display_user_base function will handle the call of business_hour
         * 
         * business hours - online/offline 
         *  most of the things update here and call display_chat(ht_ctc_chat) 
         *  with in custom element getting this display_chat(ht_ctc_chat)
         * Display
         *  time delay
         *  scroll
         * Offline
         *  call to action
         *  number
         *  hide or not
         * 
         * @var is_chat_display - if is_chat_display is 'no'.. then no need to check later display steps
         */
        document.addEventListener("ht_ctc_event_display", function (e) {

            console.log('Custom Event: ht_ctc_event_display');
            console.log(e);

            console.log(ctc);
            ctc = e.detail.ctc;
            console.log(ctc);
            online_content = e.detail.online_content;

            // is_chat_display
            console.log('is_chat_display: ' + is_chat_display);


            // user login status
            if (ctc.display_user_base) {
                is_chat_display = display_user_base(e);
                console.log('@display_user_base - is_chat_display: ' + is_chat_display);
            }

            // based on country code
            if ('no' !== is_chat_display) {

                // if country code is set
                if (ctc_values.display_countries_list && ctc_values.display_countries_list.length > 0) {
                    console.log('display based on country code');



                    document.dispatchEvent(
                        new CustomEvent("ht_ctc_event_display_country_base", { detail: { ctc, ctc_setItem, ctc_getItem } })
                    );


                    /**
                     * 
                     * ht_ctc_variables.country_code (ctc_values.country_code) is set from country.js.. 
                     * if not set with in the given time, then it like ignore the display based on country code option.
                     */
                    var i = 0;
                    var country_base_interval = setInterval(() => {
                        console.log('country_base - interval');
                        i++;
                        console.log('getting country code - interval: ' + i);
                        if (ctc_values.country_code) {
                            console.log('country code set - ctc_values.country_code: ' + ctc_values.country_code);
                            if (ctc_values.country_code.length > 0) {
                                // if country code is set (ctc_values.country_code is set from country.js)
                                // another way if(ctc_values.display_countries_list.indexOf(current_country) !== -1)
                                if (ctc_values.display_countries_list.includes(ctc_values.country_code)) {
                                    console.log('country code match');
                                    is_chat_display = 'yes';
                                } else {
                                    console.log('country code not match');
                                    is_chat_display = 'no';
                                }
                            }

                            console.log('country code set - clear interval');
                            clearInterval(country_base_interval);
                            next_after_display_country_base();
                        }
                        // 8 seconds.. 40 * 200 = 8000
                        if (i > 40) {
                            console.log('country code not set - clear interval');
                            clearInterval(country_base_interval);
                            next_after_display_country_base();
                        }
                    }, 200);

                } else {
                    // if country code is not set
                    console.log('country code not set');
                    next_after_display_country_base();
                }
            }


            function next_after_display_country_base() {
                console.log('fn: next_after_display_country_base()');
                if ('no' !== is_chat_display) {
                    business_hour(e);
                }
            }


        });

        // ht_ctc_event_after_chat_displayed
        document.addEventListener("ht_ctc_event_after_chat_displayed", function (e) {

            ctc = e.detail.ctc;
            greetings_open = e.detail.greetings_open;
            greetings_close = e.detail.greetings_close;

            console.log(ctc);

            // console.log(ctc);
            console.log('ht_ctc_event_after_chat_displayed');

            // g_no_reopen
            console.log(ht_ctc_storage);

            if (ctc.g_no_reopen && 'user_closed' == ctc_getItem('g_user_action')) {
                // no g - time, scroll actions
            } else {

                if (ctc.g_time_action) {
                    console.log('inside time action');
                    setTimeout(() => {
                        greetings_open('time_action');
                    }, ctc.g_time_action * 1000);
                }

                if (ctc.g_scroll_action) {
                    console.log('inside scroll action');

                    window.addEventListener("scroll", g_event_parse_scroll, false);

                    function g_event_parse_scroll() {
                        var h = document.documentElement;
                        var b = document.body;
                        var st = 'scrollTop';
                        var sh = 'scrollHeight';
                        var percent = (h[st] || b[st]) / ((h[sh] || b[sh]) - h.clientHeight) * 100;

                        if (percent >= ctc.g_scroll_action) {
                            console.log('greetings display based on scroll');
                            greetings_open('scroll_action');
                            window.removeEventListener("scroll", g_event_parse_scroll);
                        }
                    }
                }

            }


            // greetings container

            // method-1
            if (document.querySelector('.ctc_greetings_now') && 'IntersectionObserver' in window) {

                var p = (window.innerHeight / 4) * -1;
                p = parseInt(p);
                rm = p + "px";
                rm = String(rm)


                // method-2
                // var rm = (window.innerHeight > 900) ? '-300px' : '-150px';

                console.log(rm);

                var observer = new IntersectionObserver(function (entries) {

                    console.log('observer');
                    console.log(entries);

                    entries.forEach(entry => {
                        console.log(entry);
                        if (!entry.isIntersecting) return;
                        greetings_open('now');
                        observer.unobserve(entry.target);
                    });

                    console.log(entries);

                }, {
                    rootMargin: rm,
                });

                // start observing
                document.querySelectorAll('.ctc_greetings_now').forEach(element => {
                    console.log(element);
                    observer.observe(element, { subtree: true, childList: true });
                });

            }



        });


        /**
         * apply variables
         * 
         * {} ctc special variables - {number}, {title}, {url}
         * cookie: [[value]]
         * url parms: [value]
         * 
         * @param {*} v 
         * @returns 
         */
        function apply_variables(v) {

            console.log('pro: apply_variables: ' + v);

            var number = (ctc.chat_number && '' !== ctc.chat_number) ? ctc.chat_number : ctc.number;

            try {
                // multiple occurance not added.. replaceall might not work on all browsers.
                v = v.replace('{number}', number);
                v = v.replace('{title}', post_title);
                v = v.replace('{url}', url);

                // cookie: [[value]]
                var matches = v.match(/\[\[(.*?)\]\]/g);
                if (matches) {
                    console.log('[[cookie]] matches: ' + matches);
                    matches.forEach(e => {
                        console.log(e);
                        var key = e.replace('[[', '').replace(']]', '');
                        v = v.replace(e, getCookie(key));
                        console.log(key);
                        console.log(v);
                    }
                    );
                }

                // url parms: [value]
                var matches = v.match(/\[(.*?)\]/g);
                if (matches) {
                    console.log('[url parms] matches: ' + matches);
                    matches.forEach(e => {
                        console.log(e);
                        var key = e.replace('[', '').replace(']', '');
                        console.log(key);
                        v = v.replace(e, getParameterByName(key));
                        console.log(v);
                    }
                    );
                }

                // // local storage: {{value}}
                // var matches = v.match(/\{\{(.*?)\}\}/g);
                // console.log('{{local storage}} matches: ' + matches);
                // if (matches) {
                //     matches.forEach(e => {
                //         console.log(e);
                //         var key = e.replace('{{', '').replace('}}', '');
                //         v = v.replace(e, 'local storage');
                //         console.log(key);
                //         console.log(v);
                //     }
                //     );
                // }

                // // session storage: *value*
                // var matches = v.match(/\*\*(.*?)\*\*/g);
                // console.log('*session storage* matches: ' + matches);
                // if (matches) {
                //     matches.forEach(e => {
                //         console.log(e);
                //         var key = e.replace('**', '');
                //         console.log(key);
                //         v = v.replace(e, 'session storage');
                //         console.log(v);
                //     }
                //     );
                // }


            } catch (e) { }


            console.log(v);
            return v;
        }


        // custom event: ht_ctc_event_apply_variables
        document.addEventListener("ht_ctc_event_apply_variables", function (e) {
            console.log('custom event: ht_ctc_event_apply_variables');

            v = e.detail.v;

            console.log(v);
            v = apply_variables(v);
            console.log(v);

            // set value to window as apply_variables_value
            window.apply_variables_value = v;

        });



        // Analytics
        document.addEventListener("ht_ctc_event_analytics", function (e) {
            console.log('ht_ctc_analytics - PRO analytics');


            var num = (ctc.chat_number && '' !== ctc.chat_number) ? ctc.chat_number : ctc.number;
            console.log('clicked number: ' + num);


            // google ads conversation - gtag event conversion
            if (ctc.gads_conversation) {

                console.log('gads_conversation: ' + ctc.gads_conversation);

                // get dataLayer .. and if type of dataLayer is object..
                var data_layer = window['dataLayer'];
                console.log(data_layer);
                console.log(typeof data_layer);

                if (typeof data_layer == "object") {

                    if (typeof gtag == "undefined") {
                        console.log('gtag not defined, define gtag');
                        window.gtag = function () {
                            data_layer.push(arguments);
                        };
                    }

                    var sendto = ctc.gads_conversation;
                    console.log(sendto);

                    var gads_parms = {};
                    gads_parms.send_to = sendto;

                    console.log(gads_parms);

                    // if google ads parameters
                    if (ctc_values.g_ads_params) {
                        console.log('g_ads_params');
                        console.log(ctc_values.g_ads_params);
                        ctc_values.g_ads_params.forEach(e => {
                            console.log(e);
                            if (ctc_values[e]) {
                                var p = ctc_values[e];
                                console.log(p);
                                var k = p['key'];
                                var v = p['value'];
                                k = apply_variables(k);
                                v = apply_variables(v);
                                console.log(k);
                                console.log(v);
                                gads_parms[k] = v;
                            }
                        });
                    }
                    console.log(gads_parms);


                    gtag('event', 'conversion', gads_parms);

                }
                    
            }

            var nonce = (ctc.nonce) ? ctc.nonce : '';

            // fb conversation api event
            if (ctc.fb_conversion) {
                console.log('capi..');
                $.ajax({
                    url: ctc.ajaxurl,
                    data: {
                        action: 'ctc_pro_capi',
                        url: url,
                        title: post_title,
                        number: num,
                        nonce: nonce,
                    },
                    type: "POST",
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                });
            }

        });


        function others() {
            // ht_ctc_chat_greetings_box_layout - if class exists.. - bg color unset.
            if (document.querySelector('.ht_ctc_chat_greetings_box_layout')) {
                $('.ht_ctc_chat_greetings_box_layout').css('background-color', 'unset');
            }
        }
        others();


        // getParameterByName
        function getParameterByName(name) {
            console.log('getParameterByName');
            var url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');

            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            // array or null
            var results = regex.exec(url);

            console.log('regex: ' + regex);
            console.log('results: ' + results);

            // if (!results) return null;
            if (!results) return '';
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        // get cookie
        function getCookie(name) {
            console.log('getCookie: ' + name);

            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            // for subdomain this may not work. parts.length might be more than 2.. it might not be the correct value for that site.

            if (parts.length == 2) {
                return parts.pop().split(";").shift();
            } else {
                return '';
            }
        }



        // time based on wordpress timezone
        function time_on_wordpress() {
            var current_time = new Date();
            var current_time_ms = current_time.getTime();
            var time_offset = current_time.getTimezoneOffset() * 60000;
            var g = current_time_ms + time_offset;
            var website_timezone = parseFloat(ctc.tz);
            var website_time = g + 3600000 * website_timezone;
            var wp_time = new Date(website_time);
            return wp_time;
        }
        var wptime = time_on_wordpress();


        function get_current_minute() {

            var weekly_min = '';

            var wp_time = time_on_wordpress();

            var today = wp_time.getDay();
            var c_h = wp_time.getHours();
            var c_m = wp_time.getMinutes();
            if ('0' == today) {
                today = 7;
            }
            console.log('today: ' + today);
            console.log('hour: ' + c_h);
            console.log('minute: ' + c_m);

            var weekly_min = ((((today - 1) * 24) + c_h) * 60) + c_m;

            console.log('weekly_min: ' + weekly_min);

            return weekly_min;
        }

        get_current_minute();

        function ctc_decodeURI(value) {
            new_value = value;
            try {
                console.log('try');
                new_value = decodeURI(value);
            } catch (e) {
                console.log('catch');
                // new_value = value;
            }

            return new_value;
        }

    });
})(jQuery);