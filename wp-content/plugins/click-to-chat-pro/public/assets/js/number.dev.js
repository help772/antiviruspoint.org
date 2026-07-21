(function ($) {
    // ready
    $(function () {

        console.log('intl.js');



        function intl() {
            console.log('intl()');

            try {
                // @parm: class name
                intl_input('ctc_intl_number');
            } catch (e) {
                console.log(e);
                console.log('cache: intl_input');
            }

        }
        intl();
        


        function intl_input(className) {

            console.log('intl_input() className: ' + className);

            var is_append_intltel_files = 'n';


            if (document.querySelector("." + className)) {

                console.log(className + ' class name exists');

                console.log(ht_ctc_variables);
                if (typeof ht_ctc_variables !== 'undefined') {

                    console.log('check_delay');
                    if (ht_ctc_variables.intl_files_load_type) {
                        var load_type = ht_ctc_variables.intl_files_load_type;
                        console.log('load type: ' + load_type);
                        if ('delay_1' == load_type) {
                            delay_1();
                        } else if ('delay_2' == load_type) {
                            delay_2();
                        } else if ('nodelay' == load_type) {
                            no_delay();
                        }
                    }

                } else {
                    console.log('ht_ctc_variables not exists..');
                }


                /**
                 * delay_1: idle time and wait for 5 seconds.. OR if user interacted with chat widget (call delay_2() function)
                 */
                function delay_1() {
                    console.log('delay_1: idle time..');

                    // check if browser is idle.. then load the js and css files..
                    if ('requestIdleCallback' in window) {
                        requestIdleCallback(function () {
                            console.log('requestIdleCallback');
                            // load js and css files..
                            setTimeout(() => {
                                append_intltel_files();
                            }, 5000);
                        });
                    } else {
                        console.log('requestIdleCallback not exists');
                        // load js and css files..
                        append_intltel_files();
                    }

                    // before idle and 5 seconds wait, user can interacted with chat widget.. so call delay_2() function..
                    delay_2();
                }

                // after user interacted with widget/form.
                function delay_2() {
                    console.log('delay_2: After user interacted with chat widget');

                    // if user interacted with chat widget.. then load the js and css files.. click or mouseover.. etc.. on chat widget..

                    var ht_ctc_chat = document.querySelector('.ht-ctc-chat');
                    if (ht_ctc_chat) {
                        console.log('ht_ctc_chat exists..');

                        // one runs once but for each event type..
                        // keydown keyup keypress
                        $('.ht-ctc-chat').one('click touchstart mouseover focus', function () {
                            console.log('click touchstart mouseover focus keydown keyup keypress..');
                            append_intltel_files();
                        });


                    }

                }

                function no_delay() {
                    // required files are loaded from wp enqueue..
                    // intl-init.js file is already loaded from wp_enqueue.. it triggers intl_init() function..
                    console.log('no_delay()');
                }


                function append_intltel_files() {
                    console.log('append intl js and css..');

                    // is_append_intltel_files
                    if ('y' == is_append_intltel_files) {
                        console.log('append_intltel_files already called..');
                        return;
                    }
                    is_append_intltel_files = 'y';

                    // intl_init_js: intl-init.js. (intl_init() function is called from this file..)
                    if (ht_ctc_variables.intl_init_js) {
                        console.log('intl_init_js: ' + ht_ctc_variables.intl_init_js);
                        var script = document.createElement('script');
                        script.src = ht_ctc_variables.intl_init_js;
                        document.head.appendChild(script);
                    }

                    // intl_css: intl-tel-input css
                    if (ht_ctc_variables.intl_css) {
                        console.log('intl_css: ' + ht_ctc_variables.intl_css);
                        var link = document.createElement('link');
                        link.rel = 'stylesheet';
                        link.href = ht_ctc_variables.intl_css;
                        document.head.appendChild(link);
                    }

                    // intl_js: intl-tel-input js
                    if (ht_ctc_variables.intl_js) {
                        console.log('intl_js: ' + ht_ctc_variables.intl_js);
                        var script = document.createElement('script');
                        script.src = ht_ctc_variables.intl_js;
                        document.head.appendChild(script);
                    }

                }


            }

        }


        


    });
})(jQuery);