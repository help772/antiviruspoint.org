<?php
/**
 * Nav Menu API: Walker_Nav_Menu class
 *
 * @package WordPress
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
 
																	 Nav Menu Mobile
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class hexwp_Walker_Nav_Menu_Mobile extends Walker {
 
        public $tree_type = array( 'post_type', 'taxonomy', 'custom' );
		public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
        
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
                 $output .= '<ul class="hw-mob-drop">';
			 
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

  			 	if($item->type == 'section' || $item->type == 'image_text' || $item->type == 'widget'  ){
					include  hexwp_PATH . '/inc/menu/config/none_item-start.php';   
   				}else{ 
					include  hexwp_PATH . '/inc/menu/config/mobile_menu_item-start.php';   
				 
				 }
 				 
                 
 				 
         }
       
        public function end_el( &$output, $item, $depth = 0, $args = array() ) {
 				
				  if($item->type == 'widget'){
  			 }else if($item->type == 'image_text'){
  			 }elseif($item->type == 'image'){
			 }else if($item->type == 'section'){
 			}else{
   				$output .= "</li>";
			}
 			 
		}
} // Walker_Nav_Menu 
 