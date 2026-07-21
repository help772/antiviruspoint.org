<?php
/**
 * Product Loop End
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-end.php.
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
 * @version 10002.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(is_shop() || is_product_category() || is_product_tag()|| is_product_taxonomy()){
}else{?>
		 </ul>
	 
 <?php 
}
 if(function_exists('elementor_fail_wp_version')){
 	if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {?>
	<div class="hw-elementor-script"> 
                     
		<script type="text/javascript">
			(function($) {
			'use strict';
				jQuery(document).ready(function() {	
					$('.elementor-grid').each(function() { 
						$(this).hexwp_auto_width();
					}); 
				});
			})(jQuery);
		</script>
                
	</div>
	<?php 
	}
}?>	
 
 