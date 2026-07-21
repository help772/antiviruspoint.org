<?php
include_once hexwp_PATH . '/config-builder/config-menu.php';

include_once hexwp_PATH . '/config-builder/config-blog.php';
include_once hexwp_PATH . '/config-builder/config-blog_carousel.php';
include_once hexwp_PATH . '/config-builder/config-blog_masonry.php';
 
if ( function_exists ( "is_woocommerce" )){
include_once hexwp_PATH . '/config-builder/config-product.php';
include_once hexwp_PATH . '/config-builder/config-product_carousel.php';
include_once hexwp_PATH . '/config-builder/config-product_cat.php';
}
 
include_once hexwp_PATH . '/config-builder/config-searchfield.php';
include_once hexwp_PATH . '/config-builder/config-social_icons.php';
include_once hexwp_PATH . '/config-builder/config-share_icons.php';
include_once hexwp_PATH . '/config-builder/config-contactform_7.php';
include_once hexwp_PATH . '/config-builder/config-comments.php';

 