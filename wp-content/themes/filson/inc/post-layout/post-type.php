<?php
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Module 1
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_module_1($option,$item_1=false,$item_2=false,$item_3=false,$item_4=false){
	$post_type = hexwp_data($option,'post_type');
 	if($post_type=='post'){
		
		if(function_exists('hexwp_blog_module_1')){
			hexwp_blog_module_1($option,$item_1,$item_2,$item_3,$item_4 );		
		}
		
	}elseif($post_type=='portfolio'){
		
		if(function_exists('hexwp_portfolio_module_1')){
 			hexwp_portfolio_module_1($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}elseif($post_type=='product'){
		
		if(function_exists('is_woocommerce')){
 			hexwp_product_module_1($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}elseif($post_type=='testimonial'){
		
		if(function_exists('hexwp_testimonial_module_1')){
 			hexwp_testimonial_module_1($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}  elseif($post_type=='staff'){
		
		if(function_exists('hexwp_staff_module_1')){
 			hexwp_staff_module_1($option,$item_1,$item_2,$item_3,$item_4 );
		}	
		
 	
	} 
}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Module 2
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_module_2($option,$item_1=false,$item_2=false,$item_3=false,$item_4=false){
	$post_type = hexwp_data($option,'post_type');
 	if($post_type=='post'){
		
		if(function_exists('hexwp_blog_module_2')){
			hexwp_blog_module_2($option,$item_1,$item_2,$item_3,$item_4 );		
		}
		
	}elseif($post_type=='portfolio'){
		
		if(function_exists('hexwp_portfolio_module_2')){
 			hexwp_portfolio_module_2($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}elseif($post_type=='product'){
		
		if(function_exists('is_woocommerce')){
 			hexwp_product_module_2($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}elseif($post_type=='testimonial'){
		
		if(function_exists('hexwp_testimonial_module_2')){
 			hexwp_testimonial_module_2($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}  elseif($post_type=='staff'){
		
		if(function_exists('hexwp_staff_module_2')){
 			hexwp_staff_module_2($option,$item_1,$item_2,$item_3,$item_4 );
		}	
		
 	
	} 
}

 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Module 3
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_module_3($option,$item_1=false,$item_2=false,$item_3=false,$item_4=false){
	$post_type = hexwp_data($option,'post_type');
 	if($post_type=='post'){
		
		if(function_exists('hexwp_blog_module_3')){
			hexwp_blog_module_3($option,$item_1,$item_2,$item_3,$item_4 );		
		}
		
	}elseif($post_type=='portfolio'){
		
		if(function_exists('hexwp_portfolio_module_3')){
 			hexwp_portfolio_module_3($option,$item_1,$item_2,$item_3,$item_4 );
		}
 	
	}elseif($post_type=='staff'){
		
		if(function_exists('hexwp_staff_module_3')){
 			hexwp_staff_module_3($option,$item_1,$item_2,$item_3,$item_4 );

		}	
		
 	
 	}elseif($post_type=='sao_slide'){
		
		if(function_exists('sao_slide_post_type')){
 			hexwp_slider_module_3($option,$item_1);
		}	
		
 	
	} 
}
