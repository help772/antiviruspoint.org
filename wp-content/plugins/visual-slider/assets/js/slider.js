(function ($, undefined) {
 "use strict";
 
    var defaults = {
        item: 1,
        autoWidth: false,
        slideMove: 1,
        slideMargin: 0,
        addClass: '',
        mode: 'slide',
        useCSS: true,
        cssEasing: 'linear', //'cubic-bezier(0.25, 0, 0.25, 1)',
        easing: 'linear', //'for jquery animation',//
        speed: 400, //ms'
        auto: true,
        pauseOnHover: true,
        loop: false,
        slideEndAnimation: true,
        pause: 2000,
        keyPress: false,
        controls: false,
        timer: false,
        prevHtml: '',
        nextHtml: '',
        rtl: false,
        between: 0,
        adaptiveHeight: false,
        vertical: false,
        verticalHeight: 500,
        vThumbWidth: 160,
        thumbItem: 10,
        pager: true,
        gallery: false,
        gallery_thumb: false,
		pager_position:'top',
        gallery_thumb_text: false,
        gallery_text: false,
        gallery_inner_bottom_text: false,
         galleryMargin: 0,
        thumbMargin: 0,
        currentPagerPosition: 'middle',
        enableTouch: true,
        enableDrag: true,
        freeMove: true,
        swipeThreshold: 40,
        responsive: [],
        /* jshint ignore:start */
        onBeforeStart: function ($el) {},
        onSliderLoad: function ($el) {},
        onSliderLoad2: function ($el) {},
        onBeforeSlide: function ($el, scene) {
			
			},
        onAfterSlide: function ($el, scene) {},
        onBeforeNextSlide: function ($el, scene) {},
        onBeforePrevSlide: function ($el, scene) {}
        /* jshint ignore:end */
    };
    $.fn.vs_lightSlider = function (options) {
		
 
   			
     

        if (this.length === 0) {
            return this;
        }

        if (this.length > 1) {
            this.each(function () {
                $(this).lightSlider(options);
            });
            return this;
        }

        var plugin = {},
            settings = $.extend(true, {}, defaults, options),
            settingsTemp = {},
            $el = this;
        plugin.$el = this;

        if (settings.mode === 'fade') {
            settings.vertical = false;
        }
        var $children = $el.children(),
            windowW = $(window).width(),
            breakpoint = null,
            resposiveObj = null,
            length = 0,
            w = 0,
            on = false,
            elSize = 0,
            $slide = '',
            scene = 0,
            property = (settings.vertical === true) ? 'height' : 'width',
            gutter = (settings.vertical === true) ? 'margin-bottom' : 'margin-right',
            slideValue = 0,
            pagerWidth = 0,
            slideWidth = 0,
            thumbWidth = 0,
            interval = null,
            isTouch = ('ontouchstart' in document.documentElement);
        var refresh = {};

        refresh.chbreakpoint = function () {
 
 
	  
            windowW = $(window).width();
  			 
			
			
            if (settings.responsive.length) {
                var item;
                if (settings.autoWidth === false) {
                    item = settings.item;
                }
                if (windowW < settings.responsive[0].breakpoint) {
					
                    for (var i = 0; i < settings.responsive.length; i++) {
                        if (windowW < settings.responsive[i].breakpoint) {
                            breakpoint = settings.responsive[i].breakpoint;
                            resposiveObj = settings.responsive[i];
                        }
                    }
                }
                if (typeof resposiveObj !== 'undefined' && resposiveObj !== null) {
                    for (var j in resposiveObj.settings) {
                        if (resposiveObj.settings.hasOwnProperty(j)) {
                            if (typeof settingsTemp[j] === 'undefined' || settingsTemp[j] === null) {
                                settingsTemp[j] = settings[j];
                            }
                            settings[j] = resposiveObj.settings[j];
                        }
                    }
                }
                if (!$.isEmptyObject(settingsTemp) && windowW > settings.responsive[0].breakpoint) {
                    for (var k in settingsTemp) {
                        if (settingsTemp.hasOwnProperty(k)) {
                            settings[k] = settingsTemp[k];
                        }
                    }
                }
                if (settings.autoWidth === false) {
                    if (slideValue > 0 && slideWidth > 0) {
                        if (item !== settings.item) {
                            scene = Math.round(slideValue / ((slideWidth + settings.slideMargin) * settings.slideMove));
                        }
                    }
                }
            }
			
			
	 	
        };
 		 
 
 
        refresh.calSW = function () {
            if (settings.autoWidth === false) {
                slideWidth = (elSize - ((settings.item * (settings.slideMargin)) - settings.slideMargin)) / settings.item;
            }
        };

        refresh.calWidth = function (cln) {
            var ln = cln === true ? $slide.find('.vs-slide').length : $children.length;
            if (settings.autoWidth === false) {
                w = ln * (slideWidth + settings.slideMargin);
            } else {
                w = 0;
                for (var i = 0; i < ln; i++) {
                    w += (parseInt($children.eq(i).width()) + settings.slideMargin);
                }
            }
            return w;
        };
			
		  
		
        plugin = {
            doCss: function () {
                var support = function () {
                    var transition = ['transition', 'MozTransition', 'WebkitTransition', 'OTransition', 'msTransition', 'KhtmlTransition'];
                    var root = document.documentElement;
                    for (var i = 0; i < transition.length; i++) {
                        if (transition[i] in root.style) {
                            return true;
                        }
                    }
                };
                if (settings.useCSS && support()) {
                    return true;
                }
                return false;
            },
            keyPress: function () {
                if (settings.keyPress) {
                    $(document).on('keyup.vs-slider', function (e) {
                        if (!$(':focus').is('input, textarea')) {
                            if (e.preventDefault) {
                                e.preventDefault();
                            } else {
                                e.returnValue = false;
                            }
                            if (e.keyCode === 37) {
                                $el.goToPrevSlide();
                            } else if (e.keyCode === 39) {
                                $el.goToNextSlide();
                            }
                        }
                    });
                }
            },
 
            controls: function () {
                if (settings.controls) {
                	 
					
					
                   // $el.after('<div class="lSAction"><a class="lSPrev">' + settings.prevHtml + '</a><a class="lSNext">' + settings.nextHtml + '</a></div>');
                     
                    $slide.parents("[class*='vs-custom-slider']").find("[class*='arrow-next'],[class*='lSNext']").on('click', function (e) {
                        if (e.preventDefault) {
                            e.preventDefault();
 
                        } else {
                            e.returnValue = false;
                        }
                     
                            $el.goToNextSlide();
 
                         return false;
                    });
					$slide.parents("[class*='vs-custom-slider']").find("[class*='arrow-prev'],[class*='lSPrev']").on('click', function (e) {
                        if (e.preventDefault) {
                            e.preventDefault();
 
                        } else {
                            e.returnValue = false;
                        }
						$el.goToPrevSlide();
				      	 
                        return false;
                    });
               
			   
			   
				 
			   
			   
			   
			   
			   
			    }
            }, 
	 
            initialStyle: function () {
                var $this = this;
                if (settings.mode === 'fade') {
                    settings.autoWidth = false;
                    settings.slideEndAnimation = false;
                 }
                if (settings.auto) {
                    settings.slideEndAnimation = false;
                }
                if (settings.autoWidth) {
                    settings.slideMove = 1;
                    settings.item = 1;
                }
                if (settings.loop) {
                    settings.slideMove = 1;
                    settings.freeMove = false;
                }
				
				
                 settings.onBeforeStart.call(this, $el);
				 
				
                refresh.chbreakpoint();
                $el.addClass('vs-slider').wrap('<div class="vs-slide-outer ' + settings.addClass + '"><div class="vs-slide-wrapper"></div></div>');
				
				if (settings.loop === false){
 				 if($el.parent().parent().find('.vs-pager').find('li:first-child').hasClass('active')){
					$el.parent().parent().parent().addClass('vs-hide-prev');
				 }else{
					$el.parent().parent().parent().removeClass('vs-hide-prev');
				 }
				if($el.parent().parent().find('.vs-pager').find('li:last-child').hasClass('active')){
					$el.parent().parent().parent().addClass('vs-hide-next');
				}else{
					$el.parent().parent().parent().removeClass('vs-hide-next');
				 }
 				
				}
 				
				
				
                $slide = $el.parent('.vs-slide-wrapper');
				
				
					 
                if (settings.rtl === true) {
                    $slide.parent().addClass('lSrtl');
                }
                if (settings.vertical) {
                    $slide.parent().addClass('vertical');
                    elSize = settings.verticalHeight;
                    $slide.css('height', elSize + 'px');
 

                } else {
                    elSize = $el.outerWidth();
                }
               $children.addClass('vs-slide');
				 
				
                if (settings.loop === true ) {
                    refresh.calSW();
                    refresh.clone = function () {
                        if (refresh.calWidth(true) > elSize) {
                            /**/
                            var tWr = 0,
                                tI = 0;
                            for (var k = 0; k < $children.length; k++) {
                                tWr += (parseInt($el.find('.vs-slide').eq(k).width()) + settings.slideMargin);
                                tI++;
                                if (tWr >= (elSize + settings.slideMargin)) {
                                    break;
                                }
                            }
                            var tItem = settings.autoWidth === true ? tI : settings.item;
						if( settings.mode === 'slide'){
                            /**/
                            if (tItem < $el.find('.clone.left').length) {
                                for (var i = 0; i < $el.find('.clone.left').length - tItem; i++) {
                                    $children.eq(i).remove();
                                }
                            }
                            if (tItem < $el.find('.clone.right').length) {
                                for (var j = $children.length - 1; j > ($children.length - 1 - $el.find('.clone.right').length); j--) {
                                    scene--;
                                    $children.eq(j).remove();
                                }
                            }
                            /**/
                            for (var n = $el.find('.clone.right').length; n < tItem; n++) {
                                $el.find('.vs-slide').eq(n).clone().removeClass('vs-slide').addClass('clone right').appendTo($el);
                                scene++;
                            }
                            for (var m = $el.find('.vs-slide').length - $el.find('.clone.left').length; m > ($el.find('.vs-slide').length - tItem); m--) {
                                $el.find('.vs-slide').eq(m - 1).clone().removeClass('vs-slide').addClass('clone left').prependTo($el);
                            }
                            $children = $el.children();
                        } else {
                            if ($children.hasClass('clone')) {
                                $el.find('.clone').remove();
                                $this.move($el, 0);
                            }
                        }
						}
                    };
                    refresh.clone();
                }
                refresh.sSW = function () {
                    length = $children.length;
                    if (settings.rtl === true && settings.vertical === false) {
                        gutter = 'margin-left';
                    }
                    if (settings.autoWidth === false) {
                       // $children.css(property, slideWidth + 'px');
                    }
                 //   $children.css(gutter, settings.slideMargin + 'px');
                    w = refresh.calWidth(false);
                    $el.css(property, w + 'px');
                    if (settings.loop === true && settings.mode === 'slide') {
                        if (on === false) {
                            scene = $el.find('.clone.left').length;
                        }
                    }
                };
                refresh.calL = function () {
                    $children = $el.children();
                    length = $children.length;
                };
                if (this.doCss()) {
                    $slide.addClass('usingCss');
                }
                refresh.calL();
                if (settings.mode === 'slide') {
                    refresh.calSW();
                    refresh.sSW();
                    if (settings.loop === true) {
                        slideValue = $this.slideValue();
                        this.move($el, slideValue);
                    }
				 
                    if (settings.vertical === false) {
                        this.setHeight($el, false);
                    }
 
                } else {
                    this.setHeight($el, true);
                    $el.addClass('vs-mode-fade');
                    if (!this.doCss()) {
                        $children.fadeOut(0);
                        $children.eq(scene).fadeIn(0);
                    }
                }
				

						 	$el.parents('.vs-opacity-hide').removeClass('vs-opacity-hide').addClass('vs-opacity-show');
																	setTimeout(function(){  

						 	$el.parents('.vs_perview_global').addClass('vs-perview-show');
						 	$el.parents('.vs-visualslider').addClass('vs-arrows-show');
					},1100);

                if (settings.loop === true && settings.mode === 'slide') {

                    $children.eq(scene).addClass('active');
						setTimeout(function(){  
                    $children.eq(scene).addClass('vs-animte-active');
						 setTimeout(function(){  
						 $children.eq(scene).removeClass('first-active');
						 },50);
					},1050);
				 
                 } else {
                    $children.first().addClass('active');
					//if($children.first().hasClass('first-active')){
					///-
					setTimeout(function(){  
                    $children.first().addClass('vs-animte-active');
						 setTimeout(function(){  
						 $children.first().removeClass('first-active');
						 },50);
					},1050);
				//	}
					
					
 				 
					
					
                 }
				
 
            },
            pager: function () {
                var $this = this;
                refresh.createPager = function () {
                    thumbWidth = (elSize - ((settings.thumbItem * (settings.thumbMargin)) - settings.thumbMargin)) / settings.thumbItem;
					
					
					
					
                    var $children = $slide.find('.vs-slide');
 
					
					
					
                    var length = $slide.find('.vs-slide').length;
                    var i = 0,
                        pagers = '',
                        v = 0;
                    for (i = 0; i < length; i++) {
                        if (settings.mode === 'slide') {
                            // calculate scene * slide value
                            if (!settings.autoWidth) {
                                 v = i * ((slideWidth + settings.slideMargin) * settings.slideMove);
                            } else {
                                v += ((parseInt($children.eq(i).width()) + settings.slideMargin) * settings.slideMove);
                            }
                        }
						
						 
						 if (settings.gallery_thumb === true) {
 							var thumb_src = $children.eq(i).find('.vs-thumb img,.vs-image img').attr('src');
    							var thumb = '<div class="vs-thumbnail" ><span style="background-image:url('+thumb_src+');"></span></div>';
							 
							
						 }
						 /*
						if (settings.gallery_thumb_text === true) {
 							var thumb_src = $children.eq(i).find('.vs-thumb img').attr('src');
 							var thumb_width = $children.eq(i).find('.vs-thumb img').attr('width');
 							var thumb_height = $children.eq(i).find('.vs-thumb img').attr('height');
 							var thumb_ratio =  thumb_width/thumb_height;
							if(thumb_ratio > 1  ){
								var thumb_img ='vs-ratio-horizontal'; 
							} else{
								var thumb_img ='vs-ratio-vertical'; 
	 
							}
							if(settings.vertical === true){
															var thumbWidth_img = thumbWidth - 22;

   							}else{
							var thumbWidth_img = thumbWidth - 10;
							}
 							if(settings.vertical ==  true){
 							  var  thumbWidth_title = 'style=" width:calc(100% - ' +   thumbWidth_img + 20   +'px)"';  
  							}else{
								var thumbWidth_title  = 'style=" width:'+ (thumbWidth - 79) +'px"';
								
							}
  							var thumb_title = $children.eq(i).find('.vs-title a').html();
 							var thumb = '<div class="vs-slider-thumb '+thumb_img+'" style="width:' + thumbWidth_img +'px;" ><a href="#"><img src="' + thumb_src + '" width="'+thumb_width+'" height="'+thumb_height+'" /></a></div><div class="vs-title"  '+thumbWidth_title+' ><a href="#">'+thumb_title+'</a></div>';
						 }
						 */
						 /*
						if (settings.gallery_text === true) {
 							var thumb_title = $children.eq(i).find('.vs-title a').html();
 							var thumb = '<div class="vs-title" ><a href="#">'+thumb_title+'</a>';
						 }						 
						 */
  						if (settings.gallery === true) {
                            pagers += '<li style="' + property + ':' + thumbWidth + 'px;">'+thumb+'</li>';
 
                        } else {
                            pagers += '<li><a href="#">' + (i + 1) + '</a></li>';
                        }
                        if (settings.mode === 'slide') {
                            if ((v) >= w - elSize - settings.slideMargin) {
                                i = i + 1;
                                var minPgr = 2;
                                if (settings.autoWidth) {
                                    pagers += '<li><a href="#">' + (i + 1) + '</a></li>';
                                    minPgr = 1;
                                }
                                if (i < minPgr) {
                                    pagers = null;
                                    $slide.parent().addClass('noPager');
                                } else {
                                    $slide.parent().removeClass('noPager');
                                }
                                break;
                            }
                        }
                    }
                    var $cSouter = $slide.parent();
                    $cSouter.find('.vs-pager').html(pagers); 
  
 
  
			 
		
		/*
					$cSouter.find('.vs-slider-item').each(function(index, block) {
						var  swidth = $(this).find('.vs-slider-details').width();
 						var ratio = $(this).attr('data-ratio');
						var sheight = swidth /ratio;
						$(this).height(sheight);
 				 }); 	 
	 
				$cSouter.find('.vs-slider-item.vs-responsive-auto').each(function(index, element) {
					var pageWidth, pageHeight;
					
					var $page = $(this).find('.vs-slider-details-warp');
					// $page.attr('style',' width:' + width + '; height:' + height + ';');
					  //using underscore to delay resize method till finished resizing window
					  var maxWidth = $(this).find('.vs-slider-details').attr('width');
					   var maxHeight = $(this).find('.vs-slider-details').attr('height');
					
					 var basePage = {
					  width: $(this).find('.vs-slider-details').width(),
					  height: $(this).find('.vs-slider-details').height(),
					  scale: 1,
					  scaleX: 1,
					  scaleY: 1
					};
					var scaleX = 1, scaleY = 1;                      
					scaleX = basePage.width  / maxWidth;
					scaleY = basePage.height / maxHeight ;
					basePage.scaleX = scaleX;
					basePage.scaleY = scaleY;
					basePage.scale = (scaleX > scaleY) ? scaleY : scaleX;
					
					var res_width =   (maxWidth /basePage.width)*100 ;
					var res_height = (maxHeight  / basePage.height)*100 ;
				 
						
 					$page.attr('style', 'transform:translate(-50%,-50%) scale(' + basePage.scale + ');-webkit-transform:translate(-50%,-50%) scale(' + basePage.scale + ');-moz-transformtranslate(-50%,-50%) scale(' + basePage.scale + ');-o-transform:translate(-50%,-50%) scale(' + basePage.scale + ');-ms-transform:translate(-50%,-50%) scale(' + basePage.scale + ');width:' + res_width + '%;height:' + res_height + '%;');
			 
			 
				}); 	  
	 */
 
  
                    if (settings.gallery === true) {
                        if (settings.vertical === true) {
                            // set Gallery thumbnail width
                            $cSouter.find('.vs-pager').css('width', settings.vThumbWidth + 'px');
                        }
					 
                        pagerWidth = (i * (settings.thumbMargin + thumbWidth)) + 0.5;
                        $cSouter.find('.vs-pager').css({
                            property: pagerWidth + 'px',
                            'transition-duration': settings.speed + 'ms'
                        });
                        if (settings.vertical === true) {
							if(settings.rtl === true){
                            $slide.css('margin-left', (settings.vThumbWidth + settings.galleryMargin) + 'px');
							} else{
                            $slide.css('margin-right', (settings.vThumbWidth + settings.galleryMargin) + 'px');
							}
                        }
                        $cSouter.find('.vs-pager').css(property, pagerWidth + 'px');
                    }
                    var $pager = $cSouter.find('.vs-pager').find('li');
                    $pager.first().addClass('active');
                    $pager.on('click', function () {
                        if (settings.loop === true && settings.mode === 'slide') {
                            scene = scene + ($pager.index(this) - $cSouter.find('.vs-pager').find('li.active').index());
                        } else {
                            scene = $pager.index(this);
                        }
                        $el.mode(false);
                        if (settings.gallery === true) {
                            $this.slideThumb();
                        }
                        return false;
                    });
                };
 
 
				
				
                if (settings.pager) {
                    var cl = 'lSpg';
                    if (settings.gallery) {
                        cl = 'vs-gallery';
						$slide.after('<div class="vs-thumbnail-list-warp"><ul class="vs-pager ' + cl + ' " ></ul></div>');
                     }else{
						$slide.after('<ul class="vs-pager lSpg" ></ul>');
					 }
					
					/*
					if (settings.gallery_thumb === true) {
                 		  var vs_gallery_thumb = 'vs-gallery-thumb';
					}
					if (settings.gallery_thumb_text === true) {
                 		  var vs_gallery_thumb = 'vs-gallery-text';
					}*/
				 
						
                     var gMargin = (settings.vertical) ? 'margin-left' : 'margin-top';
                     refresh.createPager();
                }
				

  				
 				
				
 
	
                setTimeout(function () {
                      refresh.init();
                }, 0);
				
				
			 
            }, 
             setHeight: function (ob, fade) {
                var obj = null,
                    $this = this;
                if (settings.loop) {
                    obj = ob.children('.vs-slide ').first();
                } else {
                    obj = ob.children().first();
                }
                var setCss = function () {
                    var tH = obj.outerHeight(),
                        tP = 0,
                        tHT = tH;
                    if (fade) {
                        tH = 0;
                        tP = ((tHT) * 100) / elSize;
                    }
					/*
                    ob.css({
                        'height': tH + 'px',
                        'padding-bottom': tP + '%'
                    });*/
                };
                setCss();
                if (obj.find('img').length) {
                    if ( obj.find('img')[0].complete) {
                        setCss();
                        if (!interval) {
                            $this.auto();
                        }   
                    }else{
                        obj.find('img').load(function () {
                            setTimeout(function () {
                                setCss();
                                if (!interval) {
                                    $this.auto();
                                }
                            }, 100);
                        });
                    }
                }else{
                    if (!interval) {
                        $this.auto();
                    }
                }
            }, 
            active: function (ob, t) {
                if (this.doCss() && settings.mode === 'fade') {
                    $slide.addClass('on');
                  }
						 
                var sc = 0;
                if (scene * settings.slideMove < length) {
                     ob.removeClass('active vs-animte-active');
 
                    if (!this.doCss() && settings.mode === 'fade' && t === false) {
                        ob.fadeOut(settings.speed);
                    }
                    if (t === true) {
                        sc = scene;
                    } else {
                        sc = scene * settings.slideMove;
                    }
                    //t === true ? sc = scene : sc = scene * settings.slideMove;
                    var l, nl;
                    if (t === true) {
                        l = ob.length;
                        nl = l - 1;
                        if (sc + 1 >= l) {
                            sc = nl;
                        }
                    }
                    if (settings.loop === true && settings.mode === 'slide') {
                        //t === true ? sc = scene - $el.find('.clone.left').length : sc = scene * settings.slideMove;
                        if (t === true) {
                            sc = scene - $el.find('.clone.left').length;
                        } else {
                            sc = scene * settings.slideMove;
                        }
                        if (t === true) {
                            l = ob.length;
                            nl = l - 1;
                            if (sc + 1 === l) {
                                sc = nl;
                            } else if (sc + 1 > l) {
                                sc = 0;
                            }
                        }
                    }

                    if (!this.doCss() && settings.mode === 'fade' && t === false) {
                        ob.eq(sc).fadeIn(settings.speed);
                    }
					
                  
                    ob.eq(sc).addClass('active');
					if(ob.eq(sc).hasClass('first-active')){
					setTimeout(function(){  
                     ob.eq(sc).addClass('vs-animte-active');
						setTimeout(function(){  
							ob.eq(sc).removeClass('first-active');
						},50);
					},1050);
					}else{
                     ob.eq(sc).addClass('vs-animte-active');
					}

 				 
                 } else {
                    ob.removeClass('active vs-animte-active');
                     ob.eq(ob.length - 1).addClass('active');
					 
			
                    if (!this.doCss() && settings.mode === 'fade' && t === false) {
                        ob.fadeOut(settings.speed);
                        ob.eq(sc).fadeIn(settings.speed);
                    }
                }
				 
				
						
            },
            move: function (ob, v) {
                if (settings.rtl === true) {
                    v = -v;
                }
                if (this.doCss()) {
                    if (settings.vertical === true) {
                		if (settings.rtl === true) {
							ob.css({
								'transform': 'translate3d(0px, ' + (v) + 'px, 0px)',
 							});
						}else{
						ob.css({
                            'transform': 'translate3d(0px, ' + (-v) + 'px, 0px)',
                         });
						}
						
                    } else {
                        ob.css({
                            'transform': 'translate3d(' + (-v) + 'px, 0px, 0px)',
                         });
                    }
                } else {
                    if (settings.vertical === true) {
                        ob.css('position', 'relative').animate({
                            top: -v + 'px'
                        }, settings.speed, settings.easing);
                    } else {
                        ob.css('position', 'relative').animate({
                            left: -v + 'px'
                        }, settings.speed, settings.easing);
                    }
                }
                var $thumb = $slide.parent().find('.vs-pager').find('li');
                this.active($thumb, true);
				if (settings.loop === false){
 				 if($slide.parent().find('.vs-pager').find('li:first-child').hasClass('active')){
					$slide.parent().parent().addClass('vs-hide-prev');
				 }else{
					$slide.parent().parent().removeClass('vs-hide-prev');
				 }
				if($slide.parent().find('.vs-pager').find('li:last-child').hasClass('active')){
					$slide.parent().parent().addClass('vs-hide-next');
				}else{
					$slide.parent().parent().removeClass('vs-hide-next');
				 }
 				
				}
            },
            fade: function () {
                this.active($children, false);
                var $thumb = $slide.parent().find('.vs-pager').find('li');
                this.active($thumb, true);
				
				if (settings.loop === false){
 				 if($slide.parent().find('.vs-pager').find('li:first-child').hasClass('active')){
					$slide.parent().parent().addClass('vs-hide-prev');
				 }else{
					$slide.parent().parent().removeClass('vs-hide-prev');
				 }
				if($slide.parent().find('.vs-pager').find('li:last-child').hasClass('active')){
					$slide.parent().parent().addClass('vs-hide-next');
				}else{
					$slide.parent().parent().removeClass('vs-hide-next');
				 }
 				
				}
				
            },
            slide: function () {
                var $this = this;
                refresh.calSlide = function () {
                    if (w > elSize) {
                        slideValue = $this.slideValue();
                        $this.active($children, false);
                        if ((slideValue) > w - elSize - settings.slideMargin) {
                            slideValue = w - elSize - settings.slideMargin;
                        } else if (slideValue < 0) {
                            slideValue = 0;
                        }
                        $this.move($el, slideValue);
                        if (settings.loop === true && settings.mode === 'slide') {
                            if (scene >= (length - ($el.find('.clone.left').length / settings.slideMove))) {
                                $this.resetSlide($el.find('.clone.left').length);
                            }
                            if (scene === 0) {
                                $this.resetSlide($slide.find('.vs-slide').length);
                            }
                        }
                    }
                };
                refresh.calSlide();
            },
            resetSlide: function (s) {
                var $this = this;
               // $slide.find('.lSAction a').addClass('disabled');
                setTimeout(function () {
                    scene = s;
                    $slide.css('transition-duration', '0ms');
                    slideValue = $this.slideValue();
                    $this.active($children, false);
                    plugin.move($el, slideValue);
                    setTimeout(function () {
                        $slide.css('transition-duration', settings.speed + 'ms');
                        $slide.find('.lSAction a').removeClass('disabled');
                    }, 50);
                }, settings.speed + 100);
            },
            slideValue: function () {
                var _sV = 0;
                if (settings.autoWidth === false) {
                    _sV = scene * ((slideWidth + settings.slideMargin) * settings.slideMove);
                } else {
                    _sV = 0;
                    for (var i = 0; i < scene; i++) {
                        _sV += (parseInt($children.eq(i).width()) + settings.slideMargin);
                    }
                }
                return _sV;
            },
            slideThumb: function () {
                var position;
                switch (settings.currentPagerPosition) {
                case 'left':
                    position = 0;
                    break;
                case 'middle':
                    position = 0;
                    break;
                case 'right':
                    position = elSize - thumbWidth;
                }
                var sc = scene - $el.find('.clone.left').length;
                var $pager = $slide.parent().find('.vs-pager');
                if (settings.mode === 'slide' && settings.loop === true) {
                    if (sc >= $pager.children().length) {
                        sc = 0;
                    } else if (sc < 0) {
                        sc = $pager.children().length;
                    }
                }
                var thumbSlide = sc * ((thumbWidth + settings.thumbMargin)) - (position);
                if ((thumbSlide + elSize) > pagerWidth) {
                    thumbSlide = pagerWidth - elSize - settings.thumbMargin;
                }
                if (thumbSlide < 0) {
                    thumbSlide = 0;
                }
                this.move($pager, thumbSlide);
            },
            auto: function () {
                if (settings.auto) {
 				clearInterval(interval);
                    interval = setInterval(function () {
  
                        $el.goToNextSlide();
                    }, settings.pause);
                }
            },
            pauseOnHover: function(){
                var $this = this;
                if (settings.auto && settings.pauseOnHover ) {
                    $slide.on('mouseenter', function(){
                        $(this).addClass('ls-hover');
                        $el.pause();
                        settings.auto = true;
                    });
                    $slide.on('mouseleave',function(){
                        $(this).removeClass('ls-hover');
                        if (!$slide.find('.vs-slider').hasClass('vs-grabbing')) {
                            $this.auto();
                        }
                    });
                }
            },
            touchMove: function (endCoords, startCoords) {
                $slide.css('transition-duration', '0ms');
                if (settings.mode === 'slide') {
                    var distance = endCoords - startCoords;
                    var swipeVal = slideValue - distance;
                    if ((swipeVal) >= w - elSize - settings.slideMargin) {
                        if (settings.freeMove === false) {
                            swipeVal = w - elSize - settings.slideMargin;
                        } else {
                            var swipeValT = w - elSize - settings.slideMargin;
                            swipeVal = swipeValT + ((swipeVal - swipeValT) / 5);

                        }
                    } else if (swipeVal < 0) {
                        if (settings.freeMove === false) {
                            swipeVal = 0;
                        } else {
                            swipeVal = swipeVal / 5;
                        }
                    }
                    this.move($el, swipeVal);
                }
            },

            touchEnd: function (distance) {
                $slide.css('transition-duration', settings.speed + 'ms');
                if (settings.mode === 'slide') {
                    var mxVal = false;
                    var _next = true;
                    slideValue = slideValue - distance;
                    if ((slideValue) > w - elSize - settings.slideMargin) {
                        slideValue = w - elSize - settings.slideMargin;
                        if (settings.autoWidth === false) {
                            mxVal = true;
                        }
                    } else if (slideValue < 0) {
                        slideValue = 0;
                    }
                    var gC = function (next) {
                        var ad = 0;
                        if (!mxVal) {
                            if (next) {
                                ad = 1;
                            }
                        }
                        if (!settings.autoWidth) {
                            var num = slideValue / ((slideWidth + settings.slideMargin) * settings.slideMove);
                            scene = parseInt(num) + ad;
                            if (slideValue >= (w - elSize - settings.slideMargin)) {
                                if (num % 1 !== 0) {
                                    scene++;
                                }
                            }
                        } else {
                            var tW = 0;
                            for (var i = 0; i < $children.length; i++) {
                                tW += (parseInt($children.eq(i).width()) + settings.slideMargin);
                                scene = i + ad;
                                if (tW >= slideValue) {
                                    break;
                                }
                            }
                        }
                    };
                    if (distance >= settings.swipeThreshold) {
                        gC(false);
                        _next = false;
                    } else if (distance <= -settings.swipeThreshold) {
                        gC(true);
                        _next = false;
                    }
                    $el.mode(_next);
                    this.slideThumb();
                } else {
                    if (distance >= settings.swipeThreshold) {
                        $el.goToPrevSlide();
 						
                    } else if (distance <= -settings.swipeThreshold) {
                        $el.goToNextSlide();
                   }
                }
            },



            enableDrag: function () {
                var $this = this;
                if (!isTouch) {
                    var startCoords = 0,
                        endCoords = 0,
                        isDraging = false;
                    $slide.find('.vs-slider').addClass('vs-grab');
                    $slide.on('mousedown', function (e) {
                        if (w < elSize) {
                            if (w !== 0) {
                                return false;
                            }
                        }
                        if ($(e.target).attr('class') !== ('lSPrev') && $(e.target).attr('class') !== ('lSNext')) {
                            startCoords = (settings.vertical === true) ? e.pageY : e.pageX;
                            isDraging = true;
                            if (e.preventDefault) {
                                e.preventDefault();
  
                            } else {
                                e.returnValue = false;
 
                            }
                            // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                            $slide.scrollLeft += 1;
                            $slide.scrollLeft -= 1;
                            // *
                            $slide.find('.vs-slider').removeClass('vs-grab').addClass('vs-grabbing');
 
                            clearInterval(interval);
																				 
 
                        }
                    });
                    $(window).on('mousemove', function (e) {
						
                       
						 
							if (isDraging) {
								endCoords = (settings.vertical === true) ? e.pageY : e.pageX;
								if( settings.rtl === true && settings.vertical === false){
									$this.touchMove(startCoords, endCoords);
								}else{
									$this.touchMove(endCoords, startCoords);

 								}
							}
						
                    });
                    $(window).on('mouseup', function (e) {
                        if (isDraging  ) {
                            $slide.find('.vs-slider').removeClass('vs-grabbing').addClass('vs-grab');
                            isDraging = false;
                            endCoords = (settings.vertical === true) ? e.pageY : e.pageX;
							
							if( settings.rtl === true && settings.vertical === false){
                            var distance = startCoords - endCoords;
							}else{
                           	 var distance = endCoords - startCoords;
							}
                            if (Math.abs(distance) >= settings.swipeThreshold) {
                                $(window).on('click.ls', function (e) {
                                    if (e.preventDefault) {
                                        e.preventDefault();
 
                                    } else {
                                        e.returnValue = false;
                                     }
                                    e.stopImmediatePropagation();
                                    e.stopPropagation();
                                    $(window).off('click.ls');
                                });
                            }

                            $this.touchEnd(distance);

                        }
                    });
                }
            },




            enableTouch: function () {
                var $this = this;
                if (isTouch) {
                    var startCoords = {},
                        endCoords = {};
                    $slide.on('touchstart', function (e) {
                        endCoords = e.originalEvent.targetTouches[0];
                        startCoords.pageX = e.originalEvent.targetTouches[0].pageX;
                        startCoords.pageY = e.originalEvent.targetTouches[0].pageY;
 
                        clearInterval(interval);
 							
 

                    });
                    $slide.on('touchmove', function (e) {
                        if (w < elSize) {
                            if (w !== 0) {
                                return false;
                            }
                        }
                        var orig = e.originalEvent;
                        endCoords = orig.targetTouches[0];
                        var xMovement = Math.abs(endCoords.pageX - startCoords.pageX);
                        var yMovement = Math.abs(endCoords.pageY - startCoords.pageY);
                        if (settings.vertical === true) {
                            if ((yMovement * 3) > xMovement) {
                                e.preventDefault();
                             }
                            $this.touchMove(endCoords.pageY, startCoords.pageY);
                        } else {
                            if ((xMovement * 3) > yMovement) {
                                e.preventDefault();
                             }
							
							if (settings.rtl === true) {
								$this.touchMove(startCoords.pageX, endCoords.pageX);
							}else{
                           	  $this.touchMove(endCoords.pageX, startCoords.pageX);
							}
						}

                    });
 					$slide.on('touchend', function () {
                        if (w < elSize) {
                            if (w !== 0) {
                                return false;
                            }
                        }
                        var distance;
                        if (settings.vertical === true) {
                            distance = startCoord.pageY - sendCoords.pageY;
                        } else {
							if (settings.rtl === true) {
                            		distance = startCoords.pageX - endCoords.pageX;
 							}else{
								distance = endCoords.pageX - startCoords.pageX;
							}
							
                         }
                        $this.touchEnd(distance);
                    });
                }
            },
            build: function () {
                var $this = this;
                $this.initialStyle();
                if (this.doCss()) {

                    if (settings.enableTouch === true) {
                        $this.enableTouch();
                    }
                    if (settings.enableDrag === true) {
                        $this.enableDrag();
                    }
                }

                $(window).on('focus', function(){
                    $this.auto();
                });
                
                $(window).on('blur', function(){
                    clearInterval(interval);
 
                });

                $this.pager();
                $this.pauseOnHover();
                $this.controls();
                $this.keyPress();
            }
        };
        plugin.build();
        refresh.init = function () {
            refresh.chbreakpoint();
            if (settings.vertical === true) {
                if (settings.item > 1) {
                    elSize = settings.verticalHeight;
                } else {
                    elSize = $children.outerHeight();
                }
                $slide.css('height', elSize + 'px');
            } else {
                elSize = $slide.outerWidth();
            }
            if (settings.loop === true && settings.mode === 'slide') {
               // refresh.clone();
            }
            refresh.calL();
            if (settings.mode === 'slide') {
              //  $el.removeClass('vs-mode-slide');
            }
            if (settings.mode === 'slide') {
                refresh.calSW();
                refresh.sSW();
            }
            setTimeout(function () {
                if (settings.mode === 'slide') {
                    $el.addClass('vs-mode-slide');
                }
            }, 1000);
            if (settings.pager) {
                refresh.createPager();
            }
            if (settings.adaptiveHeight === true && settings.vertical === false) {
               // $el.css('height', $children.eq(scene).outerHeight(true));
            }
            if (settings.adaptiveHeight === false) {
                if (settings.mode === 'slide') {
                    if (settings.vertical === false) {
                       // plugin.setHeight($el, false);
                    }else{
                        plugin.auto();
                    }
                } else {
                  //  plugin.setHeight($el, true);
                }
            }
            if (settings.gallery === true) {
                plugin.slideThumb();
            }
            if (settings.mode === 'slide') {
                plugin.slide();
            }
            if (settings.autoWidth === false) {
                if ($children.length <= settings.item) {
                    $slide.find('.lSAction').hide();
                } else {
                    $slide.find('.lSAction').show();
                }
            } else {
                if ((refresh.calWidth(false) < elSize) && (w !== 0)) {
                    $slide.find('.lSAction').hide();
                } else {
                    $slide.find('.lSAction').show();
                }
            }
        };
        $el.goToPrevSlide = function () {
            if (scene > 0) {
                settings.onBeforePrevSlide.call(this, $el, scene);
                scene--;
                $el.mode(false);
                if (settings.gallery === true) {
                    plugin.slideThumb();
                }
            } else {
                if (settings.loop === true) {
                    settings.onBeforePrevSlide.call(this, $el, scene);
                    if (settings.mode === 'fade') {
                        var l = (length - 1);
                        scene = parseInt(l / settings.slideMove);
                    }
                    $el.mode(false);
                    if (settings.gallery === true) {
                        plugin.slideThumb();
                    }
                }  
            }
        };
        $el.goToNextSlide = function () {
            var nextI = true;
            if (settings.mode === 'slide') {
                var _slideValue = plugin.slideValue();
                nextI = _slideValue < w - elSize - settings.slideMargin;
            }
            if (((scene * settings.slideMove) < length - settings.slideMove) && nextI) {
                settings.onBeforeNextSlide.call(this, $el, scene);
                scene++;
                $el.mode(false);
                if (settings.gallery === true) {
                    plugin.slideThumb();
                }
            } else {
                if (settings.loop === true) {
                    settings.onBeforeNextSlide.call(this, $el, scene);
                    scene = 0;
                    $el.mode(false);
                    if (settings.gallery === true) {
                        plugin.slideThumb();
                    }
                } 
            }
        };
        $el.mode = function (_touch) {
            if (settings.adaptiveHeight === true && settings.vertical === false) {
                $el.css('height', $children.eq(scene).outerHeight(true));
            }
            if (on === false) {
                if (settings.mode === 'slide') {
                    if (plugin.doCss()) {
                        $el.addClass('vs-mode-slide');
                        if (settings.speed !== '') {
                            $slide.css('transition-duration', settings.speed + 'ms');
                        }
                        if (settings.cssEasing !== '') {
                            $slide.css('transition-timing-function', settings.cssEasing);
                        }
                    }
                } else {
                    if (plugin.doCss()) {
                        if (settings.speed !== '') {
                            $el.css('transition-duration', settings.speed + 'ms');
                        }
                        if (settings.cssEasing !== '') {
                            $el.css('transition-timing-function', settings.cssEasing);
                        }
                    }
                }
            }
			
 					
			
                 settings.onBeforeSlide.call(this, $el, scene);
				 
				 
			 
            if (settings.mode === 'slide') {
                plugin.slide();
            } else {
                plugin.fade();
            }
            if (!$slide.hasClass('ls-hover')) {
                plugin.auto();
            }
            setTimeout(function () {
                     settings.onAfterSlide.call(this, $el, scene);
                
            }, settings.speed);
            on = true;
        };
        $el.play = function () {
            $el.goToNextSlide();
            settings.auto = true;
            plugin.auto();
        };
        $el.pause = function () {
            settings.auto = false;
 
            clearInterval(interval);

        };
		
		
		
        $el.refresh = function () {
            refresh.init();
        };
        $el.getCurrentSlideCount = function () {
            var sc = scene;
            if (settings.loop) {
                var ln = $slide.find('.vs-slide').length,
                    cl = $el.find('.clone.left').length;
                if (scene <= cl - 1) {
                    sc = ln + (scene - cl);
                } else if (scene >= (ln + cl)) {
                    sc = scene - ln - cl;
                } else {
                    sc = scene - cl;
                }
            }
            return sc + 1;
        }; 
        $el.getTotalSlideCount = function () {
            return $slide.find('.vs-slide').length;
        };
        $el.goToSlide = function (s) {
            if (settings.loop) {
                scene = (s + $el.find('.clone.left').length - 1);
            } else {
                scene = s;
            }
            $el.mode(false);
            if (settings.gallery === true) {
                plugin.slideThumb();
            }
        };
        $el.destroy = function () {
            if ($el.lightSlider) {
                $el.goToPrevSlide = function(){};
                $el.goToNextSlide = function(){};
                $el.mode = function(){};
                $el.play = function(){};
                $el.pause = function(){};
                $el.refresh = function(){};
                $el.getCurrentSlideCount = function(){};
                $el.getTotalSlideCount = function(){};
                $el.goToSlide = function(){}; 
                $el.lightSlider = null;
                refresh = {
                    init : function(){}
                };
                $el.parent().parent().find('.lSAction, .vs-pager').remove();
                $el.removeClass('vs-slider vs-mode-fade  vs-mode-slide vs-grab lsGrabbing leftEnd right').removeAttr('style').unwrap().unwrap();
                $el.children().removeAttr('style');
                $children.removeClass('vs-slide active vs-animte-active');
                $el.find('.clone').remove();
                $children = null;
                interval = null;
                on = false;
                scene = 0;
            }
			
 

        };
        setTimeout(function () {

            settings.onSliderLoad.call(this, $el);

        }, 10);
		
 
		 
        $(window).on('resize orientationchange', function (e) {
            setTimeout(function () {
 
                if (e.preventDefault) {
                    e.preventDefault();
                } else {
                    e.returnValue = false;
                }
                refresh.init();
 			 
				 
				 
            }, 200);
        });
        return this;
    };
}(jQuery));