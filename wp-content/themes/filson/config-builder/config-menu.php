<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Socail Icons Config
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_menu_config( $args,$out = false ,$out_css=false ) {
	wp_reset_query();
	wp_reset_postdata();
 
	$option = $args['option'];
	$key = $args['key'];
	$output='';
	$css ='';
	if(hexwp_element_show($option)=='show'){
	$title= !empty($option['title']) ?$option['title']:'';

	$menu= !empty($option['menu']) ?$option['menu']:'';
	$title= !empty($option['title']) ?$option['title']:'';
 
	$number= !empty($option['number']) ?$option['number']:'';
	
	$has_title = !empty($title) ?' hw-element-menu-has-title ':'';
  
	 
    
	$classes = array(
			'hw-el-'.$key,
			'hw-el-menu',
 
	
		);
		
		
		ob_start(); 
		?>
		
		 <aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " <?php echo hexwp_el_cssanime($option);?>  >
			<div class="hw-element-menu hw-nav <?php echo $has_title;?> " data-number="<?php echo $number;?>">
			
            <?php if(!empty($title)){?>
 				<h3 class="hw-element-menu-title"><?php echo esc_html($title); ?></h3>
           	<?php }?>
            
            	<div class="hw-element-menu-warp " >
 
				<?php wp_nav_menu( array( 'container' => false, 'menu' => $menu,'menu_class' => 'menu hw-cat-drop', 'fallback_cb' => 'hexwp_fallback_nav',			'theme_location'       => false,'walker' => new hexwp_Walker_Nav_Menu ) ); ?>
         		<?php if(!empty($number)){?>
                <div class="hw-element-menu-more">
                <a class="hw-element-menu-more-text"><?php echo hexwp_t('category_more');?></a>
                <a class="hw-element-menu-more-close"><?php echo hexwp_t('category_close');?></a>
                </div>
                               <?php } ?> 
 
 				</div>  
            
			</div>   

		</aside>
     
	
  		<?php
		$item = '.hw-el-'.$key.'';
		$item_css = 
 			hexwp_var('--hw-menu-tl-txt',$option,'title_color','text').
			hexwp_var_gradient_background_color_css('--hw-menu-tl-bg',$option,'title_background','title_background_2','135deg').
			hexwp_var_font_typo('--hw-menu-tl',$option,'title_typo').   			
 			hexwp_var('--hw-menu-bg',$option,'background_color').
 			hexwp_var_2('--hw-menu-lk',$option,'menu_item_color','link').
			hexwp_var_2('--hw-menu-hv-lk',$option,'menu_item_color','hover').
			hexwp_var_font_typo('--hw-menu',$option,'menu_item_typo').   
			hexwp_var_2('--hw-menu-more-txt',$option,'more_color','text').
 			hexwp_var_2('--hw-menu-more-bg',$option,'more_color','background').
			hexwp_var_font_typo('--hw-menu-more',$option,'more_typo').   
  
			hexwp_var('--hw-menu-br-cr',$option,'border_color').
			hexwp_var('--hw-menu-rd',$option,'radius');
 
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