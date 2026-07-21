jQuery(function($) {
	'use strict';
	function isset(variable){
		return  variable  !== "undefined" && variable  !== "0" && variable !== null && variable !== '';
	}
	
	function empty(variable){
		if(variable  === "undefined" && variable === null && variable === ''){
			return '';
		}else{
			return variable;
		}
	}
 
	 /*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Fold Hide
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/
 $.fn.vs_remove_add_error_loading = function() { 	
			var output ='';
 			output ='<div class="vs-errored">Error</div>';
	 	 
		 $('.vs-mouse-wait').append(output);
		  setTimeout(function(){ $('.vs-mouse-wait').remove() }, 2500);
 	};
 
	
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Hover 
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
 $.fn.vs_item_hover = function() { 	

$('.vs-slide-item .vs-slide-inner').each( function(i) {
 var count = i + 1;
 	$(this).append('<div class="vs_module_slide_top " data-count="'+count+'"><a class="vs_module_slide_options"></a><a class="vs_module_slide_duplicate"></a><a class="vs_module_slide_template_save vs_module_template_save" data-id="slide"></a><a class="vs_module_slide_remove"></a></div>');
});

 
 };
 
 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Global Slider
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
 function settingEmptyAndZero(obj) {
  for (var key in obj) {
    if (obj[key] === null || obj[key] === undefined || obj[key] === '') {
      delete obj[key];
    } else if ($.type(obj[key]) === 'object') {
      settingEmptyAndZero(obj[key]);
      var sum = Object.values(obj[key]).reduce(function(acc, val) {
        return acc + val;
      }, 0);
      if (sum === 0) {
        delete obj[key];
      }
    }
  }
  return obj;
}
	$.fn.vs_setting_json =  function() { 
		var data_option = $('#post').serializeJSON();
   	 	$('#vs_setting_json').val( '['+JSON.stringify(settingEmptyAndZero(data_option['vs_setting']))+']');
  	};
  

  	  $.fn.vs_reload_global_slider = function() {
			var data_key = Math.floor(Math.random() * 9999999999);
			var height = $(this).height();
			 $(this).height(height);
 	  $('.vs_perview_global').addClass('vs-active-loading').removeClass('vs-perview-show');
 	  $('.vs_perview_global_content').attr('data-key',data_key).addClass('vs-loading');
 
		 setTimeout(function(){
  		var data_setting_json = $('#vs_setting_json').val();
 		var data_slide_json = $('#vs_slide').val();
    		  $.ajax({
			type: 'POST',
  			url: visualslider.ajaxurl,
			data : {
				action : 'vs_perview_global',
				vs_setting_json : data_setting_json,
				vs_slide : data_slide_json,
				post_id : visualslider.post_id,
				_wpnonce : visualslider.nonce,
  			},
			success:function(data) {
 				if(data.length){
				$('.vs_perview_global_content[data-key="'+data_key+'"]').children().remove();
				 $('.vs_perview_global_content[data-key="'+data_key+'"]').removeClass('vs-loading').append(data);
 					setTimeout(function(){  
						$('body').vs_custom_slider();
						$('body').vs_perview_size();
						$('body').vs_item_hover();
						 $('.vs_perview_global_content[data-key="'+data_key+'"]').height('auto');
 					  }, 50); 
					} else{
					vs_remove_add_error_loading();
  				}
   			} 
		}); 
 		  }, 500);
 
  };	
	
	jQuery(document).ready(function() {
		 
		 
		 
		 
		 
		 
		 
		 
 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Iseet
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
 
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Slug
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	$(document).on("click",'#edit-slug-buttons .save',function() {
						 setTimeout(function(){  
  				var slug = $('#editable-post-name').text();
  				$('#vs_shortcode').text('[visualslider id="'+slug+'"][/visualslider]');
 			},2000);
	});
	
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			Scroll Center
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	function scrollMeTo($Parent, $Child) {
		 let parentHalf = $Parent.width() / 2; // half of parent width
		let childHalf = $Child.width() / 2; //half of child width
		let childPos = $Child.position().left; // X pos of child (relative to screen)
		//We increase X with the center of the child (childHalf) - scrolling here we see the right half of the child;
		//We decrease X with the parentHalf to obtain perfect center;
		let scroll = childPos + childHalf - parentHalf; //how much we need to scroll to the element
		//We now add the needed scroll to the current scroll.
		let nextScroll = $Parent.scrollLeft() + scroll;
		$Parent.scrollLeft(nextScroll);
		 
	}
	if($('.vs_perview_global_scroll').hasClass('vs_perview_global_scroll')){
	scrollMeTo($('.vs_perview_global_scroll'),$('.vs_perview_global_content '));
	}

 	
 
	  
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Json
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
 	$(".vs_module_setting").on('change keyup mousedown',function() { 
		$('body').vs_setting_json();
 	});
  
 
 
	
	
	
	
	/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																			 Slide Options
  
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	
	 
	$(document).on('click', ".vs_animte_play", function(e) {
		
 		
  	
     $('.vs_perview_slide').addClass('vs_perview_animte_active');
	
   		var duplicate = $(".vs-perview-slide").clone();
		$(".vs_perview_slide_content .vs-slide-list").append(duplicate);
		$(".vs_perview_slide_content div.vs-slide:last-child").addClass('vs-slide-item active').removeClass('vs-perview-slide');
 
			$('.vs_module_layer_item').each( function(index, element) {
   							$(this).vs_layer_effectss();
			});
 
 						  
		setTimeout(function(){
			$(".vs_perview_slide_content div.vs-slide:last-child").addClass('vs-animte-active');
			}, 100);
			


  	});	
	
	$(document).on('click', ".vs_perview_animte_active .vs_animte_stop", function(e) {
			 
	   $('.vs_perview_slide ').removeClass('vs_perview_animte_active');
		$('.vs_perview_slide_content .vs-slide-item').remove();
	
	   
	  });	
  
  
/*************************************************************************************************************************************************************************
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 
 																		Perview Content All ELEMENT
 
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
**************************************************************************************************************************************************************************/	


 $(document).on("click", ".vs_perview_global .vs_module_slide_top .vs_module_slide_options", function(){
            e.preventDefault();
	var data= $(this).parent().attr('data-count');
	$('.vs_module_slide_item[data-count="'+data+'"] .vs_module_slide_options').trigger("click");
});
 $(document).on("click", ".vs_perview_global .vs_module_slide_top .vs_module_slide_duplicate", function(){
            e.preventDefault();
	var data= $(this).parent().attr('data-count');
	$('.vs_module_slide_item[data-count="'+data+'"] .vs_module_slide_duplicate').trigger("click");
});
 $(document).on("click", ".vs_perview_global .vs_module_slide_top .vs_module_slide_template_save", function(){
            e.preventDefault();
	var data= $(this).parent().attr('data-count');
	$('.vs_module_slide_item[data-count="'+data+'"] .vs_module_slide_template_save').trigger("click");
});
 $(document).on("click", ".vs_perview_global .vs_module_slide_top .vs_module_slide_remove", function(){
            e.preventDefault();
	var data= $(this).parent().attr('data-count');
	$('.vs_module_slide_item[data-count="'+data+'"] .vs_module_slide_remove').trigger("click");
});


	 

$(document).on('change keyup mousedown input','.vs_module_side_options_active',function() {
 	$(this).vs_layer_options_perview();

});
$(document).on('change keyup mousedown input','#vs_module_slide_options',function() {
	$('body').vs_perview_slide();

});

        
	
$(document).on('change keyup mousedown mousemove','#clr-picker',function() {
	$('.vs_module_side_options_active').vs_layer_options_perview();
	$('#vs_module_slide_options').vs_perview_slide();

});

$(document).on('click','.vs_perview_heading_change,.vs_module_setting input',function() {
 				 $('body').vs_reload_global_slider();
});
 
 				$('body').vs_setting_json();
 				 $('body').vs_reload_global_slider();
 	});
	 
});
 