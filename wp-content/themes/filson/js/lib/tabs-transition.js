(function($) {
    $.fn.hexwp_tabs_transition = function() {

        $('.rez-transition').on('click', '.hw-tab-item:not(.hw-view-all)', function(index, element) {
            var transition = $(this).parents('.rez-transition');
            transition.addClass('rez-all-transition-active');

            transition.find('.hw-tab-active').removeClass('hw-tab-active');
            $(this).addClass('hw-tab-active');

            var data_filter = $(this).attr('data-filter');

            var list_html = transition.find('.hw-gap-warp').html();
            transition.find('.hw-gap-warp').append('<div class="hw-append">' + list_html + '</div>');
            var append = transition.find('.hw-append .hw-item-list');

            var list = transition.find('.hw-gap-warp > .hw-item-list');
            var old_list_height = list.height().toFixed(0);
            list.css({
                "--hw-old-list-ht": old_list_height + 'px'
            });
            list.find('.hw-item').each(function(i) {
                var count = i + 1;
                $(this).attr('data-count', count);

                var width = $(this).width();
                var height = $(this).height();
                if (transition.hasClass('macy-masonry')) {
                    var horizontal = $(this).position().left;
                    var vertical = $(this).position().top;

                } else {
                    var horizontal = $(this).offset().left - (window.scrollX);
                    var vertical = $(this).offset().top - (window.scrollY);
                }
                $(this).css({
                    "--hw-old-wt": width.toFixed(0) + 'px',
                    "--hw-old-ht": height.toFixed(0) + 'px',
                    "--hw-old-hor": horizontal.toFixed(0) + 'px',
                    "--hw-old-ver": vertical.toFixed(0) + 'px'
                });

            });


            setTimeout(function() {
                if (data_filter !== 'all') {
                    transition.addClass('hw-filter-not-all');
                }
                if (data_filter === 'all') {
                    transition.removeClass('hw-filter-not-all');
                }

                transition.addClass('rez-transition-active');

                append.find('.hw-item').each(function(i) {
                    var count = i + 1;

                    var main_post_item = transition.find('.hw-gap-warp > .hw-item-list .hw-item[data-count="' + count + '"]');
                    var append_item = $(this);

                    if (append_item.hasClass(data_filter) || data_filter === 'all') {
                        if (append_item.hasClass('rez-has-hide')) {
                            main_post_item.addClass('rez-to-show');
                        } else {
                            main_post_item.addClass('rez-to-move');
                        }
                        main_post_item.removeClass('rez-has-hide');
                        append_item.removeClass('rez-has-hide');
                    } else {
                        if (!append_item.hasClass('rez-has-hide')) {
                            main_post_item.addClass('rez-to-hide');
                        }
                        append_item.addClass('rez-has-hide');
                    }

                });
            }, 1);


            if (jQuery().hexwp_macy) {
                setTimeout(function() {
                    append.hexwp_macy();
                }, 2);
            }
            setTimeout(function() {
                append.find('.hw-item').each(function(i) {
                    var count = i + 1;
                    var main_post_item = transition.find('.hw-gap-warp > .hw-item-list .hw-item[data-count="' + count + '"]');
                    var append_item = $(this);
                    var width = append_item.width();
                    var height = append_item.height();

                    if (transition.hasClass('macy-masonry')) {
                        var horizontal = append_item.position().left;
                        var vertical = append_item.position().top;

                    } else {
                        var horizontal = append_item.offset().left - (window.scrollX);
                        var vertical = append_item.offset().top - (window.scrollY);

                    }
                    main_post_item.css({
                        "--hw-wt": width.toFixed(0) + 'px',
                        "--hw-ht": height.toFixed(0) + 'px',
                        "--hw-hor": horizontal.toFixed(0) + 'px',
                        "--hw-ver": vertical.toFixed(0) + 'px'
                    });


                });



                var list_height = append.height().toFixed(0);
                list.css({
                    "--hw-list-ht": list_height + 'px'
                });
                if (list_height < old_list_height) {
                    transition.addClass('hw-old-list-height');
                }

            }, 10);

            setTimeout(function() {
                transition.addClass('rez-animte');
            }, 15);

            setTimeout(function() {

                transition.find('.hw-gap-warp > .hw-item-list .hw-item').each(function(index, element) {
                    if ($(this).hasClass('rez-to-hide')) {

                        $(this).addClass('rez-has-hide');
                    }
                    $(this).removeClass('rez-to-move rez-to-show rez-to-hide');
                    $(this).removeAttr('data-count');
                    $(this).css({
                        '--hw-wt': '',
                        '--hw-ht': '',
                        '--hw-hor': '',
                        '--hw-ver': '',
                        '--hw-old-wt': '',
                        '--hw-old-ht': '',
                        '--hw-old-hor': '',
                        '--hw-old-ver': ''
                    });

                });
                transition.removeClass('rez-animte rez-transition-active rez-all-transition-active hw-old-list-height');
                $(this).css({
                    '--hw-old-list-ht': '',
                    '--hw-list-ht': ''
                });
                if (jQuery().hexwp_macy) {
                    list.hexwp_macy();
                }
                transition.find('.hw-item-list').removeAttr('style');
                transition.find('.hw-append').remove();

            }, 400);

        });

    };

})(jQuery);