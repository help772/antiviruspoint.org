<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Nav Wish
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_wish', 'hexwp_nav_wish');
add_filter('hexwp_header_builder_mobile_wish', 'hexwp_nav_wish');
function hexwp_nav_wish($opt=array()){
 	
	
 	if ( function_exists ( "is_woocommerce" ) && function_exists('yith_wcwl_count_all_products')) {
		$wishlist = !empty(get_option('yith_wcwl_wishlist_page_id')) ?get_option('yith_wcwl_wishlist_page_id'):'';
		$wish_url = get_permalink($wishlist);
  		$layout =  hexwp_isset($opt,'layout', hexwp_nav_default('wish_layout'));
		$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
		$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
		
		
		
 		$classes = array(
			'hw-nav-wish',
  			'hw-nav-layout-'.$layout,
  			$class,
  			'hw-nav-'.hexwp_isset($opt,'key'),
				hexwp_isset($opt,'side'),
	
 			
		);	
 		?>
		<div class="<?php echo esc_attr(join( ' ', $classes ));?>"  >		   
      
           <li class="hw-middle">
           		<?php
           		echo '<a class="hw-link" href="'.esc_url($wish_url).'">';
					echo '<div class="hw-count"><span>'.hexwp_number_replace(yith_wcwl_count_all_products()).'</span></div>';

					if($layout =='text-right' || $layout =='text-bottom'){
						echo '<span>'.hexwp_t('mywishlist').'</span>';  
	
					}
					 
					else if($layout =='text-right-2'){
						echo '<div class="hw-twoline">';
					echo '<span>'.hexwp_t('favorite').'</span>';
					echo '<span>'.hexwp_t('mywishlist').'</span>';
						echo '</div>';
						
					}
						echo '</a>';
				?>
					
			 
            </li>
              
		</div>
    <?php 
 	}
}
/* 
******************************************************************************************************************************************************
 
																	 Mobile Menu Wish
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function hexwp_nav_mobile_menu_wish() {
	
	
 	if (  function_exists ( "is_woocommerce" ) && function_exists('yith_wcwl_count_all_products')    ) {
 		$wishlist = !empty(get_option('yith_wcwl_wishlist_page_id')) ?get_option('yith_wcwl_wishlist_page_id'):'';
		$wish_url = get_permalink($wishlist);
 		?>
   
        <div class="hw-nav-wish" >		   
            <li>
				<?php
				echo '<a href="'.esc_url($wish_url).'">';
				echo '<div class="hw-count"><span>'.hexwp_number_replace(yith_wcwl_count_all_products()).'</span></div>';
				echo hexwp_t('mywishlist');
				echo '</a>';
				?>
			</li>         
        </div>
 	
	<?php 
 	}
 }
 