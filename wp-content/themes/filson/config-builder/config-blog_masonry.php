<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Blog Masonry Config
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_masonry_config( $args,$out = false  ) {
	wp_reset_query();
	wp_reset_postdata();
	$option = $args['option'];
	$key = $args['key']; 
	
	if(hexwp_element_show($option)=='show'){

		$option['post_type']='post';
		$more_posts =!empty($option['more_posts'])? $option['more_posts']:'';
		$layout =!empty($option['layout'])?$option['layout']:'grid';
		$column =!empty($option['column'])?$option['column']:'4';
		$between_class =!empty($option['between'])? $option['between']:hexwp_option('blog_between'); 	
		$ratio_class =!empty($option['ratio'])?$option['ratio']:hexwp_option('blog_ratio');
		$alignment_class =!empty($option['alignment'])?$option['alignment']:hexwp_option('blog_alignment');
		$box_layout =!empty($option['box_layout'])? $option['box_layout']:hexwp_option('blog_box_layout');
		$caption_layout =!empty($option['caption_layout'])? $option['caption_layout']: hexwp_option('blog_caption_layout');	
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';			 
		
 		$layout_class='';
		if($layout=='featured'){	
			$layout_class='hw-cap-'.$caption_layout;
			$layout_class.=' hw-ratio-auto';
			
		 }else{
			$layout_class= ' hw-'.$box_layout; 
			$layout_class.= ' '.$ratio_class; 
			
		 }
 			
		$classes = array(
			'hw-el-'.$key,
			 'macy-masonry',
			'hw-ajax-tab',
			'hw-gap-'.$between_class,
			'hw-align-'.hexwp_alignment($alignment_class),
			$layout_class,
			hexwp_between_border($option,$box_layout ),
	
			hexwp_image_caption_effect($option),	
			hexwp_element_show($option,true),
			$custom_class,
	
		);
		
		
		ob_start(); 
		 
		?>
		 <aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_post_masonry_class($option,true) ;?> <?php echo hexwp_el_cssanime($option);?> >
			
			
			<?php hexwp_post_title_tabs($option,'hexwp_post_'.$layout.'_main');?>
						<div class="hw-gap-content">
			<div class="hw-gap-warp">
				<div class="hw-item-list hw-masonry  hw-aw <?php echo esc_attr(hexwp_post_masonry_class($option));?>"  >
 
 					<?php 
					if($layout=='grid'){
						 hexwp_post_grid_main($option);
					}
					if($layout=='featured'){
						 hexwp_post_featured_main($option);
					}
					?>
                    
 				</div>
 				
				<?php 
				if($more_posts == 'load_more'){
					hexwp_load_more($option,'hexwp_post_'.$layout.'_main');
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
		
		wp_enqueue_script( 'hexwp-masonry', hexwp_DIR . '/js/lib/masonry.min.js');
	
		if( !empty($out)){
			$output = $return['output'];
			$output.=!empty($return['css'])?'<style>'.$return['css'].'</style>':'';
			return $output;
		}else{
			return $return;	
		}
	}
}
 