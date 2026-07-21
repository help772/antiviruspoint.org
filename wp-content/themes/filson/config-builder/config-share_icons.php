<?php
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Share Icons Config
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_share_icons_config( $args ,$out = false,$custom=false ) {
 
	$option = $args['option'];
	$key = $args['key'];
	if(hexwp_element_show($option)=='show'){
		$output='';
		$css ='';
		 
		
  		$share_url= !empty($option['share_url']) ?$option['share_url']:'';
		$alignment_class= !empty($option['alignment']) ?$option['alignment']:'center';
 		$between_class= !empty($option['between']) ?'hw-gap-'.$option['between']:'';
		$icon_size= !empty($option['icon_size']) ?$option['icon_size']:'';
		$style = !empty($option['icon_style']) ?$option['icon_style']:'style-1';
		$custom_class = !empty( $option['custom_class']) ? $option['custom_class'] : '';
	
		$classes = array(
			'hw-el-'.$key,
			'hw-el-share',
			 $between_class,
			empty($out)?'hw-aw':'',
			
			empty($out)?'hw-align-'.hexwp_alignment($alignment_class):'',
			hexwp_element_show($option,true),
			$custom_class		
		);
		
		ob_start(); 
		?>
	 
		<aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?>>
			<div class="hw-social-icon-<?php echo esc_attr($style);?>">
	 
				<?php if(!empty($option['facebook']) || (!empty($out) && empty($custom))){
					$facebook_url ="popup = window.open('http://www.facebook.com/sharer.php?u=".$share_url."&t=', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$facebook_bg =$style=='style-3'?"--hw-scl-bg:#f86811;":''; 
					?><a  onClick="<?php echo esc_html($facebook_url);?>" style="--hw-scl:'\FC01';<?php echo wp_kses_post($facebook_bg);?>"></a><?php 
				} 
				
				if(!empty($option['twitter']) || (!empty($out) && empty($custom))){
					$twitter_url ="popup = window.open('http://twitter.com/home?status= ".$share_url."', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$twitter_bg =$style=='style-3'?"--hw-scl-bg:#222222;":''; 
					
					?><a onClick="<?php echo esc_html($twitter_url);?>" style="--hw-scl:'\FC03';<?php echo wp_kses_post($twitter_bg);?>"></a><?php
					
				}
				if(!empty($option['googleplus']) || (!empty($out) && empty($custom))){
					
					$googleplus_url ="popup = window.open('https://plus.google.com/share?url=".$share_url."&title=', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$googleplus_bg =$style=='style-3'?"--hw-scl-bg:#d54c40;":'';
					 
					?><a onClick="<?php echo esc_html($googleplus_url);?>" style="--hw-scl:'\FC04';<?php echo wp_kses_post($googleplus_bg);?>"></a><?php
					
				} 
							
				if(!empty($option['tumblr'])|| (!empty($out) && empty($custom))){
					$tumblr_url = "popup = window.open('http://www.tumblr.com/share/link?url=".$share_url."&name=&description=', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$tumblr_bg =$style=='style-3'?"--hw-scl-bg:#37465d;":''; 
					
					?><a onClick="<?php echo esc_html($tumblr_url);?>" style="--hw-scl:'\FC16';<?php echo wp_kses_post($tumblr_bg);?>"></a><?php
					
				} 
				if(!empty($option['linkedin'])|| (!empty($out) && empty($custom))){
					$linkedin_url ="popup = window.open('http://linkedin.com/shareArticle?mini=true&url=".$share_url."&title=', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";	
					$linkedin_bg =$style=='style-3'?"--hw-scl-bg:#0079b6;":''; 
				
					?><a onClick="<?php echo esc_html($linkedin_url);?>" style="--hw-scl:'\FC07';<?php echo wp_kses_post($linkedin_bg);?>"></a><?php
					
				} 	
	
				if(!empty($option['reddit']) || (!empty($out) && empty($custom)) ){
					$reddit_url = "popup = window.open('http://reddit.com/submit?url=http://www.google.com&title=', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$reddit_bg =$style=='style-3'?"--hw-scl-bg:#f64302;":''; 
					
					?><a onClick="<?php echo esc_html($reddit_url);?>" style="--hw-scl:'\FC29';<?php echo wp_kses_post($reddit_bg);?>"></a><?php
					
				} 
				if(!empty($option['telegram']) || (!empty($out) && empty($custom)) ){
					$telegram_url = "popup = window.open('tg://msg?text=".$share_url."', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$telegram_bg =$style=='style-3'?"--hw-scl-bg:#29a1d5;":''; 
					
					?><a onClick="<?php echo esc_html($telegram_url);?>" style="--hw-scl:'\FC05';<?php echo wp_kses_post($telegram_bg);?>"></a><?php
				} 		
		
				if(!empty($option['mail'])|| (!empty($out) && empty($custom)) ){
					$mail_url = "popup = window.open('mailto:?subject=&body=".$share_url."', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false";
					$mail_bg =$style=='style-3'?"--hw-scl-bg:#2197a4;":''; 
				
					?><a onClick="<?php echo esc_html($mail_url);?>" style="--hw-scl:'\F008';<?php echo wp_kses_post($mail_bg);?>"></a><?php
				} 
				if(!empty($option['whatsapp'])|| (!empty($out) && empty($custom)) ){
					$whatsapp_url = "whatsapp://send?text=".$share_url;
					$whatsapp_url_bg =$style=='style-3'?"--hw-scl-bg:#02d300;":''; 
				
					?><a href="<?php echo esc_html($whatsapp_url);?>" data-action="share/whatsapp/share" style="--hw-scl:'\FC21';<?php echo wp_kses_post($whatsapp_url_bg);?>"></a><?php
				} 		
				?>						
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
 