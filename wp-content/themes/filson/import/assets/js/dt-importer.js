jQuery(document).ready(function($){

 
   	function ajax_post(data,$this,obj ,i,total) {
		
		$this.attr('number-import',i);
		data['homepage_import']=obj[i];
		
		$this.find('.dt-load-import').text('Import:'+obj[i]);
		jQuery.post(ajaxurl, data, function(response){

			$this.find('.dt-importer-response').addClass('active');
			var conut = 100 / total;
			var width = Math.round(i * conut);
			$this.find('.dt-loading').attr('style',' width:'+ width +'%;');
			
 			$this.find('.dt-load-text').text(width+'%');
 			$this.find('.dt-importer-response .dt-response').append('<div class="dt-response-item"><h3>'+obj[i]+':</h3>'+response+'</div>');
			var v = i  + 1;

   			if(obj[v]==null){ 
			$this.find('.dt-importer-response').removeClass('dt-wait').addClass('dt-complate');
			
  			}else{
				
				ajax_post(data,$this,obj ,v,total);
			}
		});
	
	}	

 
	 
 
 
 
  
    $('.dt-demo-item-homepage').find('.button-import').click(function(e) {
			var $this=	jQuery(this).parents('.dt-demo-item');
	
        e.preventDefault();
			var $this=	jQuery(this).parents('.dt-demo-item');
  		 	var array = JSON.parse($this.find('.dt-option-json').html());
 			var nonce = $(this).data('nonce');
			var demo = $(this).data('import');
  	
			var image_data_name = $(this).parents('.dt-demo-item').find("#image-data-name").val();
			var image_data_id = $(this).parents('.dt-demo-item').find("#image-data-id").val();
			var image_data_slug = $(this).parents('.dt-demo-item').find("#image-data-slug").val();
			var image_data_url = $(this).parents('.dt-demo-item').find("#image-data-url").val();
			var data_option = $(this).attr('data-option');
	  

           var data = {
                action: 'hexwpdemoimport_Importer',
                nonce: nonce,
                id: 'custom',
				homepage_import:'0',
				
                  image_name: image_data_name,
                image_id: image_data_id,
                image_slug: image_data_slug,
                image_url: image_data_url,
 
				
				
				
            };
			$this.find('.dt-importer-response').addClass('active');
			$this.find('.dt-importer-response').addClass('active').addClass('dt-wait');
			
 			ajax_post(data,$this,array,1,16);
			 
		 
		 
     });


    $('.dt-demo-item-custom').find('.button-import').click(function(e) {
		
	
        e.preventDefault();
			var $this=	jQuery(this).parents('.dt-demo-item');
  		 	var array = JSON.parse($this.find('.dt-option-json').html());
 			var nonce = $(this).attr('data-nonce');
			var demo = $(this).attr('data-import');
			var option = $(this).attr('data-option');
			var total = $(this).attr('data-total');
 			var import_mini = array[option];
 

           var data = {
                action: 'hexwpdemoimport_Importer',
                nonce: nonce,
                id: 'custom',
				homepage_import:'0',
				  
				
            };
			$this.find('.dt-importer-response').addClass('active');
			$this.find('.dt-importer-response').addClass('active').addClass('dt-wait');
 			ajax_post(data,$this,import_mini,1,total);
			 
		 
		 
     }); 
	
	
    jQuery('.dismiss').click(function() {
  
        $(this).parent().removeClass('active').removeClass('dt-wait').removeClass('dt-complate');
		$(this).parent().find('.dt-response,.dt-load-import').html('').text('');
		$(this).parent().find('.dt-load-text').text('0%');
		
		 
    });

 
jQuery(document).on("click", ".dt-option-item" , function(){
	jQuery('.dt-option-item').each(function() {
		$(this).removeClass('dt-active');	
	});
  	var id =$(this).attr('data-id');
  	var total =$(this).attr('data-total');
	$(this).addClass('dt-active');
	$(this).parents('.dt-demo-item').find('.dt-demo-actions a').attr('data-option',id);
	$(this).parents('.dt-demo-item').find('.dt-demo-actions a').attr('data-total',total);
 
	
});});


