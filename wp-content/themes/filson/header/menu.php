<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_menu', 'hexwp_nav_menu');
 function hexwp_nav_menu($opt=false) {
 	 $menu = get_term_by( 'slug', hexwp_isset($opt,'menu'), 'nav_menu' );
 	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('menu_layout'));
  	$class  = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
		$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
		$classes = array(
 			 'hw-nav-layout-'.$layout,
			$class,
			'hw-nav-menu',
			'menu',
			'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
	
		);
 	if(hexwp_isset($opt,'menu') && !empty($menu)){
 		
  		
	 
		
			wp_nav_menu( 
				array(
					'container'			=> '',
 					'fallback_cb'		=> array( 
							'menu_location' 		=> 'main',
 							'layout' 	=> $layout,
						),
					'menu_class'		=> esc_attr(join( ' ', $classes )),
					'theme_location'	=> false,
					'menu' 				=> hexwp_isset($opt,'menu'),
					'walker'			=> new hexwp_Walker_Nav_Menu 
				) 
			);  
 		}else{
			echo  '<ul class="'.esc_attr(join( ' ', $classes )).'"><li><a href="'.esc_url(home_url( '/') ).'wp-admin/nav-menus.php">'.__('Set menu','hexwp').'</a></li></ul>';
 		}		
	 
	 
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Category Menu
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_category_menu', 'hexwp_nav_category_menu');
function hexwp_nav_category_menu($opt=false) {
 
 
	 $menu = get_term_by( 'slug', hexwp_isset($opt,'menu'), 'nav_menu' );
 	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('category_layout'));
  	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
	$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
    $class.= hexwp_isset($opt,'menu_margin_top')? ' hw-cat-sub-mg-tp ':'';
      $class.= hexwp_isset($opt,'menu_homepage') && hexwp_header_overlap() =='enable'?' hw-cat-sub ':'';
	
  	$classes = array(
  		'hw-nav-cat-menu',
		 'hw-nav-layout-'.$layout,
 		'hw-nav-'.hexwp_isset($opt,'key'),
 		'hw_col_'.hexwp_isset($opt,'width','1_5'),
		$class,
		hexwp_isset($opt,'side'),
      );
  
 	?>
	
    <div class="<?php echo esc_attr(join( ' ', $classes ));?>">
 		<li class="hw-middle"><a class="hw-link"><?php 
				if($layout =='text-right'  ){
						echo '<span>'.hexwp_isset($opt,'text').'</span><i></i>';
  					}?></a>
			<?php 
			
			
			if(hexwp_isset($opt,'menu')  && !empty($menu)){
 	
					wp_nav_menu( array( 
					
					'container'			=> '',
  					'fallback_cb'		=> array( 
							'menu_location' 		=> 'category',
 						),
					'menu_class'		=> 'menu hw-cat-drop',
					'theme_location'	=> false,
					'menu'				=> hexwp_isset($opt,'menu'),
					'walker'			=> new hexwp_Walker_Nav_Menu 
					));
				}else{
					echo  '<ul class="menu hw-cat-drop"><li><a href="'.esc_url(home_url( '/') ).'wp-admin/nav-menus.php">'.__('Set menu','hexwp').'</a></li></ul>';
	 
	
				}
				
				 
			?>
        </li>
  	</div>
    
	<?php
	 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu Featured
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_menu_featured($featured=false) {
	if(!empty($featured)){
		echo '<span class="hw-menu-tags-'.esc_attr($featured).'">';
		if($featured == 'new'){
			echo hexwp_t('new');
		}elseif($featured == 'sale'){
			echo  hexwp_t('sale') ;
		}elseif($featured == 'hot'){
			echo   hexwp_t('hot') ;
		}elseif($featured == 'featured'){
			echo hexwp_t('featured')  ;
		}
			echo '</span>';
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu Icon Size
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_menu_icon_size($name=false,$size=false) {
	if($size=='fa-1x'){
 		return $name.':1em;';
	}elseif($size=='fa-1-25x'){
 		return $name.':1.25em;';
	}elseif($size=='fa-1-5x'){
 		return $name.':1.5em;';
 	}elseif($size=='fa-1-75x'){
 		return $name.':1.75em;';
 	}elseif($size=='fa-2x'){
 		return $name.':2em;';
   	}elseif($size=='fa-2-5x'){
 		return $name.':2.5em;';
  	}elseif($size=='fa-3x'){
 		return $name.':3em;';
 	}elseif($size=='fa-3-5x'){
 		return $name.':3.5em;';
 	}elseif($size=='fa-4x'){
 		return $name.':4em;';
 	}elseif($size=='fa-4-5x'){
 		return $name.':4.5em;';
 	}elseif($size=='fa-5x'){
 		return $name.':5em;';
 	}else{
 		return '';
	}
 	
} 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu Background
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_menu_background($args=false) {
	 $css='';
  	
	if(!empty($args['image'])){
		$img= wp_get_attachment_image_src($args['image'],'full');
		if(!empty($img[0])){
			
 			$css.= ' --hw-menu-bg-img:url('.esc_url($img[0]).');';
  
  			if(!empty($args['size'])){
				$css.= ' --hw-menu-bg-sz:'.$args['size'].';';
			}
    			if(!empty($args['position'])){
				$css.= ' --hw-menu-bg-pos:'.hexwp_alignment($args['position']).';';
			}
	
			if(!empty($args['opacity'])){
				$css.= ' --hw-menu-bg-op:'.$args['opacity'].';';
			}
		}

	} 
	return $css;
}
function hexwp_nav_menu_image($image=false) {
	 $css='';
  	
	if(!empty($image)){
		$img= wp_get_attachment_image_src($image,'full');
		if(!empty($img[0])){
			
 			$css.= ' --hw-menu-icn-img:url('.esc_url($img[0]).');';
  		}

	} 
	return $css;
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu Width
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_menu_width($size=false) {
	if($size=='200'){
 		return '--hw-drp-wt:200px;';
	}elseif($size=='300'){
 		return '--hw-drp-wt:300px;';
	}elseif($size=='400'){
 		return '--hw-drp-wt:400px;';
 	}elseif($size=='500'){
 		return '--hw-drp-wt:500px;';
 	}elseif($size=='600'){
 		return '--hw-drp-wt:600px;';
   	}elseif($size=='700'){
 		return '--hw-drp-wt:700px;';
  	}elseif($size=='800'){
 		return '--hw-drp-wt:800px;';
 	}elseif($size=='900'){
 		return '--hw-drp-wt:900px;';
 	}elseif($size=='1000'){
 		return '--hw-drp-wt:1000px;';
 
 	}elseif($size=='full-width'){
 		return '--hw-drp-wt:100%;';
 
 	}else{
 		return '';
	}
 	
}
 
 