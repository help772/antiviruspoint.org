<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

															Contact Form 7 Config
																		
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_contactform_config( $args , $out = false ) {
	wp_reset_query();
	wp_reset_postdata();
	
 	$option = $args['option'];
	$key = $args['key'];
	if(hexwp_element_show($option)=='show'){
		$output='';
		$css ='';
		$between_class= !empty($option['between']) ?$option['between']:'20px';
		if(!empty($option['contactform_id'])){
		$contactform_id = get_page_by_path($option['contactform_id'],OBJECT, 'wpcf7_contact_form');
		}
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';
	
		$classes = array(
			'hw-el-'.$key,
			'hw-contactform',
			'hw-gap-'.$between_class.'',
			'hw-aw',
			hexwp_element_show($option,true),
			$custom_class		
		);
		ob_start(); 
		?>
	 
		<aside <?php  hexwp_el_id($option);?>  class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?>>
						<div class="hw-gap-content">

            <div class="hw-gap-warp">
                <div class="hw-item-list">
                
                    <?php 
                     if( !empty($contactform_id) && !empty($contactform_id->ID)){
                    echo do_shortcode('[contact-form-7 id="'.esc_attr($contactform_id->ID).'" title=""]');
                    }
                     ?>
                </div>
                </div>
			</div>
		</aside>
        
		<?php
 		$item = '.hw-el-'.$key.'';
 		$item_css=
 			hexwp_var_unit('--hw-input-ht',$option,'height').
			hexwp_var_unit('--hw-textarea-ht',$option,'textarea_height').
			hexwp_var('--hw-main-lk',$option,'text_color').
			hexwp_var('--hw-form-bg',$option,'field_background_color').
			hexwp_var('--hw-form-txt',$option,'field_text_color').
			hexwp_var('--hw-main-br-cr',$option,'field_border_color').
			hexwp_var_2('--hw-primary-bg',$option,'button_color','background').
			hexwp_var_2('--hw-primary-txt',$option,'button_color','text').
			hexwp_var('--hw-btn-rd',$option,'border_radius').
			hexwp_var('--hw-main-rd',$option,'textarea_radius').
			hexwp_var_font_typo('--hw-form',$option,'text_typo');
			
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
 