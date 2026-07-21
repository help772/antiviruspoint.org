<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Nav Nav Social
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_social', 'hexwp_nav_social');
 function hexwp_nav_social($opt) {
	global $smof_data;
	
 	$social_position=hexwp_isset($opt,'social_position',hexwp_nav_default('social_position'));
 	$layout=hexwp_isset($opt,'layout',hexwp_nav_default('social_layout'));
	$class='';
	if($social_position=='dropdown'){
		$class= 'hw-nav-layout-'.$layout.' ';
		$class.= hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
 		$class.=hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';

	}  
   	$classes = array(
 		'hw-nav-social',
		'hw-social-'.$social_position,
		$class,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
	);
	?>
     
  	 <ul class="<?php echo esc_attr(join( ' ', $classes ));?>"  >
 	
		 <li class="hw-middle">
			<?php if( $social_position=='dropdown'  ){?>
				 <a class="hw-link"><?php
 					if($layout =='text-right' || $layout =='text-bottom'){
						echo '<span>'.esc_html(hexwp_t('social')).'</span>';
  					}
				 
				?></a>
				<ul class="hw-drop">
			<?php }?>
            
                    <div class="hw-social-icon-<?php echo hexwp_isset($opt,'social_style',hexwp_option('social_style'));?>">
                    <?php hexwp_social_content(hexwp_isset($opt,'social_style','style-3'),$smof_data,'social_');?>
                    </div>
		
 			<?php if( $social_position=='dropdown'  ){?>
				</ul>
			<?php } ?>
		</li>
  	</ul> 
 <?php
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Mobile Social 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_mobile_social', 'hexwp_nav_mobile_social');
 function hexwp_nav_mobile_social($opt) {
	global $smof_data;
	
 	$icon_boxed='';
    $classes = array(
 		'hw-nav-social',
		'hw-social-fixed',
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
		
		
	);
	?>
     
  	 <ul class="<?php echo esc_attr(join( ' ', $classes ));?>"  >
 	
		 <li  class="hw-middle">
    	     <div class="hw-social-icon-<?php echo hexwp_isset($opt,'social_style',hexwp_option('social_style'));?>">
			<?php hexwp_social_content(hexwp_isset($opt,'social_style','style-3'),$smof_data,'social_');?>
			</div>
		
 			 
		</li>
  	</ul> 
 <?php
} 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Mobbar Social
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_nav_mobbar_social($opt) {
	global $smof_data;
	
 	$icon_boxed='';
  
    $classes = array(
 		'hw-nav-social',
		'hw-nav-social-fixed',
   		'hw-social-icon-'.hexwp_isset($opt,'social_style',hexwp_option('social_style')),
		
	);	
	?> 
 	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">
		<?php hexwp_social_content(hexwp_isset($opt,'social_style',hexwp_option('social_style')),$smof_data,'social_');?>
	</div>
  <?php
} 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Social Content 
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_social_content($style='style-1' ,$option = false,$slug=''){
 		 
 		 if(!empty($option[$slug.'rss'])){
			 $social[]=array('content'	=>'\FC01','bg'=>'#f86811','url'=>	$option[$slug.'rss']);
		 }
		 if(!empty($option[$slug.'facebook'])){
			 $social[]=array('content'	=>'\FC02','bg'=>'#395498','url'=>	$option[$slug.'facebook']);
		 }
		 if(!empty($option[$slug.'twitter'])){
			 $social[]=array('content'	=>'\FC03','bg'=>'#222222','url'=>	$option[$slug.'twitter']);
		 }
		if(!empty($option[$slug.'googleplus'])){
			 $social[]=array('content'	=>'\FC04','bg'=>'#d54c40','url'=>	$option[$slug.'googleplus']);
		 }
		 if(!empty($option[$slug.'telegram'])){
			 $social[]=array('content'	=>'\FC05','bg'=>'#29a1d5','url'=>	$option[$slug.'telegram']);
		 }
		 if(!empty($option[$slug.'dribbble'])){
			 $social[]=array('content'	=>'\FC06','bg'=>'#ed4a89','url'=>	$option[$slug.'dribbble']);
		 }
		 if(!empty($option[$slug.'linkedin'])){
			 $social[]=array('content'	=>'\FC07','bg'=>'#0079b6','url'=>	$option[$slug.'linkedin']);
		 } 
 		 if(!empty($option[$slug.'dropbox'])){
			 $social[]=array('content'	=>'\FC08','bg'=>'#2c8dda','url'=>	$option[$slug.'dropbox']);
		 }
		 if(!empty($option[$slug.'flickr'])){
			 $social[]=array('content'	=>'\FC09','bg'=>'#c01262','url'=>	$option[$slug.'flickr']);
		 }
		 if(!empty($option[$slug.'deviantart'])){
			 $social[]=array('content'	=>'\FC10','bg'=>'#506e48','url'=>	$option[$slug.'deviantart']);
		 }
		 if(!empty($option[$slug.'youTube'])){
			 $social[]=array('content'	=>'\FC11','bg'=>'#c22320','url'=>	$option[$slug.'youTube']);
		 }
		 
		 if(!empty($option[$slug.'vimeo'])){
			 $social[]=array('content'	=>'\FC12','bg'=>'#5ca9d5','url'=>	$option[$slug.'vimeo']);
		 }
		 if(!empty($option[$slug.'yahoo'])){
			 $social[]=array('content'	=>'\FC13','bg'=>'#845aa4','url'=>	$option[$slug.'yahoo']);
		 }
		 
		 
		 if(!empty($option[$slug.'skype'])){
			 $social[]=array('content'	=>'\FC30','bg'=>'#29a4e8','url'=>	$option[$slug.'skype']);
		 }
		 if(!empty($option[$slug.'digg'])){
			 $social[]=array('content'	=>'\FC14','bg'=>'#4c4c4c','url'=>	$option[$slug.'digg']);
		 }
		 if(!empty($option[$slug.'stumbleupon'])){
			 $social[]=array('content'	=>'\FC15','bg'=>'#c24235','url'=>	$option[$slug.'stumbleupon']);
		 }
		 if(!empty($option[$slug.'tumblr'])){
			 $social[]=array('content'	=>'\FC16','bg'=>'#37465d','url'=>	$option[$slug.'tumblr']);
		 }
		 if(!empty($option[$slug.'pinterest'])){
			 $social[]=array('content'	=>'\FC17','bg'=>'#ca211c','url'=>	$option[$slug.'pinterest']);
		 }
		 if(!empty($option[$slug.'instagram'])){
			 $social[]=array('content'	=>'\FC18','bg'=>'#00afc0','url'=>	$option[$slug.'instagram']);
		 }
		 
		 if(!empty($option[$slug.'paypal'])){
			 $social[]=array('content'	=>'\FC19','bg'=>'#1a96de','url'=>	$option[$slug.'paypal']);
		 }
		 
		 
		 if(!empty($option[$slug.'behance'])){
			 $social[]=array('content'	=>'\FC20','bg'=>'#2897cf','url'=>	$option[$slug.'behance']);
		 }
		 
		 if(!empty($option[$slug.'whatsapp'])){
			 $social[]=array('content'	=>'\FC21','bg'=>'#02d300','url'=>	$option[$slug.'whatsapp']);
		 } 	
		 
	 
		 if(!empty($option[$slug.'reddit'])){
			 $social[]=array('content'	=>'\FC29','bg'=>'#f64302','url'=>	$option[$slug.'reddit']);
		 } 	
	 
		 if(!empty($option[$slug.'discord'])){
			 $social[]=array('content'	=>'\FC32','bg'=>'#5562ea','url'=>	$option[$slug.'discord']);
		 } 	
		  	 if(!empty($option[$slug.'tiktok'])){
			 $social[]=array('content'	=>'\FC36','bg'=>'#222222','url'=>	$option[$slug.'tiktok']);
		 }
		 if(is_rtl()){
			 if(!empty($option[$slug.'aparat'])){
				$social[]=array('content'	=>'\FC22','bg'=>'#ed145b','url'=>	$option[$slug.'aparat']);
			 } 	  
			 
			 if(!empty($option[$slug.'bisphone'])){
				$social[]=array('content'	=>'\FC23','bg'=>'#d11023','url'=>	$option[$slug.'bisphone']);
			 } 	
			 
			 if(!empty($option[$slug.'bale'])){
				$social[]=array('content'	=>'\FC24','bg'=>'#42ac9e','url'=>	$option[$slug.'bale']);
			 } 	
			 
			 if(!empty($option[$slug.'wispi'])){
				$social[]=array('content'	=>'\FC25','bg'=>'#1093ed','url'=>	$option[$slug.'wispi']);
			 } 	
			 
			 if(!empty($option[$slug.'igap'])){
				$social[]=array('content'	=>'\FC26','bg'=>'#00afc0','url'=>	$option[$slug.'igap']);
			 } 	
			 
			 if(!empty($option[$slug.'soroush'])){
				$social[]=array('content'	=>'\FC27','bg'=>'#2d83a4','url'=>	$option[$slug.'soroush']);
			 } 	
			 if(!empty($option[$slug.'eitaa'])){
				$social[]=array('content'	=>'\FC28','bg'=>'#ee7c00','url'=>	$option[$slug.'eitaa']);
			 } 	
			 if(!empty($option[$slug.'rubika'])){
				$social[]=array('content'	=>'\FC35','bg'=>'#246889','url'=>	$option[$slug.'rubika']);
			 } 		 
		 }
			 
		 if(!empty($social)){
		 foreach($social as $value){
			$inline="--hw-scl:'".$value['content']."';";
			$inline.=$style=='style-3'?"--hw-scl-bg:".$value['bg'].";":''; 
            
 			 ?><a  style=" <?php echo wp_kses_post($inline);?>"   href="<?php echo esc_url($value['url']);?>"></a><?php
       
          }  
 
	}
}
 


