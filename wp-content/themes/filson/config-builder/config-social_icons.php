<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Socail Icons Config
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_social_icons_config( $args , $out = false) {
  	$option = $args['option'];
	$key = $args['key'];
	if(hexwp_element_show($option)=='show'){
		$output='';
		$css ='';
		 
		 
		
		$alignment_class= !empty($option['alignment']) ?$option['alignment']:'center';
		$between_class= !empty($option['between']) ?'hw-gap-'.$option['between']:'';
		$icon_size= !empty($option['icon_size']) ?$option['icon_size']:'';
		$style = !empty($option['icon_style']) ?$option['icon_style']:'style-1';
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';
	
		$classes = array(
			'hw-el-'.$key,
			'hw-el-social',
			 $between_class,
			'hw-aw',
			'hw-align-'.hexwp_alignment($alignment_class),
			hexwp_element_show($option,true),
			$custom_class		
		);
		ob_start(); 
 		?>
	 
		<aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?>>
			<div class="hw-social-icon-<?php echo esc_attr($style);?>">
				<?php hexwp_social_content($style,$option,'social_');?>
			</div>
		</aside>
        
		 <?php
		$item = '.hw-el-'.$key.'';
		$item_css=
 			hexwp_var_unit('--hw-scl-sz',$option,'icon_size').
			hexwp_var('--hw-scl-txt',$option,'icon_color').
			hexwp_var('--hw-scl-bg',$option,'icon_background').
			hexwp_var('--hw-scl-br-cr',$option,'icon_border_color').
			hexwp_var('--hw-scl-rd',$option,'icon_radius');
	
			
			
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
 