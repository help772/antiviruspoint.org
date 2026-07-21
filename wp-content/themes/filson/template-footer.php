<?php

/**

 * Template Name: Footer

 *

 * @package Make

 */

get_header(); 
global $post;
?>
</section>
<div id="hw-footer" >
	<?php the_content();?>
</div> 
 
<?php if( hexwp_option('body_layout' )=='enable'){?>
</div>
<?php }?>
 <footer><?php wp_footer(); ?></footer>
    

</body>
</html>
