(function($) {

    $.fn.ZooMove = function(options) {

         var zoo = $.extend({
            image: '',
            cursor: 'false',
            scale: '3',
            move: 'true',
            over: 'false'
        }, options);

        // cursor config
        if ($(this).attr('data-zoo-cursor')) {
            zoo.cursor = $(this).attr('data-zoo-cursor');
        }
        if (zoo.cursor === 'true') {
            zoo.cursor = 'pointer';
        } else {
            zoo.cursor = 'default';
        }

        this
            .each(function() {
                var thisZoo = $(this); // cache current jquery zoo element

                // if over exist and over true
                if (thisZoo.attr('data-zoo-over')) {
                    zoo.overD = thisZoo.attr('data-zoo-over');
                } else {
                    zoo.overD = zoo.over;
                }

                if (zoo.overD === 'true') {
                    thisZoo.css({
                        'overflow': 'visible',
                        'z-index': '100'
                    });
                }

                // if image exist
                if ($(this).find('img').attr('src')) {
                    zoo.imageD = $(this).find('img').attr('src');
                } else {
                    zoo.imageD = zoo.image;
                }

                // create image element background
                thisZoo
                    .append('<div class="zoo-img"></div>')
                    .children('.zoo-img')
                    .css({
                        'background-image': 'url(' + zoo.imageD + ')',
                        'cursor': zoo.cursor
                    });
            })

        .on('mouseover', function(e) {
            var thisZoo = $(this); // cache current jquery zoo element
            e.preventDefault();

             if (thisZoo.attr('data-zoo-scale')) {
                zoo.scaleD = thisZoo.attr('data-zoo-scale');
            } else {
                zoo.scaleD = zoo.scale;
            }

             if (thisZoo.attr('data-zoo-move')) {
                zoo.moveD = thisZoo.attr('data-zoo-move');
            } else {
                zoo.moveD = zoo.move;
            }

            // change scale
            thisZoo
                .children('.zoo-img')
                .css({
                    'transform': 'scale(' + zoo.scaleD + ')'
                });
        })

        .on('mousemove', function(e) {
            var thisZoo = $(this); 
            e.preventDefault();

             if (zoo.moveD === 'true') {
                 thisZoo
                    .children('.zoo-img')
                    .css({
                        'transform-origin':
                            ((e.pageX - thisZoo.offset().left) / thisZoo.width()) * 100 + '% ' +
                            ((e.pageY - thisZoo.offset().top) / thisZoo.height()) * 100 + '%'
                    });
            }
        })

        .on('mouseout', function(e) {
            var thisZoo = $(this);  
            e.preventDefault();

             thisZoo
                .children('.zoo-img')
                .css({
                    'transform': 'scale(1)'
                });

        });

    };

}(jQuery));
/******************************************************************************************************************************************************
******************************************************************************************************************************************************

																	 function	Zoomove
																		
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/ 
(function($) {
    $.fn.hexwp_zoomove = function() {
        jQuery(document).on("click", ".slick-slide", function(e) {
            e.preventDefault();


            $(this).parent().find('.hw-slick-current').removeClass('hw-slick-current');
            $(this).find('a').addClass('hw-slick-current');
            var href = $(this).find('a').attr('href');
			
			if(href){
				var img= href;
			}else{
				var img = $(this).find('img').attr('src');
			}

            $('.hw-el-single-product .woocommerce-product-gallery__image').children().remove();
            $('.hw-el-single-product .woocommerce-product-gallery__image').children().remove();
            $('.hw-el-single-product .woocommerce-product-gallery__image').children().remove();
            $('.hw-el-single-product .woocommerce-product-gallery__image').append('<a  href="' + img + '"><img class="wp-post-image" src="' + img + '" ></a>');
            $('.hw-product-thumb-resize a').attr('href', img);

        });

        $('.woocommerce-product-gallery__image').hover(
            function() {
                $(this).ZooMove({
                    cursor: 'false',
                    scale: '2',
                    move: 'true',
                    over: 'false'

                });
            },
            function() {

                $(this).find('.zoo-img').remove();


            }
        );

    };
}(jQuery));