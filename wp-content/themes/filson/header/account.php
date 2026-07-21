<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Nav Account
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_account', 'hexwp_nav_account');
function hexwp_nav_account($opt) {
	
  	
	global $user_ID, $user_identity, $user_level;
	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('account_layout'));
 	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
	$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';
	$classes = array(
 		'hw-nav-account',
		 'hw-nav-layout-'.$layout,
  		$class,
  		'hw-nav-'.hexwp_isset($opt,'key'),
		hexwp_isset($opt,'side'),
  	);
  
	?>
 	 
	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">   
 	
		<li class="hw-middle">
        
			<?php
             //***************************************************************************************************************************************
            /*---------------------------------------------------------Sing Out----------------------------------------------------------------------*
            *****************************************************************************************************************************************/
             if ( $user_ID ) :?> 
                    
                <a class="hw-link"><?php 
					if($layout =='text-right'  || $layout =='text-bottom'){
						echo '<span>'.esc_html(hexwp_t('myaccount')).'</span>';
  					}
					else if($layout =='text-right-2'){
						echo '<span class="hw-twoline">';
						echo '<span>'.esc_html(hexwp_t('myaccount')).'</span>';
						echo '<span>'.esc_html($user_identity).'</span>';
						echo '</span>';
						
					}
				?></a>				
                 
                <ul class="hw-drop" >
     
                    <?php if ( function_exists ( "is_woocommerce" )){ ?>
                        
 						<li><a class="hw-username"><?php echo  esc_html($user_identity);?></a></li>

                        
                        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :  ?>	
                              <li><a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) );?>"><?php echo esc_html( $label );?></a></li>
                        <?php endforeach; ?>
                            
                    <?php }else{?> 
                            
                            <li><a href="<?php echo esc_url(home_url( '/') ).'wp-admin';?>"><?php echo esc_html(hexwp_t('dashboard'));?></a></li>
                            <li><a href="<?php echo esc_url(home_url( '/') ).'wp-admin/profile.php';?>"><?php echo esc_html(hexwp_t('profile'));?></a></li>
                            <li><a href="<?php echo esc_url(wp_logout_url());?>"><?php echo esc_html(hexwp_t('logout'));?></a></li>
            
                    <?php  }?>
    
                </ul>
                   
            <?php 
            //***************************************************************************************************************************************
            /*---------------------------------------------------------Sing In----------------------------------------------------------------------*
            *****************************************************************************************************************************************/
  			else : 
			
				 if ( function_exists ( "is_woocommerce" )){
                    $link =  get_permalink( get_option('woocommerce_myaccount_page_id') );
                }else{
                    $link =  wp_login_url();
                }
                ?>
                    
				<a class="hw-link" href="<?php echo esc_url($link);?>"><?php 
		 
					if($layout =='text-right'  || $layout =='text-bottom'){
						echo '<span>'.esc_html(hexwp_t('myaccount')).'</span>';

						
				}else if($layout =='text-right-2'){
						echo '<div class="hw-twoline">';
						echo '<span>'.esc_html(hexwp_t('singin')).' '.hexwp_t('or').'</span>';
						echo '<span>'.hexwp_t('register').'</span>';
						echo '</div>';
						
					}
 				?></a>
        
			<?php 
			endif;?>
						
		</li>     	
	</div>
 	<?php 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Mobile Account
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('hexwp_header_builder_mobile_account', 'hexwp_nav_mobile_account');
function hexwp_nav_mobile_account($opt=array()) {
	$layout =  hexwp_isset($opt,'layout',hexwp_nav_default('account_layout'));
 	$class = hexwp_isset($opt,'boxed_layout')?' hw-nav-boxed ':'';
	$class.= hexwp_isset($opt,'icon_layout')?' hw-nav-icn-boxed ':'';

   	global $user_ID, $user_identity, $user_level;
	$logged = !empty ( $user_ID ) ?'hw-logged':'';

	$classes = array(
 		'hw-nav-account',
		$logged,
		 'hw-nav-layout-'.$layout,
 		$class.
		'hw-nav-'.hexwp_isset($opt,'key'),
 		hexwp_isset($opt,'side'),

  	);
 
	?>
 	 
	<div class="<?php echo esc_attr(join( ' ', $classes ));?>">   
 		<li class="hw-middle">
			<?php
             
             //***************************************************************************************************************************************
            /*---------------------------------------------------------Sing Out----------------------------------------------------------------------*
            *****************************************************************************************************************************************/
             if ( $user_ID ) :?> 
                    
                <a class="hw-link"><?php 
					if($layout =='text-right'  || $layout =='text-bottom'){
						echo '<span>'.esc_html(hexwp_t('myaccount')).'</span>';
  					
					} else if($layout =='text-right-2'){
						echo '<div class="hw-twoline">';
						echo '<span>'.esc_html(hexwp_t('myaccount')).'</span>';
						echo '<span>'.esc_html($user_identity).'</span>';
						echo '</div>';
						
					}
				 
				?></a>	
                
		<ul class="hw-mobile-content">
  			<div class="hw-mobbar-menu hw-nav-account ">
					<li><a class="hw-username"><?php echo  esc_html($user_identity);?></a></li>

						<?php if ( function_exists ( "is_woocommerce" )){ ?>
                        
							<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :  ?>	
 								<li><a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) );?>"><?php echo esc_html( $label );?></a></li>
 							<?php endforeach;?>
                            
 						<?php }else{?> 
								<li><a href="<?php echo esc_url(home_url( '/') ).'wp-admin';?>"><?php echo esc_html(hexwp_t('dashboard'));?></a></li>
								<li><a href="<?php echo esc_url(home_url( '/') ).'wp-admin/profile.php';?>"><?php echo esc_html(hexwp_t('profile'));?></a></li>
								<li><a href="<?php echo esc_url(wp_logout_url());?>"><?php echo esc_html(hexwp_t('logout'));?></a></li>
 						<?php  }?>
			</div>
		</ul>
                 
		<?php else  :  
		//***************************************************************************************************************************************
		/*---------------------------------------------------------Sing In----------------------------------------------------------------------*
		*****************************************************************************************************************************************/
		?>
        
			<?php
			if ( function_exists ( "is_woocommerce" )){
				$link =  get_permalink( get_option('woocommerce_myaccount_page_id') );
			}else{
				$link =  wp_login_url();
			}
			?>
 
			<a class="hw-link" href="<?php echo esc_url($link);?>"></a>
	
		<?php endif;?> 
             
		</li>
    </div>
   
<?php
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Mobile Menu Account
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_nav_mobile_menu_account() {
 	global $user_ID, $user_identity, $user_level;
	 
	?>
	<div class="hw-mobbar-menu hw-nav-account">
 			<?php
             //***************************************************************************************************************************************
            /*---------------------------------------------------------Sing Out----------------------------------------------------------------------*
            *****************************************************************************************************************************************/
             if ( $user_ID ) :?> 
				
                 
 						<?php if ( function_exists ( "is_woocommerce" )){ ?>
								<li><a class="hw-username"><?php echo  esc_html($user_identity);?></a></li>
							<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :  ?>	
 								<li><a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) );?>"><?php echo esc_html( $label );?></a></li>
 							<?php endforeach;?>
                            
 						<?php }else{?> 
								<li><a href="<?php echo esc_url(home_url( '/') ).'wp-admin';?>"><?php echo esc_html(hexwp_t('dashboard'));?></a></li>
								<li><a href="<?php echo esc_url(home_url( '/') ).'wp-admin/profile.php';?>"><?php echo esc_html(hexwp_t('profile'));?></a></li>
								<li><a href="<?php echo esc_url(wp_logout_url());?>"><?php echo esc_html(hexwp_t('logout'));?></a></li>
 						<?php  }?>
                  
		<?php else  :  
		//***************************************************************************************************************************************
		/*---------------------------------------------------------Sing In----------------------------------------------------------------------*
		*****************************************************************************************************************************************/
		?>
        
			<?php
			if ( function_exists ( "is_woocommerce" )){
				$link =  get_permalink( get_option('woocommerce_myaccount_page_id') );
			}else{
				$link =  wp_login_url();
			}
			?>
 
			<li><a href="<?php echo esc_url($link);?>"><?php
				echo  esc_html(hexwp_t('singin')).' '.hexwp_t('or').' '.hexwp_t('register');
 				?></a></li>
	
		<?php endif;?> 
             
      </div>
   
<?php
}