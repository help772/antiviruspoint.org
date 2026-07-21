jQuery(function($) {
 	jQuery(document).ready(function($) {
		"use strict";
		/*************************************************************************************************************
		------------------------------------------------------ Plan List------------------------------------------------
		/************************************************************************************************************/
		$('.pt-plan-list').sortable({
			opacity: 0.6,
			  stop: function() {}
		});	
		/*************************************************************************************************************
		-----------------------------------------------------details Row------------------------------------------------
		/************************************************************************************************************/
		function input_value(){
  			  $('.hw-pricetable input[type="text"]').each(function(i) {
				  $(this).attr('value',$(this).val());
			  });
		}
 		 function details_row(){
							 
			 
 			  $('.dt-head-item').each(function(i) {
				  $(this).attr('data-row',i+1);
			  });  
 			  $('.dt-content-list').each(function(i) {
				  
				  $(this).find('.dt-content-item').each(function(i) {
					  $(this).attr('data-row',i+1);
				  });  
			  });  
			  
			  
			   
			  
			  
			$('.dt-content-item').each(function(i) {
				  var row = $(this).attr('data-row');
				  var value = $('.dt-head-item[data-row="'+row+'"] input:nth-child(2)').val();
				 $(this).find('input:nth-child(2)').attr('placeholder',value);
				  
			  }); 
			   
			 
				$('.pt-plan-item-heading').each(function(i) {
				var index = i+1;
 				 $(this).find('input').attr('id','label_'+index);
 				 $(this).find('label').attr('for','label_'+index);

  			  });  
			  
				 
			  
			  
		}
		 function details_sortable(){
			$('.dt-head-list,.dt-content-list').sortable({
				opacity: 0.6,
				stop: function() {
					details_row();
				}
			});			
		}
		details_sortable();
		/*************************************************************************************************************
		------------------------------------------------------Add Plan------------------------------------------------
		/************************************************************************************************************/
		$(document).on('click', '.hw-pricetable .pt-add-plan', function(e) {
			var key =  Math.floor(Math.random() * 99999999999);
			
			var plan='';
				plan+= '<div class="pt-plan-item">'; 		
				
					plan+= '<div class="pt-plan-item-heading">';
					
 							plan+= '<input type="radio" id="label_1" name="recommened"  value="'+key+'">';
							plan+= '<label for="label_1">'+pricetable_text.recommend+'</label>';
					
						plan+= '<div class="pt-plan-item-move"></div>';
						plan+= '<div class="pt-plan-item-remove"></div>';
						plan+= '<div class="pt-plan-item-duplicate"></div>';
								 
					 plan+= '</div>';			
					plan+= '<ul class="pt-content-list">'; 
 						plan+= '<li class="pt-content-item pt-content-item-value">'; 
						plan+= '<input type="hidden" name="plan[' + key + '][value]"  value="'+key+'">'; 
						plan+= '</li>'; 
 					 
 						jQuery(".pt-head-item").each(  function(event) {
							var text = $(this).text();
							var id = $(this).attr('data-id');
 							
							plan+= '<li class="pt-content-item">'; 
							plan+= '<input type="text" name="plan[' + key + ']['+id+']" placeholder="'+text+'" value="">'; 
							plan+= '</li>'; 
 						});
					
					  
					 plan+='<ul class="dt-content-list">';
					jQuery(".dt-head-item").each(  function(event) {
						var details_key =  Math.floor(Math.random() * 99999999999);

						var text = $(this).find('span').text();
						var row = $(this).attr('data-row');
 						plan+= '<li class="dt-content-item" data-row="'+row+'" data-key="'+details_key+'">'; 
							plan+= '<input type="hidden" name="details[' + details_key + '][child]" placeholder="'+text+'" value="'+key+'">'; 
							plan+= '<input type="text" name="details[' + details_key + '][value]" placeholder="'+text+'" value="">'; 
							plan+= '<div class="dt-content-item-move"></div>'; 
							
						plan+= '</li>'; 
 					}); 
				 
				
  
					plan+= '</ul>'; 
					plan+= '</ul>'; 
				plan+= '</div>'; 
				 
			$('.pt-plan-list').append(plan);
 			 
 			 details_row();
			 details_sortable();
		});
			
 		/*************************************************************************************************************
		------------------------------------------------------Remove Plan------------------------------------------------
		/************************************************************************************************************/
		 
		$(document).on('click', '.pt-plan-item-remove', function(e) {
			var r = confirm(pricetable_text.remove_plan);
			if (r == true) {
				$(this).parents('.pt-plan-item').remove();
			}
	 	});	
		
 		/*************************************************************************************************************
		------------------------------------------------------Duplicate------------------------------------------------
		/************************************************************************************************************/
		function hexwp_duplicate(items,old_key,new_key){
			items.find('input').each(function(event) {
				var name = $(this).attr('name');		 
				var new_name = name.replace(old_key,new_key);		 
				$(this).attr('name',new_name);
				
				
				var value = $(this).attr('value');		 
				var new_value = value.replace(old_key,new_key);		 
				$(this).attr('value',new_value);
				 
				
				
			});
				
		} 
 		/*************************************************************************************************************
		-----------------------------------------------------Plan-Duplicate------------------------------------------------
		/************************************************************************************************************/
		$(document).on('click', '.pt-plan-item-duplicate', function(e) {
			input_value();

			if($(this).parent().find('input').is(':checked') === true) {
        	 var checked = true;
 			}else{
        	 var checked = false;
			}
			
			var plan = $(this).parents('.pt-plan-item').html();
			var old_key = $(this).parents('.pt-plan-item').attr('data-key');
			var new_key =  Math.floor(Math.random() * 99999999999);
			 
			var duplicate='';
				duplicate+= '<li class="pt-plan-item pt-duplicate-new" data-key="'+new_key+'">'; 
				duplicate+= plan; 
				duplicate+= '</li>'; 
			$(this).parents('.pt-plan-item').after(duplicate);
						
			hexwp_duplicate($(".pt-duplicate-new"),old_key,new_key);
		
			$(".pt-duplicate-new").find('.dt-content-item').each(function(event) {
				var details_old_key	= $(this).attr('data-key');

				var details_new_key =  Math.floor(Math.random() * 99999999999);
				$(this).attr('data-key',details_new_key);
				hexwp_duplicate($(this),details_old_key,details_new_key);
 
				
			});
 		 
  			
   			$('.pt-plan-item-has-duplicate').removeClass('pt-duplicate-new');
 						
						
			details_row();
			 details_sortable();
			
			if(checked==true){
				$(this).parent().find('input').prop('checked', true);
			}
	
	 	});		
 	 
 		/*************************************************************************************************************
		-----------------------------------------------------add Details------------------------------------------------
		/************************************************************************************************************/
		
		$(document).on('click', '.hw-pricetable .dt-add-details', function(e) {
			$(this).parent().addClass('dt-add-active-option');
 	 	});		
 		/*************************************************************************************************************
		-----------------------------------------------------cencel Details------------------------------------------------
		/************************************************************************************************************/
		
		$(document).on('click', '.hw-pricetable .dt-add-details-cencel', function(e) {
			$(this).parents('.dt-add-active-option').removeClass('dt-add-active-option');
 	 	});		
 		/*************************************************************************************************************
		-----------------------------------------------------add add Details------------------------------------------------
		/************************************************************************************************************/
		$(document).on('click', '.hw-pricetable .dt-add-details-add', function(e) {
			var heading_key =  Math.floor(Math.random() * 99999999999);
			var details =  $(this).prev().val();
			
			var heading='';
				heading+= '<li class="dt-head-item"  data-key="'+heading_key+'">'; 
					heading+= '<input type="hidden" name="details['+heading_key+'][child]"  value="heading">'; 
					heading+= '<input type="hidden" name="details['+heading_key+'][value]"  value="'+details+'">'; 
					heading+= '<div class="dt-head-item-heading">';
						heading+= '<div class="dt-head-item-remove"></div>';
						heading+= '<div class="dt-head-item-duplicate"></div>';
						heading+= '<div class="dt-head-item-move"></div>';
						heading+= '<div class="dt-head-item-edit"></div>';
					heading+= '</div>';
            
							
					heading+= '<span>'+details+'</span>'; 
				heading+= '</li>'; 
				$('.dt-head-list').append(heading);
 
					 
				$(".dt-content-list").each(function(event) {
					var content_key =  Math.floor(Math.random() * 99999999999);
					var child = $(this).parents('.pt-plan-item').attr('data-key');
					
					var content='';
						content+= '<li class="dt-content-item"  data-row="" data-key="'+content_key+'">'; 
							content+= '<input type="hidden"  name="details['+content_key+'][child]"  value="'+child+'"></input>'; 
							content+= '<input type="text" name="details['+content_key+'][value]" placeholder="'+details+'"   value="">'; 
							content+= '<div class="dt-content-item-move"></div>'; 
						content+= '</li>'; 
						$(this).append(content);
 							
				});
						 
  
				details_row();
				details_sortable();
				

			});
 		/*************************************************************************************************************
		-----------------------------------------------------remove Details------------------------------------------------
		/************************************************************************************************************/
  		$(document).on('click', '.dt-head-item-remove', function(e) {
				var row = $(this).parent().parent().attr('data-row');
 
				
				var r = confirm(pricetable_text.remove_details);
 				if (r == true) {
					$('[data-row="'+row+'"]').each(function(event) {
					$(this).remove();
					});
				}
			 
			 	details_row();
				details_sortable();
						
	 	});	
		/*************************************************************************************************************
		-----------------------------------------------------remove duplicate------------------------------------------------
		/************************************************************************************************************/	
 		$(document).on('click', '.dt-head-item-duplicate', function(e) {
			var old_row = $(this).parent().parent().attr('data-row');
						input_value();

   			$('.dt-head-item[data-row="'+old_row+'"]').each(function(event) {
 				var old_key = $(this).attr('data-key');
  				var new_key =  Math.floor(Math.random() * 99999999999);
  				var html = $(this).html();
 				var dt_head_duplicate='';
					dt_head_duplicate+= '<li class="dt-head-item dt-duplicate-new" data-row="" data-key="'+new_key+'">'; 
					dt_head_duplicate+= html; 
					dt_head_duplicate+= '</li>';
					$(this).after(dt_head_duplicate);
 					hexwp_duplicate($('.dt-duplicate-new'),old_key,new_key); 		
  			});	
	 
			$('.dt-content-item[data-row="'+old_row+'"]').each(function(event) {
 				
				var old_key = $(this).attr('data-key');
  				var new_key =  Math.floor(Math.random() * 99999999999);
 				var html = $(this).html();
 				var dt_content_duplicate='';
				dt_content_duplicate+= '<li class="dt-content-item dt-duplicate-new" data-row="" data-key="'+new_key+'" >'; 
				dt_content_duplicate+= html; 
				dt_content_duplicate+= '</li>';
				$(this).after(dt_content_duplicate);
 				hexwp_duplicate($('.dt-duplicate-new'),old_key,new_key); 	

 			});
  	 
			$(".dt-duplicate-new").each(function(event) {
 				$(this).removeClass('dt-duplicate-new');					
			});				
			details_row();
			details_sortable();
	 	});		
		/*************************************************************************************************************
		-----------------------------------------------------edit duplicate------------------------------------------------
		/************************************************************************************************************/	
		$(document).on('click', '.dt-head-item-edit', function(e) {
 				var value = $(this).parent().parent().find('input:nth-child(2)').val();
 
		 
 				var edit='';
				edit+= '<div class="dt-edit">'; 
					edit+= '<input value="'+value+'">'; 
 					edit+= '<div class="dt-edit-details-cencel button   button-medium">'+pricetable_text.cancel+'</div>';
 					edit+= '<div class="dt-edit-details-edit button button-primary button-medium">'+pricetable_text.edit+'</div>';
 				edit+= '</dv>';
				$(this).parents('.dt-head-item').append(edit); 
				details_row();
 	 	});	
		
		$(document).on('click', '.dt-edit-details-cencel', function(e) {
 			$(this).parent().remove();
   	   
	 	});	

		$(document).on('click', '.dt-edit-details-edit', function(e) {
			var value = $(this).parent().find('input').val();
			 $(this).parent().parent().find('input:nth-child(2)').val(value);
			 $(this).parent().parent().find('span').text(value);
			details_row();
  			$(this).parent().remove();
   	   
	 	});			
				
	/***********************************************************************************************************************************************************
	Detailshead Top
	***********************************************************************************************************************************************************/	
	/*			
		$(document).on('click', '.pt-detailshead-top', function(e) {
			var old_row = $(this).parent().parent().attr('data-row');
 			$('.pt-detailshead-item[data-row="'+old_row+'"]').each(function(event) {
				 $(this).addClass('pt-detailshead-old-next');
				var old_key = $(this).attr('data-key');
   				
				
				var html = $(this).html();
 				var planrow='';
				planrow+= '<li class="pt-head-item pt-detailshead-item pt-detailshead-has-next" data-row="'+old_row+'" data-key="'+old_key+'" >'; 
				planrow+= html; 
				planrow+= '</li>';
				$(this).prev().before(planrow); 	
 				
 			});	
			 
			$('.pt-planrow-item[data-row="'+old_row+'"]').each(function(event) {
				 $(this).addClass('pt-detailshead-old-next');
				var old_key = $(this).attr('data-key');
  				var html = $(this).html();
 				var planrow='';
				planrow+= '<li class="pt-planrow-item  pt-detailsrow-item  pt-detailshead-has-next" data-row="'+old_row+' data-key="'+old_key+'" >'; 
				planrow+= html; 
				planrow+= '</li>';
				$(this).prev().before(planrow);

 			}); 
			$('.pt-detailshead-old-next').each(function(event) {
				$(this).remove();
			});
			$(".pt-detailshead-has-next").each(function(event) {
 				$(this).removeClass('pt-detailshead-has-next');					
			});				
 
		});
		
 	/***********************************************************************************************************************************************************
	Detailshead Bottom
	***********************************************************************************************************************************************************			
		$(document).on('click', '.pt-detailshead-bottom', function(e) {
			var old_row = $(this).parent().parent().attr('data-row');
 			$('.pt-detailshead-item[data-row="'+old_row+'"]').each(function(event) {
				 $(this).addClass('pt-detailshead-old-next');
				var old_key = $(this).attr('data-key');
   				
				
				var html = $(this).html();
 				var planrow='';
				planrow+= '<li class="pt-head-item pt-detailshead-item pt-detailshead-has-next" data-row="'+old_row+'" data-key="'+old_key+'" >'; 
				planrow+= html; 
				planrow+= '</li>';
				$(this).next().after(planrow); 	
 				
 			});	
			 
			$('.pt-planrow-item[data-row="'+old_row+'"]').each(function(event) {
				 $(this).addClass('pt-detailshead-old-next');
				var old_key = $(this).attr('data-key');
  				var html = $(this).html();
 				var planrow='';
				planrow+= '<li class="pt-planrow-item pt-detailsrow-item  pt-detailshead-has-next" data-row="'+old_row+' data-key="'+old_key+'" >'; 
				planrow+= html; 
				planrow+= '</li>';
				$(this).next().after(planrow);

 			});
			$('.pt-detailshead-old-next').each(function(event) {
				$(this).remove();
			});
			$(".pt-detailshead-has-next").each(function(event) {
 				$(this).removeClass('pt-detailshead-has-next');					
			});				
 
		});
*/
		 		details_row();

			 
	});
});
 			