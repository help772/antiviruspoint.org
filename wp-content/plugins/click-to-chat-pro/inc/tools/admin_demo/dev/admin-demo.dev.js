(function () {

    var loop = 0;

    function waitForJQuery(callback) {
        if (typeof jQuery !== 'undefined') {
            // If jQuery is already loaded, execute the callback
            console.log('jQuery is already loaded');
            callback();
        } else {
            console.log('loop: ', loop);

            // 200 * 200ms = 40s
            if (loop < 200) {
                // If jQuery is not loaded, retry after a short delay
                setTimeout(function () {
                    console.log('Waiting for jQuery to load...');
                    waitForJQuery(callback);
                    loop++;
                }, 200); // Retry every 100ms
            }
        }
    }

    function initScript() {
        console.log('jQuery is loaded, running the script...');


        (function ($) {
            // Ready function
            $(function () {


                console.log('Document is ready');
                // greetings form - specific


                function demo_greetings_form() {
                    // Add browser-default class name to all input fields in .ctc_g_content element
                    $('.ctc_g_content input').addClass('browser-default');

                    // Update styles for checkboxes
                    $('.ctc_g_content input[type="checkbox"]').css({
                        'position': 'unset',
                        'opacity': 'unset',
                    });
                }
                demo_greetings_form();



            });
        })(jQuery);
    }

    // Wait for jQuery to load, then run the script
    waitForJQuery(initScript);
})();
