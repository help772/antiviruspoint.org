<?php
 if($type_import=='menu' ||   $homepage_import=='menu'){
 include hexwp_DI_PATH . 'demos/menu/main-menu.php';
 
include hexwp_DI_PATH . 'demos/menu/plus-menu.php';

include hexwp_DI_PATH . 'demos/menu/mobile-menu.php';

include hexwp_DI_PATH . 'demos/menu/category-menu.php';

include hexwp_DI_PATH . 'demos/menu/footer-menu.php';
include hexwp_DI_PATH . 'demos/menu/useful-links.php';

include hexwp_DI_PATH . 'demos/menu/category-footer-menu.php';

$locations['hexwp_main_menu'] = $main_menu_id;
$locations['hexwp_header_menu'] = $main_menu_id;   
$locations['hexwp_plus_menu'] = $top_menu_id;
$locations['hexwp_mobile_menu'] =$mobile_menu_id;
$locations['hexwp_category_menu'] =$cat_menu_id;
 	
set_theme_mod( 'nav_menu_locations', $locations );


 }
 
 
 
 ?>