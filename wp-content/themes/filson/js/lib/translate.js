(function($) {
    $.fn.hexwp_translate = function(input) {
       
        return input;
    }

    $.fn.hexwp_translatepad = function(n) {
        return $('body').hexwp_translate((n < 10) ? (n) : n);
    }


}(jQuery));