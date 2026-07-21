jQuery(function($) {
/**
 * SMOF js
 *
 * contains the core functionalihexwps to be used
 * inside SMOF
 */

jQuery.noConflict();

/** Fire up jQuery - let's dance!
 */
jQuery(document).ready(function($){
	"use strict";

	 

	

	//(un)fold options in a checkbox-group
	//(un)fold options in a checkbox-group
  	jQuery('body').on("click",".fld",function() {
		var $fold='.f_'+this.id;
		$($fold).slideToggle('normal', "swing");
  	});

  	//Color picker
 
	//hides warning if js is enabled
	$('#js-warning').hide();

	//Tabify Options
	$('.group').hide();

	// Display last current tab
	 
	//Color picker
 
	//hides warning if js is enabled
	$('#js-warning').hide();

	//Tabify Options
	$('.group').hide();

	 
 	var get_url_location = window.location.href;
var  location_tab = get_url_location.match(/of-option[^]*/);
 
 if(location_tab !== null){
 	$("#of-nav").find('a[href="#'+location_tab+'"]').parent().addClass('current');
	$('.group#'+location_tab).fadeIn();
 }else{
		$('.group:first').fadeIn('fast');
		$('#of-nav li:first').addClass('current');	 
	 
 }	 

		//Current Menu Class
	$('#of-nav li a').on('click',function(evt){
	// event.preventDefault();

		$('#of-nav li').removeClass('current');
		$(this).parent().addClass('current');

		var clicked_group = $(this).attr('href');

 			window.history.pushState("object or string", "Title",clicked_group);

		$('.group').hide();

		$(clicked_group).fadeIn('fast');
		return false;

	});


	//Expand Options
	var flip = 0;

	$('#expand_options').click(function(){
		if(flip == 0){
			flip = 1;
			$('#of_container #of-nav').hide();
			$('#of_container #content').width(755);
			$('#of_container .group').add('#of_container .group h2').show();

			$(this).removeClass('expand');
			$(this).addClass('close');
			$(this).text('Close');

		} else {
			flip = 0;
			$('#of_container #of-nav').show();
			$('#of_container #content').width(595);
			$('#of_container .group').add('#of_container .group h2').hide();
			$('#of_container .group:first').show();
			$('#of_container #of-nav li').removeClass('current');
			$('#of_container #of-nav li:first').addClass('current');

			$(this).removeClass('close');
			$(this).addClass('expand');
			$(this).text('Expand');

		}

	});

	//Update Message popup
	$.fn.center = function () {
		this.animate({"top":( $(window).height() - this.height() - 200 ) / 2+$(window).scrollTop() + "px"},100);
		this.css("left", 250 );
		return this;
	}


	$('#of-popup-save').center();
	$('#of-popup-reset').center();
	$('#of-popup-fail').center();

	$(window).scroll(function() {
		$('#of-popup-save').center();
		$('#of-popup-reset').center();
		$('#of-popup-fail').center();
	});

	// hexwp Edit
	$(document).on('click','.section-radio input[type=radio] ',function(){
		$(this).parent().find('[checked="checked"]').removeAttr('checked');
		$(this).attr('checked','checked');
	});
	
	//Masked Inputs (images as radio buttons)
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});
	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	//Masked Inputs (background images as radio buttons)
	$('.of-radio-tile-img').click(function(){
		$(this).parent().parent().find('.of-radio-tile-img').removeClass('of-radio-tile-selected');
		$(this).addClass('of-radio-tile-selected');
	});
	$('.of-radio-tile-label').hide();
	$('.of-radio-tile-img').show();
	$('.of-radio-tile-radio').hide();

	// Style Select
	(function ($) {
	var styleSelect = {
		init: function () {
		$('.select_wrapper').each(function () {
			$(this).prepend('<span>' + $(this).find('option:selected').text() + '</span>');
		});
		$('.select').on('change', function () {
			$(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
		});
		$('.select').bind($.browser.msie ? 'click' : 'change', function(event) {
			$(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
		});
		}
	};
	$(document).ready(function () {
		styleSelect.init()
	})
	})(jQuery);
	// End hexwp Edit


	/** Aquagraphite Slider MOD */

	//Hide (Collapse) the toggle containers on load
	$(".slide_body").hide();

	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	$(".slide_edit_button").on( 'click', function(){
		/*
		//display as an accordion
		$(".slide_header").removeClass("active");
		$(".slide_body").slideUp("fast");
		*/
		//toggle for each
		$(this).parent().toggleClass("active").next().slideToggle("fast");
		return false; //Prevent the browser jump to the link anchor
	});

	// Update slide title upon typing
	function update_slider_title(e) {
		var element = e;
		if ( this.timer ) {
			clearTimeout( element.timer );
		}
		this.timer = setTimeout( function() {
			$(element).parent().prev().find('strong').text( element.value );
		}, 100);
		return true;
	}

	$('.of-slider-title').on('keyup', function(){
		update_slider_title(this);
	});


	//Remove individual slide
	$('.slide_delete_button').on('click', function(){
	// event.preventDefault();
	var agree = confirm("Are you sure you wish to delete this slide?");
		if (agree) {
			var $trash = $(this).parents('li');
			//$trash.slideUp('slow', function(){ $trash.remove(); }); //chrome + confirm bug made slideUp not working...
			$trash.animate({
					opacity: 0.25,
					height: 0,
				}, 500, function() {
					$(this).remove();
			});
			return false; //Prevent the browser jump to the link anchor
		} else {
		return false;
		}
	});

	//Add new slide
	$(".slide_add_button").on('click', function(){
		var slidesContainer = $(this).prev();
		var sliderId = slidesContainer.attr('id');

		var numArr = $('#'+sliderId +' li').find('.order').map(function() {
			var str = this.id;
			str = str.replace(/\D/g,'');
			str = parseFloat(str);
			return str;
		}).get();

		var maxNum = Math.max.apply(Math, numArr);
		if (maxNum < 1 ) { maxNum = 0};
		var newNum = maxNum + 1;

		var newSlide = '<li class="temphide"><div class="slide_header"><strong>Slide ' + newNum + '</strong><input type="hidden" class="slide of-input order" name="' + sliderId + '[' + newNum + '][order]" id="' + sliderId + '_slide_order-' + newNum + '" value="' + newNum + '"><a class="slide_edit_button" href="#">Edit</a></div><div class="slide_body" style="display: none; "><label>Title</label><input class="slide of-input of-slider-title" name="' + sliderId + '[' + newNum + '][title]" id="' + sliderId + '_' + newNum + '_slide_title" value=""><label>Image URL</label><input class="upload slide of-input" name="' + sliderId + '[' + newNum + '][url]" id="' + sliderId + '_' + newNum + '_slide_url" value=""><div class="upload_button_div"><span class="button media_upload_button" id="' + sliderId + '_' + newNum + '">Upload</span><span class="button remove-image hide" id="reset_' + sliderId + '_' + newNum + '" title="' + sliderId + '_' + newNum + '">Remove</span></div><div class="screenshot"></div><label>Link URL (optional)</label><input class="slide of-input" name="' + sliderId + '[' + newNum + '][link]" id="' + sliderId + '_' + newNum + '_slide_link" value=""><label>Description (optional)</label><textarea class="slide of-input" name="' + sliderId + '[' + newNum + '][description]" id="' + sliderId + '_' + newNum + '_slide_description" cols="8" rows="8"></textarea><a class="slide_delete_button" href="#">Delete</a><div class="clear"></div></div></li>';

		slidesContainer.append(newSlide);
		var nSlide = slidesContainer.find('.temphide');
		nSlide.fadeIn('fast', function() {
			$(this).removeClass('temphide');
		});

		optionsframework_file_bindings(); // re-initialise upload image..

		return false; //prevent jumps, as always..
	});

	//Sort slides
	jQuery('.slider').find('ul').each( function() {
		var id = jQuery(this).attr('id');
		$('#'+ id).sortable({
			placeholder: "placeholder",
			opacity: 0.6,
			handle: ".slide_header",
			cancel: "a"
		});
	});


	/**	Sorter (Layout Manager) */
	jQuery('.sorter').each( function() {
		var id = jQuery(this).attr('id');
		$('#'+ id).find('ul').sortable({
			items: 'li',
			placeholder: "placeholder",
			connectWith: '.sortlist_' + id,
			opacity: 0.6,
			update: function() {
				$(this).find('.position').each( function() {

					var listID = $(this).parent().attr('id');
					var parentID = $(this).parent().parent().attr('id');
					parentID = parentID.replace(id + '_', '')
					var optionID = $(this).parent().parent().parent().attr('id');
					$(this).prop("name", optionID + '[' + parentID + '][' + listID + ']');

				});
			}
		});
	});

	//  hexwp Edit

	/**	Ajax Backup & Restore MOD */
	//backup button
	$('#of_backup_button').on('click', function(){

		var answer = confirm("Click OK to backup your current saved options.")

		if (answer){

			var clickedObject = $(this);
			var clickedID = $(this).attr('id');

			var nonce = $('#security').val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'backup_options',
				security: nonce
			};

			if( typeof(smof_wpml) != 'undefined' && smof_wpml.wpml_custom_current_lang ) {
				data.wpml = smof_wpml.wpml_custom_current_lang;
			}

			$.post(ajaxurl, data, function(response) {

				//check nonce
				if(response==-1){ //failed

					var fail_popup = $('#of-popup-fail');
					fail_popup.fadeIn();
					window.setTimeout(function(){
						fail_popup.fadeOut();
					}, 2000);
				}

				else {

					var success_popup = $('#of-popup-save');
					success_popup.fadeIn();
					window.setTimeout(function(){
						window.location.reload(true);
					}, 1000);
				}

			});

		}

	return false;

	});
	// End hexwp Edit

	//restore button
	$('#of_restore_button').on('click', function(){

		var answer = confirm("'Warning: All of your current options will be replaced with the data from your last backup! Proceed?")

		if (answer){

			var clickedObject = $(this);
			var clickedID = $(this).attr('id');

			var nonce = $('#security').val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'restore_options',
				security: nonce
			};

			if( typeof(smof_wpml) != 'undefined' && smof_wpml.wpml_custom_current_lang ) {
				data.wpml = smof_wpml.wpml_custom_current_lang;
			}

			$.post(ajaxurl, data, function(response) {

				//check nonce
				if(response==-1){ //failed

					var fail_popup = $('#of-popup-fail');
					fail_popup.fadeIn();
					window.setTimeout(function(){
						fail_popup.fadeOut();
					}, 2000);
				}

				else {

					var success_popup = $('#of-popup-save');
					success_popup.fadeIn();
					window.setTimeout(function(){
window.location.reload(true);					}, 1000);
				}

			});

		}

	return false;

	});

	
 


	/**	Ajax Transfer (Import/Export) Option */
	$('body').on('click',"#of_import_button", function(){

		var answer = confirm("Click OK to import options.")

		if (answer){

			var clickedObject = $(this);
			var clickedID = $(this).attr('id');

			var nonce = $('#security').val();

			var import_data = $('#export_data').val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'import_options',
				security: nonce,
				data: import_data
			};

			if( typeof(smof_wpml) != 'undefined' && smof_wpml.wpml_custom_current_lang ) {
				data.wpml = smof_wpml.wpml_custom_current_lang;
			}

			$.post(ajaxurl, data, function(response) {
				var fail_popup = $('#of-popup-fail');
				var success_popup = $('#of-popup-save');

				//check nonce
				if(response==-1){ //failed
					fail_popup.fadeIn();
					window.setTimeout(function(){
						fail_popup.fadeOut();
					}, 2000);
				}
				else
				{
					success_popup.fadeIn();
					window.setTimeout(function(){
window.location.reload(true);					}, 1000);
				}

			});

		}

	return false;

	});

 
		$("body").on("click","#of_import_buttons",function(){
			
					var answer = confirm("Click OK to import options.");
 
		if (answer){
			 jQuery('#section-predefined-demo .controls').append('<div class="hw-loading"></div>');
 
			jQuery('.hexwpsss').text('');

 			var layouts = $(this).attr('data-id');
			$.ajax({
 				data : {
			action : 'hexwp_predefined',
			layout : layouts,
				},
			url: hexwp_js.ajaxurl,
 			success:function(data) {
 				 var $data = $(data);
	 
				 if($data.length){
 
					jQuery('#section-predefined-demo .controls').append(data);
 					var hexwp = jQuery('.per-value').data('id');
 				 document.getElementById('export_data').value = hexwp;
 					
						var hexwp = jQuery('.per-value').data('id');
 			 document.getElementById('export_data').value = hexwp;
   

			var clickedObject = $('#of_import_buttons');
			var clickedID = $('#of_import_buttons').attr('id');

			var nonce = $('#security').val();

			var import_data = $('#export_data').val();

			var data = {
				action: 'of_ajax_post_action',
				type: 'import_options',
				security: nonce,
				data: import_data
			};

			if( typeof(smof_wpml) != 'undefined' && smof_wpml.wpml_custom_current_lang ) {
				data.wpml = smof_wpml.wpml_custom_current_lang;
			}

			$.post(ajaxurl, data, function(response) {
				var fail_popup = $('#of-popup-fail');
				var success_popup = $('#of-popup-save');

				//check nonce
				if(response==-1){ //failed
					fail_popup.fadeIn();
					window.setTimeout(function(){
						fail_popup.fadeOut();
					}, 2000);
				}
				else
				{
								 jQuery('.hw-loading').fadeOut();

					success_popup.fadeIn();
 					window.setTimeout(function(){
					window.location.reload(true);
					}, 1000);
				}

			}); 
					
					
				 }
  			},
			error : function(jqXHR, textStatus, errorThrown) {
				$loader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
			}
		});  
		}
		return false;
	
	});
	
	/**	Ajax Transfer (Import/Export) Option */
	 
	/** AJAX Save Options */
	$('body').on('click','#of_save',function() {

		var nonce = $('#security').val();

		$('.ajax-loading-img').fadeIn();

		//get serialized data from all our option fields
		// Avada edit
		$('#of_form :input[name][name!="security"][name!="of_reset"][name!="google_analytics"][name!="space_head"][name!="space_body"][name!="custom_css"]').val().replace(/\%22/g, "'");

		var serializedReturn = $('#of_form :input[name][name!="security"][name!="of_reset"]').serialize();
		// End Avada edit

		var data = {
			type: 'save',
			action: 'of_ajax_post_action',
			security: nonce,
			data: serializedReturn
		};

		if( typeof(smof_wpml) != 'undefined' && smof_wpml.wpml_custom_current_lang ) {
			data.wpml = smof_wpml.wpml_custom_current_lang;
		}

		$.post(ajaxurl, data, function(response) {
			var success = $('#of-popup-save');
			var fail = $('#of-popup-fail');
			var loading = $('.ajax-loading-img');
			loading.fadeOut();

			if (response==1) {
				success.fadeIn();
			} else {
				fail.fadeIn();
			}

			window.setTimeout(function(){
				success.fadeOut();
				fail.fadeOut();
			}, 2000);
		});

	return false;

	});


	/* AJAX Options Reset */
	$('#of_reset').on("click",function() {

		//confirm reset
		var answer = confirm("Click OK to reset. All settings will be lost and replaced with default settings!");

		//ajax reset
		if (answer){

			var nonce = $('#security').val();

			$('.ajax-reset-loading-img').fadeIn();

			var data = {

				type: 'reset',
				action: 'of_ajax_post_action',
				security: nonce,
			};

			if( typeof(smof_wpml) != 'undefined' && smof_wpml.wpml_custom_current_lang ) {
				data.wpml = smof_wpml.wpml_custom_current_lang;
			}

			$.post(ajaxurl, data, function(response) {
				var success = $('#of-popup-reset');
				var fail = $('#of-popup-fail');
				var loading = $('.ajax-reset-loading-img');
				loading.fadeOut();

				if (response==1)
				{
					success.fadeIn();
					window.setTimeout(function(){
window.location.reload(true);					}, 1000);
				}
				else
				{
					fail.fadeIn();
					window.setTimeout(function(){
						fail.fadeOut();
					}, 2000);
				}


			});

		}

	return false;

	});


	/**	Tipsy @since v1.3 */
	if (jQuery().tipsy) {
		$('.typography-size, .typography-height, .typography-face, .typography-style, .of-typography-color').tipsy({
			fade: true,
			gravity: 's',
			opacity: 0.7,
		});
	}


	/**
	  * JQuery UI Slider function
	  * Dependencies 	 : jquery, jquery-ui-slider
	  * Feature added by : Smartik - http://smartik.ws/
	  * Date 			 : 03.17.2013
	  */
	jQuery('.smof_sliderui').each(function() {
		var obj   = jQuery(this);
		var sId   = "#" + obj.data('id');
		var val   = parseInt(obj.data('val'));
		var min   = parseInt(obj.data('min'));
		var max   = parseInt(obj.data('max'));
		var step  = parseInt(obj.data('step'));

		//slider init
		obj.slider({
			value: val,
			min: min,
			max: max,
			step: step,
			range: "min",
			slide: function( event, ui ) {
				jQuery(sId).val( ui.value );
			}
		});

	});

	jQuery( '.section-sliderui' ).find( 'input' ).change( function() {
		jQuery( this ).siblings( '.smof_sliderui' ).slider( 'option', 'value', jQuery( this ).val() );

	});


	/**
	  * Switch
	  * Dependencies 	 : jquery
	  * Feature added by : Smartik - http://smartik.ws/
	  * Date 			 : 03.17.2013
	  */
	  
 
		//fold/unfold related options
 
  
   	function sao_fold_hide(items){ 

 		$('#of_container').find('.options_fold').each(function() {
			var show;
			$(this).find('.options_fold_item').each(function() {
				var data_name = $(this).attr('data-name'); 			
				var data_value = $(this).attr('data-value');
				var type = $('#of_container [name="'+data_name+'"]').attr('type');
				if(type == 'radio'){
 					var checked = $('#of_container [name="'+data_name+'"][value="'+data_value+'"]').attr('checked');
 					if( checked == 'checked' ){
						show = 'checked';
					}
				}else if(type == 'hidden'){
						var checked = $('#of_container .main_checkbox[name="'+data_name+'"]').attr('checked');
							if( checked == 'checked' ){
									show = 'checked';
							}	
				}else{
					var val =$('#of_container [name="'+data_name+'"]').val();
						if(data_value == val){
						show = 'checked';
					}
				}
 
			});
			
			if( show == 'checked' ){
				$(this).parent().attr('data-active','show');
			}else{
				$(this).parent().attr('data-active','hide');
			}
 		});	
 
		$('.options_fold_item').each(function() {
			var data_name = $(this).attr('data-name');
			var actives  = $('#of_container [name="'+data_name+'"]').parents(items).attr('data-active');
			if(	 actives == 'hide' ){
				$(this).parent().parent().attr('data-active','hide');
			}
 		});
		 
 	 
			$('body').on("click",'.section-select select,.section-radio input, .section-switch .switch-options,.radio-switch-options label,.of-radio-img-img',function() { 
  					$(this).parents('.group').find('.options_fold').each(function() {
					var show;
					$(this).find('.options_fold_item').each(function() {
					var data_name = $(this).attr('data-name'); 			
					var data_value = $(this).attr('data-value');
					var type = $('#of_container [name="'+data_name+'"]').attr('type');
						if(type == 'radio'){
						var checked = $('#of_container [name="'+data_name+'"][value="'+data_value+'"]').attr('checked');
					 
							if( checked == 'checked' ){
								
									show = 'checked';
							}
						}else if(type == 'hidden'){
						var checked = $('#of_container .main_checkbox[name="'+data_name+'"]').attr('checked');
							if( checked == 'checked' ){
									show = 'checked';
							}
						}else{
							var val =$('#of_container [name="'+data_name+'"]').val();
							if(data_value == val){
								show = 'checked';
							}
							
						}
											
					});
						if( show == 'checked' ){
							$(this).parent().attr('data-active','show');
						}else{
							$(this).parent().attr('data-active','hide');
						}
				 });	
	 
	 
			$(this).parents('li').find('.options_fold_item').each(function() {
				var data_name = $(this).attr('data-name');
				var actives  = $('[name="'+data_name+'"]').parents(items).attr('data-active');
				if(	 actives == 'hide' ){
					$(this).parent().parent().attr('data-active','hide');
				}
 			}); 
		});
		
		
		
$('#of-nav li a').on("click",function() {
 			var id =$(this).attr('data-id'); 
  					$('#of-option-'+id+'').find('.options_fold').each(function() {
					var show;
					$(this).find('.options_fold_item').each(function() {
					var data_name = $(this).attr('data-name'); 			
					var data_value = $(this).attr('data-value');
					var type = $('#of_container  [name="'+data_name+'"]').attr('type');
						if(type == 'radio'){
						var checked = $('#of_container [name="'+data_name+'"][value="'+data_value+'"]').attr('checked');
							if( checked == 'checked' ){
									show = 'checked';
							}
						}else if(type == 'hidden'){
						var checked = $('#of_container .main_checkbox[name="'+data_name+'"]').attr('checked');
							if( checked == 'checked' ){
									show = 'checked';
							}
						}else{
							var val =$('#of_container [name="'+data_name+'"]').val();
							if(data_value == val){
								show = 'checked';
							}
							
						}
											
					});
						if( show == 'checked' ){
							$(this).parent().attr('data-active','show');
						}else{
							$(this).parent().attr('data-active','hide');
						}
				 });	
	 
	 
			$('#of-option-'+id+'').find('.options_fold_item').each(function() {
				var data_name = $(this).attr('data-name');
				var actives  = $('[name="'+data_name+'"]').parents(items).attr('data-active');
				if(	 actives == 'hide' ){
					$(this).parent().parent().attr('data-active','hide');
				}
 			});  
		});
		
		 
  	}
	
	
	
   	function sao_fold_click(this_item){ 

	
	 
		 
  					this_item.parents('.group').find('.options_fold').each(function() {
					var show;
					$(this).find('.options_fold_item').each(function() {
					var data_name = $(this).attr('data-name'); 			
					var data_value = $(this).attr('data-value');
					var type = $('#of_container [name="'+data_name+'"]').attr('type');
						if(type == 'radio'){
						var checked = $('#of_container [name="'+data_name+'"][value="'+data_value+'"]').attr('checked');
					 
							if( checked == 'checked' ){
								
									show = 'checked';
							}
						}else if(type == 'hidden'){
						var checked = $('#of_container .main_checkbox[name="'+data_name+'"]').attr('checked');
							if( checked == 'checked' ){
									show = 'checked';
							}
						}else{
							var val =$('#of_container [name="'+data_name+'"]').val();
							if(data_value == val){
								show = 'checked';
							}
							
						}
											
					});
						if( show == 'checked' ){
							$(this).parent().attr('data-active','show');
						}else{
							$(this).parent().attr('data-active','hide');
						}
				 });	
	 
	 
			this_item.parents('li').find('.options_fold_item').each(function() {
				var data_name = $(this).attr('data-name');
				var actives  = $('[name="'+data_name+'"]').parents(items).attr('data-active');
				if(	 actives == 'hide' ){
					$(this).parent().parent().attr('data-active','hide');
				}
 			}); 
	 	
	}
   
					
			 
	//fold/unfold related options
 
  
	jQuery("body").on("click",'.cb-enable',function(){
		var parent = $(this).parents('.switch-options');
		jQuery('.cb-disable',parent).removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery('.main_checkbox',parent).attr('checked', true);

		//fold/unfold related options
		var obj = jQuery(this);
		var $fold='.f_'+obj.data('id');
		jQuery($fold).slideDown('normal', "swing");
	});
	jQuery("body").on("click",'.cb-disable',function(){
		var parent = $(this).parents('.switch-options');
		jQuery('.cb-enable',parent).removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery('.main_checkbox',parent).attr('checked', false);

		//fold/unfold related options
		var obj = jQuery(this);
		var $fold='.f_'+obj.data('id');
		jQuery($fold).slideUp('normal', "swing");
	});
	//disable text select(for modern chrome, safari and firefox is done via CSS)
	if (($.browser.msie && $.browser.version < 10) || $.browser.opera) {
		$('.cb-enable span, .cb-disable span').find().attr('unselectable', 'on');
	}

	jQuery(document).on("click",'.of-switch-enable',function(){
		jQuery(this).parent().find('.of-input-disable').attr("checked", '');
		jQuery(this).parent().find('.of-input-enabled').attr("checked", 'checked');
		jQuery(this).parent().find('.selected').removeClass('selected');
			var this_item =jQuery(this);
  		jQuery(this).addClass('selected');
	 setTimeout(function(){
				 	sao_fold_click(this_item);	
		}, 100);
		 
			
  
	});
	jQuery(document).on("click",'.of-switch-disable',function(){
		jQuery(this).parent().find('.of-input-enabled').attr("checked", '');
		jQuery(this).parent().find('.of-input-disable').attr("checked", 'checked');
		jQuery(this).parent().find('.selected').removeClass('selected');
  		jQuery(this).addClass('selected');
 			var this_item =$(this);
 		 setTimeout(function(){
				 	sao_fold_click(this_item);	
		},100);
 
	});

 

					sao_fold_hide(".options_item");	
			 
	/**
	  * Google Fonts
	  * Dependencies 	 : google.com, jquery
	  * Feature added by : Smartik - http://smartik.ws/
	  * Date 			 : 03.17.2013
	  */
	function GoogleFontSelect( slctr, mainID ){

		var _selected = $(slctr).val(); 						//get current value - selected and saved
		var _linkclass = 'style_link_'+ mainID;
		var _previewer = mainID +'_ggf_previewer';

		if( _selected ){ //if var exists and isset

			//Check if selected is not equal with "Select a font" and execute the script.
			if ( _selected !== 'none' && _selected !== 'Select a font' ) {

				//remove other elements crested in <head>
				$( '.'+ _linkclass ).remove();

				//replace spaces with "+" sign
				var the_font = _selected.replace(/\s+/g, '+');

				//add reference to google font family
				$('head').append('<link href="http://fonts.googleapis.com/css?family='+ the_font +'" rel="stylesheet" type="text/css" class="'+ _linkclass +'">');

				//show in the preview box the font
				$('.'+ _previewer ).css('font-family', _selected +', sans-serif' );

			}else{

				//if selected is not a font remove style "font-family" at preview box
				$('.'+ _previewer ).css('font-family', '' );

			}

		}

	}

	//init for each element
	jQuery( '.google_font_select' ).each(function(){
		var mainID = jQuery(this).attr('id');
		GoogleFontSelect( this, mainID );
	});

	//init when value is changed
	jQuery( '.google_font_select' ).change(function(){
		var mainID = jQuery(this).attr('id');
		GoogleFontSelect( this, mainID );
	});


	/**
	  * Media Uploader
	  * Dependencies 	 : jquery, wp media uploader
	  * Feature added by : Smartik - http://smartik.ws/
	  * Date 			 : 05.28.2013
	  */
	function optionsframework_add_file(event, selector) {

		var upload = $(".uploaded-file"), frame;
		var $el = $(this);

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $el.data('update'),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		});

		// When an image is selected, run a callback.
		frame.on( 'select', function() {
			// Grab the selected attachment.
			var attachment = frame.state().get('selection').first();
			frame.close();
			selector.find('.upload').val(attachment.attributes.url);
			if ( attachment.attributes.type == 'image' ) {
				selector.find('.screenshot').empty().hide().append('<img class="of-option-image" src="' + attachment.attributes.url + '">').slideDown('fast');
			}
			selector.find('.media_upload_button').unbind();
			selector.find('.remove-image').show().removeClass('hide');//show "Remove" button
			selector.find('.remove-image').css( 'display', 'inline-block' );
			selector.find('.of-background-properhexwps').slideDown();
			optionsframework_file_bindings();
		});

		// Finally, open the modal.
		frame.open();
	}

	function optionsframework_remove_file(selector) {
		selector.find('.remove-image').hide().addClass('hide');//hide "Remove" button
		selector.find('.upload').val('');
		selector.find('.of-background-properhexwps').hide();
		selector.find('.screenshot').slideUp();
		selector.find('.remove-file').unbind();
		// We don't display the upload button if .upload-notice is present
		// This means the user doesn't have the WordPress 3.5 Media Library Support
		if ( $('.section-upload .upload-notice').length > 0 ) {
			$('.media_upload_button').remove();
		}
		optionsframework_file_bindings();
	}

	function optionsframework_file_bindings() {
		$('.remove-image, .remove-file').on('click', function() {
			optionsframework_remove_file( $(this).parents('.section-upload, .section-media, .slide_body') );
		});

		$('.media_upload_button').unbind('click').click( function( event ) {
			optionsframework_add_file(event, $(this).parents('.section-upload, .section-media, .slide_body'));
		});
	}

	optionsframework_file_bindings();
	
	// hexwp Edit

	//accordion Title
	$('.accordion').each(function() {
		$(this).find('.section-accordion:last').remove();
		$(this).find('.accordion-content').css("display","none");
	});
	  jQuery(document).on("click", ".panel-plus" , function(){
 
		jQuery('.accordion').find('.accordion-content').hide(300);
		jQuery('.panel-minus').hide();
		jQuery('.panel-plus').show();
    });
	  
  	jQuery(document).on("click", ".panel-plus" , function(){
 		jQuery(this).parent().parent().parent().parent().find('.accordion-content').slideToggle(300);
		jQuery(this).hide();
 		jQuery(this).parent().find(".panel-minus").show();
    });
 
 	jQuery(document).on("click", ".panel-minus" , function(){
  
 		jQuery(this).parent().parent().parent().parent().find('.accordion-content').slideToggle("fast");
		jQuery(this).hide();
 		jQuery(this).parent().find(".panel-plus").show();
    });
		$('input').each(function() {
    
		$(this).val($(this).attr('value'));
    });
 
	
  
	//**************************************************** Header Layout***************/
 /*
 	function header_layout(items){
 		
		
		  if ( items.val() == 'navplus-masthead' || items.val() == 'masthead-navplus' ) {
  				jQuery('.navplus_layout_fold').each(function() {$(this).show();});
  				jQuery('.masthead_layout_fold').each(function() {$(this).show();});
  				jQuery('.header_layout_fold').each(function() {$(this).hide();});
		   }
		   
 		   if ( items.val() == 'navplus-header-masthead' ) {

  				jQuery('.navplus_layout_fold').each(function() {$(this).show();});
  				jQuery('.masthead_layout_fold').each(function() {$(this).show();});
  				jQuery('.header_layout_fold').each(function() {$(this).show();});
			}
			if ( items.val() == 'masthead' ) {
  				jQuery('.navplus_layout_fold').each(function() {$(this).hide();});
  				jQuery('.masthead_layout_fold').each(function() {$(this).show();});
  				jQuery('.header_layout_fold').each(function() {$(this).hide();});
		   }
		   
			if ( items.val() == 'header-masthead' ) {
  				jQuery('.navplus_layout_fold').each(function() {$(this).hide();});
  				jQuery('.masthead_layout_fold').each(function() {$(this).show();});
  				jQuery('.header_layout_fold').each(function() {$(this).show();});
		   }	  		
	}
	
 	$('#of_container #header_layout').each(function(index, element) {
         
		   header_layout($(this));
		});
 
 	$('#of_container #header_layout').on('change', function() {	
		   header_layout($(this));
		   		   
		});
 */
 
	// Body Background Type
	 
 }); //end doc ready
 });