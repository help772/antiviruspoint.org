<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Product Config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_product_config( $args,$out = false) {
	wp_reset_query();
	wp_reset_postdata();
	$option = $args['option'];
	$key = $args['key']; 
	
	if(hexwp_element_show($option)=='show'){
		$option['post_type']='product';
		$more_posts =!empty($option['more_posts'])? $option['more_posts']:'';
		$data_key=$more_posts =="pagenavi"?' data-key="'.$key.'" ':'';
		
		
		$layout =!empty($option['layout'])?$option['layout']:'list';
		
		$between_class =!empty($option['between'])? $option['between']:hexwp_option('product_between');
		$ratio_class =!empty($option['ratio'])?$option['ratio']:hexwp_option('product_ratio');
		$ratio_second_class =!empty($option['ratio_2'])?$option['ratio_2'].'-second':'';
		
		
		$alignment_class =!empty($option['alignment'])?$option['alignment']:hexwp_option('product_alignment');
		$image_width =!empty($option['image_width'])? $option['image_width']:hexwp_option('product_image_width');
		$second_image_class =!empty($option['second_image'])?'hw-has-second':'';
		$box_layout =!empty($option['box_layout'])?  $option['box_layout']:hexwp_option('product_box_layout');
 		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';			 
		 $layout_class='';
		if($layout=='list'){
			$layout_class ='hw_img_width_'.$image_width;
			
		} 
 	$image_featured = empty(hexwp_ismobile()) && !empty( $option['image_featured']) ? $option['image_featured'] : '';	
 	$image_featured_width = !empty( $option['image_featured_width']) ? $option['image_featured_width'] : '1_2';	
 	$image_featured_column = !empty($image_featured)  && function_exists ( "hexwp_image_featured" ) ? '  hw-image-featured hw_col_'.$image_featured_width:'';
 	$image_featured_class = !empty($image_featured) && function_exists ( "hexwp_image_featured" ) ? ' vao-slider-featured ':'' ;
		
		$classes = array(
			'hw-el-'.$key,
			'hw-ajax-tab',
			'woocommerce',
			'hw-gap-'.$between_class,
			$ratio_class,
			$ratio_second_class,
			$second_image_class,
 			$image_featured_class,
			hexwp_between_border($option,$box_layout ),
			'hw-align-'.hexwp_alignment($alignment_class),
			'hw-'.$box_layout,
			$layout_class,
			hexwp_element_show($option,true),
			$custom_class		
	
		);
		
		
		ob_start(); 
		?>
		
		 <aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option).wp_kses_post($data_key);?> >
			
 			<?php hexwp_post_title_tabs($option,'hexwp_post_'.$layout);?>
			<div class="hw-gap-content">
 			<div class="hw-gap-warp">
            
				 <?php if(!empty($image_featured )&& function_exists ( "hexwp_image_featured" ) ){?><div class="hw-gap-container <?php echo  esc_attr($image_featured_column);?>"><?php hexwp_image_featured($option );?><?php } ?>

                <div class="hw-item-list hw-aw hw-flex <?php  echo esc_attr(hexwp_post_class($option));?>">
				 
					<?php 
					if($layout=='list'){
						hexwp_post_list($option);
					}
					if($layout=='grid'){
						 hexwp_post_grid($option);
					}
 					?>
					
				</div>
                
                
				<?php if(!empty($image_featured) && function_exists ( "hexwp_image_featured" )){?></div><?php }?>
                
                
				<?php 
				if($more_posts == 'load_more'){
					hexwp_load_more($option,'hexwp_post_'.$layout);						
				}elseif($more_posts =="pagenavi"){
					hexwp_pagenavi($option);  
				}	
				?>
 				
 			</div>
			</div>
		</aside> 
	
	 
		<?php
		$item = '.hw-el-'.$key.'';
		$item_css = hexwp_title_box_css($option,$item);
		$item_css.= hexwp_post_css($option,$item);
		$item_css.= hexwp_product_css($option,$item);
		$item_css.= hexwp_element_padding($option);
		$css =hexwp_item_css($item_css,$item);
		$return['output']=  ob_get_clean();
		$return['css']= $css;
		$return['emptybefore']= true;
		$return['emptyafter']= true;
	
		if( !empty($out)){
			$output = $return['output'];
			$output.=!empty($return['css'])?'<style>'.$return['css'].'</style>':'';
			return $output;
		}else{
			return $return;	
		}
	}
} 