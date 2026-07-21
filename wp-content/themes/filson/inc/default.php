<?php

  /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Default Options
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_option_default($id=false,$id2=false,$return=false){
	global $smof_data;	
 	$primary_color_background='#ff8c00';
 
	$primary_color='#ffffff';
	
	
	$default= array(
 
 
	/////////////////////////////////////////////////////
	//	General
	////////////////////////////////////////////////////
		"responsive"							=>	'enable',
		"time_format"							=>	'traditional',
	/////////////////////////////////////////////////////
	//	Body Layout
	////////////////////////////////////////////////////	
		"body_width"							=>	'1800px',
		"body_background_color"					=>	'#ffffff',
 		"body_background_type"					=>	'none',
  		"body_boxed_background"					=>	'#ffffff',
 		"body_boxed_shadow"						=>	array(	'size'=> '15px-0px'	,	'color'	=>	'rgba(0,10,20,0.05)'	),
		
	/////////////////////////////////////////////////////
	//	General Style
	////////////////////////////////////////////////////

 		"primary_color"							=>	array(	'background'	=> $primary_color_background ,'background_2'	=>'#ff8c00' ,	'text'			=>	'#ffffff'	),
  		"main_link_color"						=>	array(	'link'			=> '#102030'	,	'hover'			=>	$primary_color_background	),
		"main_text_color"						=>	"#80888f",
		"main_highlight_color"					=>	$primary_color_background,
		"main_grey_color"						=>	"#f5f8fb",
		"main_border_color"						=>	"#ebeef1",
		"main_radius"							=>	"10px",
		"button_radius"							=>	"1000px",
		"social_radius"							=>	"1000px",
 		"social_style"							=>	"style-3",
		
	/////////////////////////////////////////////////////
	//	Header Layout
	////////////////////////////////////////////////////
 	
		"header_builder"							=> 'header-filson',
	 
	/////////////////////////////////////////////////////
	//	Content Options
	////////////////////////////////////////////////////
	/////////////////////////////////////////////////////
	//	Content Options
	////////////////////////////////////////////////////
		/////////////////////////////////////////////////////
	//	Content Options
	////////////////////////////////////////////////////
		"column"								=>	"main_right",
		"column_left"							=>	"1_4",
		"column_right"							=>	"1_4", 
		"breadcrumbs_show"							=>	'show',
		"breadcrumbs_color"						=>	array(	'background'	=> '#ebeef1'	,	'text'	=>	'#304050'	),

 
	/////////////////////////////////////////////////////
	//	Sidebar Options
	////////////////////////////////////////////////////
 		"sidebar_box_layout"					=>	"boxed-item",
		"sticky_main"							=>	true,
	/////////////////////////////////////////////////////
	//	Blog Options
	////////////////////////////////////////////////////
		"blog_title"							=>	__('Recent Blog', 'hexwp'),
		"blog_excerpt"							=>	true,
		"blog_meta_author"						=>	true,
		"blog_meta_category"					=>	true,
		"blog_meta_date"					=>	true,
 		"blog_meta_comments"					=>	true,
		"blog_readmore"							=>	true,
		"blog_hover_post_icon"					=>	'show',
		"pagenavi_ajax"							=>	true,
		"blog_layout"							=>	"list",
		"blog_grid_layout"						=>	"grid_6",
		"blog_list_layout"						=>	"list_1",
		"blog_between"							=>	"20px",
		"blog_ratio"							=>	"hw-ratio60",
		"blog_image_size"						=>	"medium_large",
		"blog_image_width"						=>	"35",
		"blog_alignment"						=>	'left',
		"blog_box_layout"						=>	"boxed-item",
		"blog_caption_layout"					=>	"gradient-bottom",
		"blog_meta_layout"						=>	array(	'location'=> 'title-bottom'	,	'between'	=>	'between-3' ,	'layout'	=>	'layout-1'	),
	 
	 
	 
 	/////////////////////////////////////////////////////
	//	Single Options
	////////////////////////////////////////////////////
		"single_template"						=>	"3",
		"single_share_icons"					=>	true,
		"single_share_icons_style"				=>'style-3',
		"single_tags"							=>	true,
		"single_lightbox"						=>	true,
 
		"single_author_box"						=>	true,   
		"single_meta_date"					=>	true,
		"single_meta_author"					=>	true,
 		"single_meta_category"						=>	true,
		"single_meta_view"						=>	true,
		"single_meta_comments"					=>	true,
		"single_meta_layout"					=>	array(	'between'	=>	'between-3' ,	'layout'	=>	'layout-1'	),
		"related"								=>	true,
		"related_title"							=>	__('Related Blog', 'hexwp'),
		"related_row"							=>	"1",
		"related_query"							=>	"category",
		"related_layout"						=>	"grid_5",
		"related_between"						=>	"20px",
		"related_ratio"							=>	"hw-ratio75",
		"related_image_size"					=>	"medium",
		"related_box_layout"					=>	"boxed-item",
		
		"related_excerpt"						=>	false,
		"comments_layout_type"					=>	"hw-thread",

	/////////////////////////////////////////////////////
	//	Product Options
	////////////////////////////////////////////////////
 		"product_column"						=>	"left_main",
		"product_column_left"					=>	"1_4",
		"product_column_right"					=>	"1_4",
		"product_number"						=>	"20",
 		"product_excerpt"						=>	'enable',
 		"product_meta_category"					=>	true,
		"product_meta_rating"					=>	true,
		"product_addcart"						=>	true,
		
		"product_onsale"						=>	'percentage',
		"product_share_icons"					=>	true,
		"product_layout"						=>	"grid",
		"product_grid_layout"					=>	"grid_6",
		"product_list_layout"					=>	"list_1",
		"product_between"						=>	"20px",
		"product_ratio"							=>	"hw-ratio-auto",
		"product_image_size"					=>	"large",
		"product_image_width"					=>	"25",		
		"product_second_image"					=>	true,
		"product_alignment"						=>	"center",
		"product_box_layout"					=>	"boxed-item",
		"single_product_image_width"			=>	"40",
		"single_product_gallery_item"			=>	5,
		"single_product_image_size"				=>	'full',
		"single_product_share_icons"			=>	'enable',
		"single_product_share_icons_style"		=>	'style-3',
		"single_product_countdown"				=>	'enable',
		"related_product_column"				=>	8,
 		 
 
	/////////////////////////////////////////////////////
	//	Title Box Options
	////////////////////////////////////////////////////
 		"title_box_all"							=>	__('All','hexwp'),
 		"title_box_style"						=>	"style-8",
		"title_box_main_color"					=>	array(	'background'	=> '#f5f8fb'	,	'text'			=>	'#102030'	),
		"title_box_tab_color"					=>	array(	'background'	=> 'rgba(0,0,0,0.0)'	,	'text'			=>	'#8090a0'	),
		"title_box_active_color"				=>	array(	'background'	=> 'rgba(0,0,0,0.0)'	,	'text'			=>	$primary_color_background	),
		"title_box_border_color"				=>	'rgba(118,128,138,0.1)',
		"title_box_radius"						=>	"20px",
	/////////////////////////////////////////////////////
	//	Post Style
	////////////////////////////////////////////////////
		"post_background_color"					=>	"#ffffff",
		"post_title_color"						=>	array(	'link'			=> '#405060'	,	'hover'			=>	$primary_color_background	),
		"price_color"							=>	array(	'main'			=> '#102030'	,	'sale'	=>	'#102030'	, 'regular'			=>	'#90a0b0'	),
		"post_excerpt_color"					=>	"#607080",
		"post_meta_color"						=>	"#90a0b0",
		"countdown_color"							=>	array(	'background'			=> '#f5f8fb'	,'number'			=>	'#203040' ,'text'			=>	'#90a0b0'	),
  		"rating_color"							=>	array(	'rating'		=> $primary_color_background	,	'none'			=>	'rgba(124,128,132,0.15)'	),
  		"featured_color"							=>	array(	'background'		=> '#203040'	,	'background_2'			=>	'#203040','text'			=>	'#FFFFFF'	),
		
   		"box_border_size"						=>	"0px-0px-0px-3px",
   		"box_border_color"						=>	"#0014280a",
		
 		 
 		"image_effect"							=>	"grow",
		"caption_effect"						=>	"imghvr-fade",
		"caption_background_color"				=>	"rgba(0,10,20,0.75)",
		"caption_color"							=>	"#ffffff",
	//	Footer Options
	////////////////////////////////////////////////////
 
		
		"page_footer"							=>	"",
		"footer_column"							=>	"5",
		"footer_bottom_code"					=>	__("© CopyRight 2024 | template designed by Hex Wp - All lefts reserved | Designed for WordPress",'hexwp'),
 		"footer_social"						=>	'show',
		"footer_icon_style"						=>	"style-3",
		
		
		
	/////////////////////////////////////////////////////
	//	Footer Style
	////////////////////////////////////////////////////
		"footer_background_color"				=>	"#102030",
		"footer_link_color"						=>	array(	'link'			=> '#f2f5f8'	,	'hover'			=>	$primary_color_background	),
		"footer_text_color"						=>	"#a0b0c0",
		"footer_highlight_color"				=>	$primary_color_background,
		"footer_grey_color"						=>	"#203040",
		"footer_border_color"					=>	"#304050",
		"footer_radius"							=>	"10px",
	/////////////////////////////////////////////////////
	//	Social Networks
	////////////////////////////////////////////////////		
		"social_facebook"						=>	"#",
		"social_twitter"						=>	"#",
		"social_googleplus"						=>	"#",
		"social_telegram"						=>	"#",
		"social_instagram"						=>	"#",
 
	/////////////////////////////////////////////////////
	//	Typography
	////////////////////////////////////////////////////	
	
 		"body_font_family"						=>	'Rubik',
    	 "title_box_main_typo"					=>	array(	'font_size'		=> '20'	,		'font_weight'		=>	'500' ,	'text_transform'	=>	''	),
		"title_box_tab_typo"					=>	array(	'font_size'		=> '15'	,		'font_weight'		=>	'normal' ,	'text_transform'	=>	''	),
 		"post_title_typo"						=>	array(	'font_weight'	=>	'500' ,	'text_transform'	=>	''	),
		"post_excerpt_typo"						=>	array(	'font_weight'	=>	'normal' ,	'text_transform'	=>	''	),
		"post_meta_typo"						=>	array(	'font_weight'	=>	'normal' ,	'text_transform'	=>	''	),
		"read_more_typo"						=>	array(	'font_weight'	=>	'500' ,	'text_transform'	=>	'uppercase'	),
		"more_posts_typo"						=>	array(	'font_weight'	=>	'normal' ,	'text_transform'	=>	''	),
		"product_title_typo"					=>	array(	'font_weight'	=>	'normal' ,	'text_transform'	=>	''	),
		"price_typo"							=>	array(	'font_weight'	=>	'500' ,	'text_transform'	=>	''	),
		"article_typo"							=>	array(	'font_size'		=>	'14px' ,		'line_height'	=>	'2.2em'	),
		"button_typo"							=>	array(	'font_size		'=> '14px'	,	'font_weight'		=>	'500' ,	'text_transform'	=>	'uppercase'	),
		
		 
	);
 if(!empty($return) || empty($smof_data['responsive'])){
		if(!empty($id2)){
			return isset($default[$id][$id2])?$default[$id][$id2]:'';
		}elseif(!empty($id)){
			return isset($default[$id])?$default[$id]:'';
		} else{
			return $default;
		}
	 
	 }else{
		return '';
	 }
}
function hexwp_nav_default($id=false){
$default= array(
	
		"menu_layout"						=>	'text-right',
 		"menu_icon_layout"						=>	'',
 		"mobile_menu_layout"						=>	'icon',
 		"mobile_menu_icon_layout"						=>	'',
 		
		"category_layout"						=>	'text-right',
 		"category_boxed_layout"					 =>	'boxed',
  		"category_icon_layout"					=>	'',
 		"category_width"						=>	'1_5',
		 
		
		"account_layout"						=>	'text-right',
		"account_icon_layout"						=>	'',


		"search_position"						=>	'fixed',
		"search_layout"							=>	'text-right',
		"search_icon_layout"					=>	'',
		
		
		"search_position"						=>	'fixed',
		"search_layout"							=>	'text-right',
		"search_button_layout"							=>	'icon',
		"search_icon_layout"					=>	'',
		
		"social_position"						=>	'fixed',
		"social_layout"							=>	'text-right',
		"social_icon_layout"					=>	'',


 		"contact_us_layout"						=>	'text-right',
		"contact_us_icon_layout"					=>	'',
		
 		"call_layout"							=>	'text-right',
		"call_icon_layout"					=>	'',

		"cart_layout"							=>	'text-right',
		"cart_icon_layout"					=>	'',
 
 
 		"wish_layout"							=>	'text-right',
		"wish_icon_layout"					=>	'',
 				
	);


			return isset($default[$id])?$default[$id]:'';
}