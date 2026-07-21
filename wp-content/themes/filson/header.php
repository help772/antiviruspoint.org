<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
 

 	<?php
 	  global $post_type;
	  
	$post_type=get_query_var('post_type');

 	global $smof_data,$post,$post_id,$post_type;
   if( is_singular()) {
		$post_id = $post->ID;
 	}  
   	?>
   
 
 	<meta charset="<?php bloginfo( 'charset' ); ?>">
 	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
     
	<?php wp_head(); ?>
   

</head>
<body <?php body_class()?>>
  <?php wp_body_open(); ?>
 
<div class="hw-body-warp">
<div id="hw-header-wrapper"> 
 
 <?php 
 
 hexwp_header(hexwp_header_data());?>

   </div>
<section class="hw-wrapper">
 