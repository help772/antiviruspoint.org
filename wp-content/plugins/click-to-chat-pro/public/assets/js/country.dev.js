(function ($) {
    // ready
    $(function () {

        console.log('country.dev.js');

        var ctc_values = {};

        
        var current_country = '';

        var ctc_setItem = '';
        var ctc_getItem = '';

        document.addEventListener("ht_ctc_event_display_country_base", function (e) {
            console.log('eventlistener: ht_ctc_event_display_country_base');

            // ctc = e.detail.ctc;
            // console.log(ctc);


            console.log(ctc_setItem);
            console.log(ctc_getItem);

            // functions
            ctc_setItem = e.detail.ctc_setItem;
            ctc_getItem = e.detail.ctc_getItem;

            console.log(ctc_setItem);
            console.log(ctc_getItem);

            if (typeof ht_ctc_variables !== "undefined") {
                
                // ctc_values = ht_ctc_variables;

                get_country();

            }

            
        });



        function update_country() {
            console.log('update_country');
            
            var xhr = $.get("https://ipinfo.io", function (data) {
                console.log('success');
                console.log(data);
            }, "jsonp")
                .done(function (resp) {
                    console.log('done');
                    console.log(resp);
                })
                .fail(function (resp) {
                    console.log('fail');
                    console.log(resp);
                })
                .always(function (resp) {
                    console.log('always');
                    console.log(resp);
                    current_country = (resp && resp.country) ? resp.country : '';
                    console.log('current_country: ' + current_country);
                    
                    var country_update_time = new Date().getTime();
                    console.log(country_update_time);

                    if (current_country && '' !== current_country) {
                        console.log('set country data to ht_ctc_variables from update_country');
                        ctc_setItem('current_country', current_country);
                        ctc_setItem('country_update_time', country_update_time);
                        console.log('added country data to ht_ctc_variables from update_country');
                        ht_ctc_variables.country_code = current_country;
                    } else {
                        console.log('error - country not found');
                        update_country_2();
                    }
            });


            // console.log(xhr);
            // console.log(typeof xhr);

            return current_country;
        }

        function update_country_2() {
            console.log('update_country_2');

            // using cloudflare geo location
            var xhr2 = $.get("https://www.cloudflare.com/cdn-cgi/trace", function (resp) {
                console.log('success');
                console.log(resp);
                var country_code = '';
                var lines = resp.split('\n');
                console.log(lines);
                lines.forEach(function (line) {
                    if (line.indexOf('loc') !== -1) {
                        var loc = line.split('=');
                        console.log(loc);
                        country_code = loc[1];
                    }
                });

                console.log('country_code: ' + country_code);

                current_country = (country_code) ? country_code : '';

                var country_update_time = new Date().getTime();
                console.log(country_update_time);

                if (current_country) {
                    console.log('set country data to ht_ctc_variables from update_country_2');
                    ctc_setItem('current_country', current_country);
                    ctc_setItem('country_update_time', country_update_time);
                    console.log('added country data to ht_ctc_variables from update_country_2');
                    ht_ctc_variables.country_code = current_country;
                } else {
                    console.log('error - country not found');
                }

                }, "text")
                    .fail(function (resp) {
                        console.log('fail');
                        console.log(resp);
                    });

            return current_country;
        }

        // current country.. 
        function get_country() {

            console.log('get_country');

            if (ctc_getItem('current_country') && ctc_getItem('country_update_time')) {

                var current_time = new Date().getTime();
                console.log('current_time: ' + current_time);

                var country_update_time = ctc_getItem('country_update_time');
                console.log('last time country_update_time: ' + country_update_time);

                var diff = (Number.isInteger(country_update_time)) ? Math.abs(current_time - country_update_time) : 43200000;
                console.log('diff: ' + diff + ' ms');

                if (diff < 43200000) {
                    // less than 12 hour
                    console.log('less than 12 hour');
                    current_country = ctc_getItem('current_country');

                    console.log('added country data to ht_ctc_variables from get_country()');
                    ht_ctc_variables.country_code = current_country;
                } else {
                    // more than 12 hour
                    console.log('more than 12 hour - update country');
                    current_country = update_country();
                    console.log('current_country: ' + current_country);
                }

            } else {
                console.log('calling update_country()');
                current_country = update_country();
            }

            console.log('current_country: ' + current_country);

            return current_country;
        }




    });
})(jQuery);