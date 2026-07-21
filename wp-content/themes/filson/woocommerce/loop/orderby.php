<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version 10003.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(
hexwp_option('product_box_layout') =='boxed-item' ||
hexwp_option('product_box_layout') == 'boxed-item-2' ||
hexwp_option('product_box_layout')=='boxed-details' ||
hexwp_option('product_box_layout')=='boxed-details-2'){
	$class= 'hw-single-boxed';
}else{
	$class= 'hw-single-none';
}
  $product_number= !empty($_GET['product_number']) ? esc_html($_GET['product_number']):'';
$grid_layout = hexwp_option('product_grid_layout');
if($grid_layout =='grid_1'){$number =1;}
elseif($grid_layout =='grid_2'){$number =2;}
elseif($grid_layout =='grid_3'){$number =3;}
elseif($grid_layout =='grid_4'){$number =4;}
elseif($grid_layout =='grid_5'){$number =5;}
else{$number =1;}
$default= !empty(hexwp_option('product_number'))?hexwp_option('product_number'):12;
 $product_number_options =array(
 	''		=>	__('Show','hexwp').' '.hexwp_number_replace($default),
 	($number * 3)			=>	__('Show','hexwp').' '.hexwp_number_replace(($number * 3)), 
 	($number * 4)			=>	__('Show','hexwp').' '.hexwp_number_replace(($number * 4)), 
 	($number * 5)			=>	__('Show','hexwp').' '.hexwp_number_replace(($number * 5)), 
 	($number * 6)			=>	__('Show','hexwp').' '.hexwp_number_replace(($number * 6)), 
 	($number * 8)			=>	__('Show','hexwp').' '.hexwp_number_replace(($number * 8)), 
 	($number * 10)			=>	__('Show','hexwp').' '.hexwp_number_replace(($number * 10)), 
 
);

$product_layout= !empty($_GET['product_layout']) ? esc_html($_GET['product_layout']):hexwp_option('product_layout');
$product_layout_options =array(
  	'list'			=>	__('Show List','hexwp'), 
 	'grid'		=>	__('Show Grid','hexwp'),  
  );
  ?>
<form class="woocommerce-ordering  <?php echo $class;?>" method="get">

    <div class="hw-order-list-1">
        <div class="hw-product-layout-filter hw-order-item">
        <?php foreach ( $product_layout_options as $id => $name ) : 
            $class_layout_active = $product_layout ==$id ?'hw-product-layout-active':'';
        ?>
        
            <label class="hw-pl-<?php echo esc_attr($id.' '.$class_layout_active);?> ">
                <input type="radio" name="product_layout"   class="product_layout"  <?php checked( $product_layout, $id ); ?> onchange="this.form.submit()"  value="<?php echo esc_attr($id);?>">
            </label>
        <?php endforeach; ?>
        
        </div>
        
            
         <?php  ob_start();  
         woocommerce_result_count();
         echo hexwp_number_replace(ob_get_clean()) ;?>
     
    </div>
    <div class="hw-order-list-2">

        <select name="orderby" class="orderby hw-order-item" aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>">
            <?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
        
        <select name="product_number" class="product_number hw-order-item" onchange="this.form.submit()" >
            <?php foreach ( $product_number_options as $id => $name ) : ?>
                <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $product_number, $id ); ?> ><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
        </select>
    
    
 
	</div>
     
 
     
	<input type="hidden" name="paged" value="1" />
 	<?php  wc_query_string_form_fields( null, array( 'orderby', 'submit', 'product_number', 'product_layout','paged', 'product-page' ) ); ?>
</form>


<?php 
 
 

 