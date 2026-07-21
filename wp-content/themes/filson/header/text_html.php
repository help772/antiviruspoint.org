<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Text Header 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_text_html', 'hexwp_nav_text_html');
add_filter('hexwp_header_builder_mobile_text_html', 'hexwp_nav_text_html');

function hexwp_nav_text_html($opt) {
 
 	
	if(hexwp_isset($opt,'text_html_textarea')){
 		$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
 	 
		
  	$classes = array(
 		'hw-nav-text-html',
		'hw-nav-layout-text-right',
		$class ,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
		
 	);
	
		
	?>
         <div class="<?php echo esc_attr(join( ' ', $classes ));?>"  >		
    	 <li class="hw-middle">
			 <?php 
             if(hexwp_isset($opt,'text_html_link')){ 
                echo '<a  class="hw-link" href="'.hexwp_isset($opt,'text_html_link').'"><span>'.do_shortcode(wp_kses_post(hexwp_isset($opt,'text_html_textarea')) ).'</span></a>';
             }else{
                echo '<span class="hw-link"><span>'.do_shortcode(wp_kses_post(hexwp_isset($opt,'text_html_textarea')) ).'</span></span>';
             }
             ?>
         </li>
         
         
   	
    </div> 
  <?php 
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Mobbar Text Header 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_nav_mobbar_text_html($opt) {
 	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
 	if(hexwp_isset($opt,'text_html_textarea')){
		
  	$classes = array(
 		'hw-nav-text-html',
		'hw-nav-layout-text-right',
		$class ,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
		
 	);
	
 	?>
         <div class="<?php echo esc_attr(join( ' ', $classes ));?>"  >		
    	 <li class="hw-middle">
			 <?php 
             if(hexwp_isset($opt,'text_html_link')){ 
                echo '<a  class="hw-link" href="'.hexwp_isset($opt,'text_html_link').'">'.do_shortcode(wp_kses_post(hexwp_isset($opt,'text_html_textarea')) ).'</a>';
             }else{
                echo '<span class="hw-link">'.do_shortcode(wp_kses_post(hexwp_isset($opt,'text_html_textarea')) ).'</span>';
             }
             ?>
         
         </li>
  	
    </div> 
  <?php 
	}
}