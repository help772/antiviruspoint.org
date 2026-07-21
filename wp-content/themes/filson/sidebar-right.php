<?php  
$sidebar= hexwp_active_sidebar('right');
$sticky_main = hexwp_option('sticky_sidebar');
$sidebar_layout='';

 	$sidebar_layout='hw-sidebar-'.hexwp_option('sidebar_box_layout');

 $sticky_sidebar =  $sticky_main=='show'?'hw-sticky-sidebar':'';
if  ( hexwp_hide_sidebar()=='show') { ?>
 
	 <div class="hw-column-sidebar hw-sidebar-right">
		<?php if( is_active_sidebar($sidebar) ){?>
		<section class="hw-sidebar <?php echo esc_attr($sidebar_layout.' '.$sticky_sidebar);?>">
		
        	<?php dynamic_sidebar( sanitize_title($sidebar) );?>
            
		</section>
    	 <?php }?>
     
 	 </div>
 <?php } ?>