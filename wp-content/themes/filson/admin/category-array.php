<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Category Array Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_category_array_options($value,$default=false) {
 
	$header_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'numberposts'      => -1,
			'post_type' => 'visualheader',
			'post_status' => 'publish'
		); 
		 
		$options_header = array();
		$options_header_obj =get_posts($header_args); 
 
		if(!empty($options_header_obj) && is_array($options_header_obj) ){
		foreach ($options_header_obj as $header) {
			$options_header[$header->post_name] = $header->post_title;
		}
	}			 
	
	
 	$options['header'] = $options_header;
	/******************************************************************************************************************************************************
																	Sidebars
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $wp_registered_sidebars;
 	$sidebar_options = array();
 	$sidebar_options[''] = esc_html__('Default','hexwp'); 
  	$sidebar_options_obj = $wp_registered_sidebars;
  	if(!empty($sidebar_options_obj)){
		foreach ($sidebar_options_obj as $side) {
			$sidebar_options[$side['id']] = $side['name'];
		}
	}	
	$options['sidebars'] = $sidebar_options;
	
/******************************************************************************************************************************************************
																		Menu
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 
	 	$menu = array();
	$menu_obj = wp_get_nav_menus();
	if(!empty($menu_obj) && is_array($menu_obj) ){
 	foreach ($menu_obj as $menu_item) {
		$menu[$menu_item->slug] = $menu_item->name;
	}
	}
	
		$options['menu'] = $menu;	
/******************************************************************************************************************************************************
																		Page builder
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 	
	$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
 			'child_of' => 0,
			'parent' => -1,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		$options_page = array();
		$options_page_obj =get_pages($page_args); 
  		$options_page[''] = __('None','hexwp');

		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $hexwppage) {
			$options_page[$hexwppage->post_name] = $hexwppage->post_title;
		}
	}		
	$options['page_builder'] = $options_page;
/******************************************************************************************************************************************************
																		Page Builder Footer
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 	
	
	$page_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
 			'child_of' => 0,
			'parent' => -1,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		$options_page = array();
		$options_page_obj =get_pages($page_args); 
 		$options_page[''] = __('Default','hexwp');

		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $hexwppage) {
			$options_page[$hexwppage->post_name] = $hexwppage->post_title;
		}
	}		
	$options['page_builder_footer'] = $options_page;
/******************************************************************************************************************************************************
																		Sliders
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	 	 
 
	$slider_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'numberposts'      => -1,
			'post_type' => 'visualslider',
			'post_status' => 'publish'
		); 
		 
		$options_slider = array();
		$options_slider_obj =get_posts($slider_args); 
 
		if(!empty($options_slider_obj) && is_array($options_slider_obj) ){
		foreach ($options_slider_obj as $visualslider) {
			$options_slider[$visualslider->ID] = $visualslider->post_title;
		}
	}			 
	
	
	$options['sao_slider'] = $options_slider;

/******************************************************************************************************************************************************
																		Product Cat
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	$options_product = array();
	$options_product_obj = get_categories('taxonomy=product_cat&type=product&hide_empty=0');
	 	$options_product['']= esc_html__('All Categories','hexwp');  
	if(!empty($options_product_obj) && is_array($options_product_obj) ){
 	foreach ($options_product_obj as $product) {
    	$options_product[$product->slug] = $product->cat_name;
	}
	}
	$options['product_cat'] = $options_product;
/******************************************************************************************************************************************************
																		Blog Cats
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	$options_categories = array();
	$options_categories_obj = get_categories('hide_empty=0');
 	$options_categories['']=esc_html__('All Categories','hexwp');
	if(!empty($options_categories_obj) && is_array($options_categories_obj) ){
 	foreach ($options_categories_obj as $category) {
		$options_categories[$category->slug] = $category->cat_name;
	}
	}
	$options['cats']= $options_categories;
/******************************************************************************************************************************************************
																		Testimonials
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
 	$menu = array();
	$menu_obj = wp_get_nav_menus();
	if(!empty($menu_obj) && is_array($menu_obj) ){
 	foreach ($menu_obj as $menu_item) {
		$menu[$menu_item->term_id] = $menu_item->name;
	}
	}
	$options_testimonial = array();
	$options_testimonial_obj = get_categories('taxonomy=testimonial_category&type=testimonial&hide_empty=0');
	$options_testimonial['']= esc_html__('All Categories','hexwp'); 
	if(!empty($options_testimonial_obj) && is_array($options_testimonial_obj) ){
 	foreach ($options_testimonial_obj as $testimonial) {
    	$options_testimonial[$testimonial->slug] = $testimonial->cat_name;
	}
	}
	$options['testimonial_category'] = $options_testimonial;

/******************************************************************************************************************************************************
																		Staff Category
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	$options_staff = array();
	$options_staff_obj = get_categories('taxonomy=staff_category&type=staff&hide_empty=0');
	 	$options_staff['']=  hexwp_t('category');  
	if(!empty($options_staff_obj) && is_array($options_staff_obj) ){
 	foreach ($options_staff_obj as $staff) {
    	$options_staff[$staff->slug] = $staff->cat_name;
	}
	}
	 
	$options['staff_category'] = $options_staff;

/******************************************************************************************************************************************************
																		Portfolio Category
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$options_portfolio = array();
	$options_portfolio_obj = get_categories('taxonomy=portfolio_category&type=portfolio&hide_empty=0');
	 	$options_portfolio['']=  hexwp_t('category');  
	if(!empty($options_portfolio_obj) && is_array($options_portfolio_obj) ){
 	foreach ($options_portfolio_obj as $portfolio) {
    	$options_portfolio[ $portfolio->slug ] = $portfolio->cat_name;
	}
	}
	 
	$options['portfolio_category'] = $options_portfolio;

/******************************************************************************************************************************************************
																		Price args
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$price_args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
 
			'post_type' => 'pricetable',
			'post_status' => 'publish'
		); 
		 
		$options_price = array();
		$options_price_obj =get_posts($price_args); 
 
		if(!empty($options_price_obj) && is_array($options_price_obj) ){
		foreach ($options_price_obj as $price) {
			$options_price[$price->post_name] = $price->post_title;
		}
	}	
	
	$options['pricetable'] = $options_price;
	
/******************************************************************************************************************************************************
																		Contactform
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$contactform_args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
 
		 
	$options_contactform= array();
	$options_contactform_obj =get_posts($contactform_args); 
 
	if(!empty($options_contactform_obj) && is_array($options_contactform_obj) ){
		foreach ($options_contactform_obj as $contactform) {
			if(!empty($contactform)){
			$options_contactform[$contactform->post_name] = $contactform->post_title;
		}}
	}	
	
	$options['contactform'] = $options_contactform;

	/******************************************************************************************************************************************************
																		Footer Category
	*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

	$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'meta_value' => 'template-footer.php',
			'child_of' => 0,
			'parent' => -1,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		 
		$options_page = array();
		$options_page_obj =get_pages($page_args); 
 		$options_page[''] = __('None','hexwp');

		if(!empty($options_page_obj) && is_array($options_page_obj) ){
		foreach ($options_page_obj as $hexwppage) {
			$options_page[$hexwppage->post_name] = $hexwppage->post_title;
		}
	}	
	
	$options['page_builder_footer'] = $options_page;

	if(!empty($default)){
		$none_default=array(''=>__('Default','hexwp'));
		if(!empty($options[$value])){
		$array =   $none_default +  $options[$value];
		}
		return !empty($array)?$array:'';
		
  	}else{
 		return !empty($options[$value])?$options[$value]:array() ;
	}

	
}