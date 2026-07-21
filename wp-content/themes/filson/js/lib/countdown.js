(function($) {
 
    $.fn.showclock = function() {

        var currentDate = new Date();
        var fieldDate = $(this).data('date').split('-');
        var text_days = $(this).data('days');
        var text_hours = $(this).data('hours');
        var text_minutes = $(this).data('minutes');
        var text_seconds = $(this).data('seconds');
        var fieldTime = [0, 0];
        if ($(this).data('time') != undefined)
            fieldTime = $(this).data('time').split(':');
        var futureDate = new Date(fieldDate[0], fieldDate[1] - 1, fieldDate[2], fieldTime[0], fieldTime[1]);
        var seconds = futureDate.getTime() / 1000 - currentDate.getTime() / 1000;

        if (seconds <= 0 || isNaN(seconds)) {
            this.hide();
            return this;
        }

        var days = Math.floor(seconds / 86400);
        seconds = seconds % 86400;

        var hours = Math.floor(seconds / 3600);
        seconds = seconds % 3600;

        var minutes = Math.floor(seconds / 60);
        seconds = Math.floor(seconds % 60);

        var html = "";
        if (days !== 0) {
            html += '<div class="hw-cd-item">';
            html += '<span>' + days + '</span>';
            html += '<span>' + text_days + '</span>';
            html += "</div>";

        }

        html += '<div class="hw-cd-item">';
        html += '<span>' + hours + '</span>';
        html += '<span>' + text_hours + '</span>';
        html += "</div>";		
		
        html += '<div class="hw-cd-item">';
        html += '<span>' + minutes + '</span>';
        html += '<span>' + text_minutes + '</span>';
        html += "</div>";

        html += '<div class="hw-cd-item">';
        html += '<span>' + seconds + '</span>';
        html += '<span>' + text_seconds + '</span>';
        html += "</div>";






        this.html(html);
    };

    $.fn.hexwp_countdown = function() {
        $(this).find(".hw-countdown").each(function() {
            var el = $(this);
            el.showclock();

            setInterval(function() {
                 el.showclock();
            }, 1000);
        });

    };
}(jQuery));