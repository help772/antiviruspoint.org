<?php

/**

 * Template Name: Content

 *

 * @package Make

 */

get_header(); 
 // Breadcrumbs 
 hexwp_breadcrumbs();
  ?>

<div class="hw-middle-content">
  
	<div class="hw-content hw-main">
 
 		<div class="hw-column-main">
    
             
                     <?php the_content();?>
              
              
        </div>
		</div>
	
    
</div>

   
<?php get_footer(); ?>
