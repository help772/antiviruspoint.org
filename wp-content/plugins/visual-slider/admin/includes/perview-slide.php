<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
 if ( !function_exists ( "vs_perview_slide_warp" )){
function vs_perview_slide_warp(){
	
	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
  	 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
 		 return;
	}
	
 	$setting_json = !empty($_POST['vs_setting_json'])? wp_kses(wp_unslash($_POST['vs_setting_json']),vs_kses()):'';
 
	$css='';
 	$setting_value= vs_options_array_row($setting_json);
	
	$setting_type= !empty($setting_value['type'])?$setting_value['type']:'slider';
 	$class=' vs-has-desktop vs-perview-type-'.$setting_type;
 	
	if($setting_type=='glider'){
 		$css.=vs_perview_glider_css($setting_value);
	}else{
		
		$css = vs_slider_var_2('wt',$setting_value,'size','width');
		$css.= vs_slider_var_2('ht',$setting_value,'size','height');
 		if(!empty($setting_value['responsive_tablet'])){
  			$css.= vs_slider_var_2('tab-wt',$setting_value,'tablet_size','width');
			$css.= vs_slider_var_2('tab-ht',$setting_value,'tablet_size','height');
		}
			
		if(!empty($setting_value['responsive_mobile'])){
 			$css.= vs_slider_var_2('mob-wt',$setting_value,'mobile_size','width');
			$css.= vs_slider_var_2('mob-ht',$setting_value,'mobile_size','height');
		}
	}
	
	if(!empty($setting_value['responsive_tablet'])){
		$class.=' vs-has-tablet ';
	}
	if(!empty($setting_value['responsive_mobile'])){
		$class.=' vs-has-mobile ';
	}
	
 	$class.= !empty($setting_value['full_width']) && $setting_type!=='glider' ? ' vs-full-width ' :' vs-not-width ';
 	$perview_width = !empty($setting_value['full_width']) && $setting_type!=='glider' ? ' vs-perview-full-width ' :' vs-perview-not-width ';
		 ?>
		
 	 <div class="vs_perview_slide vs_perview <?php echo esc_attr($perview_width);?> " style=" <?php echo esc_attr($css);?>">

		<div class="vs_perview_heading" >
            <div class="vs_perview_heading_animte vs_perview_heading_item" >
                <span class="vs_animte_play"><?php echo esc_html__('Start Animation','visual-slider');?></span>
                <span class="vs_animte_stop"><?php echo esc_html__('Stop Animation','visual-slider');?></span>
            </div> 
            <div class="vs_perview_heading_desktop vs_perview_heading_item vs_perview_heading_full_screen_hide">
                 <label for="#vs_perview_desktop_width"><?php echo esc_html__('Edit on Desktop ','visual-slider');?></label>
                  
             </div>
    
    		<?php 	if(!empty($setting_value['responsive_tablet'])){?>
             <div class="vs_perview_heading_tablet vs_perview_heading_item vs_perview_heading_full_screen_hide"> 
                 <label for="#vs_perview_tablet_width"><?php echo esc_html__('Edit on Tablet','visual-slider');?></label>
                 
             </div>
    		<?php } ?>
            
    		<?php 	if(!empty($setting_value['responsive_mobile'])){?>
            <div class="vs_perview_heading_mobile vs_perview_heading_item vs_perview_heading_full_screen_hide"> 
                 <label for="#vs_perview_mobile_width"><?php echo esc_html__('Edit on Mobile','visual-slider');?></label>
                 
             </div>   
             
             <?php }?> 
             
             
              <div class="vs_perview_heading_desktop vs_perview_heading_full_screen_show vs_perview_heading_item">
			 <label for="#vs_perview_desktop_width"><?php echo esc_html__('View on Desktop','visual-slider');?></label>
             
			 <select id="vs_perview_desktop_width" name="vs_perview_desktop_width">
				 <option value="1920px"><?php echo esc_html('1920px');?></option>
				 <option value="1680px"><?php echo esc_html('1680px');?></option>
				 <option value="1440px"><?php echo esc_html('1440px');?></option>
				 <option value="1366px"><?php echo esc_html('1366px');?></option>
				 <option value="1280px"><?php echo esc_html('1280px');?></option>
				 <option value="1040px"><?php echo esc_html('1040px');?></option>
   			 </select>
 		 </div>


 		 <div class="vs_perview_heading_tablet vs_perview_heading_full_screen_show vs_perview_heading_item"> 
			 <label for="#vs_perview_tablet_width"><?php echo esc_html__('View on Tablet','visual-slider');?></label>
			 <select id="vs_perview_tablet_width" name="vs_perview_tablet_width">
				 <option value="1024px"><?php echo esc_html('1024px');?></option>
				 <option value="991px"><?php echo esc_html('991px');?></option>
				 <option value="800px"><?php echo esc_html('800px');?></option>
				 <option value="768px"><?php echo esc_html('768px');?></option>
   			 </select>
 		 </div>
		
        
        
 	 	<div class="vs_perview_heading_mobile vs_perview_heading_full_screen_show vs_perview_heading_item"> 
			 <label for="#vs_perview_mobile_width"><?php echo esc_html__('View on Mobile','visual-slider');?></label>
			 <select id="vs_perview_mobile_width" name="vs_perview_mobile_width" value="480px">
				 <option value="767px"><?php echo esc_html('767px');?></option>
				 <option value="680px"><?php echo esc_html('680px');?></option>
				 <option value="640px"><?php echo esc_html('640px');?></option>
				 <option value="520px"><?php echo esc_html('520px');?></option>
				 <option value="480px"><?php echo esc_html('480px');?></option>
				 <option value="320px"><?php echo esc_html('320px');?></option>
  			 </select>
 		 </div>       
        
             
             
             
             
             
             <div class="vs_perview_heading_full_screen vs_perview_heading_item"> 
			<div class="vs_perview_full_screen_mode"><?php echo esc_html__('View on Full Screen','visual-slider');?></div>
			<div class="vs_perview_full_screen_close"><?php echo esc_html__('Close Full Screen','visual-slider');?></div>

 	 	</div>    
 		 </div>       





 
        <div class="vs_perview_slide_scroll " >
            <div class="vs_perview_slide_content " >
				<div class="vs-slide-warp vs-visualslider vs-not-scale <?php echo esc_attr($class);?>" >
					<div class="vs-slide-list-warp" >
            			<div class="vs-mode-fade vs-slide-list " >
						<?php vs_perview_slide(); ?>
          				</div>
					</div>
				</div>
            </div>
        </div>
	</div>
	<?php
 //   die();

}
 }
 



 if ( !function_exists ( "vs_perview_slide" )){
function vs_perview_slide(){


	if ( ! isset( $_POST['_wpnonce'] ) ||
	! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
	return;
	$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
  	 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
 		 return;
	}


 	$slide_value = !empty($_POST['option'])? vs_options_decode(urldecode(wp_kses(wp_unslash($_POST['option']),vs_kses()))):'';
	$layer =!empty($_POST['layer'])  ? vs_options_decode(urldecode(wp_kses(wp_unslash($_POST['layer']),vs_kses()))):'';
	
  	$css =vs_slider_var_gradient_background_color('sl',$slide_value,'background_color');
 	$css.=vs_slider_background_image('sl',$slide_value,'background_image');	
	$css.=vs_slider_var_background_image_position('sl',$slide_value,'background_image_position');
	$css.=vs_slider_var_background_image_position('sl-tab',$slide_value,'tablet_background_image_position');
	$css.=vs_slider_var_background_image_position('sl-mob',$slide_value,'mobile_background_image_position');


	echo '<div class="vs-slide vs-perview-slide" style="'.esc_attr($css).'">';
	echo '<aside class="vs-slide-cover"></aside>';
	
 		vs_perview_layer(true,$layer);
		
	echo '</div>';	
   
 	
		 
}
 }
 if ( !function_exists ( "vs_perview_layer" )){
add_action('wp_ajax_vs_perview_layer', 'vs_perview_layer');
function vs_perview_layer($none_ajax='',$layer=[]){
	
	if(empty($none_ajax)){
		if ( ! isset( $_POST['_wpnonce'] ) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash( $_POST['_wpnonce'])), 'vs_slider_nonce' ) )
		return;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash($_POST['post_id'])): absint(get_the_ID());
		 if (!current_user_can('edit_post', $post_id) && !current_user_can('manage_options')){
			 return;
		}
	}
	
	if(!empty($_POST['layer_json']) && empty($none_ajax)){
		$layer =vs_options_decode(urldecode(wp_kses(wp_unslash($_POST['layer_json']),vs_kses())));
	} 
	 
 	echo '<div class="vs-layer-warp">';
	echo '<div class="vs-layer-list">';
   
		if (!empty($layer)) :
		foreach($layer as $layer_key => $layer_value):
			$layer_value['key']=$layer_key;
			$layer_id= $layer_value['id'];



			if(has_filter('vs_layer_perview_'.$layer_id)) {
				apply_filters('vs_layer_perview_'.$layer_id, $layer_value) ;	
  			}
 			   
 			   
		endforeach;
		endif; 
	
	echo '</div>';	
  	echo '</div>';	
	if(empty($none_ajax)){
 	die('');
	}
	
}
 }
 