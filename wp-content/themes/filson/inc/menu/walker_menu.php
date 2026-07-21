<?php
/**
 * Nav Menu API: Walker_Nav_Menu class
 *
 * @package WohwPress
 * @subpackage Nav_Menus
 * @since 4.6.0
 */
/**
 * Core class used to implement an HTML list of nav menu items.
 *
 * @since 3.0.0
 *
 * @see Walker
 */
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Walker Nav Menu
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class hexwp_Walker_Nav_Menu extends Walker {
 
        public $tree_type = array( 'post_type', 'taxonomy', 'custom' );
		public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
        
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
				
                 $output .= '<ul class="hw-drop">';
				  
	    } 
        public function end_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul>\n";
        } 
		
        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
                $atts = array();
                 $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
                $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
                $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
                $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
				
 
	$slug=hexwp_slug();
				$fallback= !empty($args->fallback_cb)?$args->fallback_cb:'';
				$menu_location = !empty($fallback['menu_location'])?$fallback['menu_location']:'main';
				if($item->type == 'blog_grid'){
					include  hexwp_PATH . '/inc/menu/config/blog_grid-start.php';   
				}elseif($item->type == 'widget'){
					include  hexwp_PATH . '/inc/menu/config/widget-start.php';   
				}elseif($item->type == 'image_text'){
					include  hexwp_PATH .'/inc/menu/config/image_text-start.php'; 	
						  
 				}elseif($item->type == 'image'){
					include  hexwp_PATH . '/inc/menu/config/image-start.php';   
 				}elseif($item->type == 'page_builder'){
					include  hexwp_PATH . '/inc/menu/config/page_builder-start.php';   
 				}elseif($item->type == 'section'){
					include  hexwp_PATH . '/inc/menu/config/section-start.php';   
 				}else{ 
					if($menu_location=='main'){
 					include  hexwp_PATH . '/inc/menu/config/main_menu_item-start.php';   
					}else{
 						include  hexwp_PATH . '/inc/menu/config/category_menu_item-start.php';   
					}
				 }
 				 
         }
       
        public function end_el( &$output, $item, $depth = 0, $args = array() ) {
    		  if($item->type == 'blog_grid'){
			  }elseif($item->type == 'widget'){
  			 }else if($item->type == 'image_text'){
  			 }elseif($item->type == 'image'){
  			 } elseif($item->type == 'page_builder'){
   			 }else if($item->type == 'section'){
				include  hexwp_PATH .'/inc/menu/config/section-end.php';   
			}else{
   				$output .= "</li>";
			}
 			 
		}
} 
  
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Icon FontFamily
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 /*****************************************************************************************************************************************************

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Icon FontFamily
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_menu_data($item =false,$id=false,$default=false) {
	return isset( $item->$id ) ? $item->$id : $default;

 }
 function hexwp_icon_fontfamily($value =false) {
 	 	if(!empty($value)){
			
			global $hexwp_fonticon_style;
			if(strpos($value,'fa-')!==false){
				$hexwp_fonticon_style['FontAwesome']=true;
			}
			if(strpos($value,'flaticonarrow-')!==false){
				$hexwp_fonticon_style['flaticonarrow']=true;
			}
			if(strpos($value,'flaticonmultimedia-')!==false){
				$hexwp_fonticon_style['flaticonmultimedia']=true;
			} 
			
			if(strpos($value,'flaticonbusiness-')!==false){
				$hexwp_fonticon_style['flaticonbusiness']=true;
			} 
				
			if(strpos($value,'flaticonoffice-')!==false){
				$hexwp_fonticon_style['flaticonoffice']=true;
			} 
			if(strpos($value,'flaticoninterface-')!==false){
				$hexwp_fonticon_style['flaticoninterface']=true;
			} 
			
			if(strpos($value,'flaticonessentialset-')!==false){
				$hexwp_fonticon_style['flaticonessentialset']=true;
			} 
			if(strpos($value,'flaticontechsupport-')!==false){
				$hexwp_fonticon_style['flaticontechsupport']=true;
			} 
			if(strpos($value,'flaticontech-')!==false){
				$hexwp_fonticon_style['flaticontech']=true;
			} 
			if(strpos($value,'flaticonstrategy-')!==false){
				$hexwp_fonticon_style['flaticonstrategy']=true;
			} 
			if(strpos($value,'flaticonhipster-')!==false){
				$hexwp_fonticon_style['flaticonhipster']=true;
			} 
			if(strpos($value,'flaticonfashion-')!==false){
				$hexwp_fonticon_style['flaticonfashion']=true;
			} 
			if(strpos($value,'flaticonwebdesign-')!==false){
				$hexwp_fonticon_style['flaticonwebdesign']=true;
			} 
			if(strpos($value,'flaticontravel-')!==false){
				$hexwp_fonticon_style['flaticontravel']=true;
			} 
			if(strpos($value,'flaticonnetwork-')!==false){
				$hexwp_fonticon_style['flaticonnetwork']=true;
			} 
			 
			if(strpos($value,'metrizeicon-')!==false){
				$hexwp_fonticon_style['metrizeicon']=true;
			}
			if(strpos($value,'typcn-')!==false){
				$hexwp_fonticon_style['typcn']=true;
			} 
		}
	 
 }
 
 function hexwp_icon_enqueue($var=false,$true=false) {
	global $hexwp_fonticon_style;
  	if(!empty($hexwp_fonticon_style['FontAwesome']) || !empty($true)) wp_enqueue_style('hexwp_fontawesome',hexwp_DIR. '/css/fonts/fontawesome.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonarrow'])  || !empty($true)) wp_enqueue_style('hexwp_flaticonarrow',hexwp_DIR . '/css/fonts/flaticonarrow.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonmultimedia'])  || !empty($true) ) wp_enqueue_style('hexwp_flaticonmultimedia',hexwp_DIR. '/css/fonts/flaticonmultimedia.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonbusiness'])  || !empty($true)) wp_enqueue_style('hexwp_flaticonbusiness',hexwp_DIR. '/css/fonts/flaticonbusiness.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonoffice'])  || !empty($true)) wp_enqueue_style('hexwp_flaticonoffice',hexwp_DIR. '/css/fonts/flaticonoffice.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticoninterface'])  || !empty($true) ) wp_enqueue_style('hexwp_flaticoninterface',hexwp_DIR. '/css/fonts/flaticoninterface.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonessentialset']) || !empty($true)) wp_enqueue_style('hexwp_flaticonessentialset',hexwp_DIR. '/css/fonts/flaticonessentialset.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticontechsupport']) || !empty($true)) wp_enqueue_style('hexwp_flaticontechsupport',hexwp_DIR. '/css/fonts/flaticontechsupport.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticontech'])  || !empty($true)) wp_enqueue_style('hexwp_flaticontech',hexwp_DIR. '/css/fonts/flaticontech.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonstrategy'])  || !empty($true)) wp_enqueue_style('hexwp_flaticonstrategy',hexwp_DIR. '/css/fonts/flaticonstrategy.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonhipster'])  || !empty($true) ) wp_enqueue_style('hexwp_flaticonhipster',hexwp_DIR. '/css/fonts/flaticonhipster.css','',$var);
  	if(!empty($hexwp_fonticon_style['flaticonfashion']) || !empty($true) ) wp_enqueue_style('hexwp_flaticonfashion',hexwp_DIR. '/css/fonts/flaticonfashion.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonwebdesign']) || !empty($true) ) wp_enqueue_style('hexwp_flaticonwebdesign',hexwp_DIR. '/css/fonts/flaticonwebdesign.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticontravel']) || !empty($true) ) wp_enqueue_style('hexwp_flaticontravel',hexwp_DIR. '/css/fonts/flaticontravel.css','',$var);  
 	if(!empty($hexwp_fonticon_style['flaticonnetwork']) || !empty($true)) wp_enqueue_style('hexwp_flaticonnetwork',hexwp_DIR. '/css/fonts/flaticonnetwork.css','',$var);  
 	if(!empty($hexwp_fonticon_style['metrizeicon']) || !empty($true)) wp_enqueue_style('hexwp_metrizeicon',hexwp_DIR. '/css/fonts/metrizeicon.css','',$var);  
 	if(!empty($hexwp_fonticon_style['typcn']) || !empty($true)) wp_enqueue_style('hexwp_typcn',hexwp_DIR. '/css/fonts/typcn.css','',$var);   
}
add_action('wp_footer', 'hexwp_icon_enqueue'); 

