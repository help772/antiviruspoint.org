<?php
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Contact Form 7 Config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_product_cat_config( $args , $out = false ) {
  
 
 	$option = $args['option'];
	$key = $args['key'];
	if(hexwp_element_show($option)=='show'){
		$attr_array = array(
			'number'		=> !empty($option['number'])? intval($option['number']):'',
			'columns'		=> !empty($option['column'])?$option['column']:'',
			'hide_empty'	=> !empty($option['hide_empty'])?1:'',
			'orderby'		=> !empty($option['orderby'])?$option['orderby']:'',
			'order'			=> !empty($option['order'])?$option['order']:'',
		);
		$source = !empty($option['source'])?$option['source']:'';
		$categories = !empty($option['categories'])?$option['categories']:'';
		$parent = !empty($option['parent'])?$option['parent']:'';
		 
		if ( 'by_id' === $source ) {
				$attr_array['ids'] = implode( ',', array_keys($categories) );
			} elseif ( 'by_parent' === $source ) {
				$attr_array['parent'] = $parent;
			} elseif ( 'current_subcategories' === $source ) {
				$attr_array['parent'] = get_queried_object_id();
			}
		$attr='';
 		foreach($attr_array as $attr_key => $attr_value){
			$attr.=' '.$attr_key.'="'.$attr_value.'" ';
			
		} 
		 
		
		
		$between_class =!empty($option['between'])? $option['between']:'20px';
		$ratio_class =!empty($option['ratio'])?$option['ratio']:'hw-ratio-auto';
		$box_layout =!empty($option['box_layout'])?  $option['box_layout']:hexwp_option('product_box_layout');
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';			 
		$hide_title = !empty( $option['hide_title']) ? 'hw-hide-title' : '';			 
		$hide_count = !empty( $option['hide_count']) ? 'hw-hide-count' : '';			 
		 
		$classes = array(
			'hw-el-'.$key,
			'hw-gap-'.$between_class,
			$ratio_class,
			$hide_title,
			'hw-el-product_cat',
			hexwp_between_border($option,$box_layout ),
			'woocommerce',
			$hide_count,
			'hw-'.$box_layout,
			hexwp_element_show($option,true),
			$custom_class		
	
		);
		
		ob_start(); 
		
		?>
        
		<aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?> >
        
        			<?php hexwp_post_title_tabs($option);?>
 
			<div class="hw-gap-content">
             <div class="hw-gap-warp">
				<?php echo do_shortcode("[product_categories ".$attr."]");?>
			</div>
			</div>
		</aside>
        
		<?php
		$item = '.hw-el-'.$key.'';
		$item_css = hexwp_post_css($option,$item);
		$item_css.= hexwp_product_css($option,$item);
		$item_css.= hexwp_var('--hw-product-count',$option,'count_color').  
 		$item_css.= hexwp_element_padding($option);
		$css =hexwp_item_css($item_css,$item);
		 
		 
		$responsive_column =!empty($option['responsive_column'])? $option['responsive_column']:'';
		$resp_css='';
		if($responsive_column=='tab_1_mob_1'){
			$resp_css.= ' --hw-tab:100%!important; --hw-mob:100% !important;';
		}elseif($responsive_column=='tab_2_mob_1'){
			$resp_css.= ' --hw-tab-:50% !important;--hw-mob:100% !important;';
		}elseif($responsive_column=='tab_3_mob_1'){
			$resp_css.= ' --hw-tab:33.331% !important; --hw-mob:100% !important;';
		}elseif($responsive_column=='tab_4_mob_1'){
			$resp_css.= ' --hw-tab:25 %!important;--hw-mob:100% !important;';
		}elseif($responsive_column=='tab_2_mob_2'){
			$resp_css.= ' --hw-tab:50% !important;--hw-mob:50%!important;';
		}elseif($responsive_column=='tab_3_mob_2'){
			$resp_css.= ' --hw-tab:33.33%!important;--hw-mob:50% !important;';
		}elseif($responsive_column=='tab_4_mob_2'){
			$resp_css.= ' --hw-tab:25% !important;--hw-mob:50% !important;';
		} 
 		$css.=hexwp_item_css($resp_css,$item.' .hw-item-list .hw-item');
 		
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
 