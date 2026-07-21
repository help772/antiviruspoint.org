<?php
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu Mobile Width
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_mobile_mobile_menu', 'hexwp_nav_mobile_menu');
function hexwp_nav_mobile_menu($opt=array()) {
	
	 $menu = get_term_by( 'slug', hexwp_isset($opt,'mobile_menu'), 'nav_menu' );
	 
	 
	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('mobile_menu_layout'));
  	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
  	$class.=hexwp_isset($opt,'icon_layout')?'hw-nav-icn-boxed':'';
 		
   	$classes = array(
 		'hw-nav-mobile-menu',
		'hw-nav-layout-'.$layout,
 		$class,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
  	);
	 
	
 	$tabs=array();
 	$tabs['menu']=hexwp_t('menu');
	if(!empty($opt['account'])){
		$tabs['account']=hexwp_t('myaccount');
	}
	if(!empty($opt['search'])){
		$tabs['search']=hexwp_t('search');
	}
	if(!empty($opt['cart'])){
 	$tabs['cart']=hexwp_t('cart');
	}
	$count_tabs = count($tabs);
	$count=0;
           
	?>
   	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">
		<li class="hw-middle"><a class="hw-link"><?php 
					if($layout =='text-right'|| $layout =='text-bottom'){
						echo '<span>'.esc_html( hexwp_isset($opt,'menu_title')).'</span>';
  					}
				 
				?></a></li>
		<div class="hw-mobile-content">
		<div class="hw-mobbar-tabs hw_col_1_<?php echo esc_attr($count_tabs); ?>">
        	<?php 
			if($count_tabs>1){
			foreach($tabs as $key => $name):
				$count++; 
            	$active = $count == 1 ?'hw-mobbar-tab-active':'';?>
				 
            	<a data-id="<?php echo esc_attr($key);?>" class="<?php echo esc_attr($active);?>" ><?php echo esc_html($name);?></a>
            
			<?php 
			endforeach;
			}?>
        </div>
        
      		<div class="hw-mobbar-content hw-mobbar-content-active" data-id="menu">
                <div class="hw-mobbar-menu">
                    <?php
                     if(!empty(hexwp_isset($opt,'mobile_menu')) && !empty($menu)){
 
                                wp_nav_menu( 
                                    array( 
                                        'container' 		=> false,
                                        'menu_class'		=> 'hw-nav-menu',
                                        'theme_location' 	=> false,
                                        'walker'			=> new hexwp_Walker_Nav_Menu_Mobile,
                                        'menu' 				=> hexwp_isset($opt,'mobile_menu'),
                                    )
                                ); 
                        }else{
					echo  '<ul class="hw-nav-menu"><li><a href="'.esc_url(home_url( '/') ).'wp-admin/nav-menus.php">'.__('Set menu','hexwp').'</a></li></ul>';
						 
					 }
                        ?>
						<?php if(!empty($opt['wishlist'])){?>
							<?php hexwp_nav_mobile_menu_wish()?>
                        <?php }?>
                </div>
            </div>
            <?php if(!empty($opt['account'])){?>
            <div class="hw-mobbar-content" data-id="account"><?php hexwp_nav_mobile_menu_account();?></div>
            <?php }?>
            <?php if(!empty($opt['search'])){?>
            <div class="hw-mobbar-content" data-id="search"><?php  hexwp_nav_mobile_menu_search();?></div>
            <?php }?>
			<?php if(!empty($opt['cart'])){?>
             <div class="hw-mobbar-content" data-id="cart"><?php hexwp_nav_mobile_menu_cart();?></div>
            <?php }?>
            
        </div>
       </div>
        
      
	<?php
	
 
} 
 
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Nav Menu Mobile Cateogory
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_mobile_mobile_category_menu', 'hexwp_nav_mobile_category_menu');
function hexwp_nav_mobile_category_menu($opt=array()) {
	 $menu = get_term_by( 'slug', hexwp_isset($opt,'mobile_menu'), 'nav_menu' );
 	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('mobile_menu_layout'));
 	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
  	$class.=hexwp_isset($opt,'icon_layout')?'hw-nav-icn-boxed':'';
 		
 		
   	$classes = array(
 		'hw-nav-mobile-category-menu',
		'hw-nav-layout-'.$layout,
 		$class,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
		
  	);
	 
 
 
    
	$count=0;
           
	?>
   	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">
		<li class="hw-middle"><a class="hw-link"><?php 
					if($layout =='text-right'|| $layout =='text-bottom'){
						echo '<span>'.esc_html( hexwp_isset($opt,'category_menu_title')).'</span>';
  					}
				 
				?></a></li>
		<div class="hw-mobile-content">
		 <div class="hw-mobbar-tabs hw_col_1_1">
 	 
            	<a data-id="hw-mobbar-tab-category" class="hw-mobbar-tab-active" ><?php echo esc_html( hexwp_isset($opt,'category_menu_title'));?></a>
          
        </div>
        
      		<div class="hw-mobbar-content hw-mobbar-content-active" data-id="menu">
                <div class="hw-mobbar-menu">
                    <?php
                     if(!empty(hexwp_isset($opt,'mobile_menu')) && !empty($menu)){
 
                                wp_nav_menu( 
                                    array( 
                                        'container' 		=> false,
                                        'menu_class'		=> 'hw-nav-menu',
                                        'theme_location' 	=> false,
                                        'walker'			=> new hexwp_Walker_Nav_Menu_Mobile,
                                        'menu' 				=> hexwp_isset($opt,'mobile_menu'),
                                    )
                                ); 
                        }else{
					echo  '<ul class="hw-nav-menu"><li><a href="'.esc_url(home_url( '/') ).'wp-admin/nav-menus.php">'.__('Set menu','hexwp').'</a></li></ul>';
						 
					 }
                        ?>
				 
                </div>
            </div>
       
            
        </div>
       </div>
        
      
	<?php
	
 
} 
 
 