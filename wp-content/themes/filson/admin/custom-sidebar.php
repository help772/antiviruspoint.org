<?php 
/**
 * Plugin Name: Sidebar Creator
 * Description: It is Autometic sidebar Creator Plugin.
 * Version: 1.0.1
 * Author: souvikitobuz
 * License: A "Slug" license name e.g. GPL2
 */
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Sidebar Creator
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'admin_menu', 'hexwp_my_sidebar_menu' );

function hexwp_my_sidebar_menu() {
add_submenu_page(
        'hexwp_theme', // parent slug
        __('Add Sidebar', 'hexwp'), 
        __('Add Sidebar', 'hexwp'), 
		 'manage_options', 'my-sidebar-unique-identifier', 'hexwp_my_sidebar_options',50
    );
	
	
 }
function hexwp_my_sidebar_options() {
    
 	wp_enqueue_script('hexwp-sidebar-js',hexwp_DIR . '/admin/assets/js/custom-sidebar.js',array( 'jquery'));
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' , 'hexwp' ) );
	}
	?>
	
	<div class="my-form hw-add-sidebar">
        <div class="wrap">
		<h2><?php echo esc_html__('Sidebar Creator Settings' , 'hexwp');?></h2>

		<form method="post" action="options.php" id="InputsWrapper"  role="form">

		<?php wp_nonce_field('update-options'); ?>
 
		<?php  
		$xx=get_option('hexwp_boxes');
		$no=!empty($xx)?count($xx):'';
		if(isset($xx[0])) {
			$xx0 = $xx[0];
			
		}else{
			$xx0 =''; 
			
		}
		?>
        
		<div id="append">
            <p class="text-box">
                <label for="box1"><?php echo esc_html__('Sidebar' , 'hexwp');?>-<span class="box-number">1</span></label>
                <input type="text" name="hexwp_boxes[]" value="<?php echo esc_attr($xx0); ?>" id="box1" />
                <a href="#" class="remove-box"><?php echo esc_html__('Remove' , 'hexwp');?></a>
            </p>
    
    
            
            <?php if($no>1){ ?>
                <?php for($i=1;$i<$no;$i++){?>		
                    <p class="text-box"><label for="box' + n + '"><?php echo esc_html__('Sidebar' , 'hexwp');?>-<span class="box-number"><?php echo esc_html($i+1); ?></span></label> 
                    <input type="text" name="hexwp_boxes[]" value="<?php echo esc_attr($xx[$i]); ?>" id="box' + n + '" /> 
                    <a href="#" class="remove-box"><?php echo esc_html__('Remove' , 'hexwp');?></a>
                    </p>
                <?php }?>
            <?php }?>
		</div>
        <button type="button"  class="add-box"><?php echo esc_html__('Add More!' , 'hexwp');?></button>
        
         
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="no_of_sidebar,sidebar_names,hexwp_boxes" />
        
        <p class="submit">
        	<button type="submit" class="button-primary clsSubmit" value="" /><?php echo esc_html__('Save Changes','hexwp') ?></button>
        </p>
        
        
        </form>
		</div>
	</div>
      <?php
}
$xx=get_option('hexwp_boxes');
    if ( function_exists('register_sidebar') ) {
		$class = ' hw-tbox-'.hexwp_option('title_box_style');

	  if(!empty($xx)){
        foreach ($xx as $side){         
             register_sidebar(array(
				'name' 			=> $side.'('.esc_html__('Custom-Sidebar','hexwp').')',
				'id' 			=>'hexwp_'. sanitize_title($side),
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</aside></div>',
 			'before_title' 		=> '<div class="'.$class.'"><h4 class="hw-title-box"><div class="hw-tab-main"><span>',
        	'after_title'   	=> '</span></div></div><aside class="widget-container">',
           ));
             
       }
	   }
}

 