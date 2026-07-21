<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
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
 * @version 10003.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
if(is_shop() || is_product_category() || is_product_tag()|| is_product_taxonomy()){
}else{


$item =wc_get_loop_prop( 'columns' );
if($item == '12' || $item == '11' || $item == '10'|| $item == '9' || $item == '7' ){
		$tab = 3;
		$mob = 2;
 	} elseif($item == '6'  ){
		$tab = 3;
		$mob = 2;
	} elseif($item == '5'  ){
		$tab = 3;
		$mob = 2;
		
	}elseif($item == '4'  ){
 		$tab = 2;
		$mob = 2;
	} elseif($item == '3'  ){
		$tab = 3;
		$mob = 2; 
	} elseif($item == '2'  ){
		$tab = 2;
		$mob = 2; 
	}else{
		$tab = 1;
		$mob = 1;
	
		
	}
$between = hexwp_option('product_between');
$between_class =!empty($option['between'])? $option['between']:hexwp_option('product_between');
   
$classes = array(
  		'hw-item-list',
  		'hw-aw',
  		'hw-flex',
  		'hw-align-center',
		'woocommerce',
 		'hw-grid-gap-'.$between_class,
 		'hw-not-second-image',
		'hw-align-center',
		'elementor-grid',
		'products',
		'hw-none',
		'hw_col_1_'.$item,
		'hw_tab_1_'.$tab,
		'hw_mob_1_'.$mob,
		'hw-none',
	);  
?>
 
 		<ul class=" <?php echo esc_attr(join( ' ', $classes ));?> "  >
<?php } ?>