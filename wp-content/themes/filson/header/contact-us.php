<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Nav Contact Us
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_contact_us', 'hexwp_nav_contact_us');
add_filter('hexwp_header_builder_mobile_contact_us', 'hexwp_nav_contact_us');

function hexwp_nav_contact_us($opt=array()) {
	
  	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('contact_us_layout'));
	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
	$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
	
  	$classes = array(
 		'hw-nav-contact-us',
		'hw-nav-layout-'.$layout,
		$class,	
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
		
 	);
	
	if(hexwp_isset($opt,'contact_us_textarea')){ ?>
        
         <div class="<?php echo esc_attr(join( ' ', $classes ));?>"  >		
            <li class="hw-middle"><span class="hw-link"><?php
			 if($layout=='text-right' || $layout=='text-bottom'){
				echo '<span>'.wp_kses_post(hexwp_isset($opt,'contact_us_textarea') ).'</span>';
			 }
			 if($layout=='text-right-2'){
				echo '<div class="hw-twoline">';
 				echo '<span>'.wp_kses_post(hexwp_isset($opt,'contact_us_title') ).'</span>';
				echo '<span>'.wp_kses_post(hexwp_isset($opt,'contact_us_textarea') ).'</span>';
				echo '</div>';
			 }?><span></li>
			
          </div> 
  
  	 <?php 
	 }
} 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Mobbar Contact Us 
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_mobbar_contact_us($opt=array()) {
   
	if(hexwp_isset($opt,'contact_us_textarea')){ ?>
        
         <div class="hw-nav-contact-us"   >		
            <li class="hw-middle" ><span class="hw-link"><?php  echo wp_kses_post(hexwp_isset($opt,'contact_us_textarea') ); ?></span></li>
           </div> 
  
  	 <?php 
	 }
}