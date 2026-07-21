<?php
  global $pagenow;
 
function hexwp_icon_element() { 
 
	global $hexwp_icon_element;
	
	$element=array();
 
		if(has_filter('hexwp_icon_element')) {
			$hexwp_icon_element = apply_filters('hexwp_icon_element', $element);
		}
  				 
 
}
add_action('init','hexwp_icon_element');

 
 
 add_action('wp_ajax_nopriv_hexwp_icon_picker', 'hexwp_icon_picker');
add_action('wp_ajax_hexwp_icon_picker', 'hexwp_icon_picker');


function hexwp_icon_picker() {
		global $hexwp_icon_element;

		$hexwp_icons = array();
		$hexwp_icons =  $hexwp_icon_element; 
		echo '<div class="hexwp_icon" data-id="'.esc_attr(sanitize_title($_REQUEST['id'])).'">';
						echo '<div class="hexwp_icon_middle">';
							echo '<div class="hexwp_icon_title">'.esc_html__('Select Icon','hexwp').'<i class="hexwp_icon_close"></i><div class="hexwp_icon_search"><input id="search-input" class="search-icon-control" placeholder="'.esc_html__('Search Icon','hexwp').'" autocomplete="off" spellcheck="false" autocorrect="off" tabindex="1"></div>';
							
  								$count_tab = 0;
								echo'<div class="hexwp_title_tabs">';

								foreach ($hexwp_icons as  $icon_key => $icon_value):
									$count_tab++;
  												if($count_tab==1){
													$tab_active = 'hexwp_layout_active';
												}else{
													$tab_active = '';
												}
												
											echo'<a class="hexwp_tab_'.esc_attr($icon_key).' '.esc_attr($tab_active).'" data-id="'.esc_attr($icon_key).'">'.esc_html($icon_key).'</a>';
								endforeach;  
								echo'</div>';


 							
							echo '</div>';
  							echo '<ul class="hexwp_icon_content">';
							  $count_active=0;
							 	if(!empty($hexwp_icons)){
								foreach ($hexwp_icons as $font => $font_title) {
									$count_active++;
  												if($count_active==1){
													$warp_active = 'hexwp_layout_group_active';
												}else{
													$warp_active = '';
												}
									
									echo '<section class="hexwp_icon_warp '.$warp_active.'" data-tab="'.esc_attr($font).'">';
									
 									
										foreach ($font_title as $value_head => $font_child) {
 											echo  '<h2 class="hexwp_icon_head" >'.esc_attr($value_head).'</h2>';
										
 											foreach ($font_child as $key => $value ) {
												echo  '<li class="hexwp_icon_item" data-icon="'.esc_attr($key).'">';
													echo'<i class="'.esc_attr($key).'"></i>';
													echo'<span>'.esc_attr($value).'</span>';
												echo   '</li>';	
											}
										  
										}
									echo '</section>';							

								}
								} 
						 					
							echo'</ul>';
							echo '<div class="hexwp_icon_bottom"><a class="hexwp_set_icon button-primary">'.esc_html__('Set Icon','hexwp').'</a></div>';	 
						echo'</div>';
 			echo '</div>';
			  	die();
}

 


function hexwp_icon_fonts() {
echo'<div class="hexwp-icon-fonts">';
	$var='';
 	$array =array(
	'FontAwesome',
	'flaticonarrow',
	'flaticonmultimedia',
	'flaticonbusiness',
	'flaticonoffice',
	'flaticoninterface',
	'flaticonessentialset',
	'flaticontechsupport',
	'flaticontech',
	'flaticonstrategy',
	'flaticonhipster',
	'flaticonfashion',
	'flaticonwebdesign',
	'flaticontravel',
	'flaticonnetwork',
	'metrizeicon',
	'typcn'
	);
 		global $hexwp_fonticon_style;
   	foreach($array as $font){
		if(!empty($hexwp_fonticon_style[$font]) ||!empty($_REQUEST['ajax']) ){
		echo "<link rel='stylesheet' id='hexwp_".$font."-css'  href='".hexwp_DIR."/css/fonts/".$font.".css' media='all' />";
		}
	}
 	
 echo'</div>';
 if(!empty($_REQUEST['ajax'])){
	die();			  	
 }

}
 add_action('admin_footer', 'hexwp_icon_fonts');
 
 
 add_action('wp_ajax_nopriv_hexwp_icon_fonts', 'hexwp_icon_fonts');
add_action('wp_ajax_hexwp_icon_fonts', 'hexwp_icon_fonts');
  