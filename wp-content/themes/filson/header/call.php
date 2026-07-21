<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Call t Us
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_call', 'hexwp_nav_call');
add_filter('hexwp_header_builder_mobile_call', 'hexwp_nav_call');
function hexwp_nav_call($opt=array()) {
	
  	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('call_layout'));
	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
	$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
	
	
  	$classes = array(
 		'hw-nav-call',
		'hw-nav-layout-'.$layout,
		$class,	
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
 	);
	
	if(hexwp_isset($opt,'call_textarea')){ ?>
        
         <div class="<?php echo esc_attr(join( ' ', $classes ));?>"  >		
            <li class="hw-middle"><span  class="hw-link"><?php
			 if($layout=='text-right' || $layout=='text-bottom'){
				echo '<span>'.wp_kses_post(hexwp_isset($opt,'call_textarea') ).'</span>';
			 }
			 if($layout=='text-right-2'){
				echo '<div class="hw-twoline">';
 				echo '<span>'.wp_kses_post(hexwp_isset($opt,'call_title') ).'</span>';
				echo '<span>'.wp_kses_post(hexwp_isset($opt,'call_textarea') ).'</span>';
				echo '</div>';
			 }?><span></li>
			
          </div> 
  
  	 <?php 
	 }
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	 Mobbar Call
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_mobbar_call($opt=array()) {
	
 	if(hexwp_isset($opt,'call_textarea')){ ?>
        
         <div class="hw-nav-call"  >		
            <li class="hw-middle"><span class="hw-link"><?php echo wp_kses_post(hexwp_isset($opt,'call_textarea') );?></span></li>
           </div> 
  
  	 <?php 
	 }
}