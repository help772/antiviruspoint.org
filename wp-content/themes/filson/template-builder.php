<?php

/**

 * Template Name: Builder Template

 *

 * @package Make

 */

get_header(); 
global $post;
$hexwp_meta = get_post_meta( $post->ID ); ?>
<section class="hw-wrapper">
 	<?php the_content();?>
</section> 
<?php
 get_footer(); ?>

