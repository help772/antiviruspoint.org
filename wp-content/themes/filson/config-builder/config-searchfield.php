<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Search Field Config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_search_field_config( $args,$out=false ) {
 
 	$option = $args['option'];
	$key = $args['key'];
	if(hexwp_element_show($option)=='show'){
		$output='';
		$css ='';
 		$placeholder =!empty($option['placeholder'])?$option['placeholder']:'';
		$button_type =!empty($option['search_button_type'])?$option['search_button_type']:'icon';
		$alignment_class =!empty($option['alignment'])?$option['alignment']:'center';
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';
		if(function_exists ( "is_woocommerce" )){
			 $default ='product';
		}else{
			$default ==hexwp_option('searchform_post_type');	
		}
		$searchform_post_type= !empty($option['searchform_post_type'])?$option['searchform_post_type'] :$default;
				 
		$classes = array(
			'hw-el-'.$key,
			'hw-search-'.$button_type,
			'hw-searchfield',
			'hw-aw',
			'hw-align-'.hexwp_alignment_inverse($alignment_class),
			hexwp_element_show($option,true),
			$custom_class		
		);
	 
		ob_start(); 
		
		?>
		 <aside <?php  hexwp_el_id($option);?>  class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?> >
	
			<form method="get" class="hw-search" action="<?php echo esc_url(home_url( '/') );?>">
							 
					<input type="text" name="s"  value="" placeholder="<?php echo esc_html($placeholder);?>" />
					
 						<input type="hidden" name="post_type" value="product">
 							 
					
					<?php if($button_type=='text' ){?>
						<button type="submit" name="btnSubmit"  ><?php echo hexwp_t('search');?></button>
					<?php }else{?>
						<button type="submit" name="btnSubmit" ></button>
					<?php } ?>
							
			</form>
		</aside>
		<?php
		$item = '.hw-el-'.$key.'';
		$item_css=
			hexwp_var('--hw-srh-wt',$option,'width').
			hexwp_var_unit('--hw-srh-ht',$option,'height').
			hexwp_var_2('--hw-srh-txt-bg',$option,'search_style','background').
			hexwp_var_2('--hw-srh-txt-txt',$option,'search_style','text').
			hexwp_var_2('--hw-srh-btn-bg',$option,'search_button_style','background').
			hexwp_var_2('--hw-srh-btn-txt',$option,'search_button_style','text').
			hexwp_var('--hw-srh-br-cr',$option,'search_border_color').
			hexwp_var('--hw-srh-rd',$option,'search_radius').
			hexwp_var_font_typo('--hw-srh',$option,'text_typo');   			
	
		
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
 