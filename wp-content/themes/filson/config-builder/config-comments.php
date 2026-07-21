<?php

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Comments Config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_comments_config( $args , $out = false ) {
	wp_reset_query();
	wp_reset_postdata();
 
 	$option = $args['option'];
	$key = $args['key'];
	if(hexwp_element_show($option)=='show'){
		$output='';
		$css ='';
		global $smof_data;
		$comments_layout_type = !empty( $option['comments_layout_type']) ? $option['comments_layout_type'] : hexwp_option('comments_layout_type');
		$box_layout =!empty($option['box_layout'])? $option['box_layout']:hexwp_option('blog_box_layout');
		$box_layout_class = $box_layout!=='none'?'hw-single-boxed':'hw-none';
 		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';
	
		$classes = array(
			'hw-el-'.$key,
			'hw-comments',
			'hw-have-comments',
			$comments_layout_type,
			'hw-gap-20',
			'hw-aw',
				$box_layout_class,
			hexwp_element_show($option,true),
			$custom_class		
		);
	
		ob_start(); 
		?>
	 
		<aside <?php  hexwp_el_id($option);?>  class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?>>
 			<?php comments_template(  '/inc/el-comments.php', true ); ?>
		</aside>
	 
		<?php
 		$item = '.hw-el-'.$key.'';
	 
		$item_css=
			hexwp_var_unit('--hw-input-ht',$option,'height').
			hexwp_var_unit('--hw-textarea-ht',$option,'textarea_height').
			hexwp_var('--hw-post-bg',$option,'background_color').
			hexwp_var('--hw-main-hl',$option,'author_color').
			hexwp_var('--hw-main-lk',$option,'label_color').
			hexwp_var('--hw-main-txt',$option,'text_color').
			hexwp_var('--hw-form-bg',$option,'field_background_color').
			hexwp_var('--hw-form-txt',$option,'field_text_color').
			hexwp_var('--hw-main-br-cr',$option,'border_color').
			hexwp_var_2('--hw-primary-bg',$option,'button_color','background').
			hexwp_var_2('--hw-primary-txt',$option,'button_color','text').
			hexwp_var('--hw-mian-btn-rd',$option,'border_radius').
			hexwp_var('--hw-post-sd',$option,'box_border_color').
			hexwp_var('--hw-main-rd',$option,'textarea_radius').
			hexwp_var_font_typo('--hw-form',$option,'text_typo').
			hexwp_var_font_typo('--hw-at',$option,'author_typo').
			hexwp_var_font_typo('--hw-cm',$option,'comments_typo');
			
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
 