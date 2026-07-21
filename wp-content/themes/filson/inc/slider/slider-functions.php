<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Portfolio Module 3
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_slider_module_3($option,$class=''){
	global $post;
   	$ratio_class='';
 		 if(!empty($option['sliders'])){
	?>
   	<div class="hw-item hw-glider <?php  echo esc_attr($class)?>">
	<div class="hw-post-slider vao-post-<?php echo esc_attr($post->ID);?>" >
 	
    	<?php
		
 		 ?>
		<div  class="vs-elementor-<?php echo !empty($option['key'])?$option['key']:'';?> vs-elementor-item">
        <?php echo vs_slider_config($option['sliders']);?>
 	<?php
	   	 
	 if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
		 if(!empty($option['sliders'])){
		 	$setting_json = get_post_meta($option['sliders'], 'sor_setting_json', true);
 		 	$setting= vs_options_array_row($setting_json);
		if(empty($setting['disable_typography'])){
	 echo '<link rel="stylesheet" id="vs_fontfamily-css"  href="'.VISUALSLIDER_DIR.'assets/css/fontfamily.css" media="all" />';

 	}}
		vs_icon_fonts(); 
		 ?>
			 <div class="vao-elementor-script">     
					<script type="text/javascript">
					  (function($) {
						'use strict';
							jQuery(document).ready(function() {
 	 	 					setTimeout(function(){  
      								$('.vs-elementor-<?php echo $option['key'];?>').vs_custom_slider();
 
 								 
							}, 1000);
									 
   							}); 
							 
 						})(jQuery);
 	               </script>
		
			</div>
		<?php		
	   }	
	   ?> 
         </div> 
 

  		  
 	</div>
 	</div> 
     
	<?php
		 }
 }
  function hexwp_slider_thumb() { 
 	
 		
 		global $post;
		
 		
		
		$style='';
		$background_image = hexwp_meta('sao_background_image');
		$thumbnail = !empty($background_image)? wp_get_attachment_image_src($background_image, 'full'):'';
   		$background_image_src = !empty($background_image)? $thumbnail[0]:'';
		$background_image_width = !empty($background_image)? $thumbnail[1]:'';
		$background_image_height = !empty($background_image)?	$thumbnail[2]:''; 
		$the_permalink =  hexwp_meta('sao_slide_link');
 		
		
 		?>
		<div class="hw-thumb"> 
			<a <?php if(!empty($the_permalink)){?>href="<?php echo esc_url($the_permalink) ?>"  <?php } ?>  ></a>
 		</div>
	<?php
	 
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Post Module 3
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_image_featured ($option){ 

	$css='';
	$image_featured_query = hexwp_data($option,'image_featured_query');
 
 	if($image_featured_query =='slider' ){

		  if ( function_exists ( "visualslider_constructor" )){
 				hexwp_slider_module_3($option);
		  }
 
	}elseif($image_featured_query =='upload' || $image_featured_query =='link' ){ 
 		$image_featured_id = hexwp_data($option,'image_featured_id');
 		$image_featured_url = hexwp_data($option,'image_featured_url');
		$thumbnail = !empty($image_featured_id)? wp_get_attachment_image_src($image_featured_id, 'full'):'';
 		//$image_featured_id = hexwp_data($option,'image_featured_url');

   
 		?>
		
        <div class="hw-item hw-glider ">
			<div class="hw-post-slider" >
				<?php if(!empty($thumbnail[0])){ ?>
					<div class="hw-thumb"> 
					<a href="<?php echo esc_url($image_featured_url) ?>"  ><figure style="background-image:url('<?php echo $thumbnail[0];?>');"></figure></a>
					</div>
				<?php }?>   
 
     	   </div>
        </div>
 		
		<?php
     
        
 	}
	 
  	 
 
}
?>