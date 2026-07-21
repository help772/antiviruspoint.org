<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Config
																		
*/////////////////////////////////////// /////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_blog_config( $args,$out=false) {
	wp_reset_query();
	wp_reset_postdata();
	$option = $args['option'];
	$key = $args['key']; 
	if(hexwp_element_show($option)=='show'){
   		$option['post_type']='post';
		$more_posts =!empty($option['more_posts'])? $option['more_posts']:'';
		$data_key=$more_posts =="pagenavi"?' data-key="'.$key.'" ':'';
		$layout =!empty($option['layout'])?$option['layout']:'list';
		$between_class =!empty($option['between'])? $option['between']:hexwp_option('blog_between');
		$ratio_class =!empty($option['ratio'])?$option['ratio']:hexwp_option('blog_ratio');
		$ratio_second_class =!empty($option['ratio_2'])?$option['ratio_2'].'-second':'';
		$alignment_class =!empty($option['alignment'])?$option['alignment']:hexwp_option('blog_alignment');
		$image_width =!empty($option['image_width'])? $option['image_width']:hexwp_option('blog_image_width');
		$image_width_2 =!empty($option['image_width_2'])? $option['image_width_2']:'';
		$box_layout =!empty($option['box_layout'])? $option['box_layout']:hexwp_option('blog_box_layout');
		$caption_layout =!empty($option['caption_layout'])? $option['caption_layout']: hexwp_option('blog_caption_layout');
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';			 
	
		$layout_class='';
		if($layout=='list'){
			$layout_class ='hw_img_width_'.$image_width;
			if(!empty($image_width_2)){
				$layout_class.=' hw_img_width_'.$image_width_2.'-second ';
			}
			$layout_class.= ' hw-'.$box_layout; 	
			
		}elseif($layout=='grid'){	
			$layout_class.= 'hw-'.$box_layout; 
				
		}elseif($layout=='featured'){	
			$layout_class='hw-cap-'.$caption_layout;
		 } 
		
		$classes = array(
			'hw-el-'.$key,
			'hw-ajax-tab',
 			'hw-gap-'.$between_class,
			$ratio_class,
			$ratio_second_class,
			'hw-align-'.hexwp_alignment($alignment_class),
			$layout_class,
			hexwp_between_border($option,$box_layout ),
		
			$custom_class,
			hexwp_image_caption_effect($option),	
			hexwp_element_show($option,true),
		); 
		
		ob_start(); 
		?>
		
		 <aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option).wp_kses_post($data_key);?> >
			
			
			<?php hexwp_post_title_tabs($option,'hexwp_post_'.$layout);?>
			<div class="hw-gap-content">
			<div class="hw-gap-warp">
				<div class="hw-item-list hw-flex hw-aw <?php  echo esc_attr(hexwp_post_class($option));?>">
				
					<?php 
					if($layout=='list'){
						hexwp_post_list($option);
					}
					if($layout=='grid'){
						 hexwp_post_grid($option);
					}
					if($layout=='featured'){
						 hexwp_post_featured($option);
					}
					?>
					
				</div>
				
				
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
		$item_css.= hexwp_caption_css($option,$item);
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
 