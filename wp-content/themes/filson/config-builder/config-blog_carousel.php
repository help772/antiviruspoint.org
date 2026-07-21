<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Carousel config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_blog_carousel_config( $args,$out = false ) {
	wp_reset_query();
	wp_reset_postdata();
	$option = $args['option'];
	$key = $args['key']; 
	if(hexwp_element_show($option)=='show'){
	
 		$option['post_type']='post';
		$arrows =!empty($option['arrows'])?$option['arrows']:'';
		$layout =!empty($option['layout'])?$option['layout']:'list';
		$column =!empty($option['column'])?$option['column']:3;
		$between_class =!empty($option['between'])? $option['between']:hexwp_option('blog_between');
		$ratio_class =!empty($option['ratio'])?$option['ratio']:hexwp_option('blog_ratio');
		$alignment_class =!empty($option['alignment'])?$option['alignment']:hexwp_option('blog_alignment');
		$image_width =!empty($option['image_width'])? $option['image_width']:hexwp_option('blog_image_width');
		$box_layout =!empty($option['box_layout'])? $option['box_layout']:hexwp_option('blog_box_layout');
		$caption_layout =!empty($option['caption_layout'])? $option['caption_layout']: hexwp_option('blog_caption_layout');
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';			 
	
		$layout_class='';
		
		$layout_class='';
		if($layout=='list'){
			$layout_class ='hw_img_width_'.$image_width;
			$layout_class.= ' hw-'.$box_layout; 	
			
		}elseif($layout=='grid'){	
			$layout_class= 'hw-'.$box_layout; 
				
		}elseif($layout=='featured'){	
			$layout_class='hw-cap-'.$caption_layout;
		} 
		
		$classes = array(
			'hw-el-'.$key,
			 'hw-slider',
			'hw-ajax-tab',
 			'hw-gap-'.$between_class,
			$ratio_class,
			'hw-align-'.hexwp_alignment($alignment_class),
			$layout_class,
			hexwp_between_border($option,$box_layout ),
			
			hexwp_image_caption_effect($option),	
			hexwp_element_show($option,true),
			$custom_class,
		);
		
		ob_start(); 
		?>
		
			<aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?> >
			
				<?php hexwp_post_title_tabs($option,'hexwp_post_'.$layout.'_main');?>
				<div class="hw-gap-content">
                <div class="hw-gap-warp dragscroll <?php  echo esc_attr(hexwp_post_carousel_class($option));?>">
                <div class="hw-item-list  hw-aw hw-item-carousel">
                   
                    <?php 
                    if($layout=='list'){
                        hexwp_post_list_main($option);
                    }
                    if($layout=='grid'){
                         hexwp_post_grid_main($option);
                    }
                    if($layout=='featured'){
                         hexwp_post_featured_main($option);
                    }
                    ?>
                    
                </div>
                
                		<?php if($arrows=='content'  && empty(hexwp_ismobile())){?>
					<div class="hw-arrow-warp"><a class="hw-arrow-prev"></a><a class="hw-arrow-next"></a></div>
				<?php }?>
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
 