jQuery(function($) {
	"use strict";
	jQuery(document).ready(function($) {
 
 
     $.fn.hexwp_coloris = function() { 	
	
		rang_Coloris({el: '.hexwp-coloris',swatches: [
			'rgba(255,255,255,0.0)','#000000','#ffffff','#444444','#888888','#bbbbbb','#FF1493','#FF00FF','#9400D3','#FA8072','#FF0000','#8B0000','#FF8C00','#FF4500','#ffd800','#FFFF00','#F0E68C','#ADFF2F','#32CD32','#00FA9A','#20B2AA','#00FFFF','#00CED1','#00BFFF','#6495ED','#0000FF','#191970','#D2691E','#A52A2A','#800000','#708090','rgba(0,0,0,0.5)','rgba(255,255,255,0.5)'
    ]
			});
	 }; 
	 $('body').hexwp_coloris();



  	//***************************************************************************************************************/
	/* Single Template
	/****************************************************************************************************************/
		$(document).on('click','.hexwp_meta_radio input[type=radio] ',function(){
		$(this).parent().find('[checked="checked"]').removeAttr('checked');
		$(this).attr('checked','checked');
	});
	
	
		$('.meta_hexwp_single_template input:checked').parent().addClass("selected");
		$('.meta_hexwp_single_template').on("click","img",function(){
				$('.meta_hexwp_single_template').find('li').removeClass('selected');
				$(this).parents('li').addClass('selected');
				$(this).parent().prev().attr('checked','checked');
		});
		 
		$('.meta_hexwp_body_background_pattern input:checked').parent().addClass("selected");
		$('.meta_hexwp_body_background_pattern').on("click","a",function(){
				$('.meta_hexwp_body_background_pattern').find('li').removeClass('selected');
				$(this).parents('li').addClass('selected');
				$(this).prev().attr('checked','checked');
		});
		
	 

 
   	//***************************************************************************************************************/
	/* Background
	/****************************************************************************************************************/
		$('.meta_hexwp_body_background_pattern li').each(function(index, element) {
			var src = $(this).find('img').attr('src'); 
			$(this).find('a').css("background" , " transparent url("+src+") repeat scroll 0% 0%");
		});	
		 
			 
		jQuery('.meta_hexwp_body_background_image').hide();
		jQuery('.meta_hexwp_body_background_pattern').hide();	 
			// Body Background Type
		if( $('#body_background_type').val() == 'pattern') {
			jQuery('.meta_hexwp_body_background_pattern').show();
			jQuery('.meta_hexwp_body_background_image').hide();
			
		} else if($('#body_background_type').val() == 'custom'){
			jQuery('.meta_hexwp_body_background_image').show();
			jQuery('.meta_hexwp_body_background_pattern').hide();	
			
		} else{
			jQuery('.meta_hexwp_body_background_pattern').hide();
			jQuery('.meta_hexwp_body_background_image').hide();
		}
	 
		jQuery('.meta_hexwp_body_background_type').on("click" , '#body_background_type_default,#body_background_type_none' ,function(){
			jQuery('.meta_hexwp_body_background_image').hide();
			jQuery('.meta_hexwp_body_background_pattern').hide();
		});
		jQuery('.meta_hexwp_body_background_type').on("click" ,'#body_background_type_pattern'   ,function(){
			jQuery('.meta_hexwp_body_background_pattern').show();
			jQuery('.meta_hexwp_body_background_image').hide();
		});
		jQuery('.meta_hexwp_body_background_type').on("click" , '#body_background_type_custom' ,function(){
			jQuery('.meta_hexwp_body_background_image').show();
			jQuery('.meta_hexwp_body_background_pattern').hide();
		});	  
					  
		
		$(document).on('click', '.hexwp_game_remove_image', function(e) {
			$(this).parent().find('input').attr('value','');
			
			$(this).parent().find('img').remove()
			$(this).remove();
		}); 				 
				 
	// the upload image button, saves the id and outputs a preview of the image
	$('.hw_add_image').on('click',function(event) {
			var imageFrame;

		var that = $(this);
		event.preventDefault();
		
		var options, attachment;
		 var meta_hexwp_body_background_image = that.parents('.meta_hexwp_body_background_image');
		 var hexwp_text_remove = that.attr('data-remove');
		var $self = $(event.target);
		var $div = $self.closest(meta_hexwp_body_background_image);
		
		// if the frame already exists, open it
		if ( imageFrame ) {
			imageFrame.open();
			return;
		}
		
		imageFrame = wp.media({
 
			
			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: false
		});
		
		// set up our select handler
		imageFrame.on( 'select', function() {
			var selection = imageFrame.state().get('selection');
			
			if ( ! selection )
			return;
			
			// loop through the selected files
			selection.each( function( attachment ) {
				console.log(attachment);
				var src = attachment.attributes.sizes.full.url;
 				
				var data = '<a class="hw_remove_image button button-small">'+hexwp_text_remove+'</a><img  src="'+src+'"/>';
				$div.find('.hw_remove_image').remove();
				$div.find('img').remove();
 				$div.find('input').attr('value',src);	
 				$div.find('td').append(data);
  			} );
		});
		
		// open the frame
		imageFrame.open();
	});
	
	$(document).on('click', '.hw_remove_image', function(e) {
		$(this).parent().find('input').attr('value','');
		
		$(this).parent().find('img').remove()
		$(this).remove();
  	}); 
	 
  		
	 
  
 	//***************************************************************************************************************/
	/* Gallery
	/****************************************************************************************************************/
	function  gallery_jspn(){
		var data_array = new Array();
		$("#hexwp-gallery-metabox-list li").each(function(){
		
			data_array.push($(this).attr('data-id'));
	
		});
		var serialized = JSON.stringify(data_array);
 		 $("#hexwp_gallery_id_json").text(serialized);
	} 
	
	$('.hexwp_game_add_image').click(function(event) {
		var imageFrame;
 		var that = $(this);
 		var remove = $(this).attr('data-remove-text');
		event.preventDefault();
		var this_class= $('.meta_hexwp_game_body_background_image');
		var options, attachment;
		 var meta_hexwp_game_body_background_image = that.parents('.meta_hexwp_game_body_background_image');
		var $self = $(event.target);
		var $div = $self.closest(meta_hexwp_game_body_background_image);
		
		// if the frame already exists, open it
		if ( imageFrame ) {
			imageFrame.open();
			return;
		}
		
		imageFrame = wp.media({
  			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: false
		});
		
		// set up our select handler
		imageFrame.on( 'select', function() {
			var selection = imageFrame.state().get('selection');
			
			if ( ! selection )
			return;
			
			// loop through the selected files
			selection.each( function( attachment ) {
				console.log(attachment);
				var src = attachment.attributes.sizes.full.url;
				var id = attachment.attributes.id;
				var data = '<a class="hexwp_game_remove_image button button-small">'+remove+'</a><img  src="'+src+'"/>';
				this_class.find('.hexwp_game_remove_image').remove();
				this_class.find('img').remove();
 				this_class.find('input').attr('value',id);	
 				this_class.find('td').append(data);
  			} );
		});
		
		// open the frame
		imageFrame.open();
	});
	
	$(document).on('click', '.hexwp_game_remove_image', function(e) {
		$(this).parent().find('input').attr('value','');
 		$(this).parent().find('img').remove()
		$(this).remove();
		gallery_jspn();
		
  	}); 
	
 // 

  var file_frame;

  $(document).on('click', 'a.hexwp-gallery-add', function(e) {

    e.preventDefault();
	var  data_change = $(this).attr('data-change');
 	var  data_remove = $(this).attr('data-remove');
    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: true
    });

    file_frame.on('select', function() {
      var listIndex = $('#hexwp-gallery-metabox-list li').index($('#gao-allery-metabox-list li:last')),
          selection = file_frame.state().get('selection');

      selection.map(function(attachment, i) {
        attachment = attachment.toJSON(),
        index      = listIndex + (i + 1);
		console.log(attachment);
		var thumbnail_size='';
		if(attachment.sizes.thumbnail ===true ){
			thumbnail_size  = attachment.sizes.thumbnail.url ;
		}else{
			
			thumbnail_size  = attachment.sizes.full.url;
			
		}
		 

        $('#hexwp-gallery-metabox-list').append('<li data-id="' + attachment.id + '"  data-key="' + index + '"><img class="image-preview" src="' + thumbnail_size + '"><a class="change-image button button-small" href="#" data-uploader-title="'+data_change+'" data-uploader-button-text="'+data_change+'">'+data_change+'</a><br><small><a class="remove-image" href="#">'+data_remove+'</a></small></li>');
      });
	  	gallery_jspn();

    });

    makeSortable();
     file_frame.open();
 
  });

  $(document).on('click', '#hexwp-gallery-metabox-list a.change-image', function(e) {

    e.preventDefault();

    var that = $(this);

    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: false
    });

    file_frame.on( 'select', function() {
      var attachment = file_frame.state().get('selection').first().toJSON();
 
      that.parent().attr('data-id', attachment.id);
      that.parent().find('img.image-preview').attr('src', attachment.sizes.thumbnail.url);
	  gallery_jspn();

    });

    file_frame.open();
   });

  function resetIndex() {
    $('#hexwp-gallery-metabox-list li').each(function(i) {
      $(this).attr('data-key',  i );
    });
gallery_jspn();
	
  }

  function makeSortable() {
    $('#hexwp-gallery-metabox-list').sortable({
      opacity: 0.6,
      stop: function() {
        resetIndex();
		gallery_jspn();
      }
    });
  }

  $(document).on('click', '#hexwp-gallery-metabox-list a.remove-image', function(e) {
    e.preventDefault();

    $(this).parents('li').animate({ opacity: 0 }, 200, function() {
      $(this).remove();
      resetIndex();
		gallery_jspn();
	  
    });
  });

  makeSortable();


	gallery_jspn();
	
  
	gallery_jspn();
	
	$(window).on('load',function(e) {
		gallery_jspn();
  	});
 	 $('body').hexwp_coloris();


	});				

});
 