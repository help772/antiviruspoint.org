<?php
/**
 * Single Product Share
 *
 * Sharing plugins can hook into here or you can add your own code directly.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/share.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 10003.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<?php do_action( 'woocommerce_share' ); // Sharing plugins can hook into here
 
if(hexwp_option('single_product_share_icons') =='enable'){	
 $share_array['key'] = 'share-single';
 
 
$share_array['option'] = array(
	'share_url' => get_permalink(),
 	'icon_style' => hexwp_option('single_product_share_icons_style'),
  	 'alignment' => 'right',
);
  echo hexwp_share_icons_config($share_array,1); 
 }
 
 

 
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
