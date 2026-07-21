<?php
		$homepage_import = !empty($_POST['homepage_import'])? $_POST['homepage_import']:'';
 	
	$import = array(
		'blog_image'			=> 'blog_image.txt',
		'blog'					=> 'blog.txt',
		
		'cat_image'				=> 'product/cat_image.txt',
		'product_image'			=> 'product/product_image.txt',
		
		'cat'					=> 'product/cat.txt',
 		'product'				=> 'product/product.txt',
 'product_page'				=> 'product/product_page.txt',

		'about_image'				=> 'about_image.txt',
		'contactform'				=> 'contactform.txt',
 		
		'slider_image'				=> 'slider_image.txt',
		'slider'					=> 'slider.txt',
   
 		'homepage_image'				=> 'homepage_image.txt',	
  		'homepage'						=> 'homepage.txt',	
   		'header_homepage'					=> 'header_homepage.txt' 	
	);
	 
	if(!empty($import[$homepage_import])){
  	  $file = hexwp_DI_IMPORTER_CONTENT_DIR . 'xml/'.$import[$homepage_import].'';
	}
	 
	