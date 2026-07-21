<?php
  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Section Start
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$slug=hexwp_slug();

if($depth!==0){
 

$sub_column = ! empty( hexwp_menu_data($item,'hexwp_menu_sub_column')) ? 'hw_col_'.str_replace("-","_",hexwp_menu_data($item,'hexwp_menu_sub_column')) : '';
 
 
$padding = array();
 
 $padding['padding']['top'] = hexwp_menu_data($item,'hexwp_menu_padding_top');

 $padding['padding']['right'] = hexwp_menu_data($item,'hexwp_menu_padding_right');

 $padding['padding']['bottom'] =  hexwp_menu_data($item,'hexwp_menu_padding_bottom');

 $padding['padding']['left'] =  hexwp_menu_data($item,'hexwp_menu_padding_left');


$item_css=hexwp_var_padding('--hw-menu-sc-pd',$padding,'padding');


 
$gap['gap'] =  hexwp_menu_data($item,'hexwp_menu_gap');

 
$item_css.=hexwp_var_unit('--hw-menu-sc-gap',$gap,'gap');

$style_item_css=!empty($item_css)?'style="'.$item_css.'"':'';

$output.='<div class="hw-menu-section   '.$sub_column.' " '.$style_item_css.' >';

$output .= apply_filters( 'walker_nav_menu_start_el', '', $item, $depth, $args );
}
		