<?php
$type_import = !empty($_POST['type_import'])? $_POST['type_import']:'';
if($homepage_import=='widgets' ){
update_option( 'sidebars_widgets',array('') );

$cat_footer_menu = get_term_by( 'slug', 'Footer Categories', 'nav_menu' );
$cat_footer_menu_id = !empty($cat_footer_menu->term_id)?$cat_footer_menu->term_id:'0';

$footer_menu = get_term_by( 'slug', 'Footer Menu', 'nav_menu' );
$footer_menu_id = !empty($footer_menu->term_id)?$footer_menu->term_id:'0';

 
 
$useful_links = get_term_by( 'name', 'Useful Links', 'nav_menu' );
$useful_links_id = !empty($useful_links->term_id)?$useful_links->term_id:'0';
 


 
 
$widgets='
{"sidebar_main_right":{"reza_blog-6":{"title":"Recent Blog","number":"8","cats":"","orderby":"","title_limit":"","excerpt":false,"excerpt_limit":"","meta_category":true,"meta_author":true,"meta_date":true,"meta_view":false,"meta_comments":true,"hover_post_icon":"","layout":"list","column":"1","responsive_column":"","between":"","ratio":"","image_width":"","image_size":"hexwp_medium","alignment":"","box_layout":"","caption_layout":""},"reza_blog_tags-4":{"title":"Tags","count":""}},"sidebar_woocommerce_left":{"woocommerce_product_categories-5":{"title":"Product Categories","orderby":"name","dropdown":0,"count":1,"hierarchical":1,"show_children_only":1,"hide_empty":1,"max_depth":""},"woocommerce_layered_nav-8":{"title":"Color","attribute":"color","display_type":"list","query_type":"and"},"woocommerce_layered_nav-9":{"title":"Size","attribute":"size","display_type":"list","query_type":"and"},"woocommerce_price_filter-6":{"title":"Price"},"woocommerce_rating_filter-6":{"title":"Average Rating"}},"sidebar_footer_1":{"text-3":{"title":"","text":"<a href=\"https:\/\/hex-wp.com\/filson\/wp-content\/themes\/filson\/images\/logo.png\"><img class=\"alignnone size-full wp-image-301005\" src=\"https:\/\/hex-wp.com\/filson\/wp-content\/themes\/filson\/images\/logo.png\" alt=\"\" width=\"220\" height=\"46\" \/><\/a>\r\n\r\nCondimentum adipiscing vel neque dis nam parturient orci at scelerisque neque dis nam parturient.\r\n\r\n451 Wall Street, UK, London\r\nPhone: (064) 332-1233\r\nFax: (099) 453-1357","filter":true,"visual":true}},"sidebar_footer_2":{"woocommerce_product_tag_cloud-2":{"title":"Product Tags"}},"sidebar_footer_3":{"nav_menu-5":{"title":"Footer Categories","nav_menu":'.$cat_footer_menu_id .'}},"sidebar_footer_4":{"nav_menu-7":{"title":"Footer Menu","nav_menu":'.$footer_menu_id .'}},"sidebar_footer_5":{"nav_menu-8":{"title":"Useful Links","nav_menu":'.$useful_links_id .'}}}';

 

 
hexwpdemoimport_import_data(json_decode($widgets));
}
