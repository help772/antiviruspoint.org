<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Product Carousel Config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_product_carousel_config($args,$out = false) {
	wp_reset_query();
	wp_reset_postdata();
	$option = $args['option'];
	$key = $args['key']; 
	$option['key'] = $args['key']; 
 
	if(hexwp_element_show($option)=='show'){
		
 		$option['post_type']='product';
 		$layout =!empty($option['layout'])?$option['layout']:'list';
		$column =!empty($option['column'])?$option['column']:3;
		$row =!empty($option['row'])?$option['row']:1;
		$row_class =!empty($row )? 'hw_row_'.$row :'';

 		$between_class =!empty($option['between'])? $option['between']:hexwp_option('product_between');
		$ratio_class =!empty($option['ratio'])?$option['ratio']:hexwp_option('product_ratio');
		$alignment_class =!empty($option['alignment'])?$option['alignment']:hexwp_option('product_alignment');
		$image_width =!empty($option['image_width'])? $option['image_width']:hexwp_option('product_image_width');
		$second_image_class =!empty($option['second_image'])?'hw-has-second':'';
		$box_layout =!empty($option['box_layout'])?  $option['box_layout']:hexwp_option('product_box_layout');
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';	
		
			
		$image_featured = empty(hexwp_ismobile()) && !empty( $option['image_featured']) ? $option['image_featured'] : '';	
		$image_featured_width = !empty( $option['image_featured_width']) ? $option['image_featured_width'] : '1_1';	
		$image_featured_column = !empty($image_featured) && function_exists ( "hexwp_image_featured" ) ? '  hw-image-featured hw_col_'.$image_featured_width:'';
		$image_featured_class = !empty($image_featured)  && function_exists ( "hexwp_image_featured" ) ? ' vao-slider-featured ':'' ;
			
	 	$arrows = !empty($option['arrows'])?$option['arrows']:'';
		 
		$layout_class='';
		if($layout=='list'){
			$layout_class ='hw_img_width_'.$image_width;
			
		}  
		
		if($row=='3'){
			$ajax_load='slider_3';
		}elseif($row=='2'){
			$ajax_load='slider_2';
		}else{
			$ajax_load='main';
		}
		
		$classes = array(
			'hw-el-'.$key,
			'hw-slider',
			'hw-ajax-tab',
 			'hw-gap-'.$between_class,
			$ratio_class,
			hexwp_between_border($option,$box_layout ),
 			$second_image_class,
			'hw-align-'.hexwp_alignment($alignment_class),
			'hw-'.$box_layout,
			'woocommerce',
 			$layout_class,
 			$row_class,
			$image_featured_class,
			hexwp_element_show($option,true),
			$custom_class		
		);
		
		ob_start(); 
 		?>
		
		 <aside class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?> >
			
 			<?php hexwp_post_title_tabs($option,'hexwp_post_'.$layout.'_'.$ajax_load);?>
 
 			<div class="hw-gap-content  ">
			<div class="hw-gap-warp dragscroll <?php  echo esc_attr(hexwp_post_carousel_class($option));?>">
            
            	<?php if(!empty($image_featured ) && function_exists ( "hexwp_image_featured" )){?><div class="hw-gap-container <?php echo  esc_attr($image_featured_column);?>"><?php hexwp_image_featured($option );} ?>
                
				<div class="hw-item-list hw-aw hw-item-carousel ">
					
					<?php 
                    if($layout=='list'){
                         if($row=='1') hexwp_post_list_main($option); 
                         if($row=='2') hexwp_post_list_slider_2($option); 
                         if($row=='3')  hexwp_post_list_slider_3($option); 
                    }
                    if($layout=='grid'){
                          if($row=='1') hexwp_post_grid_main($option); 
                          if($row=='2')	hexwp_post_grid_slider_2($option); 
                          if($row=='3') hexwp_post_grid_slider_3($option); 
        
                    }
                    ?>
                        
				 </div>
				<?php if($arrows=='content'  && empty(hexwp_ismobile())){?>
					<div class="hw-arrow-warp"><a class="hw-arrow-prev"></a><a class="hw-arrow-next"></a></div>
				<?php }?>
            	<?php if(!empty($image_featured )&&function_exists ( "hexwp_image_featured" )){?></div> <?php } ?>
                 
			</div>
			</div>
  
               
			<?php
            $slider_options = array(); 	
            $slider_options['speed']=  !empty($option['speed']) ? $option['speed'] : '1000';
            $slider_options['pause']= !empty($option['pause']) ? $option['pause'] : '5000';
            $slider_options['between']=   '0';	
            $slider_options['pager']= true;
            $slider_options['timer']= false;	
            $slider_options['controls']=!empty($option['arrows']) ? true : '';
            $slider_options['auto']=  !empty($option['auto']) ? true : '';
            hexwp_lightslider($column,$slider_options);
			?>    
		   
		</aside> 
	
	 
	 <?php
		$item = '.hw-el-'.$key.'';
		$item_css = hexwp_title_box_css($option,$item);
		$item_css.= hexwp_product_css($option,$item);
		$item_css.= hexwp_post_css($option,$item);
		$item_css.= hexwp_caption_css($option,$item);
		$item_css.= hexwp_element_padding($option);
 		$item_css.= hexwp_arrow_layout_css($option);
		 
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