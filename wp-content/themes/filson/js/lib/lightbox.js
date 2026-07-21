(function($) {
    $.fn.hexwp_lightbox = function() {



	 $('.single .hw-single-content img').parent('a').each(function(i, el) {
 	var hexwp_href_value = this.href;
  	if (/\.(jpg|jpeg|png|gif|jpeg|bmp|webp)$/.test(hexwp_href_value)) {
		$(this).addClass('hw-singleimg-lightbox');
  	}  
	});

        if (jQuery('.hw-lightbox').hasClass('hw-lightbox-active')) {
            $('.hw-singleimg-lightbox').on('click', function(event) {
                $('.hw-lightbox').addClass('hw-lightbox-post-content')

                event.stopPropagation();
                event.preventDefault();
                var images = $('.hw-singleimg-lightbox');
                rd_lightboxActual = this;

				var href = this.getAttribute('href');
				if(href){
					var href_image= href;
				}else{
					var href_image= $(this).find('img').attr('src');
				}
                $('.hw-lightbox-targetimg').css('display', 'none').attr('src', href_image);
                $('.hw-lightbox-targetimg').one('load', function() {
                    $('.hw-lightbox-loading').css('display', 'none');
                    $(this).fadeIn();
                });

                var text = $(this).parent().find('.wp-caption-text').text();
                var display = 'block';
				/*
                if (text == null) {
                    text = '';
                    display = 'none';
                }
*/
                $('.hw-lightbox h3').text(text);
                $('.hw-lightbox').addClass('hw-multi-lightbox').slideToggle('fast');
                var actualId;
                $.each(images, function(index) {
                    if (rd_lightboxActual === images[index]) {
                        actualId = index + 1;
                    }
                });
                if (images.length == 1) {
                    $('.hw-lightbox-moreitems').css('display', 'none');
                }

                $('.hw-lightbox span').text($.rd_lightboxMessage(actualId, images.length));


            });
        }
		
		
		
		
		
        $.rd_lightboxMove = function(direction, allImages) {
            console.log(allImages);
            direction = (direction == 'next') ? 'next' : 'prev';
            var actualId;
            $.each(allImages, function(index) {
                if (allImages[index] === rd_lightboxActual) {
                    actualId = index;
                }
            });

            var iterator;
            if (direction == 'next') {
                iterator = actualId + 1;
                if (actualId == allImages.length - 1) {
                    iterator = 0;
                }
            } else if (direction == 'prev') {
                iterator = actualId - 1;
                if (actualId == 0) {
                    iterator = allImages.length - 1;
                }
            }

            var newImage = allImages[iterator];
			
			
				var href = newImage.getAttribute('href');
				if(href){
					var href_image= href;
				}else{
 					var href_image= $(newImage).find('img').attr('src');
				}
			
            $('.hw-lightbox-targetimg').css('display', 'none').attr('src',href_image);
            $('.hw-lightbox-loading').css('display', 'block');

            $('.hw-lightbox-targetimg').one('load', function() {
                $('.hw-lightbox-loading').css('display', 'none');
                $(this).css('display', 'inline-block');
            });

            var text = $(newImage).parent().find('.wp-caption-text').text();
            var display = 'block';

            if (text == null) {
                text = '';
                display = 'none';
            }
            $('.hw-lightbox h3').text(text).css('display', display);
            $('.hw-lightbox span').text($.rd_lightboxMessage(iterator + 1, allImages.length));
            rd_lightboxActual = newImage;
        };


        $(document).on('click', '.hw-lightbox-post-content.hw-multi-lightbox .hw-lightbox-prevbig', function() {
            $.rd_lightboxMove('prev', $('.hw-singleimg-lightbox'));
        });

        $(document).on('click', '.hw-lightbox-outer,.hw-lightbox-close', function() {
            $('.hw-lightbox').slideToggle('fast');
        });

        $(document).on('click', '.hw-lightbox-close,.hw-lightbox-outer', function() {
            $('.hw-lightbox').removeClass('hw-lightbox-singleimg').removeClass('hw-multi-lightbox');
            $('.hw-lightbox').removeClass('hw-lightbox-post-content').removeClass('hw-lightbox-gallery');
      

        });

        $(document).on('click', '.hw-multi-lightbox.hw-lightbox-post-content .hw-lightbox-nextbig,.hw-multi-lightbox.hw-lightbox-post-content .hw-lightbox-targetimg', function() {
            $.rd_lightboxMove('next', $('.hw-singleimg-lightbox'));
        });



        var rd_lightboxActual = null;

        $.rd_lightboxMessage = function(actual, last) {
            return '' + $('body').hexwp_translate(actual + ' / ' + last);
        };



        $(document).on('keydown', function(event) {
            if (event.keyCode == 37) {


                if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                    if ($('.hw-lightbox').hasClass('hw-lightbox-post-content')) {

                        $.rd_lightboxMove('prev', $('.hw-single-content img').parent('a'));
                    }
                }
            }
        });
        $(document).on('keydown', function(event) {

            if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                if ($('.hw-lightbox').hasClass('hw-lightbox-post-content')) {
                    if (event.keyCode == 39) {
                        $.rd_lightboxMove('next', $('.hw-single-content img').parent('a'));
                    }
                }
            }

        });






        if (jQuery('.hw-lightbox').hasClass('hw-lightbox-active')) {
            $('.hw-hover-img,.hw-single-product-image .hw-product-thumb-resize .hw-product-lightbox').on('click', function(event) {
                $('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)').each(function(i, el) {
					
                    var hexwp_href_value = this.href;
 					if(hexwp_href_value){
						var href_image=  this.href;
					}else{
						var href_image= $(this).find('img').attr('src');
					}					
					
                     if (/\.(jpg|jpeg|png|gif|jpeg|bmp|webp)$/.test(href_image)) {
                        $(this).addClass('hw-singleimg-lightbox');
                    }
                });

                event.stopPropagation();
                event.preventDefault();
                var rand = Math.floor((Math.random() * 10000000) + 1);

                $(this).parents('[class*="hw-el-"],.hw-product-thumbnails-warp').attr('data-lightbox', rand);


                $('.hw-lightbox').addClass('hw-lightbox-gallery').attr('data-rand', rand);
                var images = $(this).parents('[class*="hw-el-"],.hw-product-thumbnails-warp').find('.hw-singleimg-lightbox');
                rd_lightboxActual = this;
	
				var href = this.getAttribute('href');
				if(href){
					var href_image= href;
				}else{
					var href_image= $(this).find('img').attr('src');
				}
			
                $('.hw-lightbox-targetimg').css('display', 'none').attr('src', href_image);
                $('.hw-lightbox-targetimg').one('load', function() {
                    $('.hw-lightbox-loading').css('display', 'none');
                    $(this).slideToggle(0);
                });

                var text = $(this).next().html();
                var display = 'block';
                if (text == null) {
                    text = '';
                    display = 'none';
                }

                $('.hw-lightbox-title').text(text).css('display', display);
                $('.hw-lightbox').addClass('hw-multi-lightbox').slideToggle('fast');
                var actualId;
                $.each(images, function(index) {
                    if (rd_lightboxActual === images[index]) {
                        actualId = index + 1;
                    }
                });
                if (images.length == 1) {
                    $('.hw-lightbox-moreitems').css('display', 'none');


                }
                $('.hw-lightbox').attr('data-item', images.length);
                $('.hw-lightbox span').text($.rd_lightboxMessage(actualId, images.length));



            });
        }





        $(document).on('click', '.hw-multi-lightbox.hw-lightbox-gallery .hw-lightbox-prevbig', function() {
            var rand = $('.hw-lightbox').attr('data-rand');
            $.rd_lightboxMove('prev', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));
        });



        $(document).on('click', '.hw-multi-lightbox.hw-lightbox-gallery .hw-lightbox-nextbig,.hw-multi-lightbox.hw-lightbox-gallery .hw-lightbox-targetimg', function() {
            var rand = $('.hw-lightbox').attr('data-rand');
            $.rd_lightboxMove('next', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));
        });




        $(document).on('keydown', function(event) {
            if (event.keyCode == 39) {

                if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                    if ($('.hw-lightbox').hasClass('hw-lightbox-gallery')) {
                        var rand = $('.hw-lightbox').attr('data-rand');
                        $.rd_lightboxMove('next', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));
                    }
                }
            }
        });
        $(document).on('keydown', function(event) {
            if (event.keyCode == 37) {

                if ($('.hw-lightbox').hasClass('hw-multi-lightbox')) {
                    if ($('.hw-lightbox').hasClass('hw-lightbox-gallery')) {


                        var rand = $('.hw-lightbox').attr('data-rand');
                        $.rd_lightboxMove('prev', $('[data-lightbox="' + rand + '"]').find('.hw-hover-img,.hw-product-lightbox:not(.hw-slick-current)'));

                    }
                }

            }
        });



    };
}(jQuery));