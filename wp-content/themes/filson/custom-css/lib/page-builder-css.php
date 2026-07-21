<?php
  
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Pagebuider Style Color
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$css.='
body{
	 --vao-sc-wt:'.hexwp_option('body_width' ).';
 	 --vao-prm-bg:'.hexwp_option_2('primary_color','background').';
	 --vao-prm-cr:'.hexwp_option_2('primary_color','text').';
	 --vao-lk-cr:'.hexwp_option_2('main_link_color','link').';
	 --vao-hv-lk-cr:'.hexwp_option_2('main_link_color','hover').';
	 --vao-txt-cr:'.hexwp_option('main_text_color').';
	 --vao-hl:'.hexwp_option('main_highlight_color').';
	--vao-br-cr:'.hexwp_option('main_border_color').';
	--vao-aw-bg:var(--hw-aw-bg,rgba(0,0,0,0.75));
	--vao-aw-cr:var(--hw-aw-txt,#ffffff);
}
';